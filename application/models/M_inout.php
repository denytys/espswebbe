<?php
class M_inout extends CI_Model
{

    public function getEcertIn()
    {
        return $this->db->where('type', 'ecertin')->order_by('tgl_cert', 'DESC')->get('incoming_certificates')->result();
    }

    public function getEphytoIn()
    {
        return $this->db->where('type', 'ephytoin')->order_by('tgl_cert', 'DESC')->get('incoming_certificates')->result();
    }

    public function getEcertOut()
    {
        return $this->db->where('type', 'eahout')->order_by('tgl_cert', 'DESC')->get('outgoing_certificates')->result();
    }

    public function getEphytoOut()
    {
        return $this->db->where('type', 'ephytoout')->order_by('tgl_cert', 'DESC')->get('outgoing_certificates')->result();
    }
}
