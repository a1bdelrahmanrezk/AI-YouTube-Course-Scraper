<?php

use App\Http\Controllers\Web\CourseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CourseController::class, 'index']);


Route::controller(CourseController::class)->group(function () {
    Route::post('search', 'search')->name('course.search');
});
