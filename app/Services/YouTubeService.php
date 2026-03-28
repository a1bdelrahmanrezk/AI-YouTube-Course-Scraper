<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YouTubeService
{
    public function searchPlaylists(string $query): array
    {
        $apiKey = config('services.youtube.key');
        $maxResult = config('services.youtube.max_results');
        
        $response = Http::get('https://www.googleapis.com/youtube/v3/search', [
            'part' => 'snippet',
            'type' => 'playlist',
            'q' => $query,
            'maxResults' => $maxResult,
            'key' => $apiKey,
        ]);

        if ($response->failed()) {
            Log::error('YouTube API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [];
        }

        $searchResults = $response->json();
        $playlistIds = collect($searchResults['items'] ?? [])
            ->pluck('id.playlistId')
            ->filter()
            ->toArray();

        if (empty($playlistIds)) {
            return [];
        }

        // Get detailed playlist information
        $detailsResponse = Http::get('https://www.googleapis.com/youtube/v3/playlists', [
            'part' => 'snippet,contentDetails',
            'id' => implode(',', $playlistIds),
            'key' => $apiKey,
        ]);

        if ($detailsResponse->failed()) {
            Log::error('YouTube playlist details request failed', [
                'status' => $detailsResponse->status(),
                'body' => $detailsResponse->body(),
            ]);
            return [];
        }

        $playlists = $detailsResponse->json();
        
        $playlistsWithStats = [];
        
        foreach ($playlists['items'] as $playlist) {
            $playlistId = $playlist['id'];
            
            $itemsResponse = Http::get('https://www.googleapis.com/youtube/v3/playlistItems', [
                'part' => 'snippet',
                'playlistId' => $playlistId,
                'maxResults' => 10,
                'key' => $apiKey,
            ]);

            if ($itemsResponse->failed()) {
                Log::warning("Failed to get items for playlist {$playlistId}");
                continue;
            }

            $playlistItems = $itemsResponse->json();
            $videoIds = collect($playlistItems['items'] ?? [])
                ->pluck('snippet.resourceId.videoId')
                ->filter()
                ->toArray();

            if (empty($videoIds)) {
                continue;
            }

            // Get video statistics (views, duration)
            $videoStatsResponse = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                'part' => 'statistics,contentDetails',
                'id' => implode(',', $videoIds),
                'key' => $apiKey,
            ]);

            $totalViews = 0;
            $totalDuration = 0;
            
            if ($videoStatsResponse->successful()) {
                $videos = $videoStatsResponse->json();
                
                foreach ($videos['items'] as $video) {
                    // Add views
                    $totalViews += intval($video['statistics']['viewCount'] ?? 0);
                    
                    // Calculate duration
                    $duration = $video['contentDetails']['duration'] ?? 'PT0S';
                    $totalDuration += $this->convertDurationToSeconds($duration);
                }
            }

            $playlistsWithStats[] = [
                'id' => $playlistId,
                'title' => $playlist['snippet']['title'] ?? null,
                'description' => $playlist['snippet']['description'] ?? null,
                'thumbnail' => $playlist['snippet']['thumbnails']['high']['url']
                    ?? $playlist['snippet']['thumbnails']['default']['url']
                    ?? null,
                'channel' => $playlist['snippet']['channelTitle'] ?? null,
                'itemCount' => $playlist['contentDetails']['itemCount'] ?? 0,
                'totalViews' => $totalViews,
                'totalDuration' => $totalDuration,
            ];
        }

        Log::info('YouTube API response with stats: ' . json_encode($playlistsWithStats));
        return $playlistsWithStats;
    }

    private function convertDurationToSeconds(string $duration): int
    {
        // Convert ISO 8601 duration (PT4M13S) to seconds
        $interval = new \DateInterval($duration);
        return ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
    }
}
