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
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
                <h4>Katalog Buku</h4>

                <div class="input-group mb-3">
                    <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="Cari buku berdasarkan judul, ID, atau penulis..." style="max-width: 400px;" onkeyup="filterCards()" aria-label="Search Book" />
                </div>

                <?php
                
                $servername = "localhost"; 
                $username = "root"; 
                $password = ""; 
                $dbname = "perpustakaan"; 

                
                $conn = new mysqli($servername, $username, $password, $dbname);

                
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                
                $php_search_query = "";
                if (isset($_GET['php_search']) && !empty($_GET['php_search'])) {
                    $search_term_php = $conn->real_escape_string($_GET['php_search']);
                    $php_search_query = " WHERE judul_buku LIKE '%$search_term_php%' OR id_buku LIKE '%$search_term_php%' OR nama_penulis LIKE '%$search_term_php%'";
                }

                // Add 'kategori_buku' to the SELECT statement
                $sql = "SELECT id_buku, judul_buku, isbn, nama_penulis, nama_penerbit, jumlah_halaman, foto, kategori_buku FROM data_buku" . $php_search_query;
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <div class="card mb-4 book-card">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="<?php echo htmlspecialchars($row['foto']); ?>" class="img-fluid rounded-start" alt="Book Cover">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title book-id"><?php echo htmlspecialchars($row['id_buku']); ?></h5>
                                        <p class="card-text book-title"><strong>Judul Buku:</strong> <?php echo htmlspecialchars($row['judul_buku']); ?></p>
                                        <p class="card-text"><strong>ISBN:</strong> <?php echo htmlspecialchars($row['isbn']); ?></p>
                                        <p class="card-text book-author"><strong>Nama Penulis:</strong> <?php echo htmlspecialchars($row['nama_penulis']); ?></p>
                                        <p class="card-text"><strong>Nama Penerbit:</strong> <?php echo htmlspecialchars($row['nama_penerbit']); ?></p>
                                        <p class="card-text"><strong>kategori buku:</strong> <?php echo htmlspecialchars($row['kategori_buku']); ?></p>
                                        <p class="card-text"><strong>Jumlah Halaman:</strong> <?php echo htmlspecialchars($row['jumlah_halaman']); ?></p>
                                        <form action="process_peminjaman.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id_buku" value="<?php echo htmlspecialchars($row['id_buku']); ?>">
                                            <button type="submit" class="btn btn-success">Simpan Buku</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>Buku belum ditambahkan</p>";
                }

                $conn->close();
                ?>

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

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        function filterCards() {
            let input, filter, cards, card, bookTitle, bookAuthor, bookId, i, titleTxt, authorTxt, idTxt;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            cards = document.getElementsByClassName("book-card");

            for (i = 0; i < cards.length; i++) {
                card = cards[i];
                bookTitle = card.querySelector(".book-title"); 
                bookAuthor = card.querySelector(".book-author"); 
                bookId = card.querySelector(".book-id"); 

                let match = false;

                if (bookTitle) {
                    titleTxt = bookTitle.textContent || bookTitle.innerText;
                    if (titleTxt.toUpperCase().indexOf(filter) > -1) {
                        match = true;
                    }
                }
                if (!match && bookAuthor) {
                    authorTxt = bookAuthor.textContent || bookAuthor.innerText;
                    if (authorTxt.toUpperCase().indexOf(filter) > -1) {
                        match = true;
                    }
                }
                if (!match && bookId) {
                    idTxt = bookId.textContent || bookId.innerText;
                    if (idTxt.toUpperCase().indexOf(filter) > -1) {
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
    </script>
</body>
</html>