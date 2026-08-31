<?php

namespace App\Imports\Traits;

trait CaseInsensitiveMapper
{
    /**
     * Map dynamic column dropdown values case-insensitively
     */
    public function mapDynamicDropdowns($kolomDinamis, $dataTambahan)
    {
        foreach ($kolomDinamis as $kolom) {
            if ($kolom->tipe_input === 'dropdown' && !empty($kolom->pilihan_dropdown) && isset($dataTambahan[$kolom->nama_kolom])) {
                $options = json_decode($kolom->pilihan_dropdown, true) ?? [];
                $inputValue = strtolower(trim($dataTambahan[$kolom->nama_kolom]));
                
                foreach ($options as $opt) {
                    if (strtolower(trim($opt)) === $inputValue) {
                        // Found a case-insensitive match, map to the EXACT case from DB
                        $dataTambahan[$kolom->nama_kolom] = trim($opt);
                        break;
                    }
                }
            }
        }
        return $dataTambahan;
    }

    /**
     * Map static dropdowns case-insensitively
     */
    public function mapStaticDropdown($inputValue, $optionsArray)
    {
        $inputLower = strtolower(trim($inputValue));
        foreach ($optionsArray as $opt) {
            if (strtolower(trim($opt)) === $inputLower) {
                return trim($opt);
            }
        }
        return $inputValue; // fallback
    }
}
