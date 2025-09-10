<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
    // Hitung total data per tahun
    public function get_yearly_stats($year)
    {
        return [
            'ecert_in'   => $this->db->where('YEAR(tgl_cert)', $year)->count_all_results('ecert_in'),
            'ephyto_in'  => $this->db->where('YEAR(tgl_cert)', $year)->count_all_results('ephyto_in'),
            'eah_out'    => $this->db->where('YEAR(tgl_cert)', $year)->count_all_results('eah_out'),
            'ephyto_out' => $this->db->where('YEAR(tgl_cert)', $year)->count_all_results('ephyto_out'),
        ];
    }

    // Grafik bulanan
    public function get_monthly_data($type, $year)
    {
        switch ($type) {
            case 'ecertin':
                $table = 'ecert_in';
                $dateField = 'tgl_cert';
                break;
            case 'ephytoin':
                $table = 'ephyto_in';
                $dateField = 'tgl_cert';
                break;
            case 'eahout':
                $table = 'eah_out';
                $dateField = 'tgl_cert';
                break;
            case 'ephytoout':
                $table = 'ephyto_out';
                $dateField = 'tgl_cert';
                break;
            default:
                return [];
        }

        return $this->db
            ->select("MONTH($dateField) AS bulan, COUNT(*) AS total")
            ->from($table)
            ->where("YEAR($dateField)", $year)
            ->group_by("MONTH($dateField)")
            ->order_by("MONTH($dateField)", "ASC")
            ->get()
            ->result_array();
    }

    // Data negara per jenis
    public function get_table_data($type, $year)
    {
        switch ($type) {
            case 'ecertin':
                $table = 'ecert_in';
                $negField = 'neg_asal';
                break;
            case 'ephytoin':
                $table = 'ephyto_in';
                $negField = 'neg_asal';
                break;
            case 'eahout':
                $table = 'eah_out';
                $negField = 'neg_tuju';
                break;
            case 'ephytoout':
                $table = 'ephyto_out';
                $negField = 'neg_tuju';
                break;
            default:
                return [];
        }

        return $this->db
            ->select("$negField AS negara, COUNT(*) AS jumlah")
            ->from($table)
            ->where("YEAR(tgl_cert)", $year)
            ->group_by($negField)
            ->order_by('jumlah', 'DESC')
            ->get()
            ->result_array();
    }

    // Detail data untuk tiap jenis
    public function getEcertIn()
    {
        $query = $this->db
            ->select('tgl_cert, no_cert, doc_type, komo_eng, port_asal, neg_asal, port_tuju, tujuan, id_cert')
            ->from('ecert_in')
            ->order_by('tgl_cert', 'DESC')
            ->get();

        return [
            'total_data' => $this->db->count_all('ecert_in'),
            'data'       => $query->result_array()
        ];
    }

    public function getEphytoIn()
    {
        $query = $this->db
            ->select('tgl_cert, no_cert, doc_type, komo_eng, port_asal, neg_asal, port_tuju, kota_tuju, data_from, id_hub, moda, nama_angkut, no_angkut')
            ->from('ephyto_in')
            ->order_by('tgl_cert', 'DESC')
            ->get();

        return [
            'total_data' => $this->db->count_all('ephyto_in'),
            'data'       => $query->result_array()
        ];
    }

    public function getEahOut()
    {
        $query = $this->db
            ->select('tgl_cert, no_cert, doc_type, komoditi, neg_tuju, upt, send_to, id_cert, no_reg, pn_pelepasan_id, no_seri')
            ->from('eah_out')
            ->order_by('tgl_cert', 'DESC')
            ->get();

        return [
            'total_data' => $this->db->count_all('eah_out'),
            'data'       => $query->result_array()
        ];
    }

    public function getEphytoOut()
    {
        $query = $this->db
            ->select('tgl_cert, no_cert, doc_type, komoditi, neg_tuju, upt, send_to, id_cert, id_hub, pn_pelepasan_id, no_reg, no_seri')
            ->from('ephyto_out')
            ->order_by('tgl_cert', 'DESC')
            ->get();

        return [
            'total_data' => $this->db->count_all('ephyto_out'),
            'data'       => $query->result_array()
        ];
    }
}
