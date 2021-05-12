<?php
class Payment
{

  private static $instance = null;

  protected $is_sandbox;
  protected $stripe;
  protected $customer_id;
  protected $subscriptions;

  private function __construct()
  {

    $this->is_sandbox = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']); //false;

    if ($this->is_sandbox) {
      // Stripe
      $this->stripeConfigs['publishable_key'] = 'pk_test_bX98JZECbYwuCDuHqqRUTktS00uuBIZnlO';
      $this->stripeConfigs['secret_key'] = 'sk_test_vgPAGgiC8CzE5PCeMfNXhMbB002o1zu6Q7';
      $this->stripeConfigs['plan_id'] = 'plan_GyHm8Pt4LHcID0';
      $this->stripeConfigs['default_tax_rates'] = 'txr_1GiDj6Gdx829KD99UdrsUpVE';
      // $this->stripeConfigs['current_coupon_code'] = '93YgiA4h';

    } else {
      // Stripe
      $this->stripeConfigs['publishable_key'] = 'pk_live_bwH5gFYGDrxdtEMOR4v0Gfhq00pQVjdvar';
      $this->stripeConfigs['secret_key'] = 'sk_live_r1QlJPmaGThmo01bYmFwymIZ00I1xqYfBZ';
      $this->stripeConfigs['plan_id'] = 'plan_GyjXgQrFMNXyVM';
      $this->stripeConfigs['default_tax_rates'] = 'txr_1GQjy9Gdx829KD99cIXBU42W';
      // $this->stripeConfigs['current_coupon_code'] = 'SubFirstIssue';
    }

    // Include Stripe SDK
    require_once(plugin_dir_path(__FILE__) . '../vendor/autoload.php');

    $this->stripe = new \Stripe\StripeClient(
      $this->stripeConfigs['secret_key']
    );
  }

  public static function getInstance()
  {
    if (self::$instance == null) {
      self::$instance = new Payment();
    }
    return self::$instance;
  }

  /*
  public function getCustomer($customer_id) {
    // Include Stripe SDK
    // require_once( plugin_dir_path( __FILE__ ) . '../vendor/autoload.php' );

    // \Stripe\Stripe::setApiKey( $this->stripeConfigs['secret_key'] );

    return $this->stripe->customers->retrieve(
      $customer_id,
    );
  }
  */

  public function getPaymentMethods($customer_id)
  {
    try {
      if (!is_null($customer_id) && '' != $customer_id) {
        $customer = $this->stripe->customers->retrieve(
          $customer_id,
          []
        );
        $paymentMethods = $this->stripe->paymentMethods->all([
          'customer' => $customer->id,
          'type' => 'card',
        ])->data;
        return $paymentMethods;
      }
      return [];
    } catch (Exception $e) {
      return [];
    }
  }

