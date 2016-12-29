<?php

namespace App\Http\Controllers;

use App\MyStateMachine\Stateful;
use Finite\StateMachine\StateMachine;
use Finite\State\StateInterface;
use Finite\Loader\ArrayLoader;
use Finite\Exception\StateException;

class StateMachineController extends Controller
{
    public function test()
    {
        // Configure your graph
        $document     = new Stateful;
        $stateMachine = new StateMachine($document);
        $loader       = new ArrayLoader(array(
            'class'  => 'Document',
            'states'  => array(
                'draft' => array(
                    'type'       => StateInterface::TYPE_INITIAL,
                    'properties' => array('deletable' => true, 'editable' => true),
                ),
                'proposed' => array(
                    'type'       => StateInterface::TYPE_NORMAL,
                    'properties' => array(),
                ),
                'accepted' => array(
                    'type'       => StateInterface::TYPE_FINAL,
                    'properties' => array('printable' => true),
                )
            ),
            'transitions' => array(
                'propose' => array('from' => array('draft'), 'to' => 'proposed'),
                'accept'  => array('from' => array('proposed'), 'to' => 'accepted'),
                'reject'  => array('from' => array('proposed'), 'to' => 'draft'),
            ),
        ));
        $loader->load($stateMachine);
        $stateMachine->initialize();



// Working with workflow

// Current state
        echo "current state";
        echo "<br> current_state_name: "; var_dump($stateMachine->getCurrentState()->getName());
        echo "<br> current_state_properties: "; var_dump($stateMachine->getCurrentState()->getProperties());
        echo "<br> current_state_has_deletable: "; var_dump($stateMachine->getCurrentState()->has('deletable'));
        echo "<br> current_state_has_editable: "; var_dump($stateMachine->getCurrentState()->has('editable'));
        echo "<br> current_state_has_printable: "; var_dump($stateMachine->getCurrentState()->has('printable'));
        echo "<br><br>";

// Available transitions
        echo "Available transition";
        echo "<br> current_state_get_transition: "; var_dump($stateMachine->getCurrentState()->getTransitions());
        echo "<br> current_state_can_propose: "; var_dump($stateMachine->can('propose'));
        echo "<br> current_state_can_accept: "; var_dump($stateMachine->can('accept'));
        echo "<br> current_state_can_reject: "; var_dump($stateMachine->can('reject'));
        echo "<br><br>";

// Apply transitions
        echo "Try to Apply Transitions <br>";
        try {
            $stateMachine->apply('accept');
        } catch (StateException $e) {
            echo $e->getMessage(), "\n";
        }
        echo "<br><br>";

// Applying a transition
        echo "Applying a Transition <br>";
        $stateMachine->apply('propose');
        // $stateMachine->apply('reject');
        echo "<br> get_current_state_name: "; var_dump($stateMachine->getCurrentState()->getName());
        echo "<br> get_finite_state: "; var_dump($document->getFiniteState());
        echo "<br><br>";
        $stateMachine->apply('reject');
        // $stateMachine->apply('reject');
        echo "<br> get_current_state_name: "; var_dump($stateMachine->getCurrentState()->getName());
        echo "<br> get_finite_state: "; var_dump($document->getFiniteState());
        echo "<br><br>";

    }
}


