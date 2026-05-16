<?php

function hitungHurufKecil($teks)
{
    $jumlah = 0;

    // Loop setiap karakter dalam string
    for ($i = 0; $i < strlen($teks); $i++) {
        // Cek apakah karakter adalah huruf kecil
        if (ctype_lower($teks[$i])) {
            $jumlah++;
        }
    }

    return $jumlah;
}

// Contoh penggunaan
$input = "TranSISI";
$hasil = hitungHurufKecil($input);

echo '"' . $input . '" mengandung ' . $hasil . ' buah huruf kecil.';


//Alternatif

function hitungHurufKecilAlternatif($teks)
{
    preg_match_all('/[a-z]/', $teks, $hasil);
    return count($hasil[0]);
}

$input = "TranSISI";
$jumlah = hitungHurufKecilAlternatif($input);

echo '"' . $input . '" mengandung ' . $jumlah . ' buah huruf kecil.';
