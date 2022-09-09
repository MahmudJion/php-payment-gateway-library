<?php

$url = "https://api-sandbox.portwallet.com/payment/v2/invoice";
$app_key = 'APP_KEY_HERE';
$app_secret_key = 'APP_SECRET_KEY_HERE';

$orderArray = array(
    'amount' => 100,
    'currency' => 'BDT',
    'redirect_url' => 'http://www.yourDomainUrl.com',
    'ipn_url' => 'http://www.yourDomainUrl.com/ipn_url',
);
$productArray = array(
    'name' => 'test product',
    'description' => 'test description',
);
$billingArray = array();
$billingAddressArray = array(
    'street' => '123',
    'city' => 'Dhaka',
    'state' => 'Dhaka',
    'zipcode' =>'1212',
    'country' => "BD"
);
$billingCustomerArray = array(
    'name' => 'mr.abc',
    'email' => 'abc@test.com',
    'phone' => '01900000000',
    'address' => $billingAddressArray
);
$shippingArray = array();
$shippingAddressArray = array(
    'street' => '123',
    'city' => 'Dhaka',
    'state' => 'Dhaka',
    'zipcode' =>'1212',
    'country' => "BD"
);
$shippingCustomerArray = array(
    'name' => 'mr.abc',
    'email' => 'abc@test.com',
    'phone' => '01900000000',
    'address' => $shippingAddressArray
);
$billingArray['customer'] = $billingCustomerArray;
$shippingArray['customer'] = $shippingCustomerArray;

$configArray = array();
$configArray['order'] = $orderArray;
$configArray['order'] = $orderArray;
$configArray['product'] = $productArray;
$configArray['billing'] = $billingArray;
$configArray['shipping'] = $shippingArray;

$headers = array(
    'authorization:Bearer ' . base64_encode($app_key . ":" . md5($app_secret_key. time())),
    'Content-Type: application/json'
);

//open connection
$ch = curl_init($url);

//set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($configArray));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);
echo $server_output;
die();

$gatewayPageUrl = json_decode($server_output)->action->url;

$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$errors = curl_error($ch);

$url_forward = json_decode($server_output, true);

//close connection
curl_close($ch);

    if ($code == 200 && !$errors) {
        if ($url_forward) {
            $gatewayPageUrl;
            echo $gatewayPageUrl;
            die();
            processRequest($gatewayPageUrl);
        } else {
            $msg = "Invalid Credential";
            $obj = new \stdClass;
            $obj->error = 'yes';
            $obj->msg = $this->msg;
            return $obj;
        }
    } else {
        $msg = "Please provide a valid information about transaction with transaction id, amount, success url, fail url, cancel url, store id at least";
        $obj = new \stdClass;
        $obj->error = 'yes';
        $obj->msg = $msg;
        return $obj;
    }

function processRequest($url)
{
    $htmlData = '<html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                    <script type="text/javascript">
                            function closethisasap() {
                                    document.forms["redirectpost"].submit();
                            }
                    </script>
                    </head>
                            <body onLoad="closethisasap();">

                             <div style="text-align:center;margin:20% 20% 20%;border:2px solid blue;"><h2>Please wait we are redirecting you to PortWallet ....</h2></div>

                    <form name="redirectpost" method="post" action="' . $url . '"></form>
                            </body>
                </html>';

    echo $htmlData;
}