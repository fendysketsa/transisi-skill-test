<?php

function bentukNgram($kata, $jumlahKata)
{
    $hasil = [];

    for ($i = 0; $i < count($kata); $i += $jumlahKata) {
        $kelompok = array_slice($kata, $i, $jumlahKata);

        if (count($kelompok) == $jumlahKata) {
            $hasil[] = implode(' ', $kelompok);
        }
    }

    return $hasil;
}

function prosesKalimat($kalimat)
{
    $kalimat = strtolower($kalimat);
    $kalimat = preg_replace('/[^a-z0-9\s]/', '', $kalimat);
    $kalimat = trim($kalimat);
    $kalimat = preg_replace('/\s+/', ' ', $kalimat);

    $kata = explode(' ', $kalimat);

    return [
        'unigram' => bentukNgram($kata, 1),
        'bigram'  => bentukNgram($kata, 2),
        'trigram' => bentukNgram($kata, 3)
    ];
}

$kalimat = "Jakarta adalah ibukota negara Republik Indonesia";

$hasil = prosesKalimat($kalimat);

echo "Unigram : " . implode(', ', $hasil['unigram']) . "<br>";
echo "Bigram : " . implode(', ', $hasil['bigram']) . "<br>";
echo "Trigram : " . implode(', ', $hasil['trigram']) . "<br>";
