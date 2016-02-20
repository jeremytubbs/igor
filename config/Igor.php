<?php

return [
    // your custom post types
    'types' => [],

    // custom fields for your post types
    'custom_fields' => [
        //'projects' => ['started_at', 'completed_at'],
    ],

    // use the package routes
    'use_routes' => true,
    // use the sitemap route
    'use_sitemap' => true,

    // custom route for a custom post type
    'type_routes' => [
        // 'ModelName' => 'route-name',
    ],

    'assets' => [
        'deepzoom' => false,
        'resize'   => false
    ],

    'excerpt_separator' => '<!--more-->',
];
