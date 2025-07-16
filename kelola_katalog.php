<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Fungsi untuk mengupload foto
function uploadFoto($file_data, $target_dir = 'upload/') {
    $foto_name = $file_data['name'];
    $foto_tmp = $file_data['tmp_name'];
    $foto_error = $file_data['error'];

    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $foto_ext = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));

    if (!in_array($foto_ext, $allowed_extensions)) {
        return ['success' => false, 'message' => "Format file tidak valid. Hanya JPG, JPEG, PNG yang diizinkan."];
    }
    if ($foto_error !== 0) {
        return ['success' => false, 'message' => "Terjadi kesalahan saat upload file. Error code: " . $foto_error];
    }

    // Pastikan direktori 'upload/' ada dan dapat ditulis
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Buat direktori jika belum ada
    }

    $foto_destination = $target_dir . uniqid('buku_', true) . '.' . $foto_ext;

    if (move_uploaded_file($foto_tmp, $foto_destination)) {
        return ['success' => true, 'path' => $foto_destination];
    } else {
        return ['success' => false, 'message' => "Gagal memindahkan file yang diunggah."];
    }
}


// Ambil data dari tabel data_buku untuk ditampilkan
$query_select_buku = "SELECT * FROM data_buku";
$result = mysqli_query($koneksi, $query_select_buku);

// Menangani penyimpanan data buku baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $id_buku = $_POST['id'];
    $isbn = $_POST['isbn'];
    $judul_buku = $_POST['judul_buku'];
    $nama_penulis = $_POST['nama_penulis'];
    $nama_penerbit = $_POST['nama_penerbit'];
    $jumlah_halaman = $_POST['jumlah_halaman'];

    // Menangani upload foto
    $upload_result = uploadFoto($_FILES['foto']);

    if ($upload_result['success']) {
        $foto_destination = $upload_result['path'];

        // Mulai transaksi untuk memastikan kedua INSERT berhasil atau tidak sama sekali
        mysqli_begin_transaction($koneksi);

        try {
            // 1. Insert ke tabel data_buku
            $insert_buku_query = "INSERT INTO data_buku (id_buku, isbn, judul_buku, nama_penulis, nama_penerbit, jumlah_halaman, foto) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_buku = mysqli_prepare($koneksi, $insert_buku_query);

            if ($stmt_buku) {
                mysqli_stmt_bind_param($stmt_buku, "sssssis", $id_buku, $isbn, $judul_buku, $nama_penulis, $nama_penerbit, $jumlah_halaman, $foto_destination);
                if (!mysqli_stmt_execute($stmt_buku)) {
                    throw new Exception("Error inserting data_buku: " . mysqli_stmt_error($stmt_buku));
                }
                mysqli_stmt_close($stmt_buku);
            } else {
                throw new Exception("Error preparing data_buku statement: " . mysqli_error($koneksi));
            }

            // 2. Insert ke tabel data_list_buku (dengan tanggal_ditambahkan otomatis)
            $insert_list_buku_query = "INSERT INTO data_list_buku (id_buku, isbn, judul_buku, nama_penulis, nama_penerbit, tanggal_ditambahkan) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt_list_buku = mysqli_prepare($koneksi, $insert_list_buku_query);

            if ($stmt_list_buku) {
                mysqli_stmt_bind_param($stmt_list_buku, "sssss", $id_buku, $isbn, $judul_buku, $nama_penulis, $nama_penerbit);
                if (!mysqli_stmt_execute($stmt_list_buku)) {
                    throw new Exception("Error inserting data_list_buku: " . mysqli_stmt_error($stmt_list_buku));
                }
                mysqli_stmt_close($stmt_list_buku);
            } else {
                throw new Exception("Error preparing data_list_buku statement: " . mysqli_error($koneksi));
            }

            // Jika semua berhasil, commit transaksi
            mysqli_commit($koneksi);
            header("Location: kelola_katalog.php"); // Redirect setelah penyimpanan
            exit();

        } catch (Exception $e) {
            // Jika ada kesalahan, rollback transaksi
            mysqli_rollback($koneksi);
            // Hapus file foto yang mungkin sudah terupload jika transaksi gagal
            if (file_exists($foto_destination)) {
                unlink($foto_destination);
            }
            echo "Error saat menambahkan buku: " . $e->getMessage();
        }

    } else {
        echo "Error upload foto: " . $upload_result['message'];
    }
}

