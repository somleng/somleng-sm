<?php

namespace App\Http\Controllers;

//use App\MyStateMachine\AllFunctions;
use App\Models\tblstate;
use App\MyStateMachine\Stateful;
use Finite\Loader\ArrayLoader;
use Finite\State\StateInterface;
use Finite\StateMachine\StateMachine;

class StateMachineCnt extends Controller
{

    /** To insert transition testign data in tblstate */
    public function insert_transition_test_data()
    {
        $tbl_state = new tblstate;
        // Model insertNewTransitionData($state, $input=null, $callflow_id, $twilml=null, $path=null, $action=null, $new_state, $state_type)
        $tbl_state->insertNewTransitionData('s0', '1', '1', null, '/public/test.xml', null, 's1', '1');
        $tbl_state->insertNewTransitionData('s0', '2', '1', null, '/public/test.xml', null, 's2', '');
        $tbl_state->insertNewTransitionData('s0', '3', '1', null, '/public/test.xml', null, 's3', '');
        $tbl_state->insertNewTransitionData('s1', '1', '1', null, '/public/test.xml', null, 's4', '');
        $tbl_state->insertNewTransitionData('s4', '1', '1', null, '/public/test.xml', null, 's1', '');
        $tbl_state->insertNewTransitionData('s4', '2', '1', null, '/public/test.xml', null, 's0', '');
        $tbl_state->insertNewTransitionData('s4', '3', '1', null, '/public/test.xml', null, 's3', '');
        $tbl_state->insertNewTransitionData('s4', '4', '1', null, '/public/test.xml', null, 'hangup', '');
        $tbl_state->insertNewTransitionData('s2', '', '1', null, '/public/test.xml', null, 'hangup', '');
        $tbl_state->insertNewTransitionData('s3', '', '1', null, '/public/test.xml', null, 'hangup', '');
        $tbl_state->insertNewTransitionData('hangup', '', '1', '/public/test.xml', null, null, '', '2');

        echo "Transition test data are inserted.";

    }


    /**
     *
     */
    public function example_new()
    {
        // Create States for Graph
        $getStates = $this->getStates();
        $arrayStringStates = array();
        foreach ($getStates as $getState){
            echo $getState;
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

            $arrayStringStates[] = array(
                $state_name => array(
                    'type' => $state_type_str,
                    'properties' => array(),
                ),
            );
        }
        dd($arrayStringStates);
        // Create Transitions for Graph
        $getTransitions = $this->getTransitions();
        $arrayStringTransitions = array();
        foreach ($getTransitions as $getTransition){
            echo $getTransition;
            $transition_name = $getTransition['state_name'].'-'.$getTransition['input'];
            $new_state = $getTransition['new_state'];
            $fromStates = array($getTransition['state_name']);
            $toStates = array($new_state);
            $arrayStringStates[] = array(
                $transition_name => array(
                    'from' => $fromStates,'to' => $toStates,
                ),
            );
        }
        // Configure your graph
        $document     = new Stateful;
        $stateMachine = new StateMachine($document);
        $loader       = new ArrayLoader(array(
            'class'  => 'Document',
            'states'  => array(

                's0' => array(
                   'type'       => StateInterface::TYPE_INITIAL,
                    'properties' => array(),
                ),

                's1' => array(
                    'type'       => StateInterface::TYPE_NORMAL,
                    'properties' => array(),
                ),
                's2' => array(
                    'type'       => StateInterface::TYPE_NORMAL,
                    'properties' => array(),
                ),
                's3' => array(
                    'type'       => StateInterface::TYPE_NORMAL,
                    'properties' => array(),
                ),
                's4' => array(
                    'type'       => StateInterface::TYPE_NORMAL,
                    'properties' => array(),
                ),
                'hangout' => array(
                    'type'       => StateInterface::TYPE_FINAL,
                    'properties' => array(),
                )
            ),
            'transitions' => array(
                's01' => array('from' => array('s0'), 'to' => 's1'),
                's02' => array('from' => array('s0'), 'to' => 's2'),
                's03' => array('from' => array('s0'), 'to' => 's3'),
                's11' => array('from' => array('s1'), 'to' => 's4'),
                's41' => array('from' => array('s4'), 'to' => 's1'),
                's42' => array('from' => array('s4'), 'to' => 's0'),
                's43' => array('from' => array('s4'), 'to' => 's3'),
                's44' => array('from' => array('s4'), 'to' => 'hangout'),
            ),
//            'callbacks' => array(
//                'before' => array(
//                    array(
//                        'from' => '-proposed',
//                        'do' => function(StatefulInterface $document, TransitionEvent $e) {
//                            echo '<br> Applying transition '.$e->getTransition()->getName(), "\n";
//                        }
//                    ),
//                    array(
//                        'from' => 'proposed',
//                        'do' => function() {
//                            echo '<br> Applying transition from proposed state', "\n";
//                        }
//                    )
//                ),
//                'after' => array(
//                    array(
//                        'to' => array('accepted'), 'do' => array($document, 'display')
//                    )
//                )
//            )
        ));
        $loader->load($stateMachine);
        $stateMachine->initialize();
        dd($loader);

        $stateMachine->apply('s02');

        // Working with workflow
        // Current state
        echo "<br> 1. current state ====== ";
        echo "<br> name: "; var_dump($stateMachine->getCurrentState()->getName());
        echo "<br> properties: "; var_dump($stateMachine->getCurrentState()->getProperties());
        echo "<br> =========== <br>";

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

    public function getStates()
    {
        $tbl_state = new tblstate;
        $states = $tbl_state->getStatesFromStateTable('1');
        return $states;
        //dd($states);
    }

    public function getTransitions()
    {
        $tbl_state = new tblstate;
        $transitions = $tbl_state->getTranstionsFromStateTable('1');
        dd($transitions);
    }
}