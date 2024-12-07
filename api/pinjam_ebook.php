<?php
include '../config/koneksi.php';
date_default_timezone_set('Asia/Jakarta'); // Atur zona waktu

$response = array();
$database = new Database();
$db = $database->koneksi;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Ambil nilai input dari POST
    $judul = $_POST["judul"];
    $kategori = $_POST["kategori"];
    $id_user = $_POST["id_user"];
    $id_ebook = $_POST["id_ebook"];
    $tanggal_peminjaman = date("Y-m-d");
    $status_peminjaman = $_POST["status_peminjaman"];

    // Cek ID terakhir
    $sqlId = "SELECT id_peminjaman FROM peminjaman ORDER BY id_peminjaman DESC LIMIT 1";
    $stmtId = $db->prepare($sqlId);
    $stmtId->execute();
    $lastId = $stmtId->fetchColumn();

    // Jika ada ID terakhir, generate ID baru
    if ($lastId) {
        // Ambil angka terakhir
        $newIdNumber = (int)substr($lastId, 1) + 1;
        $id_peminjaman = "P" . $newIdNumber;

        // Pastikan ID baru tidak duplikat
        $checkIdSql = "SELECT COUNT(*) FROM peminjaman WHERE id_peminjaman = :id_peminjaman";
        $checkStmt = $db->prepare($checkIdSql);
        $checkStmt->bindParam(':id_peminjaman', $id_peminjaman);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();

        // Jika ID sudah ada, tambahkan satu lagi sampai tidak duplikat
        while ($count > 0) {
            $newIdNumber++;
            $id_peminjaman = "P" . $newIdNumber;
            $checkStmt->execute();
            $count = $checkStmt->fetchColumn();
        }
    } else {
        // Jika belum ada data, mulai dengan P1
        $id_peminjaman = "P1";
    }

    // SQL untuk insert data ke tabel peminjaman
    $sql = "INSERT INTO peminjaman (id_peminjaman, judul, kategori, status_peminjaman, tanggal_peminjaman, id_user, id_ebook)
            VALUES (:id_peminjaman, :judul, :kategori, :status_peminjaman, :tanggal_peminjaman, :id_user, :id_ebook)";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id_peminjaman', $id_peminjaman);
    $stmt->bindParam(':judul', $judul);
    $stmt->bindParam(':kategori', $kategori);
    $stmt->bindParam(':status_peminjaman', $status_peminjaman);
    $stmt->bindParam(':tanggal_peminjaman', $tanggal_peminjaman);
    $stmt->bindParam(':id_user', $id_user);
    $stmt->bindParam(':id_ebook', $id_ebook);

    if ($stmt->execute()) {
        $response["kode"] = 1;
        $response["pesan"] = "Ebook tersedia";
    } else {
        $response["kode"] = 0;
        $response["pesan"] = "Gagal melakukan peminjaman ebook";
    }
}

echo json_encode($response);

// Tutup koneksi
$db = null;
