<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "perpustakaan";


$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize and get POST data, using isset() for safety
$original_id_buku = isset($_POST['original_id_buku']) ? $_POST['original_id_buku'] : '';
$judul_buku = isset($_POST['judul_buku']) ? $_POST['judul_buku'] : '';
$isbn = isset($_POST['isbn']) ? $_POST['isbn'] : '';
$nama_penulis = isset($_POST['nama_penulis']) ? $_POST['nama_penulis'] : '';
$nama_penerbit = isset($_POST['nama_penerbit']) ? $_POST['nama_penerbit'] : '';
$jumlah_halaman = isset($_POST['jumlah_halaman']) ? (int)$_POST['jumlah_halaman'] : 0; // Cast to int
$foto = isset($_POST['foto']) ? $_POST['foto'] : '';
$tanggal_pinjam = isset($_POST['tanggal_pinjam']) ? $_POST['tanggal_pinjam'] : '';
$tanggal_pengembalian = isset($_POST['tanggal_pengembalian']) ? $_POST['tanggal_pengembalian'] : '';
$kategori_buku = isset($_POST['kategori_buku']) ? $_POST['kategori_buku'] : ''; // Ensure kategori_buku is retrieved

$status = 'error';
$message = 'Terjadi kesalahan tidak terduga.';


$conn->begin_transaction();

try {
    // Update data_pinjam
    $sql_pinjam_update = "UPDATE data_pinjam SET
                            judul_buku = ?,
                            isbn = ?,
                            nama_penulis = ?,
                            nama_penerbit = ?,
                            kategori_buku = ?, -- This was missing in the bind_param type string
                            jumlah_halaman = ?,
                            foto = ?,
                            tanggal_pinjam = ?,
                            tanggal_pengembalian = ?
                          WHERE id_buku = ?";

    $stmt_pinjam_update = $conn->prepare($sql_pinjam_update);
    if ($stmt_pinjam_update === false) {
        throw new Exception("Prepare failed on data_pinjam UPDATE: " . $conn->error);
    }
    // Corrected bind_param: added 's' for kategori_buku
    $stmt_pinjam_update->bind_param("sssssissss", $judul_buku, $isbn, $nama_penulis, $nama_penerbit, $kategori_buku, $jumlah_halaman, $foto, $tanggal_pinjam, $tanggal_pengembalian, $original_id_buku);

    if (!$stmt_pinjam_update->execute()) {
        throw new Exception("Error updating data_pinjam: " . $stmt_pinjam_update->error);
    }
    $stmt_pinjam_update->close();
    $message = "Data peminjaman buku berhasil diperbarui.";

    // Check data_pengembalian for an active borrowing (belum dikembalikan)
    $check_pengembalian_sql = "SELECT COUNT(*) AS count FROM data_pengembalian WHERE id_buku = ? AND status_pengembalian = 'belum dikembalikan'";
    $stmt_check_pengembalian = $conn->prepare($check_pengembalian_sql);
    if ($stmt_check_pengembalian === false) {
        throw new Exception("Prepare failed on data_pengembalian check: " . $conn->error);
    }
    $stmt_check_pengembalian->bind_param("s", $original_id_buku);
    $stmt_check_pengembalian->execute();
    $result_check_pengembalian = $stmt_check_pengembalian->get_result();
    $row_check_pengembalian = $result_check_pengembalian->fetch_assoc();
    $book_in_pengembalian_active_exists = $row_check_pengembalian['count'] > 0;
    $stmt_check_pengembalian->close();

    $status_for_pengembalian_table = 'belum dikembalikan';

    if ($book_in_pengembalian_active_exists) {
        // If an active 'belum dikembalikan' record exists, update it
        $sql_pengembalian_update = "UPDATE data_pengembalian SET
                                         judul_buku = ?,
                                         tanggal_pinjam = ?,
                                         tanggal_pengembalian = ?
                                     WHERE id_buku = ? AND status_pengembalian = 'belum dikembalikan'";
        $stmt_pengembalian_update = $conn->prepare($sql_pengembalian_update);
        if ($stmt_pengembalian_update === false) {
            throw new Exception("Prepare failed on data_pengembalian UPDATE: " . $conn->error);
        }
        $stmt_pengembalian_update->bind_param("ssss", $judul_buku, $tanggal_pinjam, $tanggal_pengembalian, $original_id_buku);
        if (!$stmt_pengembalian_update->execute()) {
            throw new Exception("Error updating data_pengembalian: " . $stmt_pengembalian_update->error);
        }
        $stmt_pengembalian_update->close();
    } else {
        // If no active 'belum dikembalikan' record exists, insert a new one
        $sql_pengembalian_insert = "INSERT INTO data_pengembalian (id_buku, judul_buku, tanggal_pinjam, tanggal_pengembalian, status_pengembalian) VALUES (?, ?, ?, ?, ?)";
        $stmt_pengembalian_insert = $conn->prepare($sql_pengembalian_insert);
        if ($stmt_pengembalian_insert === false) {
            throw new Exception("Prepare failed on data_pengembalian INSERT: " . $conn->error);
        }
        $stmt_pengembalian_insert->bind_param("sssss", $original_id_buku, $judul_buku, $tanggal_pinjam, $tanggal_pengembalian, $status_for_pengembalian_table);
        if (!$stmt_pengembalian_insert->execute()) {
            throw new Exception("Error inserting into data_pengembalian: " . $stmt_pengembalian_insert->error);
        }
        $stmt_pengembalian_insert->close();
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