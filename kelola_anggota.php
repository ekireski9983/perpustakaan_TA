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

    // Insert query untuk data anggota
    $insert_query = "INSERT INTO data_anggota (id_siswa, nama, jurusan, kelas, semester) VALUES ('$id_siswa', '$nama', '$jurusan', '$kelas', '$semester')";
    
    if (mysqli_query($koneksi, $insert_query)) {
        // Insert query untuk tabel users
        $username = $nama; // Nama siswa sebagai username
        $password = $id_siswa; // ID siswa sebagai password
        $role = 'user'; // Role

        $user_query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
        mysqli_query($koneksi, $user_query);

        header("Location: kelola_anggota.php"); // Redirect setelah penyimpanan
        exit();
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

// Menangani pembaruan data anggota
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id_siswa = $_POST['editId']; // Use the hidden ID for the WHERE clause
    $nama = $_POST['editNama'];
    $jurusan = $_POST['editJurusan'];
    $kelas = $_POST['editKelas'];
    $semester = $_POST['editSemester'];

    $update_query = "UPDATE data_anggota SET nama='$nama', jurusan='$jurusan', kelas='$kelas', semester='$semester' WHERE id_siswa='$id_siswa'";
    mysqli_query($koneksi, $update_query);
    header("Location: kelola_anggota.php"); // Redirect setelah pembaruan
    exit();
}

// Menangani penghapusan data anggota
if (isset($_GET['id'])) {
    $id_siswa_to_delete = $_GET['id']; // Renamed variable for clarity

    // Start a transaction for atomicity
    mysqli_begin_transaction($koneksi);

    try {
        // First, delete from the data_anggota table
        $delete_anggota_query = "DELETE FROM data_anggota WHERE id_siswa='$id_siswa_to_delete'";
        if (!mysqli_query($koneksi, $delete_anggota_query)) {
            throw new Exception(mysqli_error($koneksi));
        }

        // Then, delete from the users table (assuming username matches nama or id_siswa matches password for simplicity as per your insert logic)
        // A more robust solution would be to have a foreign key or a dedicated user ID in data_anggota
        $delete_users_query = "DELETE FROM users WHERE password='$id_siswa_to_delete'"; // Assuming password is id_siswa
        // Alternatively, if username is used as 'nama' from data_anggota, you'd need to fetch 'nama' first.
        // For now, based on your insert, password matching id_siswa is the direct link.
        
        if (!mysqli_query($koneksi, $delete_users_query)) {
            throw new Exception(mysqli_error($koneksi));
        }

        // If both queries are successful, commit the transaction
        mysqli_commit($koneksi);
        header("Location: kelola_anggota.php"); // Redirect after successful deletion
        exit();
    } catch (Exception $e) {
        // If any query fails, rollback the transaction
        mysqli_rollback($koneksi);
        echo "Error deleting record: " . $e->getMessage();
    }
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
      <h4>Kelola Anggota</h4>
      <div class="d-flex flex-wrap gap-2 align-items-center mb-3 mt-3">
        <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="Nama anggota" style="max-width: 300px;" oninput="filterTable()" />
        <button class="btn btn-tambah btn-md" data-bs-toggle="modal" data-bs-target="#tambahAnggotaModal">Tambah Anggota</button>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped" id="anggotaTable">
          <thead>
            <tr>
              <th>No</th>
              <th>Id Siswa</th>
              <th>Nama Siswa</th>
              <th>Jurusan</th>
              <th>Kelas</th>
              <th>Semester</th>
              <th>Action</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            // Re-fetch result after potential modifications
            $result = mysqli_query($koneksi, $query);
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
                    </td>';
              echo '<td>
                      <button class="btn btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#hapusAnggotaModal" data-id="' . htmlspecialchars($row['id_siswa']) . '">Delete</button>
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
            <input type="text" class="form-control" id="editid" name="editid" readonly> </div>
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
  const editButtons = document.querySelectorAll('[data-bs-target="#editAnggotaModal"]');
  editButtons.forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-id');
      const nama = button.getAttribute('data-nama');
      const jurusan = button.getAttribute('data-jurusan');
      const kelas = button.getAttribute('data-kelas');
      const semester = button.getAttribute('data-semester');

      document.getElementById('editId').value = id; // Hidden field for ID
      document.getElementById('editid').value = id; // Set ID in the input field
      document.getElementById('editNama').value = nama;
      document.getElementById('editJurusan').value = jurusan;
      document.getElementById('editKelas').value = kelas;
      document.getElementById('editSemester').value = semester;
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