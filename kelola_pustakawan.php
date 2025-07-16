<?php

$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");


if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}


$query_select_pustakawan = "SELECT * FROM data_pustakawan";
$result = mysqli_query($koneksi, $query_select_pustakawan);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $id_pustakawan = $_POST['id'];
    $nama_pustakawan = $_POST['nama'];
    $jabatan = $_POST['jabatan'];

    
    mysqli_begin_transaction($koneksi);

    try {
        
        $insert_pustakawan_query = "INSERT INTO data_pustakawan (id_pustakawan, nama_pustakawan, jabatan) VALUES (?, ?, ?)";
        $stmt_pustakawan = mysqli_prepare($koneksi, $insert_pustakawan_query);
        if (!$stmt_pustakawan) {
            throw new Exception("Error preparing insert pustakawan statement: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param($stmt_pustakawan, "sss", $id_pustakawan, $nama_pustakawan, $jabatan);
        if (!mysqli_stmt_execute($stmt_pustakawan)) {
            throw new Exception("Error inserting pustakawan data: " . mysqli_stmt_error($stmt_pustakawan));
        }
        mysqli_stmt_close($stmt_pustakawan);

        
        $username = $nama_pustakawan; 
        $password = $id_pustakawan; 
        $role = 'admin'; 

        $insert_user_query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt_user = mysqli_prepare($koneksi, $insert_user_query);
        if (!$stmt_user) {
            throw new Exception("Error preparing user statement: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param($stmt_user, "sss", $username, $password, $role);
        if (!mysqli_stmt_execute($stmt_user)) {
            throw new Exception("Error inserting user data: " . mysqli_stmt_error($stmt_user));
        }
        mysqli_stmt_close($stmt_user);

        mysqli_commit($koneksi);
        header("Location: kelola_pustakawan.php"); 
        exit();
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo "Error adding record: " . $e->getMessage();
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    
    $original_id_pustakawan = $_POST['originalId'];
    $original_nama_pustakawan = $_POST['originalNama'];

    
    $new_id_pustakawan = $_POST['editId'];
    $new_nama_pustakawan = $_POST['editNama'];
    $new_jabatan = $_POST['editJabatan'];

    
    mysqli_begin_transaction($koneksi);

    try {
        
        $update_pustakawan_query = "UPDATE data_pustakawan SET id_pustakawan=?, nama_pustakawan=?, jabatan=? WHERE id_pustakawan=?";
        $stmt_pustakawan = mysqli_prepare($koneksi, $update_pustakawan_query);

        if (!$stmt_pustakawan) {
            throw new Exception("Error preparing pustakawan data update statement: " . mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param($stmt_pustakawan, "ssss",
            $new_id_pustakawan,
            $new_nama_pustakawan,
            $new_jabatan,
            $original_id_pustakawan 
        );

        if (!mysqli_stmt_execute($stmt_pustakawan)) {
            throw new Exception("Error updating pustakawan data: " . mysqli_stmt_error($stmt_pustakawan));
        }
        mysqli_stmt_close($stmt_pustakawan);

        
        $update_user_query = "UPDATE users SET username=?, password=? WHERE password=?";
        $stmt_user = mysqli_prepare($koneksi, $update_user_query);

        if (!$stmt_user) {
            throw new Exception("Error preparing user account update statement: " . mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param($stmt_user, "sss",
            $new_nama_pustakawan,    
            $new_id_pustakawan,  
            $original_id_pustakawan 
        );

        if (!mysqli_stmt_execute($stmt_user)) {
            throw new Exception("Error updating user account: " . mysqli_stmt_error($stmt_user));
        }
        mysqli_stmt_close($stmt_user);

        
        mysqli_commit($koneksi);
        header("Location: kelola_pustakawan.php"); 
        exit();

    } catch (Exception $e) {
        
        mysqli_rollback($koneksi);
        echo "Error updating record: " . $e->getMessage();
    }
}


if (isset($_GET['id'])) {
    $id_pustakawan_to_delete = $_GET['id'];

    
    mysqli_begin_transaction($koneksi);

    try {
        
        $get_nama_query = "SELECT nama_pustakawan FROM data_pustakawan WHERE id_pustakawan = ?";
        $stmt_get_nama = mysqli_prepare($koneksi, $get_nama_query);
        if (!$stmt_get_nama) {
            throw new Exception("Error preparing select nama statement: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param($stmt_get_nama, "s", $id_pustakawan_to_delete);
        if (!mysqli_stmt_execute($stmt_get_nama)) {
            throw new Exception("Error executing select nama statement: " . mysqli_stmt_error($stmt_get_nama));
        }
        $result_get_nama = mysqli_stmt_get_result($stmt_get_nama);
        $row_nama_pustakawan = mysqli_fetch_assoc($result_get_nama);
        mysqli_stmt_close($stmt_get_nama);

        $nama_pustakawan_for_user_deletion = $row_nama_pustakawan['nama_pustakawan'] ?? null;
        
        if (is_null($nama_pustakawan_for_user_deletion)) {

        }


        
        $delete_pustakawan_query = "DELETE FROM data_pustakawan WHERE id_pustakawan=?";
        $stmt_pustakawan = mysqli_prepare($koneksi, $delete_pustakawan_query);
        if (!$stmt_pustakawan) {
            throw new Exception("Error preparing delete pustakawan statement: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param($stmt_pustakawan, "s", $id_pustakawan_to_delete);
        if (!mysqli_stmt_execute($stmt_pustakawan)) {
            throw new Exception("Error deleting pustakawan data: " . mysqli_stmt_error($stmt_pustakawan));
        }
        mysqli_stmt_close($stmt_pustakawan);

       
        $delete_users_query = "DELETE FROM users WHERE username=?"; 
        $stmt_user = mysqli_prepare($koneksi, $delete_users_query);
        if (!$stmt_user) {
            throw new Exception("Error preparing delete user statement: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param($stmt_user, "s", $id_pustakawan_to_delete); 
        if (!mysqli_stmt_execute($stmt_user)) {
            throw new Exception("Error deleting user data: " . mysqli_stmt_error($stmt_user));
        }
        mysqli_stmt_close($stmt_user);

    
        mysqli_commit($koneksi);
        header("Location: kelola_pustakawan.php"); 
        exit();
    } catch (Exception $e) {
        
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
                            <th>Action</th>
                            <th>Action</th>
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
                                        <button class="btn btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#editPustakawanModal" data-id="' . htmlspecialchars($row['id_pustakawan']) . '" data-nama="' . htmlspecialchars($row['nama_pustakawan']) . '" data-jabatan="' . htmlspecialchars($row['jabatan']) . '">Edit</button>
                                    </td>';
                                echo '<td>
                                        <button class="btn btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#hapusPustakawanModal" data-id="' . htmlspecialchars($row['id_pustakawan']) . '">Hapus</button>
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
    
    const editButtons = document.querySelectorAll('[data-bs-target="#editPustakawanModal"]');
    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const jabatan = button.getAttribute('data-jabatan');

        
            document.getElementById('originalId').value = id;
            document.getElementById('originalNama').value = nama; 

            
            document.getElementById('editId').value = id;
            document.getElementById('editNama').value = nama;
            document.getElementById('editJabatan').value = jabatan;
        });
    });

    
    const deleteButtons = document.querySelectorAll('[data-bs-target="#hapusPustakawanModal"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            document.getElementById('hapusId').value = id;
        });
    });

    
    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('pustakawanTable'); 
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) { 
            const td = tr[i].getElementsByTagName('td');
            let found = false;

            
            if (td[2]) { 
                const txtValue = td[2].textContent || td[2].innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                }
            }
            tr[i].style.display = found ? "" : "none"; 
        }
    }
</script>
</body>
</html>