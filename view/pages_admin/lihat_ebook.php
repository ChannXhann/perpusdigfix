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

// Include koneksi database
include '../../config/koneksi.php';
$db = new Database();
$koneksi = $db->koneksi;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_ebook'])) {
    $id_ebook = $_POST['id_ebook'];

    // Query untuk mengambil data e-book berdasarkan id_ebook
    $query = $koneksi->prepare("SELECT * FROM e_book WHERE id_ebook = :id_ebook");
    $query->bindParam(':id_ebook', $id_ebook, PDO::PARAM_INT);
    $query->execute();
    $data = $query->fetch(PDO::FETCH_ASSOC);

    // Jika data ditemukan
    if ($data) {
        $sampulBase64 = base64_encode($data['sampul']); // Mengubah BLOB menjadi base64
        $shortSinopsis = substr($data['sinopsis'], 0, 150); // Potong sinopsis
        $sinopsis = $data['sinopsis'];

        // Encode PDF jika ingin digunakan
        $pdfBase64 = base64_encode($data['pdf']);
    } else {
        echo "Data e-book tidak ditemukan.";
        exit;
    }
} else {
    echo "Akses tidak valid.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PerpusDig - Sistem Informasi Perpustakaan Daerah Kabupaten Nganjuk</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .header {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
        }

        .header img {
            width: 40px;
            margin-right: 10px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .sidebar img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            display: block;
            margin: 0 auto;
        }

        .sidebar h3 {
            text-align: center;
            margin: 10px 0;
        }

        .sidebar p {
            text-align: center;
            background-color: #A8CFFB;
            border-radius: 15px;
            padding: 5px 10px;
            display: inline-block;
            margin: 0 auto;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            flex-grow: 1;
            margin-top: 20px;
        }

        .sidebar ul li {
            padding: 10px;
            margin: 15px 0;
            background-color: white;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .sidebar ul li:hover {
            background-color: rgba(168, 207, 251, 0.6);
            border-radius: 20px;
        }

        .sidebar ul li i {
            margin-right: 10px;
        }


        .sidebar ul li a {
            color: inherit;
            text-decoration: none;
        }

        .sidebar ul li a:focus,
        .sidebar ul li a:active {
            outline: none;
            box-shadow: none;
        }

        .content {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .content h2 {
            background-color: #007bff;
            color: white;
            padding: 20px;
            border-radius: 0px;
            margin-bottom: 0px;
        }

        .maincontainer h2 {
            background-color: #ffffff;
            color: #0349AD;
            margin-left: 270px;
            margin-top: -330px;
            font-family: "Montserrat", sans-serif;
            font-size: 2em;

        }

        .maincontainer img {
            max-width: 200px;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .description {
            padding-top: 50px;
            padding-left: 0px
                /* Memberikan spasi di bawah deskripsi */

        }

        .read-more {
            /* display: inline-block; */
            margin-top: -100px;
            /* background-color: #000bff;
            color: #fff;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px; */
        }

        /* .read-more:hover {
            background-color: #0056b3;
        } */

        .maincontainer img {
            margin-left: 50px;
            margin-top: 15px;
        }

        .maincontainer {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .badge {
            width: 10%;
            height: 6%;
            padding: 5px;
            margin-top: 5px;
            margin-left: 290px;
            background-color: #007bff;
            color: white;
            overflow: hidden;
            transition: 0.38s;
            border: none;
            border-radius: 100%;
            font-size: 14px;
            font-family: "Montserrat", sans-serif;
            font-weight: 549;
            /* cursor: pointer; */
            -webkit-border-radius: 20px;
            -moz-border-radius: 20px;
            -ms-border-radius: 20px;
            -o-border-radius: 20px;
            -webkit-transition: 0.38s;
            -moz-transition: 0.38s;
            -ms-transition: 0.38s;
            -o-transition: 0.38s;
        }

        .info {
            margin-left: 290px;
            display: block;
            margin-top: 20px;
            /* Memberi sedikit jarak antar baris jika diperlukan */
            font-family: "Montserrat", sans-serif;
        }

        .info {
            font-family: "Montserrat", sans-serif;
            margin-left: 290px;
        }

        .info-item {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            /* Jarak antar baris */
        }

        .info-item span:first-child {
            width: 150px;
            /* Lebar tetap untuk teks sebelum tanda titik dua */
            font-weight: bold;
        }

        .info-item span:last-child {
            flex: 1;
        }


        .availability {
            margin-left: 296px;
            margin-top: 20px;
            font-size: 1.3em;
            color: #007bff;
        }

        .availability2 {
            margin-left: 320px;
            margin-top: 0px;
            font-size: 1em;
        }


        .description h3 {
            color: #007bff;
            margin-left: 120px;
            margin-bottom: 10px;
            font-size: 1.6em;
        }


        .description textarea {
            width: 100%;
            height: auto;
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            margin-left: -10px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ffffff;
            resize: none;
            padding-left: -200px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            -ms-border-radius: 5px;
            -o-border-radius: 5px;
        }

        .read-more {
            display: inline-block;
            margin-left: 930px;
            /* Sesuaikan nilai ini untuk menentukan seberapa jauh ke kanan */
            margin-bottom: 10px;
            /* Jika Anda ingin memberikan jarak ke atas */
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        table {
            width: 100%;
            /* border-collapse: collapse; */
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 10px;
            /* border: 1px solid #ddd; */
            text-align: left;
        }

        table thead tr {
            background-color: #f4f4f4;
        }

        .content .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .content .actions button {
            background-color: #ffffff;
            color: rgb(0, 0, 0);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .content .actions button:hover {
            background-color: #d8d8d8;
        }

        .content .actions input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 200px;
        }

        .action-btn {
            background-color: transparent;
            border: none;
            cursor: pointer;
            padding: 0 5px;
            /* Mengurangi padding horizontal antar ikon */
            margin: 0;
            /* Menghilangkan margin default */
        }

        .action-btn i {
            color: #e74c3c;
        }

        .breadcrumb {
            font-size: 14px;
            margin-bottom: 10px;
            background-color: #007bff;
            color: white;
            padding: 10px;
            margin-top: 0px;
        }

        /* .sortable:hover {
            cursor: pointer;
            color: #007bff;
        } */

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: -90px;
            margin-left: 290px;
        }

        .pagination button {
            background-color: transparent;
            border: 1px solid #ccc;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
            transition: background-color 0.3s;
        }

        .pagination button:hover {
            background-color: #e0e0e0;
        }

        .pagination button.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .pagination select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-left: 10px;
        }

        .top-bar button {
            border: none;
            background-color: transparent;
            /* Membuat tombol tanpa latar belakang */
            padding: 0;
            cursor: pointer;
        }

        .top-bar button a {
            text-decoration: none;
            /* Menghilangkan garis bawah pada link */
            color: inherit;
            /* Menggunakan warna teks yang diwariskan dari elemen luar */
        }

        .top-bar button:focus,
        .top-bar button:active {
            outline: none;
            /* Menghilangkan garis biru saat tombol diklik atau difokuskan */
        }

        .breadcrumb a {
            text-decoration: none;
            /* Menghilangkan garis bawah pada link */
            color: inherit;
            /* Menggunakan warna teks yang diwariskan dari elemen induk */
        }

        .breadcrumb a:focus,
        .breadcrumb a:active,
        .breadcrumb a:visited {
            color: inherit;
            /* Menghilangkan perubahan warna setelah link diklik atau dikunjungi */
            outline: none;
            /* Menghilangkan garis biru saat link difokuskan */
        }

        .pdf-link {
            margin-left: 10px;
            /* Geser sedikit ke kiri */
            text-align: left;
            /* Pastikan teks PDF terjajar ke kiri */
        }
    </style>
</head>

<body>
    <div class="header">
        <img alt="Library logo" src="../../assets/images/logo_perpusdig.png" />
        <h1>PerpusDig - Sistem Informasi Perpustakaan Daerah Kabupaten Nganjuk</h1>
    </div>
    <div class="container">
        <?php include '../../include/sidebar_admin.php'; ?>
        <div class="content">
            <h2>Detail E-Book</h2>
            <div class="breadcrumb">
                <a href="dashboard_admin.php">Beranda</a> / Data E-Book / Detail E-Book
            </div>
            <div class="maincontainer">
                <img src="data:image/jpeg;base64,<?php echo $sampulBase64; ?>" alt="Sampul Buku" />
                <h2><?php echo htmlspecialchars($data['judul']); ?></h2>
                <button class="badge"><?php echo htmlspecialchars($data['kategori']); ?></button>
                <div class="info">
                    <div class="info-item"><span>Penulis</span><span>:
                            <?php echo htmlspecialchars($data['penulis']); ?></span></div>
                    <div class="info-item"><span>Penerbit</span><span>:
                            <?php echo htmlspecialchars($data['penerbit']); ?></span></div>
                    <div class="info-item"><span>Tahun Terbit</span><span>:
                            <?php echo htmlspecialchars($data['tahun_terbit']); ?></span></div>
                    <!-- Ganti link PDF dengan object untuk menampilkan di halaman -->
                    <!-- Tombol untuk mengunduh PDF -->
                    <a class="pdf-button" href="../../model/getpdf.php?id=<?php echo $data['id_ebook']; ?>"
                        target="_blank">PDF</a>


                </div>
                <div class="description">
                    <h3>Deskripsi E-Book</h3>
                    <textarea id="sinopsis" rows="10" cols="50"
                        readonly><?php echo htmlspecialchars($shortSinopsis); ?></textarea>
                    <a id="readMore" class="read-more" href="#">Baca Selengkapnya</a>
                    <script>
                        const readMoreButton = document.getElementById('readMore');
                        const sinopsisTextarea = document.getElementById('sinopsis');

                        readMoreButton.addEventListener('click', (e) => {
                            e.preventDefault();
                            sinopsisTextarea.value = <?php echo json_encode($sinopsis); ?>;
                            readMoreButton.style.display = 'none';
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</body>

</html>