<?php
// Start session to store messages (must be at the very top before any HTML output)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Menangani penyimpanan data peminjaman baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    // Collect and sanitize input data
    $tanggal_hari = mysqli_real_escape_string($koneksi, $_POST['tanggal_hari']);
    $nama_peminjam = mysqli_real_escape_string($koneksi, $_POST['nama_peminjam']);
    $judul_buku = mysqli_real_escape_string($koneksi, $_POST['judul_buku']);
    $jumlah = (int)$_POST['jumlah']; // Cast to integer for 'jumlah' to ensure correct type
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $tanggal_pinjam = mysqli_real_escape_string($koneksi, $_POST['tanggal_pinjam']);
    $tanggal_kembali = mysqli_real_escape_string($koneksi, $_POST['tanggal_kembali']);

    // Start a transaction for atomicity
    mysqli_begin_transaction($koneksi);

    try {
        // Insert query into data_pinjam table using prepared statements
        $insert_pinjam_query = "INSERT INTO data_pinjam (tanggal_hari, nama_peminjam, judul_buku, jumlah, kelas, tanggal_pinjam, tanggal_kembali) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_pinjam = mysqli_prepare($koneksi, $insert_pinjam_query);
        if (!$stmt_pinjam) {
            throw new Exception("Error preparing data_pinjam statement: " . mysqli_error($koneksi));
        }
        // "sssisss" -> string (tanggal_hari), string (nama_peminjam), string (judul_buku), integer (jumlah), string (kelas), string (tanggal_pinjam), string (tanggal_kembali)
        mysqli_stmt_bind_param($stmt_pinjam, "sssisss", $tanggal_hari, $nama_peminjam, $judul_buku, $jumlah, $kelas, $tanggal_pinjam, $tanggal_kembali);

        if (!mysqli_stmt_execute($stmt_pinjam)) {
            throw new Exception("Error inserting into data_pinjam: " . mysqli_stmt_error($stmt_pinjam));
        }
        mysqli_stmt_close($stmt_pinjam);

        // Insert query into data_pengembalian table using prepared statements
        // Corrected: 'nama_peminjam' is now included in the columns and bind_param
        $insert_pengembalian_query = "INSERT INTO data_pengembalian (nama_peminjam, judul_buku, tanggal_pinjam, tanggal_kembali, status_pengembalian) VALUES (?, ?, ?, ?, ?)";
        $stmt_pengembalian = mysqli_prepare($koneksi, $insert_pengembalian_query);
        if (!$stmt_pengembalian) {
            throw new Exception("Error preparing data_pengembalian statement: " . mysqli_error($koneksi));
        }
        $status_pengembalian = 'Belum Dikembalikan'; // Standardize case
        // Corrected: added 'nama_peminjam' to bind_param, matching the query
        // "sssss" -> string (nama_peminjam), string (judul_buku), string (tanggal_pinjam), string (tanggal_kembali), string (status_pengembalian)
        mysqli_stmt_bind_param($stmt_pengembalian, "sssss", $nama_peminjam, $judul_buku, $tanggal_pinjam, $tanggal_kembali, $status_pengembalian);

        if (!mysqli_stmt_execute($stmt_pengembalian)) {
            throw new Exception("Error inserting into data_pengembalian: " . mysqli_stmt_error($stmt_pengembalian));
        }
        mysqli_stmt_close($stmt_pengembalian);

        mysqli_commit($koneksi); // Commit the transaction if both inserts are successful
        
        $_SESSION['message'] = 'Data peminjaman berhasil ditambahkan.';
        $_SESSION['message_type'] = 'success';
        header("Location: kelola_peminjaman.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($koneksi); // Rollback if any insert fails
        $_SESSION['message'] = 'Terjadi kesalahan: ' . htmlspecialchars($e->getMessage());
        $_SESSION['message_type'] = 'danger';
        header("Location: kelola_peminjaman.php");
        exit();
    }
}

