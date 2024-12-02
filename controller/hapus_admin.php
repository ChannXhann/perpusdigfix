<?php
// Include koneksi
require_once '../config/koneksi.php';
$db = new Database();
$koneksi = $db->koneksi;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan bahwa data yang dihapus ada di $_POST['nik_anggota']
    if (isset($_POST['nip'])) {
        $nip = $_POST['nip'];
        
        // Query untuk menghapus data anggota
        $query = $koneksi->prepare("DELETE FROM admin WHERE nip = :nip");
        $query->bindParam(':nip', $nip);
        
        try {
            $query->execute();
            header("Location: ../../view/pages_super/data_admin.php");
            exit(); // Pastikan untuk menghentikan eksekusi lebih lanjut
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