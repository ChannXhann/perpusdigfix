<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include_once '../config/koneksi.php';

$db = new Database();
$conn = $db->koneksi;

$jsonInput = file_get_contents("php://input");
$data = json_decode($jsonInput);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Metode tidak diizinkan"
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

$email_user = htmlspecialchars($data->email_user);

if (!filter_var($email_user, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status" => "error",
        "message" => "Format email tidak valid"
    ]);
    exit;
}

try {
    $checkQuery = "SELECT * FROM user WHERE email_user = :email_user";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bindParam(':email_user', $email_user);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "Email terdaftar"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Email tidak terdaftar"
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Kesalahan server",
        "error_info" => $e->getMessage()
    ]);
}