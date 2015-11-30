<?php

if(config('igor.use_routes') == true && null !== config('igor.type_routes')) {
    foreach (config('igor.type_routes') as $type) {
        Route::get($type, 'Jeremytubbs\Igor\Http\Controllers\IgorController@index');
        Route::get($type.'/{slug}', 'Jeremytubbs\Igor\Http\Controllers\IgorController@show');
    }
}