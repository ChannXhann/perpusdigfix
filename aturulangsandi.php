<?php
// Mulai sesi untuk mengakses session yang sudah disimpan
session_start();

// Koneksi ke database (gunakan koneksi dari file koneksi2.php)
require_once 'config/koneksi.php';

// Membuat objek koneksi database
$db = new Database();
$connection = $db->getConnection();

// Menyimpan pesan feedback
$feedbackMessage = "";

// Memeriksa apakah pengguna sudah login, jika tidak redirect ke login
if (!isset($_SESSION['nip']) || !isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect jika belum login
    exit();
}

// Mengambil data email dari session
$userEmail = $_SESSION['email']; // Ambil email dari session
$userId = $_SESSION['nip']; // Ambil ID pengguna dari session

// Jika formulir disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan input dari formulir
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validasi input
    if (empty($newPassword) || empty($confirmPassword)) {
        $feedbackMessage = "Harap isi kedua kolom kata sandi.";
    } elseif ($newPassword !== $confirmPassword) {
        $feedbackMessage = "Kata sandi tidak cocok. Silakan coba lagi.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+={}\[\]:;"\'<>,.?\/-])[a-zA-Z\d!@#$%^&*()_+={}\[\]:;"\'<>,.?\/-\/s]{8,}$/', $newPassword)) {
        $feedbackMessage = "Kata sandi harus minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan karakter khusus.";
    } else {
        // Hash password menggunakan MD5
        $hashedPassword = md5($newPassword);

        // Query untuk mengupdate kata sandi berdasarkan email
        $sql = "UPDATE admin SET password='$hashedPassword' WHERE email='$userEmail'";
        try {
            $stmt = $connection->prepare($sql);
            var_dump($stmt);
            $stmt->execute();
            http_response_code(200);
            echo "
            <script type='text/javascript'>
            alert('Kata sandi berhasil diperbarui!');
            window.location.href = 'login.php';
            </script>
            ";
        } catch (PDOException $e) {
            http_response_code(500);
            echo "<script type='text/javascript'>alert('Gagal memperbarui kata sandi!');</script>";
        }

        // if ($db->koneksi->query($sql) === TRUE) {
        //     $feedbackMessage = "Kata sandi berhasil disimpan!";
        //     header("Location: login.php"); // Redirect ke halaman login setelah berhasil
        //     exit();
        // } else {
        //     $feedbackMessage = "Terjadi kesalahan saat menyimpan kata sandi: " . $db->koneksi->error;
        // }
    }
}


