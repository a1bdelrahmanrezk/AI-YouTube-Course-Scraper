<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Jobs\SearchCategoryPlaylist;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CourseController extends Controller
{
    public function search(Request $request)
    {
        $categories = explode("\n", $request->categories);
        $categories = array_filter($categories, fn($cat) => !empty(trim($cat)));

        foreach ($categories as $category) {
            dispatch(new SearchCategoryPlaylist(trim($category)));
        }
        return back()->with('success', 'Fetching started');
    }

    public function index(Request $request)
    {
        $selectedCategory = $request->get('category', 'all');
        
        $query = Playlist::query();
        
        if ($selectedCategory !== 'all') {
            $query->where('category', $selectedCategory);
        }
        
        $playlists = $query->orderBy('created_at', 'desc')->paginate(8);
        $playlists->appends(['category' => $selectedCategory]); // for pagination bootstrap
        
        $categories = Playlist::distinct()->pluck('category');
        $categoryCounts = Playlist::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
        
        $totalCount = Playlist::count();
        
        return view('pages.home', compact('playlists', 'categories', 'categoryCounts', 'totalCount', 'selectedCategory'));
    }
}
