<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Tambah Buku - Akmal Library</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root {
      --primary: #4361ee;
      --secondary: #3a0ca3;
      --accent: #4cc9f0;
      --light: #f8f9fa;
      --dark: #212529;
    }
    
    body {
      background-color: #f5f7ff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .navbar {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
    }
    
    .card {
      border-radius: 10px;
      border: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease;
    }
    
    .card:hover {
      transform: translateY(-2px);
    }
    
    .card-header {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      border-radius: 10px 10px 0 0 !important;
      padding: 1.2rem;
    }
    
    .stat-card {
      border-left: 4px solid var(--primary);
      height: 100%;
    }
    
    .stat-icon {
      font-size: 2.5rem;
      color: var(--primary);
      opacity: 0.8;
    }
    
    .btn-primary {
      background: var(--primary);
      border: none;
      padding: 10px 25px;
      font-weight: 500;
    }
    
    .btn-primary:hover {
      background: var(--secondary);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }
    
    .btn-outline-primary {
      color: var(--primary);
      border-color: var(--primary);
    }
    
    .btn-outline-primary:hover {
      background: var(--primary);
      color: white;
    }
    
    .form-control:focus, .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }
    
    .table th {
      background-color: #f8f9fa;
      font-weight: 600;
      color: var(--primary);
    }
    
    .alert {
      border-radius: 8px;
      border: none;
    }
    
    .search-box {
      max-width: 300px;
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>

  <div class="container mt-4">
    <!-- Header -->
    <div class="row mb-4">
      <div class="col">
        <h2><i class="bi bi-book me-2"></i>Tambah Buku</h2>
        <p class="text-muted">Kelola koleksi buku perpustakaan</p>
      </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
      <?php
      $anggota = $conn->query("SELECT COUNT(*) AS total FROM anggota")->fetch_assoc();
      $bukuCount = $conn->query("SELECT COUNT(*) AS total FROM buku")->fetch_assoc();
      $pinjamCount = $conn->query("SELECT COUNT(*) AS total FROM peminjaman")->fetch_assoc();
      ?>
      
      <div class="col-md-4 mb-3">
        <div class="card stat-card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon me-3">
                <i class="bi bi-people-fill"></i>
              </div>
              <div>
                <h3 class="mb-0"><?php echo (int)$anggota['total']; ?></h3>
                <p class="text-muted mb-0">Total Anggota</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 mb-3">
        <div class="card stat-card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon me-3">
                <i class="bi bi-journal-bookmark-fill"></i>
              </div>
              <div>
                <h3 class="mb-0"><?php echo (int)$bukuCount['total']; ?></h3>
                <p class="text-muted mb-0">Total Buku</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 mb-3">
        <div class="card stat-card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon me-3">
                <i class="bi bi-arrow-repeat"></i>
              </div>
              <div>
                <h3 class="mb-0"><?php echo (int)$pinjamCount['total']; ?></h3>
                <p class="text-muted mb-0">Total Peminjaman</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- Form -->
      <div class="col-lg-5">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i><?php echo isset($_GET['edit']) ? 'Edit Buku' : 'Tambah Buku Baru'; ?></h5>
          </div>
          
          <div class="card-body">
            <?php
            /* Load edit data */
            $edit_mode = false;
            $edit_data = array();
            if (isset($_GET['edit'])) {
              $edit_mode = true;
              $id = intval($_GET['edit']);
              $rs = $conn->query("SELECT * FROM buku WHERE id_buku = '$id'");
              if ($rs && $rs->num_rows > 0) {
                $edit_data = $rs->fetch_assoc();
              } else {
                $edit_mode = false;
              }
            }

            /* Handle POST */
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
              if (isset($_POST['simpan']) || isset($_POST['update'])) {
                $judul = $conn->real_escape_string($_POST['judul']);
                $pengarang = $conn->real_escape_string($_POST['pengarang']);
                $tahun = (int)$_POST['tahun_terbit'];
                $stok = (int)$_POST['stok'];

                if (isset($_POST['simpan'])) {
                  // insert tanpa cover
                  $sql = "INSERT INTO buku (judul, pengarang, tahun_terbit, stok) VALUES ('$judul','$pengarang','$tahun','$stok')";
                  if ($conn->query($sql)) {
                    echo '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Buku berhasil ditambahkan.</div>';
                  } else {
                    echo '<div class="alert alert-danger">Gagal menambahkan: ' . htmlspecialchars($conn->error) . '</div>';
                  }
                } else {
                  // update tanpa cover
                  $id_upd = (int)$_POST['id_buku'];
                  $sql = "UPDATE buku SET judul='$judul', pengarang='$pengarang', tahun_terbit='$tahun', stok='$stok' WHERE id_buku='$id_upd'";
                  if ($conn->query($sql)) {
                    echo '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Data buku diperbarui.</div>';
                  } else {
                    echo '<div class="alert alert-danger">Gagal update: ' . htmlspecialchars($conn->error) . '</div>';
                  }
                }
              }
            }
            ?>

            <form method="POST">
              <?php if ($edit_mode) { ?>
                <input type="hidden" name="id_buku" value="<?php echo (int)$edit_data['id_buku']; ?>">
              <?php } ?>

              <div class="mb-3">
                <label class="form-label">Judul Buku</label>
                <input type="text" name="judul" class="form-control" required value="<?php echo $edit_mode ? htmlspecialchars($edit_data['judul']) : ''; ?>">
              </div>

              <div class="mb-3">
                <label class="form-label">Pengarang</label>
                <input type="text" name="pengarang" class="form-control" required value="<?php echo $edit_mode ? htmlspecialchars($edit_data['pengarang']) : ''; ?>">
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Tahun Terbit</label>
                  <input type="number" name="tahun_terbit" class="form-control" required value="<?php echo $edit_mode ? htmlspecialchars($edit_data['tahun_terbit']) : ''; ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Stok</label>
                  <input type="number" name="stok" class="form-control" required value="<?php echo $edit_mode ? htmlspecialchars($edit_data['stok']) : ''; ?>">
                </div>
              </div>

              <div class="d-grid gap-2">
                <?php if ($edit_mode) { ?>
                  <button type="submit" name="update" class="btn btn-primary">
                    <i class="bi bi-pencil-square me-2"></i>Perbarui Buku
                  </button>
                  <a href="tambah_buku.php" class="btn btn-outline-primary">Batal</a>
                <?php } else { ?>
                  <button type="submit" name="simpan" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Simpan Buku
                  </button>
                <?php } ?>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0"><i class="bi bi-list me-2"></i>Daftar Buku</h5>
              <div class="search-box">
                <form method="GET" class="d-flex">
                  <input type="text" class="form-control me-2" name="search" placeholder="Cari buku..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                  <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                </form>
              </div>
            </div>
          </div>
          
          <div class="card-body">
            <?php
            // handle delete
            if (isset($_GET['hapus'])) {
              $id_hapus = (int)$_GET['hapus'];
              $conn->query("DELETE FROM buku WHERE id_buku='$id_hapus'");
              echo '<div class="alert alert-success"><i class="bi bi-trash me-2"></i>Buku berhasil dihapus.</div>';
            }
            ?>

            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Tahun</th>
                    <th>Stok</th>
                    <th class="text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $searchQ = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
                  $sql = "SELECT * FROM buku WHERE judul LIKE '%$searchQ%' OR pengarang LIKE '%$searchQ%' ORDER BY id_buku DESC";
                  $res = $conn->query($sql);
                  
                  if ($res && $res->num_rows > 0) {
                    while ($r = $res->fetch_assoc()) {
                      $stok_class = $r['stok'] > 5 ? 'bg-success' : ($r['stok'] > 2 ? 'bg-warning' : 'bg-danger');
                      echo '<tr>';
                      echo '<td><strong>'.htmlspecialchars($r['judul']).'</strong></td>';
                      echo '<td>'.htmlspecialchars($r['pengarang']).'</td>';
                      echo '<td>'.$r['tahun_terbit'].'</td>';
                      echo '<td><span class="badge '.$stok_class.'">'.$r['stok'].'</span></td>';
                      echo '<td class="text-center">';
                      echo '<a href="?edit='.$r['id_buku'].'" class="btn btn-sm btn-warning me-1" title="Edit"><i class="bi bi-pencil"></i></a>';
                      echo '<a href="?hapus='.$r['id_buku'].'" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm(\'Yakin ingin menghapus buku ini?\')"><i class="bi bi-trash"></i></a>';
                      echo '</td>';
                      echo '</tr>';
                    }
                  } else {
                    echo '<tr><td colspan="5" class="text-center py-4">
                            <div class="text-muted mb-2"><i class="bi bi-inbox fs-1"></i></div>
                            <h5 class="text-muted">Tidak ada data buku</h5>
                          </td></tr>';
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Tips Card -->
        <div class="card mt-4">
          <div class="card-body">
            <div class="d-flex">
              <div class="flex-shrink-0">
                <i class="bi bi-lightbulb text-warning fs-4"></i>
              </div>
              <div class="flex-grow-1 ms-3">
                <h6 class="mb-1">Tips</h6>
                <p class="mb-0 text-muted">Gunakan tombol edit untuk mengubah data buku.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="mt-5 py-3 text-center text-muted border-top">
      <p class="mb-0">&copy; <?php echo date('Y'); ?> Akmal Library. All rights reserved.</p>
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>