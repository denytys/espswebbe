<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

class Users extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_users');
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

    // GET /users
    public function index_get()
    {
        $users = $this->M_users->getdatauserAll();
        if ($users) {
            $response = [
                'status' => true,
                'data' => $users
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'Data user tidak ditemukan'
            ];
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    // GET /users/{username}
    public function show_get($username)
    {
        $user = $this->M_users->get_by_username($username);
        if ($user) {
            $response = [
                'status' => true,
                'data' => $user
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'User tidak ditemukan'
            ];
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    // POST /users
    public function store()
    {
        $insert = [
            'username'     => $this->input->post('username'),
            'password'     => md5($this->input->post('password')),
            'nama'         => $this->input->post('nama'),
            'id_country'   => $this->input->post('id_country'),
            'organisation' => $this->input->post('organisation'),
            'telp'         => $this->input->post('telp'),
            'email'        => $this->input->post('email'),
            'level'        => $this->input->post('level'),
            'verified'     => $this->input->post('verified'),
            'last_login'   => date('Y-m-d H:i:s')
        ];

        $result = $this->M_users->insertUser($insert);

        if ($result > 0) {
            $response = [
                'status' => true,
                'message' => 'User berhasil ditambahkan'
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'Gagal menambahkan user'
            ];
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    // PUT /users/{username}
    public function update_put($username)
    {
        $input = json_decode($this->input->raw_input_stream, true);

        if ($this->M_users->updateUser($username, $input)) {
            $response = ['status' => true, 'message' => 'User berhasil diperbarui'];
        } else {
            $response = ['status' => false, 'message' => 'Gagal memperbarui user'];
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    // DELETE /users/{username}
    public function destroy($username)
    {
        if ($this->db->delete('tuser', ['username' => $username])) {
            $response = ['status' => true, 'message' => 'User berhasil dihapus'];
        } else {
            $response = ['status' => false, 'message' => 'Gagal menghapus user'];
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
