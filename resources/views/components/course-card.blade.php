<div class="col-md-3">
    <div class="card course-card border-0 shadow-sm h-100 pb-2">
        <div class="position-relative">
            <img src="{{ $thumbnail }}" class="card-img-top" alt="Course Thumbnail">
            <span class="badge bg-danger position-absolute top-0 end-0 m-2 text-white">{{ $lessons }} درس</span>
            <span class="badge bg-dark position-absolute bottom-0 start-0 m-2 opacity-75 radius-3 text-white">{{ $duration }}</span>
        </div>
        <div class="card-body p-3">
            <h6 class="fw-bold mb-2">{{ $title }}</h6>
            <div class="d-flex align-items-center mb-3 text-muted small">
                <i class="bi bi-person me-1"></i> {{ $author }}
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                <span class="badge bg-light-orange text-danger px-3">{{ $category }}</span>
                <span class="text-muted smallest">{{ $views }} مشاهدة</span>
            </div>
        </div>
    </div>
</div>