<?php
include '../config/koneksi.php';

$response = array();
$database = new Database();
$db = $database->koneksi;

if ($db) {
    try {
        // Pastikan `id_user` dan `id_ebook` diberikan
        $id_user = isset($_GET['id_user']) ? $_GET['id_user'] : null;

        if ($id_user) {
            // Query dengan JOIN untuk mengambil data dari tabel peminjaman dan ebook
            $sql = "
                SELECT 
                    peminjaman.*,
                    e_book.penulis,
                    e_book.sampul 
                FROM 
                    peminjaman 
                JOIN 
                    e_book 
                ON 
                    peminjaman.id_ebook = e_book.id_ebook
                WHERE 
                    peminjaman.id_user = :id_user 
                AND 
                    peminjaman.id_ebook IS NOT NULL
                ORDER BY 
                    peminjaman.tanggal_peminjaman DESC
            ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_user', $id_user);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $response["kode"] = 1;
                $response["pesan"] = "Data tersedia";
                // Ambil data dan masukkan ke array
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Konversi kolom `sampul_ebook` ke base64
                foreach ($data as &$item) {
                    if ($item['sampul']) {
                        $item['sampul'] = base64_encode($item['sampul']);
                    }
                }

                $response["data"] = $data; // Menyimpan semua data
            } else {
                $response["kode"] = 0;
                $response["pesan"] = "Peminjaman tidak ditemukan";
                $response["data"] = [];
            }
        } else {
            $response["kode"] = 0;
            $response["pesan"] = "ID user atau ID ebook tidak diberikan";
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
