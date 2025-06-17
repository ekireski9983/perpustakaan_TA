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
    <!-- Navbar toggle for mobile -->
    <div class="row d-md-none bg-dark text-white p-2">
      <div class="col">
        <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
          ☰ Menu
        </button>
        <span class="ms-3">Dashboard siswa</span>
      </div>
    </div>

    <div class="row">
      <!-- Sidebar for md and up -->
      <nav class="col-md-3 d-none d-md-block sidebar min-vh-100 position-relative">
        <h5 class="pt-4">siswa<br /><small>user</small></h5>
        <a href="lihat_anggota.php">lihat anggota</a>
        <a href="katalog_buku.php">katalog buku</a>
        <a href="peminjaman_buku.php">Peminjaman buku</a>
        <a href="pengembalian_buku.php">Pengembalian buku</a>
        <a href="denda_keterlambatan.php">denda keterlambatan</a>
        <a href="logout.php">Logout</a>
        <div class="image-box text-center mt-5">
          <img src="assets/Bootstrap_logo.png" alt="icon" />
        </div>
      </nav>
      
      <!-- Isi konten -->
     <?php
     session_start(); // Tambahkan ini

    $koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");
    if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
    }

    $nama_user = $_SESSION['username'] ?? ''; // Ambil nama dari session

    // Ambil data hanya untuk nama user yang sedang login
       $query = "SELECT * FROM data_anggota WHERE nama = '$nama_user'";
       $result = mysqli_query($koneksi, $query);
       ?>
<div class="col-md-9 main-content">
  <div class="card" style="width: 100%;">
    <div class="card-body">
      <?php if (mysqli_num_rows($result) > 0): ?>
        <ul>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <li>
              Nama Siswa: <?= htmlspecialchars($row['nama']) ?><br>
              Kelas: <?= htmlspecialchars($row['kelas']) ?><br>
              Jurusan: <?= htmlspecialchars($row['jurusan']) ?><br>
              Semester: <?= htmlspecialchars($row['semester']) ?>
            </li><br>
          <?php endwhile; ?>
        </ul>
      <?php else: ?>
        <p>Data tidak ditemukan atau Anda belum terdaftar sebagai anggota.</p>
      <?php endif; ?>
    </div>
  </div>
</div>


  

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