// Menutup koneksi
// $conn->close();
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <title>Atur Ulang Kata Sandi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            width: 100vw;
            background-color: #EFEFEF;
            /* overflow: hidden; Untuk menghindari scroll jika tidak diperlukan */
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            /* Menyusun elemen di tengah secara vertikal */
            width: 100%;
            height: 100%;
            /* Membuat container mengisi seluruh tinggi layar */
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            position: relative;
            /* Agar logo berada relatif terhadap kontainer */
        }

        .logo {
            position: absolute;
            /* Logo akan diposisikan di dalam kontainer */
            top: 50px;
            /* Memberikan jarak dari atas kontainer */
            left: 2rem;
            /* Memberikan jarak dari kiri kontainer */
            display: flex;
            /* Menggunakan flexbox untuk menyusun logo dan teks */
            align-items: center;
            /* Menyelaraskan logo dan teks secara vertikal */
            margin-bottom: 10rem;
        }

        .logo img {
            height: 50px;
            width: 50px;
            margin-right: 0.5rem;
            /* Memberikan jarak antara gambar dan teks */
        }

        /* Teks dalam logo */
        .logo span {
            font-family: 'Poppins';
            font-size: 24px;
            font-weight: bold;
            color: #ff6b00;
        }

        .logo span.blue {
            font-family: 'Poppins';
            font-weight: bold;
            color: #007bff;
        }

        /* Form container */
        .form-container {
            display: flex;
            flex-direction: row;
            width: 100%;
            gap: 20px;
            justify-content: space-between;
        }

        /* Form box */
        .form-box {
            padding: 20px;
            width: 50%;
        }

        .back-link {
            text-decoration: none;
            color: #000;
            font-size: 14px;
        }

        h2 {
            text-align: center;
            font-size: 40px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        p {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-bottom: 40px;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        label {
            font-size: 14px;
            color: #333;
            margin-bottom: 15px;
            /* Memberikan jarak antara label dan input */
        }

        /* Pengaturan umum untuk input */
        input[type="password"],
        input[type="text"] {
            width: 100%;
            /* Input mengisi lebar penuh */
            padding: 12px 15px;
            /* Padding yang lebih besar untuk kenyamanan */
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            /* Membuat sudut input lebih halus */
            background-color: #f9f9f9;
            /* Memberikan latar belakang terang pada input */
            margin-bottom: 10px;
            /* Memberikan jarak antara input dan elemen berikutnya */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            /* Memberikan bayangan halus untuk efek solid */
            padding-right: 35px;
            /* Memberikan ruang untuk ikon di dalam input */
            position: relative;
            /* Untuk memposisikan ikon relatif terhadap input */
        }

        /* Pengaturan untuk input saat fokus */
        input[type="password"]:focus,
        input[type="text"]:focus {
            border-color: #007bff;
            /* Warna border berubah saat fokus */
            outline: none;
            /* Menghilangkan outline default */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            /* Menambahkan bayangan saat fokus */
        }

        /* Pengaturan untuk ikon mata */
        .toggle-password {
            position: absolute;
            /* Posisi absolut untuk menempatkan ikon di dalam input */
            right: 10px;
            /* Jarak 10px dari sisi kanan input */
            top: 50%;
            /* Menempatkan ikon di tengah secara vertikal */
            transform: translateY(-50%);
            /* Menggeser ikon untuk benar-benar berada di tengah */
            cursor: pointer;
            /* Mengubah kursor saat hover */
            color: #555;
            font-size: 18px;
            /* Ukuran font yang lebih besar untuk ikon */
        }

        /* Responsif - Menyesuaikan ukuran ikon untuk layar kecil */
        @media (max-width: 600px) {
            .toggle-password {
                font-size: 16px;
                /* Ukuran ikon lebih kecil pada layar kecil */
                right: 8px;
                /* Menyesuaikan posisi ikon */
            }
        }

        /* Pengaturan untuk label */
        label {
            position: absolute;
            /* Posisi absolut untuk memposisikan label */
            top: 50%;
            /* Menempatkan label di tengah secara vertikal */
            left: 12px;
            /* Menjaga label tetap dekat dengan input */
            font-size: 1rem;
            color: #999;
            transform: translateY(-50%);
            /* Menjaga label berada di tengah secara vertikal */
            transition: 0.3s ease all;
            /* Menambahkan animasi untuk transisi */
            background-color: #fff;
            /* Latar belakang putih untuk label */
            padding: 0 5px;
            /* Padding agar label terlihat seperti berada di tengah border */
            z-index: 1;
            /* Membawa label ke depan */
            pointer-events: none;
            /* Menghindari interaksi dengan label */
        }

        /* Efek label saat input diisi atau fokus */
        input[type="password"]:focus~label,
        input[type="text"]:focus~label,
        input[type="password"]:valid~label,
        input[type="text"]:valid~label {
            padding: 1rem 0.8rem 1rem 0.8rem;
            /* Menambahkan padding untuk ruang label */
            top: -10px;
            /* Menempatkan label tepat di atas border input */
            left: 10px;
            /* Menjaga jarak kiri agar tidak terlalu dekat dengan border */
            font-size: 12px;
            /* Mengurangi ukuran font label */
            color: #007bff;
            /* Ubah warna label saat fokus */
            background-color: #fff;
            /* Latar belakang putih untuk label */
            padding: 0 5px;
            /* Padding agar label terlihat seperti berada di tengah border */
            z-index: 1;
            /* Membawa label ke depan */
        }

        /* Menambahkan gaya untuk input yang kosong dan label di bawah */
        input[type="password"]:not(:focus):not(:valid)~label,
        input[type="text"]:not(:focus):not(:valid)~label {
            top: -1px;
            /* Menjaga posisi label di tengah saat input kosong */
            left: 10px;
            /* Menjaga label tetap dekat dengan input */
            font-size: 0.8rem;
            /* Ukuran font asli */
            color: #999;
            /* Mengubah warna label saat input kosong */
            transform: translateY(-50%);
            /* Menjaga label berada di tengah secara vertikal */
        }

        /* Menambahkan animasi saat label pindah ke posisi baru */
        @keyframes labelMove {
            0% {
                top: 50%;
                font-size: 16px;
                color: #999;
                transform: translateY(-50%);
            }

            100% {
                top: -10px;
                font-size: 12px;
                color: #007bff;
                transform: translateY(0);
            }
        }

        input[type="password"]:focus~label,
        input[type="text"]:focus~label,
        input[type="password"]:valid~label,
        input[type="text"]:valid~label {
            animation: all 0.3s ease;
        }

        .feedback-message {
            align-items: center;
            font-size: 1rem;
            color: green;
            margin-top: 10px;
        }

        button {
            width: 100%;
            padding: 12px 15px;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            /* Memberikan jarak antara tombol dan input sebelumnya */
            margin-bottom: 10px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Kotak Gambar */
        .image-box {
            background-color: #f0f4ff;
            /* Warna latar belakang */
            display: flex;
            /* Menggunakan Flexbox untuk penataan */
            align-items: center;
            /* Menyelaraskan item secara vertikal di tengah */
            justify-content: center;
            /* Menyelaraskan item secara horizontal di tengah */
            width: 50%;
            /* Lebar kotak gambar */
            height: 100%;
            /* Tentukan tinggi kotak gambar sesuai kebutuhan */
            padding: 20px;
            /* Padding di sekitar konten dalam kotak gambar */
        }

        /* Mengatur gambar di dalam kotak gambar */
        .image-box img {
            max-width: 100%;
            /* Membatasi lebar gambar agar tidak lebih dari kontainer */
            height: auto;
            /* Mempertahankan rasio aspek gambar */
            object-fit: cover;
            /* Memastikan gambar mengisi area dengan seimbang */
        }

        /* Responsif: jika layar lebih kecil, lebar kotak gambar akan menyesuaikan */
        @media (max-width: 768px) {
            .image-box {
                width: 100%;
                /* Memastikan kotak gambar menggunakan lebar penuh pada layar kecil */
                height: 200px;
                /* Menurunkan tinggi kotak gambar pada layar kecil */
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img alt="logo perpusdig" src="assets/images/logo_perpusdig.png" />
            <span class="blue">Perpus</span>
            <span>Dig</span>
        </div>
        <div class="form-container">
            <div class="form-box">
                <a href="forgotverify.php" class="back-link">&lt; Kembali</a>
                <h2>Atur Ulang Kata Sandi</h2>
                <p>Kata sandi Anda sebelumnya telah diatur ulang. Silakan masukkan kata sandi baru untuk akun Anda</p>
                <form id="resetForm" action="aturulangsandi.php" method="POST">
                    <!-- Input untuk kata sandi baru -->
                    <div class="input-group">
                        <input type="password" id="newPassword" name="newPassword" placeholder=" " required>
                        <label for="newPassword">Kata Sandi Baru</label>
                        <span class="toggle-password" onclick="togglePassword('newPassword')">&#128065;</span>
                    </div>

                    <!-- Input untuk konfirmasi kata sandi -->
                    <div class="input-group">
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder=" " required>
                        <label for="confirmPassword">Konfirmasi Kata Sandi</label>
                        <span class="toggle-password" onclick="togglePassword('confirmPassword')">&#128065;</span>
                    </div>

                    <!-- Tombol untuk menyimpan kata sandi -->
                    <button type="submit">Simpan</button>

                    <!-- Tempat untuk menampilkan pesan feedback -->
                    <div id="feedbackMessage" class="feedback-message">
                        <?php if (isset($feedbackMessage))
                            echo $feedbackMessage; ?>
                    </div>
                </form>
            </div>
            <div class="image-box">
                <img src="assets/images/password.png" alt="Padlock Icon">
            </div>
        </div>
    </div>
    <script>
        // Fungsi untuk men-toggle tipe input password antara 'password' dan 'text'
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId); // Mengambil elemen input berdasarkan ID
            // Toggle antara tipe password dan text
            if (field.type === "password") {
                field.type = "text"; // Ubah menjadi 'text' agar kata sandi terlihat
            } else {
                field.type = "password"; // Ubah kembali menjadi 'password' untuk menyembunyikan kata sandi
            }
        }

        // Fungsi untuk menyimpan kata sandi dan validasi
        function savePassword() {
            const newPassword = document.getElementById("newPassword").value;
            const confirmPassword = document.getElementById("confirmPassword").value;
            const feedbackMessage = document.getElementById("feedbackMessage");

            // Regex untuk memeriksa validasi kata sandi
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (newPassword === "" || confirmPassword === "") {
                feedbackMessage.style.color = "red";
                feedbackMessage.textContent = "Harap isi kedua kolom kata sandi.";
            } else if (newPassword !== confirmPassword) {
                feedbackMessage.style.color = "red";
                feedbackMessage.textContent = "Kata sandi tidak cocok. Silakan coba lagi.";
            } else if (!passwordRegex.test(newPassword)) {
                feedbackMessage.style.color = "red";
                feedbackMessage.textContent = "Kata sandi harus minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan karakter khusus.";
            } else {
                feedbackMessage.style.color = "green";
                feedbackMessage.textContent = "Kata sandi berhasil disimpan!";
                document.getElementById("resetForm").submit(); // Submit form jika validasi berhasil
            }
        }

    </script>
</body>

</html>