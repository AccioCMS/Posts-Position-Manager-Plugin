<?php
/**
 * Routes of this plugin
 */

/**
 * GET
 */
Route::get('/', 'PositionManagerController@index')->name('index');
Route::get('/details/{postID}', 'PositionManagerController@details')->name('details');

/**
 * POST
 */
