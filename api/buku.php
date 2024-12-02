<?php
include '../config/koneksi.php';

$response = array();
$database = new Database();
$db = $database->koneksi;

if ($db) {
    try {
        $sql = "SELECT * FROM buku";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $response["kode"] = 1;
            $response["pesan"] = "Data tersedia";
            $response["data"] = array();

            // Mengambil data menggunakan PDO
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $F = array();
                $F["id_buku"] = $row["id_buku"];
                $F["judul_buku"] = $row["judul_buku"];
                $F["penulis_buku"] = $row["penulis_buku"];
                $F["kategori_buku"] = $row["kategori_buku"];
                $F["jumlah_buku"] = $row["jumlah_buku"];
                $F["sampul_buku"] = base64_encode($row["sampul_buku"]);  // Konversi BLOB ke base64

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