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
use Illuminate\Http\Request;
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
    private $array_loader;

    public function __construct()
    {
        $this->tbl_transition = new tbltransition;
        $this->tbl_call = new tblcall;
        $this->tbl_states = new tblstate;
        $this->ngrok_address = "http://a56d85dc.ngrok.io";
        $this->url_sound = "";
        $this->callID = "";
        $this->response = new Twiml();
        $this->document     = new Stateful;
        $this->stateMachine = new StateMachine($this->document);

        // create transition
        $getStates = $this->tbl_states->getStatesFromStateTable('1');
        $arrayStringStates = array();
        $arrayStringTransitions = array();
        foreach ($getStates as $getState){
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
            $arrayStringStates [$state_name] = array(
                'type' => $state_type_str,
                'properties' => array()
            );
            $Transitions = $getState->transition;

            foreach ($Transitions as $Transition)
            {
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
        //dd($arrayStringTransitions);

        $loader = array(
            'class'  => 'Document',
            'states'  => $arrayStringStates,
            'transitions' => $arrayStringTransitions,
            'callbacks' => array(
                'before' => array(
                    array(
                        'from' => 'A',
                        'do' => array($this, $this->makeCall())
                    ),
//                    array(
//                        'from' => 'proposed',
//                        'do' => function() {
//                            echo '<br> Applying transition from proposed state', "\n";
//                        }
//                    )
                ),
//                'after' => array(
//                    array(
//                        'to' => array('B'), 'do' => function(){
//                        // $this->gatherInput();
//                        $this->makeCall();
//                    }
//                    )
//                )
            )
        );

//                'after' => array(
//                    array(
//                        'from' => array('A'), 'do' => array($this, $this->makeCall())
//                    ),
//                    array(
//                        'from' => array('B'), 'do' => array($this, $this->gatherInput())
//                    ),
//                    array(
//                        'from' => array('C0'), 'do' => array($this, $this->displayIncorrectInput())
//                    ),
//                    array(
//                        'from' => array('C1'), 'do' => array($this, $this->playSoundFile())
//                    ),
//                    array(
//                        'from' => array('D'), 'do' => array($this, $this->hangup())
//                    )
//                )

        $array_loader = new ArrayLoader($this->loader);
        $array_loader->load($this->stateMachine);
        //dd($this->loader);
        $this->stateMachine->initialize();
        //dd($this->stateMachine);

    }

    public function example_new()
    {
        dd($this->stateMachine);
        echo "<br> 1. current state ====== ";
        echo "<br> a. name: "; var_dump($this->stateMachine->getCurrentState()->getName());
        echo "<br> b. properties: "; var_dump($this->stateMachine->getCurrentState()->getProperties());
        echo "<br> c. transitions: "; var_dump($this->stateMachine->getCurrentState()->getTransitions());
        echo "<br> =========== <br>";
        $tran = $this->stateMachine->getCurrentState()->getTransitions();

        echo "<br> 2. Apply transition ==== <br>: "; $this->stateMachine->apply($tran[0]);
        echo "<br> a. current state_name: "; var_dump($this->stateMachine->getCurrentState()->getName());
        echo "<br> =========== <br>";

    }

    public function makeCall()
    {
        $test_phone_number = "+85589555127";
        $twilio_sid = env('TWILIO_ACCOUNT_SID');
        $twilio_token = env('TWILIO_AUTH_TOKEN');
        $twilio_phone_number = env('TWILIO_NUMBER');

        $client = new Client($twilio_sid, $twilio_token);
        //dd($client);
        $call = $client->calls->create(
            $test_phone_number,
            $twilio_phone_number,
            array("url" => $this->ngrok_address . "/ivr/welcome")
        );

        echo $call->sid;
        $this->tbl_call->insertNewCallData($call->sid, 'A');
        //$this->changeState(0);
    }

    public function changeState($indexOfTrans)
    {
       // dd($this->stateMachine);
        //dd($this->loader);
        $tran = $this->stateMachine->getCurrentState()->getTransitions();
        $new_state = $this->stateMachine->apply($tran[$indexOfTrans]);
        $this->tbl_call->updateCallData($this->callID,$new_state);
        return $new_state;
    }

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


    public function displayIncorrectInput()
    {
        $this->response->say('input is incorrect, please try again');
        $this->changeState(0);
        $this->response->redirect($this->ngrok_address . "/ivr/gatherInput");
        return $this->response;
    }

    public function playSoundFile($sound_file_name)
    {
        $this->response->play('http://itenure.net/sounds/' . $sound_file_name);
        $this->response->redirect($this->ngrok_address . "/ivr/hangup");
        return $this->response;
    }

    public function hangup()
    {
        $this->changeState(0);
        $this->response->hangup();
        return $this->response;
    }




}