// Menangani pembaruan data buku
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    // Tangkap ID lama (sebelum diubah) dari hidden input atau sesi
    $old_id_buku = $_POST['editId'];
    // Tangkap ID baru (setelah diubah) dari input form
    $new_id_buku = $_POST['newId']; 
    
    $isbn = $_POST['editIsbn'];
    $judul_buku = $_POST['editJudulbuku'];
    $nama_penulis = $_POST['editNamaPenulis'];
    $nama_penerbit = $_POST['editNamaPenerbit'];
    $jumlah_halaman = $_POST['editJumlahHalaman'];
    $foto = $_POST['editfoto']; // Ini sepertinya tidak digunakan karena foto dihandle via $_FILES

    $foto_destination = null; // Inisialisasi path foto baru
    $current_foto_path = null; // Path foto lama

    // Dapatkan path foto lama sebelum update untuk kemungkinan penghapusan
    $get_old_foto_query = "SELECT foto FROM data_buku WHERE id_buku = ?";
    $stmt_old_foto = mysqli_prepare($koneksi, $get_old_foto_query);
    if ($stmt_old_foto) {
        mysqli_stmt_bind_param($stmt_old_foto, "s", $old_id_buku);
        mysqli_stmt_execute($stmt_old_foto);
        $res_old_foto = mysqli_stmt_get_result($stmt_old_foto);
        if ($row_old_foto = mysqli_fetch_assoc($res_old_foto)) {
            $current_foto_path = $row_old_foto['foto'];
        }
        mysqli_stmt_close($stmt_old_foto);
    } else {
        echo "Error preparing old photo query: " . mysqli_error($koneksi);
        exit();
    }


    // Menangani upload foto baru jika ada
    if (isset($_FILES['editFoto']) && $_FILES['editFoto']['error'] === 0) {
        $upload_result = uploadFoto($_FILES['editFoto']);
        if ($upload_result['success']) {
            $foto_destination = $upload_result['path'];
            // Hapus foto lama jika ada foto baru berhasil diunggah
            if ($current_foto_path && file_exists($current_foto_path)) {
                unlink($current_foto_path);
            }
        } else {
            echo "Error upload foto untuk pembaruan: " . $upload_result['message'];
            exit();
        }
    } else {
        // Jika tidak ada foto baru diupload, gunakan foto yang sudah ada
        $foto_destination = $current_foto_path;
    }

    // Mulai transaksi untuk memastikan kedua UPDATE berhasil atau tidak sama sekali
    mysqli_begin_transaction($koneksi);

    try {
        // 1. Update tabel data_buku
        // Pastikan Anda memperbarui ID_Buku di sini juga jika berubah
        $update_buku_query = "UPDATE data_buku SET id_buku=?, isbn=?, judul_buku=?, nama_penulis=?, nama_penerbit=?, jumlah_halaman=?, foto=? WHERE id_buku=?";
        $stmt_buku = mysqli_prepare($koneksi, $update_buku_query);
        if ($stmt_buku) {
            mysqli_stmt_bind_param($stmt_buku, "sssssiss", $new_id_buku, $isbn, $judul_buku, $nama_penulis, $nama_penerbit, $jumlah_halaman, $foto_destination, $old_id_buku);
            if (!mysqli_stmt_execute($stmt_buku)) {
                throw new Exception("Error updating data_buku: " . mysqli_stmt_error($stmt_buku));
            }
            mysqli_stmt_close($stmt_buku);
        } else {
            throw new Exception("Error preparing data_buku update statement: " . mysqli_error($koneksi));
        }

        // 2. Update tabel data_list_buku
        // Perbarui juga ID_Buku di tabel kedua
        $update_list_buku_query = "UPDATE data_list_buku SET id_buku=?, isbn=?, judul_buku=?, nama_penulis=?, nama_penerbit=? WHERE id_buku=?";
        $stmt_list_buku = mysqli_prepare($koneksi, $update_list_buku_query);
        if ($stmt_list_buku) {
            mysqli_stmt_bind_param($stmt_list_buku, "ssssss", $new_id_buku, $isbn, $judul_buku, $nama_penulis, $nama_penerbit, $old_id_buku);
            if (!mysqli_stmt_execute($stmt_list_buku)) {
                throw new Exception("Error updating data_list_buku: " . mysqli_stmt_error($stmt_list_buku));
            }
            mysqli_stmt_close($stmt_list_buku);
        } else {
            throw new Exception("Error preparing data_list_buku update statement: " . mysqli_error($koneksi));
        }

        mysqli_commit($koneksi);
        header("Location: kelola_katalog.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo "Error saat memperbarui buku: " . $e->getMessage();
        // Pertimbangkan untuk menghapus foto baru jika update gagal
        if ($foto_destination && $foto_destination !== $current_foto_path && file_exists($foto_destination)) {
              unlink($foto_destination);
        }
    }
}


