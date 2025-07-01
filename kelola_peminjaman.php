<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data dari tabel data_pinjam
$query = "SELECT * FROM data_pinjam";
$result = mysqli_query($koneksi, $query);

// Menangani penyimpanan data peminjaman baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    // Escape all input values to prevent SQL injection (BASIC SECURITY, USE PREPARED STATEMENTS FOR PRODUCTION)
    $id_buku = mysqli_real_escape_string($koneksi, $_POST['id']);
    $isbn = mysqli_real_escape_string($koneksi, $_POST['isbn']);
    $judul_buku = mysqli_real_escape_string($koneksi, $_POST['judul_buku']);
    $nama_penulis = mysqli_real_escape_string($koneksi, $_POST['nama_penulis']);
    $nama_penerbit = mysqli_real_escape_string($koneksi, $_POST['nama_penerbit']);
    $jumlah_halaman = mysqli_real_escape_string($koneksi, $_POST['jumlah_halaman']);
    $tanggal_pinjam = mysqli_real_escape_string($koneksi, $_POST['tanggal_pinjam']);
    $tanggal_pengembalian = mysqli_real_escape_string($koneksi, $_POST['tanggal_pengembalian']);

    // Menangani upload foto
    $foto = $_FILES['foto'];
    $foto_name = $foto['name'];
    $foto_tmp = $foto['tmp_name'];
    $foto_size = $foto['size'];
    $foto_error = $foto['error'];

    // Validasi format gambar
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $foto_ext = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));

    if (in_array($foto_ext, $allowed_extensions) && $foto_error === 0) {
        // Tentukan direktori untuk menyimpan gambar
        $foto_destination = 'upload/' . uniqid('', true) . '.' . $foto_ext;

        // Pindahkan file ke direktori
        if (move_uploaded_file($foto_tmp, $foto_destination)) {
            // Start a transaction for atomicity
            mysqli_begin_transaction($koneksi);

            // Insert query into data_pinjam table
            // Ensure column names match your data_pinjam table structure
            $insert_pinjam_query = "INSERT INTO data_pinjam (id_buku, isbn, judul_buku, nama_penulis, nama_penerbit, jumlah_halaman, foto, tanggal_pinjam, tanggal_pengembalian)
                                    VALUES ('$id_buku', '$isbn', '$judul_buku', '$nama_penulis', '$nama_penerbit', '$jumlah_halaman', '$foto_destination', '$tanggal_pinjam', '$tanggal_pengembalian')";

            if (mysqli_query($koneksi, $insert_pinjam_query)) {
                // Insert query into data_pengembalian table
                // Include 'status' column and set it to 'belum dikembalikan'
                $insert_pengembalian_query = "INSERT INTO data_pengembalian (id_buku, judul_buku, tanggal_pinjam, tanggal_pengembalian, status_pengembalian)
                                              VALUES ('$id_buku', '$judul_buku', '$tanggal_pinjam', '$tanggal_pengembalian', 'belum dikembalikan')";

                if (mysqli_query($koneksi, $insert_pengembalian_query)) {
                    mysqli_commit($koneksi); // Commit the transaction if both inserts are successful
                    header("Location: kelola_peminjaman.php"); // Redirect after saving
                    exit();
                } else {
                    mysqli_rollback($koneksi); // Rollback if pengembalian insert fails
                    echo "Error inserting into data_pengembalian: " . mysqli_error($koneksi);
                }
            } else {
                mysqli_rollback($koneksi); // Rollback if pinjam insert fails
                echo "Error inserting into data_pinjam: " . mysqli_error($koneksi);
            }
        } else {
            echo "Error: Gagal memindahkan file yang diunggah.";
        }
    } else {
        echo "Format file tidak valid atau terjadi kesalahan saat upload. Error code: " . $foto_error;
    }
}

