<?php
//Generate Merchant Unique Transaction ID
function rand_string($length)
{
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    $str = '';
    $size = strlen($chars);

    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[rand(0, $size - 1)];
    }

    return $str;
}

$cur_random_value = rand_string(10);

$url = "http://sandbox.aamarpay.com/request.php";
$fields = array(
    'store_id' => 'aamarpay_id',
    'amount' => '10',
    'payment_type' => 'VISA',
    'currency' => 'BDT',
    'tran_id' => $cur_random_value,
    'cus_name' => 'Mr. ABC',
    'cus_email' => 'abc@abc.com',
    'cus_add1' => 'House 1 Road 2',
    'cus_add2' => 'Uttara',
    'cus_city' => 'Dhaka',
    'cus_state' => 'Dhaka',
    'cus_postcode' => '1000',
    'cus_country' => 'Bangladesh',
    'cus_phone' => '011111111',
    'cus_fax' => 'Not-Applicable',
    'ship_name' => 'Mr. XYZ',
    'ship_add1' => 'House 1 Road 2',
    'ship_add2' => 'Uttara',
    'ship_city' => 'Dhaka',
    'ship_state' => 'Dhaka',
    'ship_postcode' => '1000',
    'ship_country' => 'Bangladesh',
    'desc' => 'T-Shirt',
    'success_url' => 'http://www.abc.com/payment_success_page.php',
    'fail_url' => 'http://www.abc.com/payment_fail_page.php',
    'cancel_url' => 'http://www.abc.com/payment_fail_page.php',
    'opt_a' => 'Optional Value A',
    'opt_b' => 'Optional Value B',
    'opt_c' => 'Optional Value C',
    'opt_d' => 'Optional Value D',
    'signature_key' => 'aamarpay_signature_key'


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
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$errors = curl_error($ch);

$url_forward = json_decode($server_output, true);

//close connection
curl_close($ch);

if ($code == 200 && !$errors) {
    if ($url_forward) {
        $redirect_url_final = "http://sandbox.aamarpay.com" . $url_forward;
        echo $redirect_url_final;
        die();
        processRequest($redirect_url_final);
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

                             <div style="text-align:center;margin:20% 20% 20%;border:2px solid blue;"><h2>Please wait we are redirecting you to Aamarpay ....</h2></div>

                    <form name="redirectpost" method="post" action="' . $url . '"></form>
                            </body>
                </html>';

    echo $htmlData;
}

