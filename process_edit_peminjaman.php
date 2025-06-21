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
    $original_id_buku = $conn->real_escape_string($_POST['original_id_buku']); // Use original ID to find the record
    $id_buku = $conn->real_escape_string($_POST['id_buku']); // New ID (if allowed to change, otherwise should be same as original)
    $judul_buku = $conn->real_escape_string($_POST['judul_buku']);
    $isbn = $conn->real_escape_string($_POST['isbn']);
    $nama_penulis = $conn->real_escape_string($_POST['nama_penulis']);
    $nama_penerbit = $conn->real_escape_string($_POST['nama_penerbit']);
    $jumlah_halaman = (int)$_POST['jumlah_halaman'];
    $foto = $conn->real_escape_string($_POST['foto']); // Get photo from hidden field
    $tanggal_pinjam = $conn->real_escape_string($_POST['tanggal_pinjam']);
    $tanggal_pengembalian = $conn->real_escape_string($_POST['tanggal_pengembalian']);

    // Determine status based on tanggal_pengembalian
    $status_pengembalian = ($tanggal_pengembalian !== '0000-00-00' && !empty($tanggal_pengembalian)) ? "Sudah Dikembalikan" : "Belum Dikembalikan";


    // Start a transaction
    $conn->begin_transaction();

    try {
        // Update data_pinjam table
        // Assuming id_buku is the primary key or unique identifier for the book in the loan record
        $stmt_pinjam = $conn->prepare("UPDATE data_pinjam SET id_buku=?, judul_buku=?, isbn=?, nama_penulis=?, nama_penerbit=?, foto=?, jumlah_halaman=?, tanggal_pinjam=?, tanggal_pengembalian=? WHERE id_buku=?");
        // Note: The first 's' in bind_param is for id_buku, if it's potentially changing. If not, you might not need to update it here.
        $stmt_pinjam->bind_param("ssssssisss", $id_buku, $judul_buku, $isbn, $nama_penulis, $nama_penerbit, $foto, $jumlah_halaman, $tanggal_pinjam, $tanggal_pengembalian, $original_id_buku);
        $stmt_pinjam->execute();
        $stmt_pinjam->close();

        // Update data_pengembalian table
        // This relies on id_buku being the common link and unique for each return record.
        $stmt_pengembalian = $conn->prepare("UPDATE data_pengembalian SET judul_buku=?, tanggal_pinjam=?, tanggal_pengembalian=?, status=? WHERE id_buku=?");
        $stmt_pengembalian->bind_param("sssss", $judul_buku, $tanggal_pinjam, $tanggal_pengembalian, $status_pengembalian, $original_id_buku);
        $stmt_pengembalian->execute();
        $stmt_pengembalian->close();

        // Commit the transaction
        $conn->commit();
        header("Location: peminjaman_buku.php?status=success&message=Data peminjaman buku berhasil diperbarui!");
        exit();

    } catch (mysqli_sql_exception $exception) {
        // Rollback on error
        $conn->rollback();
        header("Location: peminjaman_buku.php?status=error&message=Gagal memperbarui data: " . $exception->getMessage());
        exit();
    }
} else {
    header("Location: peminjaman_buku.php?status=error&message=Metode request tidak valid.");
    exit();
}

$conn->close();
?>