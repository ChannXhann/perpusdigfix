<?php
session_start();
if (!isset($_SESSION['nip'])) {
    // Jika session tidak ditemukan, redirect ke halaman login
    header("Location: ../../login.php");
    exit;
}
// Pastikan user sudah login
if (!isset($_SESSION['nip']) || empty($_SESSION['nip'])) {
    echo "<script>
            alert('Error: Anda harus login');
            window.location.href = '../../login.php';
          </script>";
    exit;
}

ob_start();
include '../../config/koneksi.php';
ob_end_flush();

// Buat objek database dan koneksi
$db = new Database();
$koneksi = $db->koneksi;

// Mengambil jumlah buku, e-book, anggota dan riwayat
$queryBuku = "SELECT COUNT(*) FROM buku";
$queryEbook = "SELECT COUNT(*) FROM e_book";
$queryAnggota = "SELECT COUNT(*) FROM anggota";
$queryRiwayat = "SELECT COUNT(*) FROM peminjaman WHERE status_peminjaman IN ('Selesai', 'Ditolak', 'Dibaca')";

// Eksekusi query dan ambil hasilnya
$jumlahBuku = $koneksi->query($queryBuku)->fetchColumn();
$jumlahEbook = $koneksi->query($queryEbook)->fetchColumn();
$jumlahAnggota = $koneksi->query($queryAnggota)->fetchColumn();
$jumlahRiwayat = $koneksi->query($queryRiwayat)->fetchColumn();

// Mengambil data peminjaman (tanggal pinjam, judul buku, status peminjaman)
$queryPeminjaman = "
    SELECT 
        peminjaman.tanggal_peminjaman, 
        e_book.judul AS judul_ebook, 
        peminjaman.status_peminjaman 
    FROM peminjaman
    JOIN e_book ON peminjaman.id_ebook = e_book.id_ebook
    WHERE peminjaman.status_peminjaman IN ('Selesai', 'Disetujui', 'Ditunda')
    ORDER BY peminjaman.tanggal_peminjaman DESC 
    LIMIT 10";
$peminjamanData = $koneksi->query($queryPeminjaman)->fetchAll(PDO::FETCH_ASSOC);


// Mengambil data kategori buku yang paling banyak dipinjam
$queryKategori = "
    SELECT kategori, COUNT(*) as jumlah
    FROM peminjaman
    JOIN buku ON peminjaman.id_buku = buku.id_buku
    GROUP BY kategori
    ORDER BY jumlah DESC
    LIMIT 5";

$kategoriData = $koneksi->query($queryKategori)->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PerpusDig - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="header">
        <img src="../../assets/images/logo_perpusdig.png" alt="Library logo">
        <h1>PerpusDig - Sistem Informasi Perpustakaan Daerah Kabupaten Nganjuk</h1>
    </div>
    <div class="container">
        <?php include '../../include/sidebar_admin.php'; ?>
        
        <div class="content">
            <h2>Beranda</h2>
            <div class="stats">
                <div class="stat red">
                    <h3><?php echo $jumlahEbook; ?></h3>
                    <p>E - Book</p>
                </div>
                <div class="stat orange">
                    <h3><?php echo $jumlahBuku; ?></h3>
                    <p>Buku</p>
                </div>
                <div class="stat yellow">
                    <h3><?php echo $jumlahAnggota; ?></h3>
                    <p>Anggota</p>
                </div>
                <div class="stat blue">
                    <h3><?php echo $jumlahRiwayat; ?></h3>
                    <p>Riwayat</p>
                </div>
            </div>
            <div class="main-content">
                <div class="loans">
                    <h3>Peminjaman E - Book</h3>
                    <ul>
                        <?php foreach ($peminjamanData as $data): ?>
                            <li>
                                <span><?php echo $data['tanggal_peminjaman']; ?></span>
                                <span><?php echo $data['judul_ebook']; ?></span>
                                <span style="color: #3498db;">
                                    <?php echo ($data['status_peminjaman'] == 'Dibaca') ? 'Ditunda' : 'Selesai'; ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="categories">
                    <h3>Kategori Buku yang Paling Banyak Dipinjam</h3>
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        var ctx = document.getElementById('categoryChart').getContext('2d');
        var categoryChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($kategoriData, 'kategori')); ?>,
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: <?php echo json_encode(array_column($kategoriData, 'jumlah')); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#ffff', '#00008B' , '#FF1493' ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' peminjaman';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
