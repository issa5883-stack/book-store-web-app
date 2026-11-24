<?php

use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\Admin\BookRequestController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\UserBookController;
use Illuminate\Support\Facades\Route;

/*
| Language switcher (English / Arabic)
*/
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

/*
| Public library (approved books only)
*/
Route::get('/', [BookController::class, 'home'])->name('home');
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
Route::get('/books/{book}/download', [BookController::class, 'download'])->name('books.download');

/*
| User sign up / sign in
*/
Route::middleware('guest')->group(function () {
    Route::get('/sign_up', [AuthController::class, 'showSignUp'])->name('sign_up');
    Route::post('/sign_up', [AuthController::class, 'signUp'])->name('sign_up.submit');
    Route::get('/login', [AuthController::class, 'showSignIn'])->name('login');
    Route::post('/login', [AuthController::class, 'signIn'])->name('login.submit');
});

/*
| Logged-in users: submit books as requests for admin approval
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('my-books', UserBookController::class)
        ->parameters(['my-books' => 'book'])
        ->except('show');
});

/*
| Administrator
*/
Route::get('/admin/login', [AdminController::class, 'loginform'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('adminlogin.submit');

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    Route::resource('books', AdminBookController::class)->except('show');

    Route::get('/requests', [BookRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{book}', [BookRequestController::class, 'show'])->name('requests.show');
    Route::patch('/requests/{book}/approve', [BookRequestController::class, 'approve'])->name('requests.approve');
    Route::patch('/requests/{book}/reject', [BookRequestController::class, 'reject'])->name('requests.reject');

    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('users', UserController::class)->only(['index', 'destroy']);
});
