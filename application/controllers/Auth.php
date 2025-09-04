<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

class Auth extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');

        // // ✅ CORS headers
        // header("Access-Control-Allow-Origin: http://localhost:5173"); // HARUS spesifik, jangan pakai *
        // header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        // header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        // header("Access-Control-Allow-Credentials: true");
        // header("Content-Type: application/json");

        // // ✅ handle preflight
        // if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        //     http_response_code(200);
        //     exit();
        // }
    }

    public function login_post()
    {
        $username = $this->post('username');
        $password = $this->post('password');

        if (!$username || !$password) {
            return $this->response([
                "status"  => false,
                "message" => "Username & password wajib diisi"
            ], RestController::HTTP_BAD_REQUEST);
        }

        // 🔹 Panggil API luar
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => 'https://api.karantinaindonesia.go.id/ums/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([
                "username" => $username,
                "password" => $password
            ]),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return $this->response([
                'status'  => false,
                'message' => 'Gagal terhubung ke API UMS',
            ], RestController::HTTP_INTERNAL_ERROR);
        }

        $apiRes = json_decode($response, true);

        // 🔹 Jika sukses → simpan session
        if (isset($apiRes['status']) && $apiRes['status'] == "200") {
            $userData = $apiRes['data'] ?? [];

            // 🔑 Buat JWT
            $payload = [
                'iss' => "http://localhost/esps-be",  // issuer
                'aud' => "http://localhost:5173",     // audience
                'iat' => time(),                       // issued at
                'exp' => time() + (60 * 60),           // expired 1 jam
                'data' => $userData
            ];
            $jwt = JWT::encode($payload, "rahasia_s3cret", 'HS256'); // ganti pakai secret env

            return $this->response([
                'status'  => true,
                'message' => $apiRes['message'] ?? 'Login berhasil',
                'user'    => $userData,
                'token'   => $jwt,
            ], RestController::HTTP_OK);
        }
    }

    public function logout_post()
    {
        $this->session->sess_destroy();
        return $this->response([
            'status'  => true,
            'message' => 'Logout berhasil'
        ], RestController::HTTP_OK);
    }

    public function me_get()
    {
        $authHeader = $this->input->get_request_header('Authorization');
        if ($authHeader) {
            $token = str_replace('Bearer ', '', $authHeader);
            try {
                $decoded = JWT::decode($token, new Key("rahasia_s3cret", 'HS256'));
                return $this->response([
                    'status' => true,
                    'user'   => $decoded->data
                ], 200);
            } catch (Exception $e) {
                return $this->response([
                    'status' => false,
                    'message' => 'Token invalid: ' . $e->getMessage()
                ], 401);
            }
        }
        return $this->response([
            'status' => false,
            'message' => 'Authorization header missing'
        ], 401);
    }
}
