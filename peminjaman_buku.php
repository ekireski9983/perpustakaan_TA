<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Siswa</title>
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

    /* Style for the search bar */
    .search-bar {
      margin-bottom: 20px;
    }

    @media (max-width: 768px) {
      .sidebar .logout {
        position: static;
        margin-top: 30px;
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
        <span class="ms-3">Dashboard Siswa</span>
      </div>
    </div>

    <div class="row">
      <nav class="col-md-3 d-none d-md-block sidebar min-vh-100 position-relative">
        <h5 class="pt-4">Siswa<br /><small>user</small></h5>
        <a href="lihat_anggota.php">Lihat Anggota</a>
        <a href="katalog_buku.php">Katalog Buku</a>
        <a href="peminjaman_buku.php">Peminjaman Buku</a>
        <a href="pengembalian_buku.php">Pengembalian Buku</a>
        <a href="denda_keterlambatan.php">Denda Keterlambatan</a>
        <a href="logout.php">Logout</a>
        <div class="image-box text-center mt-5">
          <img src="assets/Bootstrap_logo.png" alt="icon" />
        </div>
      </nav>

      <div class="offcanvas offcanvas-start sidebar" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="sidebarMenuLabel">Siswa</h5>
          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <h5 class="pt-4">Siswa<br /><small>user</small></h5>
          <a href="lihat_anggota.php">Lihat Anggota</a>
          <a href="katalog_buku.php">Katalog Buku</a>
          <a href="peminjaman_buku.php">Peminjaman Buku</a>
          <a href="pengembalian_buku.php">Pengembalian Buku</a>
          <a href="denda_keterlambatan.php">Denda Keterlambatan</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>

      <main class="col-md-9 col-12 main-content">
        <h4>Peminjaman buku</h4>

        <div class="input-group search-bar">
            <input type="text" id="searchInput" class="form-control form-control-md me-2" placeholder="mencari buku yang mau dipinjam" style="max-width: 300px;" oninput="filterCards()" />
        </div>

        <div class="row" id="bookCardsContainer">
          <div class="col-md-6 col-lg-4 mb-4 book-card">
            <div class="card shadow-sm">
              <img src="upload/68522c0484a1e3.49333472.jpg" class="card-img-top" alt="Book Cover 1">
              <div class="card-body">
                <h5 class="card-title">Judul Buku 1</h5>
                <h6 class="card-subtitle mb-2 text-muted">Penulis: Nama Penulis 1</h6>
                <p class="card-text">Deskripsi singkat tentang Buku 1. Ini adalah tempat untuk memberikan ringkasan atau detail penting.</p>
                <div class="d-flex justify-content-between align-items-center">
                  <button type="button" class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#bookModal"
                          data-action="pinjam"
                          data-book-id="1"
                          data-title="Judul Buku 1"
                          data-author="Nama Penulis 1"
                          data-publisher="Penerbit 1"
                          data-isbn="978-1234567890"
                          data-pages="250">Pinjam</button>
                  <button type="button" class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#bookModal"
                          data-action="edit"
                          data-book-id="1"
                          data-title="Judul Buku 1"
                          data-author="Nama Penulis 1"
                          data-publisher="Penerbit 1"
                          data-isbn="978-1234567890"
                          data-pages="250">Edit</button>
                  <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#bookModal"
                          data-action="hapus"
                          data-book-id="1"
                          data-title="Judul Buku 1">Hapus</button>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-4 mb-4 book-card">
            <div class="card shadow-sm">
              <img src="upload/68522c0484a1e3.49333472.jpg" class="card-img-top" alt="Book Cover 2">
              <div class="card-body">
                <h5 class="card-title">Judul Buku 2</h5>
                <h6 class="card-subtitle mb-2 text-muted">Penulis: Nama Penulis 2</h6>
                <p class="card-text">Deskripsi singkat tentang Buku 2. Ini adalah tempat untuk memberikan ringkasan atau detail penting.</p>
                <div class="d-flex justify-content-between align-items-center">
                  <button type="button" class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#bookModal"
                          data-action="pinjam"
                          data-book-id="2"
                          data-title="Judul Buku 2"
                          data-author="Nama Penulis 2"
                          data-publisher="Penerbit 2"
                          data-isbn="978-0987654321"
                          data-pages="300">Pinjam</button>
                  <button type="button" class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#bookModal"
                          data-action="edit"
                          data-book-id="2"
                          data-title="Judul Buku 2"
                          data-author="Nama Penulis 2"
                          data-publisher="Penerbit 2"
                          data-isbn="978-0987654321"
                          data-pages="300">Edit</button>
                  <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#bookModal"
                          data-action="hapus"
                          data-book-id="2"
                          data-title="Judul Buku 2">Hapus</button>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-4 mb-4 book-card">
            <div class="card shadow-sm">
              <img src="upload/68522c0484a1e3.49333472.jpg" class="card-img-top" alt="Book Cover 3">
              <div class="card-body">
                <h5 class="card-title">Judul Buku 3</h5>
                <h6 class="card-subtitle mb-2 text-muted">Penulis: Nama Penulis 3</h6>
                <p class="card-text">Deskripsi singkat tentang Buku 3. Ini adalah tempat untuk memberikan ringkasan atau detail penting.</p>
                <div class="d-flex justify-content-between align-items-center">
                  <button type="button" class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#bookModal"
                          data-action="pinjam"
                          data-book-id="3"
                          data-title="Judul Buku 3"
                          data-author="Nama Penulis 3"
                          data-publisher="Penerbit 3"
                          data-isbn="978-1122334455"
                          data-pages="180">Pinjam</button>
                  <button type="button" class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#bookModal"
                          data-action="edit"
                          data-book-id="3"
                          data-title="Judul Buku 3"
                          data-author="Nama Penulis 3"
                          data-publisher="Penerbit 3"
                          data-isbn="978-1122334455"
                          data-pages="180">Edit</button>
                  <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#bookModal"
                          data-action="hapus"
                          data-book-id="3"
                          data-title="Judul Buku 3">Hapus</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <div class="modal fade" id="bookModal" tabindex="-1" aria-labelledby="bookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="bookModalLabel"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="bookForm" action="#" method="POST">
            <input type="hidden" id="modalBookId" name="book_id">
            <input type="hidden" id="modalAction" name="action">

            <div class="mb-3">
              <label for="modalTitle" class="form-label">id buku</label>
              <input type="text" class="form-control" id="modalTitle" readonly>
            </div>
            <div class="mb-3">
              <label for="modalAuthor" class="form-label">isbn</label>
              <input type="text" class="form-control" id="modalAuthor" readonly>
            </div>
            <div class="mb-3">
              <label for="modalPublisher" class="form-label">nama penulis</label>
              <input type="text" class="form-control" id="modalPublisher" readonly>
            </div>
            <div class="mb-3">
              <label for="modalISBN" class="form-label">nama penerbut</label>
              <input type="text" class="form-control" id="modalISBN" readonly>
            </div>
            <div class="mb-3">
              <label for="modalPages" class="form-label">Jumlah Halaman</label>
              <input type="text" class="form-control" id="modalPages" readonly>
            </div>

            <div id="pinjamEditFields">
              <div class="mb-3">
                <label for="borrowDate" class="form-label">Tanggal Pinjam</label>
                <input type="date" class="form-control" id="borrowDate" name="borrow_date" required>
              </div>
              <div class="mb-3">
                <label for="returnDate" class="form-label">Tanggal Pengembalian</label>
                <input type="date" class="form-control" id="returnDate" name="return_date" required>
              </div>
            </div>

            <div id="hapusConfirmation" class="alert alert-danger" role="alert" style="display: none;">
              Apakah Anda yakin ingin menghapus buku "<span id="deleteBookTitle"></span>"?
            </div>

            <button type="submit" class="btn btn-primary" id="modalSubmitButton"></button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const bookModal = document.getElementById('bookModal');
    bookModal.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget;
      const action = button.getAttribute('data-action');
      const bookId = button.getAttribute('data-book-id');
      const title = button.getAttribute('data-title');
      const author = button.getAttribute('data-author');
      const publisher = button.getAttribute('data-publisher');
      const isbn = button.getAttribute('data-isbn');
      const pages = button.getAttribute('data-pages');

      const modalTitle = bookModal.querySelector('.modal-title');
      const modalBookIdInput = bookModal.querySelector('#modalBookId');
      const modalActionInput = bookModal.querySelector('#modalAction');
      const modalBookTitleInput = bookModal.querySelector('#modalTitle');
      const modalAuthorInput = bookModal.querySelector('#modalAuthor');
      const modalPublisherInput = bookModal.querySelector('#modalPublisher');
      const modalISBNInput = bookModal.querySelector('#modalISBN');
      const modalPagesInput = bookModal.querySelector('#modalPages');
      const pinjamEditFields = bookModal.querySelector('#pinjamEditFields');
      const hapusConfirmation = bookModal.querySelector('#hapusConfirmation');
      const deleteBookTitleSpan = bookModal.querySelector('#deleteBookTitle');
      const modalSubmitButton = bookModal.querySelector('#modalSubmitButton');
      const bookForm = bookModal.querySelector('#bookForm');
      const borrowDateInput = bookModal.querySelector('#borrowDate');
      const returnDateInput = bookModal.querySelector('#returnDate');

      // Reset form fields
      bookForm.reset();
      borrowDateInput.removeAttribute('required');
      returnDateInput.removeAttribute('required');

      modalBookIdInput.value = bookId;
      modalActionInput.value = action;
      modalBookTitleInput.value = title;
      modalAuthorInput.value = author;
      modalPublisherInput.value = publisher;
      modalISBNInput.value = isbn;
      modalPagesInput.value = pages;

      pinjamEditFields.style.display = 'none';
      hapusConfirmation.style.display = 'none';

      if (action === 'pinjam') {
        modalTitle.textContent = 'Pinjam Buku: ' + title;
        pinjamEditFields.style.display = 'block';
        modalSubmitButton.textContent = 'Pinjam Buku';
        modalSubmitButton.className = 'btn btn-success';
        bookForm.action = 'pinjam_buku.php'; // Example action for borrowing
        borrowDateInput.setAttribute('required', 'true');
        returnDateInput.setAttribute('required', 'true');
      } else if (action === 'edit') {
        modalTitle.textContent = 'Edit Peminjaman: ' + title;
        pinjamEditFields.style.display = 'block';
        modalSubmitButton.textContent = 'Simpan Perubahan';
        modalSubmitButton.className = 'btn btn-info';
        bookForm.action = 'edit_peminjaman.php'; // Example action for editing
        // You would typically fetch existing borrow/return dates for editing here
        borrowDateInput.setAttribute('required', 'true');
        returnDateInput.setAttribute('required', 'true');
      } else if (action === 'hapus') {
        modalTitle.textContent = 'Hapus Buku: ' + title;
        hapusConfirmation.style.display = 'block';
        deleteBookTitleSpan.textContent = title;
        modalSubmitButton.textContent = 'Konfirmasi Hapus';
        modalSubmitButton.className = 'btn btn-danger';
        bookForm.action = 'hapus_buku.php'; // Example action for deleting
      }
    });

    // Function for searching/filtering cards
    function filterCards() {
      const searchInput = document.getElementById('searchInput');
      const filter = searchInput.value.toLowerCase();
      const bookCards = document.querySelectorAll('.book-card');

      bookCards.forEach(card => {
        const title = card.querySelector('.card-title').textContent.toLowerCase();
        const author = card.querySelector('.card-subtitle').textContent.toLowerCase();
        const description = card.querySelector('.card-text').textContent.toLowerCase();

        if (title.includes(filter) || author.includes(filter) || description.includes(filter)) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    }
  </script>
</body>
</html>