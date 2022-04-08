<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->get('/test', function () use ($router) {
    return 1;
});

// $router->get('user', 'Api\UserController@index');
// $router->get('user', 'Api\UserController@index');
// $router->post('user', 'Api\UserController@create');
$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('login', 'Api\AuthController@login');
});

$router->group(['prefix' => 'api', 'middleware' => 'auth'], function () use ($router) {
    $router->get('user', 'Api\UserController@index');
    $router->get('user/{id}', 'Api\UserController@show');
    $router->post('user', 'Api\UserController@store');
    $router->put('user/{id}', 'Api\UserController@update');
    $router->delete('user/{id}', 'Api\UserController@delete');
    $router->get('address', 'Api\AddressController@index');
    $router->get('address/{id}', 'Api\AddressController@show');
    $router->post('address', 'Api\AddressController@store');
    $router->put('address/{id}', 'Api\AddressController@update');
    $router->delete('address/{id}', 'Api\AddressController@delete');
});
