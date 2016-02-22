<?php

return [
    // your custom content types
    'types' => ['pages'],

    // custom columns for your content types
    'custom_columns' => [
        //'projects' => ['started_at', 'completed_at'],
    ],

    // use the package routes
    'use_routes' => true,
    // use the sitemap route
    'use_sitemap' => true,

    // custom route for a custom content type
    'type_routes' => [
        // 'ModelName' => 'route-name',
    ],

    'assets' => [
        'deepzoom' => false,
        'resize'   => false
    ],

    'excerpt_separator' => '<!--more-->',
];
