<?php if (!defined('BASEPATH')) exit('No direct script allowed');

class M_users extends CI_Model
{

	function getdatauser($q)
	{
		return $this->db->get_where('tuser', $q)->result_array();
	}

	function getdatauserAll()
	{
		$this->db->select('username,password,nama,id_country,organisation,telp,email,level,verified,last_login');
		return $this->db->get('tuser')->result_array();
	}

	function insertUser($insert)
	{
		$this->db->insert('tuser', $insert);
		return $this->db->affected_rows();
	}

	function get_by_username($username)
	{
		return $this->db->get_where('tuser', ['username' => $username])->row_array();
	}

	function updateUser($username, $data)
	{
		return $this->db->where('username', $username)->update('tuser', $data);
	}
}
