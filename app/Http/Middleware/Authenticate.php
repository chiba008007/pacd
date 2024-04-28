<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;
class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        $current = Route::current();
        $param = "";
        //例会
        if(preg_match("/^reikai/",$current->uri)) $param="key=2";
        //高分子分析討論会
        if(preg_match("/^touronkai/",$current->uri)) $param="key=3";
        //技術講習会
        if(preg_match("/^kosyukai/",$current->uri)) $param="key=4";

        if (! $request->expectsJson()) {
            return route('login',$param);
        }
    }
}
