<?php
// Include koneksi
require_once '../config/koneksi.php';
$db = new Database();
$koneksi = $db->koneksi;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan bahwa data yang dihapus ada di $_POST['nik_anggota']
    if (isset($_POST['id_ebook'])) {
        $id_ebook = $_POST['id_ebook'];
        
        // Query untuk menghapus data anggota
        $query = $koneksi->prepare("DELETE FROM e_book WHERE id_ebook = :id_ebook");
        $query->bindParam(':id_ebook', $id_ebook);
        
        try {
            $query->execute();
            header("Location: ../../view/pages_admin/Ebook.php");
            exit(); // Pastikan untuk menghentikan eksekusi lebih lanjut
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "ID Ebook tidak ditemukan.";
    }
} else {
    echo "Metode tidak diizinkan.";
}
?>
