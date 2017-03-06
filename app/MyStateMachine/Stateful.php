<?php
/**
 * Created by PhpStorm.
 * User: phyrum
 * Date: 12/29/16
 * Time: 3:48 PM
 */

namespace App\MyStateMachine;


use Finite\StatefulInterface;

class Stateful implements StatefulInterface
{
    public $state;

    /**
     * Get state
     * @return mixed
     */
    public function getFiniteState()
    {
        return $this->state;
    }

    /**
     * Set state
     * @param string $state
     */
    public function setFiniteState($state)
    {
        $this->state = $state;
    }
}

