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

require_once '../../config/koneksi.php';

$db = new Database();
$koneksi = $db->koneksi;

$isUpdate = false;
$bookData = [];

// Aktifkan debugging error untuk troubleshooting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ambil data buku berdasarkan ID untuk diisi di form
if (isset($_POST['id_buku'])) {
    $id_buku = $_POST['id_buku'];

    $stmt = $koneksi->prepare("SELECT * FROM buku WHERE id_buku = :id_buku");
    $stmt->bindParam(':id_buku', $id_buku, PDO::PARAM_INT);
    $stmt->execute();

    $bookData = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($bookData) {
        $isUpdate = true;
    } else {
        die("Data buku tidak ditemukan.");
    }
}

// Proses update data buku
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id_buku = $_POST['id_buku'];
    $isbn = trim($_POST['isbn']);
    $judul_buku = trim($_POST['judul_buku']);
    $penulis_buku = trim($_POST['penulis_buku']);
    $penerbit_buku = trim($_POST['penerbit_buku']);
    $tahun_terbit_buku = trim($_POST['tahun_terbit_buku']);
    $deskripsi = trim($_POST['deskripsi']);
    $kategori_buku = trim($_POST['kategori_buku']);
    $jumlah_buku = trim($_POST['jumlah_buku']);
    $sampul_buku = $bookData['sampul_buku']; // Default ke sampul lama

    // Validasi input
    $inputFields = [$isbn, $judul_buku, $penulis_buku, $penerbit_buku, $tahun_terbit_buku, $deskripsi, $kategori_buku, $jumlah_buku];
    foreach ($inputFields as $field) {
        if (empty($field) || strlen(trim($field)) === 0) {
            echo "<script>alert('Kolom input tidak boleh kosong atau hanya berisi spasi.');</script>";
            exit;
        }
    }
    //Validasi ISBN hanya 13 digit
    if (!preg_match('/^\d{13}$/', $isbn)) {
        echo "<script>
               alert('ISBN harus berupa 13 digit angka.');
               window.location.href = edit_buku.php;
             </script>";
        exit;
    }
    // Validasi nama penulis: hanya huruf, spasi, titik, koma, dan petik atas
    if (!preg_match('/^[a-zA-Z\s.,\']+$/', $penulis_buku)) {
        echo "<script>
       alert('Nama penulis hanya boleh berisi huruf, spasi, titik, koma, atau petik atas.');
       window.location.href = edit_buku.php;
     </script>";
        exit;
    }

    // Validasi tahun terbit: hanya angka dan harus kurang dari atau sama dengan tahun sekarang
    $currentYear = date('Y');
    if (!preg_match('/^\d{4}$/', $tahun_terbit_buku) || $tahun_terbit_buku > $currentYear || $tahun_terbit_buku < 1000) {
        echo "<script>
       alert('Tahun terbit harus berupa angka 4 digit dan tidak boleh lebih dari tahun $currentYear, dan masuk akal');
       window.location.href = edit_buku.php;
     </script>";
        exit;
    }

    // Validasi jumlah buku: harus angka positif
    if (!filter_var($jumlah_buku, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
        echo "<script>
       alert('Jumlah buku harus berupa angka positif.');
       window.location.href = edit_buku.php;
     </script>";
        exit;
    }

    if (empty($judul_buku) || empty($isbn) || empty($penulis_buku) || empty($penerbit_buku) || empty($tahun_terbit_buku) || empty($deskripsi) || empty($kategori_buku) || empty($jumlah_buku)) {
        echo "<script>alert('Semua kolom wajib diisi.');</script>";
    } else {
         // Validasi ISBN dan judul buku agar tidak duplikat dengan buku lain
         $stmt = $koneksi->prepare("SELECT COUNT(*) FROM buku WHERE (isbn = :isbn OR judul_buku = :judul_buku) AND id_buku != :id_buku");
         $stmt->bindParam(':isbn', $isbn);
         $stmt->bindParam(':judul_buku', $judul_buku);
         $stmt->bindParam(':id_buku', $id_buku, PDO::PARAM_INT);
         $stmt->execute();
 
         $duplikasi = $stmt->fetchColumn();
 
         if ($duplikasi > 0) {
             echo "<script>
                     alert('Error: ISBN atau Judul Buku sudah digunakan oleh buku lain.');
                     window.location.href = '../../view/pages_super/lihat_buku.php';
                   </script>";
             exit;
         }
        // Jika ada file sampul diupload
        // Periksa jika file sampul diupload
        if (isset($_FILES['sampul_buku']) && !empty($_FILES['sampul_buku']['name'])) {
            if ($_FILES['sampul_buku']['error'] === UPLOAD_ERR_OK) {
                $maxFileSize = 16 * 1024 * 1024; // 16MB
                if ($_FILES['sampul_buku']['size'] > $maxFileSize) {
                    echo "<script>alert('File terlalu besar. Maksimal 16MB.');</script>";
                    exit;
                }

                // Mengambil file sampul sebagai data biner
                $sampul_buku = file_get_contents($_FILES['sampul_buku']['tmp_name']);
            } else {
                echo "<script>alert('Terjadi kesalahan saat mengunggah file. Kode Error: " . $_FILES['sampul_buku']['error'] . "');</script>";
            }
        } else {
            echo "Tidak ada file yang diunggah.<br>"; // Debugging atau log error
        }


        // Update data buku
        $stmt = $koneksi->prepare("UPDATE buku SET 
            isbn = :isbn,
            judul_buku = :judul_buku,
            penulis_buku = :penulis_buku,
            penerbit_buku = :penerbit_buku,
            tahun_terbit_buku = :tahun_terbit_buku,
            deskripsi = :deskripsi,
            kategori_buku = :kategori_buku,
            jumlah_buku = :jumlah_buku,
            sampul_buku = :sampul_buku
            WHERE id_buku = :id_buku");

        $stmt->bindParam(':isbn', $isbn);
        $stmt->bindParam(':judul_buku', $judul_buku);
        $stmt->bindParam(':penulis_buku', $penulis_buku);
        $stmt->bindParam(':penerbit_buku', $penerbit_buku);
        $stmt->bindParam(':tahun_terbit_buku', $tahun_terbit_buku);
        $stmt->bindParam(':deskripsi', $deskripsi);
        $stmt->bindParam(':kategori_buku', $kategori_buku);
        $stmt->bindParam(':jumlah_buku', $jumlah_buku);
        $stmt->bindParam(':sampul_buku', $sampul_buku, PDO::PARAM_LOB);
        $stmt->bindParam(':id_buku', $id_buku, PDO::PARAM_INT);

        try {
            $stmt->execute();
            echo "<script>
                    alert('Data berhasil diperbarui.');
                    window.location.href = 'lihat_buku.php'; // Mengarahkan ke halaman lihat_buku.php
                  </script>";
        } catch (Exception $e) {
            echo "<script>alert('Terjadi kesalahan: " . $e->getMessage() . "');</script>";
        }

    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Buku</title>
    <link rel="stylesheet" href="../../assets/css/tambahbuku.css">
    <script>
        function previewImage(event) {
            const fileInput = event.target; // Elemen file input
            const file = fileInput.files[0]; // Ambil file yang dipilih

            // Validasi file
            const allowedExtensions = /(\.jpg|\.jpeg)$/i;
            const allowedMimeTypes = ['image/jpeg'];

            if (!file) {
                alert('Tidak ada file yang dipilih.');
                return;
            }

            const fileMimeType = file.type;

            if (!allowedExtensions.exec(file.name) || !allowedMimeTypes.includes(fileMimeType)) {
                alert('Hanya file dengan format JPG atau JPEG yang diizinkan.');
                fileInput.value = ''; // Reset file input
                return; // Hentikan eksekusi jika file tidak valid
            }

            // Jika file valid, lakukan pratinjau gambar
            const preview = document.getElementById('sampulPreview');
            const reader = new FileReader();

            reader.onload = function () {
                preview.src = reader.result;
            };
            reader.readAsDataURL(file);
        }

        function validateForm(event) {
            const isbn = document.getElementById('isbn');
            const judul_buku = document.getElementById('judul_buku');
            const penulis_buku = document.getElementById('penulis_buku');
            const penerbit_buku = document.getElementById('penerbit_buku');
            const tahun_terbit_buku = document.getElementById('tahun_terbit_buku');
            const deskripsi = document.getElementById('deskripsi');
            const kategori_buku = document.getElementById('kategori_buku');
            const jumlah_buku = document.getElementById('jumlah_buku');

            //Validasi inputan tidak boleh hanya berisi sepasi
            const inputs = document.querySelectorAll("input[type='text'], input[type='number'], textarea");

            for (const input of inputs) {
                if (!input.value.trim()) {
                    alert("Kolom tidak boleh kosong atau hanya berisi spasi.");
                    input.focus();
                    event.preventDefault();
                    return false;
                }
            }

            // Validasi isian tidak boleh kosong
            if (!isbn.value || !judul_buku.value || !penulis_buku.value || !penerbit_buku.value || !tahun_terbit_buku.value || !deskripsi.value || !kategori_buku.value || !jumlah_buku.value) {
                alert('Semua kolom wajib diisi!');
                event.preventDefault();  // Mencegah pengiriman form jika ada input kosong
                return false;
            }

            // Validasi ISBN harus 13 digit angka
            if (!/^\d{13}$/.test(isbn.value)) {
                alert('ISBN harus terdiri dari 13 digit angka!');
                isbn.value = ''; // Reset input ISBN
                event.preventDefault();
                return false;
            }

            // Validasi jumlah buku harus angka positif
            if (isNaN(jumlah_buku.value) || jumlah_buku.value <= 0) {
                alert('Jumlah buku harus berupa angka positif!');
                jumlah_buku.value = ''; // Reset input jumlah buku
                event.preventDefault();
                return false;
            }

            // Validasi tahun terbit harus 4 digit angka
            if (!/^\d{4}$/.test(tahun_terbit_buku.value)) {
                alert('Tahun terbit harus berupa 4 digit angka!');
                tahun_terbit_buku.value = ''; // Reset input tahun terbit
                event.preventDefault();
                return false;
            }

            // Validasi nama penulis dan penerbit hanya boleh huruf, spasi, titik, atau petik satu
            const validNameRegex = /^[a-zA-Z\s.'-]+$/;

            if (!validNameRegex.test(penulis_buku.value)) {
                alert('Nama penulis hanya boleh mengandung huruf, spasi, titik, atau petik satu.');
                event.preventDefault();
                return false;
            }
            const penerbitRegex = /^[a-zA-Z0-9\s.'’]+$/;
            if (!validpenerbitRegex.test(penerbit_buku.value)) {
                alert('Nama penerbit hanya boleh mengandung huruf, angka, spasi, titik, atau petik satu.');
                event.preventDefault();
                return false;
            }

            // Validasi sampul jika diunggah
            const fileInput = document.getElementById('sampul_buku');
            const file = fileInput.files[0];
            const allowedExtensions = /(\.jpg|\.jpeg)$/i;

            if (file && !allowedExtensions.exec(file.name)) {
                alert('Hanya file dengan format JPG atau JPEG yang diizinkan.');
                fileInput.value = ''; // Reset file input
                event.preventDefault();
                return false;
            }

            if (file && file.size > 16 * 1024 * 1024) {
                alert('File terlalu besar. Maksimal 16MB.');
                fileInput.value = ''; // Reset file input
                event.preventDefault();
                return false;
            }

            return true; // Jika semua validasi berhasil, form akan disubmit
        }
    </script>

</head>

<body>

    <body>
        <div class="header">
            <img alt="Library logo" src="../../assets/images/logo_perpusdig.png" />
            <h1>&#124; PerpusDig - Sistem Informasi Perpustakaan Daerah Kabupaten Nganjuk</h1>
        </div>
        <div class="container">

            <div class="content">
                <div class="breadcrumb">
                    <h2>Edit Data</h2>
                    <a href="dashboard_admin.php">Beranda</a> / <a href="lihat_buku.php">Data Buku</a> / Edit Data Buku
                </div>
                <div class="form-container">
                    <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm(event)">
                        <input type="hidden" name="id_buku" value="<?php echo $bookData['id_buku'] ?? ''; ?>">
                        <div class="form-group">
                            <label for="judul_buku">Judul Buku</label>
                            <input type="text" name="judul_buku" id="judul_buku"
                                value="<?php echo $bookData['judul_buku'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="jumlah_buku">Jumlah Buku</label>
                            <input type="number" name="jumlah_buku" id="jumlah_buku"
                                value="<?php echo $bookData['jumlah_buku'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="isbn">ISBN</label>
                            <input type="text" name="isbn" id="isbn" value="<?php echo $bookData['isbn'] ?? ''; ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="penulis_buku">Penulis Buku</label>
                            <input type="text" name="penulis_buku" id="penulis_buku"
                                value="<?php echo $bookData['penulis_buku'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="tahun_terbit_buku">Tahun Terbit</label>
                            <input type="number" name="tahun_terbit_buku" id="tahun_terbit_buku"
                                value="<?php echo $bookData['tahun_terbit_buku'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="penerbit_buku">Penerbit Buku</label>
                            <input type="text" name="penerbit_buku" id="penerbit_buku"
                                value="<?php echo $bookData['penerbit_buku'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi"
                                required><?php echo $bookData['deskripsi'] ?? ''; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="kategori_buku">Kategori</label>
                            <select name="kategori_buku" id="kategori_buku" required>
                                <option value="Fiksi" <?php echo ($bookData['kategori_buku'] ?? '') === 'Fiksi' ? 'selected' : ''; ?>>
                                    Fiksi</option>
                                <option value="Komik" <?php echo ($bookData['kategori_buku'] ?? '') === 'Komik' ? 'selected' : ''; ?>>
                                    Komik</option>
                                <option value="Biografi & Otobiografi" <?php echo ($bookData['kategori_buku'] ?? '') === 'Biografi & Otobiografi' ? 'selected' : ''; ?>>Biografi & Otobiografi</option>
                                <option value="Bisnis" <?php echo ($bookData['kategori_buku'] ?? '') === 'Bisnis' ? 'selected' : ''; ?>>
                                    Bisnis</option>
                                <option value="Ensiklopedia" <?php echo ($bookData['kategori_buku'] ?? '') === 'Ensiklopedia' ? 'selected' : ''; ?>>
                                    Ensiklopedia</option>
                                <option value="Filsafat" <?php echo ($bookData['kategori_buku'] ?? '') === 'Filsafat' ? 'selected' : ''; ?>>
                                    Filsafat</option>
                                <option value="Hukum dan Politik" <?php echo ($bookData['kategori_buku'] ?? '') === 'Hukum dan Politik' ? 'selected' : ''; ?>>
                                    Hukum dan Politik</option>
                                <option value="Self Improvement" <?php echo ($bookData['kategori_buku'] ?? '') === 'Self Improvement' ? 'selected' : ''; ?>>
                                    Self Improvement</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sampul_buku">Sampul Buku</label>
                            <input type="file" name="sampul_buku" id="sampul_buku" onchange="previewImage(event)">
                            <img id="sampulPreview"
                                src="data:image/jpeg;base64,<?php echo base64_encode($bookData['sampul_buku'] ?? ''); ?>"
                                alt="Preview Sampul" style="width: 150px; height: auto;">
                        </div>
                        <button type="submit" name="update">Perbarui Data</button>
                    </form>
                </div>
            </div>
    </body>

</html>