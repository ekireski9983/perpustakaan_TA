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

// Fetch book data from data_pinjam
// It's good practice to fetch all columns you might need, even if some are nullable
$sql = "SELECT id_buku, judul_buku, isbn, nama_penulis, nama_penerbit, jumlah_halaman, foto, tanggal_pinjam, tanggal_pengembalian FROM data_pinjam";
$result = $conn->query($sql);

$books = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
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
        }

        .sidebar {
            background-color: #2f3e46;
            color: white;
            padding-top: 20px; /* Added padding to align content better */
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
            transition: background-color 0.3s ease; /* Smooth transition for hover */
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
            padding-left: 20px; /* Align logout button with other links */
        }

        .image-box img {
            width: 80px;
            opacity: 0.7;
        }

        .main-content {
            padding: 40px;
        }

        /* --- Image and Card Styling Improvements --- */
        .book-card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Added subtle shadow for depth */
            border-radius: 8px; /* Slightly more rounded corners */
        }

        .book-card .col-md-4 {
            display: flex; /* Use flexbox for vertical centering */
            align-items: center; /* Center image vertically */
            justify-content: center; /* Center image horizontally */
            padding: 15px; /* Add some padding around the image */
        }

        .book-card img {
            width: 100%; /* Make image take full width of its column */
            max-width: 180px; /* Set a maximum width for the image */
            height: auto; /* Maintain aspect ratio */
            object-fit: contain; /* Ensure the whole image is visible within its bounds */
            border-radius: var(--bs-border-radius); /* Use Bootstrap's default border-radius */
        }

        /* Optional: Adjust card-body padding if needed */
        .book-card .card-body {
            padding: 20px; /* Consistent padding inside card body */
        }

        @media (max-width: 768px) {
            .sidebar {
                padding-top: 0; /* Remove top padding for offcanvas on small screens */
            }
            .sidebar h5 {
                margin-left: 0; /* Reset margin for offcanvas title */
                text-align: center; /* Center title in offcanvas */
            }
            .sidebar a {
                padding: 10px 15px; /* Adjust padding for offcanvas links */
                text-align: center;
            }
            .sidebar .logout {
                position: static;
                margin-top: 30px;
                padding-left: 0;
                text-align: center;
            }
            .book-card .col-md-4 {
                padding-bottom: 0; /* Less padding on small screens if image is above text */
            }
            .book-card img {
                max-width: 100%; /* Allow image to fill its column on smaller screens */
                max-height: 250px; /* Adjust max height for smaller screens if necessary */
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row d-md-none bg-dark text-white p-2">
            <div class="col d-flex align-items-center">
                <button class="btn btn-outline-light me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <span class="navbar-toggler-icon"></span> Menu
                </button>
                <span class="fs-5">Dashboard Siswa</span>
            </div>
        </div>

        <div class="row">
            <nav class="col-md-3 d-none d-md-block sidebar min-vh-100 position-relative">
                <h5 class="pt-4">Siswa<br /><small>user</small></h5>
                <a href="lihat_anggota.php">lihat Anggota</a>
                <a href="katalog_buku.php">Katalog Buku</a>
                <a href="peminjaman_buku.php">Peminjaman Buku</a>
                <a href="pengembalian_buku.php">Pengembalian Buku</a>
                <a href="denda_keterlambatan.php">Denda Keterlambatan</a>
                <a href="logout.php">Logout</a>
                <div class="image-box text-center mt-5">
                    <img src="assets/Bootstrap_logo.png" alt="icon" />
                </div>
            </nav>

            <div class="offcanvas offcanvas-start sidebar" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="sidebarMenuLabel">Siswa</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <h5 class="pt-4">Siswa<br /><small>user</small></h5>
                    <a href="lihat_anggota.php">lihat Anggota</a>
                    <a href="katalog_buku.php">Katalog Buku</a>
                    <a href="peminjaman_buku.php">Peminjaman Buku</a>
                    <a href="pengembalian_buku.php">Pengembalian Buku</a>
                    <a href="denda_keterlambatan.php">Denda Keterlambatan</a>
                    <a href="logout.php">Logout</a>
                    <div class="image-box text-center mt-5">
                        <img src="assets/Bootstrap_logo.png" alt="icon" />
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
                                        <img src="<?php echo htmlspecialchars($book['foto']); ?>" class="img-fluid rounded-start" alt="Book Cover">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <h5 class="card-title book-id"><?php echo htmlspecialchars($book['id_buku']); ?></h5>
                                            <p class="card-text book-title"><strong>Judul Buku:</strong> <?php echo htmlspecialchars($book['judul_buku']); ?></p>
                                            <p class="card-text"><strong>ISBN:</strong> <?php echo htmlspecialchars($book['isbn']); ?></p>
                                            <p class="card-text book-author"><strong>Nama Penulis:</strong> <?php echo htmlspecialchars($book['nama_penulis']); ?></p>
                                            <p class="card-text"><strong>Nama Penerbit:</strong> <?php echo htmlspecialchars($book['nama_penerbit']); ?></p>
                                            <p class="card-text"><strong>Jumlah Halaman:</strong> <?php echo htmlspecialchars($book['jumlah_halaman']); ?></p>
                                            <p class="card-text"><strong>Tanggal Pinjam:</strong> <?php echo htmlspecialchars($book['tanggal_pinjam'] ?? ''); ?></p>
                                            <p class="card-text"><strong>Tanggal Pengembalian:</strong> <?php echo htmlspecialchars($book['tanggal_pengembalian'] ?? ''); ?></p>
                                            
                                            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#borrowBookModal"
                                                     onclick="showBorrowModal('<?php echo htmlspecialchars($book['id_buku']); ?>', '<?php echo htmlspecialchars($book['judul_buku']); ?>', '<?php echo htmlspecialchars($book['isbn']); ?>', '<?php echo htmlspecialchars($book['nama_penulis']); ?>', '<?php echo htmlspecialchars($book['nama_penerbit']); ?>', <?php echo htmlspecialchars($book['jumlah_halaman']); ?>, '<?php echo htmlspecialchars($book['foto']); ?>')">Pinjam buku</button>
                                            
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
                        <button type="submit" class="btn btn-primary">Edit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
                    <p>Apakah Anda yakin ingin menghapus data buku?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript for filtering cards (client-side)
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
                if (!match && bookId && (bookId.textContent || bookId.innerText).toUpperCase().indexOf(filter) > -1) {
                    match = true;
                }

                if (match) {
                    card.style.display = "";
                } else {
                    card.style.display = "none";
                }
            }
        }

        // Function to show the Borrow Book Modal and set the book details
        function showBorrowModal(id_buku, judul_buku, isbn, nama_penulis, nama_penerbit, jumlah_halaman, foto) {
            document.getElementById('borrowBookId').value = id_buku;
            document.getElementById('borrowJudulBuku').value = judul_buku;
            document.getElementById('borrowISBN').value = isbn;
            document.getElementById('borrowNamaPenulis').value = nama_penulis;
            document.getElementById('borrowNamaPenerbit').value = nama_penerbit;
            document.getElementById('borrowJumlahHalaman').value = jumlah_halaman;
            document.getElementById('borrowFoto').value = foto; // Set hidden foto field

            const today = new Date();
            // Format date to YYYY-MM-DD for input type="date"
            const todayFormatted = today.toISOString().split('T')[0];
            document.getElementById('borrowDate').value = todayFormatted;

            const returnDate = new Date(today);
            returnDate.setDate(today.getDate() + 7); // Default return date: 7 days from today
            const returnDateFormatted = returnDate.toISOString().split('T')[0];
            document.getElementById('borrowReturnDate').value = returnDateFormatted;
        }

        // Function to show the Edit Borrow Book Modal and pre-fill its fields
        function showEditModal(bookData) {
            // Set the value for the hidden input field to identify the record being edited
            document.getElementById('edit_original_book_id').value = bookData.id_buku;

            document.getElementById('editBookId').value = bookData.id_buku;
            document.getElementById('editJudul').value = bookData.judul_buku;
            document.getElementById('editISBN').value = bookData.isbn;
            document.getElementById('editPenulis').value = bookData.nama_penulis;
            document.getElementById('editPenerbit').value = bookData.nama_penerbit;
            document.getElementById('editHalaman').value = bookData.jumlah_halaman;
            document.getElementById('editFoto').value = bookData.foto; // Set hidden foto field

            // Set dates for editing, ensure they are in YYYY-MM-DD format
            // If the date is '0000-00-00' or null, set it to empty string so the date picker doesn't show an invalid date.
            document.getElementById('editTanggalPinjam').value = (bookData.tanggal_pinjam === '0000-00-00' || !bookData.tanggal_pinjam) ? '' : bookData.tanggal_pinjam;
            document.getElementById('editTanggalPengembalian').value = (bookData.tanggal_pengembalian === '0000-00-00' || !bookData.tanggal_pengembalian) ? '' : bookData.tanggal_pengembalian;
        }

        // Variable to store the ID of the book to be deleted
        let bookIdToDelete = null;

        // Function to set the book ID in the delete confirmation modal
        function setDeleteBookId(bookId) {
            bookIdToDelete = bookId;
            document.getElementById('deleteBookIdPlaceholder').textContent = bookId;
        }

        // Event listener for the "Hapus" button inside the delete confirmation modal
        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            if (bookIdToDelete) {
                // Redirect to a PHP script to handle deletion
                window.location.href = `process_delete_peminjaman.php?id=${bookIdToDelete}`;
            }
        });

        // Display status messages from URL parameters
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

                // Optional: remove the URL parameters after displaying the message
                history.replaceState({}, document.title, window.location.pathname);
            }
        };
    </script>
</body>
</html>