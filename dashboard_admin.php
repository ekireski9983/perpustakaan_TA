<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>dashboard Pustakawan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f1f5f9;
      margin: 0;
    }

    .sidebar {
      min-height: 100vh;
      background-color: #2f3e46;
      color: white;
      padding-top: 20px;
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

    .sidebar a:hover, .sidebar a.active {
      background-color: #00b4d8;
      border-radius: 5px;
    }

    .sidebar .logout {
      position: absolute;
      bottom: 20px;
      width: 100%;
    }

    .sidebar .image-box {
      margin-top: 50px;
      text-align: center;
    }

    .sidebar .image-box img {
      width: 80px;
      opacity: 0.7;
    }

    .main-content {
      padding: 40px;
    }

    @media (max-width: 768px) {
      .sidebar {
        min-height: auto;
        position: relative;
      }
      .sidebar .logout {
        position: static;
        margin-top: 30px;
      }
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row flex-md-nowrap">
      <!-- Sidebar -->
      <nav class="col-md-3 col-12 sidebar position-relative">
        <h5>Pustakawan<br><small>admin</small></h5>
         <a href="kelola_anggota.php">kelola anggota</a>
         <a href="kelola_katalog.php">kelola katalog buku</a>
         <a href="kelola_peminjaman.php">kelola Peminjaman buku</a>
         <a href="kelola_pengembalian.php">kelola Pengembalian buku</a>
         <a href="kelola_denda.php">kelola denda</a>
        <a href="#" class="logout">Logout</a>
        <div class="image-box mt-5">
          <img src="https://via.placeholder.com/80" alt="icon">
        </div>
      </nav>

      <!-- Main Content -->
      <main class="col-md-9 col-12 main-content">
        <h4>Selamat datang di halaman admin</h4>
        <p>Silakan pilih menu di sebelah kiri untuk mengelola sistem perpustakaan.</p>
      </main>
    </div>
  </div>

  <!-- Bootstrap JS (Opsional jika perlu interaksi) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
