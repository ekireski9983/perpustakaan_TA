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
                <a href="katalog_buku.php" class="active">Katalog Buku</a> <a href="peminjaman_buku.php">Peminjaman Buku</a>
                <a href="pengembalian_buku.php">Pengembalian Buku</a>
                <a href="denda_keterlambatan.php">Denda Keterlambatan</a>
                <div class="logout">
                    <a href="logout.php">Logout</a>
                </div>
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
                    <a href="katalog_buku.php" class="active">Katalog Buku</a>
                    <a href="peminjaman_buku.php">Peminjaman Buku</a>
                    <a href="pengembalian_buku.php">Pengembalian Buku</a>
                    <a href="denda_keterlambatan.php">Denda Keterlambatan</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>

            <main class="col-md-9 col-12 main-content">
                <h4>Katalog Buku</h4>

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
                                    <a href="peminjaman_buku.php?id=BOOK001" class="btn btn-success">Pinjam buku</a>
                                    <a href="peminjaman_buku.php?id=BOOK001" class="btn btn-primary">edit</a>
                                    <a href="peminjaman_buku.php?id=BOOK001" class="btn btn-danger">hapus</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript for filtering cards (client-side)
        function filterCards() {
            let input, filter, cards, card, bookTitle, bookAuthor, bookId, i, titleTxt, authorTxt, idTxt;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            cards = document.getElementsByClassName("book-card"); // Gets all elements with this class

            for (i = 0; i < cards.length; i++) {
                card = cards[i];
                bookTitle = card.querySelector(".book-title");
                bookAuthor = card.querySelector(".book-author");
                bookId = card.querySelector(".book-id");

                let match = false;

                // Check if any of the text content matches the filter
                if (bookTitle && (bookTitle.textContent || bookTitle.innerText).toUpperCase().indexOf(filter) > -1) {
                    match = true;
                }
                if (!match && bookAuthor && (bookAuthor.textContent || bookAuthor.innerText).toUpperCase().indexOf(filter) > -1) {
                    match = true;
                }
                if (!match && bookId && (bookId.textContent || bookId.innerText).toUpperCase().indexOf(filter) > -1) {
                    match = true;
                }

                // Show or hide the card based on whether a match was found
                if (match) {
                    card.style.display = "";
                } else {
                    card.style.display = "none";
                }
            }
        }

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
                                            <a href="peminjaman_buku.php?id=${book.id_buku}" class="btn btn-success">Pinjam buku</a>
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