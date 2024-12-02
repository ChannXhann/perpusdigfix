<?php
// Menyertakan file koneksi
include('../../config/koneksi.php');

// Membuat objek Database
$db = new Database();
$koneksi = $db->koneksi;

// Cek apakah ada parameter 'id_peminjaman' yang dikirimkan melalui URL
if (isset($_GET['id_peminjaman'])) {
    $id = $_GET['id_peminjaman'];
} else {
    // Jika parameter id_peminjaman tidak ada, tampilkan pesan error
    echo "ID Peminjaman tidak ditemukan.";
    exit;
}

// Query untuk mengambil detail peminjaman berdasarkan id_peminjaman
$sql = "SELECT * FROM peminjaman WHERE id_peminjaman = :id";
$stmt = $koneksi->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT); // Bind parameter dengan benar
$stmt->execute();

// Ambil data peminjaman
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika data ditemukan, lakukan redirect ke halaman detail_peminjaman_buku.php dan kirimkan id_peminjaman melalui URL
if ($data) {
    // Mengarahkan ke halaman detail_peminjaman_buku.php dengan query string id_peminjaman
    header("Location: ../../view/pages_super/detail_peminjaman_buku.php?id_peminjaman=" . $data['id_peminjaman']);
    exit;
} else {
    echo "Data peminjaman tidak ditemukan.";
    exit;
}
?>
