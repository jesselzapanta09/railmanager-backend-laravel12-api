<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CORS Configuration
    | Handled manually in CorsMiddleware to match original Slim behavior exactly.
    |--------------------------------------------------------------------------
    */

    'allowed_origins' => array_map(
        'trim',
        explode(',', env('ALLOWED_ORIGINS', 'http://localhost:3000'))
    ),

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],

    'allowed_headers' => ['Content-Type', 'Authorization'],

    'allow_credentials' => true,

];
