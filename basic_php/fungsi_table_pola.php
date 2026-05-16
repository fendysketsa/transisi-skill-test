<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tabel Pola PHP</title>

    <style>
        table.tabel-pola {
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table.tabel-pola td {
            width: 32px;
            height: 24px;
            text-align: center;
            vertical-align: middle;
        }

        .hitam {
            background-color: black;
            color: white;
        }

        .putih {
            background-color: white;
            color: black;
        }
    </style>
</head>

<body>

    <?php

    function tampilkanTabelPola()
    {
        /*
        Angka 1 berarti kotak berwarna hitam.
        Angka 0 berarti kotak berwarna putih.

        Pola ini dibuat berdasarkan gambar:
        - 8 baris
        - 8 kolom
        - total angka 1 sampai 64
    */

        $pola = [
            [1, 1, 0, 0, 1, 0, 1, 0],
            [0, 1, 1, 0, 1, 0, 0, 0],
            [1, 0, 1, 0, 0, 1, 1, 0],
            [1, 1, 0, 0, 1, 1, 1, 0],
            [0, 1, 1, 0, 1, 1, 0, 0],
            [1, 0, 1, 0, 0, 1, 1, 0],
            [1, 1, 0, 0, 1, 1, 1, 0],
            [0, 1, 1, 0, 1, 1, 0, 0],
        ];

        $angka = 1;

        echo "<table class='tabel-pola'>";

        for ($baris = 0; $baris < 8; $baris++) {
            echo "<tr>";

            for ($kolom = 0; $kolom < 8; $kolom++) {
                if ($pola[$baris][$kolom] == 1) {
                    $class = "hitam";
                } else {
                    $class = "putih";
                }

                echo "<td class='$class'>$angka</td>";

                $angka++;
            }

            echo "</tr>";
        }

        echo "</table>";
    }

    // Memanggil fungsi
    tampilkanTabelPola();

    ?>

</body>

</html>