<?php

namespace App\Http\Controllers;

use App\MyStateMachine\StatefulDocument;
use Finite\StateMachine\StateMachine;
use Finite\State\StateInterface;
use Finite\Loader\ArrayLoader;


class StateMachineController extends Controller
{
    public function test()
    {
        // Configure your graph
        $document     = new StatefulDocument;
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



        // set state
        //$document->setFiniteState('s2');
        // get state
        // dd($document->getFiniteState());

        //getTransitions($object, $graph = 'default', $asObject = false)
        // get list of array of transitions
        // dd($stateMachine->getTransitions());
        // dd($stateMachine->getObject());
       // dd($stateMachine->getGraph());


        // Working with workflow
        // Current state
        echo "<br> 1. current state ====== ";
        echo "<br> name: "; var_dump($stateMachine->getCurrentState()->getName());
        echo "<br> properties: "; var_dump($stateMachine->getCurrentState()->getProperties());
        echo "<br> =========== <br>";
//
//        // Available transitions
//        echo "<br> 2. Available transition ======== ";
//        echo "<br> get_transition: "; var_dump($stateMachine->getCurrentState()->getTransitions());
//        echo "<br> can_propose: "; var_dump($stateMachine->can('s11'));
//        echo "<br> can_accept: "; var_dump($stateMachine->can('s12'));
//        echo "<br> can_reject: "; var_dump($stateMachine->can('s13'));
//        echo "<br> =========== <br>";

//        // Apply transitions
//        echo "Try to Apply Transitions <br>";
//        try {
//            $stateMachine->apply('accept');
//        } catch (StateException $e) {
//            echo $e->getMessage(), "\n";
//        }
//        echo "<br><br>";

//        // Applying a transition
//        echo "<br> 3. Applying a Transition ======== ";
//        $stateMachine->apply('s11');
//        echo "<br> get_current_state_name: "; var_dump($stateMachine->getCurrentState()->getName());
//        echo "<br> get_finite_state: "; var_dump($document->getFiniteState());
//        echo "<br> =========== <br>";
//        $stateMachine->apply('s12');
//        echo "<br> get_current_state_name: "; var_dump($stateMachine->getCurrentState()->getName());
//        echo "<br> get_finite_state: "; var_dump($document->getFiniteState());
//        echo "<br> =========== <br>";
//        $stateMachine->apply('s13');
//        echo "<br> get_current_state_name: "; var_dump($stateMachine->getCurrentState()->getName());
//        echo "<br> get_finite_state: "; var_dump($document->getFiniteState());
//        echo "<br> =========== <br>";

    }


}


