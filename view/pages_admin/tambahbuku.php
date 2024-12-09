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

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Pastikan user sudah login
if (!isset($_SESSION['nip']) || empty($_SESSION['nip'])) {
    die("Error: Anda harus login untuk menambahkan buku.");
}

$nip = $_SESSION['nip'];

// Periksa apakah `nip` valid di tabel `admin`
$check_admin = $conn->prepare("SELECT nip FROM admin WHERE nip = :nip");
$check_admin->bindParam(':nip', $nip);
$check_admin->execute();

if ($check_admin->rowCount() === 0) {
    die("Error: nip tidak valid atau tidak ditemukan di tabel admin.");
}

// Proses tambah data buku
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form dan sanitasi
    $isbn = htmlspecialchars($_POST['isbn']);
    $judul_buku = htmlspecialchars($_POST['judul_buku']);
    $penulis_buku = htmlspecialchars($_POST['penulis_buku']);
    $penerbit_buku = htmlspecialchars($_POST['penerbit_buku']);
    $tahun_terbit_buku = htmlspecialchars($_POST['tahun_terbit_buku']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);
    $kategori_buku = htmlspecialchars($_POST['kategori_buku']);
    $jumlah_buku = (int) htmlspecialchars($_POST['jumlah_buku']);

    // Validasi input tidak boleh kosong atau hanya berisi spasi
    if (
        empty(trim($isbn)) || empty(trim($judul_buku)) || empty(trim($penulis_buku)) || empty(trim($penerbit_buku)) ||
        empty(trim($tahun_terbit_buku)) || !preg_match("/^\d{4}$/", $tahun_terbit_buku) || empty(trim($deskripsi)) ||
        empty(trim($kategori_buku)) || $jumlah_buku <= 0
    ) {
        echo "<script>
           alert('Semua isian harus diisi dan tidak boleh hanya berisi spasi!');
           window.location.href = 'tambahbuku.php';
         </script>";
        exit;
    }

     // Validasi nama pengarang: hanya huruf, spasi, titik, atau koma
     if (!preg_match("/^[a-zA-Z\s.,']+$/", $penulis_buku)) {
        echo "<script>
           alert('Nama pengarang hanya boleh berisi huruf, spasi, titik, atau koma!');
           window.location.href = 'tambahbuku.php';
         </script>";
        exit;
    }

    // Validasi ISBN: harus 13 digit angka
    if (!preg_match("/^\d{13}$/", $isbn)) {
        echo "<script>
           alert('ISBN harus terdiri dari 13 digit angka!');
           window.location.href = 'tambahbuku.php';
         </script>";
        exit;
    }

    // Validasi tahun terbit: harus 4 digit angka
    if (!preg_match("/^\d{4}$/", $tahun_terbit_buku) || $tahun_terbit_buku > date('Y') || $tahun_terbit_buku < 1000) {
        echo "<script>
           alert('Tahun terbit harus berupa 4 digit angka! Dan Melebihi tahun saat ini dan masuk akal!');
           window.location.href = 'tambahbuku.php';
         </script>";
        exit;
    }

    //cek duplikasi buku berdasarkan database
    $check_duplicate = $conn->prepare(
        "SELECT * FROM buku WHERE isbn = :isbn OR judul_buku = :judul_buku"
    );
    $check_duplicate->bindParam(':isbn', $isbn);
    $check_duplicate->bindParam(':judul_buku', $judul_buku);
    $check_duplicate->execute();

    if ($check_duplicate->rowCount() > 0) {
        echo "<script>
                alert('ISBN atau Judul Buku sudah ada di database.');
                window.location.href = 'lihat_buku.php';
              </script>";
        exit;
    }
    // Proses unggah file sampul
    $sampul_buku = null; // Nilai default untuk BLOB
    if (isset($_FILES['sampul_buku']) && $_FILES['sampul_buku']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/jpg']; // Tipe file yang diizinkan
        $file_type = $_FILES['sampul_buku']['type'];
        $file_size = $_FILES['sampul_buku']['size'];

        // Maksimum ukuran file 2MB
        if (!in_array($file_type, $allowed_types)) {
            echo "<p style='color: red;'>Hanya file JPG yang diizinkan.</p>";
        } elseif ($file_size > 2 * 1024 * 1024) {
            echo "<p style='color: red;'>Ukuran file terlalu besar. Maksimum 2MB.</p>";
        } else {
            // Baca file sebagai data biner
            $sampul_buku = file_get_contents($_FILES['sampul_buku']['tmp_name']);
        }
    }

    try {
        // Simpan data ke database
        $stmt = $conn->prepare(
            "INSERT INTO buku (isbn, judul_buku, penulis_buku, penerbit_buku, tahun_terbit_buku, deskripsi, kategori_buku, jumlah_buku, sampul_buku, nip)
            VALUES (:isbn, :judul_buku, :penulis_buku, :penerbit_buku, :tahun_terbit_buku, :deskripsi, :kategori_buku, :jumlah_buku, :sampul_buku, :nip)"
        );

        $stmt->bindParam(':isbn', $isbn);
        $stmt->bindParam(':judul_buku', $judul_buku);
        $stmt->bindParam(':penulis_buku', $penulis_buku);
        $stmt->bindParam(':penerbit_buku', $penerbit_buku);
        $stmt->bindParam(':tahun_terbit_buku', $tahun_terbit_buku);
        $stmt->bindParam(':deskripsi', $deskripsi);
        $stmt->bindParam(':kategori_buku', $kategori_buku);
        $stmt->bindParam(':jumlah_buku', $jumlah_buku);
        $stmt->bindParam(':sampul_buku', $sampul_buku, PDO::PARAM_LOB); // Gunakan PARAM_LOB untuk data biner
        $stmt->bindParam(':nip', $nip);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Buku berhasil ditambahkan.');
                    window.location.href = 'lihat_buku.php';
                  </script>";
        } else {
            echo "<p style='color: red;'>Gagal menambahkan buku.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Terjadi kesalahan: " . $e->getMessage() . "</p>";
    }
}
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Buku</title>
    <link rel="stylesheet" href="../../assets/css/tambahbuku.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

