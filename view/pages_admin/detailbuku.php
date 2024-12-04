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

// Konfigurasi database
$host = '127.0.0.1:3306';
$db_name = 'u137138991_perpusdig';
$user = 'u137138991_root1';
$password = 'Adminperpusdig123';

// Koneksi ke database
$conn = new mysqli($host, $user, $password, $db_name);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil ID buku dari parameter URL
$id_buku = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// Validasi ID buku
if ($id_buku <= 0) {
    echo "ID buku tidak valid atau tidak ditemukan.";
    exit;
}

// Query untuk mengambil data buku berdasarkan id_buku
$sql = "SELECT 
            judul_buku AS judul, 
            deskripsi, 
            sampul_buku, 
            kategori_buku AS kategori, 
            penulis_buku AS penulis, 
            penerbit_buku AS penerbit, 
            tahun_terbit_buku AS tahun_terbit 
        FROM buku 
        WHERE id_buku = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Query gagal dipersiapkan: " . $conn->error);
}
$stmt->bind_param("i", $id_buku);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah data ditemukan
$dataBuku = $result->fetch_assoc();
if (!$dataBuku) {
    echo "Data buku tidak ditemukan untuk ID: $id_buku.";
    exit;
}

// Membatasi deskripsi awal menjadi 150 karakter
$shortDeskripsi = substr($dataBuku['deskripsi'], 0, 150) . 
    (strlen($dataBuku['deskripsi']) > 150 ? "..." : "");

// Konversi BLOB ke Base64 untuk sampul buku
$sampulBase64 = $dataBuku['sampul_buku'] 
    ? 'data:image/jpeg;base64,' . base64_encode($dataBuku['sampul_buku']) 
    : '../../assets/default_cover.png';

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PerpusDig - Detail Buku</title>
    <link rel="stylesheet" href="../../assets/css/lihatbuku.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
</head>

<body>
    <div class="header">
        <img alt="Library logo" src="../../assets/images/logo_perpusdig.png" />
        <h1>PerpusDig - Sistem Informasi Perpustakaan Daerah Kabupaten Nganjuk</h1>
    </div>
    <div class="container">
        <?php include '../../include/sidebar_admin.php'; ?>
        <div class="content">
            <h2>Detail Buku</h2>
            <div class="breadcrumb">
                <a href="dashboard_admin.php">Beranda</a> / Data Buku / Detail Buku
            </div>
            <div class="maincontainer">
                <img alt="Cover Buku" height="300" src="<?php echo $sampulBase64; ?>" width="200" />
                <h2><?php echo htmlspecialchars($dataBuku['judul']); ?></h2>
                <button class="badge"><?php echo htmlspecialchars($dataBuku['kategori']); ?></button>
                <div class="info">
                    <div class="info-item"><span>Penulis</span><span>: <?php echo htmlspecialchars($dataBuku['penulis']); ?></span></div>
                    <div class="info-item"><span>Penerbit</span><span>: <?php echo htmlspecialchars($dataBuku['penerbit']); ?></span></div>
                    <div class="info-item"><span>Tahun Terbit</span><span>: <?php echo htmlspecialchars($dataBuku['tahun_terbit']); ?></span></div>
                </div>

                <div class="description">
                    <h3>Deskripsi Buku</h3>
                    <textarea id="deskripsi" rows="10" cols="50" readonly><?php echo htmlspecialchars($shortDeskripsi); ?></textarea>
                    <a id="readMore" class="read-more" href="#">Baca Selengkapnya</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const readMoreButton = document.getElementById('readMore');
        const deskripsiTextarea = document.getElementById('deskripsi');

        readMoreButton.addEventListener('click', (e) => {
            e.preventDefault();
            deskripsiTextarea.value = <?php echo json_encode($dataBuku['deskripsi']); ?>;
            readMoreButton.style.display = 'none';
        });
    </script>
</body>

</html>
