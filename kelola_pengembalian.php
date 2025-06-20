<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data dari tabel data_anggota
$query = "SELECT * FROM data_pengembalian";
$result = mysqli_query($koneksi, $query);


// Menangani penghapusan data anggota
if (isset($_GET['id'])) {
    $id_siswa = $_GET['id'];
    $delete_query = "DELETE FROM data_pengembalian WHERE id_buku='$id_buku'";
    mysqli_query($koneksi, $delete_query);
    header("Location: kelola_pengembalian.php"); // Redirect setelah penghapusan
    exit();
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
  <div class="row d-md-none bg-dark text-white p-2">
    <div class="col">
      <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">☰ Menu</button>
      <span class="ms-3">Kelola Anggota</span>
    </div>
  </div>

  <div class="row">
      <nav class="col-md-3 d-none d-md-block sidebar min-vh-100 position-relative">
        <h5 class="pt-4">Pustakawan<br /><small>admin</small></h5>
        <a href="kelola_anggota.php">Kelola Anggota</a>
        <a href="kelola_katalog.php">Kelola Katalog Buku</a>
        <a href="kelola_peminjaman.php">Kelola Peminjaman Buku</a>
        <a href="kelola_pengembalian.php">Kelola Pengembalian Buku</a>
        <a href="kelola_denda.php">Kelola Denda</a>
        <a href="logout.php">Logout</a>
        <div class="image-box text-center mt-5">
          <img src="assets/Bootstrap_logo.png" alt="icon" />
        </div>
      </nav>
      
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
          <a href="logout.php">Logout</a>
          <div class="image-box text-center mt-5">
            <img src="assets/Bootstrap_logo.png" alt="icon" />
          </div>
        </div>
      </div>

    <main class="col-md-9 col-12 main-content">
      <h4>Kelola pengembalian buku</h4>
      <div class="d-flex flex-wrap gap-2 align-items-center mb-3 mt-3">
        <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="ketik id buku" style="max-width: 300px;" oninput="filterTable()" />
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped" id="anggotaTable">
          <thead>
            <tr>
              <th>No</th>
              <th>Id buku</th>
              <th>judul buku</th>
              <th>tanggal pinjam</th>
              <th>tanggal pengembalian</th>
              <th>status</th>
              <th>Action</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
              echo "<tr>";
              echo "<td>" . $no++ . "</td>";
              echo "<td>" . htmlspecialchars($row['id_buku']) . "</td>";
              echo "<td>" . htmlspecialchars($row['judul_buku']) . "</td>";
              echo "<td>" . htmlspecialchars($row['tanggal_pinjam']) . "</td>";
              echo "<td>" . htmlspecialchars($row['tanggal_pengembalian']) . "</td>";
              echo "<td>" . htmlspecialchars($row['status']) . "</td>";
              echo '<td>
                      <button class="btn btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#ubahpengembalianModal" data-id="' . htmlspecialchars($row['id_buku']) . '">status</button>
                    </td>';
              echo '<td>
                      <button class="btn btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#hapuspengembalianModal" data-id="' . htmlspecialchars($row['id_buku']) . '">Delete</button>
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

<div class="modal fade" id="hapusAnggotaModal" tabindex="-1" aria-labelledby="hapusAnggotaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="hapusAnggotaModalLabel">Hapus Anggota</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin hapus?</p>
      </div>
      <div class="modal-footer">
        <form id="formHapusAnggota" method="GET" action="">
          <input type="hidden" id="hapusId" name="id">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">tidak</button>
          <button type="submit" class="btn btn-danger">Ya</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Script untuk mengisi data pada modal edit

  // Script untuk mengisi data pada modal hapus
  const deleteButtons = document.querySelectorAll('[data-bs-target="#hapusAnggotaModal"]');
  deleteButtons.forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-id');
      document.getElementById('hapusId').value = id;
    });
  });

  // Function to filter table rows based on search input
  function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('anggotaTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) { // Start from 1 to skip the header row
      const td = tr[i].getElementsByTagName('td');
      let found = false;

      for (let j = 0; j < td.length; j++) {
        if (td[j]) {
          const txtValue = td[j].textContent || td[j].innerText;
          if (txtValue.toLowerCase().indexOf(filter) > -1) {
            found = true;
            break;
          }
        }
      }

      tr[i].style.display = found ? "" : "none"; // Show or hide the row
    }
  }
</script>
</body>
</html>