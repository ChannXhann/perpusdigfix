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

// Menyertakan file koneksi
include '../../config/koneksi.php';
$db = new Database();
$koneksi = $db->koneksi; // Inisialisasi koneksi PDO
// Cek apakah parameter `id_peminjaman` ada
if (!isset($_GET['id_peminjaman']) || empty($_GET['id_peminjaman'])) {
    die('ID Peminjaman tidak ditemukan.');
}

// Ambil ID Peminjaman dari URL
$id_peminjaman = $_GET['id_peminjaman'];


// Query untuk mendapatkan data peminjaman berdasarkan ID
$sql = "SELECT p.*, a.nama_anggota 
        FROM peminjaman p
        JOIN anggota a ON p.nik_anggota = a.nik_anggota
        WHERE p.id_peminjaman = :id_peminjaman";
$stmt = $koneksi->prepare($sql);
$stmt->bindParam(':id_peminjaman', $id_peminjaman, PDO::PARAM_STR);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);


// Debugging: Cek apakah data ditemukan
if ($stmt->rowCount() == 0) {
    echo "Data tidak ditemukan untuk ID Peminjaman: " . htmlspecialchars($id);
    exit;
}

// Tambahkan validasi untuk $data
if (!$data) {
    echo "Terjadi kesalahan: Data tidak dapat diambil.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PerpusDig - Detail Peminjaman Buku</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <style>
        /* Styles untuk halaman ini, bisa disesuaikan jika diperlukan */
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
            <div class="profile-header">Detail Peminjaman Buku</div>
            <div class="breadcrumb">
                <a href="dashboard_super.php">Beranda</a> / <a href="pengajuan_peminjaman.php">Pengajuan Peminjaman Buku</a> / Detail Peminjaman Buku
            </div>
            <div class="profile-card">
                <div class="form-row">
                    <div class="form-group">
                        <label for="ID">ID Peminjaman</label>
                        <input type="text" id="id" value="<?php echo $data['id_peminjaman']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="NIK">NIK</label>
                        <input type="text" id="nik" value="<?php echo $data['nik_anggota']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" id="nama" value="<?php echo $data['nama_anggota']; ?>" readonly>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="judul_buku">Judul Buku</label>
                        <input type="text" id="judul_buku" value="<?php echo $data['judul']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <input type="text" id="kategori" value="<?php echo $data['kategori']; ?>" readonly>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tgl_pinjam">Tanggal Pinjam</label>
                        <input type="text" id="tgl_pinjam" value="<?= htmlspecialchars($data['tanggal_peminjaman']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="tgl_kembali">Tanggal Kembali</label>
                        <input type="text" id="tgl_kembali" value="<?= htmlspecialchars($data['tanggal_pengembalian']); ?>" readonly>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 20px">
                    <label>Status Peminjaman</label>
                    <span style="color: <?php echo ($data['status_peminjaman'] == 'Disetujui') ? '#27AE60' : '#E74C3C'; ?>;">
                        <?php echo $data['status_peminjaman']; ?>
                    </span>
                </div>
                <div class="buttons">

                <a href="pengajuan_peminjaman.php" class="cancel">Kembali</a>

                </div>
            </div>
        </div>
    </div>
</body>

</html>