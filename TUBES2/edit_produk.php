<?php
session_start();
include 'db.php';

// Validasi apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image'];

    if (empty($name) || empty($price)) {
        echo "Nama dan harga produk harus diisi!";
        exit;
    }

    // Ambil gambar lama dari database jika ada
    $oldImage = '';
    if ($id) {
        $sql = "SELECT image FROM alat_outdoor WHERE id = $id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $oldImage = $row['image'];
        }
    }

    $imagePath = $oldImage; // Default ke gambar lama jika tidak ada gambar baru yang diunggah

    if (isset($image) && $image['size'] > 0) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($image["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validasi ukuran dan tipe gambar
        if ($image["size"] > 500000) {
            echo "Ukuran file terlalu besar!";
            exit;
        }

        if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            echo "Hanya file gambar yang diizinkan!";
            exit;
        }

        // Pindahkan gambar yang diupload ke folder images
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            $imagePath = $target_file; // Gunakan gambar yang baru diupload
        } else {
            echo "Terjadi kesalahan saat mengunggah gambar!";
            exit;
        }
    }

    // Update data produk, jika gambar diubah maka update gambar baru
    $sql = "UPDATE alat_outdoor SET name = '$name', price = '$price', image = '$imagePath' WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Produk berhasil diperbarui!";
    } else {
        echo "Terjadi kesalahan: " . $conn->error;
    }
}
?>
