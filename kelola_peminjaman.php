<?php

$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Initialize start and end dates for the report
$startDate = isset($_GET['start_date']) ? mysqli_real_escape_string($koneksi, $_GET['start_date']) : '';
$endDate = isset($_GET['end_date']) ? mysqli_real_escape_string($koneksi, $_GET['end_date']) : '';

// Default query to fetch all data or apply date filter if dates are provided
$query = "SELECT * FROM data_pinjam";
if (!empty($startDate) && !empty($endDate)) {
    $query .= " WHERE tanggal_pinjam BETWEEN '$startDate' AND '$endDate'";
}

$result = mysqli_query($koneksi, $query);

// Query to count total borrowed books (this will be removed from display)
$countQuery = "SELECT COUNT(*) AS total_borrowed_books FROM data_pinjam";
if (!empty($startDate) && !empty($endDate)) {
    $countQuery .= " WHERE tanggal_pinjam BETWEEN '$startDate' AND '$endDate'";
}
$countResult = mysqli_query($koneksi, $countQuery);
$totalBorrowedBooks = 0;
if ($countResult && mysqli_num_rows($countResult) > 0) {
    $countRow = mysqli_fetch_assoc($countResult);
    $totalBorrowedBooks = $countRow['total_borrowed_books'];
}

// New: Query to fetch book categories and their counts from data_buku
$categoryQuery = "SELECT kategori_buku, COUNT(*) AS book_count FROM data_buku GROUP BY kategori_buku ORDER BY kategori_buku ASC";
$categoryResult = mysqli_query($koneksi, $categoryQuery);
$categories = [];
if ($categoryResult) {
    while ($row = mysqli_fetch_assoc($categoryResult)) {
        $categories[] = $row;
    }
}

