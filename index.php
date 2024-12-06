<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Perpustakaan Daerah</title>
  <link rel="stylesheet" href="../../assets/css/style.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <style>
    /* Layanan Perpustakaan */
    #layanan-perpustakaan {
      padding: 3rem 2rem;
      background-color: #f9f9f9;
      text-align: center;
    }

    #layanan-perpustakaan h2 {
      font-size: 2rem;
      margin-bottom: 2rem;
      color: #333;
    }

    /* Card Layanan */
    .services-container {
      display: flex;
      justify-content: center;
      gap: 2rem;
      flex-wrap: wrap;
    }

    .service-card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 1.5rem;
      max-width: 300px;
      text-align: left;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .service-card img {
      width: 50px;
      height: 50px;
      margin-bottom: 1rem;
    }

    .service-card h3 {
      font-size: 1.2rem;
      color: #0056b3;
      margin-bottom: 0.5rem;
    }

    .service-card p {
      font-size: 0.9rem;
      color: #666;
    }

    .service-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    /* Slider Section */
    /* Slider Section */
    #slider {
      width: 100%;
      max-width: 1200px;
      overflow: hidden;
      margin: 2rem auto;
      position: relative;
    }

    .slider-container {
      display: flex;
      transition: transform 1s ease;
    }

    .slide {
      min-width: 100%;
      /* Setiap slide memenuhi lebar kontainer */
      position: relative;
    }

    .slide img {
      width:600px;
      height: 400px;
      border-radius: 8px;
      display: block;
    }

    .blue-box {
      position: absolute;
      top: 10%;
      right: 5%;
      background-color: #0056b3;
      color: white;
      padding: 1rem;
      border-radius: 8px;
      max-width: 35%;
      /* Mengurangi lebar kotak biru agar tidak terlalu besar */
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      z-index: 10;
      /* Pastikan berada di atas gambar */
    }

    .blue-box h3 {
      font-size: 35;
      margin-bottom: 0.5rem;
      font-weight: bold;

    }

    .blue-box p {
      font-size: 1rem;
      line-height: 1.5;
    }

    /* Animasi */
    @keyframes slide {

      0%,
      25% {
        transform: translateX(0);
      }

      50%,
      75% {
        transform: translateX(-100%);
      }

      100% {
        transform: translateX(0);
      }
    }

    .slider-container {
      animation: slide 8s infinite;
    }

    /* Gambar dan Informasi */
    .library-info {
      display: flex;
      align-items: center;
      margin-top: 3rem;
      gap: 2rem;
    }

    .library-image {
      max-width: 900px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .info-card {
      background: #0056b3;
      color: white;
      border-radius: 10px;
      padding: 1.5rem;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      max-width: 500px;
    }

    .info-card h3 {
      font-size: 3rem;
      margin-bottom: 1rem;
    }

    .info-card p {
      font-size: 1.5rem;
      line-height: 1.6;
    }
    /* Jam Buka Section */
    #jam-buka {
  padding: 20px;
}

.jam-buka-container {
  max-width: 1200px;
  margin: 0 auto;
}

.jam-buka {
  display: flex;
  flex-direction: row; /* Menyusun elemen secara horizontal */
  justify-content: space-between; /* Memberikan jarak antar elemen */
  gap: 20px; /* Jarak antar elemen */
  background-color: #C1E8FF;
}

.hari {
  flex: 1; /* Membuat setiap elemen 'hari' memiliki lebar yang sama */
  padding: 10px;
  text-align: center;
  border: 1px solid #ccc;
  border-radius: 8px;
}

.hari h3 {
  margin: 0 0 10px;
}

.hari p {
  margin: 0;
}


/* Tentang Kami Section */
#tentang-kami {
  padding: 2rem;
  background-color: #f9f9f9;
  text-align: center;
}

.tentang-kami-container {
  display: flex;
  justify-content: center;
  gap: 3rem;
  align-items: center;
  flex-wrap: wrap;
}

.tentang-kami-img {
  max-width: 200px;
  height: auto;
  border-radius: 8px;
}

.tentang-kami-info {
  max-width: 600px;
}

.tentang-kami-info h2 {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.tentang-kami-info p {
  font-size: 1.1rem;
  line-height: 1.6;
}
body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #f5f9ff;
      text-align: center;
      padding: 3rem 2rem;
    }

    header h1 {
      font-size: 2.5rem;
      color: #333;
      margin-bottom: 1rem;
    }
    .hero-image img {
      max-width: 100%;
      height: auto;
    }

    #kontak {
      background-color: #0056b3;
      color: white;
      padding: 2rem;
      text-align: center;
    }

    #kontak h2 {
      font-size: 1.5rem;
      margin-bottom: 1rem;
    }

    #kontak p {
      margin: 0.5rem 0;
    }

    .social-icons a {
      margin: 0 10px;
      color: white;
      font-size: 1.5rem;
      text-decoration: none;
    }

    .footer {
      background-color: #003d82;
      color: white;
      text-align: center;
      padding: 1rem 0;
      font-size: 0.9rem;
    }

  </style>
</head>

