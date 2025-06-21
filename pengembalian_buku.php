<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data dari tabel data_pengembalian
$query = "SELECT id_buku, judul_buku, tanggal_pinjam, tanggal_pengembalian FROM data_pengembalian";
$result = mysqli_query($koneksi, $query);

// Cek apakah query berhasil dieksekusi
if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Handle Update (Ubah) operation - Placeholder for actual update logic
// In a real application, this would involve updating the database and potentially moving the book back to available status.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_return') {
    $id_buku_to_update = $_POST['id_buku'];
    // You might want to update a status in data_pengembalian or move the record
    // to a history table, and update the book's availability status in data_buku.
    // Example: UPDATE data_pengembalian SET status = 'returned' WHERE id_buku = '$id_buku_to_update';
    // Example: UPDATE data_buku SET status_tersedia = 1 WHERE id_buku = '$id_buku_to_update';

    // For demonstration, just show a success message
    // echo "<script>alert('Buku dengan ID " . htmlspecialchars($id_buku_to_update) . " berhasil dikonfirmasi pengembaliannya!');</script>";
    // Optionally, refresh the page to reflect changes if the database was updated
    // header("Location: pengembalian_buku.php");
    // exit();
}


// Handle Delete operation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id_buku'])) {
    $id_buku_to_delete = $_GET['id_buku'];
    
    // Perform the deletion
    $delete_query = "DELETE FROM data_pengembalian WHERE id_buku = '$id_buku_to_delete'";
    if (mysqli_query($koneksi, $delete_query)) {
        // Redirect back to the page to refresh the table
        header("Location: pengembalian_buku.php");
        exit();
    } else {
        echo "<script>alert('Gagal menghapus data: " . mysqli_error($koneksi) . "');</script>";
    }
}

?>

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
        <span class="ms-3">Dashboard siswa</span>
      </div>
    </div>

    <div class="row">
      <nav class="col-md-3 d-none d-md-block sidebar min-vh-100 position-relative">
        <h5 class="pt-4">siswa<br /><small>user</small></h5>
        <a href="lihat_anggota.php">lihat anggota</a>
        <a href="katalog_buku.php">katalog buku</a>
        <a href="peminjaman_buku.php">Peminjaman buku</a>
        <a href="pengembalian_buku.php" class="active">Pengembalian buku</a>
        <a href="denda_keterlambatan.php">denda keterlambatan</a>
        <a href="logout.php">Logout</a>
        <div class="image-box text-center mt-5">
          <img src="assets/Bootstrap_logo.png" alt="icon" />
        </div>
      </nav>

      <main class="col-md-9 col-12 main-content">
        <h4 class="mt-4">Pengembalian Buku</h4>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>ID Buku</th>
                <th>Judul Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Pengembalian</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($row['id_buku']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['judul_buku']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['tanggal_pinjam']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['tanggal_pengembalian']) . "</td>";
                      echo '<td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalUbahPengembalian" 
                                        data-idbuku="' . htmlspecialchars($row['id_buku']) . '" 
                                        data-judulbuku="' . htmlspecialchars($row['judul_buku']) . '">Ubah</button>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(\'' . htmlspecialchars($row['id_buku']) . '\')">Hapus</button>
                            </td>';
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='5' class='text-center'>Tidak ada data pengembalian buku.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>

  <div class="modal fade" id="modalUbahPengembalian" tabindex="-1" aria-labelledby="modalUbahPengembalianLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalUbahPengembalianLabel">Konfirmasi Pengembalian Buku</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Anda yakin ingin mengembalikan buku?
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
          <form method="POST" action="">
            <input type="hidden" name="action" value="update_return">
            <input type="hidden" id="confirmReturnIdBuku" name="id_buku">
            <button type="submit" class="btn btn-primary">Ya</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Script untuk mengisi data buku ke dalam modal saat tombol "Ubah" diklik
    var modalUbahPengembalian = document.getElementById('modalUbahPengembalian');
    modalUbahPengembalian.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget; 

      var idBuku = button.getAttribute('data-idbuku');
      var judulBuku = button.getAttribute('data-judulbuku');

      var modalBukuId = modalUbahPengembalian.querySelector('#modalBukuId');
      var modalBukuJudul = modalUbahPengembalian.querySelector('#modalBukuJudul');
      var confirmReturnIdBuku = modalUbahPengembalian.querySelector('#confirmReturnIdBuku'); // Hidden input for form submission

      modalBukuId.textContent = idBuku;
      modalBukuJudul.textContent = judulBuku;
      confirmReturnIdBuku.value = idBuku; // Set the value for the hidden input
    });

    // JavaScript function to handle delete confirmation
    function confirmDelete(id_buku) {
        if (confirm("Apakah Anda yakin ingin menghapus data pengembalian buku dengan ID: " + id_buku + "?")) {
            window.location.href = 'pengembalian_buku.php?action=delete&id_buku=' + id_buku;
        }
    }
  </script>
</body>
</html>