<?php
session_start();
if (!isset($_SESSION['nip'])) {
    // Jika session tidak ditemukan, redirect ke halaman login
    header("Location: ../../login.php");
    exit;
}

include '../../config/koneksi.php';  // Koneksi menggunakan PDO
$db = new Database();
$koneksi = $db->koneksi;

// Ambil NIP dari session
$nip = $_SESSION['nip'];

// Query untuk mengambil data admin berdasarkan NIP
$query = "SELECT nip, email, nama, no_telp, foto FROM admin WHERE nip = :nip";
$stmt = $koneksi->prepare($query);
$stmt->bindParam(':nip', $nip, PDO::PARAM_STR);
$stmt->execute();

// Ambil hasilnya
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika data ditemukan, perbarui data di sesi
if ($result) {
    $nip_db = $result['nip'];
    $email = $result['email'];
    $nama = $result['nama'];
    $no_telp = $result['no_telp'];

    // Periksa apakah foto ada, jika tidak gunakan foto default
    $foto = $result['foto']
        ? 'data:image/jpeg;base64,' . base64_encode($result['foto'])
        : '../../assets/images/profil.png';

    // Perbarui sesi dengan data terbaru (opsional untuk sinkronisasi)
    $_SESSION['foto'] = $result['foto'];
} else {
    echo "Data tidak ditemukan!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PerpusDig - Profil</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/data_admin.css" />
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
        }

        .header img {
            height: 40px;
            margin-right: 10px;
        }

        .header h1 {
            font-size: 20px;
            margin: 0;
        }

        .container {
            display: flex;
            flex: 1;
            background-color: white;
        }

        .sidebar {
            width: 250px;
            background-color: white;
            padding: 20px;
            color: black;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #ccc;
        }

        .sidebar .profile {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar .profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #ccc;
            display: block;
            margin: 0 auto;
        }

        .sidebar .profile h2 {
            font-size: 18px;
            margin: 10px 0 5px;
        }

        .sidebar .profile .role {
            background-color: #A8CFFB;
            color: black;
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
        }

        .sidebar .menu {
            list-style: none;
            padding: 0;
            flex-grow: 1;
        }

        .sidebar .menu li {
            margin: 10px 0;
        }

        .sidebar .menu li a {
            color: black;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 20px;
            transition: background-color 0.3s;
        }

        .sidebar .menu li a:hover {
            background-color: rgba(168, 207, 251, 0.6);
        }

        .sidebar .menu li a i {
            margin-right: 10px;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            background-color: white;
            overflow-y: auto;
        }

        .content .profile-header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 0px;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 0px;
        }

        .content .breadcrumb {
            font-size: 14px;
            margin-bottom: 10px;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 0px;
            margin-top: 0px;
        }

        .content .breadcrumb a {
            color: white;
            text-decoration: none;
        }

        .content .profile-card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            color: black;
            display: flex;
            flex-direction: column;
            height: calc(100% - 60px);
        }

        .content .profile-card .profile-pic {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }

        .content .profile-card .profile-pic img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #ccc;
            display: block;
            margin: 0 auto;
        }

        .content .profile-card .profile-pic .camera-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: #28a745;
            color: white;
            border-radius: 50%;
            padding: 5px;
            transform: translate(50%, 50%);
        }

        .content .profile-card .form-group {
            margin-bottom: 30px;
            position: relative;
        }

        .content .profile-card .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .content .profile-card .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .content .profile-card .form-group .edit-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #007bff;
            cursor: pointer;
        }

        .content .profile-card .form-row {
            display: flex;
            justify-content: space-between;
        }

        .content .profile-card .form-row .form-group {
            flex-basis: 48%;
        }

        .content .profile-card .delete-account {
            color: red;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .content .profile-card .delete-account i {
            margin-right: 10px;
        }

        .content .profile-card .delete-account input {
            flex-basis: 75%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-left: 10px;
            width: calc(100% - 40px);
        }

        .content .profile-card .delete-account .fa-trash-alt {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: red;
            cursor: pointer;
        }

        .content .profile-card .buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: auto;
        }

        .content .profile-card .buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            margin-left: 10px;
            cursor: pointer;
        }

        .content .profile-card .buttons .cancel {
            background-color: #6c757d;
            color: white;
        }

        .content .profile-card .buttons .save {
            background-color: #0F78CB;
            color: white;
        }

        .sidebar ul li a {
            color: inherit;
            text-decoration: none;
        }

        .sidebar ul li a:focus,
        .sidebar ul li a:active {
            outline: none;
            box-shadow: none;
        }

        .error {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }

        .form-group input:read-only {
            background-color: #f0f0f0;
        }

        .edit-icon {
            cursor: pointer;
            margin-left: 10px;
        }

        .camera-icon {
            cursor: pointer;
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: #28a745;
            color: white;
            border-radius: 50%;
            padding: 5px;
            transform: translate(50%, 50%);
        }

        /* Pop-up modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 25px;
            color: black;
        }

        .close:hover,
        .close:focus {
            color: red;
            text-decoration: none;
            cursor: pointer;
        }

        /* Styling tombol edit dan batal */
        .buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: auto;
        }

        .buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            margin-left: 10px;
            cursor: pointer;
        }

        .buttons .cancel {
            background-color: #6c757d;
            color: white;
        }

        .buttons .save {
            background-color: #0F78CB;
            color: white;
        }
    </style>
