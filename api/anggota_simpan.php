<?php
include '../config/koneksi.php';

$response = array();
$database = new Database();
$db = $database->koneksi;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nik_anggota = $_POST["nik_anggota"];
    $nama_anggota = $_POST["nama_anggota"];
    $telp = $_POST["telp"];
    $alamat = $_POST["alamat"];
    $status_verifikasi = $_POST["status_verifikasi"];
    $tgl_pendaftaran = date("Y-m-d");
    $id_user = $_POST["id_user"];

    // Cek apakah nik_anggota sudah terdaftar
    $check_sql = "SELECT COUNT(*) as total FROM anggota WHERE nik_anggota = :nik_anggota";
    $check_stmt = $db->prepare($check_sql);
    $check_stmt->bindParam(':nik_anggota', $nik_anggota);
    $check_stmt->execute();
    $result = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['total'] > 0) {
        // Jika nik_anggota sudah ada
        $response["kode"] = 0;
        $response["pesan"] = "NIK anggota sudah digunakan";
        echo json_encode($response);
        exit; // Hentikan proses jika NIK sudah terdaftar
    } else {
        // Konversi file KTP dan foto anggota menjadi BLOB
        $foto_ktp = file_get_contents($_FILES['foto_ktp']['tmp_name']);
        $foto_anggota = file_get_contents($_FILES['foto_anggota']['tmp_name']);

        $sql = "INSERT INTO anggota (nik_anggota, nama_anggota, telp, alamat, foto_ktp, foto_anggota, status_verifikasi, tgl_pendaftaran, id_user)
                VALUES (:nik_anggota, :nama_anggota, :telp, :alamat, :foto_ktp, :foto_anggota, :status_verifikasi, :tgl_pendaftaran, :id_user)";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':nik_anggota', $nik_anggota);
        $stmt->bindParam(':nama_anggota', $nama_anggota);
        $stmt->bindParam(':telp', $telp);
        $stmt->bindParam(':alamat', $alamat);
        $stmt->bindParam(':foto_ktp', $foto_ktp, PDO::PARAM_LOB); // Tipe BLOB
        $stmt->bindParam(':foto_anggota', $foto_anggota, PDO::PARAM_LOB); // Tipe BLOB
        $stmt->bindParam(':status_verifikasi', $status_verifikasi);
        $stmt->bindParam(':tgl_pendaftaran', $tgl_pendaftaran);
        $stmt->bindParam(':id_user', $id_user);

        if ($stmt->execute()) {
            $response["kode"] = 1;
            $response["pesan"] = "Pendaftaran sebagai anggota berhasil, mohon tunggu konfirmasi dari admin";
        } else {
            $response["kode"] = 0;
            $response["pesan"] = "Gagal melakukan pendaftaran";
        }
    }
}
echo json_encode($response);

// Tutup koneksi
$db = null;