<?php

// bKash Merchant Information
$msisdn = "01200000000"; // bKash Merchant Number
$user = "Xyz"; // bKash Merchant Username
$pass = "123"; // bKash Merchant Password
$url = "https://www.bkashcluster.com:9081"; // bKash API URL with Port Number
$trxid = "66666AAAAA"; // bKash Transaction ID : TrxID

// Final URL for getting response from bKash
$bkash_url = "{$url}/dreamwave/merchant/trxcheck/sendmsg?user={$user}&pass={$pass}&msisdn={$msisdn}&trxid={$trxid}";

// Initiate a cURL session
$curl = curl_init();

// Set cURL options
curl_setopt_array($curl, [
    CURLOPT_PORT => 9081,
    CURLOPT_URL => $bkash_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "cache-control: no-cache",
        "content-type: application/json",
    ],
]);

// Send the cURL request and capture the response
$response = curl_exec($curl);

// Check for errors
if ($error = curl_error($curl)) {
    echo "Problem for Sending Response to bKash API! Try Again after few minutes. Error: {$error}";
    exit;
}

// Get the HTTP response code
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

// Close the cURL session
curl_close($curl);

// Decode the JSON response from bKash
$api_response = json_decode($response, true);

// Get the transaction status code
$transaction_status = $api_response['transaction']['trxStatus'];

// Check if there was an error or if the transaction was successful
if ($http_code !== 200 || $transaction_status !== "0000") {
    echo "Problem with the bKash transaction! Status: {$transaction_status}";
    exit;
}

// Assign Transaction Information
$transaction_amount = $api_response['transaction']['amount']; // bKash Payment Amount
$transaction_reference = $api_response['transaction']['reference']; // bKash Reference for Invoice ID
$transaction_time = $api_response['transaction']['trxTimestamp']; // bKash Transaction Time & Date

// Print Transaction Information
echo "{$transaction_status}<br>{$transaction_amount}<br>{$transaction_reference}<br>{$transaction_time}";
