<?php

$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "perpustakaan";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$id_buku = $_POST['id_buku'];
$judul_buku = $_POST['judul_buku'];
$isbn = $_POST['isbn'];
$nama_penulis = $_POST['nama_penulis'];
$nama_penerbit = $_POST['nama_penerbit'];
$jumlah_halaman = $_POST['jumlah_halaman'];
$foto = $_POST['foto'];
$tanggal_pinjam = $_POST['tanggal_pinjam'];
$tanggal_pengembalian = $_POST['tanggal_pengembalian'];


$status = 'error';
$message = 'Terjadi kesalahan tidak terduga.';


$conn->begin_transaction();

try {
    
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

    
    $check_pengembalian_sql = "SELECT COUNT(*) AS count FROM data_pengembalian WHERE id_buku = ? AND status_pengembalian = 'belum dikembalikan'";
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

    $status_pengembalian = 'belum dikembalikan';

    if ($book_in_pengembalian_exists) {
        
        $sql_pengembalian = "UPDATE data_pengembalian SET judul_buku = ?, tanggal_pinjam = ?, tanggal_pengembalian = ? WHERE id_buku = ? AND status_pengembalian = 'belum dikembalikan'";
        $stmt_pengembalian = $conn->prepare($sql_pengembalian);
        if ($stmt_pengembalian === false) {
            throw new Exception("Prepare failed on data_pengembalian UPDATE: " . $conn->error);
        }
        
        $stmt_pengembalian->bind_param("ssss", $judul_buku, $tanggal_pinjam, $tanggal_pengembalian, $id_buku);
        if (!$stmt_pengembalian->execute()) {
            throw new Exception("Error updating data_pengembalian: " . $stmt_pengembalian->error);
        }
        $stmt_pengembalian->close();
    } else {
        
        $sql_pengembalian = "INSERT INTO data_pengembalian (id_buku, judul_buku, tanggal_pinjam, tanggal_pengembalian, status_pengembalian) VALUES (?, ?, ?, ?, ?)";
        $stmt_pengembalian = $conn->prepare($sql_pengembalian);
        if ($stmt_pengembalian === false) {
            throw new Exception("Prepare failed on data_pengembalian INSERT: " . $conn->error);
        }
        $stmt_pengembalian->bind_param("sssss", $id_buku, $judul_buku, $tanggal_pinjam, $tanggal_pengembalian, $status_pengembalian);
        if (!$stmt_pengembalian->execute()) {
            throw new Exception("Error inserting into data_pengembalian: " . $stmt_pengembalian->error);
        }
        $stmt_pengembalian->close();
    }

    
    $conn->commit();
    $status = 'success';

} catch (Exception $e) {
    
    $conn->rollback();
    $status = 'error';
    $message = $e->getMessage();
}

$conn->close();


header("Location: peminjaman_buku.php?status=$status&message=" . urlencode($message));
exit();
?>