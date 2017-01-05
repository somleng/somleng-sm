<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblstate extends Model
{
    protected $table = 'tblstate';
    protected $fillable = ['state', 'state_type'];

    /*public function transition()
    {
        $this->belongsTo('App\Models\tbltransition');
    }*/
    /**
     * Function to Insert new data into tblstate
     * @param $state
     * @param $state_type
     */
    public function insertNewState($state, $state_type=null)
    {
        if(!empty($state))
        {
            $check_existing_record = $this::where('state', $state)->first();
            //var_dump("<br> state='" . $state . "'; existing_record= '" . $check_existing_record . "'<br>");
            if(empty($check_existing_record))
            {
                $inserted = $this::create(['state' => $state,
                    'state_type' => $state_type]);
                return $inserted->id;
            }
            else return $check_existing_record->id;
        }
    }

    public function getTransitionID($state, $input=null)
    {
        $transition_id="";
        // to make sure that state and input are exist in DB
        // to avoid concate transition id which doesn't exist in DB
        $check_existing_record = $this::where('state', $state)
                        ->where('input', $input)
                        ->first();
        if(!empty($check_existing_record))
        {
            if($input != null)
                $transition_id = $state.$input;
            else
                $transition_id = $state;
        }
        return $transition_id;

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