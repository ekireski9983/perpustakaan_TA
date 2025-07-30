<?php

// Establish database connection
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Check connection
if (!$koneksi) {
    // Log the error for debugging, but don't expose sensitive details to the user
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

// Base query for data_pinjam table with the desired columns
// IMPORTANT: Added 'kategori_buku' to the SELECT statement
$query = "SELECT id_buku, isbn, judul_buku, nama_penulis, nama_penerbit, kategori_buku, tanggal_pinjam, tanggal_pengembalian FROM data_pinjam";

// Add date filter if both start and end dates are provided
if (!empty($startDate) && !empty($endDate)) {
    // Use prepared statements for better security against SQL injection
    // For simplicity, I'll keep the string concatenation here, but prepared statements are highly recommended for production
    $query .= " WHERE tanggal_pinjam BETWEEN '$startDate' AND '$endDate'";
}

$result = mysqli_query($koneksi, $query);

if (!$result) {
    // Log the error
    error_log("Query gagal: " . mysqli_error($koneksi));
    die("Maaf, terjadi masalah saat mengambil data. Silakan coba lagi nanti.");
}

// Set header to HTML content
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman Buku</title>
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
    <h1>Laporan Peminjaman Buku</h1>

    <?php if (!empty($startDate) && !empty($endDate)): ?>
        <h2>Rekapitulasi Peminjaman</h2>
        <p>Status: Aktif</p>
        <p>Jenis Rekap: Bulanan</p>
        <p>Peminjaman dari <strong><?php echo htmlspecialchars($startDate); ?></strong> sampai <strong><?php echo htmlspecialchars($endDate); ?></strong></p>
    <?php endif; ?>

    <a href="download_csv.php?start_date=<?php echo urlencode($startDate); ?>&end_date=<?php echo urlencode($endDate); ?>" class="download-button">Cetak Laporan Peminjaman</a>

    <table>
        <thead>
            <tr>
                <th>ID Buku</th>
                <th>ISBN</th>
                <th>Judul Buku</th>
                <th>Nama Penulis</th>
                <th>Nama Penerbit</th>
                <th>Kategori Buku</th> <th>Tanggal Pinjam</th>
                <th>Tanggal Pengembalian</th>
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
                    echo "<td>" . htmlspecialchars($row['nama_penulis']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_penerbit']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['kategori_buku']) . "</td>"; // Display kategori_buku
                    echo "<td>" . htmlspecialchars($row['tanggal_pinjam']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tanggal_pengembalian']) . "</td>"; // Display pengembalian
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Tidak ada data peminjaman dalam periode ini.</td></tr>"; }
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