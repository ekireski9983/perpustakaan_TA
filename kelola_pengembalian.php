<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Cek koneksi
if (!$koneksi) {
    // Log the error instead of dying directly in production
    error_log("Koneksi gagal: " . mysqli_connect_error());
    die("Koneksi gagal: " . mysqli_connect_error()); // For development, keep die
}

// Menangani perubahan status pengembalian
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    // IMPORTANT: Changed variable name to reflect that it's 'nama_peminjam' being passed
    $identifier_to_update = $_POST['id_buku_status']; // This now carries 'nama_peminjam'
    $new_status = $_POST['new_status'];

    // Validate the status to prevent arbitrary values
    $allowed_statuses = ['Belum Dikembalikan', 'Sudah Dikembalikan'];
    if (!in_array($new_status, $allowed_statuses)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Nilai status tidak valid."]);
        exit();
    }

    // Gunakan prepared statement untuk UPDATE
    // *** CRITICAL FIX: Changed WHERE clause to use 'nama_peminjam'
    $update_query = "UPDATE data_pengembalian SET status_pengembalian = ? WHERE nama_peminjam = ?";
    $stmt = mysqli_prepare($koneksi, $update_query);
    
    if ($stmt === false) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Prepare failed: " . mysqli_error($koneksi)]);
        exit();
    }
    // "ss" because both $new_status and $identifier_to_update are strings
    mysqli_stmt_bind_param($stmt, "ss", $new_status, $identifier_to_update);

    if (mysqli_stmt_execute($stmt)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'identifier' => $identifier_to_update, 'new_status' => $new_status]);
        mysqli_stmt_close($stmt);
        exit();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Error updating status: " . mysqli_stmt_error($stmt)]);
        mysqli_stmt_close($stmt);
        exit();
    }
}

// Menangani penghapusan data pengembalian
if (isset($_GET['action']) && $_GET['action'] == 'delete_pengembalian' && isset($_GET['id'])) {
    // IMPORTANT: The 'id' GET parameter is what comes from the JavaScript data-id attribute.
    // Based on your HTML/JS, this 'id' is now 'nama_peminjam'.
    $identifier_to_delete = $_GET['id']; // This now carries 'nama_peminjam'

    // Gunakan prepared statement untuk DELETE
    // *** CRITICAL FIX: Changed WHERE clause to use 'nama_peminjam'
    $delete_query = "DELETE FROM data_pengembalian WHERE nama_peminjam = ?";
    $stmt = mysqli_prepare($koneksi, $delete_query);
    
    if ($stmt === false) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Prepare failed: " . mysqli_error($koneksi)]);
        exit();
    }
    // "s" because $identifier_to_delete is a string
    mysqli_stmt_bind_param($stmt, "s", $identifier_to_delete); 

    if (mysqli_stmt_execute($stmt)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'identifier' => $identifier_to_delete]); // Sending back the identifier for client-side removal
        mysqli_stmt_close($stmt);
        exit();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Error deleting data: " . mysqli_stmt_error($stmt)]);
        mysqli_stmt_close($stmt);
        exit();
    }
}

// Ambil data dari tabel data_pengembalian (tetap di sini untuk tampilan awal)
// We still select all columns as before, to populate the table.
$query = "SELECT * FROM data_pengembalian ORDER BY tanggal_pinjam DESC"; // Added ORDER BY for consistent display
$result = mysqli_query($koneksi, $query);

