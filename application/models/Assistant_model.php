<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assistant_model extends CI_Model
{
    public function process_question($question)
    {
        $question = strtolower(trim($question));
        $today = date('Y-m-d');

        $countryMap = [
            "AC" => "Pulau Ascension",
            "AD" => "Andora",
            "AE" => "Uni Emirat Arab",
            "AG" => "Antigua & Barbuda",
            "AI" => "Anguilla",
            "AL" => "Albania",
            "AM" => "Armenia",
            "AO" => "Angola",
            "AQ" => "Antarktika",
            "AR" => "Argentina",
            "AS" => "Samoa Amerika",
            "AT" => "Austria",
            "AU" => "Australia",
            "AW" => "Aruba",
            "AX" => "Kepulauan Aland",
            "AZ" => "Azerbaijan",
            "BA" => "Bosnia-Herzegovina",
            "BB" => "Barbados",
            "BD" => "Bangladesh",
            "BE" => "Belgia",
            "BF" => "Burkina Faso",
            "BG" => "Bulgaria",
            "BH" => "Bahrain",
            "BI" => "Burundi",
            "BJ" => "Benin",
            "BL" => "St. Barthélemy",
            "BM" => "Bermuda",
            "BN" => "Brunei",
            "BO" => "Bolivia",
            "BQ" => "Karibia Belanda",
            "BR" => "Brasil",
            "BS" => "Bahama",
            "BT" => "Bhutan",
            "BV" => "Pulau Bouvet",
            "BW" => "Botswana",
            "BY" => "Belarus",
            "BZ" => "Belize",
            "CA" => "Kanada",
            "CC" => "Kepulauan Cocos (Keeling)",
            "CD" => "Kongo (Republik Demokratik)",
            "CF" => "Republik Afrika Tengah",
            "CG" => "Kongo (Republik)",
            "CH" => "Swiss",
            "CI" => "Cote d'Ivoire",
            "CK" => "Kepulauan Cook",
            "CL" => "Cile",
            "CM" => "Kamerun",
            "CO" => "Kolombia",
            "CR" => "Kosta Rika",
            "CV" => "Tanjung Verde",
            "CW" => "Curaçao",
            "CX" => "Pulau Natal",
            "CY" => "Siprus",
            "CZ" => "Republik Cheska",
            "DE" => "Jerman",
            "DJ" => "Jibuti",
            "DK" => "Denmark",
            "DM" => "Dominika",
            "DO" => "Republik Dominika",
            "DZ" => "Aljazair",
            "EC" => "Ekuador",
            "EE" => "Estonia",
            "EG" => "Mesir",
            "EH" => "Sahara Barat",
            "ER" => "Eritrea",
            "ES" => "Spanyol",
            "ET" => "Etiopia",
            "FI" => "Finlandia",
            "FJ" => "Fiji",
            "FK" => "Kepulauan Falkland",
            "FM" => "Mikronesia",
            "FO" => "Kepulauan Faeroe",
            "FR" => "Prancis",
            "GA" => "Gabon",
            "GB" => "Inggris Raya",
            "GD" => "Grenada",
            "GE" => "Georgia",
            "GF" => "Guyana Prancis",
            "GH" => "Ghana",
            "GL" => "Greenland",
            "GM" => "Gambia",
            "GN" => "Guinea",
            "GP" => "Guadeloupe",
            "GQ" => "Guinea Ekuatorial",
            "GR" => "Yunani",
            "GT" => "Guatemala",
            "GU" => "Guam",
            "GW" => "Guinea-Bissau",
            "GY" => "Guyana",
            "HK" => "Hong Kong",
            "HN" => "Honduras",
            "HR" => "Kroasia",
            "HT" => "Haiti",
            "HU" => "Hungaria",
            "ID" => "Indonesia",
            "IE" => "Irlandia",
            "IL" => "Israel",
            "IN" => "India",
            "IQ" => "Irak",
            "IS" => "Islandia",
            "IT" => "Italia",
            "JM" => "Jamaika",
            "JP" => "Jepang",
            "KE" => "Kenya",
            "KH" => "Kamboja",
            "KR" => "Korea Selatan",
            "KW" => "Kuwait",
            "LA" => "Laos",
            "LB" => "Lebanon",
            "LK" => "Sri Lanka",
            "MM" => "Myanmar",
            "MN" => "Mongolia",
            "MO" => "Makau",
            "MY" => "Malaysia",
            "MZ" => "Mozambik",
            "NG" => "Nigeria",
            "NL" => "Belanda",
            "NZ" => "Selandia Baru",
            "PH" => "Filipina",
            "PK" => "Pakistan",
            "SG" => "Singapura",
            "TH" => "Thailand",
            "TW" => "Taiwan",
            "US" => "Amerika Serikat",
            "VN" => "Vietnam",
            "ZA" => "Afrika Selatan",
            "ZW" => "Zimbabwe"
        ];

        $is_today = strpos($question, 'hari ini') !== false;
        $is_week = strpos($question, 'minggu') !== false;
        $is_month = strpos($question, 'bulan') !== false;
        $periode_label = $is_week ? 'minggu ini' : ($is_month ? 'bulan ini' : 'hari ini');

        $mappings = [
            'ecert in' => [
                'table' => 'ecert_in',
                'label' => 'eCert In',
                'negara' => 'neg_asal',
                'komoditi' => 'komo_eng',
                'jenis' => 'in'
            ],
            'ephyto in' => [
                'table' => 'ephyto_in',
                'label' => 'ePhyto In',
                'negara' => 'neg_asal',
                'komoditi' => 'komo_eng',
                'jenis' => 'in'
            ],
            'ecert out' => [
                'table' => 'eah_out',
                'label' => 'eCert Out',
                'negara' => 'neg_tuju',
                'komoditi' => 'komoditi',
                'jenis' => 'out'
            ],
            'ephyto out' => [
                'table' => 'ephyto_out',
                'label' => 'ePhyto Out',
                'negara' => 'neg_tuju',
                'komoditi' => 'komoditi',
                'jenis' => 'out'
            ],
        ];

        $responses = [];

        foreach ($mappings as $key => $info) {
            if (strpos($question, $key) !== false) {
                if ($is_today) {
                    $this->db->where("DATE(tgl_cert) =", "'$today'", false);
                } elseif ($is_week) {
                    $this->db->where("YEARWEEK(tgl_cert, 1) =", "YEARWEEK('$today', 1)", false);
                } elseif ($is_month) {
                    $this->db->where("DATE_FORMAT(tgl_cert, '%Y-%m') =", "DATE_FORMAT('$today', '%Y-%m')", false);
                } else {
                    $this->db->where("DATE(tgl_cert) =", "'$today'", false);
                }

                $this->db->select("{$info['komoditi']} AS komoditi, {$info['negara']} AS negara, no_cert");
                $query = $this->db->get($info['table']);
                $data = $query->result_array();
                $count = count($data);

                $responses[] = "{$info['label']} $periode_label: **{$count} dokumen**";

                if ($count > 0) {
                    $samples = array_slice($data, 0, 5);
                    foreach ($samples as $i => $row) {
                        $komoditi = !empty($row['komoditi']) ? $row['komoditi'] : '(komoditi tidak tercatat)';
                        $negara_kode = trim($row['negara']);
                        $negara = isset($countryMap[$negara_kode]) ? $countryMap[$negara_kode] : $negara_kode;
                        $no_cert = !empty($row['no_cert']) ? $row['no_cert'] : 'tanpa nomor sertifikat';
                        $label_negara = ($info['jenis'] === 'in') ? 'Negara Asal' : 'Negara Tujuan';

                        $responses[] = ($i + 1) . ". Komoditas : {$komoditi}\n" .
                            "{$label_negara} : {$negara}\n" .
                            "Nomor Sertifikat : {$no_cert}" .
                            (($i < count($samples) - 1) ? "\n──────────────" : "");
                    }

                    if ($count > 5) {
                        $responses[] = "…dan " . ($count - 5) . " lainnya 📄";
                    }
                }
            }
        }

        if (!empty($responses)) {
            return implode("\n", $responses);
        }

        if (strpos($question, 'tanggal') !== false || strpos($question, 'hari ini') !== false) {
            return "Hari ini adalah tanggal " . date('d F Y') . " 📅";
        }

        return "Maaf, saya belum mengenali pertanyaan itu 💬.\n\nCoba tanya seperti:\n- 'Berapa total ecert in hari ini?'\n- 'Tampilkan rincian ephyto out minggu ini'\n- 'ephyto in bulan ini apa saja?'";
    }
}
