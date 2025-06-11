<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan"); // Ganti "nama_database" sesuai kebutuhan Anda

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data dari tabel data_anggota
$query = "SELECT * FROM data_anggota";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola Anggota</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { background-color: #f1f5f9; }
    .sidebar { background-color: #2f3e46; color: white; }
    .sidebar a { display: block; color: white; padding: 10px 20px; text-decoration: none; }
    .sidebar a:hover, .sidebar a.active { background-color: #00b4d8; border-radius: 5px; }
    .sidebar .logout { position: absolute; bottom: 20px; width: 100%; }
    .sidebar .image-box img { width: 80px; opacity: 0.7; }
    .main-content { padding: 40px; }
    .table thead { background-color: #f8f9fa; }
    .btn-edit { background-color: #48cae4; color: white; }
    .btn-delete { background-color: #f94144; color: white; }
    .btn-tambah { background-color: #00b4d8; color: white; }
    @media (max-width: 768px) { .main-content { padding: 20px; } }
  </style>
</head>
<body>
<div class="container-fluid">
  <!-- Mobile Topbar -->
  <div class="row d-md-none bg-dark text-white p-2">
    <div class="col">
      <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">☰ Menu</button>
      <span class="ms-3">Kelola Anggota</span>
    </div>
  </div>

  <div class="row">
    <!-- Sidebar Desktop -->
    <nav class="col-md-3 d-none d-md-block sidebar min-vh-100 position-relative pt-4">
      <h5 class="ms-3">Pustakawan<br><small>admin</small></h5>
      <a href="kelola_anggota.php" class="active">kelola anggota</a>
      <a href="kelola_katalog.php">kelola katalog buku</a>
      <a href="kelola_peminjaman.php">kelola Peminjaman buku</a>
      <a href="kelola_pengembalian.php">kelola Pengembalian buku</a>
      <a href="kelola_denda.php">kelola denda</a>
      <a href="#" class="logout">Logout</a>
      <div class="image-box text-center mt-5">
        <img src="assets/Bootstrap_logo.png" alt="icon" />
      </div>
    </nav>

    <!-- Sidebar Mobile Offcanvas -->
    <div class="offcanvas offcanvas-start sidebar text-white" tabindex="-1" id="sidebarMenu">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title">Pustakawan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body">
        <a href="kelola_anggota.php">kelola anggota</a>
        <a href="kelola_katalog.php">kelola katalog buku</a>
        <a href="kelola_peminjaman.php">kelola Peminjaman buku</a>
        <a href="kelola_pengembalian.php">kelola Pengembalian buku</a>
        <a href="kelola_denda.php">kelola denda</a>
        <a href="#">Logout</a>
        <div class="image-box text-center mt-5">
          <img src="assets/Bootstrap_logo.png" alt="icon" />
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="col-md-9 col-12 main-content">
      <h4>Kelola Anggota</h4>
      <div class="d-flex flex-wrap gap-2 align-items-center mb-3 mt-3">
        <input type="text" class="form-control form-control-md me-2" placeholder="Nama anggota" style="max-width: 300px;" />
        <a href="tambah_anggota.php" class="btn btn-tambah btn-md"  data-bs-toggle="modal" data-bs-target="#tambahAnggotaModal">Tambah Anggota</a>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Id Siswa</th>
              <th>Nama Siswa</th>
              <th>Jurusan</th>
              <th>Kelas</th>
              <th>Semester</th>
              <th>action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
              echo "<tr>";
              echo "<td>" . $no++ . "</td>";
              echo "<td>" . htmlspecialchars($row['id_siswa']) . "</td>";
              echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
              echo "<td>" . htmlspecialchars($row['jurusan']) . "</td>";
              echo "<td>" . htmlspecialchars($row['kelas']) . "</td>";
              echo "<td>" . htmlspecialchars($row['semester']) . "</td>";
              echo '<td><a href="edit_anggota.php?id=' . urlencode($row['id_siswa']) . '" class="btn btn-sm btn-edit">Edit</a></td>';
              echo '<td><a href="hapus_anggota.php?id=' . urlencode($row['id_siswa']) . '" class="btn btn-sm btn-delete" onclick="return confirm(\'Yakin ingin menghapus?\')">Delete</a></td>';
              echo "</tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