</head>

<body>
    <div class="header">
        <img alt="Logo" height="40" src="../../assets/images/logo_perpusdig.png" width="40" />
        <h1>PerpusDig - Sistem Informasi Perpustakaan Daerah Kabupaten Nganjuk</h1>
    </div>
    <div class="container">
        <?php include '../../include/sidebar.php'; ?>
        <div class="content">
            <div class="profile-header">Profil</div>
            <div class="breadcrumb">
                <a href="dashboard_super.php">Beranda</a> / Profil
            </div>
            <div class="profile-card">
                <div class="profile-pic">
                    <!-- Tampilkan foto langsung dari variabel $foto -->
                    <img alt="Profile Picture" height="100" src="<?php echo $foto; ?>" width="100" />
                    <div class="camera-icon" onclick="openModal()">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nip">NIP</label>
                        <input id="nip" readonly type="text" value="<?php echo htmlspecialchars($nip_db); ?>" />
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" readonly type="text" value="<?php echo htmlspecialchars($email); ?>" />
                        <i class="fas fa-pencil-alt edit-icon" onclick="enableEditing('email')"></i>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input id="name" readonly type="text" value="<?php echo htmlspecialchars($nama); ?>" />
                        <i class="fas fa-pencil-alt edit-icon" onclick="enableEditing('name')"></i>
                    </div>
                    <div class="form-group">
                        <label for="phone">No. Telp</label>
                        <input id="phone" readonly type="text" value="<?php echo htmlspecialchars($no_telp); ?>" />
                        <i class="fas fa-pencil-alt edit-icon" onclick="enableEditing('phone')"></i>
                    </div>
                </div>
                <div class="buttons">
                    <button class="cancel" onclick="resetForm()">Batal</button>
                    <button class="save" onclick="validateForm()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Upload Foto -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Upload Foto Profil</h3>
            <form id="uploadPhotoForm" action="../../controller/update_profil.php" method="post"
                enctype="multipart/form-data">
                <input type="file" name="foto" id="photoInput" accept="image/*" />
                <button type="submit" class="save">Upload</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById("myModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }
    </script>

    <script>
        function enableEditing(field) {
            var input = document.getElementById(field);
            input.readOnly = false;
        }

        function validateForm() {
            var email = document.getElementById('email').value.trim();
            var name = document.getElementById('name').value.trim();
            var phone = document.getElementById('phone').value.trim();

            // Validasi kosong
            if (!email || !name || !phone) {
                alert('Semua data harus diisi!');
                return;
            }
            
            // Validasi format email (harus menggunakan @gmail.com dan tidak mengandung karakter khusus, spasi)
            var emailRegex = /^[a-zA-Z0-9]+@gmail\.com$/;
            if (!emailRegex.test(email)) {
                alert('Email harus menggunakan domain @gmail.com dan hanya boleh terdiri dari huruf dan angka.');
                return;
            }

            var nameRegex = /^[a-zA-Z\s']+$/;
            if (!nameRegex.test(name)) {
                alert("Nama hanya boleh mengandung huruf, spasi, dan tanda petik tunggal (').");
                return;
            }
            // Validasi nomor telepon (harus 11-13 digit)
            if (phone.length < 11 || phone.length > 13 || !/^\d+$/.test(phone)) {
                alert('Nomor telepon harus berupa angka dan memiliki panjang 11 hingga 13 digit.');
                return;
            }

            var formData = new FormData();
            formData.append('email', email);
            formData.append('name', name);
            formData.append('phone', phone);

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../../controller/update_profil.php", true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText); // Parse JSON response
                    if (response.status === 'error') {
                        // Tampilkan notifikasi jika email sudah terdaftar
                        alert(response.message);
                    } else if (response.status === 'success') {
                        // Tampilkan notifikasi sukses jika profil berhasil diperbarui
                        alert(response.message);
                        refreshPage();
                    }
                } else {
                    alert("Terjadi kesalahan dalam proses pembaruan profil.");
                }
            };
            xhr.send(formData);
        }

        function refreshPage() {
            location.reload();
        }



        document.getElementById("uploadPhotoForm").addEventListener("submit", function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            var xhr = new XMLHttpRequest();
            xhr.open("POST", this.action, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    alert('Foto berhasil diupload!');
                    refreshPage();
                } else {
                    alert('Gagal mengunggah foto.');
                }
            };
            xhr.send(formData);
        });

        function openSuccessModal() {
            document.getElementById("successModal").style.display = "block";
        }

        function closeSuccessModal() {
            document.getElementById("successModal").style.display = "none";
        }

        function refreshPage() {
            location.reload();
        }
    </script>
</body>

</html>