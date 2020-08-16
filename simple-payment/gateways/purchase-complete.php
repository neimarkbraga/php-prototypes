<?php
session_start();
require '../functions.php';
$response = null;
$error_msg = null;

try {
  $gateway = getGateway($_SESSION['omnipay.gateway']);
  $response = $gateway->completePurchase($_SESSION['omnipay.purchase'])->send();
  if ($response->isRedirect()) $response->redirect();
}
catch (Exception $e) {
  $error_msg = $e->getMessage();
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
        <?php if ($error_msg): ?>
          <div class="card-header bg-danger text-white">
            Payment Error
          </div>
          <div class="card-body">
            <p class="card-text">
              <?php echo $error_msg; ?>
            </p>
          </div>
        <?php elseif ($response->isSuccessful()): ?>
          <div class="card-header bg-success text-white">
            Payment Received
          </div>
          <div class="card-body">
            <p class="card-text">
              <?php echo $response->getMessage(); ?>
            </p>

            <table class="table">
              <?php foreach ($response->getData() as $key => $value): ?>
                <p>
                  <b><?php echo $key; ?>: </b>
                  <span><?php echo $value; ?></span>
                </p>
              <?php endforeach; ?>
            </table>
          </div>
        <?php else: ?>
          <div class="card-header bg-danger text-white">
            Payment Failed
          </div>
          <div class="card-body">
            <p class="card-text">
              <?php echo $response->getMessage(); ?>
            </p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </body>
</html>