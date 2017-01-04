<?php

namespace App\Http\Controllers;

use App\MyStateMachine\Order;
use App\MyStateMachine\Stateful;
use Finite\Event\Callback\CallbackBuilder;
use Finite\Event\FiniteEvents;
use Finite\Event\TransitionEvent;
use Finite\Exception\TransitionException;
use Finite\Factory\PimpleFactory;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine;
use Finite\State\StateInterface;
use Finite\Loader\ArrayLoader;
use Finite\Exception\StateException;
use Pimple;
use Symfony\Component\OptionsResolver\OptionsResolver;


class StateMachineExampleController extends Controller
{
    public function basic_graph()
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
        //echo "<br> is_initial_state: "; var_dump($stateMachine->isInitial());
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

    public function callback()
    {
        // Configure your graph
        $document     = new Stateful;
        $stateMachine = new StateMachine($document);
        $loader       = new ArrayLoader(array(
            'class'       => 'Document',
            'states'      => array(
                'draft'    => array(
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
            'callbacks' => array(
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
            )
        ));

        $loader->load($stateMachine);
        $stateMachine->initialize();

        $stateMachine->getDispatcher()->addListener(FiniteEvents::PRE_TRANSITION, function(TransitionEvent $e) {
            echo '<br> This is a pre transition', "\n";
        });

        $foobar = 42;
        $stateMachine->getDispatcher()->addListener(
            FiniteEvents::POST_TRANSITION,
            CallbackBuilder::create($stateMachine)
                ->setCallable(function () use ($foobar) {
                    echo "<br> \$foobar is ${foobar} and this is a post transition\n";
                })
                ->getCallback()
        );

        $stateMachine->apply('propose');
        $stateMachine->apply('reject');
        $stateMachine->apply('propose');
        $stateMachine->apply('accept');
    }

    // not working
    public function guard()
    {
        // Configure your graph
        $document     = new Stateful;
        $stateMachine = new StateMachine($document);
        $loader       = new ArrayLoader(array(
            'class'  => 'Document',
            'states'  => array(
                'draft' => array(
                    'type'       => StateInterface::TYPE_INITIAL,
                    'properties' => array(),
                ),
                'proposed' => array(
                    'type'       => StateInterface::TYPE_NORMAL,
                    'properties' => array(),
                ),
                'accepted' => array(
                    'type'       => StateInterface::TYPE_FINAL,
                    'properties' => array(),
                )
            ),
            'transitions' => array(
                'propose' => array('from' => array('draft'), 'to' => 'proposed', 'guard' => 'pass_guard'),
                'accept'  => array('from' => array('proposed'), 'to' => 'accepted', 'guard' => 'fail_guard'),

            ),
        ));

        $loader->load($stateMachine);
        $stateMachine->initialize();

        // testing the guard
        echo "<br> Can we apply propose ? \n";
        var_dump($stateMachine->can('propose'));
        $stateMachine->apply('propose');

        echo "<br> \nCan we apply accept ? \n";
        var_dump($stateMachine->can('accept'));
    }

    public function multiple_graph()
    {
        $order = new Order;
        // Configure the payment graph
        $paymentStateMachine = new StateMachine($order);
        $paymentLoader       = new ArrayLoader([
            'class'         => 'Order',
            'property_path' => 'paymentStatus',
            'states'        => [
                Order::PAYMENT_PENDING  => ['type' => StateInterface::TYPE_INITIAL],
                Order::PAYMENT_ACCEPTED => ['type' => StateInterface::TYPE_FINAL],
                Order::PAYMENT_REFUSED  => ['type' => StateInterface::TYPE_FINAL],
            ],
            'transitions'   => [
                'accept' => ['from' => [Order::PAYMENT_PENDING], 'to' => Order::PAYMENT_ACCEPTED],
                'refuse' => ['from' => [Order::PAYMENT_PENDING], 'to' => Order::PAYMENT_REFUSED],
            ],
        ]);

        $paymentLoader->load($paymentStateMachine);
        $paymentStateMachine->initialize();

        // Configure the shipping graph
        $shippingStateMachine = new StateMachine($order);
        $shippingLoader       = new ArrayLoader([
            'class'         => 'Order',
            'property_path' => 'shippingStatus',
            'states'        => [
                Order::SHIPPING_PENDING => ['type' => StateInterface::TYPE_INITIAL],
                Order::SHIPPING_PARTIAL => ['type' => StateInterface::TYPE_NORMAL],
                Order::SHIPPING_SHIPPED => ['type' => StateInterface::TYPE_FINAL],
            ],
            'transitions'   => [
                'ship_partially' => ['from' => [Order::SHIPPING_PENDING], 'to' => Order::SHIPPING_PARTIAL],
                'ship'           => ['from' => [Order::SHIPPING_PENDING, Order::SHIPPING_PARTIAL], 'to' => Order::SHIPPING_SHIPPED],
            ],
        ]);

        $shippingLoader->load($shippingStateMachine);
        $shippingStateMachine->initialize();

        // Working with workflows
        // Current state
        echo "<br> get current state: "; var_dump($paymentStateMachine->getCurrentState()->getName());
        echo "<br> get properties: "; var_dump($paymentStateMachine->getCurrentState()->getProperties());

        // Available transitions
        echo "<br> get available transitions: "; var_dump($paymentStateMachine->getCurrentState()->getTransitions());
        echo "<br> get can: "; var_dump($paymentStateMachine->can('accept'));
        $paymentStateMachine->apply('accept');
        echo "<br> 2. get current state: "; var_dump($paymentStateMachine->getCurrentState()->getName());

    }

    // not working
    public function multiple_graphs_with_factory()
    {
        $order = new Order;
        // Configure the payment graph
        $paymentLoader       = new ArrayLoader([
            'class'         => 'Order',
            'graph'         => 'payment',
            'property_path' => 'paymentStatus',
            'states'        => [
                Order::PAYMENT_PENDING  => ['type' => StateInterface::TYPE_INITIAL],
                Order::PAYMENT_ACCEPTED => ['type' => StateInterface::TYPE_FINAL],
                Order::PAYMENT_REFUSED  => ['type' => StateInterface::TYPE_FINAL],
            ],
            'transitions'   => [
                'accept' => ['from' => [Order::PAYMENT_PENDING], 'to' => Order::PAYMENT_ACCEPTED],
                'refuse' => ['from' => [Order::PAYMENT_PENDING], 'to' => Order::PAYMENT_REFUSED],
            ],
        ]);

        // Configure the shipping graph
        $shippingLoader       = new ArrayLoader([
            'class'         => 'Order',
            'graph'         => 'shipping',
            'property_path' => 'shippingStatus',
            'states'        => [
                Order::SHIPPING_PENDING => ['type' => StateInterface::TYPE_INITIAL],
                Order::SHIPPING_PARTIAL => ['type' => StateInterface::TYPE_NORMAL],
                Order::SHIPPING_SHIPPED => ['type' => StateInterface::TYPE_FINAL],
            ],
            'transitions'   => [
                'ship_partially' => ['from' => [Order::SHIPPING_PENDING], 'to' => Order::SHIPPING_PARTIAL],
                'ship'           => ['from' => [Order::SHIPPING_PENDING, Order::SHIPPING_PARTIAL], 'to' => Order::SHIPPING_SHIPPED],
            ],
        ]);

        // Configure the factory (Pimple factory is used here)
        $pimple = new Pimple(
            [
                'finite.state_machine' => function () {
                    return new StateMachine;
                }
            ]
        );
        $factory = new PimpleFactory($pimple, 'finite.state_machine');
        $factory->addLoader($paymentLoader);
        $factory->addLoader($shippingLoader);


        // Working with workflows
        $paymentStateMachine = $factory->get($order, 'payment');

        // Current state
        var_dump($paymentStateMachine->getCurrentState()->getName());
        var_dump($paymentStateMachine->getCurrentState()->getProperties());

        // Available transitions
        var_dump($paymentStateMachine->getCurrentState()->getTransitions());
        var_dump($paymentStateMachine->can('accept'));
        $paymentStateMachine->apply('accept');
        var_dump($paymentStateMachine->getCurrentState()->getName());
    }

    public function transition_properties()
    {
        // Configure your graph
        $document     = new Stateful;
        $stateMachine = new StateMachine($document);
        $loader       = new ArrayLoader(array(
            'class'       => 'Document',
            'states'      => array(
                'draft'    => array(
                    'type'       => StateInterface::TYPE_INITIAL,
                    'properties' => array(),
                ),
                'proposed' => array(
                    'type'       => StateInterface::TYPE_NORMAL,
                    'properties' => array(),
                ),
                'accepted' => array(
                    'type'       => StateInterface::TYPE_FINAL,
                    'properties' => array(),
                )
            ),
            'transitions' => array(
                'propose' => array('from' => array('draft'), 'to' => 'proposed'),
                'accept'  => array('from' => array('proposed'), 'to' => 'accepted', 'properties' => ['count' => 0]),
                'reject'  => array(
                    'from' => array('proposed'),
                    'to' => 'draft',
                    'configure_properties' => function(OptionsResolver $optionsResolver) {
                        $optionsResolver->setRequired('count');
                    }
                ),
            ),
            'callbacks' => array(
                'before' => array(
                    array(
                        'do' => function(StatefulInterface $document, TransitionEvent $e) {
                            echo sprintf(
                                "<br> Applying transition \"%s\", count is \"%s\"\n",
                                $e->getTransition()->getName(),
                                $e->get('count', 'undefined')
                            );
                        }
                    )
                )
            )
        ));

        $loader->load($stateMachine);
        $stateMachine->initialize();

        try {
            // Trying with an undefined property
            $stateMachine->apply('propose', ['count' => 1]);
        } catch (TransitionException $e) {
            echo "<br> Property \"propose\" does not exists.\n";
        }
        $stateMachine->apply('propose');

        try {
            // Trying without a mandatory property
            $stateMachine->apply('reject');
        } catch (TransitionException $e) {
            echo "<br> Property \"count\" is mandatory.\n";
        }
        $stateMachine->apply('reject',  ['count' => 2]);

        $stateMachine->apply('propose');

        // Default value is used
        $stateMachine->apply('accept');
    }


    public function pass_guard(StateMachine $stateMachine)
    {
        echo "<br> Pass guard called\n";
        return true;
    }

    public function fail_guard(StateMachine $stateMachine)
    {
        echo "<br> Fail guard called\n";
        return false;
    }
}


