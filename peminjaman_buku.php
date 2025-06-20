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
        <a href="lihat_anggota.php">Lihat Anggota</a>
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
          <a href="lihat_anggota.php">Lihat Anggota</a>
          <a href="katalog_buku.php">Katalog Buku</a>
          <a href="peminjaman_buku.php">Peminjaman Buku</a>
          <a href="pengembalian_buku.php">Pengembalian Buku</a>
          <a href="denda_keterlambatan.php">Denda Keterlambatan</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>

            <main class="col-md-9 col-12 main-content">
                <h4>Peminjaman Buku</h4>

                <div class="input-group mb-3">
                    <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="Cari buku berdasarkan judul, ID, atau penulis..." style="max-width: 400px;" onkeyup="filterCards()" aria-label="Search Book" />
                </div>

                <div id="bookCardsContainer">
                    <div class="card mb-4 book-card">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <img src="upload/68552287ad0f52.88016925.jpg" class="img-fluid rounded-start" alt="Book Cover">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h5 class="card-title book-id">BOOK001</h5>
                                    <p class="card-text book-title"><strong>Judul Buku:</strong> The Great Adventure</p>
                                    <p class="card-text"><strong>ISBN:</strong> 978-0123456789</p>
                                    <p class="card-text book-author"><strong>Nama Penulis:</strong> Jane Doe</p>
                                    <p class="card-text"><strong>Nama Penerbit:</strong> Adventure Publishing</p>
                                    <p class="card-text"><strong>Jumlah Halaman:</strong> 320</p>
                                    <p class="card-text"><strong>tanggal pinjam:</strong> 00/00/00</p>
                                    <p class="card-text"><strong>tanggal pengembalian:</strong> 00/00/00</p>
                                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#borrowBookModal" onclick="showBorrowModal('BOOK001')">Pinjam buku</button>
                                    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#editBookModal" onclick="showEditModal({id_buku: 'BOOK001', judul_buku: 'The Great Adventure', isbn: '978-0123456789', nama_penulis: 'Jane Doe', nama_penerbit: 'Adventure Publishing', jumlah_halaman: 320, foto: 'upload/68552287ad0f52.88016925.jpg'})">Edit</button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" onclick="setDeleteBookId('BOOK001')">Hapus</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4 book-card">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <img src="upload/68552287ad0f52.88016925.jpg" class="img-fluid rounded-start" alt="Book Cover">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h5 class="card-title book-id">BOOK002</h5>
                                    <p class="card-text book-title"><strong>Judul Buku:</strong> Mystery of the Old House</p>
                                    <p class="card-text"><strong>ISBN:</strong> 978-9876543210</p>
                                    <p class="card-text book-author"><strong>Nama Penulis:</strong> John Smith</p>
                                    <p class="card-text"><strong>Nama Penerbit:</strong> Whodunit Books</p>
                                    <p class="card-text"><strong>Jumlah Halaman:</strong> 280</p>
                                    <p class="card-text"><strong>tanggal pinjam:</strong> 00/00/00</p>
                                    <p class="card-text"><strong>tanggal pengembalian:</strong> 00/00/00</p>
                                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#borrowBookModal" onclick="showBorrowModal">Pinjam buku</button>
                                    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#editBookModal" onclick="showEditModal">Edit</button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" onclick="setDeleteBookId">Hapus</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                <form id="borrowForm" action="process_borrow.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="id_buku" name="id_buku">
                        <div class="mb-3">
                            <label for="tambahidbuku" class="form-label">id buku</label>
                            <input type="text" class="form-control" id="text" name="id_buku" required>
                        </div>
                         <div class="mb-3">
                            <label for="judulbuku" class="form-label">judul buku</label>
                            <input type="text" class="form-control" id="text" name="judul_buku" required>
                        </div>
                         <div class="mb-3">
                            <label for="isbn" class="form-label">isbn</label>
                            <input type="text" class="form-control" id="text" name="isbn" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPenulis" class="form-label">Nama Penulis</label>
                            <input type="text" class="form-control" id="editPenulis" name="nama_penulis" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPenerbit" class="form-label">Nama Penerbit</label>
                            <input type="text" class="form-control" id="editPenerbit" name="nama_penerbit">
                        </div>
                        <div class="mb-3">
                            <label for="tanggalpinjam" class="form-label">Tanggal Pinjam</label>
                            <input type="date" class="form-control" id="Date" name="tanggal_pinjam" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggalpengembalian" class="form-label">Tanggal pengembalian</label>
                            <input type="date" class="form-control" id="returnDate" name="tanggal_pengembalian" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Pinjam buku</button>
                        <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBookModalLabel">Edit pinjam Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" action="process_edit_book.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_book_id" name="book_id">
                        <div class="mb-3">
                            <label for="editJudul" class="form-label">Judul Buku</label>
                            <input type="text" class="form-control" id="editJudul" name="judul_buku" required>
                        </div>
                        <div class="mb-3">
                            <label for="editISBN" class="form-label">ISBN</label>
                            <input type="text" class="form-control" id="editISBN" name="isbn">
                        </div>
                        <div class="mb-3">
                            <label for="editPenulis" class="form-label">Nama Penulis</label>
                            <input type="text" class="form-control" id="editPenulis" name="nama_penulis" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPenerbit" class="form-label">Nama Penerbit</label>
                            <input type="text" class="form-control" id="editPenerbit" name="nama_penerbit">
                        </div>
                        <div class="mb-3">
                            <label for="editHalaman" class="form-label">Jumlah Halaman</label>
                            <input type="number" class="form-control" id="editHalaman" name="jumlah_halaman">
                        </div>
                        <div class="mb-3">
                            <label for="editpinjam" class="form-label">tanggal_pinjam</label>
                            <input type="date" class="form-control" id="editHalaman" name="tanggal_pinjam">
                        </div>
                        <div class="mb-3">
                            <label for="editPengembalian" class="form-label">tanggal pengembalian</label>
                            <input type="date" class="form-control" id="editHalaman" name="tanggal_pengembalian">
                        </div>
                        </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
                    <p>Apakah Anda yakin ingin menghapus data ini</p>
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

        // Function to show the Borrow Book Modal and set the book ID
        function showBorrowModal(bookId) {
            document.getElementById('borrow_book_id').value = bookId;
            document.getElementById('borrowDate').valueAsDate = new Date();
            const today = new Date();
            const returnDate = new Date(today);
            returnDate.setDate(today.getDate() + 7);
            document.getElementById('returnDate').valueAsDate = returnDate;
        }

        // Function to show the Edit Book Modal and pre-fill its fields
        function showEditModal(bookData) {
            document.getElementById('edit_book_id').value = bookData.id_buku;
            document.getElementById('editJudul').value = bookData.judul_buku;
            document.getElementById('editISBN').value = bookData.isbn;
            document.getElementById('editPenulis').value = bookData.nama_penulis;
            document.getElementById('editPenerbit').value = bookData.nama_penerbit;
            document.getElementById('editHalaman').value = bookData.jumlah_halaman;
            // You might remove this line if you're not pre-filling the image path
            // document.getElementById('editFoto').value = bookData.foto;
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
                window.location.href = `process_delete_book.php?id=${bookIdToDelete}`;
            }
        });

        // --- OPTIONAL: JavaScript for fetching and rendering dynamic book data ---
        // This section demonstrates how you would fetch data from a backend (e.g., a PHP API)
        // and dynamically create the book cards. This replaces the PHP loop you had.

        /*
        document.addEventListener('DOMContentLoaded', function() {
            fetchBooks();
        });

        async function fetchBooks() {
            try {
                // Replace 'your_api_endpoint.php' with the actual URL to your PHP script
                // that returns book data (e.g., as JSON)
                const response = await fetch('your_api_endpoint.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const books = await response.json(); // Assuming your PHP returns JSON

                const container = document.getElementById('bookCardsContainer');
                container.innerHTML = ''; // Clear any existing hardcoded cards

                if (books.length > 0) {
                    books.forEach(book => {
                        const cardHtml = `
                            <div class="card mb-4 book-card">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <img src="${book.foto}" class="img-fluid rounded-start" alt="Book Cover">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <h5 class="card-title book-id">${book.id_buku}</h5>
                                            <p class="card-text book-title"><strong>Judul Buku:</strong> ${book.judul_buku}</p>
                                            <p class="card-text"><strong>ISBN:</strong> ${book.isbn}</p>
                                            <p class="card-text book-author"><strong>Nama Penulis:</strong> ${book.nama_penulis}</p>
                                            <p class="card-text"><strong>Nama Penerbit:</strong> ${book.nama_penerbit}</p>
                                            <p class="card-text"><strong>Jumlah Halaman:</strong> ${book.jumlah_halaman}</p>
                                            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#borrowBookModal" onclick="showBorrowModal('${book.id_buku}')">Pinjam buku</button>
                                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#editBookModal" onclick='showEditModal(${JSON.stringify(book)})'>Edit</button>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" onclick="setDeleteBookId('${book.id_buku}')">Hapus</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', cardHtml);
                    });
                } else {
                    container.innerHTML = '<p>Buku belum ditambahkan.</p>';
                }

            } catch (error) {
                console.error("Error fetching books:", error);
                document.getElementById('bookCardsContainer').innerHTML = '<p>Gagal memuat katalog buku. Silakan coba lagi nanti.</p>';
            }
        }
        */
    </script>
</body>
</html>