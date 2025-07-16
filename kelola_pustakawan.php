<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data dari tabel data_pustakawan
$query_select_pustakawan = "SELECT * FROM data_Pustakawan";
$result = mysqli_query($koneksi, $query_select_pustakawan);

// Menangani penyimpanan data pustakawan baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $id_pustakawan = $_POST['id'];
    $nama_pustakawan = $_POST['nama'];
    $jabatan = $_POST['jabatan']; // Menggunakan 'jabatan' bukan 'jurusan'

    // Insert query untuk tabel data_Pustakawan menggunakan prepared statements
    $insert_pustakawan_query = "INSERT INTO data_Pustakawan (id_pustakawan, nama_pustakawan, jabatan) VALUES (?, ?, ?)";
    $stmt_pustakawan = mysqli_prepare($koneksi, $insert_pustakawan_query);

    if ($stmt_pustakawan) {
        // Tipe parameter: 's' untuk string (id_pustakawan, nama_pustakawan, jabatan)
        mysqli_stmt_bind_param($stmt_pustakawan, "sss", $id_pustakawan, $nama_pustakawan, $jabatan);
        if (mysqli_stmt_execute($stmt_pustakawan)) {
            // Insert query untuk tabel users (jika pustakawan perlu akun login)
            // Asumsi: username = id_pustakawan, password = nama_pustakawan (atau hash password), role = 'pustakawan'
            $username = $nama_pustakawan; // ID Pustakawan sebagai username
            $password = $id_pustakawan; // Hash password untuk keamanan
            $role = 'admin'; // Role untuk pustakawan

            $insert_user_query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
            $stmt_user = mysqli_prepare($koneksi, $insert_user_query);
            if ($stmt_user) {
                mysqli_stmt_bind_param($stmt_user, "sss", $username, $password, $role);
                if (mysqli_stmt_execute($stmt_user)) {
                    header("Location: kelola_pustakawan.php"); // Redirect setelah penyimpanan
                    exit();
                } else {
                    echo "Error inserting user: " . mysqli_stmt_error($stmt_user);
                }
                mysqli_stmt_close($stmt_user);
            } else {
                echo "Error preparing user statement: " . mysqli_error($koneksi);
            }
        } else {
            echo "Error inserting pustakawan data: " . mysqli_stmt_error($stmt_pustakawan);
        }
        mysqli_stmt_close($stmt_pustakawan);
    } else {
        echo "Error preparing pustakawan statement: " . mysqli_error($koneksi);
    }
}

// Menangani pembaruan data pustakawan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    // Ambil ID dan Nama pustakawan ASLI sebelum perubahan dari hidden input
    $original_id_pustakawan = $_POST['originalId'];
    $original_nama_pustakawan = $_POST['originalNama'];

    // Ambil data yang BARU dari form
    $new_id_pustakawan = $_POST['editId'];
    $new_nama_pustakawan = $_POST['editNama'];
    $jabatan = $_POST['editJabatan'];

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // 1. Update data_Pustakawan
        $update_pustakawan_query = "UPDATE data_Pustakawan SET id_pustakawan=?, nama_pustakawan=?, jabatan=? WHERE id_pustakawan=?";
        $stmt_pustakawan = mysqli_prepare($koneksi, $update_pustakawan_query);
        if (!$stmt_pustakawan) {
            throw new Exception("Error preparing pustakawan update statement: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param($stmt_pustakawan, "ssss", $new_id_pustakawan, $new_nama_pustakawan, $jabatan, $original_id_pustakawan);
        if (!mysqli_stmt_execute($stmt_pustakawan)) {
            throw new Exception("Error updating pustakawan data: " . mysqli_stmt_error($stmt_pustakawan));
        }
        mysqli_stmt_close($stmt_pustakawan);

        // 2. Update tabel users
        // Username di tabel users menggunakan nama_pustakawan LAMA sebagai acuan WHERE
        // Password di tabel users menggunakan id_pustakawan LAMA sebagai acuan WHERE (atau kita bisa pakai username lama juga)
        // Kita akan update username ke new_nama_pustakawan dan password ke new_id_pustakawan
        // Menggunakan original_nama_pustakawan sebagai WHERE clause untuk menemukan baris yang benar di tabel users
        $update_user_query = "UPDATE users SET username=?, password=? WHERE username=?";
        $stmt_user = mysqli_prepare($koneksi, $update_user_query);
        if (!$stmt_user) {
            throw new Exception("Error preparing user update statement: " . mysqli_error($koneksi));
        }
        // Bind parameter: (new_username, new_password, old_username)
        mysqli_stmt_bind_param($stmt_user, "sss", $new_nama_pustakawan, $new_id_pustakawan, $original_nama_pustakawan);
        if (!mysqli_stmt_execute($stmt_user)) {
            throw new Exception("Error updating user account: " . mysqli_stmt_error($stmt_user));
        }
        mysqli_stmt_close($stmt_user);

        // Jika semua query berhasil, commit transaksi
        mysqli_commit($koneksi);
        header("Location: kelola_pustakawan.php"); // Redirect setelah pembaruan
        exit();

    } catch (Exception $e) {
        // Jika ada error, rollback transaksi
        mysqli_rollback($koneksi);
        echo "Error updating record: " . $e->getMessage();
    }
}

