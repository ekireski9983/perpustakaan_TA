<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "perpustakaan";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Terjadi kesalahan koneksi ke database. Silakan coba lagi nanti.");
}

// FIX: Ensure 'kategori_buku' is selected from the database
$sql = "SELECT id_buku, judul_buku, isbn, nama_penulis, nama_penerbit, jumlah_halaman, foto, tanggal_pinjam, tanggal_pengembalian, kategori_buku FROM data_pinjam";
$result = $conn->query($sql);

$books = [];
if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
    }
    $result->free();
} else {
    error_log("Error fetching data: " . $conn->error);
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f1f5f9;
            margin: 0;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

        .sidebar {
            background-color: #2f3e46;
            color: white;
            padding-top: 20px;
            position: sticky;
            top: 0;
            align-self: flex-start;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar h5 {
            margin-left: 20px;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #00b4d8;
            border-radius: 5px;
        }

        .sidebar .logout {
            position: absolute;
            bottom: 20px;
            width: 100%;
            padding-left: 20px;
        }

        .image-box img {
            width: 80px;
            opacity: 0.7;
        }

        .main-wrapper {
            display: flex;
            flex: 1;
        }

        .main-content {
            padding: 40px;
            flex-grow: 1;
        }

        .book-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .book-card .col-md-4 {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        .book-card img {
            width: 100%;
            max-width: 180px;
            height: auto;
            object-fit: contain;
            border-radius: var(--bs-border-radius);
        }

        .book-card .card-body {
            padding: 20px;
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                padding-top: 0;
                position: static;
                height: auto;
                overflow-y: visible;
            }
            .sidebar h5 {
                margin-left: 0;
                text-align: center;
            }
            .sidebar a {
                padding: 10px 15px;
                text-align: center;
            }
            .sidebar .logout {
                position: static;
                margin-top: 30px;
                padding-left: 0;
                text-align: center;
            }
            .book-card .col-md-4 {
                padding-bottom: 0;
            }
            .book-card img {
                max-width: 100%;
                max-height: 250px;
            }
            .main-wrapper {
                flex-direction: column;
            }
            .main-content {
                max-height: unset;
                overflow-y: visible;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row d-md-none bg-dark text-white p-2 sticky-top">
            <div class="col d-flex align-items-center">
                <button class="btn btn-outline-light me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <span class="navbar-toggler-icon"></span> Menu
                </button>
                <span class="fs-5">Dashboard Siswa</span>
            </div>
        </div>

        <div class="main-wrapper">
            <nav class="col-md-3 d-none d-md-block sidebar">
                <h5 class="pt-4">Siswa<br /><small>user</small></h5>
                <a href="lihat_anggota.php">Lihat Anggota</a>
                <a href="katalog_buku.php">Katalog Buku</a>
                <a href="peminjaman_buku.php">Peminjaman Buku</a>
                <a href="pengembalian_buku.php">Pengembalian Buku</a>
                <a href="histori_pengembalian.php">Histori Pengembalian</a>
                <a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
                <div class="image-box text-center mt-5">
                    <img src="assets/logo_sekolah.png" alt="icon" />
                </div>
            </nav>

            <div class="offcanvas offcanvas-start sidebar" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="sidebarMenuLabel">Menu</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <h5 class="pt-4">Siswa<br /><small>user</small></h5>
                    <a href="lihat_anggota.php">Lihat Anggota</a>
                    <a href="katalog_buku.php">Katalog Buku</a>
                    <a href="peminjaman_buku.php">Peminjaman Buku</a>
                    <a href="pengembalian_buku.php">Pengembalian Buku</a>
                    <a href="histori_pengembalian.php">Histori Pengembalian</a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
                    <div class="image-box text-center mt-5">
                        <img src="assets/logo_sekolah.png" alt="icon" />
                    </div>
                </div>
            </div>

            <main class="col-md-9 col-12 main-content">
                <h4>Peminjaman Buku</h4>

                <div class="input-group mb-3">
                    <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="Cari buku berdasarkan judul, ID, atau penulis..." style="max-width: 400px;" onkeyup="filterCards()" aria-label="Search Book" />
                </div>

                <div id="bookCardsContainer">
                    <?php if (!empty($books)): ?>
                        <?php foreach ($books as $book): ?>
                            <div class="card mb-4 book-card">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <img src="<?php echo !empty($book['foto']) ? htmlspecialchars($book['foto']) : 'assets/default_book.png'; ?>" class="img-fluid rounded-start" alt="Book Cover">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <h5 class="card-title book-id"><?php echo htmlspecialchars($book['id_buku']); ?></h5>
                                            <p class="card-text book-title"><strong>Judul Buku:</strong> <?php echo htmlspecialchars($book['judul_buku']); ?></p>
                                            <p class="card-text"><strong>ISBN:</strong> <?php echo htmlspecialchars($book['isbn']); ?></p>
                                            <p class="card-text book-author"><strong>Nama Penulis:</strong> <?php echo htmlspecialchars($book['nama_penulis']); ?></p>
                                            <p class="card-text"><strong>Nama Penerbit:</strong> <?php echo htmlspecialchars($book['nama_penerbit']); ?></p>
                                            <p class="card-text"><strong>Kategori Buku:</strong> <?php echo htmlspecialchars($book['kategori_buku'] ?? 'N/A'); ?></p>
                                            <p class="card-text"><strong>Jumlah Halaman:</strong> <?php echo htmlspecialchars($book['jumlah_halaman']); ?></p>
                                            <p class="card-text"><strong>Tanggal Pinjam:</strong> <?php echo htmlspecialchars($book['tanggal_pinjam'] ?? 'N/A'); ?></p>
                                            <p class="card-text"><strong>Tanggal Pengembalian:</strong> <?php echo htmlspecialchars($book['tanggal_pengembalian'] ?? 'N/A'); ?></p>

                                            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#borrowBookModal"
                                                 onclick="showBorrowModal(
                                                        '<?php echo htmlspecialchars($book['id_buku']); ?>',
                                                        '<?php echo htmlspecialchars($book['judul_buku']); ?>',
                                                        '<?php echo htmlspecialchars($book['isbn']); ?>',
                                                        '<?php echo htmlspecialchars($book['nama_penulis']); ?>',
                                                        '<?php echo htmlspecialchars($book['nama_penerbit']); ?>',
                                                        <?php echo htmlspecialchars($book['jumlah_halaman']); ?>,
                                                        '<?php echo htmlspecialchars($book['foto']); ?>',
                                                        '<?php echo htmlspecialchars($book['kategori_buku'] ?? ''); ?>' // Pass kategori_buku
                                                    )">Pinjam buku</button>

                                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#editBookModal"
                                                 onclick='showEditModal(<?php echo json_encode($book); ?>)'>Edit</button>

                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal"
                                                 onclick="setDeleteBookId('<?php echo htmlspecialchars($book['id_buku']); ?>')">Hapus</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info" role="alert">
                            Tidak ada buku yang sedang dipinjam.
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="borrowBookModal" tabindex="-1" aria-labelledby="borrowBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="borrowBookModalLabel">Peminjaman Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="process_borrow_book.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="borrowBookId" class="form-label">ID Buku</label>
                            <input type="text" class="form-control" id="borrowBookId" name="id_buku" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="borrowJudulBuku" class="form-label">Judul Buku</label>
                            <input type="text" class="form-control" id="borrowJudulBuku" name="judul_buku" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="borrowISBN" class="form-label">ISBN</label>
                            <input type="text" class="form-control" id="borrowISBN" name="isbn" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="borrowNamaPenulis" class="form-label">Nama Penulis</label>
                            <input type="text" class="form-control" id="borrowNamaPenulis" name="nama_penulis" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="borrowNamaPenerbit" class="form-label">Nama Penerbit</label>
                            <input type="text" class="form-control" id="borrowNamaPenerbit" name="nama_penerbit" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="borrowJumlahHalaman" class="form-label">Jumlah Halaman</label>
                            <input type="number" class="form-control" id="borrowJumlahHalaman" name="jumlah_halaman" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="borrowKategoriBuku" class="form-label">Kategori Buku</label>
                            <input type="text" class="form-control" id="borrowKategoriBuku" name="kategori_buku" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="borrowDate" class="form-label">Tanggal Pinjam</label>
                            <input type="date" class="form-control" id="borrowDate" name="tanggal_pinjam" required>
                        </div>
                        <div class="mb-3">
                            <label for="borrowReturnDate" class="form-label">Tanggal Pengembalian</label>
                            <input type="date" class="form-control" id="borrowReturnDate" name="tanggal_pengembalian" required>
                        </div>
                        <input type="hidden" id="borrowFoto" name="foto">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Pinjam buku</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBookModalLabel">Edit Peminjaman Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="process_edit_peminjaman.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_original_book_id" name="original_id_buku">
                        <div class="mb-3">
                            <label for="editBookId" class="form-label">ID Buku</label>
                            <input type="text" class="form-control" id="editBookId" name="id_buku" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editJudul" class="form-label">Judul Buku</label>
                            <input type="text" class="form-control" id="editJudul" name="judul_buku" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editISBN" class="form-label">ISBN</label>
                            <input type="text" class="form-control" id="editISBN" name="isbn" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editPenulis" class="form-label">Nama Penulis</label>
                            <input type="text" class="form-control" id="editPenulis" name="nama_penulis" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editPenerbit" class="form-label">Nama Penerbit</label>
                            <input type="text" class="form-control" id="editPenerbit" name="nama_penerbit" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editHalaman" class="form-label">Jumlah Halaman</label>
                            <input type="number" class="form-control" id="editHalaman" name="jumlah_halaman" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editKategoriBuku" class="form-label">Kategori Buku</label>
                            <input type="text" class="form-control" id="editKategoriBuku" name="kategori_buku" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editTanggalPinjam" class="form-label">Tanggal Pinjam</label>
                            <input type="date" class="form-control" id="editTanggalPinjam" name="tanggal_pinjam" required>
                        </div>
                        <div class="mb-3">
                            <label for="editTanggalPengembalian" class="form-label">Tanggal Pengembalian</label>
                            <input type="date" class="form-control" id="editTanggalPengembalian" name="tanggal_pengembalian" required>
                        </div>
                        <input type="hidden" id="editFoto" name="foto">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Edit pinjam</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Konfirmasi Hapus Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data ini?</p>
                </div>
                <div class="modal-footer d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Hapus</button>
                </div>
                <input type="hidden" id="deleteBookIdPlaceholder" value="">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterCards() {
            let input, filter, cards, card, bookTitle, bookAuthor, bookId, i;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            cards = document.getElementsByClassName("book-card");

            for (i = 0; i < cards.length; i++) {
                card = cards[i];
                bookTitle = card.querySelector(".book-title");
                bookAuthor = card.querySelector(".book-author");
                bookId = card.querySelector(".book-id");

                let match = false;

                if (bookTitle && (bookTitle.textContent || bookTitle.innerText).toUpperCase().indexOf(filter) > -1) {
                    match = true;
                }

                if (!match && bookAuthor && (bookAuthor.textContent || bookAuthor.innerText).toUpperCase().indexOf(filter) > -1) {
                    match = true;
                }

                if (!match && bookId) {
                    const idText = (bookId.textContent || bookId.innerText).trim();
                    if (idText.toUpperCase().indexOf(filter) > -1) {
                        match = true;
                    }
                }

                if (match) {
                    card.style.display = "";
                } else {
                    card.style.display = "none";
                }
            }
        }

        // MODIFIED: Added kategori_buku parameter
        function showBorrowModal(id_buku, judul_buku, isbn, nama_penulis, nama_penerbit, jumlah_halaman, foto, kategori_buku) {
            document.getElementById('borrowBookId').value = id_buku;
            document.getElementById('borrowJudulBuku').value = judul_buku;
            document.getElementById('borrowISBN').value = isbn;
            document.getElementById('borrowNamaPenulis').value = nama_penulis;
            document.getElementById('borrowNamaPenerbit').value = nama_penerbit;
            document.getElementById('borrowJumlahHalaman').value = jumlah_halaman;
            document.getElementById('borrowFoto').value = foto;
            // ADDED: Set value for kategori_buku
            document.getElementById('borrowKategoriBuku').value = kategori_buku;

            const today = new Date();
            const todayFormatted = today.toISOString().split('T')[0];
            document.getElementById('borrowDate').value = todayFormatted;

            const returnDate = new Date(today);
            returnDate.setDate(today.getDate() + 7);
            const returnDateFormatted = returnDate.toISOString().split('T')[0];
            document.getElementById('borrowReturnDate').value = returnDateFormatted;
        }

        function showEditModal(bookData) {
            document.getElementById('edit_original_book_id').value = bookData.id_buku;
            document.getElementById('editBookId').value = bookData.id_buku;
            document.getElementById('editJudul').value = bookData.judul_buku;
            document.getElementById('editISBN').value = bookData.isbn;
            document.getElementById('editPenulis').value = bookData.nama_penulis;
            document.getElementById('editPenerbit').value = bookData.nama_penerbit;
            document.getElementById('editHalaman').value = bookData.jumlah_halaman;
            document.getElementById('editFoto').value = bookData.foto;
            // ADDED: Set value for kategori_buku in edit modal
            document.getElementById('editKategoriBuku').value = bookData.kategori_buku || '';


            document.getElementById('editTanggalPinjam').value = (bookData.tanggal_pinjam && bookData.tanggal_pinjam !== '0000-00-00') ? bookData.tanggal_pinjam : '';
            document.getElementById('editTanggalPengembalian').value = (bookData.tanggal_pengembalian && bookData.tanggal_pengembalian !== '0000-00-00') ? bookData.tanggal_pengembalian : '';
        }

        let bookIdToDelete = null;

        function setDeleteBookId(bookId) {
            bookIdToDelete = bookId;
            document.getElementById('deleteBookIdPlaceholder').value = bookId;
        }

        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            if (bookIdToDelete) {
                window.location.href = `process_delete_peminjaman.php?id=${encodeURIComponent(bookIdToDelete)}`;
            }
        });

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const message = urlParams.get('message');

            if (status && message) {
                let alertClass = '';
                if (status === 'success') {
                    alertClass = 'alert-success';
                } else if (status === 'error') {
                    alertClass = 'alert-danger';
                }

                const alertDiv = document.createElement('div');
                alertDiv.className = `alert ${alertClass} alert-dismissible fade show mt-3`;
                alertDiv.setAttribute('role', 'alert');
                alertDiv.innerHTML = `
                    ${decodeURIComponent(message)}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                document.querySelector('.main-content').prepend(alertDiv);

                history.replaceState({}, document.title, window.location.pathname);
            }
        };
    </script>
</body>
</html>