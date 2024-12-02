<?php
require_once '../config/koneksi.php';
$db = new Database();
$koneksi = $db->koneksi;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_peminjaman = $_POST['id_peminjaman'];
    $status = $_POST['status'];

    // Mulai transaksional
    $koneksi->beginTransaction();

    try {
        // Update status peminjaman
        $query = $koneksi->prepare("UPDATE peminjaman SET status_peminjaman = :status WHERE id_peminjaman = :id_peminjaman");
        $query->bindParam(':status', $status);
        $query->bindParam(':id_peminjaman', $id_peminjaman);
        $query->execute();

        // Jika status menjadi 'Disetujui', tambahkan tanggal_peminjaman dan tanggal_pengembalian
        if ($status === 'Disetujui') {
            $tanggal_peminjaman = date('Y-m-d');
            $tanggal_pengembalian = date('Y-m-d', strtotime('+7 days'));

            $query = $koneksi->prepare("UPDATE peminjaman 
                                        SET tanggal_peminjaman = :tanggal_peminjaman, 
                                            tanggal_pengembalian = :tanggal_pengembalian 
                                        WHERE id_peminjaman = :id_peminjaman");
            $query->bindParam(':tanggal_peminjaman', $tanggal_peminjaman);
            $query->bindParam(':tanggal_pengembalian', $tanggal_pengembalian);
            $query->bindParam(':id_peminjaman', $id_peminjaman);
            $query->execute();
        }

        // Komit transaksi jika berhasil
        $koneksi->commit();
        header("Location: ../../view/pages_admin/pengajuan_peminjaman.php");
        exit();
    } catch (Exception $e) {
        // Rollback jika terjadi kesalahan
        $koneksi->rollBack();
        echo "Gagal mengupdate status peminjaman: " . $e->getMessage();
    }
}
?>