  public function setupIntent($customer_id = NULL)
  {
    
    $current_user = wp_get_current_user();
    // $customer = $this->getCustomer();
    $intent = $this->stripe->setupIntents->create([
      'customer' => $customer_id
    ]);
    ob_start();
?>
    <script src="https://js.stripe.com/v3/"></script>

    <div class="row">
      <div class="col-12 mt-3">
        <div id="card-element" class="card-element"></div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 mt-3">
        <div id="js-errors" class="alert alert-danger d-none" role="alert"></div>
        <div class="spinner d-none" id="spinner">
          <div class="double-bounce1"></div>
          <div class="double-bounce2"></div>
        </div>
        <button id="card-save" class="btn btn-dark" data-secret="<?php echo $intent->client_secret; ?>">Submit</button>
      </div>
    </div>
    <script>
      var cardholderName = document.getElementById('full_name');
      var cardButton = document.getElementById('card-save');
      var clientSecret = cardButton.dataset.secret;

      var stripe = Stripe('<?php echo $this->stripeConfigs['publishable_key']; ?>');
      var elements = stripe.elements();

      var cardElement = elements.create('card');
      cardElement.mount('#card-element');

      var displayError = document.getElementById("js-errors");

      // Handle payment submission when user clicks the pay button.
      cardButton.addEventListener("click", function(event) {
        event.preventDefault();
        changeLoadingState(true);

        if (!cardholderName || !cardholderName.value) {
          changeLoadingState(false);
          displayError.classList.remove("d-none");
          displayError.textContent = 'Please enter your name';
        } else {

          displayError.classList.add("d-none");

          stripe
            .confirmCardSetup(clientSecret, {
              payment_method: {
                card: cardElement,
                billing_details: {
                  name: cardholderName.value,
                }
              }
            })
            .then(function(result) {

              if (result.error) {
                changeLoadingState(false);
                displayError.classList.remove("d-none");
                displayError.textContent = result.error.message;
              } else {
                displayError.classList.add("d-none");
                // The PaymentMethod was successfully set up
                orderComplete(stripe, clientSecret, '<?php echo isset($_GET['id']) ? trim($_GET['id']) : NULL; ?>');
              }
            });
        }
      });

      /* Shows a success / error message when the payment is complete */
      var orderComplete = function(stripe, clientSecret, Id) {
        stripe.retrieveSetupIntent(clientSecret).then(function(result) {
          var setupIntent = result.setupIntent;

          var setupIntentJson = JSON.stringify(setupIntent, null, 2);

          var formData = new FormData(document.getElementById('form-payment-details'));
          formData.append('action', 'update_payment_details');
          formData.append('id', Id);
          formData.append('setupIntentJson', setupIntentJson);

          return fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
              method: 'post',
              body: formData
            })
            .then(response => {
              return response.json();
            })
            .then(result => {
              var displayError = document.getElementById("js-errors");

              if (result.success) {
                displayError.classList.remove("d-none", "alert-danger");
                displayError.classList.add("alert-success");
                displayError.textContent = 'Your payment details have been updated!';

                var spinner = document.querySelector("#spinner");
                spinner.classList.add('d-none');

                setTimeout(function() {
                  window.location.href = "<?php echo home_url('/observer/magazine-subscriptions/'); ?>";
                }, 3000);
              } else {
                changeLoadingState(false);
                displayError.classList.remove("d-none", "alert-success");
                displayError.classList.add("alert-danger");
                displayError.textContent = result.data.error;
              }
            });
        });
      };


      // Show a spinner on payment submission
      var changeLoadingState = function(isLoading) {
        var cardButton = document.querySelector("#card-save");
        var spinner = document.querySelector("#spinner");
        if (isLoading) {
          cardButton.disabled = true;
          spinner.classList.remove('d-none');
          cardButton.classList.add('d-none');
        } else {
          cardButton.disabled = false;
          spinner.classList.add('d-none');
          cardButton.classList.remove('d-none');
        }
      };
    </script>
    <style>
      .card-element {
        height: auto;
        padding: 1rem;
        width: 100%;
        color: #32325d;
        background-color: white;
        border: 1px solid #ced4da;
        border-radius: 4px;
      }
    </style>
