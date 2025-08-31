<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_country extends CI_Model
{
    public function insert($data)
    {
        return $this->db->insert('country_setting', $data);
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update('country_setting', $data);
    }
}
