<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\AdminController;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('v1')->middleware('throttle:5,1')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // jwt.version middleware logs user from all devices
    // jwt.refresh middleware auto refresh user token after expiration
    Route::middleware(['auth:api','jwt.version','check.jwt'])->group(function () {
        Route::apiResource('tasks', TaskController::class);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/tasks/{task}/restore', [TaskController::class, 'restore']);
        Route::post('/refresh', [AuthController::class, 'refresh']); 
    });

    /*
    for front end if normal token expire
    Route::middleware(['auth:api', 'jwt.refresh'])->group(function () {
       
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']); 
    });

    */

    Route::middleware(['auth:api','role:admin','jwt.version'])->prefix('admin')->group(function () {

        Route::controller(AdminController::class)->group(function () {
            
             Route::post('/users/{user}/assign-role', 'assignRole');
             Route::post('/users/{user}/give-permission', 'givePermission');
             Route::post('/users/{user}/remove-role','removeRole');
        });
       

    });


    


});
