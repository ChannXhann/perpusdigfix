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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PerpusDig - Sistem Informasi Perpustakaan Daerah Kabupaten Nganjuk</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .header {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
        }

        .header img {
            width: 40px;
            margin-right: 10px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .sidebar img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            display: block;
            margin: 0 auto;
        }

        .sidebar h3 {
            text-align: center;
            margin: 10px 0;
        }

        .sidebar p {
            text-align: center;
            background-color: #A8CFFB;
            border-radius: 15px;
            padding: 5px 10px;
            display: inline-block;
            margin: 0 auto;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            flex-grow: 1;
            margin-top: 20px;
        }

        .sidebar ul li {
            padding: 10px;
            margin: 15px 0;
            background-color: white;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .sidebar ul li:hover {
            background-color: rgba(168, 207, 251, 0.6);
            border-radius: 20px;
        }

        .sidebar ul li i {
            margin-right: 10px;
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

        .content h2 {
            background-color: #007bff;
            color: white;
            padding: 20px;
            border-radius: 0px;
            margin-bottom: 0px;
        }



        .maincontainer {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table thead tr {
            background-color: #f4f4f4;
            text-align: left;
        }

        table th,
        table td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
            color: white;
            font-size: 0.9em;
        }

        .status.disetujui {
            background-color: #28a745;
        }

        .status.ditolak {
            background-color: #dc3545;
        }

        .status.ditunda {
            background-color: #ffc107;
        }

        .action-btn {
            background-color: transparent;
            border: none;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: background-color 0.3s;
        }

        .action-btn:hover {
            background-color: #f4f4f4;
        }

        .action-btn2 {
            color: #dc3545;
        }

        .action-btn3 {
            background-color: transparent;
            border: none;
            cursor: pointer;
            color: #28a745;
            margin-left: 10px;
        }

        .action-btn3:hover {
            color: #218838;
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

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
        }

        .content {
            padding: 20px;
        }

        .maincontainer {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        /* Gaya status */
        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 0.9em;
            display: inline-block;
            color: #fff;
        }

        .status.disetujui {
            background-color: #d4edda;
            color: #155724;
        }

        .status.ditolak {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status.ditunda {
            background-color: #fff3cd;
            color: #856404;
        }

        /* Gaya tombol */
        .action-btn {
            background-color: transparent;
            border: 1px solid #ddd;
            border-radius: 50%;
            padding: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .action-btn:hover {
            background-color: #f4f4f4;
        }

        .action-btn2 {
            color: #dc3545;
        }

        /* Gaya umum */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
        }

        .content {
            padding: 20px;
        }

        .maincontainer {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        /* Gaya status */
        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 0.9em;
            display: inline-block;
            color: #fff;
        }

        .status.disetujui {
            background-color: #d4edda;
            color: #155724;
        }

        .status.ditolak {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status.ditunda {
            background-color: #fff3cd;
            color: #856404;
        }

        /* Gaya tombol */
        .action-btn {
            background-color: transparent;
            border: 1px solid #ddd;
            border-radius: 50%;
            padding: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .action-btn:hover {
            background-color: #f4f4f4;
        }

        .action-btn2 {
            color: #dc3545;
        }

        .approve-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            cursor: pointer;
        }

        .approve-btn:hover {
            background-color: #218838;
        }

        .maincontainer .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .maincontainer .top-bar button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .maincontainer .top-bar input {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
    <script src="../../assets/js/search_pengajuan_peminjaman.js"></script>
</head>

<body>
    <div class="header">
        <img alt="Library logo" src="../../assets/images/logo_perpusdig.png" />
        <h1>PerpusDig - Sistem Informasi Perpustakaan Daerah Kabupaten Nganjuk</h1>
    </div>
    <div class="container">
        <?php include '../../include/sidebar.php'; ?>
        <div class="content">
            <h2>Pengajuan Peminjaman Buku</h2>
            <div class="breadcrumb">
                <a href="dashboard_super.php">
                    Beranda
                </a>
            </div>
            <div class="maincontainer">
                <div class="top-bar">
                    <input type="text" id="searchInput" placeholder="Cari ID Peminjaman atau Judul Buku"
                        onkeyup="searchPeminjaman()">
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Peminjaman</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Judul Buku</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require_once '../../config/koneksi.php';
                        $db = new Database();
                        $koneksi = $db->koneksi;

                        // Menambahkan kondisi WHERE untuk tidak menampilkan data dengan status "Selesai"
                        $query = $koneksi->prepare("SELECT peminjaman.id_peminjaman, peminjaman.kategori, peminjaman.nik_anggota, anggota.nama_anggota, peminjaman.judul, peminjaman.status_peminjaman 
                        FROM peminjaman 
                        JOIN anggota ON peminjaman.nik_anggota = anggota.nik_anggota
                        WHERE peminjaman.status_peminjaman != 'Selesai'
                        AND peminjaman.status_peminjaman != 'Ditolak'
                        ORDER BY peminjaman.id_peminjaman ASC");
                        $query->execute();
                        $result = $query->fetchAll(PDO::FETCH_ASSOC);

                        $query->execute();
                        $result = $query->fetchAll(PDO::FETCH_ASSOC);

                        $no = 1;
                        foreach ($result as $row) {
                            $statusClass = strtolower($row['status_peminjaman']);
                            echo "<tr>
                <td>{$no}</td>
                <td>{$row['id_peminjaman']}</td>
                <td>{$row['nik_anggota']}</td>
                <td>{$row['nama_anggota']}</td>
                <td>{$row['judul']}</td>
                <td>{$row['kategori']}</td>
                <td>
                    <span class='status {$statusClass}'>{$row['status_peminjaman']}</span>";

                            // Menambahkan tombol "Setujui" jika status bukan "Disetujui"
                            if ($row['status_peminjaman'] === 'Ditunda') {
                                echo "<form method='POST' action='../../controller/update_status.php' style='display:inline;'>
                    <input type='hidden' name='id_peminjaman' value='{$row['id_peminjaman']}' />
                    <input type='hidden' name='status' value='Disetujui' />
                    <button type='submit' class='approve-btn'>Setujui</button>
                  </form>
                  <form method='POST' action='../../controller/update_status.php' style='display:inline;'>
                    <input type='hidden' name='id_peminjaman' value='{$row['id_peminjaman']}' />
                    <input type='hidden' name='status' value='Ditolak' />
                    <button type='submit' class='approve-btn' style='background-color: #dc3545;'>Tolak</button>
                  </form>";
                            }

                            // Menambahkan tombol "Selesai" jika status sudah "Disetujui"
                            if ($row['status_peminjaman'] === 'Disetujui') {
                                echo "<form method='POST' action='../../controller/update_status.php' style='display:inline;'>
                    <input type='hidden' name='id_peminjaman' value='{$row['id_peminjaman']}' />
                    <input type='hidden' name='status' value='Selesai' />
                    <button type='submit' class='approve-btn' style='background-color: #007bff;'>Selesai</button>
                  </form>";
                            }

                            echo "</td>
                <td>
                <form method='GET' action='detail_peminjaman_buku.php' style='display:inline;'>
                                        <input type='hidden' name='id_peminjaman' value='{$row['id_peminjaman']}' />
                                        <button type='submit' class='action-btn'><i class='fas fa-eye'></i></button>
                                    </form> 
                    <form method='POST' action='../../controller/hapus_peminjaman.php' style='display:inline;' onsubmit='return confirm(\"Yakin ingin menghapus data ini?\")'>
                        <input type='hidden' name='id_peminjaman' value='{$row['id_peminjaman']}' />
                        <button type='submit' class='action-btn action-btn2'><i class='fas fa-trash'></i></button>
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
</body>

</html>