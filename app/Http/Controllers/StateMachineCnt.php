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
    private $stateMachine;
    private $response;
    private $document;

    public function __construct()
    {
        $this->tbl_transition = new tbltransition;
        $this->tbl_call = new tblcall;
        $this->tbl_states = new tblstate;
        $this->ngrok_address = "https://30b8d7d3.ngrok.io";
        $this->url_sound = "";
        $this->callID = "";
        $this->response = new Twiml();
        $this->document     = new Stateful;
        $this->stateMachine = new StateMachine($this->document);
        echo "test";
        //dd($this->stateMachine);
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
            var_dump($test->state);
            //dd($test);
            foreach($test->transition as $transition)
            {
                dd($transition->state_id);
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
        dd($transition_id);



    }

    /**
     *
     */
    public function example_new()
    {
        // Create States for Graph
//        $getStates = $this->getStates('1');
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

        $loader = new ArrayLoader(array(
            'class'  => 'Document',
            'states'  => $arrayStringStates,
            'transitions' => $arrayStringTransitions
        ,
            'callbacks' => array(
               /* 'before' => array(
//                    array(
//                        'from' => 'B',
//                        'do' => $this->C0orC1($input_val = $this->gatherInput())
//                    )
                ),*/
                'after' => array(
                    array(
                        'to' => array('A'), 'do' => array($this, 'makeCall')
                    )
                ,
                    array(
                        'to' => array('B'), 'do' => array($this, 'gatherInput')
                    )
               ,
                    array(
                        'to' => array('C0'), 'do' => $this->displayIncorrectInput()
                    ),
                    array(
                        'to' => array('C1'), 'do' => $this->playSoundFile($this->url_sound)
                    ),
                    array(
                        'to' => array('D'), 'do' => $this->hangup()
                    )
                )
            )
        ));

//        var_dump(json_decode($arrayStringTransitions));die;
        $loader->load($this->stateMachine);
        $this->stateMachine->initialize();
//        dd($this->stateMachine);
        dd($loader);

        //$stateMachine->apply('s02');

        // Working with workflow
        // Current state
        echo "<br> 1. current state ====== ";
        echo "<br> name: "; var_dump($this->stateMachine->getCurrentState()->getName());
        echo "<br> properties: "; var_dump($this->stateMachine->getCurrentState()->getProperties());
        echo "<br> =========== <br>";
        echo "<br> transitions: "; var_dump($this->stateMachine->getCurrentState()->getTransitions());
        $tran = $this->stateMachine->getCurrentState()->getTransitions();

        echo "<br> Apply transition: ";
        $this->stateMachine->apply($tran[0]);
        echo "<br> 1. current state ====== ";
        echo "<br> name: "; var_dump($this->stateMachine->getCurrentState()->getName());

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
        dd($this->stateMachine);
        $tran = $this->stateMachine->getCurrentState()->getTransitions();
        $new_state = $this->stateMachine->apply($tran[$indexOfTrans]);
        $this->tbl_call->updateCallData($this->callID,$new_state);
        return $new_state;
    }
    /**
     * Responds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function showWelcome()
    {
        $response = new Twiml();
        $response->say('Please Enter 3 digits of input');
        $this->changeState(0);

        return $response;
    }


    public function gatherInput()
    {
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
        $sound_file_name = $request->input('Digits');
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

    public function C0orC1($input_val)
    {
        if(!empty($input_val))
        {
            if($input_val == 0)
                $this->changeState(0);
            else
            {
                $this->changeState(1);
                $this->url_sound = $input_val;
            }
        }
    }

    public function displayIncorrectInput()
    {
            $this->response->say('input is incorrect, please try again');
            $this->changeState(0);
            $this->response->redirect($this->ngrok_address . "/ivr/gatherInput");
    }

    public function playSoundFile($sound_file_name)
    {
        $this->response->play('http://itenure.net/sounds/' . $sound_file_name);
    }

    public function hangup()
    {
        $this->changeState(0);
        $this->response->hangup();
    }

    public function makeCall()
    {
        // array("url" => "http://demo.twilio.com/docs/voice.xml")
        $test_phone_number = "+85517696365";
        $twilio_sid = env('TWILIO_ACCOUNT_SID');
        $twilio_token = env('TWILIO_AUTH_TOKEN');
        $twilio_phone_number = env('TWILIO_NUMBER');

        $client = new Client($twilio_sid, $twilio_token);
        $call = $client->calls->create(
            $test_phone_number,
            $twilio_phone_number,
            array("url" => $this->ngrok_address . "/ivr/welcome")
        );

        echo $call->sid;
        $this->tbl_call->insertNewCallData($call->sid, 'A');
    }




//    public function test_validation()
//    {
//       return $this->validation_sound_file("0.mp3");
//    }





}