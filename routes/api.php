<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Auth\EmailVerifyController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\Log\LoginLogController;
use App\Http\Controllers\Api\Log\OperationLogController;
use App\Http\Controllers\Api\RolePermission\PermissionController;
use App\Http\Controllers\Api\RolePermission\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::apiResource('test', TestController::class);

Route::post('login', [LoginController::class, 'login']);
Route::post('register', RegisterController::class);
Route::post('email-verify', EmailVerifyController::class)->name('email.verify');
Route::post('forget-password', [ResetPasswordController::class, 'forget']);
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('reset.password');
Route::post('invite-confirm', [AdminController::class, 'inviteConfirm'])->name('admin.invite');


Route::group(['middleware' => ['api.auth']], function () {
    Route::post('logout', [LoginController::class, 'logout']);

    // 管理员
    Route::group(['prefix' => 'admin', 'middleware' => ['check.role:admin']], function () {
        Route::get('list', [AdminController::class, 'list']);
        Route::get('detail', [AdminController::class, 'details']);
        Route::post('modify', [AdminController::class, 'modify']);
        Route::post('delete', [AdminController::class, 'delete']);
        Route::post('invite', [AdminController::class, 'invite']);
        Route::post('assign-role', [AdminController::class, 'assignRole']);
        Route::post('assign-permission', [AdminController::class, 'assignPermission']);
        Route::post('revoke-permission', [AdminController::class, 'revokePermission']);
    });

    // 用户
    Route::group(['prefix' => 'user', 'middleware' => ['check.permission']], function () {
        Route::get('detail', [UserController::class, 'details']);
        Route::post('modify-name', [UserController::class, 'modifyName']);
        Route::post('modify-password', [UserController::class, 'modifyPassword']);
        Route::get('view-permission', [UserController::class, 'viewPermission']);
    });

    // 角色
    Route::group(['prefix' => 'role', 'middleware' => ['check.role:admin']], function () {
        Route::get('list', [RoleController::class, 'index']);
        Route::get('detail', [RoleController::class, 'details']);
        Route::post('create', [RoleController::class, 'create']);
        Route::post('update', [RoleController::class, 'update']);
        Route::post('delete', [RoleController::class, 'delete']);
        Route::post('assign-permission', [RoleController::class, 'assignPermission']);
        Route::post('revoke-permission', [RoleController::class, 'revokePermission']);
    });

    // 权限
    Route::group(['prefix' => 'permission', 'middleware' => ['check.role:admin']], function () {
        Route::get('tree', [PermissionController::class, 'tree']);
        Route::get('detail', [PermissionController::class, 'details']);
        Route::post('create', [PermissionController::class, 'create']);
        Route::post('update', [PermissionController::class, 'update']);
        Route::post('delete', [PermissionController::class, 'delete']);
    });

    // 日志
    Route::group(['prefix' => 'log', 'middleware' => ['check.permission']], function () {
        Route::get('login-log', [LoginLogController::class, 'index']);
        Route::get('operation-log', [OperationLogController::class, 'index']);
    });
});
