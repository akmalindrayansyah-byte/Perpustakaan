<?php
include 'koneksi.php';

// Cek apakah ID peminjaman diterima dari URL
if (isset($_GET['id'])) {
    $id_peminjaman = $_GET['id'];

    // Mulai transaksi untuk memastikan semua operasi dihapus dengan aman
    $conn->begin_transaction();
    
    try {
        // Hapus data dari tabel detail_peminjaman terkait dengan id_peminjaman
        $conn->query("DELETE FROM detail_peminjaman WHERE id_peminjaman = $id_peminjaman");

        // Hapus data dari tabel peminjaman
        $conn->query("DELETE FROM peminjaman WHERE id_peminjaman = $id_peminjaman");

        // Reset nilai auto increment pada tabel peminjaman setelah penghapusan
        $conn->query("ALTER TABLE peminjaman AUTO_INCREMENT = 1");

        // Commit transaksi jika tidak ada kesalahan
        $conn->commit();
        
        // Redirect ke halaman laporan setelah berhasil
        header('Location: laporan.php');
        exit();
    } catch (Exception $e) {
        // Jika terjadi kesalahan, rollback transaksi
        $conn->rollback();
        echo "<div class='alert alert-danger mt-3'>Terjadi kesalahan: " . $e->getMessage() . "</div>";
    }
}
?>
