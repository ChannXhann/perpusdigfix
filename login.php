<?php
include('config/koneksi.php');
session_start();

$error = '';

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    if (!empty(trim($email)) && !empty(trim($pass))) {
        if (!preg_match('/^[a-zA-Z0-9]+@gmail\.com$/', $email)) {
            $error = 'Email harus menggunakan domain @gmail.com, hanya boleh mengandung huruf dan angka tanpa spasi atau karakter khusus.';
        } else {
            try {
                // Buat instance dari Database dan koneksi
                $database = new Database();
                $koneksi = $database->koneksi;

                // Query menggunakan prepared statement
                $query = "SELECT * FROM admin WHERE email = :email";
                $stmt = $koneksi->prepare($query);

                // Menggunakan bindParam untuk mengikat parameter
                $stmt->bindParam(':email', $email);

                // Eksekusi statement
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $hashedPassword = $row['password'];

                    // Verifikasi password (hash MD5)
                    // Jika password benar
                    if (md5($pass) === $hashedPassword) {
                        // Simpan data ke dalam sesi
                        $_SESSION['nip'] = $row['nip'];
                        $_SESSION['nama'] = $row['nama'];
                        $_SESSION['foto'] = $row['foto'];
                        $_SESSION['no_telp'] = $row['no_telp']; // Pastikan kolom 'foto' ada di database

                        // Redirect berdasarkan level
                        if ($row['level'] == 1) {
                            header('Location: view/pages_super/dashboard_super.php');
                            exit();
                        } elseif ($row['level'] == 2) {
                            header('Location: view/pages_admin/dashboard_admin.php');
                            exit();
                        } else {
                            $error = 'Level tidak dikenali!';
                        }
                    } else {
                        $error = 'Password salah!';
                    }

                } else {
                    $error = 'Email tidak ditemukan!';
                }
            } catch (PDOException $e) {
                $error = 'Terjadi kesalahan: ' . $e->getMessage();
            }
        }
    } else {
        $error = 'Data tidak boleh kosong!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: -110px;
            text-align: left;
        }

        .user-box {
            position: relative;
        }

        .eye-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
        }
    </style>
</head>

<body>
    <img src="/assets/images/logo_perpusdig.png" alt="logo perpusdig" width="90px">
    <div class="logo-container">
        <span class="logo-text">
            <span class="perpus-text">Perpus</span><span class="dig-text">Dig</span>
        </span>
    </div>

    <div class="container-wrapper">
        <!-- Container untuk Form Login -->
        <div class="container">
            <div class="login-box">
                <!-- Tombol Kembali -->
                <div class="back-button">
                    <a href="index.php" class="back-link">
                        <span class="back-icon">&lt;</span>
                        <span>Kembali</span>
                    </a>
                </div>
                <h2>Masuk Akun</h2>
                <!-- Pesan Selamat Datang -->
                <p class="welcome-message">Selamat Datang! Masukkan email dan kata sandi untuk mengakses akun Anda</p>

                <!-- Form Login -->
                <form method="POST" action="">
                    <div class="form-section">
                        <div class="user-box">
                            <input type="email" name="email" id="email" placeholder="Masukkan email anda" required>
                            <label for="email">Email</label>
                        </div>
                        <div class="user-box" style="position: relative;">
                            <input type="password" name="password" id="password" placeholder="Masukkan kata sandi anda"
                                required>
                            <label for="password">Kata Sandi</label>
                            <!-- Icon Mata untuk Menampilkan/Sembunyikan Password -->
                            <i class="fas fa-eye eye-icon" id="togglePassword"></i>
                        </div>
                        <button type="submit" name="submit" class="btn">Masuk</button>

                        <!-- Tampilkan pesan error jika ada, di sebelah kiri bawah form kata sandi -->
                        <?php if ($error): ?>
                            <div class="error-message"><?php echo $error; ?></div>
                        <?php endif; ?>
                    </div>
                    <a href="lupakatasandi.php" class="btn-lupakatasandi">Lupa Kata Sandi?</a>
                </form>
            </div>
        </div>

        <!-- Container Baru untuk Informasi -->
        <div class="info-container">
            <img src="assets/images/icon login.png" alt="ikon people login" width="580px">
        </div>
    </div>
    <script>
        // JavaScript untuk toggle password visibility
        const togglePassword = document.getElementById("togglePassword");
        const passwordField = document.getElementById("password");

        togglePassword.addEventListener("click", function () {
            // Toggle tipe input password
            const type = passwordField.type === "password" ? "text" : "password";
            passwordField.type = type;

            // Toggle eye icon (menjadi mata tertutup jika password tersembunyi)
            this.classList.toggle("fa-eye-slash");
        });
    </script>
    <script>
        document.querySelector("form").addEventListener("submit", function (event) {
            const emailField = document.getElementById("email");
            const email = emailField.value;

            // Regex untuk validasi email @gmail.com dengan angka saja sebelum @
            if (!preg_match('/^[a-zA-Z0-9]+@gmail\.com$/', $email)) {
                $error = 'Email harus menggunakan domain @gmail.com, hanya boleh mengandung huruf dan angka tanpa spasi atau karakter khusus.';
            }

            if (!regex.test(email)) {
                alert("Email harus menggunakan domain @gmail.com dan hanya boleh mengandung angka sebelum @.");
                event.preventDefault(); // Mencegah pengiriman form
            }
        });
    </script>
</body>

</html>