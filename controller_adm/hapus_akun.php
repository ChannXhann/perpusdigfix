<?php
session_start();

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['nip'])) {
    header("Location: ../../login.php");
    exit;
}

include '../config/koneksi.php';
$db = new Database();
$koneksi = $db->koneksi;

// Ambil NIP dari parameter URL
$nip = isset($_GET['nip']) ? $_GET['nip'] : '';

// Validasi jika parameter nip kosong
if (empty($nip)) {
    echo "NIP tidak valid!";
    exit;
}

// Query untuk menghapus akun admin berdasarkan NIP
$query = "DELETE FROM admin WHERE nip = :nip";
$stmt = $koneksi->prepare($query);
$stmt->bindParam(':nip', $nip, PDO::PARAM_STR);

// Eksekusi query dan periksa apakah penghapusan berhasil
if ($stmt->execute()) {
    // Menghapus sesi admin setelah akun dihapus
    session_unset();
    session_destroy();
    header("Location: ../../login.php");  // Redirect ke halaman login
    exit;
} else {
    echo "Gagal menghapus akun. Silakan coba lagi.";
    exit;
}
?>
