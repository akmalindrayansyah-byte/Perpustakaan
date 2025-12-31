<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Akmal Library</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.css" rel="stylesheet">
  
  <style>
    :root {
      --primary: #4361ee;
      --secondary: #3a0ca3;
      --success: #4cc9f0;
      --warning: #f8961e;
      --danger: #f94144;
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
    
    .stat-card {
      border-radius: 10px;
      border: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
    }
    
    .stat-icon {
      font-size: 2.5rem;
      opacity: 0.8;
    }
    
    .hero-section {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 100px 0 50px;
      margin-bottom: 50px;
    }
    
    .welcome-text {
      font-size: 2.8rem;
      font-weight: 700;
    }
    
    .card {
      border-radius: 10px;
      border: none;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .btn-primary {
      background: var(--primary);
      border: none;
      padding: 10px 25px;
      border-radius: 6px;
    }
    
    .btn-primary:hover {
      background: var(--secondary);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>
  
  <!-- Hero Section -->
  <div class="hero-section">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h1 class="welcome-text">Selamat Datang di Sistem Perpustakaan</h1>
          <p class="lead">Kelola koleksi buku, peminjaman, dan anggota dengan mudah dan efisien.</p>
        </div>
        <div class="col-md-4 text-center">
          <i class="fas fa-book-open fa-6x text-white opacity-75"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Stats Section -->
  <div class="container">
    <div class="row mb-5">
      <?php
      include 'koneksi.php';
      
      $anggota = $conn->query("SELECT COUNT(*) AS total FROM anggota")->fetch_assoc();
      $buku = $conn->query("SELECT COUNT(*) AS total FROM buku")->fetch_assoc();
      $peminjaman = $conn->query("SELECT COUNT(*) AS total FROM peminjaman")->fetch_assoc();
      ?>
      
      <div class="col-md-4 mb-4">
        <div class="card stat-card">
          <div class="card-body text-center">
            <div class="stat-icon text-primary mb-3">
              <i class="fas fa-users"></i>
            </div>
            <h2 class="card-title"><?= $anggota['total']; ?></h2>
            <p class="card-text text-muted">Total Anggota</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 mb-4">
        <div class="card stat-card">
          <div class="card-body text-center">
            <div class="stat-icon text-success mb-3">
              <i class="fas fa-book"></i>
            </div>
            <h2 class="card-title"><?= $buku['total']; ?></h2>
            <p class="card-text text-muted">Total Buku</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 mb-4">
        <div class="card stat-card">
          <div class="card-body text-center">
            <div class="stat-icon text-warning mb-3">
              <i class="fas fa-exchange-alt"></i>
            </div>
            <h2 class="card-title"><?= $peminjaman['total']; ?></h2>
            <p class="card-text text-muted">Total Peminjaman</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Akses Cepat</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3 mb-3">
                <a href="tambah_buku.php" class="btn btn-primary w-100">
                  <i class="fas fa-plus me-2"></i>Tambah Buku
                </a>
              </div>
              <div class="col-md-3 mb-3">
                <a href="pinjam.php" class="btn btn-success w-100">
                  <i class="fas fa-handshake me-2"></i>Peminjaman
                </a>
              </div>
              <div class="col-md-3 mb-3">
                <a href="kembalikan.php" class="btn btn-warning w-100 text-white">
                  <i class="fas fa-undo me-2"></i>Pengembalian
                </a>
              </div>
              <div class="col-md-3 mb-3">
                <a href="tambah_anggota.php" class="btn btn-info w-100 text-white">
                  <i class="fas fa-user-plus me-2"></i>Tambah Anggota
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-dark text-white mt-5 py-4">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <h5><i class="fas fa-book me-2"></i>Akmal Library</h5>
          <p class="mb-0">Sistem Manajemen Perpustakaan Digital</p>
        </div>
        <div class="col-md-6 text-end">
          <p class="mb-0">&copy; <?php echo date("Y"); ?> Akmal Library. All rights reserved.</p>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>