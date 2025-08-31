<?php

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.karantinaindonesia.go.id/ums/login',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '{
    "username" : "mridwan94",
    "password" : "MRidwan123!"
}',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: text/plain',
        'Cookie: PHPSESSID=8lcd2i4bjqt4tm2cjrpduv4c3f'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
