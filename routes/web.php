<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/attendance', function () {
    return '打刻画面(仮)<form action="/logout" method="post">' . csrf_field() . '<button>ログアウト</button></form>';
})->middleware('auth');



Route::get('/admin/login', function () {
    return view('admin.admin-login');
})->middleware('guest');

Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

    Route::get('/admin/attendance/list', function () {
    return '管理者の勤怠一覧(仮)<form action="/admin/logout" method="post">' . csrf_field() . '<button>ログアウト</button></form>';
})->middleware('auth');

Route::post('/admin/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth');