<?php
include 'db.php'; // Koneksi ke database

// Fetch data dari tabel 'promotions'
$sql = "SELECT * FROM promotions";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promo - Rental Outdoor</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Header + Sidebar */
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

        /* Konten */
        .content {
            margin: 20px auto;
            width: 90%;
        }

        h2 {
            text-align: center;
            color: white;
            font-size: 28px;
            margin-bottom: 20px;
            animation: fade-in 1s ease-out;
        }

        /* Promo Besar */
        .promo-large {
            background-color: #004c8c;
            color: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
            animation: fade-in-up 1s ease-out;
        }

        .promo-large img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }

        .promo-large h3 {
            font-size: 24px;
            margin-top: 15px;
        }

        .promo-large p {
            font-size: 18px;
            line-height: 1.6;
            color: white; /* Warna deskripsi menjadi putih */
        }

        /* Grid Promo */
        .promo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .promo-item {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .promo-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .promo-item:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
        }

        .promo-item h3 {
            font-size: 18px;
            text-align: center;
            padding: 10px 0;
            color: #004c8c;
        }

        .promo-item p {
            font-size: 14px;
            text-align: center;
            padding: 0 10px 10px;
            color: #555;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
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

    <!-- Konten Promo -->
    <div class="content">
        <h2>Promo Kami</h2>

        <!-- Promo Besar di Atas Grid -->
        <div class="promo-large">
            <?php
            // Menampilkan gambar promo besar jika ada
            $promoLarge = "SELECT * FROM promotions WHERE id = 4"; // ID promo besar
            $resultLarge = $conn->query($promoLarge);
            $rowLarge = $resultLarge->fetch_assoc();
            ?>
            <img src="<?php echo htmlspecialchars($rowLarge['image']); ?>" alt="Promo Besar">
            <h3><?php echo htmlspecialchars($rowLarge['title']); ?></h3>
            <p><?php echo htmlspecialchars($rowLarge['description']); ?></p>
        </div>

        <!-- Grid Promo Lainnya -->
        <div class="promo-grid">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='promo-item'>
                            <img src='" . htmlspecialchars($row['image']) . "' alt='Promo'>
                            <h3>" . htmlspecialchars($row['title']) . "</h3>
                            <p>" . htmlspecialchars($row['description']) . "</p>
                          </div>";
                }
            } else {
                echo "<p style='text-align: center; color: black;'>Belum ada promo tersedia.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
