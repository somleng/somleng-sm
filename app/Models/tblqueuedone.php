<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblqueuedone extends Model
{
    //
    public function insertNew($queue_result)
    {
        return $this::create([
//            'queue_id' => $queue_id,
            'queue_result' => $queue_result
        ]);
    }

    public function getQueueResult($qId)
    {
        $result = $this::select('queue_result')
                        ->where('queue_id', $qId)
                        ->first();
        return $result;
    }

    public function deleteQueueResult($qId)
    {
        return $this::where('queue_id', $qId)->delete();
    }
}