<body>
  <!-- Include Navbar -->
  <?php include 'include/navbar_dashboard.php'; ?>

  <!-- Hero Section -->
  <header>
    <div class="hero">
      <div class="hero-text">
        <div class="decorative-boxes">
          <div class="box"></div>
          <div class="box"></div>
          <div class="box"></div>
        </div>
        <h2>Perpustakaan Daerah Kabupaten Nganjuk</h2>
        <p>Sistem Manajemen Perpustakaan Daerah Kabupaten Nganjuk dirancang untuk memudahkan pengelolaan koleksi, data
          pengguna, serta laporan perpustakaan secara efisien, guna mendukung layanan terbaik bagi masyarakat.</p>
      </div>
      <div class="hero-image">
        <img src="../../assets/images/Image-container.png" alt="Library Image" />
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <section id="layanan-perpustakaan">
    <h2>Layanan Perpustakaan</h2>
    <div class="services-container">
      <!-- Card 1 -->
      <div class="service-card" id="service-card">
        <img src="../../assets/images/image 41.png" alt="Peminjaman Buku">
        <h3>Peminjaman Buku</h3>
        <p>Menyediakan koleksi buku cetak lengkap yang dapat dipinjam oleh anggota perpustakaan.</p>
      </div>

      <!-- Card 2 -->
      <div class="service-card">
        <img src="../../assets/images/Vector.png" alt="Layanan Informasi">
        <h3>Layanan Informasi</h3>
        <p>Memberikan bantuan informasi dan akses ke ruang perpustakaan anak, multimedia, dan lainnya.</p>
      </div>

      <!-- Card 3 -->
      <div class="service-card">
        <img src="../../assets/images/icon.png" alt="Layanan Fasilitas Publik">
        <h3>Layanan Fasilitas Publik</h3>
        <p>Fasilitas ruang belajar, toilet, dan internet cepat untuk kenyamanan pengunjung.</p>
      </div>
    </div>

    <section id="slider">
      <div class="slider-container">
        <!-- Slide 1 -->
        <div class="slide">
          <img src="../../assets/images/image 42.png" alt="Peresmian Perpustakaan">
          <div class="blue-box">
            <h3>Peresmian Perpustakaan</h3>
            <p>Perpustakaan Daerah Kabupaten Nganjuk didirikan sebagai pusat literasi dan informasi publik untuk mendukung kemajuan pendidikan dan pengetahuan di Kabupaten Nganjuk. 
Dengan koleksi yang beragam serta fasilitas modern, kami berkomitmen untuk menciptakan masyarakat yang cerdas dan melek informasi.</p>
          </div>
        </div>
        <!-- Slide 2 -->
        <div class="slide">
          <img src="../../assets/images/dinas perpustakaan image 2.png" alt="Tentang Perpustakaan">
          <div class="blue-box">
            <h3>Tentang Perpustakaan</h3>
            <p>Perpustakaan Daerah Kabupaten Nganjuk didirikan sebagai pusat literasi dan informasi publik untuk
              mendukung kemajuan pendidikan dan pengetahuan di Kabupaten Nganjuk.</p>
          </div>
        </div>
      </div>
    </section>
  </section>
  <section id="jam-buka">
  <div class="jam-buka-container">
    <h2>Jam Buka Layanan Perpustakaan</h2>
    <div class="jam-buka">
      <div class="hari">
        <h3>Senin - Kamis</h3>
        <p>08.00 - 15.30 WIB</p>
      </div>
      <div class="hari">
        <h3>Jumat</h3>
        <p>07.30 - 14.30 WIB</p>
      </div>
      <div class="hari">
        <h3>Sabtu - Minggu</h3>
        <p>08.00 - 16.30 WIB</p>
      </div>
    </div>
  </div>
</section>

<section id="tentang-kami">
  <div class="tentang-kami-container">
    <img src="../../assets/images/Logo_Navbar.png" alt="Logo PerpusDig" class="tentang-kami-img">
    <div class="tentang-kami-info">
      <h2>Tentang Kami</h2>
      <p>PerpusDig adalah aplikasi perpustakaan digital yang dirancang khusus untuk mempermudah masyarakat Kabupaten Nganjuk dalam mengakses layanan perpustakaan daerah secara daring.</p>
      <p>Aplikasi ini menghadirkan solusi modern untuk kebutuhan informasi dan literasi di era digital.</p>
    </div>
  </div>
</section>
  <section id="fitur">
    <h2>Fitur Utama</h2>
    <div class="features">
      <div class="feature">
        <h3>Online Catalog</h3>
        <p>Pencarian koleksi perpustakaan secara online.</p>
      </div>
      <div class="feature">
        <h3>E-Library</h3>
        <p>Akses buku digital dari perangkat Anda.</p>
      </div>
      <div class="feature">
        <h3>Mobile Friendly</h3>
        <p>Dapat digunakan melalui aplikasi mobile.</p>
      </div>
    </div>
  </section>
  <body>
  <header>
    <h1>Membaca Lebih Mudah, Ilmu Tanpa Batas</h1>
    <p>Temukan kemudahan membaca dan nikmati perjalanan literasi tanpa batas bersama PerpusDig.</p>
    <div class="hero-image">
      <img src="../../assets/images/Mockup.png" alt="Aplikasi PerpusDig" />
    </div>
  </header>
  <footer id="kontak">
    <h2>Kontak</h2>
    <p>Phone: 081234567890</p>
    <p>Email: tirfpolije.c@gmail.com</p>
    <div class="social-icons">
      <a href="#"><i class="fab fa-facebook"></i></a>
      <a href="#"><i class="fab fa-linkedin"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
    </div>
  </footer>

  <div class="footer">
    <p>&copy; 2024 PerpusDig - Kelompok 3 Teknik Informatika - Politeknik Negeri Jember</p>
  </div>
</body>

</html>