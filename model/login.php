<?php
// login.php
require_once 'koneksi.php';

// Mulai sesi hanya jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil email dan password dari input form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Buat koneksi database
    $db = new Database();
    $conn = $db->koneksi;

    // Query untuk mengambil data admin berdasarkan email
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debugging: Cetak hasil query
    if ($user) {
        echo "User ditemukan: " . $user['email'] . "<br>";
        echo "Password hash: " . $user['password'] . "<br>";

        // Debugging untuk cek password
        if (password_verify($password, $user['password'])) {
            echo "Password cocok!<br>";
        } else {
            echo "Password tidak cocok!<br>";
        }
    } else {
        echo "User tidak ditemukan<br>";
    }

    // Cek apakah user ditemukan dan password benar
    if ($user && password_verify($password, $user['password'])) {
        // Cek level admin
        if ($user['level'] == 1) {
            $_SESSION['admin'] = $user['email'];
            header("Location: ../../view/pages_super/dashboard_super.php");
            exit();
        } else {
            echo "Anda tidak memiliki akses ke halaman ini.";
        }
    } else {
        echo "Email atau kata sandi salah.";
    }
}
?>
