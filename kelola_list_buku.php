<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Menangani penghapusan data buku
if (isset($_GET['delete_id'])) {
    $id_buku_to_delete = $_GET['delete_id'];

    // Query untuk menghapus data dari tabel data_list_buku
    $delete_query = "DELETE FROM data_list_buku WHERE id_buku = ?";
    $stmt = mysqli_prepare($koneksi, $delete_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $id_buku_to_delete); // "s" for string, adjust if id_buku is numeric
        if (mysqli_stmt_execute($stmt)) {
            // Redirect kembali ke halaman ini setelah penghapusan berhasil
            header("Location: kelola_list_buku.php");
            exit();
        } else {
            echo "<script>alert('Error menghapus data: " . mysqli_stmt_error($stmt) . "');</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Error menyiapkan statement: " . mysqli_error($koneksi) . "');</script>";
    }
}

// Ambil data dari tabel data_list_buku untuk ditampilkan
$query_select_buku = "SELECT id_buku, judul_buku, nama_penulis, nama_penerbit, isbn, tanggal_ditambahkan FROM data_list_buku";
$result_buku = mysqli_query($koneksi, $query_select_buku);

// Tutup koneksi database di akhir script
// (Akan dipindahkan ke setelah HTML agar koneksi tetap terbuka selama proses rendering)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kelola List Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f1f5f9;
            margin: 0;
        }

        .sidebar {
            background-color: #2f3e46;
            color: white;
        }

        .sidebar h5 {
            margin-left: 20px;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #00b4d8;
            border-radius: 5px;
        }

        .sidebar .logout {
            position: absolute;
            bottom: 20px;
            width: 100%;
        }

        .image-box img {
            width: 80px;
            opacity: 0.7;
        }

        .main-content {
            padding: 40px;
        }

        .table thead {
            background-color: #f8f9fa;
        }

        .btn-delete {
            background-color: #f94144;
            color: white;
        }

        @media (max-width: 768px) {
            .sidebar .logout {
                position: static;
                margin-top: 30px;
            }
            .main-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row d-md-none bg-dark text-white p-2">
            <div class="col">
                <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                    ☰ Menu
                </button>
                <span class="ms-3">Dashboard Pustakawan</span>
            </div>
        </div>

        <div class="row">
            <nav class="col-md-3 d-none d-md-block sidebar min-vh-100 position-relative">
                <h5 class="pt-4">Pustakawan<br /><small>admin</small></h5>
                <a href="kelola_pustakawan.php">Kelola Pustakawan</a>
                <a href="kelola_anggota.php">Kelola Anggota</a>
                <a href="kelola_katalog.php">Kelola Katalog Buku</a>
                <a href="kelola_list_buku.php" class="active">Kelola list buku</a>
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
                    <a href="kelola_list_buku.php" class="active">Kelola list buku</a>
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
                <h4>Kelola List Buku</h4>
                <p>Berikut adalah daftar buku yang tersedia di perpustakaan.</p>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="bookTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Buku</th>
                                <th>Judul Buku</th>
                                <th>Nama Penulis</th>
                                <th>Nama Penerbit</th>
                                <th>ISBN</th>
                                <th>Tanggal Ditambahkan</th>
                                <th>Aksi</th> </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if (mysqli_num_rows($result_buku) > 0) {
                                while ($row = mysqli_fetch_assoc($result_buku)) {
                                    echo "<tr>";
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td>" . htmlspecialchars($row['id_buku']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['judul_buku']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nama_penulis']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nama_penerbit']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['isbn']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['tanggal_ditambahkan']) . "</td>";
                                    echo '<td>
                                            <button class="btn btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#hapusBukuModal" data-id="' . htmlspecialchars($row['id_buku']) . '">Hapus</button>
                                          </td>';
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>Tidak ada data buku yang ditemukan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="hapusBukuModal" tabindex="-1" aria-labelledby="hapusBukuModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hapusBukuModalLabel">Konfirmasi Hapus Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus buku dengan ID: <strong id="bukuIdToDelete"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="confirmDeleteButton" class="btn btn-danger">Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk mengisi ID Buku pada modal hapus
        const hapusBukuModal = document.getElementById('hapusBukuModal');
        hapusBukuModal.addEventListener('show.bs.modal', event => {
            // Button that triggered the modal
            const button = event.relatedTarget;
            // Extract info from data-bs-* attributes
            const bookId = button.getAttribute('data-id');

            // Update the modal's content.
            const modalBodyInput = hapusBukuModal.querySelector('#bukuIdToDelete');
            const confirmDeleteButton = hapusBukuModal.querySelector('#confirmDeleteButton');

            modalBodyInput.textContent = bookId; // Display the book ID in the modal
            confirmDeleteButton.href = 'kelola_list_buku.php?delete_id=' + bookId; // Set the href for deletion
        });
    </script>
</body>
</html>
<?php
// Tutup koneksi database setelah semua data ditampilkan
mysqli_close($koneksi);
?>