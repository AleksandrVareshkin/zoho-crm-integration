<?php
return [
    'client_id' => env('ZOHO_CLIENT_ID', 'default_client_id'),
    'client_secret' => env('ZOHO_CLIENT_SECRET', 'default_client_secret'),
    'redirect_uri' => env('ZOHO_REDIRECT_URI', 'http://localhost'),
    'refresh_token' => env('ZOHO_REFRESH_TOKEN', 'default_refresh_token'),
];
