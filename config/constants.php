<?php

return [
    'app_url'       => env('APP_URL','localhost'),
    'ruta_storage'  => 'gs://adjudica/',
    'ruta_storage_public' => 'https://storage.adjudica.com.pe/adjudica/',
    'ruta_temporal' => '/tmp/',
    'static_seace'  => '/static/seace/',
    'static_menores' => '/static/menores/',
    'static_temp'   => '/static/temporal/',
    'static_cloud'  => '/static/cloud/',
//    'ruta_cloud'    => env('APP_URL','localhost') . '/static/cloud/',
    'ruta_cloud'    => 'https://storage.adjudica.com.pe/adjudica/',
    'internal'      => env('DIR_INTERNAL','./'),
    'demo'          => 123,
    'ia_token'      => env('IA_TOKEN', null),
    'db_log_timeout' => 0, //seconds
    'db_log_cli'     => true,
    'db_log_all'     => false,
];

