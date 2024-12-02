<?php
include '../config/koneksi.php';

$db = new Database();
$koneksi = $db->koneksi;

// Periksa apakah nik_anggota diterima melalui POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nik_anggota'])) {
    $nik_anggota = $_POST['nik_anggota'];

    // Perbarui status_verifikasi menjadi "Ditolak"
    $stmt = $koneksi->prepare("UPDATE anggota SET status_verifikasi = 'Ditolak' WHERE nik_anggota = ?");
    $stmt->execute([$nik_anggota]);

    if ($stmt->rowCount() > 0) {
        // Jika berhasil
        echo "<script>alert('Status anggota berhasil diperbarui menjadi Ditolak.'); window.location.href='../../view/pages_super/data_anggota.php';</script>";
    } else {
        // Jika gagal (misalnya NIK tidak ditemukan)
        echo "<script>alert('Gagal memperbarui status anggota.'); window.history.back();</script>";
    }
} else {
    echo "Akses tidak valid.";
    exit;
}
?>
