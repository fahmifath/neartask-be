<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\SubTaskController;

Route::apiResource('tasks', TaskController::class);

Route::get('tasks/{taskId}/subtasks',[SubTaskController::class, 'index']);
Route::post('tasks/{taskId}/subtasks',[SubTaskController::class, 'store']);
Route::put('subtasks/{id}',[SubTaskController::class, 'update']);
Route::delete('subtasks/{id}',[SubTaskController::class, 'destroy']);
