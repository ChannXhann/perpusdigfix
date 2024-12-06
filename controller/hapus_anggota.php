<?php
// Include koneksi
require_once '../config/koneksi.php';
$db = new Database();
$koneksi = $db->koneksi;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan bahwa data yang dihapus ada di $_POST['nik_anggota']
    if (isset($_POST['nik_anggota'])) {
        $nik_anggota = $_POST['nik_anggota'];

        // Query untuk menghapus data anggota
        $query = $koneksi->prepare("DELETE FROM anggota WHERE nik_anggota = :nik_anggota");
        $query->bindParam(':nik_anggota', $nik_anggota);

        try {
            $query->execute();
            echo "<script>alert('Data Anggota Berhasil Di Hapus!'); window.location.href='../../view/pages_super/data_anggota.php';</script>";
            exit;
            // Pastikan untuk menghentikan eksekusi lebih lanjut
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "NIK anggota tidak ditemukan.";
    }
} else {
    echo "Metode tidak diizinkan.";
}
?>