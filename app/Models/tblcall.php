<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblcall extends Model
{
    protected $table = 'tblcall';
    protected $fillable = ['call_id', 'state'];

    public function insertNewCallData($call_id, $current_state)
    {
        $check_existing_record = $this::where('call_id', $call_id)->first();
        if(empty($check_existing_record))
            $this::create(['call_id' => $call_id, 'state' => $current_state]);
    }

    public function updateCallData($call_id, $current_state)
    {
        $update_call_state = $this::where('call_id', $call_id)->first();
        if(!empty($update_call_state))
        {
            $update_call_state->state=$current_state;
            $update_call_state->save();
        }
    }

    public function search($call_id)
    {
        $find_call_id = $this::where('call_id', $call_id)->first();
        if(!empty($find_call_id))
            return $find_call_id->state;
    }
}