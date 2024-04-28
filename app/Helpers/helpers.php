<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('isCurrent')) {
    /**
     * 現在アクセスしているページか
     *
     * @param string $val ... routeに設定した名前またはuri(例:/admin/login)
     * @return boolean
     */
    function isCurrent($val) {
        if (strpos($val, '/') !== false) {
            // uriでの判定
            return (str_ends_with($val, '*')) ? str_starts_with($_SERVER['REQUEST_URI'], substr($val, 0, strrpos($val, '/*'))) : $_SERVER['REQUEST_URI'] == $val;
        } else {
            // route nameでの判定
            if(Route::currentRouteName()){
                return (str_ends_with($val, '*')) ? str_starts_with(Route::currentRouteName(), substr($val, 0, strrpos($val, '.*'))) : Route::currentRouteName() == $val;
            }
        }
    }
}