// Query to fetch all books from data_buku for the combo box
$booksQuery = "SELECT id_buku, judul_buku FROM data_buku ORDER BY judul_buku ASC";
$booksResult = mysqli_query($koneksi, $booksQuery);
$books = [];
if ($booksResult) {
    while ($row = mysqli_fetch_assoc($booksResult)) {
        $books[] = $row;
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $id_buku = mysqli_real_escape_string($koneksi, $_POST['id_buku']);

    // Fetch book details from data_buku based on the selected id_buku
    // Now including the 'foto' field
    $bookDetailsQuery = "SELECT isbn, judul_buku, nama_penulis, nama_penerbit, jumlah_halaman, kategori_buku, foto FROM data_buku WHERE id_buku = '$id_buku'";
    $bookDetailsResult = mysqli_query($koneksi, $bookDetailsQuery);

    if ($bookDetailsResult && mysqli_num_rows($bookDetailsResult) > 0) {
        $bookDetails = mysqli_fetch_assoc($bookDetailsResult);

        $isbn = $bookDetails['isbn'];
        $judul_buku = $bookDetails['judul_buku'];
        $nama_penulis = $bookDetails['nama_penulis'];
        $nama_penerbit = $bookDetails['nama_penerbit'];
        $jumlah_halaman = $bookDetails['jumlah_halaman'];
        $kategori_buku = $bookDetails['kategori_buku'];
        $foto_destination = $bookDetails['foto']; // Use the photo path directly from data_buku

        $tanggal_pinjam = mysqli_real_escape_string($koneksi, $_POST['tanggal_pinjam']);
        $tanggal_pengembalian = mysqli_real_escape_string($koneksi, $_POST['tanggal_pengembalian']);

        $tanggal_ditambahkan = date('Y-m-d');

        // No file upload needed now, as photo is taken from data_buku
        // Remove file upload logic here
        /*
        $foto = $_FILES['foto'];
        $foto_name = $foto['name'];
        $foto_tmp = $foto['tmp_name'];
        $foto_size = $foto['size'];
        $foto_error = $foto['error'];

        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $foto_ext = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));

        if (in_array($foto_ext, $allowed_extensions) && $foto_error === 0) {
            $foto_destination = 'upload/' . uniqid('', true) . '.' . $foto_ext;

            if (move_uploaded_file($foto_tmp, $foto_destination)) {
        */
                mysqli_begin_transaction($koneksi);

                // Check if the book already exists in data_list_buku (now data_buku, for consistency if needed)
                // This part might need adjustment depending on whether data_buku is the *only* source
                // or if data_list_buku still serves a purpose. For now, assuming data_buku is the master.
                $check_buku_query = "SELECT COUNT(*) AS count FROM data_buku WHERE id_buku = '$id_buku'";
                $check_buku_result = mysqli_query($koneksi, $check_buku_query);
                $buku_exists = false;
                if ($check_buku_result) {
                    $row_count = mysqli_fetch_assoc($check_buku_result);
                    if ($row_count['count'] > 0) {
                        $buku_exists = true;
                    }
                }

                $insert_pinjam_query = "INSERT INTO data_pinjam (id_buku, isbn, judul_buku, nama_penulis, nama_penerbit, jumlah_halaman, kategori_buku, foto, tanggal_pinjam, tanggal_pengembalian)
                                            VALUES ('$id_buku', '$isbn', '$judul_buku', '$nama_penulis', '$nama_penerbit', '$jumlah_halaman', '$kategori_buku', '$foto_destination', '$tanggal_pinjam', '$tanggal_pengembalian')";

                if (mysqli_query($koneksi, $insert_pinjam_query)) {
                    $insert_pengembalian_query = "INSERT INTO data_pengembalian (id_buku, judul_buku, tanggal_pinjam, tanggal_pengembalian, status_pengembalian)
                                                    VALUES ('$id_buku', '$judul_buku', '$tanggal_pinjam', '$tanggal_pengembalian', 'belum dikembalikan')";

                    if (mysqli_query($koneksi, $insert_pengembalian_query)) {
                        // If the book details are coming from data_buku, we don't need to insert into data_buku again.
                        // However, if you have data_list_buku, you might still want to insert there if it's a separate "available books" list.
                        // Based on your request, we are moving fully to data_buku as the source.
                        // So, no insert into data_list_buku or data_buku here.
                        mysqli_commit($koneksi);
                        header("Location: kelola_peminjaman.php");
                        exit();
                    } else {
                        mysqli_rollback($koneksi);
                        echo "Error inserting into data_pengembalian: " . mysqli_error($koneksi);
                    }
                } else {
                    mysqli_rollback($koneksi);
                    echo "Error inserting into data_pinjam: " . mysqli_error($koneksi);
                }
        /*
            } else {
                echo "Error: Gagal memindahkan file yang diunggah.";
            }
        } else {
            echo "Format file tidak valid atau terjadi kesalahan saat upload. Error code: " . $foto_error;
        }
        */
    } else {
        echo "Error: Book ID not found in data_buku.";
    }
}


if (isset($_GET['id'])) {
    $id_buku = mysqli_real_escape_string($koneksi, $_GET['id']);

    mysqli_begin_transaction($koneksi);

    // When deleting a loan, we should NOT delete the photo from the server
    // as it might be associated with the original book in data_buku.
    // The photo path is just copied from data_buku to data_pinjam.
    // So, remove the unlink($photo_path) part.
    /*
    $get_photo_query = "SELECT foto FROM data_pinjam WHERE id_buku='$id_buku'";
    $photo_result = mysqli_query($koneksi, $get_photo_query);
    if ($photo_result && mysqli_num_rows($photo_result) > 0) {
        $row = mysqli_fetch_assoc($photo_result);
        $photo_path = $row['foto'];
        if (file_exists($photo_path)) {
            unlink($photo_path);
        }
    }
    */

    $delete_pengembalian_query = "DELETE FROM data_pengembalian WHERE id_buku='$id_buku'";
    if (mysqli_query($koneksi, $delete_pengembalian_query)) {
        // IMPORTANT: We should NOT delete from data_buku when a loan is deleted.
        // data_buku is your master list of all books.
        // Only delete from data_pinjam and data_pengembalian for a loan.
        // I am commenting out the deletion from data_list_buku (or data_buku now).
        /*
        $delete_list_buku_query = "DELETE FROM data_list_buku WHERE id_buku='$id_buku'";
        if (mysqli_query($koneksi, $delete_list_buku_query)) {
        */
            $delete_pinjam_query = "DELETE FROM data_pinjam WHERE id_buku='$id_buku'";
            if (mysqli_query($koneksi, $delete_pinjam_query)) {
                mysqli_commit($koneksi);
                header("Location: kelola_peminjaman.php");
                exit();
            } else {
                mysqli_rollback($koneksi);
                echo "Error deleting from data_pinjam: " . mysqli_error($koneksi);
            }
        /*
        } else {
            mysqli_rollback($koneksi);
            echo "Error deleting from data_list_buku: " . mysqli_error($koneksi);
        }
        */
    } else {
        mysqli_rollback($koneksi);
        echo "Error deleting from data_pengembalian: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kelola Peminjaman Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { background-color: #f1f5f9; }
        .sidebar { background-color: #2f3e46; color: white; }
        .sidebar a { display: block; color: white; padding: 10px 20px; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background-color: #00b4d8; border-radius: 5px; }
        .sidebar .logout { position: absolute; bottom: 20px; width: 100%; }
        .sidebar .image-box img { width: 80px; opacity: 0.7; }
        .main-content { padding: 40px; }
        .table thead { background-color: #f8f9fa; }
        .btn-edit { background-color: #48cae4; color: white; }
        .btn-delete { background-color: #f94144; color: white; }
        .btn-tambah { background-color: #00b4d8; color: white; }
        .category-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            flex: 1 1 calc(33% - 20px); /* Adjust for 3 columns with gap */
            min-width: 200px;
            max-width: 300px;
        }
        .category-card h5 {
            color: #2f3e46;
            margin-bottom: 10px;
        }
        .category-card p {
            font-size: 1.5rem;
            font-weight: bold;
            color: #00b4d8;
        }
        @media (max-width: 768px) {
            .main-content { padding: 20px; }
            .category-card {
                flex: 1 1 calc(50% - 20px); /* 2 columns on smaller screens */
            }
        }
        @media (max-width: 576px) {
            .category-card {
                flex: 1 1 100%; /* 1 column on extra small screens */
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row d-md-none bg-dark text-white p-2">
        <div class="col">
            <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">☰ Menu</button>
            <span class="ms-3">Kelola Peminjaman Buku</span>
        </div>
    </div>

    <div class="row">
        <nav class="col-md-3 d-none d-md-block sidebar min-vh-100 position-relative">
            <h5 class="pt-4">Pustakawan<br /><small>admin</small></h5>
            <a href="kelola_pustakawan.php">Kelola Pustakawan</a>
            <a href="kelola_anggota.php">Kelola Anggota</a>
            <a href="kelola_katalog.php">Kelola Katalog Buku</a>
            <a href="kelola_list_buku.php">Kelola list buku</a>
            <a href="kelola_peminjaman.php">Kelola Peminjaman Buku</a>
            <a href="kelola_pengembalian.php">Kelola Pengembalian Buku</a>
            <a href="kelola_histori.php">Kelola histori pengembalian</a>
            <a href="logout.php" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
            <div class="image-box text-center mt-5">
                <img src="assets/logo_sekolah.png" alt="icon" />
            </div>
        </nav>

        <div class="offcanvas offcanvas-start sidebar text-white" tabindex="-1" id="sidebarMenu">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Pustakawan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <a href="kelola_pustakawan.php">Kelola Pustakawan</a>
                <a href="kelola_anggota.php">kelola anggota</a>
                <a href="kelola_katalog.php">kelola katalog buku</a>
                <a href="kelola_list_buku.php">Kelola list buku</a>
                <a href="kelola_peminjaman.php">kelola Peminjaman buku</a>
                <a href="kelola_pengembalian.php">kelola Pengembalian buku</a>
                <a href="kelola_histori.php">kelola histori pengembalian</a>
                <a href="logout.php" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
                <div class="image-box text-center mt-5">
                    <img src="assets/logo_sekolah.png" alt="icon" />
                </div>
            </div>
        </div>

        <main class="col-md-9 col-12 main-content">
            <h4>Kelola Peminjaman Buku</h4>

            kategori
            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-3 mb-4">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <div class="category-card">
                            <h5><?php echo htmlspecialchars($category['kategori_buku']); ?></h5>
                            <p><?php echo htmlspecialchars($category['book_count']); ?></p>
                            <small>buku</small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning w-100 text-center" role="alert">
                        Tidak ada kategori buku ditemukan.
                    </div>
                <?php endif; ?>
            </div>
            ---

            <div class="d-flex flex-wrap gap-2 align-items-center mb-3 mt-3">
                <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="mencari peminjam" style="max-width: 300px;" oninput="filterTable()" />
                <button class="btn btn-tambah btn-md" data-bs-toggle="modal" data-bs-target="#tambahPeminjamanModal">Tambah Peminjaman</button>
                <a href="export_peminjaman.php?start_date=<?php echo htmlspecialchars($startDate); ?>&end_date=<?php echo htmlspecialchars($endDate); ?>" class="btn btn-success ms-2">laporan peminjaman</a>
                <form class="d-flex align-items-center" method="GET" action="">
                    <label for="start_date" class="form-label me-2 mb-0">dari tanggal :</label>
                    <input type="date" class="form-control me-2" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>" style="max-width: 180px;">
                    <label for="end_date" class="form-label me-2 mb-0">sampai tanggal:</label>
                    <input type="date" class="form-control me-2" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>" style="max-width: 180px;">
                    <button type="submit" class="btn btn-success ms-2">tampilkan</button>
                    <?php if (!empty($startDate) || !empty($endDate)) : ?>
                        <a href="kelola_peminjaman.php" class="btn btn-secondary ms-2">hapus</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="PeminjamanTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Buku</th>
                            <th>ISBN</th>
                            <th>Judul Buku</th>
                            <th>Nama Penulis</th>
                            <th>Nama Penerbit</th>
                            <th>Jumlah Halaman</th>
                            <th>Kategori Buku</th>
                            <th>Foto buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Pengembalian</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['id_buku']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['isbn']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['judul_buku']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_penulis']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_penerbit']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['jumlah_halaman']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['kategori_buku']) . "</td>";
                            echo "<td><img src='" . htmlspecialchars($row['foto']) . "' alt='Foto Buku' style='width: 50px; height: auto;'></td>";
                            echo "<td>" . htmlspecialchars($row['tanggal_pinjam']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['tanggal_pengembalian']) . "</td>";
                            echo '<td>
                                        <button class="btn btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#hapusPeminjamanModal" data-id="' . htmlspecialchars($row['id_buku']) . '">Delete</button>
                                    </td>';
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<div class="modal fade" id="tambahPeminjamanModal" tabindex="-1" aria-labelledby="tambahPeminjamanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahPeminjamanModalLabel">Tambah Peminjaman Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formTambahPeminjaman" method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="tambah">
                    <div class="mb-3">
                        <label for="id_buku" class="form-label">ID Buku</label>
                        <select class="form-select" id="id_buku" name="id_buku" required>
                            <option value="">Pilih ID Buku</option>
                            <?php foreach ($books as $book): ?>
                                <option value="<?php echo htmlspecialchars($book['id_buku']); ?>"><?php echo htmlspecialchars($book['id_buku'] . " - " . $book['judul_buku']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="isbn" class="form-label">ISBN</label>
                        <input type="text" class="form-control" id="isbn" name="isbn" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="judul_buku" class="form-label">Judul Buku</label>
                        <input type="text" class="form-control" id="judul_buku" name="judul_buku" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_penulis" class="form-label">Nama Penulis</label>
                        <input type="text" class="form-control" id="nama_penulis" name="nama_penulis" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_penerbit" class="form-label">Nama Penerbit</label>
                        <input type="text" class="form-control" id="nama_penerbit" name="nama_penerbit" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_halaman" class="form-label">Jumlah Halaman</label>
                        <input type="number" class="form-control" id="jumlah_halaman" name="jumlah_halaman" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="kategori_buku" class="form-label">Kategori Buku</label>
                        <input type="text" class="form-control" id="kategori_buku" name="kategori_buku" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="current_foto_display" class="form-label">Foto Buku</label>
                        <div id="current_foto_display">
                            No image selected.
                        </div>
                        <input type="hidden" id="foto_path" name="foto_path"> </div>
                    <div class="mb-3">
                        <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                        <input type="date" class="form-control" name="tanggal_pinjam" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_pengembalian" class="form-label">Tanggal Pengembalian</label>
                        <input type="date" class="form-control" name="tanggal_pengembalian" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah Peminjaman</button>
                    <button type="reset" class="btn btn-danger">Reset</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="hapusPeminjamanModal" tabindex="-1" aria-labelledby="hapusPeminjamanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hapusPeminjamanModalLabel">Hapus Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin mau hapus</p>
            </div>
            <div class="modal-footer d-flex justify-content-center gap-2">
                <form id="formHapusPeminjaman" method="GET" action="">
                    <input type="hidden" id="hapusId" name="id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin logout?
            </div>
            <div class="modal-footer d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                <a href="logout.php" class="btn btn-danger">Ya</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const deleteButtons = document.querySelectorAll('[data-bs-target="#hapusPeminjamanModal"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            document.getElementById('hapusId').value = id;
        });
    });

    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('PeminjamanTable');
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            const td = tr[i].getElementsByTagName('td');
            let found = false;

            const judulBukuCol = td[3];
            const namaPenulisCol = td[4];
            const namaPenerbitCol = td[5];
            const kategoriBukuCol = td[6];

            if (judulBukuCol && judulBukuCol.textContent.toLowerCase().indexOf(filter) > -1) {
                found = true;
            } else if (namaPenulisCol && namaPenulisCol.textContent.toLowerCase().indexOf(filter) > -1) {
                found = true;
            } else if (namaPenerbitCol && namaPenerbitCol.textContent.toLowerCase().indexOf(filter) > -1) {
                found = true;
            } else if (kategoriBukuCol && kategoriBukuCol.textContent.toLowerCase().indexOf(filter) > -1) {
                found = true;
            }

            tr[i].style.display = found ? "" : "none";
        }
    }

    // JavaScript to populate form fields and display image based on selected book ID
    document.getElementById('id_buku').addEventListener('change', function() {
        const selectedBookId = this.value;
        const currentFotoDisplay = document.getElementById('current_foto_display');
        const fotoPathInput = document.getElementById('foto_path');

        if (selectedBookId) {
            // Fetch book details using AJAX
            fetch('get_book_details.php?id_buku=' + selectedBookId)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('isbn').value = data.isbn;
                        document.getElementById('judul_buku').value = data.judul_buku;
                        document.getElementById('nama_penulis').value = data.nama_penulis;
                        document.getElementById('nama_penerbit').value = data.nama_penerbit;
                        document.getElementById('jumlah_halaman').value = data.jumlah_halaman;
                        document.getElementById('kategori_buku').value = data.kategori_buku;

                        // Display the image and set the hidden input value
                        if (data.foto) {
                            currentFotoDisplay.innerHTML = `<img src="${data.foto}" alt="Foto Buku" style="width: 100px; height: auto;">`;
                            fotoPathInput.value = data.foto;
                        } else {
                            currentFotoDisplay.innerHTML = 'No image available.';
                            fotoPathInput.value = '';
                        }
                    } else {
                        // Clear fields and image if book not found
                        document.getElementById('isbn').value = '';
                        document.getElementById('judul_buku').value = '';
                        document.getElementById('nama_penulis').value = '';
                        document.getElementById('nama_penerbit').value = '';
                        document.getElementById('jumlah_halaman').value = '';
                        document.getElementById('kategori_buku').value = '';
                        currentFotoDisplay.innerHTML = 'No image selected.';
                        fotoPathInput.value = '';
                    }
                })
                .catch(error => console.error('Error fetching book details:', error));
        } else {
            // Clear fields and image if no book is selected
            document.getElementById('isbn').value = '';
            document.getElementById('judul_buku').value = '';
            document.getElementById('nama_penulis').value = '';
            document.getElementById('nama_penerbit').value = '';
            document.getElementById('jumlah_halaman').value = '';
            document.getElementById('kategori_buku').value = '';
            currentFotoDisplay.innerHTML = 'No image selected.';
            fotoPathInput.value = '';
        }
    });
</script>
</body>
</html>