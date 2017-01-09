<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//class tblstate extends Model
class tblstate extends Model
{
    protected $table = 'tblstate';
    protected $fillable = ['state', 'callflow_id', 'state_type'];


    /*public function transition()
    {
        return $this->belongsTo('App\Models\tbltransition','id');
    }*/

    // get result as null
    public function transition()
    {
        return $this->hasMany('App\Models\tbltransition','state_id','id');
    }

    /**
     * Function to Insert new data into tblstate
     * @param $state
     * @param $state_type
     * @return record_id
     */
    public function insertNewState($state, $callflow_id, $state_type=null)
    {
        if(!empty($state))
        {
            $check_existing_record = $this::where('state', $state)
                                    ->where('callflow_id', $callflow_id)
                                    ->first();
            if(empty($check_existing_record))
            {
                $inserted = $this::create(['state' => $state,
                        'callflow_id' => $callflow_id,
                        'state_type' => $state_type]);
                return $inserted->id;
            }
            else return $check_existing_record->id;
        }
    }


//    public function getTransitionID($state, $input=null)
//    {
//        $transition_id="";
//        // to make sure that state and input are exist in DB
//        // to avoid concate transition id which doesn't exist in DB
//        $check_existing_record = $this::where('state', $state)
//                        //->where('input', $input)
//                        ->first();
//        if(!empty($check_existing_record))
//        {
//            if($input != null)
//                $transition_id = $state.'-'.$input;
//            else
//                $transition_id = $state;
//        }
//        return $transition_id;
//
//    }

    /**
     * Function to get states of specific callflow from tblstate
     * @param $CallFlow_ID is call flow id
     * author: Samak
     */

    public function getStatesFromStateTable($CallFlow_ID)
    {
//        $callflowStates1 = tblstate::find(1)->transition()->select('state_id')->get(); => works
        $callflowStates = tblstate::with('transition')->where('callflow_id','=',$CallFlow_ID)->get();
        return $callflowStates;
    }

    /**
     * Function to get states of specific callflow from tblstate
     * @param $CallFlow_ID is call flow id
     * author: Samak
     */

    public function getStateName($stateId)
    {
//        $callflowStates1 = tblstate::find(1)->transition()->select('state_id')->get(); => works
        $stateName = tblstate::select('state')->where('id','=',$stateId)->first();
        return $stateName['state'];
    }

}