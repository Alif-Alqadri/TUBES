<?php
include 'db.php';

$id = $_GET['id'];

$sql = "SELECT * FROM alat_outdoor WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode([]);
}
?>