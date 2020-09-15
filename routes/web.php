<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|ffsffds your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', 'HomeController@index');
Route::get('/home/callback', 'HomeController@callback');
Route::get('/home/get_refresh_token', 'HomeController@get_refresh_token');
Route::get('/home/test', 'HomeController@test');

/* user controller routes */
Route::get('/user/create', 'UserController@create');
Route::post('/user/save', 'UserController@save');
Route::get('/user/allusers', 'UserController@allusers');
Route::get('/user/edit/{id}', 'UserController@edit');
Route::get('/user/delete/{id}', 'UserController@delete');
Route::post('/user/update', 'UserController@update');
Route::any('/user/quickbook_post_data', 'UserController@quickbook_post_data');
Route::any('/test', 'UserController@test');
Route::any('/basic_email/{msg}', 'UserController@basic_email');

/* invoice controller routes */
Route::get('/invoices/create', 'InvoicesController@create');
Route::post('/invoices/save', 'InvoicesController@save');
Route::get('/invoices/all', 'InvoicesController@allinvoices');
Route::get('/invoices/edit/{id}', 'InvoicesController@edit');
Route::post('/invoices/update', 'InvoicesController@update');

/*Route::get('/', function()
{
    return View::make('pages.home');
});*/

Route::get('about', function()
{
    return View::make('pages.about');
});

Route::get('projects', function()
{
    return View::make('pages.projects');
});

Route::get('contact', function()
{
    return View::make('pages.contact');
});
