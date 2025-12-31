<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Anggota - Akmal Library</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">

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
    padding: 10px 30px;
  }
  
  .btn-primary:hover {
    background: #3a0ca3;
    transform: translateY(-2px);
  }
  
  .table th {
    background-color: #f8f9fa;
    font-weight: 600;
  }
  
  .search-box {
    max-width: 300px;
  }
</style>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <div class="container mt-4">
    <!-- Form Section -->
    <div class="card mb-4">
      <div class="card-header">
        <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Tambah Anggota Baru</h4>
      </div>
      <div class="card-body">
        <?php
        $editMode = false;
        $editData = [
          'id_anggota' => '',
          'nama' => '',
          'alamat' => '',
          'tanggal_daftar' => ''
        ];

        if (isset($_GET['edit'])) {
          $id_edit = (int)$_GET['edit'];
          $sql_edit = "SELECT * FROM anggota WHERE id_anggota = $id_edit";
          $result_edit = $conn->query($sql_edit);

          if ($result_edit && $result_edit->num_rows > 0) {
            $editMode = true;
            $editData = $result_edit->fetch_assoc();
          }
        }

        if (isset($_POST['simpan'])) {
          $nama = $conn->real_escape_string($_POST['nama']);
          $alamat = $conn->real_escape_string($_POST['alamat']);
          $tanggal = $_POST['tanggal_daftar'];

          $query = "INSERT INTO anggota (nama, alamat, tanggal_daftar) VALUES ('$nama', '$alamat', '$tanggal')";
          if ($conn->query($query)) {
            echo '<div class="alert alert-success">Berhasil menambahkan anggota baru.</div>';
          }
        }

        if (isset($_POST['update'])) {
          $id = (int)$_POST['id_anggota'];
          $nama = $conn->real_escape_string($_POST['nama']);
          $alamat = $conn->real_escape_string($_POST['alamat']);
          $tanggal = $_POST['tanggal_daftar'];

          $query = "UPDATE anggota SET nama='$nama', alamat='$alamat', tanggal_daftar='$tanggal' WHERE id_anggota=$id";
          if ($conn->query($query)) {
            echo '<div class="alert alert-success">Data anggota berhasil diperbarui.</div>';
            echo '<meta http-equiv="refresh" content="2; url=tambah_anggota.php">';
          }
        }
        ?>

        <form method="POST">
          <input type="hidden" name="id_anggota" value="<?= $editData['id_anggota'] ?>">

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" name="nama" required value="<?= htmlspecialchars($editData['nama']) ?>">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Tanggal Daftar</label>
              <input type="date" class="form-control" name="tanggal_daftar" required value="<?= $editData['tanggal_daftar'] ?>">
            </div>

            <div class="col-12 mb-3">
              <label class="form-label">Alamat</label>
              <textarea class="form-control" name="alamat" rows="3" required><?= htmlspecialchars($editData['alamat']) ?></textarea>
            </div>

            <div class="col-12">
              <button type="submit" name="<?= $editMode ? 'update' : 'simpan' ?>" class="btn btn-primary">
                <i class="fas fa-save me-2"></i><?= $editMode ? 'Update Anggota' : 'Simpan Anggota' ?>
              </button>
              <?php if ($editMode): ?>
                <a href="tambah_anggota.php" class="btn btn-secondary">Batal</a>
              <?php endif; ?>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Table Section -->
    <div class="card">
      <div class="card-header">
        <h4 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Anggota</h4>
      </div>
      <div class="card-body">
        <!-- Search -->
        <div class="row mb-3">
          <div class="col-md-6">
            <form method="GET" class="d-flex">
              <input type="text" class="form-control me-2 search-box" name="cari" placeholder="Cari nama anggota..." value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>">
              <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
            </form>
          </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Tanggal Daftar</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $where = "";
              if (isset($_GET['cari']) && $_GET['cari'] != '') {
                $keyword = $conn->real_escape_string($_GET['cari']);
                $where = "WHERE nama LIKE '%$keyword%'";
              }

              $sql = "SELECT * FROM anggota $where ORDER BY tanggal_daftar DESC";
              $result = $conn->query($sql);

              if ($result && $result->num_rows > 0) {
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                  echo "
                    <tr>
                      <td>{$no}</td>
                      <td>".htmlspecialchars($row['nama'])."</td>
                      <td>".htmlspecialchars($row['alamat'])."</td>
                      <td>{$row['tanggal_daftar']}</td>
                      <td>
                        <a href='?edit={$row['id_anggota']}' class='btn btn-sm btn-warning me-1'><i class='fas fa-edit'></i></a>
                        <button class='btn btn-sm btn-danger' onclick='hapusAnggota({$row['id_anggota']}, this)'><i class='fas fa-trash'></i></button>
                      </td>
                    </tr>
                  ";
                  $no++;
                }
              } else {
                echo "<tr><td colspan='5' class='text-center py-4'>Data anggota tidak ditemukan.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
  function hapusAnggota(id, el) {
    if (confirm("Yakin ingin menghapus anggota ini?")) {
      fetch('hapus_anggota.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          el.closest("tr").remove();
          alert("Anggota berhasil dihapus!");
        } else {
          alert("Gagal menghapus anggota.");
        }
      });
    }
  }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>