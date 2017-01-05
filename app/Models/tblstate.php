<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblstate extends Model
{
    protected $table = 'tblstate';

    /**
     * Function to Insert new data into tblstate
     * @param $state
     * @param null $input
     * @param $callflow_id
     * @param null $twilml
     * @param null $path
     * @param null $action
     * @param $new_state
     * @param $state_type
     */
    public function insertNewTransitionData($state, $input=null, $callflow_id, $twilml=null, $path=null, $action=null, $new_state, $state_type)
    {
        $tbl_state = new tblstate;
        $tbl_state->state = $state;
        $tbl_state->input = $input;
        $tbl_state->callflow_id = $callflow_id;
        $tbl_state->twilml = $twilml;
        $tbl_state->path = $path;
        $tbl_state->action = $action;
        $tbl_state->new_state = $new_state;
        $tbl_state->state_type = $state_type;
        $tbl_state->save();
    }


    /**
     * Function to get states of specific callflow from tblstate
     * @param $CallFlow_ID is call flow id
     * author: Samak
     */
    public function getStatesFromStateTable($CallFlow_ID)
    {
        $callflowStates = $this::distinct()->select('state','state_type')->where('callflow_id', $CallFlow_ID)->get();
        return $callflowStates;
    }

    /**
     * Function to get transitions of specific callflow from tblstate
     * @param $CallFlow_ID is call flow id
     * * author: Samak
     */
    public function getTranstionsFromStateTable($CallFlow_ID)
    {
        $callflowTransitions = $this::select('state','input')->where('callflow_id', $CallFlow_ID)->get();
        return $callflowTransitions;
    }
}