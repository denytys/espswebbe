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

    public function ecertin_xmlsigned_get($id_cert)
    {
        $row = $this->db->select('xmlsigned')
            ->from('ecert_in')
            ->where('id_cert', $id_cert)
            ->get()
            ->row();

        if ($row) {
            $this->response([
                'id_cert' => $id_cert,
                'xmlsigned' => $row->xmlsigned
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data not found'
            ], 404);
        }
    }
    public function ephytoin_xmlsigned_get($id_hub)
    {
        $row = $this->db->select('xmlsigned')
            ->from('ephyto_in')
            ->where('id_hub', $id_hub)
            ->get()
            ->row();

        if ($row) {
            $this->response([
                'id_hub' => $id_hub,
                'xmlsigned' => $row->xmlsigned
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data not found'
            ], 404);
        }
    }
}
