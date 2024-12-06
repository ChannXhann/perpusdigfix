<style>
    /* Tombol Unduh Aplikasi */
    .btn-download {
        background-color: transparent;
        /* Warna default transparan */
        border: 2px solid #4CAF50;
        /* Border hijau */
        color: #4CAF50;
        /* Teks hijau */
        padding: 10px 20px;
        border-radius: 10px;
        /* Membuat tombol oval */
        font-size: 14px;
        font-weight: bold;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
        /* Animasi saat hover */
    }

    /* Hover Effect */
    .btn-download:hover {
        background-color: #4CAF50;
        /* Hijau penuh */
        color: white;
        /* Teks putih */
        transform: scale(1.05);
        /* Sedikit membesar */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        /* Bayangan */
    }

    /* Modal Background */
    .modal {
        display: none;
        /* Default disembunyikan */
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
        /* Background transparan */
    }

    /* Modal Content */
    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border-radius: 8px;
        width: 80%;
        max-width: 400px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    /* Close Button */
    .modal .close {
        color: #aaa;
        float: right;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
    }

    .modal .close:hover,
    .modal .close:focus {
        color: #000;
        text-decoration: none;
    }
</style>
<nav class="navbar">
    <!-- Logo -->
    <div class="logo">
        <a href="index.php">
            <img src="../../assets/images/Logo_Navbar.png" alt="Logo PerpusDig">
        </a>
    </div>

    <!-- Menu Navbar -->
    <div class="menu">
        <a href="../../index.php/#service-card">Layanan</a>
        <a href="../../index.php/#tentang-kami">Tentang Kami</a>
        <a href="../../index.php/#fitur">Fitur</a>
        <a href="../../index.php/#kontak">Kontak</a>

    </div>

    <!-- Tombol Masuk -->
    <a href="../config/download.php" class="btn-download">
        <i class="fas fa-download"></i> Unduh Aplikasi PerpusDig
    </a>
    <a href="../login.php" class="btn-login">Masuk</a>
</nav>

<!-- Modal -->
<div id="downloadModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Anda berhasil mengunduh aplikasi PerpusDig!</p>
    </div>
</div>

<!-- JavaScript -->
<script>
    const modal = document.getElementById("downloadModal");
    const closeModal = document.querySelector(".modal .close");

    document.querySelector(".btn-download").addEventListener("click", function (event) {
        setTimeout(() => {
            modal.style.display = "block";
        }, 500);
    });

    closeModal.addEventListener("click", function () {
        modal.style.display = "none";
    });

    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
</script>