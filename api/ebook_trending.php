<?php
include '../config/koneksi.php';

$response = array();
$database = new Database();
$db = $database->koneksi;

if ($db) {
    try {
        // Ambil semua ID peminjaman
        $sqlPeminjaman = "SELECT id_peminjaman FROM peminjaman";
        $stmtPeminjaman = $db->prepare($sqlPeminjaman);
        $stmtPeminjaman->execute();
        $listPeminjaman = $stmtPeminjaman->fetchAll(PDO::FETCH_ASSOC);

        $response["kode"] = 1;
        $response["pesan"] = "Data tersedia";
        $response["data"] = array();

        $ebookData = array(); // Untuk menyimpan hasil e-book
        $ebookIds = array();  // Untuk mencegah duplikasi e-book

        // Loop melalui setiap id_peminjaman untuk mengecek id_ebook
        foreach ($listPeminjaman as $peminjaman) {
            $id_peminjaman = $peminjaman['id_peminjaman'];

            // Cek apakah id_ebook ada untuk id_peminjaman ini
            $cekSql = "SELECT e.id_ebook, e.judul, e.penulis, e.penerbit, SUBSTRING(e.sinopsis, 1, 150) AS sinopsis, e.sampul
                       FROM peminjaman p
                       INNER JOIN e_book e ON p.id_ebook = e.id_ebook
                       WHERE p.id_peminjaman = :id_peminjaman";
            $cekStmt = $db->prepare($cekSql);
            $cekStmt->bindParam(':id_peminjaman', $id_peminjaman, PDO::PARAM_INT);
            $cekStmt->execute();

            // Tambahkan e-book hasil peminjaman ke array data
            while ($row = $cekStmt->fetch(PDO::FETCH_ASSOC)) {
                if (!in_array($row['id_ebook'], $ebookIds)) { // Cek duplikasi
                    $ebookIds[] = $row['id_ebook'];
                    $ebookData[] = array(
                        "id_ebook" => $row["id_ebook"],
                        "judul" => $row["judul"],
                        "penulis" => $row["penulis"],
                        "penerbit" => $row["penerbit"],
                        "sinopsis" => $row["sinopsis"],
                        "sampul" => base64_encode($row["sampul"]), // Konversi sampul ke base64
                    );
                }
            }
        }

        // Jika kurang dari 5, tambahkan e-book terbaru
        $countNeeded = 5 - count($ebookData);
        if ($countNeeded > 0) {
            $sqlEbookTerbaru = "
                SELECT id_ebook, judul, penulis, penerbit, SUBSTRING(sinopsis, 1, 150) AS sinopsis, sampul
                FROM e_book
                WHERE id_ebook NOT IN (" . implode(',', $ebookIds ?: [0]) . ")
                ORDER BY id_ebook DESC
                LIMIT :limit";
            $stmtEbookTerbaru = $db->prepare($sqlEbookTerbaru);
            $stmtEbookTerbaru->bindValue(':limit', $countNeeded, PDO::PARAM_INT);
            $stmtEbookTerbaru->execute();

            while ($row = $stmtEbookTerbaru->fetch(PDO::FETCH_ASSOC)) {
                $ebookData[] = array(
                    "id_ebook" => $row["id_ebook"],
                    "judul" => $row["judul"],
                    "penulis" => $row["penulis"],
                    "penerbit" => $row["penerbit"],
                    "sinopsis" => $row["sinopsis"],
                    "sampul" => base64_encode($row["sampul"]), // Konversi sampul ke base64
                );
            }
        }

        // Jika ada data, tampilkan
        if (!empty($ebookData)) {
            $response["data"] = $ebookData;
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