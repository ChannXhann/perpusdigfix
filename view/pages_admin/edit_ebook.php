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

// Koneksi menggunakan PDO
$host = '127.0.0.1:3306';
$db_name = 'u137138991_perpusdig';
$user = 'u137138991_root1';
$password = 'Adminperpusdig123';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

$isUpdate = false;
$bookData = [];
$sampulBase64 = "";
$errorMessage = '';

// Ambil data e-book jika ID diberikan
if (isset($_GET['id_ebook'])) {
    $id_ebook = $_GET['id_ebook'];
    $stmt = $conn->prepare("SELECT * FROM e_book WHERE id_ebook = :id_ebook");
    $stmt->bindParam(':id_ebook', $id_ebook, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $bookData = $stmt->fetch(PDO::FETCH_ASSOC);
        $isUpdate = true;

        // Konversi sampul BLOB ke base64 jika ada data
        if (!empty($bookData['sampul'])) {
            $sampulBase64 = "data:image/jpeg;base64," . base64_encode($bookData['sampul']);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'] ?? '';
    $penulis = $_POST['penulis'] ?? '';
    $penerbit = $_POST['penerbit'] ?? '';
    $tahun_terbit = $_POST['tahun_terbit'] ?? '';
    $sinopsis = $_POST['deskripsi'] ?? '';
    $kategori = $_POST['kategori'] ?? '';

    // Validasi input: tidak boleh kosong atau hanya berisi spasi
    if (empty(trim($judul))) {
        $errorMessage = "Judul tidak boleh kosong atau hanya berisi spasi.";
    } elseif (empty(trim($penulis))) {
        $errorMessage = "Penulis tidak boleh kosong atau hanya berisi spasi.";
    } elseif (empty(trim($penerbit))) {
        $errorMessage = "Penerbit tidak boleh kosong atau hanya berisi spasi.";
    } elseif (empty(trim($tahun_terbit))) {
        $errorMessage = "Tahun terbit tidak boleh kosong atau hanya berisi spasi.";
    } elseif (empty(trim($sinopsis))) {
        $errorMessage = "Sinopsis tidak boleh kosong atau hanya berisi spasi.";
    } elseif (empty(trim($kategori))) {
        $errorMessage = "Kategori tidak boleh kosong atau hanya berisi spasi.";
    }
    
    // Validasi input
    if (!preg_match("/^[a-zA-Z0-9 .,']+$/", $penerbit)) {
        $errorMessage = "Penerbit hanya boleh mengandung huruf, angka, titik, dan petik atas.";
    } elseif (!preg_match("/^[a-zA-Z\s.'’]+$/", $penulis)) {
        $errorMessage = "Penulis hanya boleh mengandung huruf, titik, dan petik atas.";
    } elseif (!preg_match("/^\d{4}$/", $tahun_terbit)) {
        $errorMessage = "Tahun terbit harus berupa angka 4 digit.";
    } elseif ($tahun_terbit > date('Y') || $tahun_terbit < 1000) {
        $errorMessage = "Tahun terbit tidak boleh lebih dari tahun" . date('Y') . "dan masuk akal.";
    }
    
    if (empty($errorMessage)) {
        try {
            $stmt = $conn->prepare("SELECT id_ebook FROM e_book WHERE judul = :judul AND id_ebook != :id_ebook");
            $stmt->bindParam(':judul', $judul);
            $stmt->bindParam(':id_ebook', $id_ebook, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $errorMessage = "Judul e-book sudah digunakan oleh e-book lain. Silakan gunakan judul yang berbeda.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Terjadi kesalahan saat validasi judul: " . $e->getMessage();
        }
    }

    // Validasi file sampul
    $sampul = $bookData['sampul'] ?? null;
    if (isset($_FILES['sampul']) && $_FILES['sampul']['error'] === UPLOAD_ERR_OK) {
        $sampulType = mime_content_type($_FILES['sampul']['tmp_name']);
        if ($sampulType !== 'image/jpeg') {
            $errorMessage = "Sampul harus berformat JPG atau JPEG.";
        } else {
            $sampul = file_get_contents($_FILES['sampul']['tmp_name']);
        }
    }

    // Validasi file PDF
    $pdfFile = $bookData['pdf'] ?? null;
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $pdfType = mime_content_type($_FILES['pdf']['tmp_name']);
        if ($pdfType !== 'application/pdf') {
            $errorMessage = "File ebook harus berformat PDF.";
        } else {
            $pdfFile = file_get_contents($_FILES['pdf']['tmp_name']);
        }
    }

    // Jika tidak ada error, proses update data
    if (empty($errorMessage)) {
        try {
            $query = "UPDATE e_book 
                      SET judul = :judul, 
                          penulis = :penulis, 
                          penerbit = :penerbit, 
                          tahun_terbit = :tahun_terbit, 
                          sinopsis = :sinopsis, 
                          kategori = :kategori, 
                          sampul = COALESCE(:sampul, sampul), 
                          pdf = COALESCE(:pdf, pdf) 
                      WHERE id_ebook = :id_ebook";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':judul', $judul);
            $stmt->bindParam(':penulis', $penulis);
            $stmt->bindParam(':penerbit', $penerbit);
            $stmt->bindParam(':tahun_terbit', $tahun_terbit);
            $stmt->bindParam(':sinopsis', $sinopsis);
            $stmt->bindParam(':kategori', $kategori);

            if ($sampul !== null) {
                $stmt->bindParam(':sampul', $sampul, PDO::PARAM_LOB);
            } else {
                $stmt->bindValue(':sampul', null, PDO::PARAM_NULL);
            }

            if ($pdfFile !== null) {
                $stmt->bindParam(':pdf', $pdfFile, PDO::PARAM_LOB);
            } else {
                $stmt->bindValue(':pdf', null, PDO::PARAM_NULL);
            }

            $stmt->bindParam(':id_ebook', $id_ebook, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo "<script>
                    alert('Data berhasil diperbarui!');
                    window.location.href = 'Ebook.php';
                </script>";
                exit;
            } else {
                $errorMessage = "Gagal memperbarui data buku.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Terjadi kesalahan: " . $e->getMessage();
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
        // Preview gambar sampul
        function previewSampul(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function () {
                document.getElementById('preview').src = reader.result;
                document.getElementById('preview').style.display = 'block';
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        }

        function validateForm(event) {
            const penulisInput = document.getElementById('penulis');
            const penulisValue = penulisInput.value.trim();

            // Pola validasi: huruf, spasi, titik (.), dan petik satu (')
            const regex = /^[a-zA-Z\s.'’]+$/;

            if (!regex.test(penulisValue)) {
                event.preventDefault(); // Cegah form submit jika tidak valid
                alert("Penulis hanya boleh mengandung huruf, spasi, titik (.), dan simbol petik satu (').");
            }
        }

        // Pasang event listener pada form
        document.querySelector('form').addEventListener('submit', validateForm);
    </script>
</head>

<body>
    <div class="header">
        <img alt="Library logo" src="../../assets/images/logo_perpusdig.png" />
        <h1>&#124; PerpusDig - Sistem Informasi Perpustakaan Daerah Kabupaten Nganjuk</h1>
    </div>
    <div class="container">
        <div class="content">
            <div class="breadcrumb">
                <h2>Edit Data</h2>
                <a href="dashboard_admin.php">Beranda</a> / <a href="Ebook.php">Data Ebook</a> / Edit Data Ebook
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-container">
                    <div class="main-form-content">
                        <div class="form-group-left">
                            <input type="hidden" name="id_ebook" value="<?php echo $bookData['id_ebook'] ?? ''; ?>">

                            <label for="judul">Judul</label>
                            <input type="text" id="judul" name="judul" value="<?php echo $bookData['judul'] ?? ''; ?>"
                                required>

                            <label for="penulis">Penulis</label>
                            <input type="text" id="penulis" name="penulis"
                                value="<?php echo $bookData['penulis'] ?? ''; ?>" required>

                            <label for="penerbit">Penerbit</label>
                            <input type="text" id="penerbit" name="penerbit"
                                value="<?php echo $bookData['penerbit'] ?? ''; ?>" required>

                            <label for="tahun_terbit">Tahun Terbit</label>
                            <input type="text" id="tahun_terbit" name="tahun_terbit"
                                value="<?php echo $bookData['tahun_terbit'] ?? ''; ?>" required>

                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi"
                                name="deskripsi"><?php echo $bookData['sinopsis'] ?? ''; ?></textarea>
                        </div>
                        <div class="form-group-right">
                            <div class="from-kategori">
                                <label for="kategori">Kategori</label>
                                <select id="kategori" name="kategori" required>
                                    <option value="Fiksi" <?php echo ($bookData['kategori'] ?? '') === 'Fiksi' ? 'selected' : ''; ?>>Fiksi</option>
                                    <option value="Komik" <?php echo ($bookData['kategori'] ?? '') === 'Komik' ? 'selected' : ''; ?>>Komik</option>
                                    <option value="Biografi & Otobiografi" <?php echo ($bookData['kategori'] ?? '') === 'Biografi & Otobiografi' ? 'selected' : ''; ?>>Biografi & Otobiografi
                                    </option>
                                    <option value="Bisnis" <?php echo ($bookData['kategori'] ?? '') === 'Bisnis' ? 'selected' : ''; ?>>Bisnis</option>
                                    <option value="Ensiklopedia" <?php echo ($bookData['kategori'] ?? '') === 'Ensiklopedia' ? 'selected' : ''; ?>>Ensiklopedia</option>
                                    <option value="Filsafat" <?php echo ($bookData['kategori'] ?? '') === 'Filsafat' ? 'selected' : ''; ?>>Filsafat</option>
                                    <option value="Hukum dan Politik" <?php echo ($bookData['kategori'] ?? '') === 'Hukum dan Politik' ? 'selected' : ''; ?>>Hukum dan Politik</option>
                                    <option value="Self Improvement" <?php echo ($bookData['kategori'] ?? '') === 'Self Improvement' ? 'selected' : ''; ?>>Self Improvement</option>
                                </select>
                            </div>
                            <div class="from-cover">
                                <label for="sampul">Sampul Buku</label>
                                <input type="file" id="sampul" name="sampul" accept="image/*"
                                    onchange="previewSampul(event)">
                                <img id="preview" src="<?php echo $sampulBase64 ?: '#'; ?>" alt="Preview Sampul"
                                    style="display: <?php echo $sampulBase64 ? 'block' : 'none'; ?>; width: 200px; margin-top: 10px;">
                            </div>
                            <div class="from-pdf">
                                <label for="pdf">Upload PDF Ebook</label>
                                <input type="file" id="pdf" name="pdf" accept="application/pdf">
                            </div>
                        </div>
                    </div>
                    <div class="form-action">
                        <button type="submit" class="btn-save">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
            <?php echo $errorMessage ? "<div class='error-message'>$errorMessage</div>" : ''; ?>
        </div>
    </div>
</body>

</html>