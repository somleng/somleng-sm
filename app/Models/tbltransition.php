<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tbltransition extends Model
{
    protected $table = 'tbltransition';
    protected $fillable = ['state_id', 'input', 'callflow_id', 'twilml', 'path', 'action', 'new_state'];

    public function state()
    {
        return $this->hasMany('App\Models\tblstate');
    }
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
    public function insertNewTransitionData($state, $input=null, $callflow_id, $twilml=null, $path=null, $action=null, $new_state=null, $state_type)
    {
        $state_table = new tblstate;
        $state_id = $state_table->insertNewState($state, $state_type);
        $new_state_id = $state_table->insertNewState($new_state, '');

        $check_existing_record = $this::where('state_id', $state_id)
                                ->where('input', $input)
                                ->where('callflow_id', $callflow_id)
                                ->where('new_state', $new_state_id)
                                ->first();

        if(empty($check_existing_record))
            $this::create(['state_id' => $state_id,
                            'input' => $input,
                            'callflow_id' => $callflow_id,
                            'twilml' => $twilml,
                            'path' => $path,
                            'action' => $action,
                            'new_state' => $new_state_id]);
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
     * Function to get transitions of specific callflow from tblstate
     * @param $CallFlow_ID is call flow id
     * * author: Samak
     */
    public function getTranstionsFromStateTable($CallFlow_ID)
    {
        $callflowTransitions = $this::state()->select('state','input')->where('callflow_id', $CallFlow_ID)->get();
        return $callflowTransitions;
    }
}