// Check if the query was successful before proceeding
if (!$result) {
    error_log("Query gagal: " . mysqli_error($koneksi));
    die("Terjadi kesalahan saat mengambil data. Mohon coba lagi nanti.");
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
            <h4>Kelola Pengembalian Buku</h4>
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3 mt-3">
                <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="ketik nama peminjam atau judul buku" style="max-width: 300px;" oninput="filterTable()" />
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="pengembalianTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Peminjam</th>
                            <th>Judul Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Pengembalian</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                // IMPORTANT: data-row-identifier is now 'nama_peminjam' for row identification
                                // The `data-id` for buttons will also pass 'nama_peminjam'
                                echo '<tr data-row-identifier="' . htmlspecialchars($row['nama_peminjam']) . '">';
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . htmlspecialchars($row['nama_peminjam']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['judul_buku']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tanggal_pinjam']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tanggal_kembali']) . "</td>";
                                echo '<td class="status-cell">' . htmlspecialchars($row['status_pengembalian']) . '</td>'; // Add class for easy access
                                echo '<td>
                                        <button class="btn btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#ubahStatusPengembalianModal" 
                                                data-id="' . htmlspecialchars($row['nama_peminjam']) . '" data-status="' . htmlspecialchars($row['status_pengembalian']) . '">
                                            Ubah 
                                        </button>
                                        <button class="btn btn-sm btn-delete mt-1" data-bs-toggle="modal" data-bs-target="#hapusPengembalianModal" data-id="' . htmlspecialchars($row['nama_peminjam']) . '">
                                            Hapus
                                        </button>
                                    </td>';
                                echo "</tr>";
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center">Tidak ada data pengembalian.</td></tr>'; 
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
                <p>Ubah status untuk peminjam: <strong id="modalPeminjamNameDisplay"></strong></p> <div class="d-flex justify-content-between mt-3">
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
                <p>Apakah Anda yakin ingin menghapus data pengembalian untuk peminjam: <strong id="hapusPeminjamNameDisplay"></strong>?</p> </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Renamed variables for clarity: now referring to 'peminjam name' as identifier
    let currentPeminjamToUpdate = null; 
    let currentTableRow = null;       

    // Initialize the 'Ubah Status' modal
    const ubahStatusPengembalianModal = new bootstrap.Modal(document.getElementById('ubahStatusPengembalianModal'));
    const modalPeminjamNameDisplay = document.getElementById('modalPeminjamNameDisplay'); // Changed ID

    // Script to populate data in the status change modal
    document.querySelectorAll('[data-bs-target="#ubahStatusPengembalianModal"]').forEach(button => {
        button.addEventListener('click', function() {
            currentPeminjamToUpdate = this.getAttribute('data-id'); // Gets the 'nama_peminjam'
            currentTableRow = this.closest('tr'); // Get reference to the <tr> row

            if (modalPeminjamNameDisplay) { 
                modalPeminjamNameDisplay.textContent = currentPeminjamToUpdate; // Display the name
            }
        });
    });

    // Listener for "Belum Dikembalikan" and "Sudah Dikembalikan" buttons inside the modal
    document.querySelectorAll('#ubahStatusPengembalianModal .btn').forEach(button => {
        button.addEventListener('click', function() {
            if (currentPeminjamToUpdate) {
                const newStatus = this.getAttribute('data-status-value');

                // Send fetch request to PHP
                fetch('kelola_pengembalian.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    // Send 'nama_peminjam' as 'id_buku_status' (as your PHP is expecting it)
                    body: `action=update_status&id_buku_status=${encodeURIComponent(currentPeminjamToUpdate)}&new_status=${encodeURIComponent(newStatus)}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update status in the table directly without page reload
                        if (currentTableRow) {
                            const statusCell = currentTableRow.querySelector('.status-cell');
                            if (statusCell) {
                                statusCell.textContent = data.new_status; // Update status text
                            }
                        }
                        ubahStatusPengembalianModal.hide(); // Hide the modal
                        alert('Status berhasil diperbarui!'); // Success feedback
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

    // Initialize the Delete modal
    const hapusPengembalianModal = new bootstrap.Modal(document.getElementById('hapusPengembalianModal'));
    let peminjamToDelete = null; // Stores the 'nama_peminjam' to be deleted
    const hapusPeminjamNameDisplay = document.getElementById('hapusPeminjamNameDisplay'); // Changed ID

    // Script to populate data in the delete modal
    document.querySelectorAll('[data-bs-target="#hapusPengembalianModal"]').forEach(button => {
        button.addEventListener('click', function() {
            peminjamToDelete = this.getAttribute('data-id'); // Gets the 'nama_peminjam'
            if (hapusPeminjamNameDisplay) {
                hapusPeminjamNameDisplay.textContent = peminjamToDelete; // Display the name
            }
        });
    });

    // Listener for "Ya, Hapus" button in the delete modal
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (peminjamToDelete) {
            // Send fetch request to PHP for deletion
            // The URL parameter 'id' now passes 'nama_peminjam'
            fetch(`kelola_pengembalian.php?action=delete_pengembalian&id=${encodeURIComponent(peminjamToDelete)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Remove the row from the table directly using the unique data attribute
                        // *** CRITICAL FIX: Changed selector to use data-row-identifier
                        const rowToRemove = document.querySelector(`tr[data-row-identifier="${data.identifier}"]`);
                        if (rowToRemove) {
                            rowToRemove.remove();
                            // Update row numbers after deletion
                            updateRowNumbers();
                        }
                        hapusPengembalianModal.hide(); // Hide the modal
                        alert('Data berhasil dihapus!'); // Success feedback
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

    // Function to update row numbers (No) after deletion or filtering
    function updateRowNumbers() {
        const tableRows = document.querySelectorAll('#pengembalianTable tbody tr');
        let currentNo = 1;
        tableRows.forEach((row) => {
            // Only update if the row is visible (not hidden by filter)
            if (row.style.display !== 'none') {
                const noCell = row.querySelector('td:first-child');
                if (noCell) {
                    noCell.textContent = currentNo++;
                }
            }
        });
    }

    // Function to filter table rows based on search input
    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('pengembalianTable');
        const tr = table.getElementsByTagName('tr');

        let visibleRowCount = 0; 

        for (let i = 1; i < tr.length; i++) { // Start from 1 to skip the header row
            const td = tr[i].getElementsByTagName('td');
            let found = false;

            // Search across relevant columns (Nama Peminjam - index 1, Judul Buku - index 2)
            const namaPeminjamCol = td[1]; // Nama Peminjam
            const judulBukuCol = td[2];    // Judul Buku

            if (namaPeminjamCol && namaPeminjamCol.textContent.toLowerCase().includes(filter)) {
                found = true;
            } else if (judulBukuCol && judulBukuCol.textContent.toLowerCase().includes(filter)) {
                found = true;
            }
            
            tr[i].style.display = found ? "" : "none";
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

