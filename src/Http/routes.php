<?php

if (config('igor.use_content_routes') == true) {
    if (null !== config('igor.content_type_routes')) {
        foreach (config('igor.content_type_routes') as $type) {
            Route::get($type, 'Jeremytubbs\Igor\Http\Controllers\IgorPostController@index');
            Route::get($type.'/{slug}', 'Jeremytubbs\Igor\Http\Controllers\IgorPostController@show');
        }
    }
}

if (config('igor.use_category_routes') == true) {
    Route::get('categories', 'Jeremytubbs\Igor\Http\Controllers\IgorCategoryController@index');
    Route::get('categories/{slug}', 'Jeremytubbs\Igor\Http\Controllers\IgorCategoryController@show');
}
if (config('igor.use_tag_routes') == true) {
    Route::get('tags', 'Jeremytubbs\Igor\Http\Controllers\IgorTagController@index');
    Route::get('tags/{slug}', 'Jeremytubbs\Igor\Http\Controllers\IgorTagController@show');
}

if (config('igor.use_sitemap') == true) {
    Route::get('sitemap', 'Jeremytubbs\Igor\Http\Controllers\IgorSitemapController@show');
}

if (config('igor.use_page_routes') == true) {
    Route::get('{slug}', 'Jeremytubbs\Igor\Http\Controllers\IgorPageController@show');
}
