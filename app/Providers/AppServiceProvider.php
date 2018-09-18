<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Using Closure based composers
        view()->composer('includes.navbar', function ($view) {
            $config = array();
            $config_data = DB::table('config')->get();
            
            foreach($config_data as $data)
            {
              $config[$data->ckey] = $data->value;
            }
            $view->config = $config;
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
