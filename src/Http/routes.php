<?php

if(null !== config('igor.types')) {
    foreach (config('igor.types') as $type) {
        Route::get($type, 'Jeremytubbs\Igor\Http\Controllers\IgorController@index');
        Route::get($type.'/{slug}', 'Jeremytubbs\Igor\Http\Controllers\IgorController@show');
    }
}