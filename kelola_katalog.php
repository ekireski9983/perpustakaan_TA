<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data dari tabel data_buku
$query = "SELECT * FROM katalog_buku";
$result = mysqli_query($koneksi, $query);

// Menangani penyimpanan data buku baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $id_buku = $_POST['id'];
    $isbn = $_POST['isbn'];
    $judul_buku = $_POST['judul_buku'];
    $nama_penulis = $_POST['nama_penulis'];
    $nama_penerbit = $_POST['nama_penerbit'];
    $jumlah_halaman = $_POST['jumlah_halaman'];
    $foto = $_POST['foto']; // Assuming you handle file uploads separately

    // Insert query
    $insert_query = "INSERT INTO data_buku (id_buku, isbn, nama_penulis, nama_penerbit, jumlah_halaman, foto) VALUES ('$id_buku', '$isbn', '$nama_penulis', '$nama_penerbit', '$jumlah_halaman', '$foto')";
    
    if (mysqli_query($koneksi, $insert_query)) {
        header("Location: kelola_buku.php"); // Redirect setelah penyimpanan
        exit();
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

// Menangani pembaruan data buku
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id_buku = $_POST['editId'];
    $isbn = $_POST['editIsbn'];
    $judul_buku = $_POST['judul_buku'];
    $nama_penulis = $_POST['editNamaPenulis'];
    $nama_penerbit = $_POST['editNamaPenerbit'];
    $jumlah_halaman = $_POST['editJumlahHalaman'];
    $foto = $_POST['editFoto']; // Assuming you handle file uploads separately

    $update_query = "UPDATE data_buku SET isbn='$isbn', nama_penulis='$nama_penulis', nama_penerbit='$nama_penerbit', jumlah_halaman='$jumlah_halaman', foto='$foto' WHERE id_buku='$id_buku'";
    
    if (mysqli_query($koneksi, $update_query)) {
        header("Location: kelola_buku.php"); // Redirect setelah pembaruan
        exit();
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

// Menangani penghapusan data buku
if (isset($_GET['id'])) {
    $id_buku = $_GET['id'];
    $delete_query = "DELETE FROM data_buku WHERE id_buku='$id_buku'";
    mysqli_query($koneksi, $delete_query);
    header("Location: kelola_buku.php"); // Redirect setelah penghapusan
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola katalog Buku</title>
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
      <span class="ms-3">Kelola katalog Buku</span>
    </div>
  </div>

  <div class="row">
      <!-- Sidebar for md and up -->
      <nav class="col-md-3 d-none d-md-block sidebar min-vh-100 position-relative">
        <h5 class="pt-4">Pustakawan<br /><small>admin</small></h5>
        <a href="kelola_anggota.php">kelola anggota</a>
        <a href="kelola_katalog.php">kelola katalog buku</a>
        <a href="kelola_peminjaman.php">kelola Peminjaman buku</a>
        <a href="kelola_pengembalian.php">kelola Pengembalian buku</a>
        <a href="kelola_denda.php">kelola denda</a>
        <a href="logout.php">Logout</a>
        <div class="image-box text-center mt-5">
          <img src="assets/Bootstrap_logo.png" alt="icon" />
        </div>
      </nav>

    <!-- Main Content -->
    <main class="col-md-9 col-12 main-content">
      <h4>Kelola katalog Buku</h4>
      <div class="d-flex flex-wrap gap-2 align-items-center mb-3 mt-3">
        <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="Nama buku" style="max-width: 300px;" oninput="filterTable()" />
        <button class="btn btn-tambah btn-md" data-bs-toggle="modal" data-bs-target="#tambahAnggotaModal">Tambah Buku</button>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped" id="anggotaTable">
          <thead>
            <tr>
              <th>No</th>
              <th>ID Buku</th>
              <th>ISBN</th>
              <th>judul buku</th>
              <th>Nama Penulis</th>
              <th>Nama Penerbit</th>
              <th>Jumlah Halaman</th>
              <th>Foto</th>
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
              echo "<td>" . htmlspecialchars($row['isbn']) . "</td>";
              echo "<td>" . htmlspecialchars($row['judul_buku']) . "</td>";
              echo "<td>" . htmlspecialchars($row['nama_penulis']) . "</td>";
              echo "<td>" . htmlspecialchars($row['nama_penerbit']) . "</td>";
              echo "<td>" . htmlspecialchars($row['jumlah_halaman']) . "</td>";
              echo "<td><img src='" . htmlspecialchars($row['foto']) . "' alt='Foto Buku' style='width: 50px; height: auto;'></td>";
              echo '<td>
                      <button class="btn btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#editAnggotaModal" data-id="' . htmlspecialchars($row['id_buku']) . '" data-isbn="' . htmlspecialchars($row['isbn']) . '" data-nama_penulis="' . htmlspecialchars($row['nama_penulis']) . '" data-nama_penerbit="' . htmlspecialchars($row['nama_penerbit']) . '" data-jumlah_halaman="' . htmlspecialchars($row['jumlah_halaman']) . '" data-foto="' . htmlspecialchars($row['foto']) . '">Edit</button>
                    </td>';
              echo '<td>
                    <button class="btn btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#hapusAnggotaModal" data-id="' . htmlspecialchars($row['id_buku']) . '">Delete</button>
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

<!-- Modal Tambah Buku -->
<div class="modal fade" id="tambahAnggotaModal" tabindex="-1" aria-labelledby="tambahAnggotaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tambahAnggotaModalLabel">Tambah Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formTambahAnggota" method="POST" action="">
          <input type="hidden" name="action" value="tambah">
          <div class="mb-3">
            <label for="id" class="form-label">ID Buku</label>
            <input type="text" class="form-control" name="id" required>
          </div>
          <div class="mb-3">
            <label for="isbn" class="form-label">ISBN</label>
            <input type="text" class="form-control" name="isbn" required>
          </div>
          <div class="mb-3">
            <label for="judul_buku" class="form-label">judul buku</label>
            <input type="text" class="form-control" name="judul_buku" required>
          </div>
          <div class="mb-3">
            <label for="nama_penerbit" class="form-label">Nama Penerbit</label>
            <input type="text" class="form-control" name="nama_penerbit" required>
          </div>
          <div class="mb-3">
            <label for="jumlah_halaman" class="form-label">Jumlah Halaman</label>
            <input type="number" class="form-control" name="jumlah_halaman" required>
          </div>
          <div class="mb-3">
            <label for="foto" class="form-label">Foto</label>
            <input type="file" class="form-control" name="foto" accept="image/*" required>
          </div>
          <button type="submit" class="btn btn-primary">Tambah Buku</button>
          <button type="reset" class="btn btn-danger">Reset</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Buku -->
<div class="modal fade" id="editAnggotaModal" tabindex="-1" aria-labelledby="editAnggotaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editAnggotaModalLabel">Edit Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formEditAnggota" method="POST" action="">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" id="editId" name="editId">
          <div class="mb-3">
            <label for="editid" class="form-label">ID Buku</label>
            <input type="text" class="form-control" id="editid" name="editid" required>
          </div>
          <div class="mb-3">
            <label for="editIsbn" class="form-label">ISBN</label>
            <input type="text" class="form-control" id="editIsbn" name="editIsbn" required>
          </div>
          <div class="mb-3">
            <label for="editJudulbuku" class="form-label">judul buku</label>
            <input type="text" class="form-control" id="editJudulbuku" name="editJudulbuku" required>
          </div>
          <div class="mb-3">
            <label for="editNamaPenulis" class="form-label">Nama Penulis</label>
            <input type="text" class="form-control" id="editNamaPenulis" name="editNamaPenulis" required>
          </div>
          <div class="mb-3">
            <label for="editNamaPenerbit" class="form-label">Nama Penerbit</label>
            <input type="text" class="form-control" id="editNamaPenerbit" name="editNamaPenerbit" required>
          </div>
          <div class="mb-3">
            <label for="editJumlahHalaman" class="form-label">Jumlah Halaman</label>
            <input type="number" class="form-control" id="editJumlahHalaman" name="editJumlahHalaman" required>
          </div>
          <label for="editFoto" class="form-label">Foto</label>
            <input type="file" class="form-control" id="editFoto" name="editFoto" accept="image/*" required>
          </div>
          <button type="submit" class="btn btn-primary">Edit Buku</button>
          <button type="reset" class="btn btn-danger">Reset</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Hapus Buku -->
<div class="modal fade" id="hapusAnggotaModal" tabindex="-1" aria-labelledby="hapusAnggotaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="hapusAnggotaModalLabel">Hapus Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin hapus?</p>
      </div>
      <div class="modal-footer">
        <form id="formHapusAnggota" method="GET" action="">
          <input type="hidden" id="hapusId" name="id">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Script untuk mengisi data pada modal edit
  const editButtons = document.querySelectorAll('[data-bs-target="#editAnggotaModal"]');
  editButtons.forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-id');
      const isbn = button.getAttribute('data-isbn');
      const judul_buku = button.getAttribute('data-judul_buku');
      const nama_penulis = button.getAttribute('data-nama_penulis');
      const nama_penerbit = button.getAttribute('data-nama_penerbit');
      const jumlah_halaman = button.getAttribute('data-jumlah_halaman');
      const foto = button.getAttribute('data-foto');

      document.getElementById('editId').value = id; // Hidden field for ID
      document.getElementById('editid').value = id; // Set ID in the input field
      document.getElementById('editIsbn').value = isbn;
      document.getElementById('editJudulbuku').value = judul_buku;
      document.getElementById('editNamaPenulis').value = nama_penulis;
      document.getElementById('editNamaPenerbit').value = nama_penerbit;
      document.getElementById('editJumlahHalaman').value = jumlah_halaman;
      document.getElementById('editFoto').value = foto;
    });
  });

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
