<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Menangani penghapusan data dari histori_pengembalian
if (isset($_GET['action']) && $_GET['action'] == 'delete_histori' && isset($_GET['id'])) { // Changed action to 'delete_histori'
    $id_buku_to_delete = $_GET['id'];

    // Gunakan prepared statement untuk DELETE dari histori_pengembalian
    $delete_query = "DELETE FROM histori_pengembalian WHERE id_buku = ?"; // Changed table to histori_pengembalian
    $stmt = mysqli_prepare($koneksi, $delete_query);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => "Prepare failed: " . mysqli_error($koneksi)]);
        exit();
    }
    // "s" karena id_buku bisa jadi string, jika id_buku adalah integer gunakan "i"
    mysqli_stmt_bind_param($stmt, "s", $id_buku_to_delete); 

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'id_buku' => $id_buku_to_delete]);
        mysqli_stmt_close($stmt);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => "Error deleting data: " . mysqli_stmt_error($stmt)]);
        mysqli_stmt_close($stmt);
        exit();
    }
}

// Ambil data dari tabel histori_pengembalian
$query = "SELECT id_buku, judul_buku, tanggal_pinjam, tanggal_pengembalian FROM histori_pengembalian"; 
$result = mysqli_query($koneksi, $query);

// Tambahkan penanganan kesalahan untuk query SELECT
if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>kelola Histori Pengembalian Buku</title> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { background-color: #f1f5f9; }
        .sidebar { background-color: #2f3e46; color: white; }
        .sidebar a { display: block; color: white; padding: 10px 20px; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background-color: #00b4d8; border-radius: 5px; }
        .sidebar .logout { position: absolute; bottom: 20px; width: 100%; }
        .sidebar .image-box img { width: 80px; opacity: 0.7; }
        .main-content { padding: 40px; }
        .table thead { background-color: #f8f9fa; }
        /* Removed status update button styles as they are no longer used */
        .btn-delete { background-color: #dc3545; color: white; } /* Bootstrap danger for delete */
        .btn-tambah { background-color: #00b4d8; color: white; }
        @media (max-width: 768px) { .main-content { padding: 20px; } }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row d-md-none bg-dark text-white p-2">
        <div class="col">
            <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">☰ Menu</button>
            <span class="ms-3">Histori Pengembalian Buku</span> </div>
    </div>

    <div class="row">
        <nav class="col-md-3 d-none d-md-block sidebar min-vh-100 position-relative">
            <h5 class="pt-4">pustakawan<br /><small>admin</small></h5>
           <a href="kelola_pustakawan.php">Kelola Pustakawan</a>
           <a href="kelola_anggota.php">kelola anggota</a>
           <a href="kelola_katalog.php">kelola katalog buku</a>
           <a href="kelola_list_buku.php">Kelola list buku</a>
           <a href="kelola_peminjaman.php">kelola Peminjaman buku</a>
           <a href="kelola_pengembalian.php">kelola pengembalian buku</a>
           <a href="kelola_histori.php">kelola histori pengembalian</a>
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
            <a href="kelola_anggota.php">kelola anggota</a>
            <a href="kelola_katalog.php">kelola katalog buku</a>
            <a href="kelola_list_buku.php">Kelola list buku</a>
            <a href="kelola_peminjaman.php">kelola Peminjaman buku</a>
            <a href="kelola_pengembalian.php">kelola pengembalian buku</a>
            <a href="kelola_histori.php">kelola histori pengembalian</a>
            <a href="logout.php" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
                <div class="image-box text-center mt-5">
                    <img src="assets/logo_sekolah.png" alt="icon" />
                </div>
            </div>
        </div>

        <main class="col-md-9 col-12 main-content">
            <h4>Histori Pengembalian</h4> <div class="table-responsive">
                <table class="table table-bordered table-striped" id="historiTable"> <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Buku</th>
                            <th>Judul Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Pengembalian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr data-id-buku="' . htmlspecialchars($row['id_buku']) . '">';
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . htmlspecialchars($row['id_buku']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['judul_buku']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tanggal_pinjam']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tanggal_pengembalian']) . "</td>";
                                echo '<td>
                                            <button class="btn btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#hapusHistoriModal" data-id="' . htmlspecialchars($row['id_buku']) . '">
                                                Hapus
                                            </button>
                                        </td>';
                                echo "</tr>";
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center">Tidak ada data histori pengembalian.</td></tr>'; }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<div class="modal fade" id="hapusHistoriModal" tabindex="-1" aria-labelledby="hapusHistoriModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hapusHistoriModalLabel">Hapus Data Histori Pengembalian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data histori pengembalian untuk buku ID: <strong id="modalDeleteBookIdDisplay"></strong> ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Ya, Hapus</button>
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
    let idBukuToDelete = null; // Menyimpan ID buku yang akan dihapus

    // Inisialisasi modal Hapus
    const hapusHistoriModal = new bootstrap.Modal(document.getElementById('hapusHistoriModal')); // Changed modal ID
    const modalDeleteBookIdDisplay = document.getElementById('modalDeleteBookIdDisplay');

    // Script untuk mengisi data pada modal hapus
    document.querySelectorAll('[data-bs-target="#hapusHistoriModal"]').forEach(button => { // Changed modal ID in data-bs-target
        button.addEventListener('click', function() {
            idBukuToDelete = this.getAttribute('data-id');
            if (modalDeleteBookIdDisplay) {
                modalDeleteBookIdDisplay.textContent = idBukuToDelete;
            }
        });
    });

    // Listener untuk tombol "Ya, Hapus" di modal hapus
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (idBukuToDelete) {
            // Kirim permintaan fetch ke PHP untuk menghapus
            // Pastikan action dan file PHP sesuai dengan tujuan (histori_pengembalian.php)
            fetch(`histori_pengembalian.php?action=delete_histori&id=${encodeURIComponent(idBukuToDelete)}`) // Changed action and file name
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hapus baris dari tabel secara langsung
                        const rowToRemove = document.querySelector(`tr[data-id-buku="${data.id_buku}"]`);
                        if (rowToRemove) {
                            rowToRemove.remove();
                            // Opsional: perbarui nomor urut jika ada
                            updateRowNumbers();
                        }
                        hapusHistoriModal.hide(); // Sembunyikan modal // Changed modal ID
                        alert('Data histori pengembalian berhasil dihapus!'); // Feedback sukses
                    } else {
                        alert('Gagal menghapus data histori pengembalian: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus data histori pengembalian.');
                });
        }
    });

    // Fungsi untuk memperbarui nomor urut (No) setelah penghapusan
    function updateRowNumbers() {
        const tableRows = document.querySelectorAll('#historiTable tbody tr'); // Changed table ID
        tableRows.forEach((row, index) => {
            const noCell = row.querySelector('td:first-child');
            if (noCell) {
                noCell.textContent = index + 1;
            }
        });
    }
</script>
</body>
</html>