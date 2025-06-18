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

    /* Style for the search bar */
    .search-bar {
      margin-bottom: 20px;
    }

    @media (max-width: 768px) {
      .sidebar .logout {
        position: static;
        margin-top: 30px;
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
        <h4>Peminjaman buku</h4>

        <div class="input-group search-bar">
            <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="mencari buku yang mau dipinjam" style="max-width: 300px;" oninput="filterCards()" />
        </div>

        <div class="row">
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm">
              <img src="upload/68522c0484a1e3.49333472.jpg" class="card-img-top" alt="Book Cover 1">
              <div class="card-body">
                <h5 class="card-title">Judul Buku 1</h5>
                <h6 class="card-subtitle mb-2 text-muted">Penulis: Nama Penulis 1</h6>
                <p class="card-text">Deskripsi singkat tentang Buku 1. Ini adalah tempat untuk memberikan ringkasan atau detail penting.</p>
                <a href="#" class="btn btn-success btn-sm me-2">Pinjam</a>
                <a href="#" class="btn btn-info btn-sm me-2">Edit</a>
                <a href="#" class="btn btn-danger btn-sm">Hapus</a>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm">
              <img src="upload/68522c0484a1e3.49333472.jpg" class="card-img-top" alt="Book Cover 2">
              <div class="card-body">
                <h5 class="card-title">Judul Buku 2</h5>
                <h6 class="card-subtitle mb-2 text-muted">Penulis: Nama Penulis 2</h6>
                <p class="card-text">Deskripsi singkat tentang Buku 2. Ini adalah tempat untuk memberikan ringkasan atau detail penting.</p>
                <a href="#" class="btn btn-success btn-sm me-2">Pinjam</a>
                <a href="#" class="btn btn-info btn-sm me-2">Edit</a>
                <a href="#" class="btn btn-danger btn-sm">Hapus</a>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm">
              <img src="upload/68522c0484a1e3.49333472.jpg" class="card-img-top" alt="Book Cover 3">
              <div class="card-body">
                <h5 class="card-title">Judul Buku 3</h5>
                <h6 class="card-subtitle mb-2 text-muted">Penulis: Nama Penulis 3</h6>
                <p class="card-text">Deskripsi singkat tentang Buku 3. Ini adalah tempat untuk memberikan ringkasan atau detail penting.</p>
                <a href="#" class="btn btn-success btn-sm me-2">Pinjam</a>
                <a href="#" class="btn btn-info btn-sm me-2">edit</a>
                <a href="#" class="btn btn-danger btn-sm">Hapus</a>
              </div>
            </div>
          </div>
          </div>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>