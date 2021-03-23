<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExperimentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function () {

    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});


Route::post('experiments', [ExperimentController::class, 'store']);
Route::get('experiments', [ExperimentController::class, 'index']);
Route::get('experiments/{experiment}', [ExperimentController::class, 'show']);
Route::post('handle-experiment', [ExperimentController::class, 'handleExperiment']);
Route::post('handle-assignment', [ExperimentController::class, 'assignEao']);

Route::get('eaos', [UserController::class, 'eaos']);
Route::get('admins', [UserController::class, 'admins']);
Route::get('students', [UserController::class, 'students']);