// Menangani penghapusan data pustakawan
if (isset($_GET['id'])) {
    $id_pustakawan_to_delete = $_GET['id'];
    // It's generally safer to retrieve 'nama_pustakawan' from the database
    // using 'id_pustakawan' if it's needed for the 'users' table deletion,
    // rather than directly from $_GET, as $_GET['nama_pustakawan'] could be
    // missing or manipulated. However, if 'nama_pustakawan' is indeed
    // used as the username, we'll proceed with it, but with a warning.
    $nama_pustakawan_to_delete = $_GET['nama_pustakawan']; // Be cautious with this if not strictly necessary.

    // Mulai transaksi untuk atomisitas
    mysqli_begin_transaction($koneksi);

    try {
        // --- Delete from data_Pustakawan table ---
        // Use ONLY id_pustakawan for deletion from data_Pustakawan,
        // as 'id' is typically the primary key and unique.
        $delete_pustakawan_query = "DELETE FROM data_Pustakawan WHERE id_pustakawan=?";
        $stmt_pustakawan = mysqli_prepare($koneksi, $delete_pustakawan_query);

        if (!$stmt_pustakawan) {
            throw new Exception("Error preparing delete pustakawan statement: " . mysqli_error($koneksi));
        }

        // Bind parameter: 's' for string, using id_pustakawan for deletion
        mysqli_stmt_bind_param($stmt_pustakawan, "s", $id_pustakawan_to_delete);

        if (!mysqli_stmt_execute($stmt_pustakawan)) {
            throw new Exception("Error deleting pustakawan data: " . mysqli_stmt_error($stmt_pustakawan));
        }
        mysqli_stmt_close($stmt_pustakawan);

        // --- Delete from users table ---
        // Asumsi: kolom 'username' di tabel 'users' menyimpan 'nama_pustakawan'
        // dan kolom 'password' di tabel 'users' menyimpan 'id_pustakawan'.
        // CATATAN PENTING: Menggunakan 'id_pustakawan' sebagai password adalah praktik yang SANGAT TIDAK AMAN!
        // Password harus selalu di-hash (misalnya dengan password_hash()).
        // Idealnya, tabel 'users' memiliki kolom 'pustakawan_id' yang merupakan foreign key ke 'data_Pustakawan.id_pustakawan'.

        $delete_users_query = "DELETE FROM users WHERE username=? AND password=?";
        $stmt_user = mysqli_prepare($koneksi, $delete_users_query);

        if (!$stmt_user) {
            throw new Exception("Error preparing delete user statement: " . mysqli_error($koneksi));
        }

        // Bind kedua parameter:
        // 'ss' berarti dua parameter string.
        // Parameter pertama untuk 'username' (menggunakan $nama_pustakawan_to_delete)
        // Parameter kedua untuk 'password' (menggunakan $id_pustakawan_to_delete)
        mysqli_stmt_bind_param($stmt_user, "ss", $nama_pustakawan_to_delete, $id_pustakawan_to_delete);

        if (!mysqli_stmt_execute($stmt_user)) {
            throw new Exception("Error deleting user data: " . mysqli_stmt_error($stmt_user));
        }
        mysqli_stmt_close($stmt_user);

        // Jika kedua query berhasil, commit transaksi
        mysqli_commit($koneksi);
        header("Location: kelola_pustakawan.php"); // Redirect setelah penghapusan berhasil
        exit();

    } catch (Exception $e) {
        // Jika ada query yang gagal, rollback transaksi
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
    <title>Kelola Pustakawan</title>
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
            <span class="ms-3">Kelola Pustakawan</span>
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

        <div class="offcanvas offcanvas-start sidebar text-white" tabindex="-1" id="sidebarMenu">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Pustakawan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
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
            </div>
        </div>

        <main class="col-md-9 col-12 main-content">
            <h4>Kelola Pustakawan</h4>
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3 mt-3">
                <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="Cari nama pustakawan..." style="max-width: 300px;" oninput="filterTable()" />
                <button class="btn btn-tambah btn-md" data-bs-toggle="modal" data-bs-target="#tambahPustakawanModal">Tambah Pustakawan</button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="pustakawanTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Pustakawan</th>
                            <th>Nama Pustakawan</th>
                            <th>Jabatan</th>
                            <th>action</th>
                            <th>action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        // Re-fetch result after potential modifications
                        $result = mysqli_query($koneksi, $query_select_pustakawan);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . htmlspecialchars($row['id_pustakawan']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['nama_pustakawan']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['jabatan']) . "</td>";
                                echo '<td>
                                        <button class="btn btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#editPustakawanModal" 
                                                data-id="' . htmlspecialchars($row['id_pustakawan']) . '" 
                                                data-nama="' . htmlspecialchars($row['nama_pustakawan']) . '" 
                                                data-jabatan="' . htmlspecialchars($row['jabatan']) . '">Edit</button>
                                      </td>';
                                echo '<td>
                                        <button class="btn btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#hapusPustakawanModal" 
                                                data-id="' . htmlspecialchars($row['id_pustakawan']) . '">Hapus</button>
                                      </td>';
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Tidak ada data pustakawan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<div class="modal fade" id="tambahPustakawanModal" tabindex="-1" aria-labelledby="tambahPustakawanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahPustakawanModalLabel">Tambah Pustakawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formTambahPustakawan" method="POST" action="">
                    <input type="hidden" name="action" value="tambah">
                    <div class="mb-3">
                        <label for="id" class="form-label">ID Pustakawan</label>
                        <input type="text" class="form-control" id="id" name="id" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Pustakawan</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="jabatan" class="form-label">Jabatan</label>
                        <input type="text" class="form-control" id="jabatan" name="jabatan" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah Pustakawan</button>
                    <button type="reset" class="btn btn-danger">Reset</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editPustakawanModal" tabindex="-1" aria-labelledby="editPustakawanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPustakawanModalLabel">Edit Pustakawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditPustakawan" method="POST" action="">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" id="originalId" name="originalId">
                    <input type="hidden" id="originalNama" name="originalNama">

                    <div class="mb-3">
                        <label for="editId" class="form-label">ID Pustakawan</label>
                        <input type="text" class="form-control" id="editId" name="editId" required>
                    </div>
                    <div class="mb-3">
                        <label for="editNama" class="form-label">Nama Pustakawan</label>
                        <input type="text" class="form-control" id="editNama" name="editNama" required>
                    </div>
                    <div class="mb-3">
                        <label for="editJabatan" class="form-label">Jabatan</label>
                        <input type="text" class="form-control" id="editJabatan" name="editJabatan" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Edit Pustakawan</button>
                    <button type="reset" class="btn btn-danger">Reset</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="hapusPustakawanModal" tabindex="-1" aria-labelledby="hapusPustakawanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hapusPustakawanModalLabel">Hapus Pustakawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data pustakawan ini?</p>
            </div>
            <div class="modal-footer d-flex justify-content-center gap-2">
                <form id="formHapusPustakawan" method="GET" action="">
                    <input type="hidden" id="hapusId" name="id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
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
    const editButtons = document.querySelectorAll('[data-bs-target="#editPustakawanModal"]');
    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const jabatan = button.getAttribute('data-jabatan');

            // Set original values into hidden inputs
            document.getElementById('originalId').value = id;
            document.getElementById('originalNama').value = nama;

            // Set values for editable inputs
            document.getElementById('editId').value = id;
            document.getElementById('editNama').value = nama;
            document.getElementById('editJabatan').value = jabatan;
        });
    });

    // Script untuk mengisi data pada modal hapus
    const deleteButtons = document.querySelectorAll('[data-bs-target="#hapusPustakawanModal"]');
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
        const table = document.getElementById('pustakawanTable'); // Menggunakan ID tabel pustakawan
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) { // Start from 1 to skip the header row
            const td = tr[i].getElementsByTagName('td');
            let found = false;

            // Cari di kolom Nama Pustakawan (indeks 2) atau ID Pustakawan (indeks 1) atau Jabatan (indeks 3)
            if (td[1] && td[1].textContent.toLowerCase().indexOf(filter) > -1) { // ID Pustakawan
                found = true;
            } else if (td[2] && td[2].textContent.toLowerCase().indexOf(filter) > -1) { // Nama Pustakawan
                found = true;
            } else if (td[3] && td[3].textContent.toLowerCase().indexOf(filter) > -1) { // Jabatan
                found = true;
            }
            tr[i].style.display = found ? "" : "none"; // Show or hide the row
        }
    }
</script>
</body>
</html>