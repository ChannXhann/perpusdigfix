<?php
include '../config/koneksi.php';

$response = array();
$database = new Database();
$db = $database->koneksi;

if ($db) {
    try {
        // Pastikan `id_peminjaman` diberikan
        $id_peminjaman = isset($_GET['id_peminjaman']) ? $_GET['id_peminjaman'] : null;

        if ($id_peminjaman) {
            // Query untuk menghapus data peminjaman berdasarkan `id_peminjaman`
            $sql = "DELETE FROM peminjaman WHERE id_peminjaman = :id_peminjaman";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_peminjaman', $id_peminjaman);

            // Eksekusi query
            if ($stmt->execute()) {
                $response["kode"] = 1;
                $response["pesan"] = "Peminjaman berhasil dihapus";
                $response["data"] = [];
            } else {
                $response["kode"] = 0;
                $response["pesan"] = "Gagal menghapus peminjaman";
                $response["data"] = [];
            }
        } else {
            $response["kode"] = 0;
            $response["pesan"] = "ID peminjaman tidak diberikan";
            $response["data"] = [];
        }
    } catch (PDOException $e) {
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