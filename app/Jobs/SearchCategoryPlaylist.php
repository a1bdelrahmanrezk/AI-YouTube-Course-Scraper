<?php

namespace App\Jobs;

use App\Models\Playlist;
use App\Services\AIService;
use App\Services\YouTubeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SearchCategoryPlaylist implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $category)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AIService $aiService, YouTubeService $youtubeService): void
    {
        Log::info("SEARCH-CATEGORY-PLAYLIST: Starting playlist search for category: {$this->category}");
        
        // Generate course titles using AI
        $titles = $aiService->generateCourseTitles($this->category);
        Log::info("SEARCH-CATEGORY-PLAYLIST:  Generated " . count($titles) . " titles for category: {$this->category}", ['titles' => $titles]);
        
        $allPlaylists = [];
        
        // Search YouTube for each generated title
        foreach ($titles as $title) {
            Log::info("SEARCH-CATEGORY-PLAYLIST: Searching YouTube for title: {$title}");
            $playlists = $youtubeService->searchPlaylists($title);
            
            if (!empty($playlists)) {
                $limitedPlaylists = array_slice($playlists, 0, 2);
                $allPlaylists = array_merge($allPlaylists, $limitedPlaylists);
                
                Log::info("SEARCH-CATEGORY-PLAYLIST: Found " . count($limitedPlaylists) . " playlists for title: {$title}", ['playlists' => $limitedPlaylists]);
            } else {
                Log::info("SEARCH-CATEGORY-PLAYLIST: No playlists found for title: {$title}");
            }
        }
        
        Log::info("SEARCH-CATEGORY-PLAYLIST: Total playlists collected for category '{$this->category}': " . count($allPlaylists), ['all_playlists' => $allPlaylists]);
        
        // Save playlists to database with deduplication
        foreach ($allPlaylists as $playlist) {
            Playlist::updateOrCreate(
                ['playlist_id' => $playlist['id']], // deduplication
                [
                    'title' => $playlist['title'],
                    'description' => $playlist['description'],
                    'thumbnail' => $playlist['thumbnail'],
                    'channel_name' => $playlist['channel'],
                    'category' => $this->category,
                    'video_count' => $playlist['itemCount'] ?? 0,
                    'total_views' => $playlist['totalViews'] ?? 0,
                    'total_duration' => $playlist['totalDuration'] ?? 0,
                ]
            );
        }
        
        Log::info("SEARCH-CATEGORY-PLAYLIST: Saved " . count($allPlaylists) . " playlists to database for category: {$this->category}");
    }
}
