<?php

namespace App\Providers;

use App\Http\Controllers\StateMachineCnt;
use App\Models\tbltwimlafterqueue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\ServiceProvider;
//use Queue;
use Illuminate\Support\Facades\Queue;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
//        Queue::after(function (JobProcessed $event) {
//////            dd($event->data);
////            // $event->connectionName
//////            echo "job id in after queue = ".
//////                var_dump($event->job->getJobId());
////            // $event->data
//////            $tbl_twiml_after_queue = new tbltwimlafterqueue;
//////            $event1 = $tbl_twiml_after_queue->getTwilmlText($event->job->getJobId());
//////            //echo "event= " . $event;
////////            Redirect::to('sm_callflow', ['event' => $event]);
//////            return redirect()->route('sm_callflow', ['event' => $event1]);
//////            dd($event);
//////            return $event;
//            $stateMachineConObj = new StateMachineCnt;
//            print "return in Qafter: ".$stateMachineConObj->afterQueue($event->job->getJobId());
//           return $stateMachineConObj->afterQueue($event->job->getJobId());
//
//
//        });


         /*Queue::after(function (JobProcessed $event) {
//            $content = Storage::disk('public')->get('twiml_result.xml');
             $tbl_twiml_after_queue = new tbltwimlafterqueue;
             $content = $tbl_twiml_after_queue->getTwilmlText($request->CallSid);
            var_dump($content);
        });*/
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
