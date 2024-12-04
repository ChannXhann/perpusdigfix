<?php
session_start();
// Pastikan user sudah login
if (!isset($_SESSION['nip']) || empty($_SESSION['nip'])) {
    echo "<script>
            alert('Error: Anda harus login');
            window.location.href = '../../login.php';
          </script>";
    exit;
}

include '../../config/koneksi.php';

$db = new Database();
$koneksi = $db->koneksi;

// Periksa apakah nik_anggota diterima melalui POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nik_anggota'])) {
    $nik_anggota = $_POST['nik_anggota'];
    $stmt = $koneksi->prepare("SELECT nik_anggota, nama_anggota, alamat, telp, status_verifikasi, foto_ktp, foto_anggota FROM anggota WHERE nik_anggota = ?");
    $stmt->execute([$nik_anggota]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo "Data anggota tidak ditemukan.";
        exit;
    }
} else {
    echo "<script>window.location.href='../../pages_super/data_anggota.php';</script>";
    exit;
}

// Ubah foto BLOB menjadi format base64 agar bisa ditampilkan sebagai gambar
$foto_base64 = $data['foto_anggota'] ? 'data:image/jpeg;base64,' . base64_encode($data['foto_anggota']) : '../../assets/images/default_photo.png';
$foto_ktp = $data['foto_ktp'] ? 'data:image/jpeg;base64,' . base64_encode($data['foto_ktp']) : '../../assets/images/default_photo.png';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PerpusDig - Profil Anggota</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/dashboard.css">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            height: 100vh;
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
            background-color: #f4f4f4;
        }

        .sidebar {
            width: 250px;
            background-color: white;
            padding: 20px;
            border-right: 1px solid #ccc;
            display: flex;
            flex-direction: column;
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
            border-radius: 10px;
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
            background-color: #f9f9f9;
        }

        .content .profile-header {
            font-size: 24px;
            font-weight: bold;
            color: white;
            background-color: #007bff;
            padding: 15px;
            border-radius: 10px 10px 0 0;
        }

        .content h2 {
            background-color: #007bff;
            color: white;
            padding: 15px;
            border-radius: 0px;
            margin-bottom: 0px;
            height: 20px;
        }

        .breadcrumb {
            font-size: 14px;
            margin-bottom: 10px;
            background-color: #007bff;
            color: white;
            padding: 15px;
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-card .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .profile-card .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .profile-card .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .profile-card .form-group .edit-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #007bff;
            cursor: pointer;
        }

        .profile-card .delete-account {
            color: red;
            display: flex;
            align-items: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .profile-card .delete-account i {
            margin-right: 10px;
        }

        .profile-card .buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .profile-card .buttons button {
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

        .profile-card {
            display: flex;
            align-items: flex-start;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-card .photo-section {
            flex-shrink: 0;
            margin-right: 20px;
            text-align: center;
        }

        .profile-card .photo-section img {
            width: 120px;
            height: 150px;
            border-radius: 10px;
            object-fit: cover;
        }

        .profile-card .photo-section .ktp-btn {
            margin-top: 10px;
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
        }

        .profile-card .info-section {
            flex: 1;
        }

        .profile-card .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .profile-card .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .profile-card .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .profile-card .form-group .edit-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #007bff;
            cursor: pointer;
        }

        .profile-card .buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .profile-card .buttons .cancel {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
        }

        .framefoto {
            background-color: #ffff;
            width: 175px;
            height: 175px;
            margin-top: 20px;
            border: #6c757d;
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

        .breadcrumb a {
            text-decoration: none;
            /* Menghilangkan garis bawah pada link */
            color: inherit;
            /* Menggunakan warna teks yang diwariskan dari elemen induk */
        }

        .breadcrumb a:focus,
        .breadcrumb a:active,
        .breadcrumb a:visited {
            color: inherit;
            /* Menghilangkan perubahan warna setelah link diklik atau dikunjungi */
            outline: none;
            /* Menghilangkan garis biru saat link difokuskan */
        }

        /* Gaya untuk modal */
        .modal {
            display: none;
            /* Secara default modal disembunyikan */
            position: fixed;
            /* Tetap di layar */
            z-index: 1;
            /* Pastikan modal muncul di atas konten lainnya */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            /* Latar belakang hitam transparan */
        }

        /* Gaya untuk konten modal */
        .modal-content {
            position: relative;
            margin: 10% auto;
            padding: 20px;
            background-color: #fff;
            width: 80%;
            max-width: 800px;
            border-radius: 10px;
        }

        /* Gaya untuk gambar di dalam modal */
        #ktpImage {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        /* Tombol untuk menutup modal */
        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #fff;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
        }

        .buttons .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            color: white;
            text-align: center;
            text-decoration: none;
        }

        .buttons .btn-back {
            background-color: #6c757d;
            /* Abu-abu */
        }

        .buttons .btn-verify {
            background-color: #28a745;
            /* Hijau */
        }

        .btn.btn-delete {
            background-color: #dc3545;
            /* Merah */
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn.btn-delete:hover {
            background-color: #c82333;
        }
    </style>

</head>

<body>
    <div class="header">
        <img src="../../assets/images/logo_perpusdig.png" alt="Logo">
        <h1>PerpusDig - Sistem Informasi Perpustakaan Daerah Kabupaten Nganjuk</h1>
    </div>
    <div class="container">
        <?php include '../../include/sidebar.php'; ?>
        <div class="content">
            <div class="profile-header">Profil Anggota</div>
            <div class="breadcrumb">
                <a href="dashboard_super.php">Beranda</a> / <a href="data_anggota.php">Data Anggota</a> / Profil Anggota
            </div>
            <div class="profile-card">
                <div class="framefoto">
                    <div class="photo-section">
                        <!-- Menampilkan foto dari database -->
                        <img src="<?php echo $foto_base64; ?>" alt="Profile Photo">
                        <a href="#" class="ktp-btn">KTP</a>
                    </div>
                </div>
                <div class="info-section">
                    <div class="form-group">
                        <label for="nik">NIK Anggota</label>
                        <input type="text" id="nik" value="<?php echo htmlspecialchars($data['nik_anggota']); ?>"
                            readonly>
                    </div>
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" id="name" value="<?php echo htmlspecialchars($data['nama_anggota']); ?>"
                            readonly>
                    </div>
                    <div class="form-group">
                        <label for="phone">No. Telepon</label>
                        <input type="text" id="phone" value="<?php echo htmlspecialchars($data['telp']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="address">Alamat</label>
                        <input type="text" id="address" value="<?php echo htmlspecialchars($data['alamat']); ?>"
                            readonly>
                    </div>
                    <div class="form-group">
                        <label>Status Verifikasi</label>
                        <span
                            style="color: <?php echo ($data['status_verifikasi'] === 'Disetujui') ? 'green' : 'grey'; ?>;">
                            <?php echo htmlspecialchars($data['status_verifikasi']); ?>
                        </span>
                    </div>
                    <div class="buttons">
                        <a href="data_anggota.php" class="btn btn-back">Kembali</a>
                        <?php if ($data['status_verifikasi'] === 'Ditunda'): ?>
                            <form method="POST" action="../../controller/verifikasi_anggota.php" style="display:inline;">
                                <input type="hidden" name="nik_anggota"
                                    value="<?php echo htmlspecialchars($data['nik_anggota']); ?>">
                                <button type="submit" class="btn btn-verify">Verifikasi</button>
                            </form>

                            <form method="POST" action="../../controller/tolak_anggota.php" style="display:inline;">
                                <input type="hidden" name="nik_anggota"
                                    value="<?php echo htmlspecialchars($data['nik_anggota']); ?>">
                                <button type="submit" class="btn btn-delete"
                                    onclick="return confirm('Yakin ingin menolak akun ini?')">Tolak Akun</button>
                            </form>

                        <?php elseif ($data['status_verifikasi'] !== 'Disetujui'): ?>
                            <form method="POST" action="../../controller/hapus_anggota.php" style="display:inline;">
                                <input type="hidden" name="nik_anggota"
                                    value="<?php echo htmlspecialchars($data['nik_anggota']); ?>">
                                <button type="submit" class="btn btn-delete"
                                    onclick="return confirm('Yakin ingin menghapus akun ini?')">Hapus Akun</button>
                            </form>

                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
<!-- Modal untuk menampilkan foto KTP -->
<div id="ktpModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <img id="ktpImage" src="" alt="KTP Image" />
    </div>
</div>

</html>
<script>
    // Ambil elemen modal dan gambar KTP
    var modal = document.getElementById("ktpModal");
    var ktpImage = document.getElementById("ktpImage");
    var btn = document.querySelector(".ktp-btn");
    var span = document.querySelector(".close");

    // Fungsi untuk menampilkan modal dan gambar
    btn.onclick = function () {
        modal.style.display = "block";
        ktpImage.src = "<?php echo $foto_ktp; ?>"; // Menampilkan gambar KTP
    }

    // Fungsi untuk menutup modal
    span.onclick = function () {
        modal.style.display = "none";
    }

    // Jika pengguna mengklik area di luar modal, tutup modal
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

</script>