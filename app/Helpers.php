<?php
function callRcsSendTextMessage($mobile_no = null, $user_id = null, $message = null, $image = null)
{
    if ($mobile_no == null || $user_id == null || $message == null)
        return false;
    //check if rcs json key exist with current user
    if (!file_exists(public_path('rcs_keys/' . $user_id . ".json")))
        return false;

    $message_id = generateRandomString(15);
    $zone = 'asia';
    $access_token = getAccessToken($user_id);
    $curl = curl_init();
    //if image found 
    if ($image !== null) {
        $data = array(
            "fileUrl" => "http://www.google.com/logos/doodles/2015/googles-new-logo-5078286822539264.3-hp2x.gif",
            "forceRefresh" => false
        );
        $contentInfo = array('contentInfo' => $data);
        $data_string = json_encode(array("contentMessage" => $contentInfo));
    }else{
        $data = array(
        "text" => $message
    );
    $data_string = json_encode(array("contentMessage"=>$data));
    }
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://' . $zone . '-rcsbusinessmessaging.googleapis.com/v1/phones/' . $mobile_no . '/agentMessages?messageId=' . $message_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data_string,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
            'Authorization: Bearer ' . $access_token
        ),
    ));

    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return ['status_code' => $httpcode, 'response' => $response];
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


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
// oauth2l fetch --type oauth --credentials rbm-metro-max-services-ovlozqm-21006987a25b.json  --scope rcsbusinessmessaging
// ya29.c.Kp8BFghKNvmkaN24MjMraUuwob1g7YWUjKsKdduwTkWCtXeCkTnf4blpnd1Do7ijUyGKbGRwX2Deb-vYLE43bfDE7_TV1TZ3vn3lMnfvHg4ATJsMhcxxn9_8Z5lcwSAUlsmrAPGgy5jOebTLhhUFWszm5V-k8Sn3FqQhSCNh4P6BbY6C3o1594FoQ4_3l14urC5uvgAMu3LEaRWgRLhKtp5B
