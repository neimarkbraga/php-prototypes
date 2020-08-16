<?php

return [
  'gateways' => [
    'PayPal_Express' => [
      'displayName' => 'PayPal',
      'settings' => [
        'username' => '', // you need to create sandbox account on paypal
        'password' => '', // you need to create sandbox account on paypal
        'signature' => '', // you need to create sandbox account on paypal
        'testMode' => true
      ]
    ],
    'Dummy' => [
      'displayName' => 'Dummy',
      'settings' => null
    ],
    'Stripe' => [
      'displayName' => 'Stripe',
      'publishableKey' => '', // you need test publishable key from stripe
      'settings' => [
        'apiKey' => '' // you need test secret api key from stripe
      ]
    ]
  ]
];