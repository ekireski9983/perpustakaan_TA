<?php
session_start(); 

$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "perpustakaan"; 


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_POST['id_buku']) && !empty($_POST['id_buku'])) {
    $id_buku = $conn->real_escape_string($_POST['id_buku']);

    
    
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
        $jumlah_halaman = $book_details['jumlah_halaman']; 
        
        $tanggal_pinjam = "0000-00-00"; 
        $tanggal_pengembalian = "0000-00-00"; 

        
        $stmt_insert = $conn->prepare("INSERT INTO data_pinjam (id_buku, judul_buku, isbn, nama_penulis, nama_penerbit, foto, jumlah_halaman, tanggal_pinjam, tanggal_pengembalian) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
       
        $stmt_insert->bind_param("ssssssiss", $id_buku, $judul_buku, $isbn, $nama_penulis, $nama_penerbit, $foto, $jumlah_halaman, $tanggal_pinjam, $tanggal_pengembalian); 

        if ($stmt_insert->execute()) {
            
            header("Location: katalog_buku.php?status=success&message=Buku telah disimpan!");
            exit();
        } else {
            
            header("Location: katalog_buku.php?status=error&message=Gagal menyimpan buku: " . $stmt_insert->error);
            exit();
        }
        $stmt_insert->close();

    } else {
        
        header("Location: katalog_buku.php?status=error&message=Detail buku tidak ditemukan.");
        exit();
    }
    $stmt_fetch->close();

} else {
    
    header("Location: katalog_buku.php?status=error&message=ID Buku tidak ditemukan.");
    exit();
}

$conn->close();
?>