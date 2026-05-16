<?php

$nilai = "72 65 73 78 75 74 90 81 87 65 55 69 72 78 79 91 100 40 67 77 86";

// Mengubah string menjadi array
$arrayNilai = explode(" ", $nilai);

// Mengubah setiap data array menjadi integer
$arrayNilai = array_map('intval', $arrayNilai);

// Menghitung jumlah data
$jumlahData = count($arrayNilai);

// Menghitung total nilai
$totalNilai = array_sum($arrayNilai);

// Menghitung rata-rata
$rataRata = $totalNilai / $jumlahData;

// Mengurutkan nilai dari terbesar ke terkecil
$nilaiTertinggi = $arrayNilai;
rsort($nilaiTertinggi);

// Mengambil 7 nilai tertinggi
$tujuhNilaiTertinggi = array_slice($nilaiTertinggi, 0, 7);

// Mengurutkan nilai dari terkecil ke terbesar
$nilaiTerendah = $arrayNilai;
sort($nilaiTerendah);

// Mengambil 7 nilai terendah
$tujuhNilaiTerendah = array_slice($nilaiTerendah, 0, 7);

// Menampilkan hasil
echo "Nilai rata-rata: " . number_format($rataRata, 2) . "<br>";

echo "7 Nilai tertinggi: ";
echo implode(", ", $tujuhNilaiTertinggi);
echo "<br>";

echo "7 Nilai terendah: ";
echo implode(", ", $tujuhNilaiTerendah);
