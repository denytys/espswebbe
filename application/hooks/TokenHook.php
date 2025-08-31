<?php
defined('BASEPATH') or exit('No direct script access allowed');

// use Firebase\JWT\JWT;
// use Firebase\JWT\Key;

// function token_authentication()
// {
//     $CI = &get_instance();
//     $CI->load->config('jwt');
//     $CI->load->helper('url');

//     $key = $CI->config->item('jwt_key');
//     $headers = $CI->input->get_request_header('Authorization');

//     // Hanya lanjutkan jika ada Authorization header
//     if (!$headers) {
//         header('Content-Type: application/json');
//         http_response_code(401);
//         echo json_encode(['status' => false, 'message' => 'Authorization header not found']);
//         exit;
//     }

//     try {
//         $token = str_replace('Bearer ', '', $headers);
//         $decoded = JWT::decode($token, new Key($key, 'HS256'));

//         // Simpan ke userdata agar bisa digunakan di controller
//         $CI->session->set_userdata('jwt_data', (array) $decoded);
//     } catch (Exception $e) {
//         header('Content-Type: application/json');
//         http_response_code(401);
//         echo json_encode(['status' => false, 'message' => 'Invalid token: ' . $e->getMessage()]);
//         exit;
//     }
// }
