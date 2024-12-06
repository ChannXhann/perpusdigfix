<?php
include '../config/koneksi.php';

$db = new Database();
$koneksi = $db->koneksi;

// Pastikan permintaan menggunakan metode POST dan data nik_anggota tersedia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nik_anggota'])) {
    $nik_anggota = $_POST['nik_anggota'];
    $tgl_pendaftaran = date('Y-m-d'); // Mendapatkan tanggal hari ini

    try {
        // Perbarui status verifikasi dan tanggal pendaftaran
        $stmt = $koneksi->prepare("UPDATE anggota SET status_verifikasi = 'Disetujui', tgl_pendaftaran = ? WHERE nik_anggota = ?");
        $stmt->execute([$tgl_pendaftaran, $nik_anggota]);

        if ($stmt->rowCount() > 0) {
            // Jika berhasil, tampilkan pesan sukses dan hentikan eksekusi
            echo "<script>alert('Status verifikasi berhasil diubah menjadi Disetujui dan tanggal pendaftaran diperbarui.'); window.location.href='../../view/pages_admin/data_anggota.php';</script>";
            exit;
        } else {
            // Jika tidak ada baris yang diperbarui, tampilkan pesan gagal dan hentikan eksekusi
            echo "<script>alert('Gagal mengubah status verifikasi.'); window.history.back();</script>";
            exit;
        }
    } catch (PDOException $e) {
        // Tangani error jika query gagal
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    // Jika akses tidak valid, tampilkan pesan dan hentikan eksekusi
    echo "<script>alert('Akses Tidak Valid'); window.location.href='../../view/pages_admin/data_anggota.php';</script>";
    exit;
}
?>
