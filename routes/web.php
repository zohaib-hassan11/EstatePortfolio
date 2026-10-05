<?php

use App\Http\Controllers\Admin\AssistantController as AdminAssistantController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnquiryController as AdminEnquiryController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/buying', [PageController::class, 'buying'])->name('buying');
Route::get('/selling', [PageController::class, 'selling'])->name('selling');
Route::get('/testimonials', [PageController::class, 'testimonials'])->name('testimonials');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');

Route::get('/properties', [PropertyController::class, 'index'])->name('properties');
Route::get('/recently-sold', [PropertyController::class, 'sold'])->name('sold');
Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');

Route::post('/enquiries', [EnquiryController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('enquiries.store');

Route::get('/assistant', [AssistantController::class, 'show'])->name('assistant.show');
Route::post('/assistant', [AssistantController::class, 'store'])
    ->middleware('throttle:assistant')
    ->name('assistant.store');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('properties', AdminPropertyController::class)->except('show');
        Route::delete('properties/{property}/images/{image}', [AdminPropertyController::class, 'destroyImage'])
            ->name('properties.images.destroy');

        Route::get('enquiries', [AdminEnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('enquiries/{enquiry}', [AdminEnquiryController::class, 'show'])->name('enquiries.show');
        Route::patch('enquiries/{enquiry}', [AdminEnquiryController::class, 'update'])->name('enquiries.update');
        Route::post('enquiries/{enquiry}/draft', [AdminEnquiryController::class, 'draft'])
            ->middleware('throttle:20,1')
            ->name('enquiries.draft');
        Route::delete('enquiries/{enquiry}', [AdminEnquiryController::class, 'destroy'])->name('enquiries.destroy');

        Route::get('assistant', [AdminAssistantController::class, 'index'])->name('assistant.index');
        Route::get('assistant/questions', [AdminAssistantController::class, 'questions'])->name('assistant.questions');
        Route::get('assistant/{conversation}', [AdminAssistantController::class, 'show'])->name('assistant.show');

        Route::resource('testimonials', AdminTestimonialController::class)->except('show');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [ProfileController::class, 'password'])->name('profile.password');
        Route::post('profile/photo', [ProfileController::class, 'photo'])->name('profile.photo');

        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings/{section}', [SettingsController::class, 'update'])->name('settings.update');
        Route::delete('settings/{section}', [SettingsController::class, 'reset'])->name('settings.reset');
    });
});
