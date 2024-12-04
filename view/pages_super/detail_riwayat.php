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
$koneksi = $db->koneksi; // Inisialisasi koneksi PDO
// Cek apakah parameter `id_peminjaman` ada
if (!isset($_GET['id_peminjaman']) || empty($_GET['id_peminjaman'])) {
    die('ID Peminjaman tidak ditemukan.');
}

// Ambil ID Peminjaman dari URL
$id_peminjaman = $_GET['id_peminjaman'];

// Query untuk mengambil detail peminjaman
$query = "
    SELECT 
        peminjaman.id_peminjaman,
        peminjaman.nik_anggota,
        anggota.nama_anggota,
        buku.judul_buku AS judul_buku,
        buku.kategori_buku,
        peminjaman.tanggal_peminjaman AS tgl_pinjam,
        peminjaman.tanggal_pengembalian AS tgl_kembali,
        peminjaman.status_peminjaman
    FROM peminjaman
    JOIN anggota ON peminjaman.nik_anggota = anggota.nik_anggota
    LEFT JOIN buku ON peminjaman.id_buku = buku.id_buku
    WHERE peminjaman.id_peminjaman = :id_peminjaman
";

$stmt = $koneksi->prepare($query);
$stmt->bindParam(':id_peminjaman', $id_peminjaman, PDO::PARAM_STR);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);


// Jika data tidak ditemukan
if (!$data) {
    die('Data peminjaman tidak ditemukan.');
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
        }

        .sidebar .profile {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar .profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
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
        }

        .sidebar .menu {
            list-style: none;
            padding: 0;
        }

        .sidebar .menu li a {
            display: flex;
            align-items: center;
            padding: 10px;
            text-decoration: none;
            color: black;
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
            flex: 1;
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

        .profile-card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            height: 650px;
        }

        .form-row {
            display: flex;
            gap: 40px;
            padding-top: 20px;
        }

        .form-group {
            flex: 1;
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 10px;
        }

        .buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .buttons .cancel {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
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
            <div class="profile-header">Detail Riwayat</div>
            <div class="breadcrumb">
                <a href="dashboard_super.php">Beranda</a> / <a href="history_peminjaman.php">Riwayat Peminjaman Buku</a>
                / Detail Riwayat
            </div>
            <div class="profile-card">
                <div class="form-row">
                    <div class="form-group">
                        <label for="ID">ID</label>
                        <input type="text" id="id" value="<?= htmlspecialchars($data['id_peminjaman']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="NIK">NIK</label>
                        <input type="text" id="nik" value="<?= htmlspecialchars($data['nik_anggota']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" id="nama" value="<?= htmlspecialchars($data['nama_anggota']); ?>" readonly>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="judul_buku">Judul Buku</label>
                        <input type="text" id="judul_buku" value="<?= htmlspecialchars($data['judul_buku']); ?>"
                            readonly>
                    </div>
                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <input type="text" id="kategori" value="<?= htmlspecialchars($data['kategori_buku']); ?>"
                            readonly>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tgl_pinjam">Tanggal Pinjam</label>
                        <input type="text" id="tgl_pinjam" value="<?= htmlspecialchars($data['tgl_pinjam']); ?>"
                            readonly>
                    </div>
                    <div class="form-group">
                        <label for="tgl_kembali">Tanggal Kembali</label>
                        <input type="text" id="tgl_kembali" value="<?= htmlspecialchars($data['tgl_kembali']); ?>"
                            readonly>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 20px">
                    <label>Status Peminjaman</label>
                    <span style="color: 
        <?php
        if ($data['status_peminjaman'] === 'Ditolak') {
            echo 'red';
        } elseif ($data['status_peminjaman'] === 'Selesai') {
            echo 'green';
        } else {
            echo '#6C757D'; // Default warna abu-abu
        }
        ?>;">
                        <?= htmlspecialchars($data['status_peminjaman']); ?>
                    </span>
                </div>

                <div class="buttons">
                    <a class="cancel" href="history_peminjaman.php">Kembali</a>
                </div>

            </div>
        </div>
    </div>
</body>

</html>