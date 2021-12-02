<?php
$is_sandbox = isset($_ENV) && isset($_ENV['ENVIRONMENT']) && 'sandbox' == $_ENV['ENVIRONMENT'];

if ($is_sandbox) {
    $braze = [
        'sdk_api_key' => '08d1f29b-c48e-4bb3-aef3-e133789a0c89',
        'sdk_endpoint' => 'sdk.iad-05.braze.com',
        'api_key' => '4bdcad2b-f354-48b5-a305-7a9d77eb356e',
        'api_url' => 'https://rest.iad-05.braze.com',
    ];
} else {
    $braze = [
        'sdk_api_key' => '5fd1c924-ded7-46e7-b75d-1dc4831ecd92',
        'sdk_endpoint' => 'sdk.iad-05.braze.com',
        'api_key' => '3570732f-b2bd-4687-9b19-e2cb32f226ae',
        'api_url' => 'https://rest.iad-05.braze.com',
    ];
}

return [
    'braze' => $braze
];
