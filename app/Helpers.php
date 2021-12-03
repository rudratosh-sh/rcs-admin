<?php

use App\RcsBalance;

/**
 * Send Text RCS Messages
 * @return json 
 */
function callRcsSendTextMessage($mobile_no = null, $user_id = null, $content = null, $message_id = null)
{
    if ($mobile_no == null || $user_id == null || $content == null || $message_id == null)
        return false;
    $content = json_encode($content);
    dd(file_exists(public_path('rcs_keys/' . $user_id . ".json")));
    //check if rcs json key exist with current user
    if (!file_exists(public_path('rcs_keys/' . $user_id . ".json")))
        return false;

    $zone = 'asia';
    $access_token = getAccessToken($user_id);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://' . $zone . '-rcsbusinessmessaging.googleapis.com/v1/phones/' . $mobile_no . '/agentMessages?messageId=' . $message_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $content,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content),
            'Authorization: Bearer ' . $access_token
        ),
    ));

    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return ['status_code' => $httpcode, 'response' => $response];
}

/**
 * Send carousel RCS Messages   
 * @return json 
 */
function callRcsSendCarouselMessage($mobile_no = null, $user_id = null, $content = null, $message_id = null)
{
    if ($mobile_no == null || $user_id == null || $content == null || $message_id == null)
        return false;
    $content = json_encode($content);
    //check if rcs json key exist with current user
    if (!file_exists(public_path('rcs_keys/' . $user_id . ".json")))
        return false;

    $zone = 'asia';
    $access_token = getAccessToken($user_id);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://' . $zone . '-rcsbusinessmessaging.googleapis.com/v1/phones/' . $mobile_no . '/agentMessages?messageId=' . $message_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $content,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content),
            'Authorization: Bearer ' . $access_token
        ),
    ));

    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return ['status_code' => $httpcode, 'response' => $response, 'raw_mobile' => $mobile_no, 'raw_response' => $content];
}

/**
 * Send Filter Messages  
 * @return json 
 */
function callRcsValidate($mobileNOS = null, $user_id = null)
{
    if ($mobileNOS == null || $user_id == null)
        return false;
    //check if rcs json key exist with current user
    if (!file_exists(public_path('rcs_keys/' . $user_id . ".json")))
        return false;
    $content = json_encode(array(
        'users' => $mobileNOS
    ));
    $zone = 'asia';
    $access_token = getAccessToken($user_id);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://' . $zone . '-rcsbusinessmessaging.googleapis.com/v1/users:batchGet',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $content,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content),
            'Authorization: Bearer ' . $access_token
        ),
    ));

    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return ['status_code' => $httpcode, 'response' => json_decode($response)];
}

/**
 * Generate Google OAuth Access Token
 * @return string
 */
function getAccessToken($user_id)
{
    $credentialsFilePath = public_path('rcs_keys/' . $user_id . ".json");
    $client = new Google_Client();
    $client->setAuthConfig($credentialsFilePath);

    $client->addScope('https://www.googleapis.com/auth/rcsbusinessmessaging');
    $client->setApplicationName("rcsbusinessmessaging");
    $client->refreshTokenWithAssertion();
    $token = $client->getAccessToken();
    return $token['access_token'];
}

/**
 * Senitize Mobile Number
 * @return array
 */
function senitizeMobileNumbers($numbers = null)
{
    $numbers = str_replace(["'", "-"], "", $numbers);
    $numbers = array_map('intval', explode(',', $numbers));
    $mobile =  array_map(function ($val) {
        $val = preg_replace('/\D/', '', $val);
        if (substr($val, 0, 3) == '091' && strlen($val) == 13) {
            $val = substr($val, 3);
        }
        if (substr($val, 0, 2) == '91' && strlen($val) == 12) {
            $val = substr($val, 2);
        }
        if (substr($val, 0, 1) == '0' && strlen($val) == 11) {
            $val = substr($val, 1);
        }
        return '+91' . $val;
    }, $numbers);
    return array_unique($mobile);
}

function getBalance($userId = null)
{
    if (!$userId)
        return [];

    $balance = RcsBalance::where('user_id', $userId)->get();
    $balance = $balance->sortByDesc('id');
    $totalMessageCredit = $creditSpend = $creditReverted = $creditExpired = $creditRemaining = $lastRecharged = 0;
    $lastRechargedOn = $creditExpiredOn = null;

    if (!empty($balance)) {
        foreach ($balance as $key => $bal) {
            if ($key == 0){
                $lastRechargedOn = date_format(date_create($bal->created_at), "d F Y");
                $lastRecharged = $bal;
            }    
            //valid till date
            $validTillDate = new DateTime($bal->valid_till);
            $today = new DateTime("today");
            if ($validTillDate >= $today) {
                $creditRemaining = $bal->credit_remaining + $creditRemaining;
            } else {
                $creditExpired = $bal->credit_remaining + $creditExpired;
            }
            //find out expiry date of credit
            if ($creditExpiredOn == null) {
                $creditExpiredOn = $validTillDate;
            } elseif ($creditExpiredOn <= $validTillDate) {
                $creditExpiredOn = $validTillDate;
            }

            $totalMessageCredit = $bal->recharge + $totalMessageCredit;
            $creditSpend = $bal->credit_spend + $creditSpend;
            $creditReverted = $bal->credit_reverted + $creditReverted;
        }
        return  array(
            'totalMessageCredit' => $totalMessageCredit,
            'creditSpend' => $creditSpend,
            'creditReverted' => $creditReverted,
            'creditExpired' => $creditExpired,
            'creditRemaining' => $creditRemaining,
            'lastRechargedOn' => $lastRechargedOn,
            'creditExpiredOn' => $creditExpiredOn!=null ? date_format($creditExpiredOn, "d F Y"):'Not Available',
            'lastRecharged' => $lastRecharged
        );
    }
}



// oauth2l fetch --type oauth --credentials rbm-metro-max-services-ovlozqm-21006987a25b.json  --scope rcsbusinessmessaging
// ya29.c.Kp8BFghKNvmkaN24MjMraUuwob1g7YWUjKsKdduwTkWCtXeCkTnf4blpnd1Do7ijUyGKbGRwX2Deb-vYLE43bfDE7_TV1TZ3vn3lMnfvHg4ATJsMhcxxn9_8Z5lcwSAUlsmrAPGgy5jOebTLhhUFWszm5V-k8Sn3FqQhSCNh4P6BbY6C3o1594FoQ4_3l14urC5uvgAMu3LEaRWgRLhKtp5B
