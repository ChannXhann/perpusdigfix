<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
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
            justify-content: center; /* Menyusun elemen di tengah secara vertikal */
            width: 100%;
            height: 100%; /* Membuat container mengisi seluruh tinggi layar */
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            position: relative; /* Agar logo berada relatif terhadap kontainer */
        }

        .logo {
            position: absolute; /* Logo akan diposisikan di dalam kontainer */
            top: 50px; /* Memberikan jarak dari atas kontainer */
            left: 2rem; /* Memberikan jarak dari kiri kontainer */
            display: flex; /* Menggunakan flexbox untuk menyusun logo dan teks */
            align-items: center; /* Menyelaraskan logo dan teks secara vertikal */
            margin-bottom: 5rem;
        }

        .logo img {
            height: 50px;
            width: 50px;
            margin-right: 1rem; /* Memberikan jarak antara gambar dan teks */
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
            margin-bottom: 15px; /* Memberikan jarak antara label dan input */
        }

        /* Pengaturan umum untuk input */
        input[type="password"], input[type="text"] {
            width: 100%; /* Input mengisi lebar penuh */
            padding: 12px 15px; /* Padding yang lebih besar untuk kenyamanan */
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px; /* Membuat sudut input lebih halus */
            background-color: #f9f9f9; /* Memberikan latar belakang terang pada input */
            margin-bottom: 10px; /* Memberikan jarak antara input dan elemen berikutnya */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); /* Memberikan bayangan halus untuk efek solid */
            padding-right: 35px; /* Memberikan ruang untuk ikon di dalam input */
            position: relative; /* Untuk memposisikan ikon relatif terhadap input */
        }

        /* Pengaturan untuk input saat fokus */
        input[type="password"]:focus, input[type="text"]:focus {
            border-color: #007bff; /* Warna border berubah saat fokus */
            outline: none; /* Menghilangkan outline default */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Menambahkan bayangan saat fokus */
        }

        /* Pengaturan untuk ikon mata */
        .toggle-password {
            position: absolute; /* Posisi absolut untuk menempatkan ikon di dalam input */
            right: 10px; /* Jarak 10px dari sisi kanan input */
            top: 50%; /* Menempatkan ikon di tengah secara vertikal */
            transform: translateY(-50%); /* Menggeser ikon untuk benar-benar berada di tengah */
            cursor: pointer; /* Mengubah kursor saat hover */
            color: #555;
            font-size: 18px; /* Ukuran font yang lebih besar untuk ikon */
        }

        /* Responsif - Menyesuaikan ukuran ikon untuk layar kecil */
        @media (max-width: 600px) {
            .toggle-password {
                font-size: 16px; /* Ukuran ikon lebih kecil pada layar kecil */
                right: 8px; /* Menyesuaikan posisi ikon */
            }
        }

        /* Pengaturan untuk label */
        label {
            position: absolute; /* Posisi absolut untuk memposisikan label */
            top: 50%; /* Menempatkan label di tengah secara vertikal */
            left: 12px; /* Menjaga label tetap dekat dengan input */
            font-size: 1rem;
            color: #999;
            transform: translateY(-50%); /* Menjaga label berada di tengah secara vertikal */
            transition: 0.3s ease all; /* Menambahkan animasi untuk transisi */
            background-color: #fff; /* Latar belakang putih untuk label */
            padding: 0 5px; /* Padding agar label terlihat seperti berada di tengah border */
            z-index: 1; /* Membawa label ke depan */
            pointer-events: none; /* Menghindari interaksi dengan label */
        }

        /* Efek label saat input diisi atau fokus */
        input[type="password"]:focus ~ label, input[type="text"]:focus ~ label,
        input[type="password"]:valid ~ label, input[type="text"]:valid ~ label {
            top: -10px; /* Menempatkan label tepat di atas border input */
            left: 10px; /* Menjaga jarak kiri agar tidak terlalu dekat dengan border */
            font-size: 12px; /* Mengurangi ukuran font label */
            color: #007bff; /* Ubah warna label saat fokus */
            background-color: #fff; /* Latar belakang putih untuk label */
            padding: 0 5px; /* Padding agar label terlihat seperti berada di tengah border */
            z-index: 1; /* Membawa label ke depan */
        }

        /* Menambahkan gaya untuk input yang kosong dan label di bawah */
        input[type="password"]:not(:focus):not(:valid) ~ label,
        input[type="text"]:not(:focus):not(:valid) ~ label {
            top: 50%; /* Menjaga posisi label di tengah saat input kosong */
            left: 12px; /* Menjaga label tetap dekat dengan input */
            font-size: 16px; /* Ukuran font asli */
            color: #999; /* Mengubah warna label saat input kosong */
            transform: translateY(-50%); /* Menjaga label berada di tengah secara vertikal */
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

        input[type="password"]:focus ~ label, input[type="text"]:focus ~ label,
        input[type="password"]:valid ~ label, input[type="text"]:valid ~ label {
            animation: labelMove 0.3s ease forwards;
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
            margin-top: 10px; /* Memberikan jarak antara tombol dan input sebelumnya */
            margin-bottom: 10px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Kotak Gambar */
        .image-box {
            background-color: #f0f4ff; /* Warna latar belakang */
            display: flex; /* Menggunakan Flexbox untuk penataan */
            align-items: center; /* Menyelaraskan item secara vertikal di tengah */
            justify-content: center; /* Menyelaraskan item secara horizontal di tengah */
            width: 50%; /* Lebar kotak gambar */
            padding: 20px; /* Padding di sekitar konten dalam kotak gambar */
            background-image: url('assets/images/password.png'); /* Ganti dengan path gambar Anda */
            background-size: cover; /* Mengatur gambar latar belakang agar memenuhi seluruh area kontainer */
            background-repeat: no-repeat; /* Mencegah pengulangan gambar */
            background-position: center center; /* Mengatur posisi gambar agar berada di tengah */
        }

        .image-box img {
            max-width: 100%; /* Membatasi lebar maksimum gambar hingga 100% dari kontainer */
            height: auto; /* Mempertahankan rasio aspek gambar */
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .form-container {
                flex-direction: column; /* Stack form and image on smaller screens */
                align-items: center;
            }

            .form-box {
                width: 100%;
                padding: 20px 10px;
            }

            .image-box {
                width: 100%;
                margin-top: 20px;
            }
        }
    </style>
    </head>
    <body>
    <div class="container">
        <div class="logo">
            <img alt="logo perpusdig" src="assets/logo perpusdig.png"/>
            <span class="blue">Perpus</span>
            <span>Dig</span>
        </div>
        <div class="form-container">
            <div class="form-box">
                <a href="login.php" class="back-link">&lt; Kembali</a>
                <h2>Atur Ulang Kata Sandi</h2>
                <p>Kata sandi Anda sebelumnya telah diatur ulang. Silakan masukkan kata sandi baru untuk akun Anda</p>
                <form id="resetForm">
                    <!-- Input untuk kata sandi baru -->
                    <div class="input-group">
                        <input type="password" id="newPassword" placeholder=" " required>
                        <label for="newPassword">Kata Sandi Baru</label>
                        <span class="toggle-password" onclick="togglePassword('newPassword')">&#128065;</span>
                    </div>

                    <!-- Input untuk konfirmasi kata sandi -->
                    <div class="input-group">
                        <input type="password" id="confirmPassword" placeholder=" " required>
                        <label for="confirmPassword">Konfirmasi Kata Sandi</label>
                        <span class="toggle-password" onclick="togglePassword('confirmPassword')">&#128065;</span>
                    </div>

                    <!-- Tombol untuk menyimpan kata sandi -->
                    <button type="button" onclick="savePassword()">Simpan</button>
                    
                    <!-- Tempat untuk menampilkan pesan feedback -->
                    <div id="feedbackMessage" class="feedback-message"></div>
                </form>
            </div>
            <div class="image-box">
                <!-- <img src="assets/password.png" alt="Padlock Icon"> -->
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

        // Fungsi untuk menyimpan kata sandi
        function savePassword() {
            const newPassword = document.getElementById("newPassword").value; // Ambil nilai input kata sandi baru
            const confirmPassword = document.getElementById("confirmPassword").value; // Ambil nilai input konfirmasi kata sandi
            const feedbackMessage = document.getElementById("feedbackMessage");

            // Validasi untuk memeriksa apakah input kosong
            if (newPassword === "" || confirmPassword === "") {
                feedbackMessage.style.color = "red";
                feedbackMessage.textContent = "Harap isi kedua kolom kata sandi.";
            }
            // Validasi untuk memeriksa apakah kata sandi baru dan konfirmasi kata sandi cocok
            else if (newPassword !== confirmPassword) {
                feedbackMessage.style.color = "red";
                feedbackMessage.textContent = "Kata sandi tidak cocok. Silakan coba lagi.";
            }
            // Jika semua validasi berhasil, tampilkan pesan sukses
            else {
                feedbackMessage.style.color = "green";
                feedbackMessage.textContent = "Kata sandi berhasil disimpan!";
            }
        }
    </script>
</body>
</html>