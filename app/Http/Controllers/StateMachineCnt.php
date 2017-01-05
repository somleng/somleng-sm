<?php

namespace App\Http\Controllers;

//use App\MyStateMachine\AllFunctions;
use App\Models\tblcall;
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

    public function __construct()
    {
        $this->tbl_transition = new tbltransition;
        $this->tbl_call = new tblcall;
    }

    /** To insert transition testing data in tblstate */
    public function insert_transition_test_data()
    {
        // Model insertNewTransitionData($state, $input=null, $callflow_id, $twilml=null, $path=null, $action=null, $new_state, $state_type)
        $this->tbl_transition->insertNewTransitionData('s0', '1', '1', null, '/public/test.xml', null, 's1', '1');
        $this->tbl_transition->insertNewTransitionData('s0', '2', '1', null, '/public/test.xml', null, 's2', '');
        $this->tbl_transition->insertNewTransitionData('s0', '3', '1', null, '/public/test.xml', null, 's3', '');
        $this->tbl_transition->insertNewTransitionData('s1', '1', '1', null, '/public/test.xml', null, 's4', '');
        $this->tbl_transition->insertNewTransitionData('s4', '1', '1', null, '/public/test.xml', null, 's1', '');
        $this->tbl_transition->insertNewTransitionData('s4', '2', '1', null, '/public/test.xml', null, 's0', '');
        $this->tbl_transition->insertNewTransitionData('s4', '3', '1', null, '/public/test.xml', null, 's3', '');
        $this->tbl_transition->insertNewTransitionData('s4', '4', '1', null, '/public/test.xml', null, 'hangup', '');
        $this->tbl_transition->insertNewTransitionData('s2', '', '1', null, '/public/test.xml', null, 'hangup', '');
        $this->tbl_transition->insertNewTransitionData('s3', '', '1', null, '/public/test.xml', null, 'hangup', '');
        $this->tbl_transition->insertNewTransitionData('hangup', '', '1', '/public/test.xml', null, null, '', '2');
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

    public function example_new()
    {
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
}