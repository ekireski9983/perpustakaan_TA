<?php

$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");


if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $id_buku_to_update = $_POST['id_buku_status'];
    $new_status = $_POST['new_status'];

    
    $select_query = "SELECT judul_buku, tanggal_pinjam FROM data_pengembalian WHERE id_buku = ?";
    $stmt_select = mysqli_prepare($koneksi, $select_query);
    if ($stmt_select === false) {
        echo json_encode(['success' => false, 'message' => "Prepare select failed: " . mysqli_error($koneksi)]);
        exit();
    }
    mysqli_stmt_bind_param($stmt_select, "s", $id_buku_to_update);
    mysqli_stmt_execute($stmt_select);
    $result_select = mysqli_stmt_get_result($stmt_select);
    $row_data_buku = mysqli_fetch_assoc($result_select);
    mysqli_stmt_close($stmt_select);

    if (!$row_data_buku) {
        echo json_encode(['success' => false, 'message' => "Data buku tidak ditemukan."]);
        exit();
    }

    $judul_buku = $row_data_buku['judul_buku'];
    $tanggal_pinjam = $row_data_buku['tanggal_pinjam'];
    
    
    $tanggal_pengembalian_histori = date('Y-m-d'); 

    
    $update_query = "UPDATE data_pengembalian SET status_pengembalian = ? WHERE id_buku = ?";
    $stmt_update = mysqli_prepare($koneksi, $update_query);
    if ($stmt_update === false) {
        echo json_encode(['success' => false, 'message' => "Prepare update failed: " . mysqli_error($koneksi)]);
        exit();
    }
    mysqli_stmt_bind_param($stmt_update, "ss", $new_status, $id_buku_to_update);

    if (mysqli_stmt_execute($stmt_update)) {
        
        if ($new_status == "Sudah Dikembalikan") {
            $insert_histori_query = "INSERT INTO histori_pengembalian (id_buku, judul_buku, tanggal_pinjam, tanggal_pengembalian) VALUES (?, ?, ?, ?)";
            $stmt_histori = mysqli_prepare($koneksi, $insert_histori_query);
            if ($stmt_histori === false) {
                echo json_encode(['success' => false, 'message' => "Prepare insert histori failed: " . mysqli_error($koneksi)]);
                exit();
            }
            
            mysqli_stmt_bind_param($stmt_histori, "ssss", $id_buku_to_update, $judul_buku, $tanggal_pinjam, $tanggal_pengembalian_histori);

            if (mysqli_stmt_execute($stmt_histori)) {
                mysqli_stmt_close($stmt_histori);
                
                echo json_encode(['success' => true, 'id_buku' => $id_buku_to_update, 'new_status' => $new_status, 'action' => 'status_updated_and_added_to_history']);
            } else {
                echo json_encode(['success' => false, 'message' => "Error inserting into histori: " . mysqli_stmt_error($stmt_histori)]);
            }
        } else {
            
            echo json_encode(['success' => true, 'id_buku' => $id_buku_to_update, 'new_status' => $new_status, 'action' => 'status_updated']);
        }
        mysqli_stmt_close($stmt_update);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => "Error updating status: " . mysqli_stmt_error($stmt_update)]);
        mysqli_stmt_close($stmt_update);
        exit();
    }
}


if (isset($_GET['action']) && $_GET['action'] == 'delete_pengembalian' && isset($_GET['id'])) {
    $id_buku_to_delete = $_GET['id'];

    
    $delete_query = "DELETE FROM data_pengembalian WHERE id_buku = ?";
    $stmt = mysqli_prepare($koneksi, $delete_query);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => "Prepare failed: " . mysqli_error($koneksi)]);
        exit();
    }
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


$query = "SELECT * FROM data_pengembalian";
$result = mysqli_query($koneksi, $query);


