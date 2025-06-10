<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola Anggota</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f1f5f9;
    }

    .sidebar {
      min-height: 100vh;
      background-color: #2f3e46;
      color: white;
      padding-top: 20px;
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

    .sidebar .image-box {
      margin-top: 50px;
      text-align: center;
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
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 sidebar position-relative">
      <h5>Pustakawan<br><small>admin</small></h5>
      <a href="kelola_anggota.php">kelola anggota</a>
      <a href="kelola_katalog.php">kelola katalog buku</a>
      <a href="kelola_peminjaman.php">kelola Peminjaman buku</a>
      <a href="kelola_pengembalian.php">kelola Pengembalian buku</a>
      <a href="kelola_denda.php">kelola denda</a>
      <a href="#" class="logout">Logout</a>
      <div class="image-box">
        <img src="https://via.placeholder.com/80" alt="icon" />
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 main-content">
      <h4>Kelola anggota</h4>
      <div class="d-flex flex-wrap gap-2 align-items-center mb-3 mt-3">
        <input type="text" class="form-control form-control-md me-2" placeholder="Nama anggota" style="max-width: 300px;" />
        <button class="btn btn-tambah btn-md">Tambah Anggota</button>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead>
          <tr>
            <th>No</th>
            <th>Id siswa</th>
            <th>Nama siswa</th>
            <th>Jurusan</th>
            <th>Kelas</th>
            <th>Semester</th>
            <th colspan="2">Action</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>1</td>
            <td>RPL1111</td>
            <td>WAWAN</td>
            <td>Rekayasa perangkat lunak</td>
            <td>XI</td>
            <td>3</td>
            <td><button class="btn btn-sm btn-edit">Edit</button></td>
            <td><button class="btn btn-sm btn-delete">Delete</button></td>
          </tr>
          <!-- Baris data lain tetap sama -->
          <!-- ... -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>
