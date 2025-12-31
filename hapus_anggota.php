<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  $id = intval($_POST['id']);

  $conn->query("DELETE FROM detail_peminjaman WHERE id_peminjaman IN (SELECT id_peminjaman FROM peminjaman WHERE id_anggota = $id)");
  $conn->query("DELETE FROM peminjaman WHERE id_anggota = $id");

  $hapus = $conn->query("DELETE FROM anggota WHERE id_anggota = $id");

  echo json_encode(['success' => $hapus]);
} else {
  echo json_encode(['success' => false]);
}
?>
