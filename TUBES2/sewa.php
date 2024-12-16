<?php
session_start();
include 'db.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login!']);
    exit;
}

// Ambil data JSON
$input = json_decode(file_get_contents('php://input'), true);

$item_id = $input['item_id'];
$jumlah_hari = $input['jumlah_hari'];
$total_harga = $input['total_harga'];
$username = $_SESSION['username'];

// Simpan ke tabel penyewaan
$sql = "INSERT INTO penyewaan (username, item_id, jumlah_hari, total_harga) VALUES ('$username', '$item_id', '$jumlah_hari', '$total_harga')";
if ($conn->query($sql)) {
    // Perbarui status produk menjadi 'tersewa'
    $update_sql = "UPDATE alat_outdoor SET status = 'tersewa' WHERE id = '$item_id'";
    if ($conn->query($update_sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status produk']);
    }
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
?>