// Menangani penghapusan data buku
if (isset($_GET['id'])) {
    $id_buku_to_delete = $_GET['id'];

    mysqli_begin_transaction($koneksi);

    try {
        // 1. Ambil path foto dari data_buku untuk dihapus dari server
        $get_photo_query = "SELECT foto FROM data_buku WHERE id_buku=?";
        $stmt_photo = mysqli_prepare($koneksi, $get_photo_query);
        if (!$stmt_photo) {
            throw new Exception("Error preparing get photo statement: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param($stmt_photo, "s", $id_buku_to_delete);
        mysqli_stmt_execute($stmt_photo);
        $photo_result = mysqli_stmt_get_result($stmt_photo);
        $photo_path = null;
        if ($photo_row = mysqli_fetch_assoc($photo_result)) {
            $photo_path = $photo_row['foto'];
        }
        mysqli_stmt_close($stmt_photo);

        // 2. Hapus dari tabel data_buku
        $delete_buku_query = "DELETE FROM data_buku WHERE id_buku=?";
        $stmt_buku = mysqli_prepare($koneksi, $delete_buku_query);
        if (!$stmt_buku) {
            throw new Exception("Error preparing delete data_buku statement: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param($stmt_buku, "s", $id_buku_to_delete);
        if (!mysqli_stmt_execute($stmt_buku)) {
            throw new Exception("Error deleting data_buku: " . mysqli_stmt_error($stmt_buku));
        }
        mysqli_stmt_close($stmt_buku);

        // 3. Hapus dari tabel data_list_buku
        $delete_list_buku_query = "DELETE FROM data_list_buku WHERE id_buku=?";
        $stmt_list_buku = mysqli_prepare($koneksi, $delete_list_buku_query);
        if (!$stmt_list_buku) {
            throw new Exception("Error preparing delete data_list_buku statement: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param($stmt_list_buku, "s", $id_buku_to_delete);
        if (!mysqli_stmt_execute($stmt_list_buku)) {
            throw new Exception("Error deleting data_list_buku: " . mysqli_stmt_error($stmt_list_buku));
        }
        mysqli_stmt_close($stmt_list_buku);

        // Jika kedua query berhasil, commit transaksi dan hapus file foto
        mysqli_commit($koneksi);
        if ($photo_path && file_exists($photo_path)) {
            unlink($photo_path); // Hapus file foto dari server
        }
        header("Location: kelola_katalog.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo "Error saat menghapus buku: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola Katalog Buku</title>
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
      <span class="ms-3">Kelola Katalog Buku</span>
    </div>
  </div>

  <div class="row">
      <nav class="col-md-3 d-none d-md-block sidebar min-vh-100 position-relative">
        <h5 class="pt-4">Pustakawan<br /><small>admin</small></h5>
        <a href="kelola_pustakawan.php">Kelola Pustakawan</a>
        <a href="kelola_anggota.php">Kelola Anggota</a>
        <a href="kelola_katalog.php">Kelola Katalog Buku</a>
        <a href="kelola_list_buku.php">Kelola list buku</a>
        <a href="kelola_peminjaman.php">Kelola Peminjaman Buku</a>
        <a href="kelola_pengembalian.php">Kelola Pengembalian Buku</a>
        <a href="kelola_histori.php">Kelola histori pengembalian</a>
        <a href="logout.php" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
        <div class="image-box text-center mt-5">
          <img src="assets/logo_sekolah.png" alt="icon" />
        </div>
      </nav>

    <main class="col-md-9 col-12 main-content">
      <h4>Kelola Katalog Buku</h4>
      <div class="d-flex flex-wrap gap-2 align-items-center mb-3 mt-3">
        <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="Cari nama buku..." style="max-width: 300px;" oninput="filterTable()" />
        <button class="btn btn-tambah btn-md" data-bs-toggle="modal" data-bs-target="#tambahKatalogModal">Tambah Buku</button>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped" id="KatalogTable"> <thead>
            <tr>
              <th>No</th>
              <th>ID Buku</th>
              <th>ISBN</th>
              <th>Judul Buku</th>
              <th>Nama Penulis</th>
              <th>Nama Penerbit</th>
              <th>Jumlah Halaman</th>
              <th>Foto</th>
              <th>action</th>
              <th>action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            // Re-fetch result after potential modifications
            $result = mysqli_query($koneksi, $query_select_buku);
            if (mysqli_num_rows($result) > 0) {
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
                                 <button class="btn btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#editKatalogModal"
                                     data-id="' . htmlspecialchars($row['id_buku']) . '"
                                     data-isbn="' . htmlspecialchars($row['isbn']) . '"
                                     data-judul_buku="' . htmlspecialchars($row['judul_buku']) . '"
                                     data-nama_penulis="' . htmlspecialchars($row['nama_penulis']) . '"
                                     data-nama_penerbit="' . htmlspecialchars($row['nama_penerbit']) . '"
                                     data-jumlah_halaman="' . htmlspecialchars($row['jumlah_halaman']) . '"
                                     data-foto="' . htmlspecialchars($row['foto']) . '">Edit</button>
                               </td>';
                    echo '<td>
                                 <button class="btn btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#hapusKatalogModal" data-id="' . htmlspecialchars($row['id_buku']) . '">Hapus</button>
                               </td>';
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10' class='text-center'>Tidak ada data buku.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<div class="modal fade" id="tambahKatalogModal" tabindex="-1" aria-labelledby="tambahKatalogModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tambahKatalogModalLabel">Tambah Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formTambahKatalog" method="POST" action="" enctype="multipart/form-data">
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
          <button type="submit" class="btn btn-primary">Tambah Buku</button>
          <button type="reset" class="btn btn-danger">Reset</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editKatalogModal" tabindex="-1" aria-labelledby="editKatalogModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editKatalogModalLabel">Edit Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formEditKatalog" method="POST" action="" enctype="multipart/form-data">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" id="editId" name="editId"> 
          <div class="mb-3">
            <label for="newId" class="form-label">ID Buku</label>
            <input type="text" class="form-control" id="newId" name="newId" required> 
          </div>
          <div class="mb-3">
            <label for="editIsbn" class="form-label">ISBN</label>
            <input type="text" class="form-control" id="editIsbn" name="editIsbn" required>
          </div>
          <div class="mb-3">
            <label for="editJudulbuku" class="form-label">Judul Buku</label>
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
          <div class="mb-3">
            <label for="editFoto" class="form-label">Foto</label>
            <input type="file" class="form-control" id="editFoto" name="editFoto" accept="image/*">
          </div>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          <button type="reset" class="btn btn-danger">Reset</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="hapusKatalogModal" tabindex="-1" aria-labelledby="hapusKatalogModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="hapusKatalogModalLabel">Hapus Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus data buku ini?</p>
      </div>
      <div class="modal-footer d-flex justify-content-center gap-2">
        <form id="formHapusKatalog" method="GET" action="">
          <input type="hidden" id="hapusId" name="id">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
          <button type="submit" class="btn btn-danger">Ya</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin logout?
      </div>
      <div class="modal-footer d-flex justify-content-center gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
        <a href="logout.php" class="btn btn-danger">Ya</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Script untuk mengisi data pada modal edit
  const editButtons = document.querySelectorAll('[data-bs-target="#editKatalogModal"]');
  editButtons.forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-id');
      const isbn = button.getAttribute('data-isbn');
      const judul_buku = button.getAttribute('data-judul_buku');
      const nama_penulis = button.getAttribute('data-nama_penulis');
      const nama_penerbit = button.getAttribute('data-nama_penerbit');
      const jumlah_halaman = button.getAttribute('data-jumlah_halaman');

      // Set the ORIGINAL ID in the hidden field (editId)
      document.getElementById('editId').value = id; 
      // Set the CURRENT ID in the editable field (newId)
      document.getElementById('newId').value = id; 
      
      document.getElementById('editIsbn').value = isbn;
      document.getElementById('editJudulbuku').value = judul_buku;
      document.getElementById('editNamaPenulis').value = nama_penulis;
      document.getElementById('editNamaPenerbit').value = nama_penerbit;
      document.getElementById('editJumlahHalaman').value = jumlah_halaman;
    });
  });

  // Script untuk mengisi data pada modal hapus
  const deleteButtons = document.querySelectorAll('[data-bs-target="#hapusKatalogModal"]');
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
    const table = document.getElementById('KatalogTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) { // Start from 1 to skip the header row
      const td = tr[i].getElementsByTagName('td');
      let found = false;

      // Search across relevant columns (e.g., Judul Buku, Nama Penulis, Nama Penerbit, ID Buku, ISBN)
      // Adjust column indices as needed based on your table structure
      const idBukuCol = td[1]; // ID Buku
      const isbnCol = td[2]; // ISBN
      const judulBukuCol = td[3]; // Judul Buku
      const namaPenulisCol = td[4]; // Nama Penulis
      const namaPenerbitCol = td[5]; // Nama Penerbit

      if (idBukuCol && idBukuCol.textContent.toLowerCase().indexOf(filter) > -1) {
        found = true;
      } else if (isbnCol && isbnCol.textContent.toLowerCase().indexOf(filter) > -1) {
        found = true;
      } else if (judulBukuCol && judulBukuCol.textContent.toLowerCase().indexOf(filter) > -1) {
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