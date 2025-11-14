<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\InvestorController;

// =============================================================================
// PUBLIC ROUTES (Guest & Authenticated)
// =============================================================================
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

// Temporary admin routes (static pages)
Route::get('/adm/dashboard', function () {
    return view('pages.admin.dashboard');
})->name('admin.dashboard');

Route::get('/adm/projects', function () {
    return view('pages.admin.projects');
})->name('admin.projects');

Route::get('/adm/users', function () {
    return view('pages.admin.users');
})->name('admin.users');

Route::get('/adm/comments', function () {
    return view('pages.admin.comments');
})->name('admin.comments');

Route::get('/adm/wishlist', function () {
    return view('pages.admin.wishlist');
})->name('admin.wishlist');

// =============================================================================
// AUTH ROUTES (Guest Only)
// =============================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.forgot');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// =============================================================================
// STUDENT ROUTES
// =============================================================================
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile', [StudentController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [StudentController::class, 'update'])->name('update');
    
    // Dynamic data creation for projects
    Route::post('/categories', [StudentController::class, 'storeCategory'])->name('categories.store');
    Route::post('/subjects', [StudentController::class, 'storeSubject'])->name('subjects.store');
    Route::post('/teachers', [StudentController::class, 'storeTeacher'])->name('teachers.store');
    Route::post('/expertises', [StudentController::class, 'storeExpertise'])->name('expertises.store');
    
    // Project Management
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    
    // Comments
    Route::post('/projects/{project}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// =============================================================================
// INVESTOR ROUTES
// =============================================================================
Route::middleware(['auth', 'role:investor'])->prefix('investor')->name('investor.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [InvestorController::class, 'dashboard'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile', [InvestorController::class, 'edit'])->name('profile');
    Route::put('/profile', [InvestorController::class, 'update'])->name('update');
    
    // Wishlist
    Route::get('/wishlists', [WishlistController::class, 'index'])->name('wishlists.index');
    Route::post('/projects/{project}/wishlist', [WishlistController::class, 'toggle'])->name('wishlists.toggle');
    
    // Comments
    Route::post('/projects/{project}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});
