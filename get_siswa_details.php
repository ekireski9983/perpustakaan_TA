<?php
// get_siswa_details.php
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

if (!$koneksi) {
    die(json_encode(["error" => "Koneksi gagal: " . mysqli_connect_error()]));
}

header('Content-Type: application/json');

$id_siswa = isset($_GET['id_siswa']) ? mysqli_real_escape_string($koneksi, $_GET['id_siswa']) : '';

if (!empty($id_siswa)) {
    $query = "SELECT nama_siswa FROM data_anggota WHERE id_siswa = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $id_siswa);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row);
    } else {
        echo json_encode(["nama_siswa" => null]); // Or an empty array/object
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["error" => "ID Siswa tidak disediakan."]);
}

mysqli_close($koneksi);
?>