<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data dari tabel data_pengembalian
// Make sure 'status' column exists in data_pengembalian table.
// If not, you'll need to add it: ALTER TABLE data_pengembalian ADD COLUMN status VARCHAR(50) DEFAULT 'Belum Dikembalikan';
$query = "SELECT * FROM data_pengembalian";
$result = mysqli_query($koneksi, $query);

// Menangani perubahan status pengembalian
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $id_buku_to_update = mysqli_real_escape_string($koneksi, $_POST['id_buku_status']);
    $new_status = mysqli_real_escape_string($koneksi, $_POST['new_status']);

    $update_query = "UPDATE data_pengembalian SET status='$new_status' WHERE id_buku='$id_buku_to_update'";
    if (mysqli_query($koneksi, $update_query)) {
        header("Location: kelola_pengembalian.php"); // Redirect after update
        exit();
    } else {
        echo "Error updating status: " . mysqli_error($koneksi);
    }
}

// Menangani penghapusan data pengembalian
if (isset($_GET['id'])) {
    $id_buku_to_delete = mysqli_real_escape_string($koneksi, $_GET['id']);
    // You might also want to update the status in data_pinjam if a book is deleted from pengembalian
    // For simplicity, this example just deletes from data_pengembalian.
    // Consider adding a transaction here if you need to update other tables.

    $delete_query = "DELETE FROM data_pengembalian WHERE id_buku='$id_buku_to_delete'";
    if (mysqli_query($koneksi, $delete_query)) {
        header("Location: kelola_pengembalian.php"); // Redirect after deletion
        exit();
    } else {
        echo "Error deleting data: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola Pengembalian Buku</title>
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
      <span class="ms-3">Kelola Pengembalian Buku</span>
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
      <h4>Kelola Pengembalian Buku</h4>
      <div class="d-flex flex-wrap gap-2 align-items-center mb-3 mt-3">
        <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="ketik id buku" style="max-width: 300px;" oninput="filterTable()" />
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped" id="pengembalianTable"> <thead>
            <tr>
              <th>No</th>
              <th>Id Buku</th>
              <th>Judul Buku</th>
              <th>Tanggal Pinjam</th>
              <th>Tanggal Pengembalian</th>
              <th>Status</th>
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
                        <button class="btn btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#ubahStatusPengembalianModal" 
                                data-id="' . htmlspecialchars($row['id_buku']) . '" data-status="' . htmlspecialchars($row['status']) . '">
                            Ubah Status
                        </button>
                    </td>';
              echo '<td>
                        <button class="btn btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#hapusPengembalianModal" data-id="' . htmlspecialchars($row['id_buku']) . '">
                            Delete
                        </button>
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

<div class="modal fade" id="ubahStatusPengembalianModal" tabindex="-1" aria-labelledby="ubahStatusPengembalianModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ubahStatusPengembalianModalLabel">Status Pengembalian</h5>
      </div>
      <div class="modal-body">
      <form id="formUbahStatusPengembalian" method="POST" action="">
      <input type="hidden" name="action" value="update_status">
      <input type="hidden" id="idBukuStatus" name="id_buku_status">
       <div class="d-flex justify-content-between"> <button type="submit" name="new_status" value="Belum Dikembalikan" class="btn btn-danger">Belum Dikembalikan</button>
      <button type="submit" name="new_status" value="Sudah Dikembalikan" class="btn btn-success">Sudah Dikembalikan</button>
      </div>
     </form>
     </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="hapusPengembalianModal" tabindex="-1" aria-labelledby="hapusPengembalianModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="hapusPengembalianModalLabel">Hapus Data Pengembalian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus data pengembalian ini?</p>
      </div>
      <div class="modal-footer">
        <form id="formHapusPengembalian" method="GET" action="">
          <input type="hidden" id="hapusIdPengembalian" name="id">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
          <button type="submit" class="btn btn-danger">Ya, Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Script untuk mengisi data pada modal ubah status
  const ubahStatusButtons = document.querySelectorAll('[data-bs-target="#ubahStatusPengembalianModal"]'); 
  ubahStatusButtons.forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-id');
      const currentStatus = button.getAttribute('data-status');
      document.getElementById('modalBookId').textContent = id;
      document.getElementById('idBukuStatus').value = id;
    });
  });

  // Script untuk mengisi data pada modal hapus (updated ID for consistency)
  const deleteButtonsPengembalian = document.querySelectorAll('[data-bs-target="#hapusPengembalianModal"]');
  deleteButtonsPengembalian.forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-id');
      document.getElementById('hapusIdPengembalian').value = id;
    });
  });

  // Function to filter table rows based on search input
  function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('pengembalianTable'); // Corrected table ID
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) { // Start from 1 to skip the header row
      const td = tr[i].getElementsByTagName('td');
      let found = false;

      // Search by ID Buku (column index 1) or Judul Buku (column index 2)
      const idBukuCol = td[1]; 
      const judulBukuCol = td[2]; 

      if (idBukuCol && idBukuCol.textContent.toLowerCase().indexOf(filter) > -1) {
        found = true;
      } else if (judulBukuCol && judulBukuCol.textContent.toLowerCase().indexOf(filter) > -1) {
        found = true;
      }

      tr[i].style.display = found ? "" : "none"; // Show or hide the row
    }
  }
</script>
</body>
</html>