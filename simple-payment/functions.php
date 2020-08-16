<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Omnipay\Omnipay;

function getGateway($name) {
  $config = require_once __DIR__ . '/configuration.php';
  foreach ($config['gateways'] as $code => $value) {
    if ($name === $code) {
      $gateway = Omnipay::create($name);
      if ($value['settings']) $gateway->initialize($value['settings']);
      return $gateway;
    }
  }

  // gateway not supported
  return null;
}