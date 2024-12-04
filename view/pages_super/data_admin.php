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
$db = new Database(); // Membuat instance dari class Database
$koneksi = $db->koneksi; // Mengakses properti koneksi dari instance
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PerpusDig - Sistem Informasi Perpustakaan Daerah Kabupaten Nganjuk</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/data_admin.css"/>
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
    </style>
   <script src="../../assets/js/search_admin.js"></script>
</head>

<body>
    <div class="header">
        <img alt="Library logo" src="../../assets/images/logo_perpusdig.png" />
        <h1>PerpusDig - Sistem Informasi Perpustakaan Daerah Kabupaten Nganjuk</h1>
    </div>
    <div class="container">
        <?php include '../../include/sidebar.php'; ?>
        <div class="content">
            <h2>Data Admin</h2>
            <div class="breadcrumb">
                <a href="dashboard_super.php">
                    Beranda
                </a>
                / Data Admin
            </div>
            <div class="maincontainer">
                <div class="top-bar">
                    <button><a href="tambah_data_admin.php">+ TAMBAH DATA</a></button>
                    <input type="text" id="searchInput" placeholder="Cari NIP atau Nama Admin" onkeyup="searchAdmin()">
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>No. Telp</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = $koneksi->prepare("SELECT nip, nama, no_telp, email FROM admin WHERE level !=1");
                        $query->execute();
                        $result = $query->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($result as $row) {
                            echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['nip']}</td>
                                <td>{$row['nama']}</td>
                                <td>{$row['no_telp']}</td>
                                <td>{$row['email']}</td>
                                <td>
                                    <form method='POST' action='../../controller/hapus_admin.php' style='display:inline;'>
                                        <input type='hidden' name='nip' value='{$row['nip']}' />
                                        <button type='submit' class='action-btn' onclick='return confirm(\"Yakin ingin menghapus data ini?\")'>
                                            <i class='fas fa-trash-alt'></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>