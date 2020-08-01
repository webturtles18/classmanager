<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();


Route::group(['middleware' => "auth"],function(){
    Route::get('/classes', 'ClassController@index')->name('home');
    Route::post('/classes', 'ClassController@index')->name('classes.search');
    
    Route::get('/classes/add', 'ClassController@create')->name('classes.create');
    Route::post('/classes/add', 'ClassController@store');
    
    Route::get('/classes/edit/{id}', 'ClassController@edit')->name('classes.edit');
    Route::post('/classes/edit/{id}', 'ClassController@update');

    Route::get('/classes/delete/{id}', 'ClassController@destroy')->name('classes.delete');
    Route::get('/classes/download/{id}', 'ClassController@downloadDocument')->name('classes.download');
    
});
