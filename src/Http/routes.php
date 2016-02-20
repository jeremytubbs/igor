<?php

if (config('igor.use_routes') == true) {
    if (null !== config('igor.type_routes')) {
        foreach (config('igor.type_routes') as $type) {
            Route::get($type, 'Jeremytubbs\Igor\Http\Controllers\IgorController@index');
            Route::get($type.'/{slug}', 'Jeremytubbs\Igor\Http\Controllers\IgorController@showPost');
        }
    }
    if (config('igor.use_sitemap') == true) {
        require __DIR__.'/sitemapRoutes.php';
    }
    Route::get('{slug}',  'Jeremytubbs\Igor\Http\Controllers\IgorController@showPage');
}
