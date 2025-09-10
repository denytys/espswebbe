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

    public function getEahOut()
    {
        return $this->db->where('type', 'eahout')->order_by('tgl_cert', 'DESC')->get('outgoing_certificates')->result();
    }

    public function getEphytoOut()
    {
        return $this->db->where('type', 'ephytoout')->order_by('tgl_cert', 'DESC')->get('outgoing_certificates')->result();
    }

    // ecert_in
    public function getXmlEcertIn($id_cert)
    {
        return $this->db->select('xmlsigned')
            ->from('ecert_in')
            ->where('id_cert', $id_cert)
            ->get()
            ->row();
    }

    // ephyto_in
    public function getXmlEphytoIn($id_hub)
    {
        return $this->db->select('xmlsigned')
            ->from('ephyto_in')
            ->where('id_hub', $id_hub)
            ->get()
            ->row();
    }

    // eah_out
    public function getXmlEahOut($id_cert)
    {
        return $this->db->select('xml')
            ->from('eah_out')
            ->where('id_cert', $id_cert)
            ->get()
            ->row();
    }

    // ephyto_out
    public function getXmlEphytoOut($id_cert)
    {
        return $this->db->select('xml')
            ->from('ephyto_out')
            ->where('id_cert', $id_cert)
            ->get()
            ->row();
    }
}
