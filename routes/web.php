<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/project', function () {
    return view('pages.gallery');
})->name('project');

Route::get('/about', function () {
    return view('pages.comming');
})->name('about');

Route::get('/blog', function () {
    return view('pages.comming');
})->name('blog');

Route::get('/qa', function () {
    return view('pages.comming');
})->name('qa');
Route::get('/project-detail', function () {
    return view('pages.project-detail');
})->name('project.detail');
Route::get('/student-detail', function () {
    return view('pages.detail-student');
})->name('detail.student');

Route::get('/adm/dashboard', function () {
    return view('pages.admin.dashboard');
})->name('admin.dashboard');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.forgot');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot.post');