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
        $question = $this->get('q'); // ambil query param ?q=
        if (!$question) {
            return $this->response([
                'status' => false,
                'message' => 'Pertanyaan tidak boleh kosong.',
            ], RestController::HTTP_BAD_REQUEST);
        }

        $answer = $this->assistant->process_question($question);

        $this->response([
            'status' => true,
            'message' => 'Jawaban ditemukan.',
            'data' => ['answer' => $answer]
        ], RestController::HTTP_OK);
    }
}