// Menangani penghapusan data peminjaman
if (isset($_GET['id'])) {
    $id_buku = mysqli_real_escape_string($koneksi, $_GET['id']);

    // Start a transaction for atomicity
    mysqli_begin_transaction($koneksi);

    // Optional: Get the photo path before deleting the record to delete the file as well
    $get_photo_query = "SELECT foto FROM data_pinjam WHERE id_buku='$id_buku'";
    $photo_result = mysqli_query($koneksi, $get_photo_query);
    if ($photo_result && mysqli_num_rows($photo_result) > 0) {
        $row = mysqli_fetch_assoc($photo_result);
        $photo_path = $row['foto'];
        if (file_exists($photo_path)) {
            unlink($photo_path); // Delete the actual photo file
        }
    }

    // Delete from data_pengembalian first to avoid foreign key constraints if they exist
    $delete_pengembalian_query = "DELETE FROM data_pengembalian WHERE id_buku='$id_buku'";
    if (mysqli_query($koneksi, $delete_pengembalian_query)) {
        $delete_pinjam_query = "DELETE FROM data_pinjam WHERE id_buku='$id_buku'";
        if (mysqli_query($koneksi, $delete_pinjam_query)) {
            mysqli_commit($koneksi); // Commit if both deletes are successful
            header("Location: kelola_peminjaman.php"); // Redirect after deletion
            exit();
        } else {
            mysqli_rollback($koneksi); // Rollback if pinjam delete fails
            echo "Error deleting from data_pinjam: " . mysqli_error($koneksi);
        }
    } else {
        mysqli_rollback($koneksi); // Rollback if pengembalian delete fails
        echo "Error deleting from data_pengembalian: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola Peminjaman Buku</title>
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
      <span class="ms-3">Kelola Peminjaman Buku</span>
    </div>
  </div>

 <div class="row">
      <nav class="col-md-3 d-none d-md-block sidebar min-vh-100 position-relative">
        <h5 class="pt-4">Pustakawan<br /><small>admin</small></h5>
        <a href="kelola_anggota.php">Kelola Anggota</a>
        <a href="kelola_katalog.php">Kelola Katalog Buku</a>
        <a href="kelola_peminjaman.php">Kelola Peminjaman Buku</a>
        <a href="kelola_pengembalian.php">Kelola Pengembalian Buku</a>
        <a href="denda_kelola.php">Kelola Denda</a>
        <a href="logout.php">Logout</a>
        <div class="image-box text-center mt-5">
          <img src="assets/logo_sekolah.png" alt="icon" />
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
          <a href="denda_kelola.php">kelola denda</a>
          <a href="logout.php">Logout</a>
          <div class="image-box text-center mt-5">
            <img src="assets/logo_sekolah.png" alt="icon" />
          </div>
        </div>
      </div>

    <main class="col-md-9 col-12 main-content">
      <h4>Kelola Peminjaman Buku</h4>
      <div class="d-flex flex-wrap gap-2 align-items-center mb-3 mt-3">
        <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="Nama buku" style="max-width: 300px;" oninput="filterTable()" />
        <button class="btn btn-tambah btn-md" data-bs-toggle="modal" data-bs-target="#tambahPeminjamanModal">Tambah Peminjaman</button>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped" id="PeminjamanTable"> <thead>
            <tr>
              <th>No</th>
              <th>ID Buku</th>
              <th>ISBN</th>
              <th>Judul Buku</th>
              <th>Nama Penulis</th>
              <th>Nama Penerbit</th>
              <th>Jumlah Halaman</th>
              <th>Foto buku</th>
              <th>Tanggal Pinjam</th>
              <th>Tanggal Pengembalian</th>
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
              echo "<td>" . htmlspecialchars($row['tanggal_pinjam']) . "</td>";
              echo "<td>" . htmlspecialchars($row['tanggal_pengembalian']) . "</td>";
              echo '<td>
                                <button class="btn btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#hapusPeminjamanModal" data-id="' . htmlspecialchars($row['id_buku']) . '">Delete</button>
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

<div class="modal fade" id="tambahPeminjamanModal" tabindex="-1" aria-labelledby="tambahPeminjamanModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tambahPeminjamanModalLabel">Tambah Peminjaman Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formTambahPeminjaman" method="POST" action="" enctype="multipart/form-data"> <input type="hidden" name="action" value="tambah">
          <div class="mb-3">
            <label for="id" class="form-label">ID Buku</label>
            <input type="text" class="form-control" name="id" required>
          </div>
          <div class="mb-3">
            <label for="isbn" class="form-label">ISBN</label>
            <input type="text" class="form-control" name="isbn" required>
          </div>
          <div class="mb-3">
            <label for="judul_buku" class="form-label">Judul Buku</label>
            <input type="text" class="form-control" name="judul_buku" required>
          </div>
          <div class="mb-3">
            <label for="nama_penulis" class="form-label">Nama Penulis</label>
            <input type="text" class="form-control" name="nama_penulis" required>
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
          <div class="mb-3">
            <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
            <input type="date" class="form-control" name="tanggal_pinjam" required>
          </div>
          <div class="mb-3">
            <label for="tanggal_pengembalian" class="form-label">Tanggal Pengembalian</label>
            <input type="date" class="form-control" name="tanggal_pengembalian" required>
          </div>
          <button type="submit" class="btn btn-primary">Tambah Peminjaman</button>
          <button type="reset" class="btn btn-danger">Reset</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="hapusPeminjamanModal" tabindex="-1" aria-labelledby="hapusPeminjamanModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="hapusPeminjamanModalLabel">Hapus Peminjaman</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus data peminjaman ini?</p>
      </div>
      <div class="modal-footer">
        <form id="formHapusPeminjaman" method="GET" action="">
          <input type="hidden" id="hapusId" name="id">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Script untuk mengisi data pada modal hapus
  const deleteButtons = document.querySelectorAll('[data-bs-target="#hapusPeminjamanModal"]');
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
    const table = document.getElementById('PeminjamanTable'); // Corrected ID
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) { // Start from 1 to skip the header row
      const td = tr[i].getElementsByTagName('td');
      let found = false;

      // Search across relevant columns (e.g., Judul Buku, Nama Penulis, Nama Penerbit)
      // Adjust column indices as needed based on your table structure
      const judulBukuCol = td[3]; // Assuming Judul Buku is the 4th column (index 3)
      const namaPenulisCol = td[4]; // Assuming Nama Penulis is the 5th column (index 4)
      const namaPenerbitCol = td[5]; // Assuming Nama Penerbit is the 6th column (index 5)

      if (judulBukuCol && judulBukuCol.textContent.toLowerCase().indexOf(filter) > -1) {
        found = true;
      } else if (namaPenulisCol && namaPenulisCol.textContent.toLowerCase().indexOf(filter) > -1) {
        found = true;
      } else if (namaPenerbitCol && namaPenerbitCol.textContent.toLowerCase().indexOf(filter) > -1) {
        found = true;
      }

      tr[i].style.display = found ? "" : "none"; // Show or hide the row
    }
  }
</script>
</body>
</html>