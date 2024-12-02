<?php
session_start();
require 'config/koneksi.php';
require 'send_email.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email_user'];

    // Cek apakah email ada di database
    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email ditemukan, kirim kode OTP
        $otp = rand(1000, 9999);
        $_SESSION['otp'] = $otp;
        $_SESSION['email_user'] = $email;

        if (sendOTPEmail($email, $otp)) {
            header("Location: forgotverify.php");
            exit();
        } else {
            $_SESSION['message'] = "Gagal mengirim email. Silakan coba lagi.";
        }
    } else {
        $_SESSION['message'] = "Email tidak ditemukan.";
    }

    $stmt->close();
    $koneksi->close();
    header("Location: reset_password_form.php");
    exit();
}
?>
