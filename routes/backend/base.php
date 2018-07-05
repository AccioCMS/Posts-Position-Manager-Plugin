<?php
Route::group(['middleware' => ['auth:admin']], function () {

    /**
     * GET
     */
    Route::get('/', 'PositionManagerController@index')->name('index');
    Route::get('/details/{postID}', 'PositionManagerController@details')->name('details');

    /**
     * POST
     */
});