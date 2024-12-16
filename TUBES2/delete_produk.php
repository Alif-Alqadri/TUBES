<?php
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

if ($id) {
    $sql = "DELETE FROM alat_outdoor WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Terjadi kesalahan: " . $conn->error]);
    }
}
?>
