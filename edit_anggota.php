<?php
include 'koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = intval($_POST['id']);
  $field = $_POST['field'];
  $value = $conn->real_escape_string($_POST['value']);

  $allowed_fields = ['nama', 'alamat', 'tanggal_daftar'];
  if (in_array($field, $allowed_fields)) {
    $conn->query("UPDATE anggota SET $field = '$value' WHERE id_anggota = $id");
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false]);
  }
}
?>