</head>
<script>
    // Fungsi untuk memvalidasi input jumlah buku
    function validateJumlah() {
        const jumlahBuku = document.getElementById("jumlah_buku");
        // Pastikan input hanya angka positif
        if (isNaN(jumlahBuku.value) || jumlahBuku.value < 0) {
            alert('Jumlah buku harus berupa angka positif!');
            jumlahBuku.value = ''; // Reset input
        }
    }

    // Fungsi untuk validasi ISBN
    function validateISBN() {
        const isbn = document.getElementById("isbn");
        // Pastikan ISBN adalah 13 digit angka
        if (!/^\d{13}$/.test(isbn.value)) {
            alert('ISBN harus terdiri dari 13 digit angka!');
            isbn.value = ''; // Reset input
        }
    }

    // Fungsi untuk menangani validasi form
    function validateForm(event) {
        const isbn = document.getElementById('isbn').value.trim();
        const judul_buku = document.getElementById('judul_buku').value.trim();
        const penulis_buku = document.getElementById('penulis_buku').value.trim();
        const penerbit_buku = document.getElementById('penerbit_buku').value.trim();
        const tahun_terbit_buku = document.getElementById('tahun_terbit_buku').value.trim();
        const deskripsi = document.getElementById('deskripsi').value.trim();
        const kategori_buku = document.getElementById('kategori_buku').value.trim();
        const jumlah_buku = document.getElementById('jumlah_buku').value.trim();

        // Validasi isian tidak boleh kosong atau hanya berisi spasi
        if (!isbn || !judul_buku || !penulis_buku || !penerbit_buku || !tahun_terbit_buku || !deskripsi || !kategori_buku || !jumlah_buku) {
            alert('Semua isian harus diisi dan tidak boleh hanya berisi spasi!');
            event.preventDefault();
            return false;
        }

        // Validasi isian tidak boleh kosong
        if (!isbn.value || !judul_buku.value || !penulis_buku.value || !penerbit_buku.value || !tahun_terbit_buku.value || !deskripsi.value || !kategori_buku.value || !jumlah_buku.value) {
            alert('Semua isian harus diisi!');
            event.preventDefault();
            return false;
        }

        // Validasi jumlah harus angka positif
        if (isNaN(jumlah_buku.value) || jumlah_buku.value <= 0) {
            alert('Jumlah harus berupa angka positif!');
            event.preventDefault();
            return false;
        }

        // Validasi tahun terbit harus 4 digit angka
        if (!/^\d{4}$/.test(tahun_terbit_buku.value)) {
            alert('Tahun terbit harus berupa 4 digit angka!');
            event.preventDefault();
            return false;
        }

        return true;
    }
</script>

