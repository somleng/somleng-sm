<?php

namespace App\Jobs;

use App\Models\tblcall;
use App\Models\tblstate;
use App\Models\tbltransition;
use App\Models\tbltwimlafterqueue;
use App\MyStateMachine\Stateful;
use Finite\Loader\ArrayLoader;
use Finite\State\StateInterface;
use Finite\StateMachine\StateMachine;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Twilio\Twiml;
use Illuminate\Support\Facades\Log;

class SendRequestToSomleng extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $tbl_transition;
    private $tbl_call;
    private $tbl_states;
    private $url_sound;
    private $callID;
    public $response;
    private $call_Sid;
    private $digits;
    private $return_input;
    public $tbl_queue_result;
//    public $request;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
//        dd($request);
//        $this->tbl_transition = new tbltransition;
//        $this->tbl_call = new tblcall;
//        $this->tbl_states = new tblstate;
        $this->tbl_transition ="";
        $this->tbl_call ="";
        $this->tbl_states ="";
        $this->url_sound = "";
        $this->callID = "";
        $this->response="response in constructor";
//        $this->response = new Twiml();
        $this->call_Sid = $request->CallSid;
        $this->digits = $request->Digits;
        $this->return_input = $request->return_input;
        $this->tbl_queue_result = "";
//        Log:info($request);
//        $this->rq = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        echo "handle";
        $this->tbl_transition = new tbltransition;
        $this->tbl_call = new tblcall;
        $this->tbl_states = new tblstate;
        $this->response = new Twiml();
//        $this->response ="here";
        // send request to Somleng
//        $this->call_Sid = $request->CallSid;
//        $this->digits = $request->Digits;
//        $this->return_input = $request->return_input;

//        dd($this->call_Sid);
        /*if(!empty($request->return_input))
        {*/
//        Log::info($request);

//            Log::info("return_input = " .  $request->return_input);
//            Log::info("return_input using REQUEST= " .  $_REQUEST['return_input']);
//            Log::info("return_input using input= " .  Input::post('return_input'));
//            Log::info("return_input using POST= " .  $_POST['return_input']);
//        }

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

            $arrayStringStates [$state_name] = array(
                'type' => $state_type_str,
                'properties' => array()
            );

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

        $loader = new ArrayLoader(array(
            'class'  => 'Document',
            'states'  => $arrayStringStates,
            'transitions' => $arrayStringTransitions,
            'callbacks' => array(
                'before' => array(

                    array(
                        'from' => 'A',
                        'do' => function() {
//                            Log::info($this->call_Sid);
//                            echo $this->playWelcome();
//                            return $this->playWelcome();
                            $this->response = $this->playWelcome();
                        }
                    ),
                    array(
                        'from' => 'B',
                        'do' => function() {
//                            $this->changeState($this->call_Sid, $current_state);
//                            Log::info('gatherInput 1');
//                            echo $this->gatherInput();
//                            return $this->gatherInput();
                            $this->response = $this->gatherInput();
                            //echo $this->response; // has twiml value
                        }
                    ),
                    array(
                        'from' => 'C',
                        'do' => function() {
//                            echo $this->validation_sound_file($this->digits);
//                            return $this->validation_sound_file($this->digits);
                            $this->response = $this->validation_sound_file($this->digits);

                        }
                    ),
                    array(
                        'from' => 'D',
                        'to' => 'E0',
                        'do' => function() {
//                            echo $this->displayIncorrectInput();
//                            return $this->displayIncorrectInput();
                            $this->response = $this->displayIncorrectInput();
                        }
                    ),
                    array(
                        'from' => 'D',
                        'to' => 'E1',
                        'do' => function() {
//                            echo $this->playSoundFile($this->return_input);
//                            return $this->playSoundFile($this->return_input);
                            $this->response = $this->playSoundFile($this->return_input);
                        }
                    ),
                    array(
                        'from' => 'E0',
                        'to' => 'B',
                        'do' => function(){
//                            echo $this->redirectToSM_Callflow();
//                            return $this->redirectToSM_Callflow();
                            $this->response = $this->redirectToSM_Callflow();
                        }
                    ),
                    array(
                        'from' => 'E1',
                        'do' => function() {
//                            echo $this->hangup();
//                            return $this->hangup();
                            $this->response = $this->hangup();
                        }
                    ),

                ),
                'after' => array(
                    array(
                        'to' => array('B'), 'do' => function($current_state) {
                        $this->changeState($this->call_Sid, $current_state);
                    }
                    ),

                    array(
                        'to' => array('C'), 'do' => function($current_state) {
                        $this->changeState($this->call_Sid, $current_state);
                    }
                    ),// validation return input then what to do next???
                    array(
                        'to' => array('D'), 'do' => function($current_state) {
                        $this->changeState($this->call_Sid, $current_state);
                    }
                    ),
                    array(
                        'to' => array('E0'), 'do' => function($current_state) {
                        $this->changeState($this->call_Sid, $current_state);
                    }
                    ),
                    array(
                        'to' => array('E1'), 'do' => function($current_state) {
                        $this->changeState($this->call_Sid, $current_state);
                    }
                    ),
                    array(
                        'to' => array('F'), 'do' => function($current_state) {
                        $this->changeState($this->call_Sid, $current_state);
                    }
                    )
                )

            )
        ));

        $document     = new Stateful;
        /** find CallSid in TblCall
         * if it exists get the state and apply that state
         * if the CallSid is not exist insert this data into TblCall with the default inital state
         *
         */
        $find_call_sid = $this->tbl_call->searchForCallID($this->call_Sid);
        print "<br> callSID = ".$this->call_Sid . " ";
        print "<br> Digits = ".$this->digits  . " ";
        echo 'Queued Id:'. $this->job->getJobId() . " ";
        if(!empty($find_call_sid))
        {
            $state_name = $this->tbl_states->getStateName($find_call_sid);
            $document->setFiniteState($state_name);
            $stateMachine = new StateMachine($document);
            $loader->load($stateMachine);
            $stateMachine->initialize();

            $transition = $stateMachine->getCurrentState()->getTransitions();
//            Log::debug($transition);
            if($this->return_input != 0)
            {
                $stateMachine->apply($transition[1]);
//                Log::debug($transition[1]);
            }
            else // when $return_input = 0 || null
            {
                $stateMachine->apply($transition[0]);
//                Log::info('applying E0?');
            }

        }
        else
        {
            $stateMachine = new StateMachine($document);
            $loader->load($stateMachine);
            $stateMachine->initialize();

            $current_state = $stateMachine->getCurrentState()->getName();
            $this->tbl_call->insertNewCallData($this->call_Sid, $current_state);

            $transition = $stateMachine->getCurrentState()->getTransitions();
            $stateMachine->apply($transition[0]);
        }
        // samak: write into file
        /*  $storage = Storage::disk('public')->put('twiml_result.xml',$this->response);
            $content = Storage::disk('public')->get('twiml_result.xml');
            print "content of file = ". $content;
            return $this->response;
        */

        // phyrum: write into tbltwimlafterqueue
