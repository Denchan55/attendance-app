<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceRecordController;

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

Route::middleware('auth')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'create']);
    Route::post('/attendance', [AttendanceController::class, 'store']);
    Route::get('/attendance/list', [AttendanceController::class, 'index']);
    Route::get('/attendance/{id}', [AttendanceRecordController::class, 'show'])
    ->whereNumber('id');
});



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