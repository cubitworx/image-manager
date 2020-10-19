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

$router->get('/', TaggerController::class);
$router->get('/metadata', 'MetadataController@index');
$router->post('/metadata', 'MetadataController@save');

$router->get('/storage/images/thumbnails/{groupUid}/{filename}', 'ImageController@generateThumbnail');
