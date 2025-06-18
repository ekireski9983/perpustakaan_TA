<?php
// Database connection details
$servername = "localhost"; // Replace with your database server name if different
$username = "root";      // Replace with your database username
$password = "";          // Replace with your database password
$dbname = "perpustakaan";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add Peminjaman
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_peminjaman"])) {
    $judul_peminjaman = $_POST["judul_peminjaman"];
    // In a real application, you would also get other book details like id_buku, author, publisher, etc.
    // For this example, we'll just insert the title and placeholder dates.
    $id_buku = "N/A"; // Placeholder
    $nama_penulis = "N/A"; // Placeholder
    $nama_penerbit = "N/A"; // Placeholder
    $jumlah_halaman = 0; // Placeholder
    $foto = "default.png"; // Placeholder
    $tanggal_pinjam = date("Y-m-d"); // Current date
    $tanggal_kembali = date("Y-m-d", strtotime("+7 days")); // 7 days from now

    $stmt = $conn->prepare("INSERT INTO data_pinjam (id_buku, judul_buku, nama_penulis, nama_penerbit, jumlah_halaman, foto, tanggal_pinjam, tanggal_kembali) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisss", $id_buku, $judul_peminjaman, $nama_penulis, $nama_penerbit, $jumlah_halaman, $foto, $tanggal_pinjam, $tanggal_kembali);

    if ($stmt->execute()) {
        $message = "Peminjaman added successfully!";
        $message_type = "success";
    } else {
        $message = "Error adding peminjaman: " . $stmt->error;
        $message_type = "danger";
    }
    $stmt->close();
}

// Handle Delete Peminjaman
if (isset($_GET["delete_id"])) {
    $id_pinjam = $_GET["delete_id"];

    $stmt = $conn->prepare("DELETE FROM data_pinjam WHERE id_buku = ?");
    $stmt->bind_param("i", $id_pinjam);

    if ($stmt->execute()) {
        $message = "Peminjaman deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting peminjaman: " . $stmt->error;
        $message_type = "danger";
    }
    $stmt->close();
    // Redirect to avoid resubmission on refresh
    header("Location: kelola_peminjaman.php?message=" . urlencode($message) . "&type=" . $message_type);
    exit();
}

// Fetch all peminjaman data
$sql = "SELECT id_buku, judul_buku, nama_penulis, nama_penerbit, jumlah_halaman, foto, tanggal_pinjam, tanggal_pengembalian FROM data_pinjam";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola Peminjaman</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f1f5f9;
    }

    .sidebar {
      background-color: #2f3e46;
      color: white;
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

    .sidebar .image-box img {
      width: 80px;
      opacity: 0.7;
    }

    .main-content {
      padding: 40px;
    }

    .table thead {
      background-color: #f8f9fa;
    }

    .btn-edit {
      background-color: #48cae4;
      color: white;
    }

    .btn-delete {
      background-color: #f94144;
      color: white;
    }

    .btn-tambah {
      background-color: #00b4d8;
      color: white;
    }

    @media (max-width: 768px) {
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
        <span class="ms-3">Kelola Peminjaman</span>
      </div>
    </div>

    <div class="row">
      <nav class="col-md-3 d-none d-md-block sidebar min-vh-100 position-relative pt-4">
        <h5 class="ms-3">Pustakawan<br><small>admin</small></h5>
        <a href="kelola_anggota.php">kelola anggota</a>
        <a href="kelola_katalog.php">kelola katalog buku</a>
        <a href="kelola_peminjaman.php" class="active">kelola Peminjaman buku</a>
        <a href="kelola_pengembalian.php">kelola Pengembalian buku</a>
        <a href="kelola_denda.php">kelola denda</a>
        <a href="#" class="logout">Logout</a>
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
          <a href="#">Logout</a>
          <div class="image-box text-center mt-5">
            <img src="assets/Bootstrap_logo.png" alt="icon" />
          </div>
        </div>
      </div>

      <main class="col-md-9 col-12 main-content">
        <h4>Kelola peminjaman buku</h4>

        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars($_GET['type']); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="kelola_peminjaman.php">
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3 mt-3">
              <input type="text" name="judul_peminjaman" class="form-control form-control-md me-2" placeholder="Judul Peminjaman" style="max-width: 300px;" required />
              <button type="submit" name="add_peminjaman" class="btn btn-tambah btn-md">Tambah Peminjaman</button>
            </div>
        </form>

        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>No</th>
                <th>ID Buku</th>
                <th>Judul Buku</th>
                <th>Nama Penulis</th>
                <th>Nama Penerbit</th>
                <th>Jumlah Halaman</th>
                <th>Foto</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Pengembalian</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result->num_rows > 0) {
                  $no = 1;
                  while($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>" . $no++ . "</td>";
                      echo "<td>" . htmlspecialchars($row["id_pinjam"]) . "</td>";
                      echo "<td>" . htmlspecialchars($row["id_buku"]) . "</td>";
                      echo "<td>" . htmlspecialchars($row["judul_buku"]) . "</td>";
                      echo "<td>" . htmlspecialchars($row["nama_penulis"]) . "</td>";
                      echo "<td>" . htmlspecialchars($row["nama_penerbit"]) . "</td>";
                      echo "<td>" . htmlspecialchars($row["jumlah_halaman"]) . "</td>";
                      echo '<td><img src="upload/' . htmlspecialchars($row["foto"]) . '" alt="Foto Buku" class="img-thumbnail" style="max-width: 80px;" /></td>';
                      echo "<td>" . htmlspecialchars($row["tanggal_pinjam"]) . "</td>";
                      echo "<td>" . htmlspecialchars($row["tanggal_pengembalian"]) . "</td>";
                      echo '<td><a href="kelola_peminjaman.php?delete_id=' . htmlspecialchars($row["id_buku"]) . '" class="btn btn-sm btn-delete" onclick="return confirm(\'Are you sure you want to delete this loan?\');">Delete</a></td>';
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='11' class='text-center'>tidak ada buku yang dipinjam.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>