<div class="container card shadow-sm border-0 mb-5 radius-12">
    <div class="card-body py-4">
        <form action="{{ route('course.search') }}" method="POST">

            <div class="row g-4 flex-wrap">
                <div class="col-md-8">
                    <label class="form-label text-muted fw-bold">أدخل التصنيفات (كل تصنيف في سطر جديد)</label>
                    <textarea name="categories" class="form-control bg-light bolder-fields radius-12" rows="6" placeholder="كمثال:&#10;التسويق&#10;البرمجة&#10;الجرافيكس"></textarea>
                </div>
                <div class="col-md-4 d-flex flex-column justify-content-end">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-lg mb-3 py-2 fw-bold w-100 shadow-sm radius-12">
                        <span>
                            <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.25 1.73181L16.25 18.1869L2 9.95935L16.25 1.73181Z" stroke="white" stroke-width="2" />
                            </svg>
                        </span>
                        <span>ابدأ الجمع</span>
                    </button>
                    <button class="btn fw-bold w-100 py-2 bolder-fields radius-12">
                        <span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="1" y="1" width="16" height="16" rx="2" stroke="#7D7D7D" stroke-width="2" />
                            </svg>
                            
                        </span>
                        <span class="gray-color-2">إيقاف</span><br/>
                        <span>To track progress of search, see storage/logs<br/>
                            then refresh page
                        </span>
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>