<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Adapter\Cardly;

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
            return new Cardly("test_7b7454b6212aa417877f87b0cc32c1af224fc4f5");
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
