<?php
Route::group(['namespace' => 'Dorcas\ModulesFinanceTax\Http\Controllers', 'middleware' => ['web','auth'], 'prefix' => 'mfn'], function() {
    Route::get('tax-main', 'ModulesFinanceTaxController@index')->name('tax-main');

    Route::get('tax-authorities','ModulesFinanceTaxController@Authorities')->name('tax-authorities');

    Route::get('tax-authorities-search','ModulesFinanceTaxController@searchAuthority')->name('authority-search');
    Route::get('tax-authorities/{id}','ModulesFinanceTaxController@singleAuthority')->name('single_authority');
    Route::post('tax-authorities' ,'ModulesFinanceTaxController@createAuthority')->name('create_authority');
    Route::put('tax-authorities/{id}' ,'ModulesFinanceTaxController@updateAuthority')->name('update_authority');
    Route::delete('tax-authorities/{id}' ,'ModulesFinanceTaxController@deleteAuthority')->name('delete_authority');

    Route::get('tax-element-search','ModulesFinanceTaxController@searchElement')->name('element-search');
    Route::post('tax-elements' ,'ModulesFinanceTaxController@addElement')->name('create_element');
    Route::get('tax-element/{id}' ,'ModulesFinanceTaxController@singleElement')->name('single_element');
    Route::put('tax-element/{id}','ModulesFinanceTaxController@updateElement')->name('update_element');
    Route::delete('tax-element/{id}','ModulesFinanceTaxController@deleteElement')->name('delete_element');

    Route::get('tax-runs/{id}','ModulesFinanceTaxController@Runs')->name('tax-run');
    Route::get('tax-run-search','ModulesFinanceTaxController@searchRun')->name('run-search');
    Route::post('tax-run','ModulesFinanceTaxController@createRun')->name('create_run');
    Route::get('tax-run/{id}','ModulesFinanceTaxController@singleRun')->name('single_run');
    Route::put('tax-run/{id}','ModulesFinanceTaxController@updateRun')->name('update_run');
    Route::delete('tax-run/{id}','ModulesFinanceTaxController@deleteRun')->name('delete_run');

});