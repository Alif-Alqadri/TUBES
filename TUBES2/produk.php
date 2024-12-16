<?php
include 'db.php'; // Koneksi ke database

// Fetch data dari tabel 'products'
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Kami - Rental Outdoor</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Styling Header + Sidebar */
        body {
            font-family: 'Roboto', sans-serif;
            background: url('images/default-background.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .header-sidebar {
            background-color: #004c8c;
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header-sidebar h1 {
            font-size: 24px;
        }

        .header-sidebar ul {
            display: flex;
            gap: 15px;
        }

        .header-sidebar ul li {
            list-style: none;
        }

        .header-sidebar ul button {
            background-color: transparent;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .header-sidebar ul button:hover {
            background-color: #005a9c;
            transform: scale(1.05);
        }

        /* Styling Tabel */
        .table-container {
            margin: 20px auto;
            width: 90%;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #004c8c;
            color: white;
            font-size: 18px;
        }

        table td {
            font-size: 16px;
            color: #555;
            cursor: pointer; /* Menambahkan pointer saat hover */
            transition: background-color 0.3s ease;
        }

        table tr:hover {
            background-color: #f2f2f2;
        }

        table img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3); /* Shadow pada gambar */
            transition: transform 0.3s, box-shadow 0.3s;
        }

        table img:hover {
            transform: scale(1.1);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4); /* Animasi shadow */
        }

        h2 {
            text-align: center;
            color: white; /* Warna teks putih */
            margin: 20px 0;
            font-size: 28px;
            font-weight: bold;
            animation: zoom-in 1s ease-out; /* Animasi zoom-in */
        }

        @keyframes zoom-in {
            0% {
                transform: scale(0.5); /* Awal ukuran teks kecil */
                opacity: 0; /* Tidak terlihat */
            }
            100% {
                transform: scale(1); /* Ukuran teks normal */
                opacity: 1; /* Terlihat */
            }
        }
    </style>
    <script>
        // Fungsi untuk menampilkan popup dan mengarahkan ke login
        function showLoginPopup() {
            alert("Harus login terlebih dahulu jika ingin rental alat.");
            window.location.href = 'login.php';
        }
    </script>
</head>
<body>
    <!-- Header dan Sidebar -->
    <div class="header-sidebar">
        <h1>Rental Outdoor</h1>
        <ul>
            <li><button onclick="window.location.href='index.php'">Tentang Kami</button></li>
            <li><button onclick="window.location.href='produk.php'">Produk Kami</button></li>
            <li><button onclick="window.location.href='promo.php'">Promo</button></li>
            <li><button onclick="window.location.href='login.php'">Login</button></li>
        </ul>
    </div>

    <!-- Konten Utama -->
    <div class="content">
        <h2>Produk Kami</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Merek</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr onclick='showLoginPopup()'>
                                    <td><img src='" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "'></td>
                                    <td>" . htmlspecialchars($row['name']) . "</td>
                                    <td>Rp " . number_format($row['price'], 0, ',', '.') . "</td>
                                    <td>" . htmlspecialchars($row['brand']) . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Tidak ada produk tersedia.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
