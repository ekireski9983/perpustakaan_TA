<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard siswa</title>
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

    /* Card Specific Styles */
    .book-card .card-img-top {
      height: 200px; /* Fixed height for consistent image display */
      object-fit: cover; /* Ensures images cover the area without distortion */
      border-bottom: 1px solid rgba(0, 0, 0, 0.125); /* Separator for image */
    }

    .book-card .card-body {
      padding: 1rem;
    }

    .book-card .card-title {
      font-size: 1.25rem;
      font-weight: bold;
      margin-bottom: 0.5rem;
    }

    .book-card .card-text small {
      display: block;
      color: #6c757d;
      font-size: 0.875em;
    }

    .book-card .btn {
      margin-top: 1rem;
    }


    @media (max-width: 768px) {
      .sidebar .logout {
        position: static;
        margin-top: 30px;
      }
      .main-content {
        padding: 20px; /* Adjust padding for smaller screens */
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
        <span class="ms-3">Dashboard siswa</span>
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

      <main class="col-md-9 col-12 main-content">
        <h4>Katalog Buku</h4>
        <div class="mb-4 mt-3">
          <input type="text" id="searchInput" class="form-control form-control-md" placeholder="Cari Judul Buku" style="max-width: 400px;" oninput="filterCards()" />
        </div>

       <?php
// --- Database Connection ---
// IMPORTANT: Replace these with your actual database credentials
$servername = "localhost"; // Usually 'localhost'
$username = "root";       // Your MySQL username
$password = "";           // Your MySQL password (often empty for root on local setup)
$dbname = "perpustakaan"; // The name of your database

// Create connection
$koneksi = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// --- Fetch Book Data ---
// Query to select all relevant columns from the data_buku table
$sql = "SELECT id_buku, isbn, judul_buku, nama_penulis, nama_penerbit, jumlah_halaman, foto FROM data_buku ORDER BY judul_buku ASC";
$result = $koneksi->query($sql);

// Check for query errors
if (!$result) {
    die("Error retrieving books: " . $koneksi->error);
}
?>

<?php
// --- Database Connection ---
// IMPORTANT: Replace these with your actual database credentials
$servername = "localhost"; // Usually 'localhost'
$username = "root";        // Your MySQL username
$password = "";            // Your MySQL password (often empty for root on local setup)
$dbname = "perpustakaan";  // The name of your database

// Create connection
$koneksi = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// --- Fetch Book Data ---
// Query to select all relevant columns from the data_buku table
$sql = "SELECT id_buku, isbn, judul_buku, nama_penulis, nama_penerbit, jumlah_halaman, foto FROM data_buku ORDER BY judul_buku ASC";
$result = $koneksi->query($sql);

// Check for query errors
if (!$result) {
    die("Error retrieving books: " . $koneksi->error);
}
?>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="katalogBukuContainer">
    <?php
    // Check if there are results from the database query
    if ($result->num_rows > 0) {
        // Loop through each row of data
        while ($row = $result->fetch_assoc()) {
            // Determine image path:
            // Use 'upload/' directory (singular) as specified by you.
            // If 'foto' exists in the database, use it; otherwise, use a default image.
            $imagePath = !empty($row['foto']) ? 'upload/' . htmlspecialchars($row['foto']) : 'assets/default_book.jpg';
            ?>
            <div class="col">
                <div class="card h-100 book-card">
                    <img src="<?php echo $imagePath; ?>" class="card-img-top" alt="Cover Buku <?php echo htmlspecialchars($row['judul_buku']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['judul_buku']); ?></h5>
                        <p class="card-text">
                            <small class="text-muted">ID Buku: <?php echo htmlspecialchars($row['id_buku']); ?></small>
                            <small class="text-muted">ISBN: <?php echo htmlspecialchars($row['isbn']); ?></small>
                            <small class="text-muted">Penulis: <?php echo htmlspecialchars($row['nama_penulis']); ?></small>
                            <small class="text-muted">Penerbit: <?php echo htmlspecialchars($row['nama_penerbit']); ?></small>
                            <small class="text-muted">Halaman: <?php echo htmlspecialchars($row['jumlah_halaman']); ?></small>
                        </p>
                        <button class="btn btn-primary btn-sm">Pinjam</button>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        // Message if no books are found in the database
        echo '<div class="col-12"><p class="text-center text-muted">Tidak ada buku yang ditemukan dalam katalog.</p></div>';
    }
    // Close the database connection
    $koneksi->close();
    ?>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Function to filter book cards based on search input
    function filterCards() {
      const input = document.getElementById('searchInput');
      const filter = input.value.toLowerCase();
      const container = document.getElementById('katalogBukuContainer');
      const bookCols = container.getElementsByClassName('col'); // Get all column divs holding cards

      for (let i = 0; i < bookCols.length; i++) {
        const col = bookCols[i];
        const card = col.querySelector('.card');
        if (!card) continue; // Skip if no card found in the column

        const titleElement = card.querySelector('.card-title');
        const textElements = card.querySelectorAll('.card-text small'); // Get all small text elements

        let cardText = '';
        if (titleElement) {
          cardText += titleElement.textContent.toLowerCase() + ' ';
        }
        textElements.forEach(small => {
          cardText += small.textContent.toLowerCase() + ' ';
        });

        // Check if the filter text is present in the combined card text
        if (cardText.includes(filter)) {
          col.style.display = ""; // Show the column (and card)
        } else {
          col.style.display = "none"; // Hide the column (and card)
        }
      }
    }
  </script>
</body>
</html>