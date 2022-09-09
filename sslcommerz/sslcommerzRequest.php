<?php

$url = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";

$fields = array(
    'store_id' => 'testbox',
    'store_passwd' => 'qwerty',
    'total_amount' => 100,
    'currency' => 'BDT',
    'tran_id' => '1234',
    'success_url' => 'http://yoursite.com/success.php',
    'fail_url' => 'http://yoursite.com/fail.php',
    'cancel_url' => 'http://yoursite.com/cancel.php',
    'emi_option' => 0,
    'cus_name' => 'Customer Name',
    'cus_email' => 'cust@yahoo.com',
    'cus_phone' => '01700000000',
    'cus_add1' => 'Dhaka',
    'cus_city' => 'Dhaka',
    'cus_country' => 'Bangladesh',
    'multi_card_name' => 'mastercard',
    'product_name' => 'Test',
    'product_category' => 'Test Category',
    'product_profile' => 'general',
    'shipping_method' => 'NO',
    'ship_name' => 'Customer Name',
    'ship_add1' => 'Dhaka',
    'ship_city' => 'Dhaka',
    'ship_country' => 'Bangladesh',
);

$domain = $_SERVER["SERVER_NAME"]; // or Manually put your domain name
$ip = $_SERVER["SERVER_ADDR"];

$fields_string = '';
//url-ify the data for the POST
foreach ($fields as $key => $value) {
    $fields_string .= $key . '=' . $value . '&';
}
rtrim($fields_string, '&');

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: $ip", "HTTP_X_FORWARDED_FOR:$ip"));
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_REFERER, $domain);
curl_setopt($ch, CURLOPT_INTERFACE, $ip);
curl_setopt($ch, CURLOPT_POST, count($fields));
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);
$gatewayPageUrl = json_decode($server_output)->GatewayPageURL;

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

                             <div style="text-align:center;margin:20% 20% 20%;border:2px solid blue;"><h2>Please wait we are redirecting you to sslcommerz ....</h2></div>

                    <form name="redirectpost" method="post" action="' . $url . '"></form>
                            </body>
                </html>';

    echo $htmlData;
}