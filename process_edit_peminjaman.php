<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = "";    // Replace with your database password
$dbname = "perpustakaan";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from the form
$original_id_buku = $_POST['original_id_buku']; 
$judul_buku = $_POST['judul_buku'];
$isbn = $_POST['isbn'];
$nama_penulis = $_POST['nama_penulis'];
$nama_penerbit = $_POST['nama_penerbit'];
$jumlah_halaman = $_POST['jumlah_halaman'];
$foto = $_POST['foto'];
$tanggal_pinjam = $_POST['tanggal_pinjam'];
$tanggal_pengembalian = $_POST['tanggal_pengembalian'];

// Initialize status and message for the overall operation result
$status = 'error';
$message = 'Terjadi kesalahan tidak terduga.';

// Start a transaction for atomicity
$conn->begin_transaction();

try {
    // 1. Update the existing record in data_pinjam
    $sql_pinjam_update = "UPDATE data_pinjam SET
                            judul_buku = ?,
                            isbn = ?,
                            nama_penulis = ?,
                            nama_penerbit = ?,
                            jumlah_halaman = ?,
                            foto = ?,
                            tanggal_pinjam = ?,
                            tanggal_pengembalian = ?
                          WHERE id_buku = ?"; // Use original_id_buku to ensure correct record is updated

    $stmt_pinjam_update = $conn->prepare($sql_pinjam_update);
    if ($stmt_pinjam_update === false) {
        throw new Exception("Prepare failed on data_pinjam UPDATE: " . $conn->error);
    }
    $stmt_pinjam_update->bind_param("ssssissss", $judul_buku, $isbn, $nama_penulis, $nama_penerbit, $jumlah_halaman, $foto, $tanggal_pinjam, $tanggal_pengembalian, $original_id_buku);

    if (!$stmt_pinjam_update->execute()) {
        throw new Exception("Error updating data_pinjam: " . $stmt_pinjam_update->error);
    }
    $stmt_pinjam_update->close();
    $message = "Data peminjaman buku berhasil diperbarui.";



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
        // Update the existing 'belum dikembalikan' record in data_pengembalian
        $sql_pengembalian_update = "UPDATE data_pengembalian SET
                                            judul_buku = ?,
                                            tanggal_pinjam = ?,
                                            tanggal_pengembalian = ?
                                        WHERE id_buku = ? AND status_pengembalian = 'belum dikembalikan'"; // Keep WHERE clause for active status
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
        // If no active 'belum dikembalikan' record exists, insert a new one.
        // This ensures data_pengembalian reflects the current borrow state.
        $sql_pengembalian_insert = "INSERT INTO data_pengembalian (id_buku, judul_buku, tanggal_pinjam, tanggal_pengembalian, status_pengembalian) VALUES (?, ?, ?, ?, ?)";
        $stmt_pengembalian_insert = $conn->prepare($sql_pengembalian_insert);
        if ($stmt_pengembalian_insert === false) {
            throw new Exception("Prepare failed on data_pengembalian INSERT: " . $conn->error);
        }
        $stmt_pengembalian_insert->bind_param("sssss", $original_id_buku, $judul_buku, $tanggal_pinjam, $tanggal_pengembalian, $status_for_pengembalian_table); // Use the correct variable
        if (!$stmt_pengembalian_insert->execute()) {
            throw new Exception("Error inserting into data_pengembalian: " . $stmt_pengembalian_insert->error);
        }
        $stmt_pengembalian_insert->close();
    }

    // --- END REVISED LOGIC FOR data_pengembalian ---

    // Commit the transaction if all queries were successful
    $conn->commit();
    $status = 'success'; // This 'status' is for the redirection message

} catch (Exception $e) {
    // Rollback the transaction if any query failed
    $conn->rollback();
    $status = 'error'; // This 'status' is for the redirection message
    $message = $e->getMessage();
}

$conn->close();

// Redirect back to the peminjaman_buku.php page with status message
header("Location: peminjaman_buku.php?status=$status&message=" . urlencode($message));
exit();
?>