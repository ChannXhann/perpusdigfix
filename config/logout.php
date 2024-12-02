<?php
// Mulai session
session_start();

// Simpan URL halaman terakhir
$_SESSION['last_page'] = $_SERVER['HTTP_REFERER'] ?? '../../view/pages_super/dashboard.php';  // Default ke dashboard jika HTTP_REFERER tidak ada

// Cek apakah pengguna sudah mengonfirmasi logout
if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    // Hancurkan semua data session
    session_unset();
    session_destroy();

    // Redirect ke halaman login
    header("Location: ../../login.php");
    exit;
}

// Jika tidak ada konfirmasi logout, tampilkan pesan konfirmasi
echo '<script>
        if (confirm("Apakah Anda yakin ingin keluar?")) {
            window.location.href = "?confirm=yes";
        } else {
            window.location.href = "' . $_SESSION['last_page'] . '";  // Redirect ke halaman terakhir
        }
      </script>';
?>
