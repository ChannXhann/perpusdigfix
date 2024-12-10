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

if (empty($data->username)) {
    echo json_encode([
        "status" => "error",
        "message" => "Username diperlukan"
    ]);
    exit;
}

$username = htmlspecialchars($data->username);

try {
    // Query untuk memeriksa apakah username ada di database
    $checkQuery = "SELECT * FROM user WHERE username = :username";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "status" => "erro",
            "message" => "Username sudah terdaftar"
        ]);
    } else {
        echo json_encode([
            "status" => "success",
            "message" => "Username tidak terdaftar"
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Kesalahan server",
        "error_info" => $e->getMessage()
    ]);
}