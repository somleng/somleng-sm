<?php

namespace App\Providers;

use Illuminate\Queue\Events\JobProcessed;
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
        Queue::after(function (JobProcessed $event) {
//            dd($event->data);
            // $event->connectionName
            echo "job id in after queue = ".
                var_dump($event->job);
            // $event->data
//            dd($event);
            return $event;
        });


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
