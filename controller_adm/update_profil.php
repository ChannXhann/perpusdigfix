<?php
session_start();

// Pastikan koneksi sudah benar
include '../config/koneksi.php';
$db = new Database();
$conn = $db->koneksi;

$nip = $_SESSION['nip'];

// Ambil data lama untuk memastikan tidak hilang setelah update
$query = "SELECT email, nama, no_telp, foto FROM admin WHERE nip = :nip";
$stmt = $conn->prepare($query);
$stmt->bindParam(':nip', $nip, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Cek apakah form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $email = $_POST['email'] ?? $user['email'];  // Gunakan nilai lama jika kosong
    $name = $_POST['name'] ?? $user['nama'];     // Gunakan nilai lama jika kosong
    $phone = $_POST['phone'] ?? $user['no_telp']; // Gunakan nilai lama jika kosong

    // Validasi email
    if ($email !== $user['email']) {
        // Cek apakah email sudah terdaftar
        $query = "SELECT COUNT(*) FROM admin WHERE email = :email";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $email_count = $stmt->fetchColumn();

        if ($email_count > 0) {
            // Jika email sudah terdaftar, beri pesan error dalam format JSON
            echo json_encode(['status' => 'error', 'message' => 'Email sudah terdaftar. Silakan gunakan email lain.']);
            exit();
        }
    }
    
    // Periksa apakah ada file foto yang diupload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        // Ambil konten foto dan update foto
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
    } else {
        // Jika tidak ada foto baru, gunakan foto lama
        $foto = $user['foto'];
    }

    // Query untuk update data admin
    $query = "UPDATE admin SET email = :email, nama = :name, no_telp = :phone";
    
    // Jika ada foto baru, tambahkan ke query
    if ($foto !== null) {
        $query .= ", foto = :foto";
    }

    // Tambahkan kondisi WHERE berdasarkan NIP
    $query .= " WHERE nip = :nip";

    // Persiapkan query
    $stmt = $conn->prepare($query);
    
    // Bind parameter untuk query
    $stmt->bindParam(':nip', $nip, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    
    // Jika ada foto, bind parameter foto
    if ($foto !== null) {
        $stmt->bindParam(':foto', $foto, PDO::PARAM_LOB);
    }

    // Eksekusi query
    if ($stmt->execute()) {
        
        // Jika berhasil, perbarui session dengan data terbaru

        $_SESSION['nama'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['no_telp'] = $phone;

        // Jika berhasil, kirimkan response sukses dalam format JSON
        echo json_encode(['status' => 'success', 'message' => 'Profil berhasil diperbarui.']);
    } else {
        // Jika gagal, kirimkan response error dalam format JSON
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui profil.']);
    }

    exit(); // Menghentikan eksekusi setelah mengirimkan response
}
?>