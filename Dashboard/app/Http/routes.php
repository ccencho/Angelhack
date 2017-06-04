<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::post('sensores/nuevo', 'SensorController@nuevo');
Route::get('sensores/lista', 'SensorController@lista');
Route::get('sensores/listado', 'SensorController@listado');
Route::get('sensores/tabla', 'SensorController@tabla');
Route::get('colegio', 'ColegioController@index');
Route::get('colegio/listar-mapa', 'ColegioController@listar');
Route::get('colegio/{id}', 'SensorController@colegio');
Route::get('ultimo/{id}', 'SensorController@ultimo');