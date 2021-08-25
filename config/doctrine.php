<?php

return [
    'default_connection' => 'odm_default',
    'connection' => [
        'odm_default' => [
            'server'           => env('DB_HOST', 'localhost'),
            'port'             => env('DB_HOST', '27017'),
            'connectionString' => null,
            'user'             => null,
            'password'         => null,
            'dbname'           => env('DB_DATABASE', 'live'),
            'options'          => []
        ],
    ],

    'configuration' => [
        'odm_default' => [
            'metadata_cache'     => 'array',
            'driver'             => 'odm_default',
            'generate_proxies'   => true,
            'proxy_dir'          => storage_path('Laravel5DoctrineODM/Proxy'),
            'proxy_namespace'    => 'Laravel5DoctrineODM\Proxy',
            'generate_hydrators' => true,
            'hydrator_dir'       => storage_path('Laravel5DoctrineODM/Hydrator'),
            'hydrator_namespace' => 'Laravel5DoctrineODM\Hydrator',
            'default_db'         => null,
            'filters'            => [],
            'logger'             => null
        ]
    ],

    'driver' => [
        'odm_default' => [
            'drivers' => []
        ]
    ],

    'documentmanager' => [
        'odm_default' => [
            'connection'    => 'odm_default',
            'configuration' => 'odm_default',
            'eventmanager' => 'odm_default'
        ]
    ],

    'eventmanager' => [
        'odm_default' => [
            'subscribers' => []
        ]
    ],

];