<?php
/**
 * Created by PhpStorm.
 * User: phyrum
 * Date: 12/29/16
 * Time: 3:48 PM
 */

namespace App\MyStateMachine;


use Finite\StatefulInterface;

class StatefulDocument implements StatefulInterface
{
    private $state;
    public function getFiniteState()
    {
        return $this->state;
    }
    public function setFiniteState($state)
    {
        $this->state = $state;
    }

    public function display()
    {
        echo '<br> Hello, I\'m a document and I\'m currently at the ', $this->state, ' state.', "\n";
    }
}