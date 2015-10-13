<?php

return [

    'default_type' => [
        'posts' => 'Post',
    ],

    'custom_types' => [
        'projects' =>'Project',
    ],

    'custom_fields' => [
        'projects' => [
            'started_at',
            'completed_at'
        ],
    ],

    // choose between gd and imagick
    'image_driver' => 'imagick',

    'image_2x' => true,

    // choose between jpg, png
    'image_format' => 'png',

    'image_sizes' => [
        // 'size' => [height, width],
        'thumb' => [100, 200],
        'large' => [300, 400],
    ],
];
