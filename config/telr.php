<?php
return [
    // The current mode is live|production or test
    'test_mode' => env('TELR_TEST_MODE', true),

    // The currency of store

    'currency' => '₹',

    // The sale endpoint that receive the params
    // @see https://telr.com/support/knowledge-base/hosted-payment-page-integration-guide
    'sale' => [
        'endpoint' => 'https://secure.telr.com/gateway/order.json',
    ],

    // The hosted payment page use the following params as it explained in the integration guide
    // @see https://telr.com/support/knowledge-base/hosted-payment-page-integration-guide/#request-method-and-format
    'create' => [
        'ivp_method' => "create",
        'ivp_store' => 30487,
        'ivp_authkey' => 'sgVBc~3cDwb#fTwv',
        'return_auth' => '/handle-payment/success',
        'return_can' => '/handle-payment/cancel',
        'return_decl' => '/handle-payment/cancel',
        'phone' => '9876543210'
    ]
];
