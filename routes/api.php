<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PermissionGroupController;
use App\Http\Controllers\UserController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/roles', [RoleController::class, 'index']);
Route::post('/roles', [RoleController::class, 'store']);

Route::get('/permissions', [PermissionController::class, 'index']);
Route::post('/permissions/store', [PermissionController::class, 'store']);

Route::post('/roles/{role}/permissions', [RoleController::class, 'assignPermissions']);

Route::post('/permission-groups/{group}/permissions', [PermissionGroupController::class, 'updatePermissions']);
Route::post('/permission-groups/add', [PermissionGroupController::class, 'store']);
Route::get('/permission-groups', [PermissionGroupController::class, 'index']);

Route::post('/roles/{role}/permissions', [RoleController::class, 'assignPermission']);
Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'removePermission']);
Route::get('roles/{role}/permissions', [RoleController::class, 'getPermissions']);

Route::get('/users', [UserController::class, 'list']);
Route::post('/users', [UserController::class, 'store']);
Route::delete('/users/{user}', [UserController::class, 'destroy']);
