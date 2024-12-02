<?php
include '../config/koneksi.php';

$response = array();
$database = new Database();
$db = $database->koneksi;

if ($db) {
    try {
        $sql = "SELECT * FROM e_book";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $response["kode"] = 1;
            $response["pesan"] = "Data tersedia";
            $response["data"] = array();

            // Mengambil data menggunakan PDO
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $F = array();
                $F["id_ebook"] = $row["id_ebook"];
                $F["judul"] = $row["judul"];
                $F["penulis"] = $row["penulis"];
                $F["kategori"] = $row["kategori"];
                $F["sampul"] = base64_encode($row["sampul"]);  // Konversi BLOB ke base64

                array_push($response["data"], $F);
            }
        } else {
            $response["kode"] = 0;
            $response["pesan"] = "Data tidak tersedia";
        }
    } catch (PDOException $e) {
        $response["kode"] = 0;
        $response["pesan"] = "Kesalahan: " . $e->getMessage();
    }
} else {
    $response["kode"] = 0;
    $response["pesan"] = "Koneksi database gagal";
}

echo json_encode($response);

// Tutup koneksi
$db = null;