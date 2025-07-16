<?php

$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");


if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}


$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    
    $query = "DELETE FROM data_anggota WHERE id_siswa = '$id'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        
        header("Location: kelola_anggota.php");
        exit();
    } else {
        echo "Gagal menghapus data: " . mysqli_error($koneksi);
    }
} else {
    echo "ID tidak ditemukan.";
}


mysqli_close($koneksi);
?>
