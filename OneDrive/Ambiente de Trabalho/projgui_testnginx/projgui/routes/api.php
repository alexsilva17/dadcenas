<?php

use Illuminate\Http\Request;

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


//Lists
//Route::get('users', 'UserControllerAPI@index');
Route::middleware('auth:api')->get('users/count', 'UserControllerAPI@totalUsers');
Route::get('users/{id}', 'UserControllerAPI@getCurrentUser');
Route::get('walletsIndex', 'WalletControllerAPI@index');
Route::get('money', 'WalletControllerAPI@getMoney');

//Route::middleware('auth.admin')->get('users', 'UserControllerAPI@index');

Route::middleware('auth:api')->get('/user', function(Request $request) {
    return $request->user();
});

//Create User
Route::middleware('auth:api')->post('users', 'UserControllerAPI@store');
Route::middleware('auth:api')->put('users/{id}', 'UserControllerAPI@update');
Route::post('users/regist', 'UserControllerAPI@store');
//Users
Route::post('users', 'UserControllerAPI@store');
Route::middleware('auth:api')->get('users', 'UserControllerAPI@index');
Route::middleware('auth:api')->patch('users/active/yes/{id}', 'UserControllerAPI@activateUser');
Route::middleware('auth:api')->patch('users/active/no/{id}', 'UserControllerAPI@deactivateUser');
Route::middleware('auth:api')->delete('users/delete/{id}', 'UserControllerAPI@destroy');


//Login
Route::post('login', 'LoginControllerAPI@login');
Route::middleware('auth:api')->post('logout','LoginControllerAPI@logout');

//Movements

Route::middleware('auth:api')->get('movements', 'MovementControllerAPI@index');
Route::middleware('auth:api')->get('movements/count', 'MovementControllerAPI@totalMovements'); //só serve pa paginação
Route::middleware('auth:api')->post('movements/income', 'MovementControllerAPI@movementIncome');
Route::middleware('auth:api')->post('movements/expense', 'MovementControllerAPI@movementExpense');
Route::middleware('auth:api')->get('movements/categories', 'MovementControllerAPI@getCategories'); //rota auxiliar
Route::middleware('auth:api')->put('movements/{movement}', 'MovementControllerAPI@updateMovement');


//Wallets
Route::get('wallets', 'WalletControllerAPI@getWallets');
Route::middleware('auth:api')->get('balance', 'WalletControllerAPI@getBalance');



