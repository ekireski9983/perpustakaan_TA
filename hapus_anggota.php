<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil ID dari parameter URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    // Query hapus data
    $query = "DELETE FROM data_anggota WHERE id_siswa = '$id'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        // Redirect kembali ke halaman anggota setelah berhasil hapus
        header("Location: kelola_anggota.php");
        exit();
    } else {
        echo "Gagal menghapus data: " . mysqli_error($koneksi);
    }
} else {
    echo "ID tidak ditemukan.";
}

// Tutup koneksi
mysqli_close($koneksi);
?>
