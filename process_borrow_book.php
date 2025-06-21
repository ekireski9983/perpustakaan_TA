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
$id_buku = $_POST['id_buku'];
$judul_buku = $_POST['judul_buku'];
$isbn = $_POST['isbn'];
$nama_penulis = $_POST['nama_penulis'];
$nama_penerbit = $_POST['nama_penerbit'];
$jumlah_halaman = $_POST['jumlah_halaman'];
$foto = $_POST['foto'];
$tanggal_pinjam = $_POST['tanggal_pinjam'];
$tanggal_pengembalian = $_POST['tanggal_pengembalian'];

// Check if the book already exists in data_pinjam
$check_sql = "SELECT COUNT(*) AS count FROM data_pinjam WHERE id_buku = ?";
$stmt_check = $conn->prepare($check_sql);
$stmt_check->bind_param("s", $id_buku);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row_check = $result_check->fetch_assoc();
$book_exists = $row_check['count'] > 0;
$stmt_check->close();

if ($book_exists) {
    // If book exists, update the borrow and return dates
    $sql = "UPDATE data_pinjam SET tanggal_pinjam = ?, tanggal_pengembalian = ? WHERE id_buku = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $tanggal_pinjam, $tanggal_pengembalian, $id_buku);
    $message = "Tanggal peminjaman buku berhasil diperbarui.";
} else {
    // If book does not exist, insert a new record
    $sql = "INSERT INTO data_pinjam (id_buku, judul_buku, isbn, nama_penulis, nama_penerbit, jumlah_halaman, foto, tanggal_pinjam, tanggal_pengembalian) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssisss", $id_buku, $judul_buku, $isbn, $nama_penulis, $nama_penerbit, $jumlah_halaman, $foto, $tanggal_pinjam, $tanggal_pengembalian);
    $message = "Buku berhasil dipinjam.";
}

if ($stmt->execute()) {
    $status = 'success';
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