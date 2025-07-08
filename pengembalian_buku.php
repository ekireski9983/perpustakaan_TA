<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Menangani perubahan status pengembalian
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $id_buku_to_update = $_POST['id_buku_status'];
    $new_status = $_POST['new_status'];

    // Gunakan prepared statement untuk UPDATE
    $update_query = "UPDATE data_pengembalian SET status_pengembalian = ? WHERE id_buku = ?";
    $stmt = mysqli_prepare($koneksi, $update_query);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => "Prepare failed: " . mysqli_error($koneksi)]);
        exit();
    }
    mysqli_stmt_bind_param($stmt, "ss", $new_status, $id_buku_to_update); // "ss" karena keduanya string

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'id_buku' => $id_buku_to_update, 'new_status' => $new_status]);
        mysqli_stmt_close($stmt);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => "Error updating status: " . mysqli_stmt_error($stmt)]);
        mysqli_stmt_close($stmt);
        exit();
    }
}

// Menangani penghapusan data pengembalian
if (isset($_GET['action']) && $_GET['action'] == 'delete_pengembalian' && isset($_GET['id'])) {
    $id_buku_to_delete = $_GET['id'];

    // Gunakan prepared statement untuk DELETE
    $delete_query = "DELETE FROM data_pengembalian WHERE id_buku = ?";
    $stmt = mysqli_prepare($koneksi, $delete_query);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => "Prepare failed: " . mysqli_error($koneksi)]);
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $id_buku_to_delete); // "s" karena id_buku adalah string

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

