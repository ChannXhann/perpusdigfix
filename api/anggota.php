<?php
include '../config/koneksi.php';

$response = array();
$database = new Database();
$db = $database->koneksi;

if ($db) {
    try {
        // Ambil id_user dari parameter
        $id_user = isset($_GET['id_user']) ? $_GET['id_user'] : null;

        if ($id_user) {
            $sql = "SELECT 
                        user.username, 
                        user.email_user, 
                        user.password_user, 
                        anggota.nik_anggota, 
                        anggota.nama_anggota, 
                        anggota.telp, 
                        anggota.alamat, 
                        anggota.foto_anggota,
                        anggota.status_verifikasi
                    FROM user 
                    LEFT JOIN anggota ON user.id_user = anggota.id_user
                    WHERE user.id_user = :id_user";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $response["kode"] = 1;
                $response["pesan"] = "Data tersedia";
                $response["data"] = array();

                // Mengambil data
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $data = array();
                    $data["username"] = $row["username"];
                    $data["email_user"] = $row["email_user"];
                    $data["password_user"] = $row["password_user"];
                    $data["nik_anggota"] = $row["nik_anggota"];
                    $data["nama_anggota"] = $row["nama_anggota"];
                    $data["telp"] = $row["telp"];
                    $data["alamat"] = $row["alamat"];
                    $data["foto_anggota"] = base64_encode($row["foto_anggota"]); // Konversi BLOB ke base64
                    $data["status_verifikasi"] = $row["status_verifikasi"];

                    array_push($response["data"], $data);
                }
            } else {
                $response["kode"] = 0;
                $response["pesan"] = "Data tidak tersedia";
            }
        } else {
            $response["kode"] = 0;
            $response["pesan"] = "id_user tidak ditemukan";
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