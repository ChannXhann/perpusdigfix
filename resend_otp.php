<?php
session_start();
include 'config/koneksi.php'; // Koneksi ke database

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $otp = rand(1000, 9999); // Generate OTP baru
    $_SESSION['otp'] = $otp;  // Simpan OTP di session

    // Kirim ulang OTP
    if (sendOTPEmail($email, $otp)) {
        $_SESSION['message'] = "Kode OTP telah dikirim ulang ke email Anda.";
    } else {
        $_SESSION['message'] = "Gagal mengirim ulang OTP. Silakan coba lagi.";
    }
} else {
    $_SESSION['message'] = "Email tidak ditemukan.";
}

header("Location: forgotverify.php");
exit();
?>
