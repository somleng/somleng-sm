<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblstate extends Model
{
    protected $table = 'tblstate';
    protected $fillable = ['state', 'input', 'callflow_id', 'twilml', 'path', 'action', 'new_state', 'state_type'];
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
        $check_existing_record = $this::where('state', $state)
                                ->where('input', $input)
                                ->where('callflow_id', $callflow_id)
                                ->where('new_state', $new_state)
                                ->first();
        if(empty($check_existing_record))
            tblstate::create(['state' => $state,
                            'input' => $input,
                            'callflow_id' => $callflow_id,
                            'twilml' => $twilml,
                            'path' => $path,
                            'action' => $action,
                            'new_state' => $new_state,
                            'state_type' => $state_type]);
    }
}