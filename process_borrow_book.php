<?php
session_start();

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and get POST data
    $id_buku = $conn->real_escape_string($_POST['id_buku']);
    $judul_buku = $conn->real_escape_string($_POST['judul_buku']);
    $isbn = $conn->real_escape_string($_POST['isbn']);
    $nama_penulis = $conn->real_escape_string($_POST['nama_penulis']);
    $nama_penerbit = $conn->real_escape_string($_POST['nama_penerbit']);
    $jumlah_halaman = (int)$_POST['jumlah_halaman']; // Cast to integer
    $foto = $conn->real_escape_string($_POST['foto']); // Get photo from hidden field
    $tanggal_pinjam = $conn->real_escape_string($_POST['tanggal_pinjam']);
    $tanggal_pengembalian = $conn->real_escape_string($_POST['tanggal_pengembalian']);
    $status_pengembalian = "Belum Dikembalikan"; // Default status

    // Start a transaction to ensure both inserts succeed or fail together
    $conn->begin_transaction();

    try {
        // Insert into data_pinjam table
        $stmt_pinjam = $conn->prepare("INSERT INTO data_pinjam (id_buku, judul_buku, isbn, nama_penulis, nama_penerbit, foto, jumlah_halaman, tanggal_pinjam, tanggal_pengembalian) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_pinjam->bind_param("ssssssiss", $id_buku, $judul_buku, $isbn, $nama_penulis, $nama_penerbit, $foto, $jumlah_halaman, $tanggal_pinjam, $tanggal_pengembalian);
        $stmt_pinjam->execute();
        $stmt_pinjam->close();

        // Insert into data_pengembalian table
        // Ensure your data_pengembalian table has these columns: id_buku, judul_buku, tanggal_pinjam, tanggal_pengembalian, status
        $stmt_pengembalian = $conn->prepare("INSERT INTO data_pengembalian (id_buku, judul_buku, tanggal_pinjam, tanggal_pengembalian, status) VALUES (?, ?, ?, ?, ?)");
        $stmt_pengembalian->bind_param("sssss", $id_buku, $judul_buku, $tanggal_pinjam, $tanggal_pengembalian, $status_pengembalian);
        $stmt_pengembalian->execute();
        $stmt_pengembalian->close();

        // If both inserts are successful, commit the transaction
        $conn->commit();
        header("Location: peminjaman_buku.php?status=success&message=Buku berhasil dipinjam dan dicatat untuk pengembalian!");
        exit();

    } catch (mysqli_sql_exception $exception) {
        // If any error occurs, rollback the transaction
        $conn->rollback();
        header("Location: peminjaman_buku.php?status=error&message=Gagal meminjam buku: " . $exception->getMessage());
        exit();
    }
} else {
    header("Location: peminjaman_buku.php?status=error&message=Metode request tidak valid.");
    exit();
}

$conn->close();
?>