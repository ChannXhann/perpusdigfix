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
$jsonInput = file_get_contents("php://input");
$data = json_decode($jsonInput);

// Debug: Periksa JSON Input
if ($data === null) {
    echo json_encode([
        "status" => "error",
        "message" => "Format JSON tidak valid",
        "received" => $jsonInput
    ]);
    exit;
}

// Pastikan metode adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input
    if (empty($data->username)) {
        echo json_encode([
            "status" => "error",
            "message" => "Username diperlukan"
        ]);
        exit;
    }

    if (empty($data->email_user)) {
        echo json_encode([
            "status" => "error",
            "message" => "Email diperlukan"
        ]);
        exit;
    }

    if (empty($data->password_user)) {
        echo json_encode([
            "status" => "error",
            "message" => "Password diperlukan"
        ]);
        exit;
    }

    $username = htmlspecialchars($data->username);
    $email_user = htmlspecialchars($data->email_user);
    $password_user = htmlspecialchars($data->password_user);

    // Validasi email
    if (!filter_var($email_user, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "status" => "error",
            "message" => "Format email tidak valid"
        ]);
        exit;
    }

    try {
        // Cek apakah email sudah digunakan
        $checkQuery = "SELECT * FROM user WHERE email_user = :email_user";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(':email_user', $email_user);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            echo json_encode([
                "status" => "error",
                "message" => "Email sudah terdaftar"
            ]);
            exit;
        }

        // Hash password untuk keamanan
        $hashedPassword = password_hash($password_user, PASSWORD_DEFAULT);

        // Query untuk menyimpan data pengguna baru
        $query = "INSERT INTO user (username, email_user, password_user) VALUES (:username, :email_user, :password_user)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email_user', $email_user);
        $stmt->bindParam(':password_user', $hashedPassword);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Registrasi berhasil"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Gagal menyimpan data pengguna"
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Terjadi kesalahan: " . $e->getMessage()
        ]);
    }
} else {
    // Jika metode bukan POST
    echo json_encode([
        "status" => "error",
        "message" => "Metode tidak diizinkan"
    ]);
}
?>