<?php
    $html = ob_get_contents();
    ob_end_clean();
    echo $html;
  }

  public function update_payment_details($customer_id)
  {

    $post = stripslashes_deep($_POST);
    $setupIntent = json_decode($post['setupIntentJson']);

    $paymentMethods = $this->getPaymentMethods($customer_id);
    if ($paymentMethods && count($paymentMethods) > 0) {
      foreach ($paymentMethods as $paymentMethod) :
        if ($paymentMethod->id != $setupIntent->payment_method) {
          $this->stripe->paymentMethods->detach(
            $paymentMethod->id,
            []
          );
        }
      endforeach;
    }

    try {
      $cu = $this->stripe->customers->update(
        $customer_id,
        [
          // 'name' => $post['full_name'],
          // 'address' => [
          //   'line1' => $post['sub_address_1'],
          //   'line2' => $post['sub_address_2'],
          //   'city' => $post['sub_city'],
          //   'country' => $post['sub_country'],
          //   'postal_code' => $post['sub_postcode'],
          //   'state' => $post['sub_state'],
          // ],
          'invoice_settings' => ['default_payment_method' => $setupIntent->payment_method]
        ]
      );
    } catch (Exception $e) {
      error_log(' ---- Stripe Error: ' . $e->getMessage() . ' LINE: ' . __LINE__);
      wp_send_json_error(['error' => 'Error setting default payment method.']);
      wp_die();
    }

    wp_send_json_success($setupIntent->payment_method);
    wp_die();
  }

  public function createCustomer($payment_method, $sub_email, $sub_full_name, $buyer = [], $shipping = [])
  {
    // Include Stripe SDK
    require_once(plugin_dir_path(__FILE__) . '../vendor/autoload.php');

    \Stripe\Stripe::setApiKey($this->stripeConfigs['secret_key']);

    try {
      $customer = \Stripe\Customer::create([
        'payment_method' => $payment_method,
        'email' => $sub_email,
        'name' => $buyer['full_name'],
        'address' => [
          'line1' => $buyer['address_1'],
          'line2' => $buyer['address_2'],
          'city' => $buyer['city'],
          'country' => $buyer['country'],
          'postal_code' => $buyer['postcode'],
          'state' => $buyer['state'],
        ],
        'shipping' => [
          'address' => [
            'line1' => $shipping['address_1'],
            'line2' => $shipping['address_2'],
            'city' => $shipping['city'],
            'country' => $shipping['country'],
            'postal_code' => $shipping['postcode'],
            'state' => $shipping['state'],
          ],
          'name' => $sub_full_name,
        ],
        'invoice_settings' => [
          'default_payment_method' => $payment_method
        ]
      ]);

      return $customer;
    } catch (\Stripe\Exception\CardException $e) {
      error_log('--Stripe Error | Customer | CardException : ' . $e->getError()->message);
      return ['error' => 'It looks like your card has been declined. Make sure all your details are correct, your card is valid, and give it another shot.'];
      wp_die();
    } catch (\Stripe\Exception\RateLimitException $e) {
      error_log('--Stripe Error | Customer | RateLimitException: ' . $e->getError()->message);
      return ['error' => 'Whoops, like something unexpected happened on our of things. Feel free to refresh your browser and give it another shot.'];
      wp_die();
    } catch (\Stripe\Exception\InvalidRequestException $e) {
      error_log('--Stripe Error | Customer | InvalidRequestException: ' . $e->getError()->message);
      return ['error' => 'Whoops, like something unexpected happened on our of things. Feel free to refresh your browser and give it another shot.'];
      wp_die();
    } catch (\Stripe\Exception\AuthenticationException $e) {
      error_log('--Stripe Error | Customer | AuthenticationException: ' . $e->getError()->message);
      return ['error' => 'Whoops, like something unexpected happened on our of things. Feel free to refresh your browser and give it another shot.'];
      wp_die();
    } catch (\Stripe\Exception\ApiConnectionException $e) {
      error_log('--Stripe Error | Customer | ApiConnectionException: ' . $e->getError()->message);
      return ['error' => 'Whoops, it seems we had a bit of trouble getting in contact with the payment service. Feel free to refresh your browser and give it another shot!'];
      wp_die();;
    } catch (\Stripe\Exception\ApiErrorException $e) {
      error_log('--Stripe Error | Customer | ApiErrorException: ' . $e->getError()->message);
      return ['error' => 'Whoops, like something unexpected happened on our side of things. Please contact subscribe@thebrag.media with the details you submitted.'];
      wp_die();
    } catch (Exception $e) {
      error_log('--Stripe Error | Customer | Exception: ' . $e->getError()->message);
      return ['error' => 'Whoops, like something unexpected happened on our side of things. Please contact subscribe@thebrag.media with the details you submitted.'];
      wp_die();
    }
  }

  public function createInvoice($subtotal, $shipping_cost, $number_of_issues, $coupon_code, $amount_off, $payment_method, $sub_email, $sub_full_name, $buyer = [], $shipping = [])
  {

    // error_log( $coupon_code . ' | ' . $amount_off ); exit;

    // Include Stripe SDK
    require_once(plugin_dir_path(__FILE__) . '../vendor/autoload.php');

    \Stripe\Stripe::setApiKey($this->stripeConfigs['secret_key']);

    try {
      $customer = $this->createCustomer($payment_method, $sub_email, $sub_full_name, $buyer, $shipping);

      if ($customer['error']) {
        return ['error' => $customer['error']];
        wp_die();
      }

      \Stripe\InvoiceItem::create([
        'customer' => $customer->id,
        'amount' => $subtotal,
        'currency' => 'aud',
        'description' => 'Rolling Stone Australia Subscription (4 issues)',
      ]);

      \Stripe\InvoiceItem::create([
        'customer' => $customer->id,
        'unit_amount' => $shipping_cost,
        'currency' => 'aud',
        'quantity' => $number_of_issues,
        'description' => 'Shipping',
      ]);

      if ('' != $coupon_code && $amount_off < 0) :
        \Stripe\InvoiceItem::create([
          'customer' => $customer->id,
          'amount' => $amount_off,
          'currency' => 'aud',
          'description' => 'Coupon code: ' . $coupon_code,
        ]);
      endif;

      $invoice = \Stripe\Invoice::create([
        'customer' => $customer->id,
        // 'auto_advance' => true,
        'default_tax_rates' => [
          $this->stripe['default_tax_rates']
        ],
      ]);

      $invoice->pay();

      return [
        'customer' => $customer,
        'invoice' => $invoice
      ];
    } catch (\Stripe\Exception\CardException $e) {
      error_log('--Stripe Error | Invoice | CardException : ' . $e->getError()->message);
      return ['error' => 'It looks like your card has been declined. Make sure all your details are correct, your card is valid, and give it another shot.'];
      wp_die();
    } catch (\Stripe\Exception\RateLimitException $e) {
      error_log('--Stripe Error | Invoice | RateLimitException: ' . $e->getError()->message);
      return ['error' => 'Whoops, it looks like something unexpected happened on our of things. Feel free to refresh your browser and give it another shot.'];
      wp_die();
    } catch (\Stripe\Exception\InvalidRequestException $e) {
      error_log('--Stripe Error | Invoice | InvalidRequestException: ' . $e->getError()->message);
      return ['error' => 'Whoops, like something unexpected happened on our of things. Feel free to refresh your browser and give it another shot.'];
      wp_die();
    } catch (\Stripe\Exception\AuthenticationException $e) {
      error_log('--Stripe Error | Invoice | AuthenticationException: ' . $e->getError()->message);
      return ['error' => 'Whoops, like something unexpected happened on our of things. Feel free to refresh your browser and give it another shot.'];
      wp_die();
    } catch (\Stripe\Exception\ApiConnectionException $e) {
      error_log('--Stripe Error | Invoice | ApiConnectionException: ' . $e->getError()->message);
      return ['error' => 'Whoops, it seems we had a bit of trouble getting in contact with the payment service. Feel free to refresh your browser and give it another shot!'];
      wp_die();;
    } catch (\Stripe\Exception\ApiErrorException $e) {
      error_log('--Stripe Error | Invoice | ApiErrorException: ' . $e->getError()->message);
      return ['error' => 'Whoops, like something unexpected happened on our side of things. Please contact subscribe@thebrag.media with the details you submitted.'];
      wp_die();
    } catch (Exception $e) {
      error_log('--Stripe Error | Invoice | Exception: ' . $e->getError()->message);
      return ['error' => 'Whoops, like something unexpected happened on our side of things. Please contact subscribe@thebrag.media with the details you submitted.'];
      wp_die();
    }
  }

  private static function callAPI($method, $url, $data = '')
  {
    // return;
    $curl = curl_init();
    switch ($method) {
      case "POST":
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data)
          curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        break;
      case "PUT":
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        if ($data)
          curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        break;
      default:
        if ($data)
          $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // die($url);
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    // curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // EXECUTE:
    $result = curl_exec($curl);
    // var_dump( $result );
    if (!$result)
      return;
    curl_close($curl);
    return $result;
  }

  /*
  * Get Countries
  */
  public static function getCountries()
  {
    return array(
      "AU" => "Australia",
      "NZ" => "New Zealand",
      "GB" => "United Kingdom",
      "US" => "United States",
      "CA" => "Canada",

      "0" => "──────────",

      "AF" => "Afghanistan",
      "AL" => "Albania",
      "DZ" => "Algeria",
      "AS" => "American Samoa",
      "AD" => "Andorra",
      "AO" => "Angola",
      "AI" => "Anguilla",
      "AQ" => "Antarctica",
      "AG" => "Antigua and Barbuda",
      "AR" => "Argentina",
      "AM" => "Armenia",
      "AW" => "Aruba",

      "AT" => "Austria",
      "AZ" => "Azerbaijan",
      "BS" => "Bahamas",
      "BH" => "Bahrain",
      "BD" => "Bangladesh",
      "BB" => "Barbados",
      "BY" => "Belarus",
      "BE" => "Belgium",
      "BZ" => "Belize",
      "BJ" => "Benin",
      "BM" => "Bermuda",
      "BT" => "Bhutan",
      "BO" => "Bolivia",
      "BA" => "Bosnia and Herzegovina",
      "BW" => "Botswana",
      "BV" => "Bouvet Island",
      "BR" => "Brazil",
      "BQ" => "British Antarctic Territory",
      "IO" => "British Indian Ocean Territory",
      "VG" => "British Virgin Islands",
      "BN" => "Brunei",
      "BG" => "Bulgaria",
      "BF" => "Burkina Faso",
      "BI" => "Burundi",
      "KH" => "Cambodia",
      "CM" => "Cameroon",

      "CT" => "Canton and Enderbury Islands",
      "CV" => "Cape Verde",
      "KY" => "Cayman Islands",
      "CF" => "Central African Republic",
      "TD" => "Chad",
      "CL" => "Chile",
      "CN" => "China",
      "CX" => "Christmas Island",
      "CC" => "Cocos [Keeling] Islands",
      "CO" => "Colombia",
      "KM" => "Comoros",
      "CG" => "Congo - Brazzaville",
      "CD" => "Congo - Kinshasa",
      "CK" => "Cook Islands",
      "CR" => "Costa Rica",
      "HR" => "Croatia",
      "CU" => "Cuba",
      "CY" => "Cyprus",
      "CZ" => "Czech Republic",
      "CI" => "Côte d’Ivoire",
      "DK" => "Denmark",
      "DJ" => "Djibouti",
      "DM" => "Dominica",
      "DO" => "Dominican Republic",
      "NQ" => "Dronning Maud Land",
      "DD" => "East Germany",
      "EC" => "Ecuador",
      "EG" => "Egypt",
      "SV" => "El Salvador",
      "GQ" => "Equatorial Guinea",
      "ER" => "Eritrea",
      "EE" => "Estonia",
      "ET" => "Ethiopia",
      "FK" => "Falkland Islands",
      "FO" => "Faroe Islands",
      "FJ" => "Fiji",
      "FI" => "Finland",
      "FR" => "France",
      "GF" => "French Guiana",
      "PF" => "French Polynesia",
      "TF" => "French Southern Territories",
      "FQ" => "French Southern and Antarctic Territories",
      "GA" => "Gabon",
      "GM" => "Gambia",
      "GE" => "Georgia",
      "DE" => "Germany",
      "GH" => "Ghana",
      "GI" => "Gibraltar",
      "GR" => "Greece",
      "GL" => "Greenland",
      "GD" => "Grenada",
      "GP" => "Guadeloupe",
      "GU" => "Guam",
      "GT" => "Guatemala",
      "GG" => "Guernsey",
      "GN" => "Guinea",
      "GW" => "Guinea-Bissau",
      "GY" => "Guyana",
      "HT" => "Haiti",
      "HM" => "Heard Island and McDonald Islands",
      "HN" => "Honduras",
      "HK" => "Hong Kong SAR China",
      "HU" => "Hungary",
      "IS" => "Iceland",
      "IN" => "India",
      "ID" => "Indonesia",
      "IR" => "Iran",
      "IQ" => "Iraq",
      "IE" => "Ireland",
      "IM" => "Isle of Man",
      "IL" => "Israel",
      "IT" => "Italy",
      "JM" => "Jamaica",
      "JP" => "Japan",
      "JE" => "Jersey",
      "JT" => "Johnston Island",
      "JO" => "Jordan",
      "KZ" => "Kazakhstan",
      "KE" => "Kenya",
      "KI" => "Kiribati",
      "KW" => "Kuwait",
      "KG" => "Kyrgyzstan",
      "LA" => "Laos",
      "LV" => "Latvia",
      "LB" => "Lebanon",
      "LS" => "Lesotho",
      "LR" => "Liberia",
      "LY" => "Libya",
      "LI" => "Liechtenstein",
      "LT" => "Lithuania",
      "LU" => "Luxembourg",
      "MO" => "Macau SAR China",
      "MK" => "Macedonia",
      "MG" => "Madagascar",
      "MW" => "Malawi",
      "MY" => "Malaysia",
      "MV" => "Maldives",
      "ML" => "Mali",
      "MT" => "Malta",
      "MH" => "Marshall Islands",
      "MQ" => "Martinique",
      "MR" => "Mauritania",
      "MU" => "Mauritius",
      "YT" => "Mayotte",
      "FX" => "Metropolitan France",
      "MX" => "Mexico",
      "FM" => "Micronesia",
      "MI" => "Midway Islands",
      "MD" => "Moldova",
      "MC" => "Monaco",
      "MN" => "Mongolia",
      "ME" => "Montenegro",
      "MS" => "Montserrat",
      "MA" => "Morocco",
      "MZ" => "Mozambique",
      "MM" => "Myanmar [Burma]",
      "NA" => "Namibia",
      "NR" => "Nauru",
      "NP" => "Nepal",
      "NL" => "Netherlands",
      "AN" => "Netherlands Antilles",
      "NT" => "Neutral Zone",
      "NC" => "New Caledonia",

      "NI" => "Nicaragua",
      "NE" => "Niger",
      "NG" => "Nigeria",
      "NU" => "Niue",
      "NF" => "Norfolk Island",
      "KP" => "North Korea",
      "VD" => "North Vietnam",
      "MP" => "Northern Mariana Islands",
      "NO" => "Norway",
      "OM" => "Oman",
      "PC" => "Pacific Islands Trust Territory",
      "PK" => "Pakistan",
      "PW" => "Palau",
      "PS" => "Palestinian Territories",
      "PA" => "Panama",
      "PZ" => "Panama Canal Zone",
      "PG" => "Papua New Guinea",
      "PY" => "Paraguay",
      "YD" => "People's Democratic Republic of Yemen",
      "PE" => "Peru",
      "PH" => "Philippines",
      "PN" => "Pitcairn Islands",
      "PL" => "Poland",
      "PT" => "Portugal",
      "PR" => "Puerto Rico",
      "QA" => "Qatar",
      "RO" => "Romania",
      "RU" => "Russia",
      "RW" => "Rwanda",
      "RE" => "Réunion",
      "BL" => "Saint Barthélemy",
      "SH" => "Saint Helena",
      "KN" => "Saint Kitts and Nevis",
      "LC" => "Saint Lucia",
      "MF" => "Saint Martin",
      "PM" => "Saint Pierre and Miquelon",
      "VC" => "Saint Vincent and the Grenadines",
      "WS" => "Samoa",
      "SM" => "San Marino",
      "SA" => "Saudi Arabia",
      "SN" => "Senegal",
      "RS" => "Serbia",
      "CS" => "Serbia and Montenegro",
      "SC" => "Seychelles",
      "SL" => "Sierra Leone",
      "SG" => "Singapore",
      "SK" => "Slovakia",
      "SI" => "Slovenia",
      "SB" => "Solomon Islands",
      "SO" => "Somalia",
      "ZA" => "South Africa",
      "GS" => "South Georgia and the South Sandwich Islands",
      "KR" => "South Korea",
      "ES" => "Spain",
      "LK" => "Sri Lanka",
      "SD" => "Sudan",
      "SR" => "Suriname",
      "SJ" => "Svalbard and Jan Mayen",
      "SZ" => "Swaziland",
      "SE" => "Sweden",
      "CH" => "Switzerland",
      "SY" => "Syria",
      "ST" => "São Tomé and Príncipe",
      "TW" => "Taiwan",
      "TJ" => "Tajikistan",
      "TZ" => "Tanzania",
      "TH" => "Thailand",
      "TL" => "Timor-Leste",
      "TG" => "Togo",
      "TK" => "Tokelau",
      "TO" => "Tonga",
      "TT" => "Trinidad and Tobago",
      "TN" => "Tunisia",
      "TR" => "Turkey",
      "TM" => "Turkmenistan",
      "TC" => "Turks and Caicos Islands",
      "TV" => "Tuvalu",
      "UM" => "U.S. Minor Outlying Islands",
      "PU" => "U.S. Miscellaneous Pacific Islands",
      "VI" => "U.S. Virgin Islands",
      "UG" => "Uganda",
      "UA" => "Ukraine",
      "SU" => "Union of Soviet Socialist Republics",
      "AE" => "United Arab Emirates",


      "ZZ" => "Unknown or Invalid Region",
      "UY" => "Uruguay",
      "UZ" => "Uzbekistan",
      "VU" => "Vanuatu",
      "VA" => "Vatican City",
      "VE" => "Venezuela",
      "VN" => "Vietnam",
      "WK" => "Wake Island",
      "WF" => "Wallis and Futuna",
      "EH" => "Western Sahara",
      "YE" => "Yemen",
      "ZM" => "Zambia",
      "ZW" => "Zimbabwe",
      "AX" => "Åland Islands",
    );
  }
}
