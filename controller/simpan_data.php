<?php
session_start();

// Pastikan koneksi sudah benar
include '../config/koneksi.php';  // Periksa jalur koneksi PDO Anda
$db = new Database();
$conn = $db->koneksi;  // Menggunakan koneksi PDO

// Ambil data dari form
$nip = $_POST['nip'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? ''; // password yang diinputkan

// Cek apakah NIP atau email sudah terdaftar
$queryCheck = "SELECT COUNT(*) FROM admin WHERE nip = :nip OR email = :email";
$stmtCheck = $conn->prepare($queryCheck);
$stmtCheck->bindParam(':nip', $nip, PDO::PARAM_STR);
$stmtCheck->bindParam(':email', $email, PDO::PARAM_STR);
$stmtCheck->execute();
$result = $stmtCheck->fetchColumn();

if ($result > 0) {
    // Jika NIP atau email sudah ada, beri pesan error
    $_SESSION['error'] = "NIP atau email sudah terdaftar!";
    header("Location: ../../view/pages_super/tambah_data_admin.php"); // Redirect kembali ke halaman tambah admin
    exit();
}

// Jika NIP dan email belum terdaftar, lakukan insert data ke database
$passwordHash = md5($password); // Contoh menggunakan MD5, bisa menggunakan hashing lain yang lebih aman seperti bcrypt

$query = "INSERT INTO admin (nip, email, password, level) VALUES (:nip, :email, :password, :level)";
$stmt = $conn->prepare($query);
$stmt->bindParam(':nip', $nip, PDO::PARAM_STR);
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->bindParam(':password', $passwordHash, PDO::PARAM_STR);
$level = 2; // Contoh level admin
$stmt->bindParam(':level', $level, PDO::PARAM_INT);

if ($stmt->execute()) {
    $_SESSION['success'] = "Admin berhasil ditambahkan.";
} else {
    $_SESSION['error'] = "Gagal menambahkan admin.";
}

header("Location: ../../view/pages_super/tambah_data_admin.php"); // Redirect ke halaman Data Admin setelah berhasil
exit();
?>
