<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblcall extends Model
{
    protected $table = 'tblcall';
    protected $fillable = ['call_id', 'state_id'];

    /**
     * Function to insert New record into tblcall
     * @param $call_id
     * @param $current_state : name of state
     */
    public function insertNewCallData($call_id, $current_state)
    {
        $state_tbl = new tblstate;
        $state_id = $state_tbl->findStateID($current_state);
        if(!empty($state_id))
        {
            $check_existing_record = $this::where('call_id', $call_id)->first();
            if(empty($check_existing_record))
                $this::create(['call_id' => $call_id, 'state_id' => $state_id]);
        }
    }

    /**
     * Function to update record in tblcall
     * @param $call_id
     * @param $current_state
     */
    public function updateCallData($call_id, $current_state)
    {
        $state_tbl = new tblstate;
        $state_id = $state_tbl->findStateID($current_state);
        if(!empty($state_id))
        {
            $update_call_state = $this::where('call_id', $call_id)->first();
            if(!empty($update_call_state))
            {
                $update_call_state->state_id=$state_id;
                $update_call_state->save();
            }
        }
    }

    /**
     * search for Call ID
     * @param $call_id
     * @return mixed
     */
    public function searchForCallID($call_id)
    {
        $find_call_id = $this::where('call_id', $call_id)->first();
        if(!empty($find_call_id))
            return $find_call_id->state_id;
    }
}