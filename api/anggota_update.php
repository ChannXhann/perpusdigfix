<?php
include '../config/koneksi.php';

$response = array();
$database = new Database();
$db = $database->koneksi;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Data yang dikirimkan melalui POST
    $id = $_POST["id_user"];
    $username = $_POST["username"];
    $nama_anggota = $_POST["nama_anggota"];
    $telp = $_POST["telp"];
    $alamat = $_POST["alamat"];

    // Mulai transaksi
    try {
        $db->beginTransaction();

        // Update tabel user
        $sql_user = "UPDATE user SET username = :username WHERE id_user = :id_user";
        $stmt_user = $db->prepare($sql_user);
        $stmt_user->bindParam(':username', $username);
        $stmt_user->bindParam(':id_user', $id);
        $stmt_user->execute();

        // Update tabel anggota
        $sql_anggota = "UPDATE anggota SET nama_anggota = :nama_anggota, telp = :telp, alamat = :alamat WHERE id_user = :id_user";
        $stmt_anggota = $db->prepare($sql_anggota);
        $stmt_anggota->bindParam(':nama_anggota', $nama_anggota);
        $stmt_anggota->bindParam(':telp', $telp);
        $stmt_anggota->bindParam(':alamat', $alamat);
        $stmt_anggota->bindParam(':id_user', $id);
        $stmt_anggota->execute();

        // Commit transaksi jika kedua update berhasil
        $db->commit();

        $response["kode"] = 1;
        $response["pesan"] = "Data berhasil diperbarui";
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        $db->rollBack();
        $response["kode"] = 0;
        $response["pesan"] = "Gagal memperbarui data: " . $e->getMessage();
    }
}

echo json_encode($response);

// Tutup koneksi
$db = null;
?>