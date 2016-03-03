<?php

return [
    // your custom content types
    'types' => [],

    // custom columns for your content types
    'custom_columns' => [
        // 'projects' => [
        //     'started_at' => 'timestamp',
        //     'completed_at' => 'timestamp'
        // ],
    ],

    // use the package routes
    'use_content_routes' => true,
    // custom route for a custom content type
    'content_type_routes' => [
        // 'content-type' => 'route-name',
    ],
    'use_page_routes' => true,
    'use_tag_routes' => true,
    'use_category_routes' => true,
    // use the sitemap route
    'use_sitemap' => true,

    'assets' => [
        'deepzoom' => false,
        'resize'   => false
    ],

    'excerpt_separator' => '<!--more-->',
];
