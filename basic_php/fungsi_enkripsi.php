<?php

function enkripsi($teks)
{
    $hasil = "";
    $teks = strtoupper($teks);

    for ($i = 0; $i < strlen($teks); $i++) {
        $huruf = $teks[$i];

        // Mengubah huruf menjadi angka 0 - 25
        // A = 0, B = 1, C = 2, ..., Z = 25
        $posisiHuruf = ord($huruf) - ord('A');

        // Posisi karakter dimulai dari 1
        $posisi = $i + 1;

        // Jika posisi ganjil, huruf digeser maju
        if ($posisi % 2 == 1) {
            $posisiBaru = $posisiHuruf + $posisi;
        }
        // Jika posisi genap, huruf digeser mundur
        else {
            $posisiBaru = $posisiHuruf - $posisi;
        }

        // Agar tetap berada dalam rentang A - Z
        $posisiBaru = ($posisiBaru + 26) % 26;

        // Mengubah kembali angka menjadi huruf
        $hurufBaru = chr($posisiBaru + ord('A'));

        $hasil .= $hurufBaru;
    }

    return $hasil;
}

// Contoh penggunaan
$input = "DFHKNQ";
$output = enkripsi($input);

echo "Input  : " . $input . "<br>";
echo "Output : " . $output;
