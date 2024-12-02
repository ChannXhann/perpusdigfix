<?php
include '../config/koneksi.php';

$response = array();
$database = new Database();
$db = $database->koneksi;

if ($db) {
    try {
        // Pastikan `id` diberikan
        $id = isset($_GET['id']) ? $_GET['id'] : null;

        if ($id) {
            $sql = "SELECT * FROM e_book WHERE id_ebook = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $response["kode"] = 1;
                $response["pesan"] = "Detail buku tersedia";
                // Ambil data dan masukkan ke array
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Konversi BLOB sampul menjadi Base64
                if (!empty($data["sampul"])) {
                    $data["sampul"] = base64_encode($data["sampul"]);
                }

                // Konversi BLOB PDF menjadi Base64
                if (!empty($data["pdf"])) {
                    $data["pdf"] = base64_encode($data["pdf"]);
                }

                // Bungkus data menjadi array
                $response["data"] = [$data]; // Ubah menjadi array
            } else {
                $response["kode"] = 0;
                $response["pesan"] = "Buku tidak ditemukan";
                $response["data"] = [];
            }
        } else {
            $response["kode"] = 0;
            $response["pesan"] = "ID buku tidak diberikan";
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