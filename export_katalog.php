<?php

// Establish database connection
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Check connection
if (!$koneksi) {
    // Log the error for debugging, but don't expose sensitive details to the user
    error_log("Koneksi gagal: " . mysqli_connect_error());
    die("Maaf, terjadi masalah saat menghubungkan ke database. Silakan coba lagi nanti.");
}

// Base query for data_buku table with the desired columns
$query = "SELECT id_buku, isbn, judul_buku, nama_penulis, nama_penerbit, kategori_buku, jumlah_halaman FROM data_buku ORDER BY judul_buku ASC";

// Execute the query
$result = mysqli_query($koneksi, $query);

if (!$result) {
    // Log the error
    error_log("Query gagal: " . mysqli_error($koneksi));
    die("Maaf, terjadi masalah saat mengambil data katalog buku. Silakan coba lagi nanti.");
}

// Set header to HTML content
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Katalog Buku</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: rgb(22, 242, 22); /* Bright greenish for header background */
            color: black; /* Changed to black for better contrast on bright green */
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: bold;
        }
        td {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .download-button {
            display: block;
            width: fit-content;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .download-button:hover {
            background-color: #0056b3;
        }
        @media print {
            body {
                margin: 0;
            }
            table {
                font-size: 10pt;
            }
            th, td {
                padding: 5px;
            }
            .download-button {
                display: none; /* Hide download button when printing */
            }
        }
    </style>
</head>
<body>
    <h1>Laporan Katalog Buku</h1>
    <h2>Rekapitulasi katalog</h2>
    <p>Status: Aktif</p>
    <p>Jenis Rekap: Bulanan</p>
    <a href="download_csv_katalog.php" class="download-button">Cetak Laporan Katalog</a>

    <table>
        <thead>
            <tr>
                <th>ID Buku</th>
                <th>ISBN</th>
                <th>Judul Buku</th>
                <th>Kategori Buku</th>
                <th>Nama Penulis</th>
                <th>Nama Penerbit</th>
                <th>Jumlah Halaman</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id_buku']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['isbn']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['judul_buku']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['kategori_buku']) . "</td>"; // Added kategori_buku
                    echo "<td>" . htmlspecialchars($row['nama_penulis']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_penerbit']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['jumlah_halaman']) . "</td>"; // Added jumlah_halaman
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Tidak ada data buku dalam katalog.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <?php
    // Close database connection
    mysqli_close($koneksi);
    ?>
</body>
</html>
<?php
exit();
?>