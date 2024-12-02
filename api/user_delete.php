<?php
include '../config/koneksi.php';

$response = array();
$database = new Database();
$db = $database->koneksi;

if ($db) {
    try {
        $id_user = isset($_GET['id_user']) ? $_GET['id_user'] : null;

        if ($id_user) {
            // Mulai transaksi untuk memastikan data terhapus dengan benar
            $db->beginTransaction();

            // Query untuk menghapus data user berdasarkan `id_user`
            $sql_user = "DELETE FROM user WHERE id_user = :id_user";
            $stmt_user = $db->prepare($sql_user);
            $stmt_user->bindParam(':id_user', $id_user);

            // Eksekusi query penghapusan user
            if ($stmt_user->execute()) {
                // Commit transaksi jika penghapusan berhasil
                $db->commit();
                $response["kode"] = 1;
                $response["pesan"] = "Data user berhasil dihapus";
                $response["data"] = [];
            } else {
                // Rollback jika penghapusan user gagal
                $db->rollBack();
                $response["kode"] = 0;
                $response["pesan"] = "Gagal menghapus user";
                $response["data"] = [];
            }
        } else {
            $response["kode"] = 0;
            $response["pesan"] = "ID user tidak diberikan";
            $response["data"] = [];
        }
    } catch (PDOException $e) {
        // Jika terjadi kesalahan
        $db->rollBack();
        $response["kode"] = 0;
        $response["pesan"] = "Kesalahan: " . $e->getMessage();
        $response["data"] = [];
    }
} else {
    $response["kode"] = 0;
    $response["pesan"] = "Koneksi database gagal";
    $response["data"] = [];
}

echo json_encode($response);

// Tutup koneksi
$db = null;
?>