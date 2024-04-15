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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', 'AuthController@login');

Route::get('pedidos/buscar-articulos/{parametro}', 'PedidosController@buscarArticulos');

Route::get('pedidos/pre-datos', 'PedidosController@preDatos');
   
Route::middleware('auth:api')->group(function () {
    Route::get('/prueba', 'AuthController@lista');

    //pedidos
    Route::get('pedidos/buscar-cliente/{parametro}', 'PedidosController@buscarCliente');

});