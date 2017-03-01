<?php

namespace App\Http\Controllers;

use App\Jobs\SendRequestToSomleng;
use App\Models\tblcall;
use App\Models\tblcallflow;
use App\Models\tblstate;
use App\Models\tbltransition;
use App\MyStateMachine\Stateful;
use Finite\Event\TransitionEvent;
use Finite\Loader\ArrayLoader;
use Finite\State\StateInterface;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Twilio\Rest\Client;
use Twilio\Twiml;

class StateMachineCnt extends Controller
{
    private $tbl_transition;
    private $tbl_call;
    private $tbl_states;
    private $url_sound;
    private $callID;
//    private $response;
    private $call_Sid;
    private $digits;
    private $return_input;

    public function __construct()
    {
        $this->tbl_transition = new tbltransition;
        $this->tbl_call = new tblcall;
        $this->tbl_states = new tblstate;
        $this->url_sound = "";
        $this->callID = "";
//        $this->response = new Twiml();
        $this->call_Sid = "";
        $this->digits = "";
        $this->return_input="";
    }

    public function display()
    {

        //echo '<br> Play TwilML ', $this->state, ' state.', "\n";
        $sid = env('TWILIO_ACCOUNT_SID'); // Your Account SID from www.twilio.com/console
        $token = env('TWILIO_AUTH_TOKEN'); // Your Auth Token from www.twilio.com/console

        /*
            <Response>
                <Play>http://demo.twilio.com/hellomonkey/monkey.mp3</Play>
            </Response>
        */
        $client = new Client($sid, $token);
        // Read TwiML at this URL when a call connects (hold music)
        $call = $client->calls->create(
            '+85517696365', // Call this number
            env('TWILIO_NUMBER'), // From a valid Twilio number
            array(
                'url' => 'https://ee198af6.ngrok.io/welcomTwiMLCode'
            )
        );

        // To play sound file
        /*$response = new Twiml();
        $response->say('Hello');
        $response->play('https://api.twilio.com/cowbell.mp3', array("loop" => 5));
        //print $response;
        //dd($response);

        $response_1 = response($response,200);
        $response_1->header('Content-Type', 'text/xml');

         $client = new Client($sid, $token);

// Read TwiML at this URL when a call connects (hold music)
        $call = $client->calls->create(
            '+85517696365', // Call this number
            env('TWILIO_NUMBER'), // From a valid Twilio number
            array(
                'url' => route('call.flow')
            )
        );*/


    }


    public function test_eloquent_relationship()
    {
        $test = $this->tbl_states->selectcross();
        //dd($test);
        //        $tbl_state_1 = new tblstate;
        //        //$test = $this->tbl_state->firstOrFail(['*'])->tbltransition;
        //        $test = $tbl_state_1::with('transition')->get();
        //        //$test = $tbl_state_1->transition->get();
        //        //$test = $this->tbl_transition->selectcross();
        //        dd($test);
        foreach($test as $test)
        {
            //var_dump($test->state);
            //dd($test);
            foreach($test->transition as $transition)
            {
                //dd($transition->state_id);
            }

        }
    }

    /** To insert transition testing data in tblstate */
    public function insert_transition_test_data()
    {
        $callflow_tbl = new tblcallflow;
        $callflow_id = $callflow_tbl->insertNewCallflow('callflow_1');

        // OLD GRAPH THAT COMBINE VALIDATE FUNCTION WITH GATHERING //
        /*$this->tbl_transition->insertNewTransitionData('A', null, $callflow_id, null, '/public/TwilMLCodeToPlayMessage.xml', null, 'B', '1');
//        $this->tbl_transition->insertNewTransitionData('B', null, $callflow_id, null, 'Gater 5 digits', null, 'C', '');
        $this->tbl_transition->insertNewTransitionData('B', '0', $callflow_id, null, 'Play invalid input, please try again', null, 'C0', '');
        $this->tbl_transition->insertNewTransitionData('B', '1', $callflow_id, null, 'Play file5digits_twilML.xml', null, 'C1', '');
        $this->tbl_transition->insertNewTransitionData('C0', null, $callflow_id, null, 'Play invalid input', null, 'B', '');
        $this->tbl_transition->insertNewTransitionData('C1', null, $callflow_id, null, 'Play (found sound file)', null, 'D', '');
        $this->tbl_transition->insertNewTransitionData('D', null, $callflow_id, null, 'hangout', null, '', '2');*/

        // NEW GRAPH THAT SEPARATE VALIDATION FUNCTION FROM GATHERING // => SAMAK
        $this->tbl_transition->insertNewTransitionData('A', null, $callflow_id, null, '/public/TwilMLCodeToPlayMessage.xml', null, 'B', '1');
        $this->tbl_transition->insertNewTransitionData('B', null, $callflow_id, null, 'Gater 5 digits', null, 'C', '');
        $this->tbl_transition->insertNewTransitionData('C', null, $callflow_id, null, 'Validation', null, 'D', '');
        $this->tbl_transition->insertNewTransitionData('D', '0', $callflow_id, null, 'Play invalid input, please try again', null, 'E0', '');
        $this->tbl_transition->insertNewTransitionData('D', '1', $callflow_id, null, 'Play file5digits_twilML.xml', null, 'E1', '');
        $this->tbl_transition->insertNewTransitionData('E0', null, $callflow_id, null, 'redirect to gathering', null, 'B', '');
        $this->tbl_transition->insertNewTransitionData('E1', null, $callflow_id, null, 'Play (found sound file)', null, 'F', '');
        $this->tbl_transition->insertNewTransitionData('F', null, $callflow_id, null, 'hang up', null, '', '2');

        echo "Transition test data are inserted.";
    }

    /** To insert or update call test data in tblcall */
    public function insert_update_call_test_data()
    {
        $this->tbl_call->insertNewCallData('c001', 'A');
        /*$this->tbl_call->insertNewCallData('c002', 's1');
        $this->tbl_call->insertNewCallData('c003', 's4');*/
        echo "Call test data are inserted.";

        // update call record
        $this->tbl_call->updateCallData('c003', 'hangup');
    }

    public function action()
    {
        act('c001', null, null);
    }

    public function act($callid, $choice_input=null, $non_choice_input=null)
    {
        //dd($choice_input);
        $state_id = $this->tbl_call->searchForCallID($callid);
        $transition_id = $this->tbl_transition->getTransitionID($state_id, $choice_input);
        //dd($transition_id);
    }

    /**
     * @param Request $request
     */
    public function sm_callflow(Request $request)
    {

//        $this->dispatch(serialize(new SendRequestToSomleng($request)));
        $job_request = new SendRequestToSomleng($request); // => execute constructor

        $qId = $this->dispatch($job_request); // => execute handle
//        return $qId;
        $content = Storage::disk('public')->get('twiml_result.xml');
//        $content1 = "test ";
//        return  $content1;
        return $content;
//        var_dump($job_request);
        //return $job_request->getResponse();
        //$job_request
//        return $job_request;
//        Log:info($request);
//        Log:info($test);

        //return $job_request->getResponse();
    }



    public function makeCall()
    {
        $test_phone_number = "+85517696365";
        $twilio_sid = env('TWILIO_ACCOUNT_SID');
        $twilio_token = env('TWILIO_AUTH_TOKEN');
        $twilio_phone_number = env('TWILIO_NUMBER');

        $client = new Client($twilio_sid, $twilio_token);
        try
        {
            $call = $client->calls->create(
                $test_phone_number,
                $twilio_phone_number,
                array("url" => route('playWelcome'))
            );
            echo "<br>" . $call->sid;
        }
        catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

    }
}