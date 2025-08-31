<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cors
{
    public function enable()
    {
        // asal domain frontend
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
        error_log("🔥 Cors hook jalan: " . $_SERVER['REQUEST_METHOD']);
        // hanya izinkan localhost:5173
        if ($origin === 'http://localhost:5173') {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        }

        // untuk preflight OPTIONS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
