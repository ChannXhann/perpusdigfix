<?php
require_once '../config/koneksi.php';
$db = new Database();
$koneksi = $db->koneksi;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_peminjaman = $_POST['id_peminjaman'];
    $status = $_POST['status'];

    try {
        // Ambil data peminjaman untuk mendapatkan id_buku
        $query = $koneksi->prepare("SELECT id_buku FROM peminjaman WHERE id_peminjaman = :id_peminjaman");
        $query->bindParam(':id_peminjaman', $id_peminjaman);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new Exception("Data peminjaman tidak ditemukan.");
        }

        $id_buku = $result['id_buku'];

        // Jika status adalah 'Disetujui', cek stok buku terlebih dahulu
        if ($status === 'Disetujui') {
            $query = $koneksi->prepare("SELECT jumlah_buku FROM buku WHERE id_buku = :id_buku");
            $query->bindParam(':id_buku', $id_buku);
            $query->execute();
            $stok_buku = $query->fetchColumn();

            if ($stok_buku === false || $stok_buku <= 0) {
                // Stok habis, tampilkan alert dan arahkan kembali ke halaman
                echo "<script>
                    alert('Stok buku kosong! Tidak dapat menyetujui peminjaman.');
                    window.location.href = '../../view/pages_super/pengajuan_peminjaman.php';
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

        // Jika status menjadi 'Disetujui', tambahkan tanggal peminjaman dan pengembalian
        if ($status === 'Disetujui') {
            $tanggal_peminjaman = date('Y-m-d');
            $tanggal_pengembalian = date('Y-m-d', strtotime('+7 days'));

            // Update tanggal peminjaman dan pengembalian
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
        header("Location: ../../view/pages_super/pengajuan_peminjaman.php");
        exit();
    } catch (Exception $e) {
        // Rollback jika terjadi kesalahan
        if ($koneksi->inTransaction()) {
            $koneksi->rollBack();
        }
        echo "<script>
            alert('Gagal mengupdate status peminjaman: " . $e->getMessage() . "');
            window.location.href = '../../view/pages_super/pengajuan_peminjaman.php';
            </script>";
        exit();
    }
}
?>
