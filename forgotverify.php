<?php
// Mulai sesi untuk mengakses session yang sudah disimpan
session_start();

// // Koneksi ke database (gunakan koneksi dari file koneksi2.php)
// include('config/koneksi.php');

// // Membuat objek koneksi database
// $db = new Database();

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     // Menggabungkan 4 input OTP
//     $input_otp = $_POST['otp1'] . $_POST['otp2'] . $_POST['otp3'] . $_POST['otp4'];

//     // Periksa apakah OTP yang dimasukkan sesuai dengan OTP yang ada di session
//     if ($input_otp == $_SESSION['otp']) {
//         // Jika OTP benar, arahkan ke halaman reset password
//         header("Location: aturulangsandi.php");
//         exit();
//     } else {
//         // Jika OTP salah
//         $_SESSION['message'] = "Kode OTP salah. Silakan coba lagi.";
//         header("Location: forgotverify.php");
//         exit();
//     }
// }
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password Verify</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Body Styling -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .main-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            width: 100%;
            max-width: 1200px;
            gap: 20px;
            margin-top: 40px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .logo-container img {
            width: 50px;
        }

        .logo-text {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5em;
            font-weight: 900;
            margin-left: 10px;
        }

        .perpus-text {
            color: #0349AD;
            font-weight: 600;
        }

        .dig-text {
            color: #FF904D;
            font-weight: 600;
        }

        .left-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .info-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            background-color: #EFEFEF;
            padding: 40px;
            border-radius: 10px;
            color: white;
            text-align: center;
            height: 650px;
            font-family: 'Poppins', sans-serif;
        }

        .right-container {
            flex: 1;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            flex-direction: column;
        }

        .login-box {
            width: 100%;
            height: auto;
            background: rgba(255, 255, 255, 0.85);
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .login-box h2 {
            margin-bottom: 20px;
            font-size: 2.3em;
            font-weight: 600;
            display: flex;
            justify-content: center;
            gap: 5px;
            color: #313131;
        }

        .login-box h2 span {
            display: inline-block;
        }

        .back-button {
            display: flex;
            align-items: center;
            position: absolute;
            top: 15px;
            left: 15px;
            font-size: 1.1em;
            color: #313131;
            text-decoration: none;
            /* Hilangkan garis bawah pada link */
            cursor: pointer;
        }

        .welcome-message {
            margin-top: -10px;
            font-size: 1em;
            color: #555;
        }

        .code-input-wrapper {
            display: flex;
            gap: 10px;
            margin-top: 50px;
            margin-bottom: 20px;
            justify-content: center;
        }

        .code-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            border: 2px solid #007bff;
            border-radius: 5px;
            color: #313131;
            outline: none;
        }

        .resend-message {
            font-size: 0.9em;
            color: #555;
            text-align: center;
            margin-top: 10px;
        }

        .resend-link {
            color: #007bff;
            text-decoration: none;
            cursor: pointer;
        }

        .resend-link:hover {
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            overflow: hidden;
            transition: 0.38s;
            border: none;
            border-radius: 8px;
            font-size: 21px;
            font-weight: 549;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .error {
            padding: 10px;
            margin: 20px 0;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }

        .error a {
            color: #721c24;
            text-decoration: underline;
        }

        .error a:hover {
            color: #f44336;
        }

        .modal {
            display: none;
            /* Modal disembunyikan secara default */
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }

        .loading-message {
            color: blue;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="right-container">
            <div class="logo-container">
                <img src="assets/images/logo_perpusdig.png" alt="PerpusDig Logo" class="logo">
                <span class="logo-text">
                    <span class="perpus-text">Perpus</span><span class="dig-text">Dig</span>
                </span>
            </div>
            <div class="login-box">
                <a href="lupakatasandi.php" class="back-button">
                    <span class="back-icon">&lt;</span>
                    <span>Kembali</span>
                </a>
                <h2><span>Verifikasi</span> <span>Kode</span></h2>
                <p class="welcome-message">Kode otentikasi telah dikirim ke email Anda</p>

                <form id="otpForm" method="POST" action="verify_otp.php">
                    <div class="code-input-wrapper">
                        <input type="text" maxlength="1" class="code-input" name="otp1" required>
                        <input type="text" maxlength="1" class="code-input" name="otp2" required>
                        <input type="text" maxlength="1" class="code-input" name="otp3" required>
                        <input type="text" maxlength="1" class="code-input" name="otp4" required>
                    </div>
                    <p class="resend-message">Tidak menerima kode? <a href="resend_otp.php" class="resend-link">Kirim
                            ulang</a></p>
                    <button type="submit" class="btn">Verifikasi</button>
                </form>
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="error"><?= $_SESSION['message']; ?></div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="left-container">
            <img src="assets/images/icon login.png" alt="ikon people login" width="580px">
        </div>
        <!-- Modal untuk status -->
        <div id="statusModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <p id="modalMessage"></p>
            </div>
        </div>
    </div>

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function showModal(type, message) {
            const modal = $("#statusModal");
            const modalMessage = $("#modalMessage");

            // Tambahkan class dan pesan sesuai tipe
            modalMessage.removeClass().addClass(type + '-message');
            modalMessage.text(message);

            // Tampilkan modal
            modal.show();

            // Event untuk menutup modal
            $(".close").click(function () {
                modal.hide();
            });

            $(window).click(function (event) {
                if (event.target === modal[0]) {
                    modal.hide();
                }
            });
        }

        $(document).ready(function () {
            $("#otpForm").submit(function (e) {
                e.preventDefault();

                // Ambil nilai OTP dari input
                const otp = [
                    $("input[name='otp1']").val(),
                    $("input[name='otp2']").val(),
                    $("input[name='otp3']").val(),
                    $("input[name='otp4']").val()
                ].join(''); // Gabungkan nilai OTP menjadi satu string

                // Validasi OTP (pastikan tidak kosong dan hanya angka)
                if (!otp) {
                    showModal('error', 'Kode OTP tidak boleh kosong.');
                    return;
                }

                if (!/^\d+$/.test(otp)) {
                    showModal('error', 'Kode OTP hanya boleh berupa angka.');
                    return;
                }

                // Tampilkan modal loading
                showModal('loading', 'Memverifikasi OTP...');

                let otpCode = otp.charAt(0) + otp.charAt(1) + otp.charAt(2) + otp.charAt(3);

                console.log(otpCode);
                // Kirim OTP ke server untuk diverifikasi
                $.ajax({
                    type: "POST",
                    url: "verify_otp.php", // URL untuk memproses OTP
                    data: { otp_code: ${otpCode} }, // Kirim data OTP
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            showModal('success', 'OTP berhasil diverifikasi! Mengarahkan ke halaman reset password...');
                            setTimeout(function () {
                                window.location.href = 'aturulangsandi.php'; // Redirect ke halaman reset password
                            }, 2000); // Tunda 2 detik sebelum redirect
                        } else {
                            showModal('error', response.message); // Tampilkan pesan error jika OTP salah
                        }
                    },
                    error: function () {

                        showModal('error', 'Terjadi kesalahan. Silakan coba lagi nanti.');
                    }
                })
                    .fail(function () {
                        alert("error");
                    });
            });
        });
    </script> -->
</body>

</html>