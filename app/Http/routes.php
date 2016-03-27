<?php

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

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->group(['prefix' => 'api/v1/users', 'namespace' => 'App\Http\Controllers'], function($app)
{ 
    $app->get('{id}', ['as' => 'user_show', 'uses' => 'UserController@show']);                          // GET /users/:id               => Get an user
    $app->post('/','UserController@create');                                                            // POST /users                  => Create an user
    $app->put('{id}',['middleware' => 'auth', 'uses' => 'UserController@update']);                                                          // PUT /users/:id               => Update an user
});

$app->group(['prefix' => 'api/v1/images', 'namespace' => 'App\Http\Controllers'], function($app)
{
    $app->get('/', 'ImageController@index');                                                            // GET /images                  => Get all images (gallery)
    $app->get('{id}',['as' => 'image_show', 'uses' => 'ImageController@show']);                         // GET /images/:id              => Get an image
    $app->post('/', ['middleware' => 'auth', 'uses' => 'ImageController@create']);                      // POST /images                 => Create an image (require auth)
    $app->put('{id}','ImageController@update');                                                         // PUT /images/:id              => Update an image
    $app->delete('{id}','ImageController@delete');                                                      // DELETE /images/:id           => Delete an image
    $app->post('{id}/comments', ['middleware' => 'auth', 'uses' => 'CommentController@create']);        // POST /images/:id/comments    => Create a comment on an image (require auth)

});

$app->group(['prefix' => 'api/v1/comments', 'middleware' => 'auth', 'namespace' => 'App\Http\Controllers'], function($app)
{
    $app->put('{id}','CommentController@update');                                                       // PUT /comments/:id            => Update a comment
    $app->delete('{id}','CommentController@delete');                                                    // DELETE /comments/:id         => Delete a comment
});
