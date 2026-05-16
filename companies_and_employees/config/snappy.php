<?php

return [
    'pdf' => [
        'enabled' => true,
        'binary' => env('WKHTML_PDF_BINARY', '/usr/bin/wkhtmltopdf'),
        'timeout' => 60,
        'options' => [
            'encoding' => 'UTF-8',
            'enable-local-file-access' => true,
            'margin-top' => 12,
            'margin-right' => 12,
            'margin-bottom' => 12,
            'margin-left' => 12,
        ],
        'env' => [],
    ],

    'image' => [
        'enabled' => true,
        'binary' => env('WKHTML_IMG_BINARY', '/usr/bin/wkhtmltoimage'),
        'timeout' => 60,
        'options' => [],
        'env' => [],
    ],
];
