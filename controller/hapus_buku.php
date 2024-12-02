<?php
// Include koneksi
require_once '../config/koneksi.php';
$db = new Database();
$koneksi = $db->koneksi;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan bahwa data yang dihapus ada di $_POST['nik_anggota']
    if (isset($_POST['id_buku'])) {
        $id_buku = $_POST['id_buku'];
        
        // Query untuk menghapus data anggota
        $query = $koneksi->prepare("DELETE FROM buku WHERE id_buku = :id_buku");
        $query->bindParam(':id_buku', $id_buku);
        
        try {
            $query->execute();
            header("Location: ../../view/pages_super/lihat_buku.php");
            exit(); // Pastikan untuk menghentikan eksekusi lebih lanjut
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "id_ebook tidak ditemukan.";
    }
} else {
    echo "Metode tidak diizinkan.";
}
?>
