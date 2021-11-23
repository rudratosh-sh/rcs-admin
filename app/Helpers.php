<?php
/**
 * Send Text RCS Messages
 * @return json 
 */
function callRcsSendTextMessage($mobile_no = null, $user_id = null,$content=null,$message_id=null)
{
    
    if ($mobile_no == null || $user_id == null || $content == null || $message_id==null)
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
    return ['status_code' => $httpcode, 'response' => $response,'raw_mobile'=>$mobile_no,'raw_response'=>$content];
}

/**
 * Send carousel RCS Messages   
 * @return json 
 */
function callRcsSendCarouselMessage($mobile_no = null, $user_id = null,$content=null,$message_id=null)
{
    if ($mobile_no == null || $user_id == null || $content == null || $message_id==null)
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
    return ['status_code' => $httpcode, 'response' => $response,'raw_mobile'=>$mobile_no,'raw_response'=>$content];
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
function senitizeMobileNumbers($numbers = null){
    $numbers = str_replace(["'","-"], "", $numbers);
    $numbers = array_map('intval', explode(',', $numbers));
    $mobile =  array_map(function ($val) {
        $val = preg_replace('/\D/', '', $val); 
        if(substr($val, 0, 3)=='091' && strlen($val)==13){
         $val = substr($val,3);
        }
        if(substr($val, 0, 2)=='91' && strlen($val)==12){
        $val = substr($val,2);  
        }
        if(substr($val, 0, 1)=='0' && strlen($val)==11){
        $val = substr($val,1);  
        }
        return '+91'.$val;
    }, $numbers);
    return array_unique($mobile);
}
// oauth2l fetch --type oauth --credentials rbm-metro-max-services-ovlozqm-21006987a25b.json  --scope rcsbusinessmessaging
// ya29.c.Kp8BFghKNvmkaN24MjMraUuwob1g7YWUjKsKdduwTkWCtXeCkTnf4blpnd1Do7ijUyGKbGRwX2Deb-vYLE43bfDE7_TV1TZ3vn3lMnfvHg4ATJsMhcxxn9_8Z5lcwSAUlsmrAPGgy5jOebTLhhUFWszm5V-k8Sn3FqQhSCNh4P6BbY6C3o1594FoQ4_3l14urC5uvgAMu3LEaRWgRLhKtp5B
