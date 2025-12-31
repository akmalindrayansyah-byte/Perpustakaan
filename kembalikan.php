<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pengembalian Buku - Akmal Library</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

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
    
    .form-control:focus {
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
    
    .modal-header {
      background: linear-gradient(135deg, #4361ee, #3a0ca3);
      color: white;
    }
    
    .denda-badge {
      font-size: 1.1rem;
      padding: 8px 15px;
    }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h4 class="mb-0 text-center"><i class="bi bi-arrow-repeat me-2"></i>Pengembalian Buku</h4>
          </div>

          <div class="card-body">
            <form method="POST">
              <div class="mb-4">
                <label class="form-label fw-semibold">ID Peminjaman</label>
                <input type="number" class="form-control form-control-lg" name="id_peminjaman" 
                       placeholder="Masukkan ID peminjaman" required>
                <div class="form-text">Masukkan ID peminjaman yang tercatat di sistem</div>
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold">Tanggal Kembali</label>
                <input type="date" class="form-control form-control-lg" name="tanggal_kembali" required>
                <div class="form-text">Tanggal buku dikembalikan</div>
              </div>

              <div class="d-grid mt-4">
                <button type="submit" name="kembalikan" class="btn btn-primary btn-lg">
                  <i class="bi bi-box-arrow-in-down-left me-2"></i> Proses Pengembalian
                </button>
              </div>
            </form>

            <?php
            if (isset($_POST['kembalikan'])) {
              $id_peminjaman = $_POST['id_peminjaman'];
              $tanggal_kembali = $_POST['tanggal_kembali'];

              // Cek apakah peminjaman ada
              $cek_pinjaman = $conn->query("SELECT * FROM peminjaman WHERE id_peminjaman='$id_peminjaman'");
              if ($cek_pinjaman->num_rows == 0) {
                echo "<div class='alert alert-danger mt-3'>ID Peminjaman tidak ditemukan!</div>";
              } else {
                // Update stok buku
                $cek = $conn->query("SELECT * FROM detail_peminjaman WHERE id_peminjaman='$id_peminjaman'");
                while ($d = $cek->fetch_assoc()) {
                  $conn->query("UPDATE buku SET stok = stok + {$d['jumlah']} WHERE id_buku = {$d['id_buku']}");
                }

                // Ambil tanggal pinjam
                $getPinjam = $conn->query("SELECT tanggal_pinjam FROM peminjaman WHERE id_peminjaman='$id_peminjaman'");
                $dataPinjam = $getPinjam->fetch_assoc();

                $tanggal_pinjam = $dataPinjam['tanggal_pinjam'];
                $datetime1 = new DateTime($tanggal_pinjam);
                $datetime2 = new DateTime($tanggal_kembali);
                $selisih = $datetime1->diff($datetime2)->days;

                // Hitung denda
                $denda = 0;
                if ($selisih > 7) {
                  $terlambat = $selisih - 7;
                  $denda = $terlambat * 1000;
                }

                // Update tanggal kembali
                $conn->query("UPDATE peminjaman SET tanggal_kembali='$tanggal_kembali' WHERE id_peminjaman='$id_peminjaman'");

                // Tampilkan modal sukses
                echo "
                <div class='modal fade' id='successModal' tabindex='-1'>
                  <div class='modal-dialog modal-dialog-centered'>
                    <div class='modal-content'>
                      <div class='modal-header'>
                        <h5 class='modal-title'><i class='bi bi-check-circle-fill me-2'></i>Pengembalian Berhasil!</h5>
                        <button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal'></button>
                      </div>
                      <div class='modal-body'>
                        <div class='text-center mb-4'>
                          <i class='bi bi-check-circle text-success' style='font-size: 4rem;'></i>
                        </div>
                        <p class='text-center'>Pengembalian berhasil dicatat pada <strong>{$tanggal_kembali}</strong>.</p>";
                
                if ($denda > 0) {
                  echo "<div class='alert alert-warning text-center'>
                          <h5 class='mb-2'>Denda Terlambat</h5>
                          <div class='denda-badge badge bg-warning text-dark'>Rp " . number_format($denda, 0, ',', '.') . "</div>
                          <p class='mt-2 mb-0'>Buku terlambat dikembalikan " . ($selisih - 7) . " hari</p>
                        </div>";
                } else {
                  echo "<div class='alert alert-success text-center'>
                          <h5 class='mb-2'><i class='bi bi-award me-2'></i>Tepat Waktu</h5>
                          <p>Buku dikembalikan sesuai batas waktu.</p>
                        </div>";
                }
                
                echo "</div>
                      <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Tutup</button>
                        <a href='laporan.php' class='btn btn-primary'>Lihat Laporan</a>
                      </div>
                    </div>
                  </div>
                </div>
                <script>
                  window.onload = function() {
                    new bootstrap.Modal(document.getElementById('successModal')).show();
                  };
                </script>";
              }
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>