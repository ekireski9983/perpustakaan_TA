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

// Check if an 'id' is provided in the URL (from the delete button's onclick)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_buku_to_delete = $conn->real_escape_string($_GET['id']);

    // Start a transaction to ensure both deletions succeed or fail together
    $conn->begin_transaction();

    try {
        // Delete from data_pinjam table
        $stmt_pinjam = $conn->prepare("DELETE FROM data_pinjam WHERE id_buku = ?");
        $stmt_pinjam->bind_param("s", $id_buku_to_delete); // 's' denotes string type for id_buku
        $stmt_pinjam->execute();
        $stmt_pinjam->close();

        // Delete from data_pengembalian table
        // Assuming id_buku is the foreign key or unique identifier in data_pengembalian
        $stmt_pengembalian = $conn->prepare("DELETE FROM data_pengembalian WHERE id_buku = ?");
        $stmt_pengembalian->bind_param("s", $id_buku_to_delete);
        $stmt_pengembalian->execute();
        $stmt_pengembalian->close();

        // If both deletions are successful, commit the transaction
        $conn->commit();
        // Redirect back to the peminjaman_buku page with a success message
        header("Location: peminjaman_buku.php?status=success&message=Data peminjaman dan pengembalian buku berhasil dihapus!");
        exit();

    } catch (mysqli_sql_exception $exception) {
        // If any error occurs, rollback the transaction
        $conn->rollback();
        // Redirect back with an error message
        header("Location: peminjaman_buku.php?status=error&message=Gagal menghapus data: " . $exception->getMessage());
        exit();
    }
} else {
    // If no 'id' is provided, redirect with an error
    header("Location: peminjaman_buku.php?status=error&message=ID Buku tidak ditemukan.");
    exit();
}

$conn->close();
?>