<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assistant_model extends CI_Model
{
    public function process_question($question)
    {
        $question = strtolower(trim($question));
        $today = date('Y-m-d');

        // periode
        // =============================
        $is_today = strpos($question, 'hari ini') !== false;
        $is_week = strpos($question, 'minggu') !== false;
        $is_month = strpos($question, 'bulan') !== false;

        $periode_label = $is_week ? 'minggu ini' : ($is_month ? 'bulan ini' : 'hari ini');

        $mappings = [
            'ecert in'   => ['table' => 'ecert_in',   'label' => 'eCert In'],
            'ephyto in'  => ['table' => 'ephyto_in',  'label' => 'ePhyto In'],
            'ecert out'  => ['table' => 'eah_out',    'label' => 'eCert Out'],
            'ephyto out' => ['table' => 'ephyto_out', 'label' => 'ePhyto Out'],
        ];

        $responses = [];

        // Cek tiap keyword di pertanyaan
        // =============================
        foreach ($mappings as $key => $info) {
            if (strpos($question, $key) !== false) {
                // Filter berdasarkan periode
                if ($is_today) {
                    $this->db->where("DATE(tgl_cert) =", "'$today'", false);
                } elseif ($is_week) {
                    $this->db->where("YEARWEEK(tgl_cert, 1) =", "YEARWEEK('$today', 1)", false);
                } elseif ($is_month) {
                    $this->db->where("DATE_FORMAT(tgl_cert, '%Y-%m') =", "DATE_FORMAT('$today', '%Y-%m')", false);
                } else {
                    $this->db->where("DATE(tgl_cert) =", "'$today'", false);
                }

                $count = $this->db->count_all_results($info['table']);
                $responses[] = "{$info['label']} $periode_label: $count dokumen";
            }
        }

        if (!empty($responses)) {
            return implode("\n", $responses);
        }

        if (strpos($question, 'tanggal') !== false || strpos($question, 'hari ini') !== false) {
            return "Hari ini adalah tanggal " . date('d F Y') . " 📅";
        }

        return "Maaf, saya belum mengenali pertanyaan itu 💬.  
Coba tanya seperti:
- 'Berapa total ecert in hari ini?'
- 'Total ephyto out minggu ini?'
- 'Berapa ecert in dan ephyto in hari ini?'";
    }
}
