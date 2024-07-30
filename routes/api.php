<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

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


Route::controller(AuthController::class)->group(function () {
  
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
   
    Route::get('get_user', 'getUser');
});
Route::controller(AdminController::class)->group(function() {

    Route::post('adlogin', 'adlogin');
    Route::post('adregister', 'adregister');
    Route::post('adlogout', 'adlogout');
    
    Route::get('get_admin', 'getAdmin');

});
Route::controller(TaskController::class)->group(function(){
    Route::post('addTask','addTask');
    Route::put('editTask','editTask');
    Route::delete('deleteTask', 'deleteTask'); 
    Route::get('getTasks', 'getTasks'); // Adjust based on your controller name

});
