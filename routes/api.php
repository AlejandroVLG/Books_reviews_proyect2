<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


///////////////// ENDPOINTS QUE NO REQUIEREN AUTENTIFICACIÓN //////////////////////

Route::get('/', function () { return ['Bienvenido a mi api de libros']; });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('book/showAllBooks', [BookController::class, 'showAllBooks']);

///////////////// USER ENDPOINTS QUE REQUIEREN AUTENTIFICACIÓN //////////////////////

Route::group(["middleware" => "jwt.auth"], function () {

    Route::get('user/myProfile', [UserController::class, 'showMyProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('user/editMyProfile', [UserController::class, 'editMyProfile']);
    Route::delete('user/deleteMyProfile', [UserController::class, 'deleteMyProfile']);
});

///////////////// BOOKS ENDPOINTS QUE REQUIEREN AUTENTIFICACIÓN //////////////////////

Route::group(["middleware" => "jwt.auth"], function () {

    Route::post('book/createBook', [BookController::class, 'createBook']);
    Route::put('book/editBookById/{id}', [BookController::class, 'editBookById']);
    Route::get('book/searchBookByTitle/{title}', [BookController::class, 'searchBookByTitle']);
    Route::get('book/searchBooksByAuthor/{author}', [BookController::class, 'searchBookByAuthor']);
    Route::get('book/searchBooksBySeries/{series}', [BookController::class, 'searchBookBySeries']);
    Route::get('book/searchBooksByGenre/{genre}', [BookController::class, 'searchBooksByGenre']);
    Route::get('book/searchBookByYear/{year}', [BookController::class, 'searchBookByYear']);
});

///////////////// REVIEWS ENDPOINTS QUE REQUIEREN AUTENTIFICACIÓN //////////////////////

Route::group(["middleware" => "jwt.auth"], function () {

    Route::post('review/createReview', [ReviewController::class, 'createReview']);
    Route::get('review/showAllReviews', [ReviewController::class, 'showAllReviews']);
    Route::put('review/editReviewById/{id}', [ReviewController::class, 'editReviewById']);
    Route::delete('review/deleteReview/{id}', [ReviewController::class, 'deleteReview']);
    Route::get('review/searchReviewByUserName/{name}', [ReviewController::class, 'searchReviewByUserName']);
    Route::get('review/showReviewsOrderedByScoreDesc', [ReviewController::class, 'showReviewsOrderedByScoreDesc']);
    Route::get('review/showReviewsOrderedByScoreAsc', [ReviewController::class, 'showReviewsOrderedByScoreAsc']);
    Route::get('review/searchReviewByBookId/{id}', [ReviewController::class, 'searchReviewByBookId']);
});

//////////////// ENDPOINTS QUE REQUIEREN EL MIDDLEWARE "IsAdmin" ///////////////////////

Route::group(["Middleware" => ["jwt.auth", "IsAdmin"]], function () {

    Route::get('/user/getAllUsers', [UserController::class, 'getUsers']);
    Route::delete('book/deleteBook/{id}', [BookController::class, 'deleteBook']);
});

//////////////// ENDPOINTS QUE REQUIEREN EL MIDDLEWARE "IsSuperAdmin" ////////////////////

Route::group(["Middleware" => ["jwt.auth", "IsSuperAdmin"]], function () {

    Route::post('/role/newRole', [RoleController::class, 'newRole']);
    Route::delete('/role/deleteRole/{id}', [RoleController::class, 'deleteRole']);

    Route::post('/user/newAdmin/{id}', [UserController::class, 'addAdminRoleToUser']);
    Route::delete('/user/adminRemove/{id}', [UserController::class, 'removeAdminRoleToUser']);
});
