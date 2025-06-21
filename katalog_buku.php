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
    }

    .image-box img {
      width: 80px;
      opacity: 0.7;
    }

    .main-content {
      padding: 40px;
    }

    /* --- Image and Card Styling Improvements --- */
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
        padding-left: 0; /* Remove default left padding to align with image on small screens */
    }

    @media (max-width: 768px) {
      .sidebar .logout {
        position: static;
        margin-top: 30px;
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
      <div class="col">
        <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
          ☰ Menu
        </button>
        <span class="ms-3">Dashboard Siswa</span>
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
        </div>
      </div>

      <main class="col-md-9 col-12 main-content">
        <h4>Katalog Buku</h4>

        <div class="input-group mb-3">
          <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="mencari buku yang mau dipinjam" style="max-width: 400px;" onkeyup="filterCards()" />
        </div>

        <?php
        // Database connection details
        $servername = "localhost"; // Your database server
        $username = "root"; // Your database username
        $password = ""; // Your database password
        $dbname = "perpustakaan"; // **IMPORTANT: Change this to your actual database name**

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Initialize search query for PHP
        $php_search_query = "";
        if (isset($_GET['php_search']) && !empty($_GET['php_search'])) {
            $search_term_php = $conn->real_escape_string($_GET['php_search']);
            $php_search_query = " WHERE judul_buku LIKE '%$search_term_php%' OR id_buku LIKE '%$search_term_php%' OR nama_penulis LIKE '%$search_term_php%'";
        }

        // SQL query to fetch data from the 'data_buku' table
        $sql = "SELECT id_buku, judul_buku, isbn, nama_penulis, nama_penerbit, jumlah_halaman, foto FROM data_buku" . $php_search_query;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Output data for each row
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="card mb-4 book-card">
                  <div class="row g-0">
                    <div class="col-md-4">
                      <img src="<?php echo htmlspecialchars($row['foto']); ?>" class="img-fluid rounded-start" alt="Book Cover">
                    </div>
                    <div class="col-md-8">
                      <div class="card-body">
                        <h5 class="card-title book-id"><?php echo htmlspecialchars($row['id_buku']); ?></h5> <p class="card-text book-title"><strong>Judul Buku:</strong> <?php echo htmlspecialchars($row['judul_buku']); ?></p> <p class="card-text"><strong>ISBN:</strong> <?php echo htmlspecialchars($row['isbn']); ?></p>
                        <p class="card-text book-author"><strong>Nama Penulis:</strong> <?php echo htmlspecialchars($row['nama_penulis']); ?></p> <p class="card-text"><strong>Nama Penerbit:</strong> <?php echo htmlspecialchars($row['nama_penerbit']); ?></p>
                        <p class="card-text"><strong>Jumlah Halaman:</strong> <?php echo htmlspecialchars($row['jumlah_halaman']); ?></p>
                        <form action="process_peminjaman.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id_buku" value="<?php echo htmlspecialchars($row['id_buku']); ?>">
                        <button type="submit" class="btn btn-success">simpan Buku</button>
                       </form>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
            }
        } else {
            echo "<p>buku belum ditambahkan</p>";
        }

        $conn->close();
        ?>

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
        bookTitle = card.querySelector(".book-title"); // Select by specific class for title
        bookAuthor = card.querySelector(".book-author"); // Select by specific class for author
        bookId = card.querySelector(".book-id"); // Select by specific class for book ID

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