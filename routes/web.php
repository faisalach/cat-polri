<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\PackageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::middleware(['isLogin:true','roleSession:1,2'])->group(function () {
    Route::middleware(['roleSession:1'])->group(function () {
        Route::get('/panel/users', [HomeController::class, 'users'])->name('users');
        Route::get('/panel/users_get_datas', [HomeController::class, 'users_get_datas'])->name('users.get_data');
        Route::get('/panel/users/create', [HomeController::class, 'users_create'])->name('users.create');
        Route::post('/panel/users/create', [HomeController::class, 'users_store'])->name('users.create');
        Route::get('/panel/users/edit/{user_id}', [HomeController::class, 'users_edit'])->name('users.edit');
        Route::post('/panel/users/edit/{user_id}', [HomeController::class, 'users_update'])->name('users.edit');
        Route::post('/panel/users/delete/', [HomeController::class, 'users_delete'])->name('users.delete');
        Route::post('/panel/score_delete', [ScoreController::class, 'score_delete'])->name('score.delete');
    });
    Route::get('/panel/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    Route::get('/panel/scores/{user_id}', [ScoreController::class, 'scores'])->name('scores');
    Route::get('/panel/detail_score/{score_id}', [ScoreController::class, 'detail_score'])->name('scores.detail');

    Route::get('/panel/courses', [CourseController::class, 'index'])->name('courses');
    Route::get('/panel/course/create', [CourseController::class, 'create'])->name('course.create');
    Route::post('/panel/course/create', [CourseController::class, 'store'])->name('course.create');
    Route::post('/panel/course/update_israndom', [CourseController::class, 'update_israndom'])->name('course.update_israndom');
    Route::post('/panel/course/delete/', [CourseController::class, 'delete'])->name('course.delete');

    Route::get('/panel/question/create/{course_id}', [QuestionController::class, 'create'])->name('question.create');
    Route::post('/panel/question/create/{course_id}', [QuestionController::class, 'store'])->name('question.create');
    Route::post('/panel/question/update/{course_id}', [QuestionController::class, 'update'])->name('question.update');
    Route::post('/panel/question/create_column/{course_id}', [QuestionController::class, 'store_column'])->name('question.create_column');
    Route::post('/panel/question/update_column/{course_id}', [QuestionController::class, 'update_column'])->name('question.update_column');

    Route::get('/panel/packages', [PackageController::class, 'index'])->name('packages');
    Route::get('/panel/package_get_datas', [PackageController::class, 'package_get_datas'])->name('package.get_datas');
    Route::get('/panel/detail_package_get_datas/{package_id}', [PackageController::class, 'detail_package_get_datas'])->name('package.detail_get_datas');
    Route::get('/panel/package/edit/{package_id?}', [PackageController::class, 'edit'])->name('package.edit');
    Route::post('/panel/package/edit/{package_id?}', [PackageController::class, 'update'])->name('package.edit');
    Route::post('/panel/package/generate_token/{package_id}/{course_id}', [PackageController::class, 'generate_token'])->name('package.generate_token');
    Route::post('/panel/package/delete/', [PackageController::class, 'delete'])->name('package.delete');

    Route::get('/panel/scores', [ScoreController::class, 'list'])->name('scores.list');

});
Route::middleware(['isLogin:true','roleSession:3'])->group(function () {
    Route::get('/siswa/dashboard', [SiswaController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/siswa/exams', [ExamController::class, 'list'])->name('exam.list');
    Route::get('/siswa/exam_token/{course_id}/{course_package_id}', [ExamController::class, 'token'])->name('exam.token');
    Route::post('/siswa/exam_token/{course_id}/{course_package_id}', [ExamController::class, 'token'])->name('exam.token');
    Route::get('/siswa/exam/{course_id}/{course_package_id}/{token?}', [ExamController::class, 'exam'])->name('exam.exam');
    Route::post('/siswa/exam/{course_id}/{course_package_id}/{token?}', [ExamController::class, 'store'])->name('exam.exam');
});
Route::middleware(['isLogin:true'])->group(function () {
    Route::get('/score/{course_type_id}/{course_package_id}/{user_id?}', [ScoreController::class, 'show'])->name('score');
});
Route::middleware(['isLogin:false'])->group(function () {
    Route::get('/', [AuthController::class, 'login']);
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'login_process'])->name('login');
});

