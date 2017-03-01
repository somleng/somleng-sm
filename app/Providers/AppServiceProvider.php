<?php

namespace App\Providers;


use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\ServiceProvider;
use Queue;

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
            // $event->job
            // $event->data
//            dd($event);
            return $event;
        });


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