// Menangani penghapusan data peminjaman
if (isset($_GET['nama_peminjam']) && isset($_GET['tanggal_pinjam'])) {
    $nama_peminjam_to_delete = mysqli_real_escape_string($koneksi, $_GET['nama_peminjam']);
    $tanggal_pinjam_to_delete = mysqli_real_escape_string($koneksi, $_GET['tanggal_pinjam']);

    // Start a transaction for atomicity
    mysqli_begin_transaction($koneksi);

    try {
        // Delete from data_pengembalian first to avoid foreign key constraints if they exist
        $delete_pengembalian_query = "DELETE FROM data_pengembalian WHERE nama_peminjam = ? AND tanggal_pinjam = ?";
        $stmt_pengembalian = mysqli_prepare($koneksi, $delete_pengembalian_query);
        if (!$stmt_pengembalian) {
            throw new Exception("Error preparing delete_pengembalian statement: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param($stmt_pengembalian, "ss", $nama_peminjam_to_delete, $tanggal_pinjam_to_delete);
        if (!mysqli_stmt_execute($stmt_pengembalian)) {
            throw new Exception("Error deleting from data_pengembalian: " . mysqli_stmt_error($stmt_pengembalian));
        }
        mysqli_stmt_close($stmt_pengembalian);

        // Delete from data_pinjam
        $delete_pinjam_query = "DELETE FROM data_pinjam WHERE nama_peminjam = ? AND tanggal_pinjam = ?";
        $stmt_pinjam = mysqli_prepare($koneksi, $delete_pinjam_query);
        if (!$stmt_pinjam) {
            throw new Exception("Error preparing delete_pinjam statement: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param($stmt_pinjam, "ss", $nama_peminjam_to_delete, $tanggal_pinjam_to_delete);
        if (!mysqli_stmt_execute($stmt_pinjam)) {
            throw new Exception("Error deleting from data_pinjam: " . mysqli_stmt_error($stmt_pinjam));
        }
        mysqli_stmt_close($stmt_pinjam);

        mysqli_commit($koneksi); // Commit if both deletes are successful
        $_SESSION['message'] = 'Data peminjaman berhasil dihapus.';
        $_SESSION['message_type'] = 'success';
        header("Location: kelola_peminjaman.php"); // Redirect after deletion
        exit();

    } catch (Exception $e) {
        mysqli_rollback($koneksi); // Rollback if any delete fails
        $_SESSION['message'] = 'Terjadi kesalahan saat menghapus: ' . htmlspecialchars($e->getMessage());
        $_SESSION['message_type'] = 'danger';
        header("Location: kelola_peminjaman.php");
        exit();
    }
}

// Ambil data dari tabel data_pinjam
$query = "SELECT tanggal_hari, nama_peminjam, judul_buku, jumlah, kelas, tanggal_pinjam, tanggal_kembali FROM data_pinjam ORDER BY tanggal_hari DESC, tanggal_pinjam DESC";
$result = mysqli_query($koneksi, $query);

// Check if the query was successful before using $result
if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
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
        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            width: 90%; /* Adjust width for better display on smaller screens */
            max-width: 400px; /* Max width for larger screens */
        }
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
            <a href="kelola_pustakawan.php">Kelola Pustakawan</a>
            <a href="kelola_anggota.php">Kelola Anggota</a>
            <a href="kelola_katalog.php">Kelola Katalog Buku</a>
            <a href="kelola_list_buku.php">Kelola list buku</a>
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
                <a href="kelola_pustakawan.php">Kelola Pustakawan</a>
                <a href="kelola_anggota.php">Kelola Anggota</a>
                <a href="kelola_katalog.php">Kelola Katalog Buku</a>
                <a href="kelola_list_buku.php">Kelola list buku</a>
                <a href="kelola_peminjaman.php">Kelola Peminjaman Buku</a>
                <a href="kelola_pengembalian.php">Kelola Pengembalian Buku</a>
                <a href="denda_kelola.php">Kelola Denda</a>
                <a href="logout.php">Logout</a>
                <div class="image-box text-center mt-5">
                    <img src="assets/logo_sekolah.png" alt="icon" />
                </div>
            </div>
        </div>

        <main class="col-md-9 col-12 main-content">
            <h4>Kelola Peminjaman Buku</h4>
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3 mt-3">
                <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="Cari Nama Peminjam, Judul Buku, atau Kelas" style="max-width: 300px;" oninput="filterTable()" />
                <button class="btn btn-tambah btn-md" data-bs-toggle="modal" data-bs-target="#tambahPeminjamanModal">Tambah Peminjaman</button>
            </div>

            <div class="alert-container">
                <?php
                if (isset($_SESSION['message'])) {
                    $alert_class = ($_SESSION['message_type'] == 'success') ? 'alert-success' : 'alert-danger';
                    echo '<div class="alert ' . $alert_class . ' alert-dismissible fade show" role="alert">';
                    echo $_SESSION['message'];
                    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                    echo '</div>';
                    // Clear the message after displaying it
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                }
                ?>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="PeminjamanTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Hari Ini</th>
                            <th>Nama Peminjam</th>
                            <th>Judul Buku</th>
                            <th>Jumlah</th>
                            <th>Kelas</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . htmlspecialchars($row['tanggal_hari']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['nama_peminjam']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['judul_buku']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['jumlah']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['kelas']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tanggal_pinjam']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tanggal_kembali']) . "</td>";
                                echo '<td>
                                            <button class="btn btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#hapusPeminjamanModal" 
                                                data-nama-peminjam="' . htmlspecialchars($row['nama_peminjam']) . '" 
                                                data-tanggal-pinjam="' . htmlspecialchars($row['tanggal_pinjam']) . '">Hapus</button>
                                        </td>';
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9' class='text-center'>Tidak ada data peminjaman.</td></tr>";
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
                <form id="formTambahPeminjaman" method="POST" action="">
                    <input type="hidden" name="action" value="tambah">
                    <div class="mb-3">
                        <label for="tanggal_hari" class="form-label">Tanggal Hari Ini</label>
                        <input type="date" class="form-control" name="tanggal_hari" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_peminjam" class="form-label">Nama Peminjam</label>
                        <input type="text" class="form-control" name="nama_peminjam" required>
                    </div>
                    <div class="mb-3">
                        <label for="judul_buku" class="form-label">Judul Buku</label>
                        <input type="text" class="form-control" name="judul_buku" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah Buku</label>
                        <input type="number" class="form-control" name="jumlah" required min="1">
                    </div>
                    <div class="mb-3">
                        <label for="kelas" class="form-label">Kelas</label>
                        <input type="text" class="form-control" name="kelas">
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                        <input type="date" class="form-control" name="tanggal_pinjam" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label> <input type="date" class="form-control" name="tanggal_kembali" required>
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
                <p>Apakah Anda yakin ingin menghapus data peminjaman untuk <strong id="hapusNamaPeminjamDisplay"></strong> pada tanggal <strong id="hapusTanggalPinjamDisplay"></strong>?</p>
            </div>
            <div class="modal-footer">
                <form id="formHapusPeminjaman" method="GET" action="">
                    <input type="hidden" id="hapusNamaPeminjam" name="nama_peminjam">
                    <input type="hidden" id="hapusTanggalPinjam" name="tanggal_pinjam">
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
    const hapusNamaPeminjamInput = document.getElementById('hapusNamaPeminjam');
    const hapusTanggalPinjamInput = document.getElementById('hapusTanggalPinjam');
    const hapusNamaPeminjamDisplay = document.getElementById('hapusNamaPeminjamDisplay');
    const hapusTanggalPinjamDisplay = document.getElementById('hapusTanggalPinjamDisplay');

    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const namaPeminjam = button.getAttribute('data-nama-peminjam');
            const tanggalPinjam = button.getAttribute('data-tanggal-pinjam');
            
            hapusNamaPeminjamInput.value = namaPeminjam;
            hapusTanggalPinjamInput.value = tanggalPinjam;
            
            hapusNamaPeminjamDisplay.textContent = namaPeminjam;
            hapusTanggalPinjamDisplay.textContent = tanggalPinjam;
        });
    });

    // Function to filter table rows based on search input
    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('PeminjamanTable');
        const tr = table.getElementsByTagName('tr');

        let visibleRowCount = 0; // To keep track of visible rows for numbering

        for (let i = 1; i < tr.length; i++) { // Start from 1 to skip the header row
            const td = tr[i].getElementsByTagName('td');
            let found = false;

            // Search across relevant columns (Nama Peminjam, Judul Buku, Kelas)
            const namaPeminjamCol = td[2]; // Nama Peminjam is now index 2
            const judulBukuCol = td[3];    // Judul Buku is index 3
            const kelasCol = td[5];        // Kelas is now index 5

            if (namaPeminjamCol && namaPeminjamCol.textContent.toLowerCase().includes(filter)) {
                found = true;
            } else if (judulBukuCol && judulBukuCol.textContent.toLowerCase().includes(filter)) {
                found = true;
            }
             else if (kelasCol && kelasCol.textContent.toLowerCase().includes(filter)) {
                found = true;
            }

            tr[i].style.display = found ? "" : "none"; // Show or hide the row
            if (found) {
                visibleRowCount++;
                // Update 'No' column for visible rows
                tr[i].getElementsByTagName('td')[0].textContent = visibleRowCount;
            }
        }
    }
</script>
</body>
</html>
<?php
// Tutup koneksi database setelah semua data ditampilkan
mysqli_close($koneksi);
?>