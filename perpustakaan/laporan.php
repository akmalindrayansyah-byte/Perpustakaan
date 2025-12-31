<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Peminjaman - Akmal Library</title>
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
    
    .table th {
      background-color: #f8f9fa;
      font-weight: 600;
      color: #4361ee;
      border-top: none;
    }
    
    .badge-status {
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 500;
    }
    
    .badge-success {
      background-color: #d4edda;
      color: #155724;
    }
    
    .badge-warning {
      background-color: #fff3cd;
      color: #856404;
    }
    
    .badge-danger {
      background-color: #f8d7da;
      color: #721c24;
    }
    
    .btn-outline-danger:hover {
      transform: translateY(-2px);
    }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <div class="container py-4">
    <div class="card">
      <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-journal-text me-2"></i>Laporan Peminjaman</h4>
        <p class="mb-0 opacity-75">Rekap data peminjaman dan pengembalian buku</p>
      </div>
      
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Nama Anggota</th>
                <th>Judul Buku</th>
                <th class="text-center">Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Batas Akhir</th>
                <th>Tanggal Kembali</th>
                <th class="text-center">Denda</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>

            <tbody>
              <?php
              $sql = "SELECT p.id_peminjaman, a.nama, b.judul, d.jumlah, 
                             p.tanggal_pinjam, p.tanggal_kembali, 
                             DATE_ADD(p.tanggal_pinjam, INTERVAL 7 DAY) AS batas_akhir
                      FROM peminjaman p
                      JOIN anggota a ON p.id_anggota = a.id_anggota
                      JOIN detail_peminjaman d ON p.id_peminjaman = d.id_peminjaman
                      JOIN buku b ON d.id_buku = b.id_buku
                      ORDER BY p.id_peminjaman DESC";

              $res = $conn->query($sql);

              if ($res && $res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                  
                  $batas = new DateTime($row['batas_akhir']);
                  $denda = 0;
                  $status = '<span class="badge-status badge-success">Dikembalikan</span>';

                  if ($row['tanggal_kembali']) {
                    $kembali = new DateTime($row['tanggal_kembali']);
                    if ($kembali > $batas) {
                      $selisih = $kembali->diff($batas)->days;
                      $denda = $selisih * 1000;
                    }
                  } else {
                    $hari_ini = new DateTime();
                    if ($hari_ini > $batas) {
                      $selisih = $hari_ini->diff($batas)->days;
                      $denda = $selisih * 1000;
                      $status = '<span class="badge-status badge-danger">Terlambat</span>';
                    } else {
                      $status = '<span class="badge-status badge-warning">Dipinjam</span>';
                    }
                  }

                  echo "
                  <tr>
                    <td class='fw-bold'>{$row['id_peminjaman']}</td>
                    <td>{$row['nama']}</td>
                    <td>{$row['judul']}</td>
                    <td class='text-center'><span class='badge bg-primary'>{$row['jumlah']}</span></td>
                    <td>{$row['tanggal_pinjam']}</td>
                    <td class='fw-semibold'>{$row['batas_akhir']}</td>
                    <td>" . ($row['tanggal_kembali'] ? $row['tanggal_kembali'] : $status) . "</td>
                    <td class='text-center fw-bold'>" . ($denda > 0 ? "Rp " . number_format($denda, 0, ',', '.') : "-") . "</td>
                    <td class='text-center'>
                      <a href='hapus_laporan.php?id={$row['id_peminjaman']}'
                         class='btn btn-sm btn-outline-danger'
                         onclick=\"return confirm('Yakin ingin menghapus laporan ini?');\">
                        <i class='bi bi-trash'></i>
                      </a>
                    </td>
                  </tr>";
                }
              } else {
                echo "<tr><td colspan='9' class='text-center py-4'>
                        <div class='text-muted mb-2'><i class='bi bi-inbox fs-1'></i></div>
                        <h5 class='text-muted'>Tidak ada data peminjaman</h5>
                      </td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>