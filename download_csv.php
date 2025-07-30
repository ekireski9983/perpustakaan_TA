<?php

// Establish database connection
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Check connection
if (!$koneksi) {
    // Log the error and provide a generic message to the user
    error_log("Koneksi gagal: " . mysqli_connect_error());
    die("Maaf, terjadi masalah saat menghubungkan ke database. Silakan coba lagi nanti.");
}

// Initialize start and end dates
$startDate = '';
$endDate = '';

// Get and sanitize start and end dates from the request if they exist
if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
    $startDate = mysqli_real_escape_string($koneksi, $_GET['start_date']);
}
if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
    $endDate = mysqli_real_escape_string($koneksi, $_GET['end_date']);
}

// Base query for data_pinjam table.
// Ensure 'tanggal_pengembalian' is selected as it's part of your table structure.
// IMPORTANT: Added 'kategori_buku' to the SELECT statement
$query = "SELECT id_buku, isbn, judul_buku, nama_penulis, nama_penerbit, kategori_buku, tanggal_pinjam, tanggal_pengembalian FROM data_pinjam";

// Add date filter if both start and end dates are provided
if (!empty($startDate) && !empty($endDate)) {
    // Using mysqli_real_escape_string provides some protection,
    // but prepared statements are strongly recommended for production applications.
    $query .= " WHERE tanggal_pinjam BETWEEN '$startDate' AND '$endDate'";
}

$result = mysqli_query($koneksi, $query);

if (!$result) {
    // Log the error and provide a generic message
    error_log("Query gagal: " . mysqli_error($koneksi));
    die("Maaf, terjadi masalah saat mengambil data. Silakan coba lagi nanti.");
}

// Set headers for "Excel" download (HTML interpreted by Excel)
// Using .xls is more widely compatible for basic HTML exports to Excel.
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="data_peminjaman_styled_' . date('Y-m-d') . '.xls"');
header('Pragma: no-cache'); // Recommended for older IE versions
header('Expires: 0'); // Recommended for older IE versions

// Start outputting HTML directly with Excel XML namespaces
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">
    <title>Laporan Peminjaman Buku</title>
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
        /* Style for date display */
        .report-info {
            margin-bottom: 20px;
        }
        .report-info p {
            margin: 5px 0;
        }
        h2 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>';

// Display period if dates are set
if (!empty($startDate) && !empty($endDate)) {
    echo '<div class="report-info">';
    echo '<h2>Rekapitulasi Peminjaman</h2>';
    echo '<p>Status: Aktif</p>';
    echo '<p>Jenis Rekap: Bulanan</p>';
    echo '<p>Peminjaman dari: <strong>' . htmlspecialchars($startDate) . '</strong> sampai <strong>' . htmlspecialchars($endDate) . '</strong></p>';
    echo '</div>';
}

echo '<table>
    <thead>
        <tr>
            <th>ID Buku</th>
            <th>ISBN</th>
            <th>Judul Buku</th>
            <th>Nama Penulis</th>
            <th>Nama Penerbit</th>
            <th>Kategori buku</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Pengembalian</th>
        </tr>
    </thead>
    <tbody>';

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id_buku']) . "</td>";
        echo "<td>" . htmlspecialchars($row['isbn']) . "</td>";
        echo "<td>" . htmlspecialchars($row['judul_buku']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_penulis']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_penerbit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['kategori_buku']) . "</td>"; // Display kategori_buku
        echo "<td>" . htmlspecialchars($row['tanggal_pinjam']) . "</td>";
        echo "<td>" . htmlspecialchars($row['tanggal_pengembalian']) . "</td>"; // Display pengembalian
        echo "</tr>";
    }
} else {
    // Updated colspan to 8 to match the number of columns
    echo "<tr><td colspan='8'>Tidak ada data peminjaman dalam periode ini.</td></tr>";
}

echo '    </tbody>
</table>
</body>
</html>';

// Close database connection
mysqli_close($koneksi);
exit();
?>