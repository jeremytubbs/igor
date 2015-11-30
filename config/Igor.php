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

    // custom route for a custom post type
    'type_routes' => [
        // 'ModelName' => 'route-name',
    ],

    // choose between gd and imagick
    'image_driver' => 'imagick',

    'image_2x' => true,

    // choose between jpg, png
    'image_format' => 'png',
];
