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
        $year = date('Y'); // auto tahun kiye bae

        $ecert_in = $this->db->where('YEAR(tgl_cert)', $year)->count_all_results('ecert_in');
        $ephyto_in = $this->db->where('YEAR(tgl_cert)', $year)->count_all_results('ephyto_in');
        $ecert_out = $this->db->where('YEAR(tgl_cert)', $year)->count_all_results('ecert_out');
        $ephyto_out = $this->db->where('YEAR(tgl_cert)', $year)->count_all_results('ephyto_out');

        $response = [
            'year' => $year,
            'ecert_in' => $ecert_in,
            'ephyto_in' => $ephyto_in,
            'ecert_out' => $ecert_out,
            'ephyto_out' => $ephyto_out
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }


    // GET /dashboard/tabledata?type=ecertin
    public function tabledata_get()
    {
        $type = $this->input->get('type');
        $table = '';
        $negField = '';
        $dateField = 'tgl_cert';
        $year = date('Y'); // auto tahun kiye bae

        switch ($type) {
            case 'ecertin':
                $table = 'ecert_in';
                $negField = 'neg_asal';
                break;
            case 'ephytoin':
                $table = 'ephyto_in';
                $negField = 'neg_asal';
                break;
            case 'ecertout':
                $table = 'ecert_out';
                $negField = 'neg_tuju';
                break;
            case 'ephytoout':
                $table = 'ephyto_out';
                $negField = 'neg_tuju';
                break;
            default:
                show_404();
                return;
        }

        $result = $this->db
            ->select("$negField AS negara, COUNT(*) AS jumlah")
            ->from($table)
            ->where("YEAR($dateField)", $year) // filter tahun
            ->group_by($negField)
            ->order_by('jumlah', 'DESC')
            ->get()
            ->result_array();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }



    // dashboard
    public function monthly_get()
    {
        $type = $this->input->get('type');
        $year = $this->input->get('year');

        if (!$type || !$year) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(["message" => "type dan year wajib diisi"]));
        }

        // Ambil data dari model
        $result = $this->dashboard_model->get_monthly_data($type, $year);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }
}
