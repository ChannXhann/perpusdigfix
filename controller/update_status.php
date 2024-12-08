<?php
require_once '../config/koneksi.php';
$db = new Database();
$koneksi = $db->koneksi;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_peminjaman = $_POST['id_peminjaman'];
    $status = $_POST['status'];

    try {
        // Ambil id_buku dari tabel peminjaman
        $query = $koneksi->prepare("SELECT id_buku FROM peminjaman WHERE id_peminjaman = :id_peminjaman");
        $query->bindParam(':id_peminjaman', $id_peminjaman);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        // Jika peminjaman untuk buku, cek stok
        if ($result && $status === 'Disetujui') {
            $id_buku = $result['id_buku'];

            // Cek stok buku
            $query = $koneksi->prepare("SELECT stok FROM buku WHERE id_buku = :id_buku");
            $query->bindParam(':id_buku', $id_buku);
            $query->execute();
            $buku = $query->fetch(PDO::FETCH_ASSOC);

            // Jika stok = 0, kembalikan ke riwayat_peminjaman.php dengan notifikasi
            if ($buku['stok'] <= 0) {
                echo "<script>
                        alert('Stok buku habis! Tidak dapat menyetujui peminjaman.');
                        window.location.href = '../../view/pages_super/riwayat_peminjaman.php';
                      </script>";
                exit();
            }
        }

        // Mulai transaksional
        $koneksi->beginTransaction();

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

            // Kurangi stok buku
            $query = $koneksi->prepare("UPDATE buku SET stok = stok - 1 WHERE id_buku = :id_buku");
            $query->bindParam(':id_buku', $id_buku);
            $query->execute();
        }

        // Komit transaksi jika berhasil
        $koneksi->commit();
        header("Location: ../../view/pages_super/pengajuan_peminjaman.php");
        exit();
    } catch (Exception $e) {
        // Rollback jika terjadi kesalahan
        $koneksi->rollBack();
        echo "Gagal mengupdate status peminjaman: " . $e->getMessage();
    }
}
?>
