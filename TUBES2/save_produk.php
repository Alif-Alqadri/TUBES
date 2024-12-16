<?php
include 'db.php'; // Menghubungkan ke database

// Cek apakah form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    // Variabel untuk menyimpan nama gambar
    $imageName = "";

    // Jika ada file gambar baru yang diupload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $imageName = time() . '_' . $image['name'];
        $imageTmp = $image['tmp_name'];
        $imagePath = 'images/' . $imageName;

        // Pindahkan gambar ke folder images
        if (move_uploaded_file($imageTmp, $imagePath)) {
            // Jika file gambar berhasil di-upload, gambar baru akan digunakan
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal meng-upload gambar.']);
            exit;
        }
    } else {
        // Jika tidak ada gambar baru, ambil gambar lama dari database
        if ($id) {
            // Ambil gambar lama dari database jika mengedit produk
            $sql = "SELECT image FROM alat_outdoor WHERE id = $id";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $imageName = $row['image']; // Gunakan gambar lama jika tidak ada gambar baru
        } else {
            // Jika tidak ada gambar dan ini adalah produk baru, gunakan gambar default
            $imageName = 'default-image.png';
        }
    }

    // Jika ID ada, update produk, jika tidak, tambahkan produk baru
    if ($id) {
        // Update produk yang ada dengan gambar lama (jika tidak ada gambar baru)
        $sql = "UPDATE alat_outdoor SET name = '$name', price = '$price', image = '$imageName' WHERE id = $id";
    } else {
        // Jika ID tidak ada, berarti produk baru, maka insert produk baru
        $sql = "INSERT INTO alat_outdoor (name, price, image) VALUES ('$name', '$price', '$imageName')";
    }

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Produk berhasil disimpan!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $conn->error]);
    }
}
?>
