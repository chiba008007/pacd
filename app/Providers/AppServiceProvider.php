<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Validators\CustomValidator;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // add custom validator
        $validator = $this->app['validator'];
        $validator->resolver(function($translator, $data, $rules, $messages, $attributes) {
            return new CustomValidator($translator, $data, $rules, $messages, $attributes);
        });

        Paginator::defaultView('vendor.pagination.default');
    }
}