//        Log::info('1=' . $this->call_Sid);
        $tbl_twiml_after_queue = new tbltwimlafterqueue;
//        echo 'Queued Id:'. $this->job->getJobId();
//        $tbl_twiml_after_queue->insertNewTwimlText($this->call_Sid, $this->response);
        $tbl_twiml_after_queue->insertNewTwimlText($this->job->getJobId(), $this->response);
//        echo $tbl_twiml_after_queue->getTwilmlText($this->job->getJobId());



        //echo "test";
//        Log::debug($stateMachine->getCurrentState()->getName());
        //Log:info($this->response);
//        print $storage;
//        var_dump($content);
//        $content_twiml = Storage::get('twiml_result.xml');
//        print "twiml content from file = ". $content_twiml;
//        print "response in handle here = ".$this->response;
//        var_dump($this->response);
//        print "insert = ". $this->tbl_queue_result->insertNew(serialize($this->response));
        //return $this->response = "t";
//        $this->rq = $this->response;
    }

    /*public function sm_callflow_new(Request $request)
    {

//        $this->dispatch(serialize(new SendRequestToSomleng($request)));
        $job_request = new SendRequestToSomleng($request);

        $this->dispatch($job_request);

        return $job_request->getResponse();
        //$job_request
//        return $job_request;
//        Log:info($request);
//        Log:info($test);

        //return $job_request->getResponse();
    }*/

    public function transit($sm,$tran_name)
    {
        $sm->apply($tran_name);
    }

    public function getResponse()
    {
        //echo "test inside getResponse ";
//        echo $this->response;
        return $this->response;

    }

    public function changeState($call_sid, $current_state)
    {
        $this->tbl_call->updateCallData($call_sid,$current_state->state);
        return $current_state;
    }

    /**
     * Responds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function playWelcome()
    {
        try{
            $this->response->say('Please Enter 5 digits of input');
//            $this->response->redirect(route('sm_callflow'));
            $this->response->redirect(url('sm_callflow'));

        }
        catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }

        return $this->response;
    }

    public function gatherInput()
    {
        $this->response->gather(
            [
                'timeout' => 20,
                'numDigits' => 5,
//                'action' => route('sm_callflow')
                'action' => url('sm_callflow')
            ]
        );

        return $this->response;
    }

    public function validation_sound_file($digits)
    {
        $this->response->say("Your Claim number is " . $digits);
        $sound_file_name = $digits.".mp3";
        $url = "http://itenure.net/sounds/";
        $header_response = get_headers($url.$sound_file_name, 1);
        if(strpos($header_response[0], "404")!==false )
        {
            // FILE DOES NOT EXIST
            $this->response->redirect(route('sm_callflow',['return_input' => 0]));
        }
        else
        {
            // FILE EXISTS
            $this->response->redirect(route('sm_callflow',['return_input' => $sound_file_name]));
        }
        return $this->response;
    }

    public function displayIncorrectInput()
    {
        $this->response->say('input is incorrect, please try again');
        $this->response->redirect(route('sm_callflow'));
        return $this->response;
    }
    public function redirectToSM_Callflow()
    {
        $this->response->redirect(route('sm_callflow'));
        return $this->response;
    }

    public function playSoundFile($sound_file_name)
    {
        $this->response->play('http://itenure.net/sounds/' . $sound_file_name);
        $this->response->redirect(route('sm_callflow'));
        return $this->response;
    }

    public function hangup()
    {
        $this->response->hangup();
        return $this->response;
    }
}