if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title> Pengembalian Buku</title>
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
        
        .btn-update-status-belum { background-color: #dc3545; color: white; } 
        .btn-update-status-sudah { background-color: #28a745; color: white; } 
        .btn-edit { background-color: #007bff; color: white; } 
        .btn-delete { background-color: #6c757d; color: white; } 
        .btn-tambah { background-color: #00b4d8; color: white; }
        @media (max-width: 768px) { .main-content { padding: 20px; } }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row d-md-none bg-dark text-white p-2">
        <div class="col">
            <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">☰ Menu</button>
            <span class="ms-3">Pengembalian Buku</span>
        </div>
    </div>

    <div class="row">
        <nav class="col-md-3 d-none d-md-block sidebar min-vh-100 position-relative">
            <h5 class="pt-4">siswa<br /><small>user</small></h5>
            <a href="lihat_anggota.php">lihat anggota</a>
            <a href="katalog_buku.php">katalog buku</a>
            <a href="peminjaman_buku.php">Peminjaman buku</a>
            <a href="pengembalian_buku.php">Pengembalian buku</a>
            <a href="histori_pengembalian.php">histori pengembalian</a>
            <a href="logout.php" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
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
            <a href="histori_pengembalian.php">histori pengembalian</a> 
            <a href="logout.php" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
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
                            <th colspan="2">Action</th> </tr>
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
                                echo '<td class="status-cell">' . htmlspecialchars($row['status_pengembalian']) . '</td>'; // Tambahkan class untuk akses mudah
                                echo '<td>
                                            <button class="btn btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#ubahStatusPengembalianModal" 
                                                data-id="' . htmlspecialchars($row['id_buku']) . '" data-status="' . htmlspecialchars($row['status_pengembalian']) . '">
                                                Ubah
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
                <p>Apakah Anda yakin ingin kembalikan buku</p>
                <input type="hidden" id="returnBookIdPlaceholder" value="">
            </div>
            <div class="modal-footer d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="confirmReturnButton" data-status-value="Belum dikembalikan">tidak</button>
                <button type="button" class="btn btn-primary" id="confirmReturnButton" data-status-value="Sudah Dikembalikan">Ya</button>
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
                <p>Apakah Anda yakin ingin hapus</p>
            </div>
            <div class="modal-footer d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Ya</button>
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
    let currentBookIdToUpdate = null; 
    let currentTableRow = null;       

    
    const ubahStatusPengembalianModal = new bootstrap.Modal(document.getElementById('ubahStatusPengembalianModal'));
    const modalBookIdDisplay = document.getElementById('modalBookIdDisplay');

    
    document.querySelectorAll('[data-bs-target="#ubahStatusPengembalianModal"]').forEach(button => {
        button.addEventListener('click', function() {
            currentBookIdToUpdate = this.getAttribute('data-id');
            const currentStatus = this.getAttribute('data-status'); 
            currentTableRow = this.closest('tr'); 

            if (modalBookIdDisplay) { 
                modalBookIdDisplay.textContent = currentBookIdToUpdate;
            }
        });
    });

    
    document.querySelectorAll('#ubahStatusPengembalianModal .btn').forEach(button => {
        button.addEventListener('click', function() {
            if (currentBookIdToUpdate) {
                const newStatus = this.getAttribute('data-status-value');

                
                fetch('pengembalian_buku.php', { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_status&id_buku_status=${encodeURIComponent(currentBookIdToUpdate)}&new_status=${encodeURIComponent(newStatus)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        
                        if (currentTableRow) {
                            const statusCell = currentTableRow.querySelector('.status-cell');
                            if (statusCell) {
                                statusCell.textContent = data.new_status; 
                            }
                        }
                        alert('buku berhasil dikembalikan');
                        ubahStatusPengembalianModal.hide(); 
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

    
    const hapusPengembalianModal = new bootstrap.Modal(document.getElementById('hapusPengembalianModal'));
    let idBukuToDelete = null; 

    
    document.querySelectorAll('[data-bs-target="#hapusPengembalianModal"]').forEach(button => {
        button.addEventListener('click', function() {
            idBukuToDelete = this.getAttribute('data-id');

        });
    });

    
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (idBukuToDelete) {
            
            fetch(`pengembalian_buku.php?action=delete_pengembalian&id=${encodeURIComponent(idBukuToDelete)}`) // Ensure this points to the correct PHP file
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        
                        const rowToRemove = document.querySelector(`tr[data-id-buku="${data.id_buku}"]`);
                        if (rowToRemove) {
                            rowToRemove.remove();
                            
                            updateRowNumbers();
                        }
                        hapusPengembalianModal.hide(); 
                        alert('Data berhasil dihapus!'); 
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
        let currentNo = 1; // Start counter from 1
        tableRows.forEach((row) => {
            const noCell = row.querySelector('td:first-child');
            if (noCell) {
                noCell.textContent = currentNo++;
            }
        });
    }

    
    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('pengembalianTable');
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) { 
            const td = tr[i].getElementsByTagName('td');
            let found = false;

            
            for (let j = 1; j < td.length; j++) { 
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