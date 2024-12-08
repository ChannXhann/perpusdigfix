<?php
include '../config/koneksi.php';

$response = array();
$database = new Database();
$db = $database->koneksi;

if ($db) {
    try {
        // Pastikan id_user dan id_ebook diberikan
        $id_user = isset($_GET['id_user']) ? $_GET['id_user'] : null;

        if ($id_user) {
            // Query dengan JOIN untuk mengambil data dari tabel peminjaman dan ebook
            $sql = "
                SELECT 
                peminjaman.*,
                buku.penulis_buku,
                buku.sampul_buku 
            FROM 
                peminjaman 
            JOIN 
                buku 
            ON 
                peminjaman.id_buku = buku.id_buku
            WHERE 
                peminjaman.id_user = :id_user 
            AND 
                peminjaman.id_buku IS NOT NULL
            ORDER BY 
                CASE 
                    WHEN peminjaman.status_peminjaman = 'Ditunda' THEN 1
                    ELSE 2
                END,
                peminjaman.tanggal_peminjaman DESC;
            ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_user', $id_user);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $response["kode"] = 1;
                $response["pesan"] = "Data tersedia";
                // Ambil data dan masukkan ke array
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Konversi kolom sampul_ebook ke base64
                foreach ($data as &$item) {
                    if ($item['sampul_buku']) {
                        $item['sampul_buku'] = base64_encode($item['sampul_buku']);
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