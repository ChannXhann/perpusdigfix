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

// Debug jika JSON tidak valid
if ($data === null) {
    echo json_encode([
        "status" => "error",
        "message" => "Format JSON tidak valid",
        "received" => $jsonInput
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Metode tidak diizinkan"
    ]);
    exit;
}

// Validasi input
if (empty($data->email_user)) {
    echo json_encode([
        "status" => "error",
        "message" => "Email diperlukan"
    ]);
    exit;
}

if (empty($data->new_password)) {
    echo json_encode([
        "status" => "error",
        "message" => "Password baru diperlukan"
    ]);
    exit;
}

$email_user = htmlspecialchars($data->email_user);
$new_password = htmlspecialchars($data->new_password);

// Validasi format email
if (!filter_var($email_user, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status" => "error",
        "message" => "Format email tidak valid"
    ]);
    exit;
}

try {
    // Cek apakah email ada di database
    $checkQuery = "SELECT * FROM user WHERE email_user = :email_user";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindParam(':email_user', $email_user);
    $checkStmt->execute();

    if ($checkStmt->rowCount() === 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Email tidak ditemukan",
            "debug_email" => $email_user
        ]);
        exit;
    }

    // Hash password baru
    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password di database
    $updateQuery = "UPDATE user SET password_user = :password_user WHERE email_user = :email_user";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':password_user', $hashedPassword);
    $updateStmt->bindParam(':email_user', $email_user);

    $conn->beginTransaction();
    if ($updateStmt->execute()) {
        $conn->commit();
        echo json_encode([
            "status" => "success",
            "message" => "Password berhasil diperbarui"
        ]);
    } else {
        $conn->rollBack();
        echo json_encode([
            "status" => "error",
            "message" => "Gagal memperbarui password",
            "debug_query" => $updateQuery
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Terjadi kesalahan pada server",
        "error_info" => $e->getMessage()
    ]);
}
?>