<style>
.alert-warning {
    background-color: #ffcc00;
    color: #000;
    padding: 10px;
    margin: 15px 0;
    border-radius: 5px;
    font-size: 14px;
    border-left: 5px solid #ff9900;
}

.alert-warning .btn-warning {
    display: inline-block;
    margin-top: 10px;
    padding: 5px 10px;
    background-color: #ff9900;
    color: #fff;
    text-decoration: none;
    border-radius: 3px;
    font-size: 12px;
}

.alert-warning .btn-warning:hover {
    background-color: #cc7a00;
}
</style>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ambil data dari session
$nama = isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : null;
$foto = isset($_SESSION['foto']) && !empty($_SESSION['foto'])
    ? 'data:image/jpeg;base64,' . base64_encode($_SESSION['foto'])
    : '../../assets/images/profil.png';
$no_telp = isset($_SESSION['no_telp']) ? htmlspecialchars($_SESSION['no_telp']) : null;
?>

<div class="sidebar">
    <a href="../../view/pages_super/profil.php">
        <img src="<?php echo $foto; ?>" alt="User profile picture" style="width: 100px; border-radius: 50%;">
    </a>
    <h3><?php echo $nama ?: "Nama belum diisi"; ?></h3>
    <p>Super Admin</p>
    
    <!-- Pesan peringatan -->
    <?php if (empty($nama) || empty($_SESSION['foto']) || empty($no_telp)): ?>
        <div class="alert-warning">
            <p>Profil Anda belum lengkap! Harap lengkapi:</p>
            <a href="../../view/pages_super/profil.php" class="btn-warning">Lengkapi Profil</a>
        </div>
    <?php endif; ?>

    <ul>
        <li><a href="../../view/pages_super/dashboard_super.php"><i class="fas fa-home"></i> Beranda</a></li>
        <li><a href="../../view/pages_super/Ebook.php"><i class="fas fa-book"></i> E-Book</a></li>
        <li><a href="../../view/pages_super/lihat_buku.php"><i class="fas fa-book-open"></i> Buku</a></li>
        <li><a href="../../view/pages_super/data_anggota.php"><i class="fas fa-users"></i> Data Anggota</a></li>
        <li><a href="../../view/pages_super/data_admin.php"><i class="fas fa-user-shield"></i> Data Admin</a></li>
        <li><a href="../../view/pages_super/pengajuan_peminjaman.php"><i class="fas fa-file-alt"></i> Pengajuan Peminjaman</a></li>
        <li><a href="../../view/pages_super/history_peminjaman.php"><i class="fas fa-history"></i> Riwayat Peminjaman</a></li>
        <li><a href="../../config/logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
    </ul>
</div>


