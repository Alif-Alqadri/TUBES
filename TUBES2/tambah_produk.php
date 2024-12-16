<?php
session_start();
include 'db.php';

// Validasi apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_FILES['image'];

    // Validasi input
    if (empty($name) || empty($price) || !isset($image)) {
        echo "Semua field wajib diisi!";
        exit;
    }

    // Proses upload gambar
    $target_dir = "images/";
    $target_file = $target_dir . basename($image["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if ($image["size"] > 500000) {
        echo "Ukuran file terlalu besar!";
        exit;
    }

    if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
        echo "Hanya file gambar yang diizinkan!";
        exit;
    }

    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        // Masukkan data ke dalam database
        $sql = "INSERT INTO alat_outdoor (name, price, image, status) VALUES ('$name', '$price', '$target_file', 'tersedia')";
        if ($conn->query($sql) === TRUE) {
            echo "Produk berhasil ditambahkan!";
        } else {
            echo "Terjadi kesalahan: " . $conn->error;
        }
    } else {
        echo "Terjadi kesalahan saat mengunggah gambar!";
    }
}
?>
