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

use Twilio\Twiml;

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/example_new', 'StateMachineCnt@example_new');

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

Route::get('/get_states_from_table', 'StateMachineCnt@getStates');

Route::get('/get_transitions_from_table', 'StateMachineCnt@getTransitions');
/** To insert call test data into tblcall */
Route::get('/insert_update_call_test_data', 'StateMachineCnt@insert_update_call_test_data');

/** To  */
// Route::get('/act_input/c001/1', 'StateMachineCnt@act_input');

//Route::get('/act_input/{callid}/{choice_input}/{non_choice_input}',  'StateMachineCnt@act_input');
Route::get('/action', 'StateMachineCnt@action');

Route::group(
    ['prefix' => 'ivr'], function () {

    Route::any('/makecall', [
        'as' => 'makecall', 'uses' => 'StateMachineCnt@makeCall'
    ]);
    Route::any(
        '/playWelcome', [
            'as' => 'playWelcome', 'uses' => 'StateMachineCnt@playWelcome'
        ]
    );
    Route::any(
        '/gatherInput', [
            'as' => 'gatherInput', 'uses' => 'StateMachineCnt@gatherInput'
        ]
    );
    Route::any(
        '/validation_sound_file', [
            'as' => 'validation_sound_file', 'uses' => 'StateMachineCnt@validation_sound_file'
        ]
    );
    Route::any(
        '/displayIncorrectInput', [
            'as' => 'displayIncorrectInput', 'uses' => 'StateMachineCnt@displayIncorrectInput'
        ]
    );
    Route::any(
        '/playSoundFile', [
            'as' => 'playSoundFile', 'uses' => 'StateMachineCnt@playSoundFile'
        ]
    );
    Route::any(
        '/hangup', [
            'as' => 'hangup', 'uses' => 'StateMachineCnt@hangup'
        ]
    );
    Route::any(
        '/transitToNextState/{sm}/{tran_name}', [
            'as' => 'hangup', 'uses' => 'StateMachineCnt@transit'
        ]
    );


    Route::any(
        '/changeState/{indexOfTrans}', [
            'as' => 'changeState', 'uses' => 'StateMachineCnt@changeState'
        ]
    );

//    Route::any(
//        '/inputvalidation', [
//            'as' => 'inputvalidation', 'uses' => 'StateMachineCnt@inputValidation'
//        ]
//    );

//    Route::any(
//        '/test_validation', [
//            'as' => 'test_validation', 'uses' => 'IVRCnt@test_validation'
//        ]
//    );

}
);

Route::get('/act_input/{callid}/{input}', 'StateMachineCnt@act_input');

Route::any('welcomTwiMLCode', ['as' => 'welcomTwiMLCode', 'uses' => function (){
    // To play sound file
    $response = new Twiml();
    $response->say('Hello Please enter 5 digits code');
//    $response->play('https://api.twilio.com/cowbell.mp3', array("loop" => 5));
//    $gather = $response->gather(array('numDigits' => 1, 'action' => 'http://503c8427.ngrok.io/choose'));
    $gather = $response->gather(['numDigits' => 1, 'action' => 'https://ee198af6.ngrok.io/choose']);
    $gather->say("Verification code incorrect, please try again.");

    header("Content-Type: text/xml");
    print $response;
    //dd($response);

//    $response_1 = response($response,200);
//    $response_1->header('Content-Type', 'text/xml');
//    return $response_1;

}]);

//Route::any('playWelcomAndGather', ['as' => 'callflow', 'StateMachineCnt@showWelcome']);

Route::any('choose', ['as' => 'choose', 'uses' => function (){

    $digits = $_REQUEST['Digits'];
    // To play sound file
    $response1 = new Twiml();
    if($digits == 1)
        $response1->play('https://ee198af6.ngrok.io/Pursat_03.mp3');
    else
        $response1->say('Sorry, your input is invalid, please try again');
    //header("Content-Type: text/xml");
    return $response1;

}]);

Route::any('sm_callflow', ['as' => 'sm_callflow', 'uses' => 'StateMachineCnt@sm_callflow']);

Route::any('testsqs', ['as' => 'testsqs', 'uses' => 'AWSQueue@testSqs']);

//Route::any('sm_callflow_new', ['as' => 'sm_callflow_new', 'uses' => 'SendRequestToSomleng@sm_callflow_new']);

