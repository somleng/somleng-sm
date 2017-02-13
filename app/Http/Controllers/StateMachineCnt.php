<?php

namespace App\Http\Controllers;

use App\Models\tblcall;
use App\Models\tblcallflow;
use App\Models\tblstate;
use App\Models\tbltransition;
use App\MyStateMachine\Stateful;
use Finite\Event\TransitionEvent;
use Finite\Loader\ArrayLoader;
use Finite\State\StateInterface;
use Finite\StateMachine\StateMachine;
use Twilio\Rest\Client;
use Twilio\Twiml;

class StateMachineCnt extends Controller
{
    private $tbl_transition;
    private $tbl_call;
    private $tbl_states;
    private $url_sound;
    private $callID;
//    public $stateMachine;
    private $response;
    //public $document;

    public function __construct()
    {
        $this->tbl_transition = new tbltransition;
        $this->tbl_call = new tblcall;
        $this->tbl_states = new tblstate;
        $this->ngrok_address = "https://82f89c70.ngrok.io";
        $this->url_sound = "";
        $this->callID = "";
        $this->response = new Twiml();
        
    }

    public function display()
    {

        //echo '<br> Play TwilML ', $this->state, ' state.', "\n";
        $sid = "ACe888d537776f80870b2ae5d8bd37bf4c"; // Your Account SID from www.twilio.com/console
        $token = "d09aa9dd6a4440d6984c60cfe1e41881"; // Your Auth Token from www.twilio.com/console

        /*

            <Response>
                <Play>http://demo.twilio.com/hellomonkey/monkey.mp3</Play>
            </Response>
        */
        $client = new Client($sid, $token);
        // Read TwiML at this URL when a call connects (hold music)
        $call = $client->calls->create(
            '+85517696365', // Call this number
            '+12013800532', // From a valid Twilio number
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
            '+12013800532', // From a valid Twilio number
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
//        dd($callflow_id);

        // Model insertNewTransitionData($state, $input=null, $callflow_id, $twilml=null, $path=null, $action=null, $new_state, $state_type)

        /*
        // old state machine transition table data
         $this->tbl_transition->insertNewTransitionData('s0', '1', $callflow_id, null, '/public/test.xml', null, 's1', '1');
         $this->tbl_transition->insertNewTransitionData('s0', '2', $callflow_id, null, '/public/test.xml', null, 's2', '');
         $this->tbl_transition->insertNewTransitionData('s0', '3', $callflow_id, null, '/public/test.xml', null, 's3', '');
         $this->tbl_transition->insertNewTransitionData('s1', '1', $callflow_id, null, '/public/test.xml', null, 's4', '');
         $this->tbl_transition->insertNewTransitionData('s4', '1', $callflow_id, null, '/public/test.xml', null, 's1', '');
         $this->tbl_transition->insertNewTransitionData('s4', '2', $callflow_id, null, '/public/test.xml', null, 's0', '');
         $this->tbl_transition->insertNewTransitionData('s4', '3', $callflow_id, null, '/public/test.xml', null, 's3', '');
         $this->tbl_transition->insertNewTransitionData('s4', '4', $callflow_id, null, '/public/test.xml', null, 'hangup', '');
         $this->tbl_transition->insertNewTransitionData('s2', 'null', $callflow_id, null, '/public/test.xml', null, 'hangup', '');
         $this->tbl_transition->insertNewTransitionData('s3', 'null', $callflow_id, null, '/public/test.xml', null, 'hangup', '');
         $this->tbl_transition->insertNewTransitionData('hangup', 'null', $callflow_id, '/public/test.xml', null, null, '', '2');
        */

        $this->tbl_transition->insertNewTransitionData('A', null, $callflow_id, null, '/public/TwilMLCodeToPlayMessage.xml', null, 'B', '1');
//        $this->tbl_transition->insertNewTransitionData('B', null, $callflow_id, null, 'Gater 5 digits', null, 'C', '');
        $this->tbl_transition->insertNewTransitionData('B', '0', $callflow_id, null, 'Play invalid input, please try again', null, 'C0', '');
        $this->tbl_transition->insertNewTransitionData('B', '1', $callflow_id, null, 'Play file5digits_twilML.xml', null, 'C1', '');
        $this->tbl_transition->insertNewTransitionData('C0', null, $callflow_id, null, 'Play invalid input', null, 'B', '');
        $this->tbl_transition->insertNewTransitionData('C1', null, $callflow_id, null, 'Play (found sound file)', null, 'D', '');
        $this->tbl_transition->insertNewTransitionData('D', null, $callflow_id, null, 'hangout', null, '', '2');

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
     *
     */
    public function example_new()
    {


        $getStates = $this->tbl_states->getStatesFromStateTable('1');
//        dd($getStates);
        $arrayStringStates = array();
        $arrayStringTransitions = array();
        foreach ($getStates as $getState){
//            echo $getState;
            $state_name = $getState['state'];
            $state_type = $getState['state_type'];
            $state_type_str = "";
            switch ($state_type)
            {
                case 0:
                    $state_type_str = StateInterface::TYPE_NORMAL;
                    break;

                case 1:
                    $state_type_str = StateInterface::TYPE_INITIAL;
                    break;

                case 2:
                    $state_type_str = StateInterface::TYPE_FINAL;
                    break;
            }
//            $eachState = array(
//                $state_name => array(
//                    'type' => $state_type_str,
//                    'properties' => array(),
//                ));
            //$arrayStringStates[] = $eachState[0];

            $arrayStringStates [$state_name] = array(
                'type' => $state_type_str,
                'properties' => array()
            );

//            $test = array_push($arrayStringStates,$eachState);
            //var_dump(json_decode($eachState));die;
            //$arrayStringStates[] =  $eachState;

            /*$arrayStringStates[] = array(
                $state_name => array(
                    'type' => $state_type_str,
                    'properties' => array(),
                ),

            );*/

            /* $arrayStringStates[] =
                 $state_name => array(
                 'type' => $state_type_str,
                 'properties' => array(),
             );*/


            $Transitions = $getState->transition;
            //dd($Transitions);

            foreach ($Transitions as $Transition){
                //dd($getTransition);
                $transition_name = $getState['state'];
                if($Transition['input'] != "")
                    $transition_name = $getState['state'].'-'.$Transition['input'];
                $new_state_id = $Transition['new_state'];
                $new_state_name = $this->tbl_states->getStateName($new_state_id);
                $fromStates = array($getState['state']);
                $toStates = $new_state_name;
                $arrayStringTransitions[$transition_name] = array(
                    'from' => $fromStates,'to' => $toStates,
                );
            }

        }
        //
        //var_dump(json_decode($arrayStringTransitions));

//        dd($arrayStringStates);
//        dd($arrayStringStates);
        //dd($arrayStringTransitions);
        // Create Transitions for Graph

        // Configure your graph
        // $document1 = new Stateful();
        //$document1->display();
        $loader = new ArrayLoader(array(
            'class'  => 'Document',
            'states'  => $arrayStringStates,
            'transitions' => $arrayStringTransitions,
            'callbacks' => array(
                'before' => array(
                     /*array(
                         'from' => 'A',
                         'do' => function($stateMachine) {
//                             dd($stateMachine);
                             $this->makeCall($stateMachine);
                         }
                     )*/
                ),
                'after' => array(

                   array(
                        'to' => array('B'), 'do' => function($current_state) {
//                             dd($stateMachine);
                             $this->makeCall();
                             $this->changeState($current_state);
                         }
                    )
                    ,
                    array(
                        'to' => array('C0'), 'do' => function($current_state) {
                            $this->displayIncorrectInput();
                            $this->changeState($current_state);
                        }
                     ),
                    array(
                        'to' => array('C1'), 'do' => function($current_state) {
                             $this->displayIncorrectInput();
                             $this->changeState($current_state);
                        }
                    ),
                    array(
                        'to' => array('D'), 'do' => function($current_state) {
                            $this->displayIncorrectInput();
                            $this->changeState($current_state);
                        }
                    )
                )

            )
        ));

//        var_dump(json_decode($arrayStringTransitions));die;
        $document     = new Stateful;
        $stateMachine = new StateMachine($document);

        $loader->load($stateMachine);
        $stateMachine->initialize();
//        $this->changeState(0,$this->stateMachine);
//        dd($stateMachine);
        echo "1. example_new";
        //dd($stateMachine);
        // Create States for Graph
//        $getStates = $this->getStates('1');

//        dd($stateMachine);
        //dd($loader);

        //$stateMachine->apply('s02');

        // Working with workflow
        // Current state
        echo "<br> 1. initial state ====== ";
        echo "<br> a.name: "; var_dump($stateMachine->getCurrentState()->getName());
//        echo "<br> properties: "; var_dump($stateMachine->getCurrentState()->getProperties());
//        echo "<br> =========== <br>";
        echo "<br> b. transitions: "; var_dump($stateMachine->getCurrentState()->getTransitions());

        $tran = $stateMachine->getCurrentState()->getTransitions();
        //dd($tran);
        echo "<br> B4 Apply transition: ";
        var_dump($stateMachine->getCurrentState()->getName());
        echo "<br> Apply transition: ";
        $stateMachine->apply($tran[0]);

        echo "<br> name: "; var_dump($stateMachine->getCurrentState()->getName());

        // set state
        //$document->setFiniteState('s2');
        // get state
        // dd($document->getFiniteState());

        //getTransitions($object, $graph = 'default', $asObject = false)
        // get list of array of transitions
        // dd($stateMachine->getTransitions());
        // dd($stateMachine->getObject());
        // dd($stateMachine->getGraph());


    }

//    public function getStates($callflow_id)
//    {
//        $tbl_state = new tblstate;
//        $states = $this->tbl_states->getStatesFromStateTable($callflow_id);
//       // dd($states);
//        return $states;
//
//    }

    /* public function getTransitions()
     {
         $tbl_state = new tbltransition;
         $transitions = $tbl_state->getTranstionsFromStateTable('1');
         dd($transitions);
     }*/

//    public function getTransitions()
//    {
//        $states = $this->getStates('1');
//        $transistions = array();
//        foreach ($states as $state)
//        {
//
//        }
//        dd($states);
//    }

//    public  function lookupForStateName($new_state_id)
//    {
//        return
//    }

    public function changeState($indexOfTrans)
    {
        //dd($sm);
        //echo "<br>change state<br>";
        //echo "<br> B4 Apply transition: ";
        //var_dump($stateMachine->getCurrentState()->getName());
//        $tran = $this->stateMachine->getCurrentState()->getTransitions(); // error because $stateMachine == null
//        dd($stateMachine);
//        $this->stateMachine->apply($tran[$indexOfTrans]);
//        $new_state = $this->stateMachine->getCurrentState()->getName();
        //dd($new_state);
//        $this->tbl_call->updateCallData($this->callID,$new_state);
//        dd($indexOfTrans->state);
        $this->tbl_call->updateCallData('CA000d44bb9266cf88d59d7b0b3f9d7fbe',$indexOfTrans->state);

        return $indexOfTrans;
    }
    /**
     * Responds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function showWelcome($sm)
    {
//        echo "<br> show welcome <br>";
        $response = new Twiml();
        $response->say('Please Enter 3 digits of input');
        //dd($stateMachine);
        $this->changeState($sm);

        return $response;


    }

    public function gatherInput()
    {
//        echo "<br> gather input <br>";
        //echo "gatherInput function<br>";
        $response = new Twiml();
        $response->gather(
            [
                'numDigits' => 3,
                'action' => $this->ngrok_address . "/ivr/validation_sound_file"
            ]
        );

        return $response;
    }

    public function validation_sound_file(Request $request)
    {
//        echo "<br> validation sound file <br>";
        $sound_file_name = $request->input('Digits').".mp3";
        $url = "http://itenure.net/sounds/";
        $header_response = get_headers($url.$sound_file_name, 1);
        if(strpos($header_response[0], "404")!==false )
        {
            // FILE DOES NOT EXIST
            $this->changeState(0);
            return 0;
        }
        else
        {
            // FILE EXISTS
            $this->changeState(1);
            return $sound_file_name;
        }
    }

//    public function C0orC1($input_val)
//    {
//        if(!empty($input_val))
//        {
//            if($input_val == 0)
//                $this->changeState(0);
//            else
//            {
//                $this->changeState(1);
//                $this->url_sound = $input_val;
//            }
//        }
//    }

    public function displayIncorrectInput()
    {
//        echo "<br> display incorrect input <br>";
        $this->response->say('input is incorrect, please try again');
        $this->changeState(0);
        $this->response->redirect($this->ngrok_address . "/ivr/gatherInput");
        return $this->response;
    }

    public function playSoundFile()
    {
//        echo "<br> play sound file <br>";
        //$this->response->play('http://itenure.net/sounds/' . $sound_file_name);
        $this->response->play('http://itenure.net/sounds/357.mp3');
        $this->changeState(0);
        return $this->response;
    }

    public function hangup()
    {
//        echo "<br>hangup<br>";
        $this->changeState(0);
        $this->response->hangup();
    }

    public function makeCall($sm)
    {
        // array("url" => "http://demo.twilio.com/docs/voice.xml")
        //echo "makeCall function<br>";
        $test_phone_number = "+85517696365";
        $twilio_sid = env('TWILIO_ACCOUNT_SID');
        $twilio_token = env('TWILIO_AUTH_TOKEN');
        $twilio_phone_number = env('TWILIO_NUMBER');

//        $client = new Client($twilio_sid, $twilio_token);
//        $call = $client->calls->create(
//            $test_phone_number,
//            $twilio_phone_number,
//            array("url" => $this->ngrok_address . "/ivr/welcome")
//        );
//
//       echo "<br>" . $call->sid . "<br>";
//        $this->tbl_call->insertNewCallData($call->sid, 'A');
        //dd($stateMachine);
//        $this->changeState($sm);
    }




//    public function test_validation()
//    {
//       return $this->validation_sound_file("0.mp3");
//    }





}