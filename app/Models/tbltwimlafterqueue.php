<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tbltwimlafterqueue extends Model
{
    protected $table = 'twiml_after_queue';
    protected $fillable = ['call_id', 'twiml_text'];

    public function insertNewTwimlText($call_sid, $twiml_str)
    {
        $check_existing_record = $this::where('call_id', $call_sid)->first();
        if(empty($check_existing_record))
            $this::create(['call_id' => $call_sid, 'twiml_text' => $twiml_str]);
        else
        {
            $check_existing_record->twiml_text = $twiml_str;
            $check_existing_record->save();
        }
    }

    public function getTwilmlText($call_sid)
    {
        $twiml_txt = $this::where('call_id', $call_sid)->first();
        return $twiml_txt;
//        if(!empty($twiml_txt))
//            return $twiml_txt->twiml_text;
    }
}