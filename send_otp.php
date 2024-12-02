<?php
session_start();
include 'config/koneksi.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

header("Content-Type:application/json");

// Fungsi untuk generate OTP
function generateOTP($length = 4)
{
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= rand(0, 9);
    }
    return $otp;
}

// Fungsi untuk mengirim OTP ke email
function sendOTPEmail($email, $otp)
{
    $mail = new PHPMailer(true);
    $senderEmail = 'e41231785@student.polije.ac.id'; // Ganti dengan email anda
    $senderName = 'perpusdig'; // Ganti dengan nama pengirim

    try {
        // Set pengaturan SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Ganti dengan SMTP server yang Anda gunakan
        $mail->SMTPAuth = true;
        $mail->Username = $senderEmail;  // Ganti dengan alamat email pengirim
        $mail->Password = 'uhem ezhx ksfp lsbm';  // Ganti dengan password email pengirim
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Aktifkan mode debugging SMTP
        $mail->SMTPDebug = 2;  // Level debugging: 0 = Off, 1 = Client Messages, 2 = Client and Server Messages

        // Penerima
        $mail->setFrom($senderEmail, $senderName);
        $mail->addAddress($email);

        // Konten email
        $mail->isHTML(true);
        $mail->Subject = 'Kode OTP untuk Perubahan Kata Sandi';
        $mail->Body = 'Kode OTP Anda adalah: ' . $otp;

        // Kirim email
        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}

// Cek apakah email dikirimkan melalui POST
if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $response = array();

    // Validasi apakah email tidak kosong dan format email valid
    if (empty($email)) {
        $response['status'] = 'error';
        $response['message'] = 'Email tidak boleh kosong.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['status'] = 'error';
        $response['message'] = 'Email tidak valid.';
    } else {
        // Cek apakah email ada di database
        if ($koneksi) {
            $stmt = $koneksi->prepare("SELECT * FROM `admin` WHERE `email` = ?;");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Jika email ditemukan, generate OTP
                $otp = generateOTP(4);  // Generate OTP 4 digit
                $_SESSION['otp'] = $otp;  // Menyimpan OTP dalam session
                $_SESSION['email'] = $email;
            // Simpan email ke session untuk verifikasi berikutnya

                // Kirim OTP ke email pengguna menggunakan fungsi sendOtp
                if (sendOTPEmail($email, $otp)) {
                    $response['status'] = 'success';
                    $response['redirect'] = true;
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Gagal mengirim OTP ke email.';
                }
            } else {
                // Jika email tidak ditemukan
                $response['status'] = 'error';
                $response['message'] = 'Email tidak ditemukan.';
            }

            // Menutup statement
            $stmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Koneksi database gagal.';
        }
    }

    // Menutup koneksi
    $koneksi->close();

    // Mengembalikan respon dalam format JSON
    echo json_encode($response);
} else {
    // Jika email tidak dikirimkan lewat POST
    echo json_encode(array('status' => 'error', 'message' => 'Email tidak dikirimkan.'));
}
?>