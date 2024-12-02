<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Mengimpor autoload Composer
require 'vendor/autoload.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email_user'];

    // Cek apakah email ada di database
    require_once 'koneksi2.php'; // Pastikan koneksi ke database

    $query = "SELECT * FROM `admin` WHERE email = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Buat OTP
        $otp = rand(1000, 9999); // OTP 4 digit

        // Konfigurasi PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Pengaturan server SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.polije.ac.id'; // Sesuaikan dengan SMTP server Anda
            $mail->SMTPAuth = true;
            $mail->Username = 'e41231785@student.polije.ac.id'; // Email Anda
            $mail->Password = 'nnct hcai bbdq wept'; // Password atau app-specific password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Penerima
            $mail->setFrom('kia.man2993@gmail.com', 'Azkia');
            $mail->addAddress($email);

            // Konten email
            $mail->isHTML(true);
            $mail->Subject = 'OTP untuk Reset Kata Sandi';
            $mail->Body    = 'Kode OTP Anda adalah: <b>' . $otp . '</b>';

            // Kirim email
            $mail->send();

            // Simpan OTP dalam session untuk verifikasi
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $email;

            $_SESSION['message'] = "OTP telah dikirim ke email Anda.";
            header("Location: lupakatasandi.php");
        } catch (Exception $e) {
            $_SESSION['message'] = "Gagal mengirim OTP. Error: {$mail->ErrorInfo}";
            header("Location: lupakatasandi.php");
        }
    } else {
        $_SESSION['message'] = "Email tidak ditemukan.";
        header("Location: lupakatasandi.php");
    }
}
?>
