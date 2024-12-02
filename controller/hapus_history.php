<?php
require_once '../config/koneksi.php';
$db = new Database();
$koneksi = $db->koneksi;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik_anggota = $_POST['id_peminjaman'];

    $query = $koneksi->prepare("DELETE FROM peminjaman WHERE id_peminjaman = :id_peminjaman");
    $query->bindParam(':id_peminjaman', $nik_anggota);

    try {
        $query->execute();
        header("Location: ../../view/pages_super/history_peminjaman.php"); 
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
