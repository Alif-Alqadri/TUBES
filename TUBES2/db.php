<?php
$host = 'localhost';
$user = 'root'; // Sesuaikan username database
$password = 'alif'; // Sesuaikan password database
$dbname = 'rental_outdoor';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
