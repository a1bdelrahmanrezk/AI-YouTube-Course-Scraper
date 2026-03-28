<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CourseCard extends Component
{
    public $title;
    public $thumbnail;
    public $lessons;
    public $duration;
    public $author;
    public $views;
    public $category;

    /**
     * Create a new component instance.
     */
    public function __construct($title, $thumbnail, $lessons, $duration, $author, $views, $category)
    {
        $this->title = $title;
        $this->thumbnail = $thumbnail;
        $this->lessons = $lessons;
        $this->duration = $duration;
        $this->author = $author;
        $this->views = $views;
        $this->category = $category;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.course-card');
    }
}