// Ambil data dari tabel data_pengembalian (tetap di sini untuk tampilan awal)
$query = "SELECT * FROM data_pengembalian";
$result = mysqli_query($koneksi, $query);
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
        /* Updated button colors for consistency and better visual feedback */
        .btn-update-status-belum { background-color: #dc3545; color: white; } /* Bootstrap danger */
        .btn-update-status-sudah { background-color: #28a745; color: white; } /* Bootstrap success */
        .btn-edit { background-color: #007bff; color: white; } /* Bootstrap primary for edit */
        .btn-delete { background-color: #6c757d; color: white; } /* Bootstrap secondary for delete */
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
            <h5 class="pt-4">siswa<br /><small>user</small></h5>
            <a href="lihat_anggota.php">lihat anggota</a>
            <a href="katalog_buku.php">katalog buku</a>
            <a href="peminjaman_buku.php">Peminjaman buku</a>
            <a href="pengembalian_buku.php">Pengembalian buku</a>
            <a href="denda_keterlambatan.php">denda keterlambatan</a>
            <a href="logout.php">Logout</a>
            <div class="image-box text-center mt-5">
                <img src="assets/logo_sekolah.png" alt="icon" />
            </div>
        </nav>
        
        <div class="offcanvas offcanvas-start sidebar text-white" tabindex="-1" id="sidebarMenu">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">user</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
            <a href="lihat_anggota.php">lihat anggota</a>
            <a href="katalog_buku.php">katalog buku</a>
            <a href="peminjaman_buku.php">Peminjaman buku</a>
            <a href="pengembalian_buku.php">Pengembalian buku</a>
            <a href="denda_keterlambatan.php">denda keterlambatan</a>
            <a href="logout.php">Logout</a>
                <div class="image-box text-center mt-5">
                    <img src="assets/logo_sekolah.png" alt="icon" />
                </div>
            </div>
        </div>

        <main class="col-md-9 col-12 main-content">
            <h4>Pengembalian Buku</h4>
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3 mt-3">
                <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="ketik id buku atau judul buku" style="max-width: 300px;" oninput="filterTable()" />
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="pengembalianTable">
                    <thead>
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
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Tambahkan data-row-id untuk identifikasi baris yang unik
                                echo '<tr data-id-buku="' . htmlspecialchars($row['id_buku']) . '">';
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . htmlspecialchars($row['id_buku']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['judul_buku']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tanggal_pinjam']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tanggal_pengembalian']) . "</td>";
                                echo '<td class="status-cell">' . htmlspecialchars($row['status_pengembalian']) . '</td>'; // Tambahkan class untuk akses mudah
                                echo '<td>
                                        <button class="btn btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#ubahStatusPengembalianModal" 
                                                data-id="' . htmlspecialchars($row['id_buku']) . '" data-status="' . htmlspecialchars($row['status_pengembalian']) . '">
                                            Ubah 
                                        </button>
                                    </td>';
                                echo '<td>
                                        <button class="btn btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#hapusPengembalianModal" data-id="' . htmlspecialchars($row['id_buku']) . '">
                                            Delete
                                        </button>
                                    </td>';
                                echo "</tr>";
                            }
                        } else {
                            echo '<tr><td colspan="8" class="text-center">Tidak ada data pengembalian.</td></tr>';
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
                <h5 class="modal-title" id="ubahStatusPengembalianModalLabel">Ubah Status Pengembalian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Ubah status untuk buku ID: <strong id="modalBookIdDisplay"></strong></p>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-update-status-belum" data-status-value="Belum Dikembalikan">Belum Dikembalikan</button>
                    <button type="button" class="btn btn-update-status-sudah" data-status-value="Sudah Dikembalikan">Sudah Dikembalikan</button>
                </div>
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
                <p>Apakah Anda yakin ingin menghapus data ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let currentBookIdToUpdate = null; // Menyimpan ID buku yang sedang dioperasikan (untuk update status)
    let currentTableRow = null;       // Menyimpan referensi ke baris tabel yang sedang dioperasikan

    // Inisialisasi modal Ubah Status
    const ubahStatusPengembalianModal = new bootstrap.Modal(document.getElementById('ubahStatusPengembalianModal'));
    const modalBookIdDisplay = document.getElementById('modalBookIdDisplay');

    // Script untuk mengisi data pada modal ubah status
    document.querySelectorAll('[data-bs-target="#ubahStatusPengembalianModal"]').forEach(button => {
        button.addEventListener('click', function() {
            currentBookIdToUpdate = this.getAttribute('data-id');
            const currentStatus = this.getAttribute('data-status'); // Not used directly in modal, but good to keep
            currentTableRow = this.closest('tr'); // Dapatkan referensi ke baris <tr>

            if (modalBookIdDisplay) { // Ensure element exists before trying to set textContent
                modalBookIdDisplay.textContent = currentBookIdToUpdate;
            }
        });
    });

    // Listener untuk tombol "Belum Dikembalikan" dan "Sudah Dikembalikan" di dalam modal
    document.querySelectorAll('#ubahStatusPengembalianModal .btn').forEach(button => {
        button.addEventListener('click', function() {
            if (currentBookIdToUpdate) {
                const newStatus = this.getAttribute('data-status-value');

                // Kirim permintaan fetch ke PHP
                fetch('kelola_pengembalian.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_status&id_buku_status=${encodeURIComponent(currentBookIdToUpdate)}&new_status=${encodeURIComponent(newStatus)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Perbarui status di tabel secara langsung tanpa reload
                        if (currentTableRow) {
                            const statusCell = currentTableRow.querySelector('.status-cell');
                            if (statusCell) {
                                statusCell.textContent = data.new_status; // Perbarui teks status
                                // Optional: perbarui warna latar belakang sel status jika diinginkan
                                // statusCell.style.backgroundColor = (data.new_status === 'Sudah Dikembalikan') ? '#d4edda' : '#f8d7da';
                            }
                        }
                        ubahStatusPengembalianModal.hide(); // Sembunyikan modal
                        alert('Status berhasil diperbarui!'); // Feedback sukses
                    } else {
                        alert('Gagal mengubah status: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat berkomunikasi dengan server.');
                });
            }
        });
    });

    // Inisialisasi modal Hapus
    const hapusPengembalianModal = new bootstrap.Modal(document.getElementById('hapusPengembalianModal'));
    let idBukuToDelete = null; // Menyimpan ID buku yang akan dihapus
    const hapusIdPengembalianDisplay = document.getElementById('hapusIdPengembalianDisplay');

    // Script untuk mengisi data pada modal hapus
    document.querySelectorAll('[data-bs-target="#hapusPengembalianModal"]').forEach(button => {
        button.addEventListener('click', function() {
            idBukuToDelete = this.getAttribute('data-id');
            if (hapusIdPengembalianDisplay) {
                hapusIdPengembalianDisplay.textContent = idBukuToDelete;
            }
        });
    });

    // Listener untuk tombol "Ya, Hapus" di modal hapus
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (idBukuToDelete) {
            // Kirim permintaan fetch ke PHP untuk menghapus
            fetch(`kelola_pengembalian.php?action=delete_pengembalian&id=${encodeURIComponent(idBukuToDelete)}`)
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
                        hapusPengembalianModal.hide(); // Sembunyikan modal
                        alert('Data berhasil dihapus!'); // Feedback sukses
                    } else {
                        alert('Gagal menghapus data: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus data.');
                });
        }
    });

    // Fungsi untuk memperbarui nomor urut (No) setelah penghapusan
    function updateRowNumbers() {
        const tableRows = document.querySelectorAll('#pengembalianTable tbody tr');
        tableRows.forEach((row, index) => {
            const noCell = row.querySelector('td:first-child');
            if (noCell) {
                noCell.textContent = index + 1;
            }
        });
    }

    // Fungsi untuk memfilter baris tabel berdasarkan input pencarian
    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('pengembalianTable');
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) { // Mulai dari 1 untuk melewati baris header
            const td = tr[i].getElementsByTagName('td');
            let found = false;

            // Iterasi melalui setiap kolom di baris saat ini (kecuali kolom "No" di index 0)
            for (let j = 1; j < td.length; j++) { // Mulai dari index 1 (Id Buku)
                if (td[j]) {
                    const txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break; 
                    }
                }
            }
            tr[i].style.display = found ? "" : "none";
        }
    }
</script>
</body>
</html>