<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\adapter\Cardly;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Cardly::class,function(){
            return new Cardly("test_7b7454b6212anCVi3jzRc7p6sZ1119QUdQQdfwby3A5");
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
