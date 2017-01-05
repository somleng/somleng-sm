<?php
/**
 * Created by PhpStorm.
 * User: phyrum
 * Date: 1/4/17
 * Time: 11:15 AM
 */

namespace App\MyStateMachine;

use App\Models\tblstate;

trait AllFunctions
{
    private $tbl_state;
    private $tbl_call;

    public function __construct()
    {
        $this->tbl_state = new tblstate;
        $this->tbl_call = new tblcall;
    }

    public function act($call_id, $input=null)
    {
        // state
        $state = $this->tbl_call->search($call_id);
        dd($state);
        $transition_id = $this->tbl_state->getTransitionID('s10','');
        dd($transition_id);
        // getTransitionID($state, $input=null)
    }

}