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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/example_new', 'StateMachineCnt@example_new');

// Example of State machine
Route::get('/basic_graph', 'StateMachineExampleController@basic_graph');
Route::get('/callback', 'StateMachineExampleController@callback');
Route::get('/multiple_graph', 'StateMachineExampleController@multiple_graph');
Route::get('/transition_properties', 'StateMachineExampleController@transition_properties');

// not working
// Route::get('/guard', 'StateMachineExampleController@guard');
// Route::get('/multiple_graphs_with_factory', 'StateMachineExampleController@multiple_graphs_with_factory');

/** To insert transition test data into tblstate */
Route::get('/insert_transition_test_data', 'StateMachineCnt@insert_transition_test_data');

/** To insert call test data into tblcall */
Route::get('/insert_update_call_test_data', 'StateMachineCnt@insert_update_call_test_data');

/** To  */
Route::get('/act', 'StateMachineCnt@act');

