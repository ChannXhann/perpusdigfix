<?php
// Header untuk JSON dan CORS
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Include file koneksi
include_once '../config/koneksi.php';

// Buat instance dari class Database
$db = new Database();
$conn = $db->koneksi;

// Ambil data dari input JSON
$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi jika data JSON tidak valid
    if ($data === null) {
        echo json_encode([
            "status" => "error",
            "pesan" => "Format JSON tidak valid"
        ]);
        exit;
    }

    // Validasi input
    if (!empty($data->email) && !empty($data->password)) {
        $email = htmlspecialchars($data->email);
        $password = htmlspecialchars($data->password);

        try {
            // Query untuk mencari user berdasarkan email/username
            $query = "SELECT * FROM user WHERE email_user = :email_user OR username = :email_user";
            $stmt = $conn->prepare($query);

            // Bind parameter
            $stmt->bindParam(':email_user', $email);

            // Eksekusi query
            $stmt->execute();

            // Cek jika user ditemukan
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verifikasi password yang sudah di-hash
                if (password_verify($password, $user['password_user'])) {

                    // Query untuk mengecek apakah user adalah anggota
                    $checkAnggotaQuery = "SELECT nik_anggota, nama_anggota, telp, alamat, foto_anggota, status_verifikasi, tgl_pendaftaran 
                                          FROM anggota WHERE id_user = :id_user AND status_verifikasi = 'Disetujui'";
                    $checkStmt = $conn->prepare($checkAnggotaQuery);
                    $checkStmt->bindParam(':id_user', $user['id_user']);
                    $checkStmt->execute();

                    // Cek apakah anggota ditemukan
                    if ($checkStmt->rowCount() > 0) {
                        $anggota = $checkStmt->fetch(PDO::FETCH_ASSOC);
                        $nik_anggota = $anggota['nik_anggota'];
                        $nama_anggota = $anggota['nama_anggota'];
                        $telp = $anggota['telp'];
                        $alamat = $anggota['alamat'];
                        $foto_anggota = base64_encode($anggota['foto_anggota']);
                        $status_verifikasi = $anggota['status_verifikasi'];
                        $tgl_pendaftaran = $anggota['tgl_pendaftaran'];
                    } else {
                        // Jika anggota tidak ditemukan, set nilai null
                        $nik_anggota = null;
                        $nama_anggota = null;
                        $telp = null;
                        $alamat = null;
                        $foto_anggota = null;
                        $status_verifikasi = null;
                        $tgl_pendaftaran = null;
                    }

                    // Login berhasil
                    echo json_encode([
                        "status" => "success",
                        "pesan" => "Login berhasil",
                        "data" => [
                            "id_user" => $user['id_user'],
                            "username" => $user['username'],
                            "email_user" => $user['email_user'],
                            "nik_anggota" => $nik_anggota,
                            "nama_anggota" => $nama_anggota,
                            "telp" => $telp,
                            "alamat" => $alamat,
                            "foto_anggota" => $foto_anggota,
                            "status_verifikasi" => $status_verifikasi,
                            "tgl_pendaftaran" => $tgl_pendaftaran
                        ]
                    ]);
                } else {
                    // Password salah
                    echo json_encode([
                        "status" => "error",
                        "pesan" => "Password salah"
                    ]);
                }
            } else {
                // User tidak ditemukan
                echo json_encode([
                    "status" => "error",
                    "pesan" => "Email atau username tidak ditemukan"
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "pesan" => "Terjadi kesalahan: " . $e->getMessage()
            ]);
        }
    } else {
        // Jika input tidak lengkap
        echo json_encode([
            "status" => "error",
            "pesan" => "Email dan password harus diisi"
        ]);
    }
} else {
    // Jika metode bukan POST
    echo json_encode([
        "status" => "error",
        "pesan" => "Metode tidak diizinkan"
    ]);
}