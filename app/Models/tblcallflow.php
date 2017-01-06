<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblcallflow extends Model
{
    protected $table = 'tblcallflow';
    protected $fillable = ['callflow_name'];

    public function insertNewCallflow($callflow_name)
    {
        $check_existing_callflow = $this::where('callflow_name', $callflow_name)->first();
        if(empty($check_existing_callflow))
        {
            $callflow_id = $this::create(['callflow_name' => $callflow_name]);
            return $callflow_id;
        }
        else return $check_existing_callflow->id;
    }


}