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
$koneksi = $db->koneksi; // Inisialisasi koneksi dari objek Database
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PerpusDig - Sistem Informasi Perpustakaan Daerah Kabupaten Nganjuk</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link href="../../assets/css/data_anggota.css" rel="stylesheet" />
    <script src="../../assets/js/search_anggota.js"></script>
</head>

<body>
    <div class="header">
        <img alt="Library logo" src="../../assets/images/logo_perpusdig.png" />
        <h1>PerpusDig - Sistem Informasi Perpustakaan Daerah Kabupaten Nganjuk</h1>
    </div>
    <div class="container">
        <?php include '../../include/sidebar_admin.php'; ?>
        <div class="content">
            <h2>Data Anggota Perpustakaan</h2>
            <div class="breadcrumb">
                <a href="dashboard_admin.php">Beranda</a> / Data Anggota Perpustakaan
            </div>
            <div class="maincontainer">
                <div class="top-bar">
                    <input type="text" id="searchInput" placeholder="Cari NIK atau Nama Anggota"
                        onkeyup="searchAnggota()">
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>No Telpon</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = $koneksi->prepare("SELECT nik_anggota, nama_anggota, alamat, telp, status_verifikasi FROM anggota");
                        $query->execute();
                        $result = $query->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($result as $row) {
                            // Tentukan warna berdasarkan status
                            $statusColor = '';
                            if ($row['status_verifikasi'] === 'Disetujui') {
                                $statusColor = 'green';
                            } elseif ($row['status_verifikasi'] === 'Ditolak') {
                                $statusColor = 'red';
                            } else { // Status Ditunda atau lainnya
                                $statusColor = 'grey';
                            }

                            echo "<tr>
            <td>{$no}</td>
            <td>{$row['nik_anggota']}</td>
            <td>{$row['nama_anggota']}</td>
            <td>{$row['alamat']}</td>
            <td>{$row['telp']}</td>
            <td style='color: {$statusColor};'>{$row['status_verifikasi']}</td>
            <td>
                <form method='POST' action='lihat_data_anggota.php' style='display:inline;'>
                    <input type='hidden' name='nik_anggota' value='{$row['nik_anggota']}' />
                    <button type='submit' class='action-btn'>
                        <i class='fas fa-eye'></i> <!-- Ikon Mata -->
                    </button>
                </form>
                <form method='POST' action='../../controller_adm/hapus_anggota.php' style='display:inline;'>
                    <input type='hidden' name='nik_anggota' value='{$row['nik_anggota']}' />
                    <button type='submit' class='action-btn' onclick='return confirm(\"Yakin ingin menghapus data ini?\")'>
                        <i class='fas fa-trash-alt'></i> <!-- Ikon Tempat Sampah -->
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