<body>
    <div class="header">
        <img alt="Library logo" src="../../assets/images/logo_perpusdig.png" />
        <h1>&#124; PerpusDig - Sistem Informasi Perpustakaan Daerah Kabupaten Nganjuk</h1>
    </div>
    <div class="container">
        <?php include '../../include/sidebar_admin.php'; ?>
        <div class="content">
            <div class="breadcrumb">
                <h2>Tambah Data</h2>
                <a href="dashboard_admin.php">Beranda</a> / <a href="lihat_buku.php">Data Buku</a> / Tambah Data Buku
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-container">
                    <!-- <div class="top-form-container">
                        <label for="file-upload" class="btn-upload">
                            <i class="fas fa-upload"></i> Unggah E - Book
                        </label>
                        <input type="file" id="file-upload" style="display: none;">
                        <div id="file-info" class="file-info">
                            <span id="file-name">No file selected</span> |
                            <span id="file-size">0 KB</span>
                        </div>
                    </div> -->
                    <div class="main-form-content">
                        <div class="form-group-left">
                            <div class="from-judul">
                                <label for="judul_buku">Judul</label>
                                <input type="text" id="judul_buku" name="judul_buku" placeholder="Masukkan Judul Buku"
                                    required>
                            </div>
                            <div class="from-jumlah">
                                <label for="jumlah_buku">Jumlah</label>
                                <input type="number" id="jumlah_buku" name="jumlah_buku" placeholder="0" required>
                            </div>
                            <div class="from-pengarang">
                                <label for="isbn">ISBN</label>
                                <input type="text" id="isbn" name="isbn" placeholder="Masukkan ISBN" required>
                            </div>
                            <div class="from-penulis">
                                <label for="penulis_buku">Penulis</label>
                                <input type="text" id="penulis_buku" name="penulis_buku"
                                    placeholder="Masukkan Nama Penulis" required>
                            </div>
                            <div class="form-terbit">
                                <div class="form-field">
                                    <label for="penerbit_buku">Penerbit</label>
                                    <input type="text" id="penerbit_buku" name="penerbit_buku"
                                        placeholder="Nama Penerbit" required>
                                </div>
                                <div class="form-field">
                                    <label for="tahun_terbit_buku">Tahun Terbit</label>
                                    <input type="text" id="tahun_terbit_buku" name="tahun_terbit_buku"
                                        placeholder="Tahun Terbit" pattern="\d{4}"
                                        title="Masukkan tahun dalam format 4 digit, misalnya: 2023" required>
                                </div>
                            </div>
                            <div class="from-deskripsi">
                                <label for="deskripsi">Deskripsi Buku</label>
                                <textarea id="deskripsi" name="deskripsi" placeholder="Masukkan deskripsi E - Book"
                                    required></textarea>
                                <div class="char-counter" id="char-counter">0/500</div>
                            </div>
                        </div>
                        <div class="form-group-right">
                            <div class="from-kategori">
                                <label for="kategori_buku">Kategori</label>
                                <select id="kategori_buku" name="kategori_buku" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Fiksi">Fiksi</option>
                                    <option value="Komik">Komik</option>
                                    <option value="Biografi dan Otobiografi">Biografi dan Otobiografi</option>
                                    <option value="Bisnis">Bisnis</option>
                                    <option value="Ensiklopedia">Ensiklopedia</option>
                                    <option value="Filsafat">Filsafat</option>
                                    <option value="Hukum dan Politik">Hukum dan Politik</option>
                                    <option value="Self Improvement">Self Improvement</option>
                                    <!-- Tambahkan kategori lain sesuai kebutuhan -->
                                </select>
                            </div>
                            <div class="upload-preview">
                                <label for="cover">Unggah Cover E - Book</label>
                                <div class="preview-container">
                                    <img id="preview-img" src="https://via.placeholder.com/" alt="Preview Sampul">
                                    <button type="button" id="upload-cover">
                                        <i class="fas fa-upload"></i> Unggah Sampul
                                    </button>
                                </div>
                                <div id="cover-info" class="cover-info">
                                    <span id="cover-name">No cover selected</span> |
                                    <span id="cover-size">0 KB</span>
                                </div>
                                <input type="file" id="cover-file" name="sampul_buku" accept="image/*"
                                    style="display: none;">
                            </div>


                            <div class="form-actions">
                                <button type="reset" class="btn-cancel">Batal</button>

                                <button type="submit" class="btn-save"
                                    onclick="return validateForm(event)">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const uploadButton = document.getElementById("upload-cover");
        const fileInput = document.getElementById("cover-file");
        const previewImg = document.getElementById("preview-img");
        const coverName = document.getElementById("cover-name");
        const coverSize = document.getElementById("cover-size");

        uploadButton.addEventListener("click", function () {
            fileInput.click();
        });

        fileInput.addEventListener("change", function () {
            const file = fileInput.files[0];
            if (file) {
                const allowedExtensions = /(\.jpg|\.jpeg)$/i;

                if (!allowedExtensions.exec(file.name)) {
                    alert("Hanya file dengan format JPG atau JPEG yang diizinkan.");
                    fileInput.value = ''; // Reset file input
                    previewImg.src = "https://via.placeholder.com/";
                    coverName.textContent = "No cover selected";
                    coverSize.textContent = "0 KB";
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(file);

                coverName.textContent = file.name;
                coverSize.textContent = (file.size / 1024).toFixed(2) + " KB";
            } else {
                previewImg.src = "https://via.placeholder.com/";
                coverName.textContent = "No cover selected";
                coverSize.textContent = "0 KB";
            }
        });
    });

    $(document).ready(function () {
        // Ambil URL saat ini
        var currentUrl = window.location.pathname.split('/').pop();

        // Tambahkan kelas 'active' pada elemen <li> yang sesuai
        $('ul li a').each(function () {
            var href = $(this).attr('href');
            if (href === currentUrl) {
                $(this).parent().addClass('active');
            }
        });
    });

    function validateJumlah() {
        const jumlahBuku = document.getElementById("jumlah_buku");
        // Pastikan input hanya angka positif
        if (isNaN(jumlahBuku.value) || jumlahBuku.value <= 0) {
            alert('Jumlah buku harus berupa angka positif!');
            jumlahBuku.value = ''; // Reset input
        }
    }

    // Fungsi untuk validasi ISBN
    function validateISBN() {
        const isbn = document.getElementById("isbn");
        // Pastikan ISBN adalah 13 digit angka
        if (!/^\d{13}$/.test(isbn.value)) {
            alert('ISBN harus terdiri dari 13 digit angka!');
            isbn.value = ''; // Reset input
        }
    }

    // Fungsi untuk validasi sampul buku
    function validateSampul() {
        const fileInput = document.getElementById("cover-file");
        const file = fileInput.files[0];
        const allowedExtensions = /(\.jpg|\.jpeg)$/i;

        if (!file || !allowedExtensions.exec(file.name)) {
            alert("File sampul harus diunggah dengan format JPG atau JPEG!");
            return false;
        }
        return true;
    }

    // Fungsi untuk menangani validasi form
    function validateForm(event) {
        const isbn = document.getElementById('isbn');
        const judul_buku = document.getElementById('judul_buku');
        const penulis_buku = document.getElementById('penulis_buku');
        const penerbit_buku = document.getElementById('penerbit_buku');
        const tahun_terbit_buku = document.getElementById('tahun_terbit_buku');
        const deskripsi = document.getElementById('deskripsi');
        const kategori_buku = document.getElementById('kategori_buku');
        const jumlah_buku = document.getElementById('jumlah_buku');

        // Validasi isian tidak boleh kosong
        if (!isbn.value || !judul_buku.value || !penulis_buku.value || !penerbit_buku.value || !tahun_terbit_buku.value || !deskripsi.value || !kategori_buku.value || !jumlah_buku.value) {
            alert('Semua isian harus diisi!');
            event.preventDefault();
            return false;
        }

        // Validasi jumlah harus angka positif
        if (isNaN(jumlah_buku.value) || jumlah_buku.value <= 0) {
            alert('Jumlah harus berupa angka positif!');
            event.preventDefault();
            return false;
        }

        // Validasi tahun terbit harus 4 digit angka
        if (!/^\d{4}$/.test(tahun_terbit_buku.value)) {
            alert('Tahun terbit harus berupa 4 digit angka!');
            event.preventDefault();
            return false;
        }

        // Validasi ISBN harus 13 digit angka
        validateISBN();

        // Validasi sampul harus diupload dan dalam format yang benar
        if (!validateSampul()) {
            event.preventDefault();
            return false;
        }

        // Validasi nama penulis dan penerbit
        const validNameRegex = /^[a-zA-Z\s.'’]+$/;

        if (!validNameRegex.test(penulis_buku.value)) {
            alert('Penulis hanya boleh mengandung huruf, spasi, titik, atau petik satu.');
            event.preventDefault();
            return false;
        }
        const penerbitRegex = /^[a-zA-Z0-9\s.'’]+$/;
        if (!validpenerbitRegex.test(penerbit_buku.value)) {
            alert('Penerbit hanya boleh mengandung huruf, spasi, angka, titik, atau petik satu.');
            event.preventDefault();
            return false;
        }

        return true;
    }
</script>

</body>

</html>