<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

class Assistant extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Assistant_model', 'assistant');
        $this->load->database();
    }

    public function index_get()
    {
        $question = $this->get('q');
        if (!$question) {
            return $this->response([
                'status' => false,
                'message' => 'Pertanyaan tidak boleh kosong.',
            ], RestController::HTTP_BAD_REQUEST);
        }

        $q_lower = strtolower($question);

        if (preg_match('/(siapa.*kamu|kamu.*siapa|siapa.*anda|anda.*siapa)/', $q_lower)) {
            $answer = "Perkenalkan saya assistant yang akan membantu anda mengenai esps. ada yang bisa saya bantu yang mulia?";
            return $this->response([
                'status' => true,
                'message' => 'Jawaban dari lokal.',
                'data' => ['answer' => $answer]
            ], RestController::HTTP_OK);
        }

        if (preg_match('/\b(hai|halo|helo|pagi|siang|sore|malam|terima kasih|makasih|thanks)\b/', $q_lower)) {
            if (preg_match('/(terima kasih|makasih|thanks)/', $q_lower)) {
                $answer = "Sama-sama! Senang bisa membantu 😊";
            } elseif (preg_match('/(pagi)/', $q_lower)) {
                $answer = "Selamat pagi! Semoga harimu senin terus";
            } elseif (preg_match('/(siang)/', $q_lower)) {
                $answer = "Selamat siang! Jangan lupa makan dan minum air ya";
            } elseif (preg_match('/(sore)/', $q_lower)) {
                $answer = "Selamat sore! Semoga aktivitasmu lancar. jaya! jaya! samsu!";
            } elseif (preg_match('/(malam)/', $q_lower)) {
                $answer = "Selamat malam! Saatnya rehat sejenak, jangan ngoyo mulu";
            } else {
                $answer = "Hai! Ada yang bisa saya bantu hari ini?";
            }

            return $this->response([
                'status' => true,
                'message' => 'Jawaban dari lokal.',
                'data' => ['answer' => $answer]
            ], RestController::HTTP_OK);
        }

        if (
            strpos($q_lower, 'ecert') !== false ||
            strpos($q_lower, 'e-phyto') !== false ||
            strpos($q_lower, 'ephyto') !== false
        ) {
            $answer = $this->assistant->process_question($question);
        } else {

            $answer = $this->ask_gemini($question);
        }

        $this->response([
            'status' => true,
            'message' => 'Jawaban ditemukan.',
            'data' => ['answer' => $answer]
        ], RestController::HTTP_OK);
    }

    private function ask_gemini($question)
    {
        $apiKey = 'AIzaSyCTPAmrHT3m2m8UxZaanCQvGOIqUKYQrH4';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;

        $payload = [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [
                        ["text" => "Hari ini adalah " . date('l, d F Y') . ". Pertanyaan pengguna: " . $question]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return "Gagal menghubungi Gemini AI: $error";
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);

        if ($http_code !== 200) {
            return "Gagal mendapatkan jawaban dari suhunya suhu (HTTP $http_code)";
        }

        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }

        return "Maaf, saya tidak mendapatkan jawaban apapun dari Gemini.";
    }
}
