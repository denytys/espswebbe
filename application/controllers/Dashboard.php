<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

class Dashboard extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Dashboard_model', 'dashboard_model');
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

    // GET /dashboard/stats
    public function stats_get()
    {
        $year = date('Y');
        $stats = $this->dashboard_model->get_yearly_stats($year);

        $response = array_merge(['year' => $year], $stats);
        return $this->response($response, RestController::HTTP_OK);
    }

    // GET /dashboard/tabledata?type=ecertin
    public function tabledata_get()
    {
        $type = $this->input->get('type');
        $year = date('Y');

        $result = $this->dashboard_model->get_table_data($type, $year);
        if (empty($result)) {
            return $this->response(["message" => "Invalid type"], RestController::HTTP_BAD_REQUEST);
        }

        return $this->response($result, RestController::HTTP_OK);
    }

    // GET /dashboard/monthly?type=ecertin&year=2025
    public function monthly_get()
    {
        $type = $this->input->get('type');
        $year = $this->input->get('year');

        if (!$type || !$year) {
            return $this->response(["message" => "type dan year wajib diisi"], RestController::HTTP_BAD_REQUEST);
        }

        $result = $this->dashboard_model->get_monthly_data($type, $year);
        return $this->response($result, RestController::HTTP_OK);
    }
}
