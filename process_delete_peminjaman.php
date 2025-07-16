<?php

$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "perpustakaan";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_buku_to_delete = $conn->real_escape_string($_GET['id']);

    
    $conn->begin_transaction();

    try {
        
        $stmt_pinjam = $conn->prepare("DELETE FROM data_pinjam WHERE id_buku = ?");
        $stmt_pinjam->bind_param("s", $id_buku_to_delete); 
        $stmt_pinjam->execute();
        $stmt_pinjam->close();

        
        $stmt_pengembalian = $conn->prepare("DELETE FROM data_pengembalian WHERE id_buku = ?");
        $stmt_pengembalian->bind_param("s", $id_buku_to_delete);
        $stmt_pengembalian->execute();
        $stmt_pengembalian->close();

        
        $conn->commit();
        
        header("Location: peminjaman_buku.php?status=success&message=Data peminjaman dan pengembalian buku berhasil dihapus!");
        exit();

    } catch (mysqli_sql_exception $exception) {
        
        $conn->rollback();
        
        header("Location: peminjaman_buku.php?status=error&message=Gagal menghapus data: " . $exception->getMessage());
        exit();
    }
} else {
    
    header("Location: peminjaman_buku.php?status=error&message=ID Buku tidak ditemukan.");
    exit();
}

$conn->close();
?>