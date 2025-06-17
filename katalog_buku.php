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

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="katalogBukuContainer">
          <div class="col">
            <div class="card h-100 book-card">
              <img src="assets/default_book.jpg" class="card-img-top" alt="The Great Gatsby Cover">
              <div class="card-body">
                <h5 class="card-title">The Great Gatsby</h5>
                <p class="card-text">
                  <small class="text-muted">ID Buku: B001</small>
                  <small class="text-muted">ISBN: 978-0321765723</small>
                  <small class="text-muted">Penulis: F. Scott Fitzgerald</small>
                  <small class="text-muted">Penerbit: Scribner</small>
                  <small class="text-muted">Halaman: 180</small>
                </p>
                <button class="btn btn-primary btn-sm">Pinjam</button>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card h-100 book-card">
              <img src="assets/default_book.jpg" class="card-img-top" alt="To Kill a Mockingbird Cover">
              <div class="card-body">
                <h5 class="card-title">To Kill a Mockingbird</h5>
                <p class="card-text">
                  <small class="text-muted">ID Buku: B002</small>
                  <small class="text-muted">ISBN: 978-0743273565</small>
                  <small class="text-muted">Penulis: Harper Lee</small>
                  <small class="text-muted">Penerbit: J.B. Lippincott & Co.</small>
                  <small class="text-muted">Halaman: 324</small>
                </p>
                <button class="btn btn-primary btn-sm">Pinjam</button>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card h-100 book-card">
              <img src="assets/default_book.jpg" class="card-img-top" alt="1984 Cover">
              <div class="card-body">
                <h5 class="card-title">1984</h5>
                <p class="card-text">
                  <small class="text-muted">ID Buku: B003</small>
                  <small class="text-muted">ISBN: 978-0451524935</small>
                  <small class="text-muted">Penulis: George Orwell</small>
                  <small class="text-muted">Penerbit: Signet Classic</small>
                  <small class="text-muted">Halaman: 328</small>
                </p>
                <button class="btn btn-primary btn-sm">Pinjam</button>
              </div>
            </div>
          </div>
          </div>
      </main>
    </div>
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