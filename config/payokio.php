<?php

return [
    'project_id' => env('PAYOKIO_PROJECT_ID', ''),
    'secret_key' => env('PAYOKIO_SECRET_KEY', ''),
    'currency' => null,
    'searchOrder' => null, //  'App\Http\Controllers\PayokIoController@searchOrder',
    'paidOrder' => null, //  'App\Http\Controllers\PayokIoController@paidOrder',
    'errors' => [
        'validateOrderFromHandle' => 'Validate Order Error',
        'searchOrder' => 'Search Order Error',
        'paidOrder' => 'Paid Order Error',
    ],
    'pay_url' => 'https://payok.io/pay',
    'success_url' => null,
    'fail_url' => null,
];
