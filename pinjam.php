<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Peminjaman Buku - Akmal Library</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      background-color: #f5f7ff;
    }
    
    .card {
      border-radius: 10px;
      border: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    .card-header {
      background: linear-gradient(135deg, #4361ee, #3a0ca3);
      color: white;
      border-radius: 10px 10px 0 0 !important;
      padding: 1.2rem;
    }
    
    .form-control:focus, .form-select:focus {
      border-color: #4361ee;
      box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }
    
    .btn-primary {
      background: #4361ee;
      border: none;
      padding: 12px 30px;
      font-weight: 500;
    }
    
    .btn-primary:hover {
      background: #3a0ca3;
      transform: translateY(-2px);
    }
    
    .table th {
      background-color: #f8f9fa;
      font-weight: 600;
      color: #4361ee;
    }
    
    .stok-badge {
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 500;
    }
    
    .search-box {
      max-width: 300px;
    }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <div class="container mt-4">
    <!-- Form Peminjaman -->
    <div class="card mb-4">
      <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-journal-arrow-up me-2"></i>Formulir Peminjaman Buku</h4>
      </div>
      
      <div class="card-body">
        <form method="POST">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Nama Anggota</label>
              <select class="form-select" name="id_anggota" required>
                <option value="">Pilih Anggota</option>
                <?php
                $q = $conn->query("SELECT * FROM anggota ORDER BY nama");
                while ($a = $q->fetch_assoc()) {
                  echo "<option value='{$a['id_anggota']}'>{$a['nama']}</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Tanggal Pinjam</label>
              <input type="date" class="form-control" name="tanggal_pinjam" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Buku</label>
              <select class="form-select" name="id_buku" required id="bukuSelect" onchange="updateStok()">
                <option value="">Pilih Buku</option>
                <?php
                $b = $conn->query("SELECT * FROM buku WHERE stok > 0 ORDER BY judul");
                while ($bk = $b->fetch_assoc()) {
                  echo "<option value='{$bk['id_buku']}' data-stok='{$bk['stok']}'>{$bk['judul']} (Stok: {$bk['stok']})</option>";
                }
                ?>
              </select>
              <div id="stokInfo" class="form-text"></div>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Jumlah</label>
              <input type="number" class="form-control" name="jumlah" min="1" required id="jumlahInput" oninput="validateJumlah()">
              <div id="jumlahError" class="text-danger small mt-1 d-none"></div>
            </div>
          </div>

          <div class="d-grid mt-4">
            <button type="submit" name="pinjam" class="btn btn-primary btn-lg" id="submitBtn">
              <i class="bi bi-arrow-right-circle me-2"></i> Pinjam Sekarang
            </button>
          </div>
        </form>

        <?php
        if (isset($_POST['pinjam'])) {
          $conn->begin_transaction();
          try {
            $id_anggota = $_POST['id_anggota'];
            $tanggal_pinjam = $_POST['tanggal_pinjam'];
            $id_buku = $_POST['id_buku'];
            $jumlah = $_POST['jumlah'];
            
            // Cek stok
            $cek_stok = $conn->query("SELECT stok FROM buku WHERE id_buku = $id_buku")->fetch_assoc();
            if ($cek_stok['stok'] < $jumlah) {
              echo "<script>
                Swal.fire({
                  icon: 'error',
                  title: 'Stok Tidak Cukup',
                  text: 'Stok buku hanya tersedia {$cek_stok['stok']} eksemplar.'
                });
              </script>";
            } else {
              $conn->query("INSERT INTO peminjaman (id_anggota, tanggal_pinjam) 
                            VALUES ('$id_anggota', '$tanggal_pinjam')");

              $id_peminjaman = $conn->insert_id;

              $conn->query("INSERT INTO detail_peminjaman (id_peminjaman, id_buku, jumlah)
                            VALUES ('$id_peminjaman', '$id_buku', '$jumlah')");

              $conn->query("UPDATE buku SET stok = stok - $jumlah WHERE id_buku = $id_buku");

              $conn->commit();

              echo "<script>
                Swal.fire({
                  icon: 'success',
                  title: 'Berhasil!',
                  text: 'Peminjaman berhasil dicatat.',
                  footer: 'ID Peminjaman: $id_peminjaman'
                });
              </script>";
            }

          } catch (Exception $e) {
            $conn->rollback();
            echo "<script>
              Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan: {$e->getMessage()}'
              });
            </script>";
          }
        }
        ?>
      </div>
    </div>

    <!-- Daftar Buku Tersedia -->
    <div class="card">
      <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
          <h4 class="mb-0"><i class="bi bi-book me-2"></i>Daftar Buku Tersedia</h4>
          <div class="search-box">
            <input type="text" class="form-control" id="searchBuku" placeholder="Cari buku...">
          </div>
        </div>
      </div>
      
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Judul Buku</th>
                <th>Pengarang</th>
                <th>Tahun Terbit</th>
                <th class="text-center">Stok</th>
              </tr>
            </thead>
            <tbody id="bukuTable">
              <?php
              $buku = $conn->query("SELECT * FROM buku WHERE stok > 0 ORDER BY judul");
              while ($bk = $buku->fetch_assoc()) {
                $stok_class = $bk['stok'] > 5 ? 'bg-success' : ($bk['stok'] > 2 ? 'bg-warning' : 'bg-danger');
                echo "<tr>
                        <td><strong>{$bk['judul']}</strong></td>
                        <td>{$bk['pengarang']}</td>
                        <td>{$bk['tahun_terbit']}</td>
                        <td class='text-center'>
                          <span class='stok-badge {$stok_class} text-white'>{$bk['stok']} tersedia</span>
                        </td>
                      </tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Fungsi validasi stok
    function updateStok() {
      const select = document.getElementById('bukuSelect');
      const selectedOption = select.options[select.selectedIndex];
      const stok = selectedOption.getAttribute('data-stok') || 0;
      document.getElementById('stokInfo').textContent = `Stok tersedia: ${stok} eksemplar`;
    }
    
    function validateJumlah() {
      const select = document.getElementById('bukuSelect');
      const jumlah = document.getElementById('jumlahInput').value;
      const errorDiv = document.getElementById('jumlahError');
      const submitBtn = document.getElementById('submitBtn');
      
      if (select.value === '') {
        errorDiv.textContent = 'Silakan pilih buku terlebih dahulu';
        errorDiv.classList.remove('d-none');
        submitBtn.disabled = true;
        return;
      }
      
      const stok = parseInt(select.options[select.selectedIndex].getAttribute('data-stok'));
      
      if (jumlah > stok) {
        errorDiv.textContent = `Jumlah melebihi stok tersedia (${stok})`;
        errorDiv.classList.remove('d-none');
        submitBtn.disabled = true;
      } else if (jumlah < 1) {
        errorDiv.textContent = 'Jumlah minimal 1';
        errorDiv.classList.remove('d-none');
        submitBtn.disabled = true;
      } else {
        errorDiv.classList.add('d-none');
        submitBtn.disabled = false;
      }
    }
    
    // Search buku
    document.getElementById('searchBuku').addEventListener('input', function() {
      let filter = this.value.toLowerCase();
      let rows = document.querySelectorAll('#bukuTable tr');
      rows.forEach(function(row) {
        let title = row.querySelectorAll('td')[0].textContent.toLowerCase();
        row.style.display = title.includes(filter) ? '' : 'none';
      });
    });
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      updateStok();
      validateJumlah();
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>