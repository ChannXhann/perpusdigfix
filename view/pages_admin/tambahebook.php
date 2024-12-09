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

// Ambil NIP dari session login
$nip = $_SESSION['nip'];

// Konfigurasi database
$host = '127.0.0.1:3306';
$db_name = 'u137138991_perpusdig';
$user = 'u137138991_root1';
$password = 'Adminperpusdig123';

try {
    // Koneksi ke database menggunakan PDO
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $user, $password);
    // Set mode error PDO ke Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $judul = trim($_POST['judul']);
    $penulis = trim($_POST['penulis']);
    $penerbit = trim($_POST['penerbit']);
    $tahun_terbit = trim($_POST['tahun_terbit']);
    $sinopsis = trim($_POST['sinopsis']);
    $kategori = trim($_POST['kategori']);

    // Validasi input, pastikan semua data diisi dan tidak hanya berisi spasi
    if (empty($judul) || empty($penulis) || empty($penerbit) || empty($tahun_terbit) || empty($sinopsis) || empty($kategori)) {
        echo "<script>
           alert('Semua data harus diisi dan tidak boleh hanya berisi spasi!');
           window.location.href = 'tambahebook.php';
         </script>";
        exit();
    }

    // Validasi agar tidak hanya berisi spasi
    if (strlen($judul) == 0 || strlen($penulis) == 0 || strlen($penerbit) == 0 || strlen($sinopsis) == 0 || strlen($kategori) == 0) {
        echo "<script>
           alert('Data tidak boleh hanya berisi spasi!');
           window.location.href = 'tambahebook.php';
         </script>";
        exit();
    }

    // Validasi nama pengarang: hanya huruf, spasi, titik, atau koma dan koma atas
    if (!preg_match("/^[a-zA-Z\s.,']+$/", $penulis)) {
        echo "<script>
           alert('Nama pengarang hanya boleh berisi huruf, spasi, titik, koma atau petik atas!');
           window.location.href = 'tambahebook.php';
         </script>";
        exit;
    }

    //Validasi Tahun Terbit harus 4 angka , dan tidak lebih dari tahun ini , dan masuk akal
    if (!preg_match("/^\d{4}$/", $tahun_terbit) || $tahun_terbit > date('Y') || $tahun_terbit < 1000) {
        echo "<script>
           alert('Tahun terbit harus berupa 4 digit angka tidak melebihi tahun saat ini dan masuk akal!');
           window.location.href = 'tambahebook.php';
         </script>";
        exit;
    }
    
    // Validasi file sampul, pastikan ada file yang diunggah
    if (!isset($_FILES['sampul']) || $_FILES['sampul']['error'] != 0) {
        echo "<p style='color: red;'>File sampul harus diunggah!</p>";
        exit();
    }

    // Validasi file PDF, pastikan ada file yang diunggah
    if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] != 0) {
        echo "<p style='color: red;'>File PDF harus diunggah!</p>";
        exit();
    }

    // Validasi duplikasi judul e-book
    $check_duplicate = $conn->prepare("SELECT * FROM e_book WHERE judul = :judul");
    $check_duplicate->bindParam(':judul', $judul);
    $check_duplicate->execute();

    if ($check_duplicate->rowCount() > 0) {
        echo "<script>
                alert('Judul e-book sudah ada di database. Harap gunakan judul lain.');
                window.location.href = 'Ebook.php';
              </script>";
        exit();
    }

    // Proses unggah file sampul sebagai BLOB
    $sampul = null;
    if ($_FILES['sampul']['error'] == 0) {
        $sampul = file_get_contents($_FILES['sampul']['tmp_name']);
    }

    // Proses unggah file PDF sebagai BLOB
    $pdf = null;
    if ($_FILES['pdf']['error'] == 0) {
        $pdf = file_get_contents($_FILES['pdf']['tmp_name']);
    }

    // Simpan data ke database
    try {
        $stmt = $conn->prepare("INSERT INTO e_book (judul, penulis, penerbit, tahun_terbit, sinopsis, kategori, sampul, pdf, nip) 
                                VALUES (:judul, :penulis, :penerbit, :tahun_terbit, :sinopsis, :kategori, :sampul, :pdf, :nip)");

        // Binding parameter
        $stmt->bindParam(':judul', $judul);
        $stmt->bindParam(':penulis', $penulis);
        $stmt->bindParam(':penerbit', $penerbit);
        $stmt->bindParam(':tahun_terbit', $tahun_terbit);
        $stmt->bindParam(':sinopsis', $sinopsis);
        $stmt->bindParam(':kategori', $kategori);
        $stmt->bindParam(':sampul', $sampul, PDO::PARAM_LOB); // Menyimpan file sebagai BLOB
        $stmt->bindParam(':pdf', $pdf, PDO::PARAM_LOB);       // Menyimpan file sebagai BLOB
        $stmt->bindParam(':nip', $nip); // NIP dari session login

        // Eksekusi query
        if ($stmt->execute()) {
            // Tampilkan pesan sukses dan redirect ke Ebook.php
            echo "<script>
                    alert('E-Book berhasil ditambahkan.');
                    window.location.href = 'Ebook.php';
                  </script>";
        } else {
            echo "<p style='color: red;'>Gagal menambahkan E-Book.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Terjadi kesalahan: " . $e->getMessage() . "</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah dan Lihat E-Book</title>
    <link rel="stylesheet" href="../../assets/css/tambahebook.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>

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
                <a href="dashboard_admin.php">Beranda</a> / <a href="Ebook.php">Data E Book</a> / Tambah Data E-Book
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-container">
                    <div class="top-form-container">
                        <label for="file-upload" class="btn-upload">
                            <i class="fas fa-upload"></i> Unggah E - Book
                        </label>
                        <input type="file" id="file-upload" name="pdf" style="display: none;">
                        <div id="file-info" class="file-info">
                            <span id="file-name">No file selected</span> |
                            <span id="file-size">0 KB</span>
                        </div>
                    </div>
                    <div class="main-form-content">
                        <div class="form-group-left">
                            <div class="from-judul">
                                <label for="judul">Judul</label>
                                <input type="text" id="judul" name="judul" placeholder="Masukkan Judul Buku" required>
                            </div>
                            <div class="from-pengarang">
                                <label for="penulis">Penulis</label>
                                <input type="text" id="penulis" name="penulis" placeholder="Masukkan Nama Pengarang"
                                    required>
                            </div>
                            <div class="form-terbit">
                                <div class="form-field">
                                    <label for="penerbit">Penerbit</label>
                                    <input type="text" id="penerbit" name="penerbit" placeholder="Nama Penerbit"
                                        required>
                                </div>
                                <div class="form-field">
                                    <label for="tahun_terbit">Tahun Terbit</label>
                                    <input type="text" id="tahun_terbit" name="tahun_terbit" placeholder="Tahun Terbit"
                                        pattern="\d{4}" title="Masukkan tahun dalam format 4 digit, misalnya: 2023"
                                        required>
                                </div>
                            </div>
                            <div class="from-deskripsi">
                                <label for="sinopsis">Deskripsi E - Book</label>
                                <textarea id="sinopsis" name="sinopsis" placeholder="Masukkan deskripsi E - Book"
                                    maxlength="500" required></textarea>
                                <div class="char-counter" id="char-counter">0/500</div>
                            </div>
                        </div>
                        <div class="form-group-right">
                            <div class="from-kategori">
                                <label for="kategori">Kategori</label>
                                <select id="kategori" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Fiksi">Fiksi</option>
                                    <option value="Komik">Komik</option>
                                    <option value="Biografi dan Otobiografi">Biografi dan Otobiografi</option>
                                    <option value="B    nis">Bisnis</option>
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
                                <input type="file" id="cover-file" name="sampul" accept="image/*"
                                    style="display: none;">
                            </div>
                            <div class="form-actions">
                                <button type="reset" class="btn-cancel">Batal</button>
                                <button type="submit" class="btn-save">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <h2>Daftar E-Book</h2>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const sinopsis = document.getElementById('sinopsis');
        const charCounter = document.getElementById('char-counter');
        const coverFile = document.getElementById('cover-file');
        const previewImg = document.getElementById('preview-img');
        const uploadCover = document.getElementById('upload-cover');
        const fileUpload = document.getElementById('file-upload');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');
        const coverName = document.getElementById('cover-name');
        const coverSize = document.getElementById('cover-size');
        const tahunTerbit = document.getElementById('tahun_terbit');

        // Set the max file size to 40 MB (in bytes)
        const maxFileSize = 40 * 1024 * 1024; // 40 MB in bytes

        // Update char counter for Deskripsi
        sinopsis.addEventListener('input', () => {
            const charCount = sinopsis.value.length;
            charCounter.textContent = `${charCount}/500`; // Update the counter
        });

        // Preview cover image
        uploadCover.addEventListener('click', () => {
            coverFile.click(); // Trigger the file input click
        });

        // Set ukuran maksimal sampul 10MB
        const maxCoverSize = 10 * 1024 * 1024; // 10MB in bytes

        // Preview cover image (Sampul)
        coverFile.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                // Validasi tipe file (hanya JPEG dan JPG yang diperbolehkan)
                const fileType = file.type;
                if (fileType !== 'image/jpeg' && fileType !== 'image/jpg') {
                    alert("Hanya file JPEG dan JPG yang diperbolehkan untuk sampul.");
                    // Reset file input
                    coverFile.value = '';
                    coverName.textContent = 'No cover selected';
                    coverSize.textContent = '0 KB';
                    previewImg.src = 'https://via.placeholder.com/'; // Reset to placeholder
                } else if (file.size > maxCoverSize) {
                    alert("Ukuran file terlalu besar. Maksimal 10 MB.");
                    // Reset file input
                    coverFile.value = '';
                    coverName.textContent = 'No cover selected';
                    coverSize.textContent = '0 KB';
                    previewImg.src = 'https://via.placeholder.com/'; // Reset to placeholder
                } else {
                    // Display file name and size
                    coverName.textContent = file.name; // Display file name
                    coverSize.textContent = (file.size / 1024).toFixed(2) + ' KB'; // Display file size in KB

                    // If the file is an image, show preview
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewImg.src = e.target.result; // Set preview image to the uploaded file
                    };
                    reader.readAsDataURL(file);
                }
            } else {
                coverName.textContent = 'No cover selected';
                coverSize.textContent = '0 KB';
                previewImg.src = 'https://via.placeholder.com/'; // Reset to placeholder
            }
        });


        // File Upload for E-Book (PDF)
        // File Upload for E-Book (PDF)
        fileUpload.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                // Validasi tipe file (hanya PDF yang diperbolehkan)
                const fileType = file.type;
                if (fileType !== 'application/pdf') {
                    alert("Hanya file PDF yang diperbolehkan.");
                    // Reset file input
                    fileUpload.value = '';
                    fileName.textContent = 'No file selected';
                    fileSize.textContent = '0 KB';
                } else if (file.size > maxFileSize) {
                    alert("Ukuran file terlalu besar. Maksimal 40 MB.");
                    // Reset file input
                    fileUpload.value = '';
                    fileName.textContent = 'No file selected';
                    fileSize.textContent = '0 KB';
                } else {
                    // Display file name and size
                    fileName.textContent = file.name; // Display file name
                    fileSize.textContent = (file.size / 1024).toFixed(2) + ' KB'; // Display file size in KB
                }
            } else {
                fileName.textContent = 'No file selected';
                fileSize.textContent = '0 KB';
            }
        });


        // Validasi tahun terbit (hanya angka 4 digit)
        // Validasi tahun terbit (hanya angka 4 digit)
        tahunTerbit.addEventListener('blur', (e) => { // Gunakan 'blur' agar validasi hanya dilakukan setelah selesai mengetik
            const value = e.target.value;
            if (value && !/^\d{4}$/.test(value)) {
                alert('Tahun terbit harus berupa angka 4 digit.');
                tahunTerbit.value = ''; // Reset value jika input tidak valid
            }
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
        // Menambahkan event listener saat tombol submit ditekan untuk memvalidasi file PDF dan Sampul
        document.querySelector('form').addEventListener('submit', function (event) {
            // Cek apakah file PDF sudah diunggah
            const pdfFile = document.getElementById('file-upload').files[0];
            // Cek apakah file sampul sudah diunggah
            const coverFile = document.getElementById('cover-file').files[0];
            const penulis = document.getElementById('penulis').value;
            const penerbit = document.getElementById('penerbit').value;

            // Validasi Penulis: hanya boleh huruf, titik (.), dan simbol petik satu (')
            const penulisRegex = /^[a-zA-Z\s.'’]+$/;
            if (!penulisRegex.test(penulis)) {
                alert("Penulis hanya boleh mengandung huruf, spasi, titik (.), dan simbol petik satu (').");
                event.preventDefault(); // Menghentikan pengiriman form
                return;
            }

            // Validasi Penerbit: hanya boleh huruf, angka, spasi, dan titik (.)
            const penerbitRegex = /^[a-zA-Z0-9\s.'’]+$/;
            if (!penerbitRegex.test(penerbit)) {
                alert("Penerbit hanya boleh mengandung huruf, angka, spasi, dan titik dan petik satu");
                event.preventDefault(); // Menghentikan pengiriman form
                return;
            }

            // Cek jika salah satu file belum diunggah
            if (!pdfFile) {
                alert("File PDF harus diunggah!");
                event.preventDefault(); // Menghentikan pengiriman form
                return;
            }

            if (!coverFile) {
                alert("File sampul harus diunggah!");
                event.preventDefault(); // Menghentikan pengiriman form
                return;
            }

            // Validasi tipe file untuk PDF dan Sampul
            if (pdfFile.type !== 'application/pdf') {
                alert("Hanya file PDF yang diperbolehkan untuk E-Book!");
                event.preventDefault(); // Menghentikan pengiriman form
                return;
            }

            if (coverFile.type !== 'image/jpeg' && coverFile.type !== 'image/jpg') {
                alert("Hanya file JPEG atau JPG yang diperbolehkan untuk sampul!");
                event.preventDefault(); // Menghentikan pengiriman form
                return;
            }

            // Validasi ukuran file
            const maxPdfSize = 40 * 1024 * 1024; // 40 MB
            const maxCoverSize = 10 * 1024 * 1024; // 10 MB

            if (pdfFile.size > maxPdfSize) {
                alert("Ukuran file PDF terlalu besar. Maksimal 40 MB.");
                event.preventDefault(); // Menghentikan pengiriman form
                return;
            }

            if (coverFile.size > maxCoverSize) {
                alert("Ukuran file sampul terlalu besar. Maksimal 10 MB.");
                event.preventDefault(); // Menghentikan pengiriman form
                return;
            }
        });


    </script>

</body>

</html>