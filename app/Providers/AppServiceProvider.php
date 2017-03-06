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
