<?php
function check_basic_auth()
{
    $CI = &get_instance();
    $authHeader = $CI->input->get_request_header('Authorization', true);

    if (!$authHeader || stripos($authHeader, 'Basic ') !== 0) {
        return ['status' => false, 'message' => 'Authorization header diperlukan'];
    }

    $base64Creds = substr($authHeader, 6);
    $decoded = base64_decode($base64Creds);
    list($username, $password) = explode(':', $decoded, 2);

    if ($username !== "admin" || $password !== "rahasia") {
        return ['status' => false, 'message' => 'Username atau password salah'];
    }

    return ['status' => true, 'username' => $username];
}
