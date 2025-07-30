<?php

// Establish database connection
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Check connection
if (!$koneksi) {
    // Log the error and provide a generic message to the user
    error_log("Koneksi gagal: " . mysqli_connect_error());
    die("Maaf, terjadi masalah saat menghubungkan ke database. Silakan coba lagi nanti.");
}

// Base query for data_buku table with the desired columns
// No date filtering is needed for a static book catalog report
$query = "SELECT id_buku, isbn, judul_buku, kategori_buku, nama_penulis, nama_penerbit, jumlah_halaman FROM data_buku ORDER BY judul_buku ASC";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    // Log the error and provide a generic message
    error_log("Query gagal: " . mysqli_error($koneksi));
    die("Maaf, terjadi masalah saat mengambil data katalog buku. Silakan coba lagi nanti.");
}

// Set headers for "Excel" download (HTML interpreted by Excel)
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="laporan_katalog_buku_' . date('Y-m-d') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

// Start outputting HTML directly with Excel XML namespaces
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">
    <title>Laporan Katalog Buku</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%; /* Ensure table takes full width for better Excel interpretation */
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-family: Arial, sans-serif;
            font-size: 12px;
            white-space: nowrap; /* Prevent text wrapping in cells */
        }
        th {
            background-color: rgb(22, 24, 22); /* Dark grey/greenish for header background */
            color: white; /* White text for better contrast */
            font-weight: bold;
            font-size: 14px; /* Larger font for headers */
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>';

echo '<h1>Laporan Katalog Buku</h1>';
echo '<h2>Rekapitulasi katalog</h2>';
echo '<p>Status: Aktif</p>';
echo '<p>Jenis Rekap: Bulanan</p>';

echo '<table>
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
    <tbody>';

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id_buku']) . "</td>";
        echo "<td>" . htmlspecialchars($row['isbn']) . "</td>";
        echo "<td>" . htmlspecialchars($row['judul_buku']) . "</td>";
        echo "<td>" . htmlspecialchars($row['kategori_buku']) . "</td>"; // Display Kategori Buku
        echo "<td>" . htmlspecialchars($row['nama_penulis']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_penerbit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['jumlah_halaman']) . "</td>"; // Display Jumlah Halaman
        echo "</tr>";
    }
} else {
    // Updated colspan to 7 to match the number of columns
    echo "<tr><td colspan='7'>Tidak ada data buku dalam katalog.</td></tr>";
}

echo '    </tbody>
</table>
</body>
</html>';

// Close database connection
mysqli_close($koneksi);
exit();
?>