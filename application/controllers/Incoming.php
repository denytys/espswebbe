<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

class Incoming extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_model');
        $this->load->helper('jwt');

        $authHeader = $this->input->get_request_header("Authorization");
        if (!$authHeader || strpos($authHeader, "Bearer ") !== 0) {
            $this->response(["status" => false, "message" => "Unauthorized"], RestController::HTTP_UNAUTHORIZED);
            exit;
        }
        $jwt = str_replace("Bearer ", "", $authHeader);
        $decoded = validateJWT($jwt);
        if (!$decoded) {
            $this->response(["status" => false, "message" => "Invalid or expired token"], RestController::HTTP_UNAUTHORIZED);
            exit;
        }
        $this->user = $decoded;
    }

    public function ecertin_get()
    {
        $response = $this->Dashboard_model->getEcertIn();
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function ephytoin_get()
    {
        $response = $this->Dashboard_model->getEphytoIn();
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
