<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class tbltwimlafterqueue extends Model
{
    protected $table = 'twiml_after_queue';
    protected $fillable = ['queue_id', 'twiml_text'];

    public function insertNewTwimlText($queue_id, $twiml_str)
    {
        $check_existing_record = $this::where('call_id', $queue_id)->first();
        if(empty($check_existing_record))
            $this::create(['queue_id' => $queue_id, 'twiml_text' => $twiml_str]);
        else
        {
            $check_existing_record->twiml_text = $twiml_str;
            $check_existing_record->save();
        }
    }

    public function getTwilmlText($queue_id)
    {

        $twiml_txt = $this::where('queue_id', $queue_id)->first();
        if(!empty($twiml_txt))
            return $twiml_txt->twiml_text;
    }

    public function deleteJob($queue_id)
    {
        $job = $this::where('queue_id', $queue_id)->first();
        if(!empty($job)) $job->delete();

    }
}