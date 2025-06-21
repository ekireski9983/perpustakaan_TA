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

// Initialize status and message
$status = 'error';
$message = 'Terjadi kesalahan tidak terduga.';

// Start a transaction for atomicity
$conn->begin_transaction();

try {
    // 1. Handle data_pinjam table (Insert or Update)
    $check_pinjam_sql = "SELECT COUNT(*) AS count FROM data_pinjam WHERE id_buku = ?";
    $stmt_check_pinjam = $conn->prepare($check_pinjam_sql);
    if ($stmt_check_pinjam === false) {
        throw new Exception("Prepare failed on data_pinjam check: " . $conn->error);
    }
    $stmt_check_pinjam->bind_param("s", $id_buku);
    $stmt_check_pinjam->execute();
    $result_check_pinjam = $stmt_check_pinjam->get_result();
    $row_check_pinjam = $result_check_pinjam->fetch_assoc();
    $book_in_pinjam_exists = $row_check_pinjam['count'] > 0;
    $stmt_check_pinjam->close();

    if ($book_in_pinjam_exists) {
        // If book exists in data_pinjam, update the borrow and return dates
        $sql_pinjam = "UPDATE data_pinjam SET tanggal_pinjam = ?, tanggal_pengembalian = ? WHERE id_buku = ?";
        $stmt_pinjam = $conn->prepare($sql_pinjam);
        if ($stmt_pinjam === false) {
            throw new Exception("Prepare failed on data_pinjam UPDATE: " . $conn->error);
        }
        $stmt_pinjam->bind_param("sss", $tanggal_pinjam, $tanggal_pengembalian, $id_buku);
        if (!$stmt_pinjam->execute()) {
            throw new Exception("Error updating data_pinjam: " . $stmt_pinjam->error);
        }
        $stmt_pinjam->close();
        $message = "Tanggal peminjaman buku berhasil diperbarui.";
    } else {
        // If book does not exist in data_pinjam, insert a new record
        $sql_pinjam = "INSERT INTO data_pinjam (id_buku, judul_buku, isbn, nama_penulis, nama_penerbit, jumlah_halaman, foto, tanggal_pinjam, tanggal_pengembalian) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_pinjam = $conn->prepare($sql_pinjam);
        if ($stmt_pinjam === false) {
            throw new Exception("Prepare failed on data_pinjam INSERT: " . $conn->error);
        }
        $stmt_pinjam->bind_param("sssssisss", $id_buku, $judul_buku, $isbn, $nama_penulis, $nama_penerbit, $jumlah_halaman, $foto, $tanggal_pinjam, $tanggal_pengembalian);
        if (!$stmt_pinjam->execute()) {
            throw new Exception("Error inserting into data_pinjam: " . $stmt_pinjam->error);
        }
        $stmt_pinjam->close();
        $message = "Buku berhasil dipinjam.";
    }

    // 2. Handle data_pengembalian table (Insert or Update)
    $check_pengembalian_sql = "SELECT COUNT(*) AS count FROM data_pengembalian WHERE id_buku = ? AND status = 'Belum Dikembalikan'";
    $stmt_check_pengembalian = $conn->prepare($check_pengembalian_sql);
    if ($stmt_check_pengembalian === false) {
        throw new Exception("Prepare failed on data_pengembalian check: " . $conn->error);
    }
    $stmt_check_pengembalian->bind_param("s", $id_buku);
    $stmt_check_pengembalian->execute();
    $result_check_pengembalian = $stmt_check_pengembalian->get_result();
    $row_check_pengembalian = $result_check_pengembalian->fetch_assoc();
    $book_in_pengembalian_exists = $row_check_pengembalian['count'] > 0;
    $stmt_check_pengembalian->close();

    $status_pengembalian = 'Belum Dikembalikan';

    if ($book_in_pengembalian_exists) {
        // If an active 'Belum Dikembalikan' record exists, update its dates
        $sql_pengembalian = "UPDATE data_pengembalian SET judul_buku = ?, tanggal_pinjam = ?, tanggal_pengembalian = ? WHERE id_buku = ? AND status = 'Belum Dikembalikan'";
        $stmt_pengembalian = $conn->prepare($sql_pengembalian);
        if ($stmt_pengembalian === false) {
            throw new Exception("Prepare failed on data_pengembalian UPDATE: " . $conn->error);
        }
        // Line 79, now with proper error checking for prepare
        $stmt_pengembalian->bind_param("ssss", $judul_buku, $tanggal_pinjam, $tanggal_pengembalian, $id_buku);
        if (!$stmt_pengembalian->execute()) {
            throw new Exception("Error updating data_pengembalian: " . $stmt_pengembalian->error);
        }
        $stmt_pengembalian->close();
    } else {
        // Otherwise, insert a new record for this borrowing event
        $sql_pengembalian = "INSERT INTO data_pengembalian (id_buku, judul_buku, tanggal_pinjam, tanggal_pengembalian, status) VALUES (?, ?, ?, ?, ?)";
        $stmt_pengembalian = $conn->prepare($sql_pengembalian);
        if ($stmt_pengembalian === false) {
            throw new Exception("Prepare failed on data_pengembalian INSERT: " . $conn->error);
        }
        $stmt_pengembalian->bind_param("sssss", $id_buku, $judul_buku, $tanggal_pinjam, $tanggal_pengembalian, $status);
        if (!$stmt_pengembalian->execute()) {
            throw new Exception("Error inserting into data_pengembalian: " . $stmt_pengembalian->error);
        }
        $stmt_pengembalian->close();
    }

    // Commit the transaction if all queries were successful
    $conn->commit();
    $status = 'success';

} catch (Exception $e) {
    // Rollback the transaction if any query failed
    $conn->rollback();
    $status = 'error';
    $message = $e->getMessage();
}

$conn->close();

// Redirect back to the peminjaman_buku.php page with status message
header("Location: peminjaman_buku.php?status=$status&message=" . urlencode($message));
exit();
?>