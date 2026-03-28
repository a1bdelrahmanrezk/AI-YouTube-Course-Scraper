<html dir="rtl">

<head>
    @include('layouts.styles')
</head>

<body>
    <!-- Header -->
    @include('components.header')

    <!-- Section 1 -->
    <div class="scrapping-section">
        @include('components.scrapping')
    </div>

    <!-- Section 2 -->
    <div class="container py-5">
        <h4 class="fw-bold mb-0">الدورات المكتشفة</h4>
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div>
                <span class="gray-color-2">تم العثور ع {{ $totalCount }} دورة في {{ $categories->count() }} تصنيفات</span>
            </div>
            <div class="filter-pills d-flex gap-2">
                <a href="?category=all" class="badge px-3 py-2 {{ $selectedCategory === 'all' ? 'active' : '' }}">
                    <span class="">الكل</span>
                    <span class="">({{ $totalCount }})</span>
                </a>
                @foreach($categories as $category)
                    <a href="?category={{ $category }}" class="badge px-3 py-2 {{ $selectedCategory === $category ? 'active' : '' }}">
                        <span class="">{{ $category }}</span>
                        <span class="">({{ $categoryCounts[$category] ?? 0 }})</span>
                    </a>
                @endforeach
            </div>
        </div>
        <div class="row g-4">
            @forelse($playlists as $playlist)
                @php
                    $durationHours = floor($playlist->total_duration / 3600);
                    $durationMinutes = floor(($playlist->total_duration % 3600) / 60);
                    $durationText = '';
                    if ($durationHours > 0) {
                        $durationText .= $durationHours . ' ساعة ';
                    }
                    if ($durationMinutes > 0) {
                        $durationText .= $durationMinutes . ' دقيقة';
                    }
                    if (empty($durationText)) {
                        $durationText = 'أقل من دقيقة';
                    }
                @endphp
                @include('components.course-card', [
                    'title' => $playlist->title,
                    'thumbnail' => $playlist->thumbnail,
                    'lessons' => $playlist->video_count,
                    'duration' => trim($durationText),
                    'author' => $playlist->channel_name,
                    'views' => number_format($playlist->total_views),
                    'category' => $playlist->category
                ])
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">لا توجد دورات متاحة حالياً في هذا التصنيف</p>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($playlists->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $playlists->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>

</body>
@include('layouts.scripts')

</html>