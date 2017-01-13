<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tbltransition extends Model
{
    protected $table = 'tbltransition';
    protected $fillable = ['state_id', 'input', 'callflow_id', 'twilml', 'path', 'action', 'new_state'];

    public function state()
    {
         return $this->belongsTo('App\Models\tblstate','state_id','id');
    }

    // get result as null
    /*public function state()
    {
        return $this->belongsTo('App\Models\tblstate');
        //dd($this->hasMany('App\Models\tblstate','id','state_id'));
        //return $this->hasMany('App\Models\tblstate','id','state_id');
    }*/
    /**
     * Function to Insert new data into tblstate
     * @author: phyrum
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
        $state_id = $state_table->insertNewState($state, $callflow_id, $state_type);
        $new_state_id = $state_table->insertNewState($new_state, $callflow_id ,'');

        $check_existing_record = $this::where('state_id', $state_id)
                                ->where('input', $input)
                                ->where('new_state', $new_state_id)
                                ->first();
        if(empty($check_existing_record))
            $this::create(['state_id' => $state_id,
                            'input' => $input,
                            'twilml' => $twilml,
                            'path' => $path,
                            'action' => $action,
                            'new_state' => $new_state_id]);
    }

    /**
     * Function to get transitions of specific callflow from tblstate
     * @param $CallFlow_ID is call flow id
     * * author: Samak
     */
    public function getTranstionsFromStateTable($CallFlow_ID)
    {
//        $callflowTransitions = $this::select('state_id','input')->$this->state()->where('tblcallflow.id', $CallFlow_ID)->get();
//        $callflowTransitions = $this::find(1)->states;
//        $callflowTransitions = $this::select('state_id','input')->with('state')->where('callflow_id', $CallFlow_ID)->get();
        $callflowTransitions = $this::state()->select('id')->get();
        //dd($callflowTransitions);

        return $callflowTransitions;
    }

    /**
     * get transition id
     *  @author: phyrum
     * @param $state
     * @param null $input
     */
    public function getTransitionID($state_id, $input=Null)
    {

        $transition_id="";
        /**
         * Check whether state and input are exist in tblstate and tbltransition
         * to avoid concate transition id which doesn't exist in DB
         */
        $existing_tran_and_state = $this::with('state')->where('state_id',$state_id)
                                    ->where('input', $input)
                                    ->first();
        dd($existing_tran_and_state);
        if(!empty($existing_tran_and_state))
        {
            $state_name = $existing_tran_and_state->state->state;
            if($input != null)
                $transition_id = $state_name . '-' . $input;
            else
                $transition_id = $state_name;
        }
        return $transition_id;
    }

}