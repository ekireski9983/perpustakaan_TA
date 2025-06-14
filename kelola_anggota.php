<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data dari tabel data_anggota
$query = "SELECT * FROM data_anggota";
$result = mysqli_query($koneksi, $query);

// Menangani penyimpanan data anggota baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $id_siswa = $_POST['id'];
    $nama = $_POST['nama'];
    $jurusan = $_POST['jurusan'];
    $kelas = $_POST['kelas'];
    $semester = $_POST['semester'];

    $insert_query = "INSERT INTO data_anggota (id_siswa, nama, jurusan, kelas, semester) VALUES ('$id_siswa', '$nama', '$jurusan', '$kelas', '$semester')";
    mysqli_query($koneksi, $insert_query);
    header("Location: kelola_anggota.php"); // Redirect setelah penyimpanan
}

// Menangani pembaruan data anggota
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id_siswa = $_POST['editId'];
    $nama = $_POST['editNama'];
    $jurusan = $_POST['editJurusan'];
    $kelas = $_POST['editKelas'];
    $semester = $_POST['editSemester'];

    $update_query = "UPDATE data_anggota SET nama='$nama', jurusan='$jurusan', kelas='$kelas', semester='$semester' WHERE id_siswa='$id_siswa'";
    mysqli_query($koneksi, $update_query);
    header("Location: kelola_anggota.php"); // Redirect setelah pembaruan
}
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

    <!-- Main Content -->
    <main class="col-md-9 col-12 main-content">
      <h4>Kelola Anggota</h4>
      <div class="d-flex flex-wrap gap-2 align-items-center mb-3 mt-3">
        <input type="text" class="form-control form-control-md me-2" placeholder="Nama anggota" style="max-width: 300px;" />
        <button class="btn btn-tambah btn-md" data-bs-toggle="modal" data-bs-target="#tambahAnggotaModal">Tambah Anggota</button>
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
              <th>Action</th>
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
              echo '<td>
                      <button class="btn btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#editAnggotaModal" data-id="' . htmlspecialchars($row['id_siswa']) . '" data-nama="' . htmlspecialchars($row['nama']) . '" data-jurusan="' . htmlspecialchars($row['jurusan']) . '" data-kelas="' . htmlspecialchars($row['kelas']) . '" data-semester="' . htmlspecialchars($row['semester']) . '">Edit</button>
                      <a href="hapus_anggota.php?id=' . urlencode($row['id_siswa']) . '" class="btn btn-sm btn-delete" onclick="return confirm(\'Yakin ingin menghapus?\')">Delete</a>
                    </td>';
              echo "</tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<!-- Modal Tambah Anggota -->
<div class="modal fade" id="tambahAnggotaModal" tabindex="-1" aria-labelledby="tambahAnggotaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tambahAnggotaModalLabel">Tambah Anggota</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formTambahAnggota" method="POST" action="">
          <input type="hidden" name="action" value="tambah">
          <div class="mb-3">
            <label for="id" class="form-label">id siswa</label>
            <input type="text" class="form-control" name="id" required>
          </div>
          <div class="mb-3">
            <label for="nama" class="form-label">Nama Siswa</label>
            <input type="text" class="form-control" name="nama" required>
          </div>
          <div class="mb-3">
            <label for="jurusan" class="form-label">Jurusan</label>
            <input type="text" class="form-control" name="jurusan" required>
          </div>
          <div class="mb-3">
            <label for="kelas" class="form-label">Kelas</label>
            <input type="text" class="form-control" name="kelas" required>
          </div>
          <div class="mb-3">
            <label for="semester" class="form-label">Semester</label>
            <input type="text" class="form-control" name="semester" required>
          </div>
          <button type="submit" class="btn btn-primary">tambah anggota</button>
          <button type="reset" class="btn btn-danger">reset</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Anggota -->
<div class="modal fade" id="editAnggotaModal" tabindex="-1" aria-labelledby="editAnggotaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editAnggotaModalLabel">Edit Anggota</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formEditAnggota" method="POST" action="">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" id="editId" name="editId">
          <div class="mb-3">
            <label for="editid" class="form-label">id Siswa</label>
            <input type="text" class="form-control" id="editid" name="editid" required>
          </div>
          <div class="mb-3">
            <label for="editNama" class="form-label">Nama Siswa</label>
            <input type="text" class="form-control" id="editNama" name="editNama" required>
          </div>
          <div class="mb-3">
            <label for="editJurusan" class="form-label">Jurusan</label>
            <input type="text" class="form-control" id="editJurusan" name="editJurusan" required>
          </div>
          <div class="mb-3">
            <label for="editKelas" class="form-label">Kelas</label>
            <input type="text" class="form-control" id="editKelas" name="editKelas" required>
          </div>
          <div class="mb-3">
            <label for="editSemester" class="form-label">Semester</label>
            <input type="text" class="form-control" id="editSemester" name="editSemester" required>
          </div>
          <button type="submit" class="btn btn-primary">edit anggota</button>
          <button type="reset" class="btn btn-danger">reset</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Script untuk mengisi data pada modal edit
  const editButtons = document.querySelectorAll('[data-bs-target="#editAnggotaModal"]');
  editButtons.forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-id');
      const nama = button.getAttribute('data-nama');
      const jurusan = button.getAttribute('data-jurusan');
      const kelas = button.getAttribute('data-kelas');
      const semester = button.getAttribute('data-semester');

      document.getElementById('editId').value = id;
      document.getElementById('editNama').value = nama;
      document.getElementById('editJurusan').value = jurusan;
      document.getElementById('editKelas').value = kelas;
      document.getElementById('editSemester').value = semester;
    });
  });
</script>
</body>
</html>
