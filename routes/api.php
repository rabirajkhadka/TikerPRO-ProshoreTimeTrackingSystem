<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TimeLogController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('all-roles', [UserController::class, 'allUserRoles']);

Route::controller(AuthController::class)->prefix('user')->group(function () {
    Route::post('register', 'registerUser')->name('register');
    Route::post('login', 'loginUser')->name('login');
    Route::get('logout', 'logoutUser')->middleware('auth:sanctum');
    Route::post('forgot-password', 'forgotPass');
    Route::post('reset-password', 'resetPass');
});

Route::controller(AdminController::class)->prefix('admin')->middleware(['auth:sanctum', 'user.status', 'isAdmin'])->group(function () {
    Route::get('all-users', 'viewAllUsers');
    Route::post('change-roles', 'assignRoles');
    Route::post('delete-user/{id}', 'deleteUser');
    Route::post('invite', 'inviteOthers');
});

Route::controller(UserController::class)->prefix('user')->middleware(['auth:sanctum', 'user.status'])->group(function () {
    Route::get('me', 'viewMe');
    Route::patch('update', 'updateMe');
});

<<<<<<< HEAD
Route::controller(AdminController::class)->prefix('project')->middleware(['auth:sanctum', 'user.status', 'isAdmin'])->group(function () {
    Route::post('add-project', 'addProject');
    Route::post('update-project/{id}', 'updateProject');
});
=======
//Time Logging Routes
Route::controller(TimeLogController::class)->prefix('log')->middleware(['auth:sanctum', 'user.status'])->group(function () {
    Route::post('add-entry', 'addActivity');
});





>>>>>>> 16ea0f574534d4d854d3eb605f6d4d045f81962e
