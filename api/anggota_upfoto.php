<?php
include '../config/koneksi.php';

$response = array();
$database = new Database();
$db = $database->koneksi;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Data yang dikirimkan melalui POST
    $id = $_POST["id_user"];
    $foto_anggota = file_get_contents($_FILES["foto_anggota"]["tmp_name"]); // Mengambil file sebagai binary

    // Mulai transaksi
    try {
        $db->beginTransaction();

        // Update tabel anggota
        $sql_anggota = "UPDATE anggota SET foto_anggota = :foto_anggota WHERE id_user = :id_user";
        $stmt_anggota = $db->prepare($sql_anggota);
        $stmt_anggota->bindParam(':foto_anggota', $foto_anggota, PDO::PARAM_LOB); // Menggunakan PDO::PARAM_LOB untuk BLOB
        $stmt_anggota->bindParam(':id_user', $id);
        $stmt_anggota->execute();

        // Commit transaksi jika update berhasil
        $db->commit();

        $response["kode"] = 1;
        $response["pesan"] = "Foto anggota berhasil diperbarui";
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        $db->rollBack();
        $response["kode"] = 0;
        $response["pesan"] = "Gagal memperbarui foto anggota: " . $e->getMessage();
    }
}

echo json_encode($response);

// Tutup koneksi
$db = null;
?>