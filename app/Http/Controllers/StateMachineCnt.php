<?php

namespace App\Http\Controllers;

use App\Jobs\SendRequestToSomleng;
use App\Models\tblcall;
use App\Models\tblcallflow;
use App\Models\tblstate;
use App\Models\tbltransition;
use App\Models\tbltwimlafterqueue;
use App\MyStateMachine\Stateful;
use Finite\Event\TransitionEvent;
use Finite\Loader\ArrayLoader;
use Finite\State\StateInterface;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine;
use Illuminate\Http\Request;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
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

    /** To insert transition testing data in tblstate */
    public function insert_transition_test_data()
    {
        $callflow_tbl = new tblcallflow;
        $callflow_id = $callflow_tbl->insertNewCallflow('callflow_1');

        // NEW GRAPH THAT SEPARATE VALIDATION FUNCTION FROM GATHERING
        $this->tbl_transition->insertNewTransitionData('A', null, $callflow_id, null, '/public/TwiMLCodeToPlayMessage.xml', null, 'B', '1');
        $this->tbl_transition->insertNewTransitionData('B', null, $callflow_id, null, 'Gater 5 digits', null, 'C', '');
        $this->tbl_transition->insertNewTransitionData('C', null, $callflow_id, null, 'Validation', null, 'D', '');
        $this->tbl_transition->insertNewTransitionData('D', '0', $callflow_id, null, 'Play invalid input, please try again', null, 'E0', '');
        $this->tbl_transition->insertNewTransitionData('D', '1', $callflow_id, null, 'Play file5digits_twiML.xml', null, 'E1', '');
        $this->tbl_transition->insertNewTransitionData('E0', null, $callflow_id, null, 'redirect to gathering', null, 'B', '');
        $this->tbl_transition->insertNewTransitionData('E1', null, $callflow_id, null, 'Play (found sound file)', null, 'F', '');
        $this->tbl_transition->insertNewTransitionData('F', null, $callflow_id, null, 'hang up', null, '', '2');

        echo "Transition test data are inserted.";
    }

    /** To insert or update call test data in tblcall */
//    public function insert_update_call_test_data()
//    {
//        $this->tbl_call->insertNewCallData('c001', 'A');
//        /*
//         $this->tbl_call->insertNewCallData('c002', 's1');
//        $this->tbl_call->insertNewCallData('c003', 's4');
//        */
//        echo "Call test data are inserted.";
//        // update call record
//        $this->tbl_call->updateCallData('c003', 'hangup');
//    }
//
//    public function action()
//    {
//        act('c001', null, null);
//    }
//
//    public function act($callid, $choice_input=null, $non_choice_input=null)
//    {
//        //dd($choice_input);
//        $state_id = $this->tbl_call->searchForCallID($callid);
//        $transition_id = $this->tbl_transition->getTransitionID($state_id, $choice_input);
//        //dd($transition_id);
//    }

    /**
     * @param Request $request
     */
    public function sm_callflow(Request $request)
    {
//        $this->dispatch(serialize(new SendRequestToSomleng($request)));
        $job_request = (new SendRequestToSomleng($request)); // => execute constructor
        /* Note: dispatch job execute very late, but the above code is execute ahead*/
        $qId = $this->dispatch($job_request); // => execute handle
        $tbl_twiml_after_queue = new tbltwimlafterqueue;
        $result = $tbl_twiml_after_queue->getTwimlText($qId);
        while(empty($result))
        {
            $result = $tbl_twiml_after_queue->getTwimlText($qId);
        }
        $tbl_twiml_after_queue->deleteRecordOfTwimlAfterQueue($qId);
        return $result;
    }
}