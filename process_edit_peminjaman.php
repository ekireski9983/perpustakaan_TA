<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = "";     // Replace with your database password
$dbname = "perpustakaan";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from the form
$original_id_buku = $_POST['original_id_buku']; // This is crucial for identifying the record
$id_buku = $_POST['id_buku']; // This will be the same as original_id_buku if ID is not editable
$judul_buku = $_POST['judul_buku'];
$isbn = $_POST['isbn'];
$nama_penulis = $_POST['nama_penulis'];
$nama_penerbit = $_POST['nama_penerbit'];
$jumlah_halaman = $_POST['jumlah_halaman'];
$foto = $_POST['foto'];
$tanggal_pinjam = $_POST['tanggal_pinjam'];
$tanggal_pengembalian = $_POST['tanggal_pengembalian'];

// Update the existing record in data_pinjam
$sql = "UPDATE data_pinjam SET
            judul_buku = ?,
            isbn = ?,
            nama_penulis = ?,
            nama_penerbit = ?,
            jumlah_halaman = ?,
            foto = ?,
            tanggal_pinjam = ?,
            tanggal_pengembalian = ?
        WHERE id_buku = ?"; // Use original_id_buku to ensure correct record is updated

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssissss", $judul_buku, $isbn, $nama_penulis, $nama_penerbit, $jumlah_halaman, $foto, $tanggal_pinjam, $tanggal_pengembalian, $original_id_buku);

if ($stmt->execute()) {
    $status = 'success';
    $message = "Data peminjaman buku berhasil diperbarui.";
} else {
    $status = 'error';
    $message = "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

// Redirect back to the peminjaman_buku.php page with status message
header("Location: peminjaman_buku.php?status=$status&message=" . urlencode($message));
exit();
?>