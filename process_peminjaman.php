<?php
session_start(); // Start session (though id_anggota isn't used in data_pinjam now, keep for general session use)

// Database connection details
$servername = "localhost"; // Your database server
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "perpustakaan"; // IMPORTANT: Change this to your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if id_buku is received via POST
if (isset($_POST['id_buku']) && !empty($_POST['id_buku'])) {
    $id_buku = $conn->real_escape_string($_POST['id_buku']);

    // --- Step 1: Fetch book details from data_buku table ---
    // Added jumlah_halaman to the SELECT query
    $sql_fetch_book = "SELECT judul_buku, isbn, nama_penulis, nama_penerbit, foto, jumlah_halaman FROM data_buku WHERE id_buku = ?";
    $stmt_fetch = $conn->prepare($sql_fetch_book);
    $stmt_fetch->bind_param("s", $id_buku);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();

    if ($result_fetch->num_rows > 0) {
        $book_details = $result_fetch->fetch_assoc();

        $judul_buku = $conn->real_escape_string($book_details['judul_buku']);
        $isbn = $conn->real_escape_string($book_details['isbn']);
        $nama_penulis = $conn->real_escape_string($book_details['nama_penulis']);
        $nama_penerbit = $conn->real_escape_string($book_details['nama_penerbit']);
        $foto = $conn->real_escape_string($book_details['foto']);
        $jumlah_halaman = $book_details['jumlah_halaman']; // Fetch jumlah_halaman

        // Set tanggal_pinjam and tanggal_pengembalian to '0000-00-00'
        $tanggal_pinjam = "0000-00-00"; // Changed to default date string
        $tanggal_pengembalian = "0000-00-00"; // Changed to default date string

        // --- Step 2: Insert into data_pinjam table ---
        // Added jumlah_halaman to the INSERT statement
        $stmt_insert = $conn->prepare("INSERT INTO data_pinjam (id_buku, judul_buku, isbn, nama_penulis, nama_penerbit, foto, jumlah_halaman, tanggal_pinjam, tanggal_pengembalian) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // Updated bind_param: 's' for string type for jumlah_halaman (if stored as string), or 'i' for integer.
        // Also 's' for the new '0000-00-00' date strings.
        // Assuming jumlah_halaman is an integer, so 'i' is used.
        $stmt_insert->bind_param("ssssssiss", $id_buku, $judul_buku, $isbn, $nama_penulis, $nama_penerbit, $foto, $jumlah_halaman, $tanggal_pinjam, $tanggal_pengembalian); 

        if ($stmt_insert->execute()) {
            // Successfully borrowed the book
            header("Location: katalog_buku.php?status=success&message=Buku telah disimpan!");
            exit();
        } else {
            // Error in borrowing
            header("Location: katalog_buku.php?status=error&message=Gagal menyimpan buku: " . $stmt_insert->error);
            exit();
        }
        $stmt_insert->close();

    } else {
        // Book ID not found in data_buku
        header("Location: katalog_buku.php?status=error&message=Detail buku tidak ditemukan.");
        exit();
    }
    $stmt_fetch->close();

} else {
    // id_buku not provided in the POST request
    header("Location: katalog_buku.php?status=error&message=ID Buku tidak ditemukan.");
    exit();
}

$conn->close();
?>