<?php
session_start();
require '../functions.php';
$response = null;
$error_msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  $purchase = [
    'returnUrl' => str_replace('purchase.php', 'purchase-complete.php', $url),
    'cancelUrl' => str_replace('purchase.php', 'purchase-cancel.php', $url),
    'amount' => $_POST['amount'],
    'currency' => 'USD',
    'description' => $_POST['description'],
    'card' => [
      'firstName' => isset($_POST['firstName']) ? $_POST['firstName'] : null,
      'lastName' => isset($_POST['lastName']) ? $_POST['lastName'] : null,
      'number' => isset($_POST['number']) ? $_POST['number'] : null,
      'expiryMonth' => isset($_POST['expiryMonth']) ? $_POST['expiryMonth'] : null,
      'expiryYear' => isset($_POST['expiryYear']) ? $_POST['expiryYear'] : null,
      'cvv' => isset($_POST['cvv']) ? $_POST['cvv'] : null
    ]
  ];

  if ($_POST['gateway'] === 'Stripe') {
    unset($purchase['card']);
    $purchase['token'] = $_POST['token'];
  }

  $_SESSION['omnipay.gateway'] = $_POST['gateway'];
  $_SESSION['omnipay.purchase'] = $purchase;

  try {
    $gateway = getGateway($_SESSION['omnipay.gateway']);
    $response = $gateway->purchase($_SESSION['omnipay.purchase'])->send();
    if ($response->isRedirect()) $response->redirect();
  }
  catch (Exception $e) {
    $error_msg = $e->getMessage();
  }
}
else {
  echo 'Invalid method';
  return;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Purchase Complete</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  </head>
  <body>
    <div class="container py-5">
      <div class="card mx-auto" style="max-width: 600px">
        <?PHP if ($error_msg): ?>
          <div class="card-header bg-danger text-white">
            Payment Error
          </div>
          <div class="card-body">
            <p class="card-text">
              <?PHP echo $error_msg; ?>
            </p>
          </div>
        <?PHP elseif ($response->isSuccessful()): ?>
          <div class="card-header bg-success text-white">
            Payment Received
          </div>
          <div class="card-body">
            <p class="card-text">
              <?PHP echo $response->getMessage(); ?>
            </p>
            <table class="table">
              <?php foreach ($response->getData() as $key => $value): ?>
                <p>
                  <b><?php echo $key; ?>: </b>
                  <span><?php echo json_encode($value); ?></span>
                </p>
              <?php endforeach; ?>
            </table>
          </div>
        <?PHP else: ?>
          <div class="card-header bg-danger text-white">
            Payment Failed
          </div>
          <div class="card-body">
            <p class="card-text">
              <?PHP echo $response->getMessage(); ?>
            </p>
          </div>
        <?PHP endif; ?>
      </div>
    </div>
  </body>
</html>