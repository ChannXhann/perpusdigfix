<?php
session_start();
include 'config/koneksi.php'; // Koneksi ke database

require 'vendor/autoload.php'; // Pastikan path autoload sesuai
// Import PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Autoload PHPMailer

$db = new Database();
// Membuat objek koneksi database

// Fungsi untuk generate OTP
function generateOTP($length = 4) {
    return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

// Periksa jika form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan email dari form
    $email = $_POST['email_user'] ?? null;

    if (empty($email)) {
        $_SESSION['message'] = 'Email tidak dikirimkan!';
        echo json_encode(['status' => 'error', 'message' => 'Email tidak dikirimkan!']);
        exit;
    }

    // Validasi format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = 'Format email tidak valid!';
        echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid!']);
        exit;
    }

    try {
        // Periksa email di tabel admin
        $sql = "SELECT * FROM `admin` WHERE email = :email";
        $stmt = $db->koneksi->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Jika email ditemukan, generate OTP
            $otp = generateOTP();
            $_SESSION['otp'] = $otp;
            $_SESSION['nip'] = $result['nip'];
            $_SESSION['email'] = $email;
            $_SESSION['otp_expiry'] = time() + (5 * 60); // OTP berlaku selama 5 menit

            try {
                $mail = new PHPMailer(true);
            } catch (\Throwable $th) {
                var_dump($th);
            }

            // Kirim OTP ke email menggunakan PHPMailer
            
            try {
                // Konfigurasi server SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Ganti dengan server SMTP Anda
                $mail->SMTPAuth = true;
                $mail->Username = 'e41231785@student.polije.ac.id'; // Ganti dengan email Anda
                $mail->Password = 'sdww mlpk bmjo fvud'; // Ganti dengan password email Anda (gunakan App Password jika ada)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587; // Atau port lain yang digunakan oleh server SMTP

                // Penerima
                $mail->setFrom('e41231785@student.polije.ac.id', 'Perpus Dig');
                $mail->addAddress($email);

                // Konten Email
                $mail->isHTML(true);
                $mail->Subject = 'Kode OTP untuk Reset Password';
                $mail->Body    = "Kode OTP Anda adalah: <strong>$otp</strong>";

                // Kirim email
                if ($mail->send()) {
                    $_SESSION['message'] = 'Kode OTP berhasil dikirim ke email.';
                    echo json_encode(['status' => 'success', 'message' => 'Kode OTP berhasil dikirim ke email.']);
                    exit;
                } else {
                    $_SESSION['message'] = 'Gagal mengirim kode OTP.';
                    echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim kode OTP.']);
                    exit;
                }
            } catch (Exception $e) {
                $_SESSION['message'] = "Gagal mengirim email. Error: {$mail->ErrorInfo}";
                echo json_encode(['status' => 'error', 'message' => "Gagal mengirim email. Error: {$mail->ErrorInfo}"]);
                exit;
            }
        } else {
            $_SESSION['message'] = 'Email tidak ditemukan.';
            echo json_encode(['status' => 'error', 'message' => 'Email tidak ditemukan.']);
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Terjadi kesalahan pada database: ' . $e->getMessage();
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada database: ' . $e->getMessage()]);
        exit;
    }
}
?>
