<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['otp']) && isset($_SESSION['otp_expiry'])) {
        $currentTime = time();

        if (isset($_POST['otp1']) && isset($_POST['otp2']) && isset($_POST['otp3']) && isset($_POST['otp4'])) {
            $otpCode = $_POST['otp1'] . $_POST['otp2'] . $_POST['otp3'] . $_POST['otp4'];
            if ($_SESSION['otp'] === $otpCode) {
                header('Location: aturulangsandi.php');
            } else {
                echo "
                <script type='text/javascript'>alert('Kode OTP Tidak Valid!');
                
                window.location.href = 'forgotverify.php';
                </script>";
            }
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Kode OTP tidak ditemukan atau Kode OTP telah kedaluwarsa';
        http_response_code(400);
        echo json_encode($response);
        exit;
    }
}




//     $inputOtp = $_POST['otp_code'];

//     echo json_decode($input_otp);

//     // Cek apakah OTP tersimpan di session
//     if (!isset($_SESSION['otp'], )) {
//         $response['status'] = 'error';
//         $response['message'] = 'Kode OTP tidak ditemukan.';
//         http_response_code(400);
//         echo json_encode($response);
//         exit;
//     }

//     $currentTime = time();

//     // Cek waktu kedaluwarsa OTP
//     if ($currentTime > $_SESSION['otp_expiry']) {
//         unset($_SESSION['otp'], $_SESSION['otp_expiry']); // Hapus data OTP dari session
//         $response['status'] = 'error';
//         $response['message'] = 'Kode OTP telah kedaluwarsa. Silakan minta kode baru.';
//         http_response_code(400);
//         echo json_encode($response);
//         exit;
//     }

//     // Validasi OTP
//     if ($inputOtp == $_SESSION['otp']) {
//         unset($_SESSION['otp'], $_SESSION['otp_expiry']); // Hapus OTP setelah berhasil diverifikasi
//         $response['status'] = 'success';
//         $response['message'] = 'Kode OTP valid.';
//     } else {
//         $response['status'] = 'error';
//         $response['message'] = 'Kode OTP tidak valid. Silakan coba lagi.';
//     }

//     echo json_encode($response);
// } else {
//     // Jika request tidak sesuai, beri response error
//     $response['status'] = 'error';
//     $response['message'] = 'Request tidak valid.';
//     var_dump($response);
//     echo json_encode($response);
// }
