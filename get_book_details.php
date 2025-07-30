<?php
// get_book_details.php

$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

header('Content-Type: application/json');

if (isset($_GET['id_buku'])) {
    $id_buku = mysqli_real_escape_string($koneksi, $_GET['id_buku']);

    // Select the 'foto' column from data_buku
    $query = "SELECT isbn, judul_buku, nama_penulis, nama_penerbit, jumlah_halaman, kategori_buku, foto FROM data_buku WHERE id_buku = '$id_buku'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $bookDetails = mysqli_fetch_assoc($result);
        echo json_encode($bookDetails);
    } else {
        echo json_encode(null); // Return null if book not found
    }
} else {
    echo json_encode(null); // Return null if no id_buku is provided
}

mysqli_close($koneksi);
?>