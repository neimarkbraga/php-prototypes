<?php
$config = require_once __DIR__ . '/configuration.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Test Checkout Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <script src="https://js.stripe.com/v3/"></script>
  </head>
  <body>
    <div class="container py-5">
      <div class="card mx-auto" style="max-width: 600px">
        <div class="card-body">
          <h3>Test Checkout Page</h3>
          <form id="CheckoutForm"
                action="gateways/purchase.php"
                method="post">

            <!-- amount -->
            <div class="form-group">
              <label>Amount</label>
              <div class="d-flex align-items-center">
                <input
                  name="amount"
                  type="number"
                  value="10.00"
                  step="any"
                  class="form-control mr-3"
                  required="required"
                />
                <div>USD</div>
              </div>
            </div>

            <!-- description -->
            <div class="form-group">
              <label>Description</label>
              <textarea
                name="description"
                class="form-control"
                required="required"
                rows="3">Test Item</textarea>
            </div>

            <!-- gateway -->
            <div class="border-top pt-3 mt-4">
              <div class="form-group">
                <label>Payment Option</label>
                <select
                  id="GatewaySelect"
                  class="form-control"
                  name="gateway">
                  <?php foreach ($config['gateways'] as $code => $gateway): ?>
                    <option value="<?php echo $code; ?>"><?php echo $gateway['displayName']; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- card details -->
              <fieldset id="CardDetails" disabled>
                <div class="row">
                  <div class="col-12 col-lg-6">
                    <div class="form-group">
                      <label>First Name</label>
                      <input
                        type="text"
                        name="firstName"
                        class="form-control"
                        required="required"
                      />
                    </div>
                  </div>

                  <div class="col-12 col-lg-6">
                    <div class="form-group">
                      <label>Last Name</label>
                      <input
                        type="text"
                        name="lastName"
                        class="form-control"
                        required="required"
                      />
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-group">
                      <label>Card Number</label>
                      <input
                        type="text"
                        name="number"
                        class="form-control"
                        required="required"
                      />
                    </div>
                  </div>

                  <div class="col-4">
                    <div class="form-group">
                      <label>Expiry Month</label>
                      <input
                        type="number"
                        name="expiryMonth"
                        class="form-control"
                        required="required"
                      />
                    </div>
                  </div>

                  <div class="col-4">
                    <div class="form-group">
                      <label>Expiry Year</label>
                      <input
                        type="number"
                        name="expiryYear"
                        class="form-control"
                        required="required"
                      />
                    </div>
                  </div>

                  <div class="col-4">
                    <div class="form-group">
                      <label>CVV</label>
                      <input
                        type="text"
                        name="cvv"
                        class="form-control"
                        required="required"
                      />
                    </div>
                  </div>
                </div>
              </fieldset>

              <!-- stripe form -->
              <div id="StripeForm" class="p-4 mb-3 border rounded">
              </div>
            </div>

            <button type="submit"
                    class="btn btn-lg btn-primary w-100">
              Pay now
            </button>
          </form>
        </div>
      </div>
    </div>

    <script>
      (function main() {
        var stripe = Stripe('<?php echo $config['gateways']['Stripe']['publishableKey']; ?>');
        var stripeCard = null;

        function refreshPaymentMethodScreen() {
          var gatewaySelect = document.querySelector('#GatewaySelect');
          var cardDetails = document.querySelector('#CardDetails');
          var stripeForm = document.querySelector('#StripeForm');

          cardDetails.style.display = 'none';
          cardDetails.disabled = true;

          stripeForm.style.display = 'none';
          stripeForm.innerHTML = '';

          switch (gatewaySelect.value) {
            case 'Dummy':
              cardDetails.style.display = '';
              cardDetails.disabled = false;
              break;

            case 'Stripe':
              stripeForm.style.display = '';
              stripeCard = stripe.elements().create('card');
              stripeCard.mount('#StripeForm');
              break;
          }
        }

        function handleCheckoutFormSubmit(e) {
          var gatewaySelect = document.querySelector('#GatewaySelect');
          var checkoutForm = document.querySelector('#CheckoutForm');

          if (gatewaySelect.value === 'Stripe') {
            e.preventDefault();
            stripe.createToken(stripeCard).then(function(result) {
              if (result.error) {
                alert('Error: ' + result.error.message);
              } else {
                var input = document.createElement('input');
                input.name = 'token';
                input.type = 'hidden';
                input.value = result.token.id;
                checkoutForm.append(input);
                checkoutForm.submit();
              }
            }).catch(function (e) {
              alert('Error: ' + e.message);
            });
          }
        }

        document.querySelector('#CheckoutForm').addEventListener('submit', handleCheckoutFormSubmit);
        document.querySelector('#GatewaySelect').addEventListener('change', refreshPaymentMethodScreen);

        refreshPaymentMethodScreen();
      })();
    </script>
  </body>
</html>