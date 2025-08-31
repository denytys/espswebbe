<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

class Countryset extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_country', 'country_setting');
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

        // CORS
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

    /**
     * GET /countryset
     */
    public function index_get()
    {
        $limit  = (int) $this->get('limit') ?: 5;
        $page   = (int) $this->get('page') ?: 1;
        $offset = ($page - 1) * $limit;

        $total = $this->db->count_all('country_setting');

        $query = $this->db
            ->order_by('id', 'DESC')
            ->get('country_setting', $limit, $offset)
            ->result();

        $this->response([
            'status' => true,
            'data' => $query,
            'pagination' => [
                'total' => $total,
                'limit' => $limit,
                'page' => $page,
                'total_pages' => ceil($total / $limit)
            ]
        ], RestController::HTTP_OK);
    }

    /**
     * POST /countryset
     */
    public function index_post()
    {
        $data = [
            'id_neg' => $this->post('id_neg'),
            'doc'    => $this->post('doc'),
            'via'    => $this->post('via')
        ];

        $insert = $this->country_setting->insert($data);

        if ($insert) {
            $this->response(['status' => true, 'message' => 'Perizinan berhasil disimpan'], RestController::HTTP_CREATED);
        } else {
            $this->response(['status' => false, 'message' => 'Gagal menyimpan data'], RestController::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * PUT /countryset/{id}
     */
    public function index_put($id = null)
    {
        if (!$id || !is_numeric($id)) {
            return $this->response(['status' => false, 'message' => 'ID tidak valid'], RestController::HTTP_BAD_REQUEST);
        }

        $data = [
            'id_neg' => $this->put('id_neg'),
            'doc'    => $this->put('doc'),
            'via'    => $this->put('via')
        ];

        $update = $this->country_setting->update($id, $data);

        if ($update) {
            $this->response(['status' => true, 'message' => 'Data berhasil diperbarui'], RestController::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Gagal memperbarui data'], RestController::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * DELETE /countryset/{id}
     */
    public function index_delete($id = null)
    {
        if (!$id || !is_numeric($id)) {
            return $this->response(['status' => false, 'message' => 'ID tidak valid'], RestController::HTTP_BAD_REQUEST);
        }

        $delete = $this->country_setting->delete($id);

        if ($delete) {
            $this->response(['status' => true, 'message' => 'Data berhasil dihapus'], RestController::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Gagal menghapus data'], RestController::HTTP_INTERNAL_ERROR);
        }
    }
}
