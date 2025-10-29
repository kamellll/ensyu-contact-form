<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AuthController;
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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', [ContactController::class, 'index']);
Route::post('/confirm', [ContactController::class, 'confirm']);
Route::post('/back', [ContactController::class, 'contactBack']);
Route::post('/thanks', [ContactController::class, 'thanks']);
Route::post('/register', [AuthController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
//Route::post('/admin', [AuthController::class, 'admin']);
Route::middleware('auth')->group(function () {
    Route::get('/admin', [AuthController::class, 'admin']);
    Route::get('/search', [AuthController::class, 'search']);
    Route::delete('/delete', [AuthController::class, 'destroy']);
    Route::get('/export', [AuthController::class, 'export']);
});
