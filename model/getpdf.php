<?php
include '../config/koneksi.php';
$db = new Database();
$koneksi = $db->koneksi;
if (isset($_GET['id'])) {
    $id_ebook = $_GET['id'];

    // Query untuk mengambil file PDF berdasarkan id_ebook
    $query = $koneksi->prepare("SELECT pdf FROM e_book WHERE id_ebook = :id_ebook");
    $query->bindParam(':id_ebook', $id_ebook, PDO::PARAM_INT);
    $query->execute();
    $data = $query->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        header('Content-Type: application/pdf');
        echo $data['file_pdf'];
    } else {
        echo "File tidak ditemukan.";
    }
} else {
    echo "ID tidak valid.";
}
