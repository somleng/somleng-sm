<?php

namespace App\Http\Controllers;

//use App\MyStateMachine\AllFunctions;
use App\Models\tblcall;
use App\Models\tblcallflow;
use App\Models\tblstate;
use App\Models\tbltransition;
use App\MyStateMachine\AllFunctions;
use App\MyStateMachine\Stateful;
use Finite\Loader\ArrayLoader;
use Finite\State\StateInterface;
use Finite\StateMachine\StateMachine;

class StateMachineCnt extends Controller
{
    private $tbl_transition;
    private $tbl_call;
    public  $tbl_states;

    public function __construct()
    {
        $this->tbl_transition = new tbltransition;
        $this->tbl_call = new tblcall;
        $this->tbl_states = new tblstate;
    }

    /** To insert transition testing data in tblstate */
    public function insert_transition_test_data()
    {
        $callflow_tbl = new tblcallflow;
        $callflow_id = $callflow_tbl->insertNewCallflow('callflow_1');
//        dd($callflow_id);

        // Model insertNewTransitionData($state, $input=null, $callflow_id, $twilml=null, $path=null, $action=null, $new_state, $state_type)
        $this->tbl_transition->insertNewTransitionData('s0', '1', $callflow_id, null, '/public/test.xml', null, 's1', '1');
        $this->tbl_transition->insertNewTransitionData('s0', '2', $callflow_id, null, '/public/test.xml', null, 's2', '');
        $this->tbl_transition->insertNewTransitionData('s0', '3', $callflow_id, null, '/public/test.xml', null, 's3', '');
        $this->tbl_transition->insertNewTransitionData('s1', '1', $callflow_id, null, '/public/test.xml', null, 's4', '');
        $this->tbl_transition->insertNewTransitionData('s4', '1', $callflow_id, null, '/public/test.xml', null, 's1', '');
        $this->tbl_transition->insertNewTransitionData('s4', '2', $callflow_id, null, '/public/test.xml', null, 's0', '');
        $this->tbl_transition->insertNewTransitionData('s4', '3', $callflow_id, null, '/public/test.xml', null, 's3', '');
        $this->tbl_transition->insertNewTransitionData('s4', '4', $callflow_id, null, '/public/test.xml', null, 'hangup', '');
        $this->tbl_transition->insertNewTransitionData('s2', '', $callflow_id, null, '/public/test.xml', null, 'hangup', '');
        $this->tbl_transition->insertNewTransitionData('s3', '', $callflow_id, null, '/public/test.xml', null, 'hangup', '');
        $this->tbl_transition->insertNewTransitionData('hangup', '', $callflow_id, '/public/test.xml', null, null, '', '2');
        echo "Transition test data are inserted.";
    }

    /** To insert or update call test data in tblcall */
    public function insert_update_call_test_data()
    {
//        $tbl_call = new tblcall;
        $this->tbl_call->insertNewCallData('c001', '1');
        $this->tbl_call->insertNewCallData('c002', '2');
        $this->tbl_call->insertNewCallData('c003', '3');
        echo "Call test data are inserted.";

        // update call record
        $this->tbl_call->updateCallData('c003', '4');
    }

    public function act()
    {
        AllFunctions::act("c001", '1');

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
        $document     = new Stateful;
        $stateMachine = new StateMachine($document);
        $loader       = new ArrayLoader(array(
            'class'  => 'Document',
            'states'  => $arrayStringStates,
            'transitions' => $arrayStringTransitions,
            /*'callbacks' => array(
                'before' => array(
                    array(
                        'from' => '-proposed',
                        'do' => function(StatefulInterface $document, TransitionEvent $e) {
                            echo '<br> Applying transition '.$e->getTransition()->getName(), "\n";
                        }
                    ),
                    array(
                        'from' => 'proposed',
                        'do' => function() {
                            echo '<br> Applying transition from proposed state', "\n";
                        }
                    )
                ),
                'after' => array(
                    array(
                        'to' => array('accepted'), 'do' => array($document, 'display')
                    )
                )
            )*/
        ));

//        var_dump(json_decode($arrayStringTransitions));die;
        $loader->load($stateMachine);
        $stateMachine->initialize();
//        dd($loader);

        //$stateMachine->apply('s02');

        // Working with workflow
        // Current state
        echo "<br> 1. current state ====== ";
        echo "<br> name: "; var_dump($stateMachine->getCurrentState()->getName());
        echo "<br> properties: "; var_dump($stateMachine->getCurrentState()->getProperties());
        echo "<br> =========== <br>";
        echo "<br> transitions: "; var_dump($stateMachine->getCurrentState()->getTransitions());

        echo "<br> Apply transition: ";
        $stateMachine->apply('s0-2');
        echo "<br> 1. current state ====== ";
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
}