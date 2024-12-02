<?php
include '../config/koneksi.php';

$response = array();
$database = new Database();
$db = $database->koneksi;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Data yang dikirimkan melalui POST
    $id = $_POST["id_user"];
    $username = $_POST["username"];

    // Mulai transaksi
    try {
        $db->beginTransaction();

        // Update tabel user
        $sql_user = "UPDATE user SET username = :username WHERE id_user = :id_user";
        $stmt_user = $db->prepare($sql_user);
        $stmt_user->bindParam(':username', $username);
        $stmt_user->bindParam(':id_user', $id);
        $stmt_user->execute();

        // Commit transaksi jika update berhasil
        $db->commit();

        $response["kode"] = 1;
        $response["pesan"] = "Username berhasil diperbarui";
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