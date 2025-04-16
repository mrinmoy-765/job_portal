<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\jobsController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CustomAuthenticate;
use App\Http\Middleware\RedirectIfAuthenticated;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/jobs', [jobsController::class, 'index'])->name('jobs');
Route::get('/jobs/detail/{id}', [jobsController::class, 'detail'])->name('jobDetail');




// Apply the 'guest' middleware to login and registration routes
Route::middleware([RedirectIfAuthenticated::class])->group(function () {
    Route::get('/account/registration', [AccountController::class, 'registration'])->name('account.registration');
    Route::post('/account/process-register', [AccountController::class, 'processRegistration'])->name('account.processRegistration');
    Route::get('/account/login', [AccountController::class, 'login'])->name('account.login');
    Route::post('/account/authenticate', [AccountController::class, 'authenticate'])->name('account.authenticate');
});

// Apply the 'auth' middleware to authenticated routes (like profile, logout)
Route::middleware([CustomAuthenticate::class])->group(function () {
    Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
    Route::put('/account/update-profile', [AccountController::class, 'updateProfile'])->name('account.updateProfile');
    Route::get('/account/logout', [AccountController::class, 'logout'])->name('account.logout');
    Route::post('/account/update-profile-pic', [AccountController::class, 'updateProfilePic'])->name('account.updateProfilePic');
    Route::get('/create-job', [AccountController::class, 'createJob'])->name('account.createJob');
    Route::post('/save-job', [AccountController::class, 'saveJob'])->name('account.saveJob');
    Route::get('/my-jobs', [AccountController::class, 'myJobs'])->name('account.myJobs');
    Route::get('/my-jobs/edit/{jobId}', [AccountController::class, 'editJob'])->name('account.editJob');
    Route::post('/update-job/{jobId}', [AccountController::class, 'updateJob'])->name('account.updateJob');
    Route::post('/delete-job', [AccountController::class, 'deleteJob'])->name('account.deleteJob');
});