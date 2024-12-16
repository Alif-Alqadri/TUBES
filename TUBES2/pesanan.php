<?php
session_start();
include 'db.php';

// Validasi apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Ambil nama pengguna dari sesi
$username = $_SESSION['username'];

// Ambil data pesanan berdasarkan username
$sql_pesanan = "
    SELECT p.id, p.tanggal_sewa, p.status, p.total_harga, i.name AS item_name, i.image AS item_image 
    FROM penyewaan p
    JOIN alat_outdoor i ON p.item_id = i.id
    WHERE p.username = '$username'
    ORDER BY p.tanggal_sewa DESC
";

$result_pesanan = $conn->query($sql_pesanan);

if (!$result_pesanan) {
    die("Error pada query: " . $conn->error);
}
$user_logged_in = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Fetch Background Image and Logo from Database
$sql_bg = "SELECT value FROM settings WHERE name = 'background_image'";
$result_bg = $conn->query($sql_bg);
$background_image = $result_bg->num_rows > 0 ? $result_bg->fetch_assoc()['value'] : 'default-bg.jpg';

$sql_logo = "SELECT value FROM settings WHERE name = 'logo'";
$result_logo = $conn->query($sql_logo);
$logo = $result_logo->num_rows > 0 ? $result_logo->fetch_assoc()['value'] : 'default-logo.png';

// Validasi apakah file logo tersedia
if (!file_exists("images/$logo")) {
    $logo = 'default-logo.png'; // Fallback jika file logo tidak ditemukan
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Rental Outdoor</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: url('images/default-background.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f1f1f1;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content a {
            padding: 8px 16px;
            text-decoration: none;
            display: block;
            color: black;
            text-align: left;
        }

        .dropdown-content a:hover {
            background-color: #ddd;
        }

        

        .content {
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            margin: 20px auto;
            max-width: 900px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .content h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .order-item {
        display: flex;
        margin-bottom: 20px;
        padding: 15px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        align-items: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .order-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        background-color: #f9f9f9; /* Warna latar sedikit lebih terang */
    }


        .order-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
        }

        .order-info {
            flex: 1;
        }

        .order-info h3 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #004c8c;
        }

        .order-info p {
            margin: 5px 0;
            color: #333;
        }

        .status {
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            display: inline-block;
        }

        .status.diproses {
            background-color: #3498db;
        }

        .status.selesai {
            background-color: #2ecc71;
        }

        .status.batal {
            background-color: #e74c3c;
        }

        .order-actions {
            margin-top: 10px;
        }

        .order-actions button {
            background-color: #004c8c;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .order-actions button:hover {
            background-color: #003366;
        }
    </style>
</head>
<body>
     <!-- Header with Sidebar -->
     <div class="header-sidebar">
        <!-- Logo -->
        <img src="images/<?php echo $logo; ?>" alt="Logo" />
        <!-- Title -->
        <h1>Rental Outdoor</h1>

        

        <!-- Sidebar Menu -->
        <ul>
            <li><button onclick="window.location.href='index_user.php'">Tentang Kami</button></li>
            <li><button onclick="window.location.href='produk1.php'">Produk Kami</button></li>
            <li><button onclick="window.location.href='Pesanan.php'">Pesanan</button></li>
            <!-- Account Dropdown with User's Name -->
            <?php if ($user_logged_in): ?>
                <li class="dropdown">
                    <button class="dropbtn"><?php echo $user_logged_in; ?> </button>
                    <div class="dropdown-content">
                        <a href="profile.php">Profile</a>
                        <a href="biodata.php">Biodata</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </li>
            <?php else: ?>
                <li><button onclick="window.location.href='login.php'">Akun</button></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Content -->
    <div class="content">
        <h2>Riwayat Pesanan saya</h2>

        <?php if ($result_pesanan->num_rows > 0): ?>
            <?php while ($row = $result_pesanan->fetch_assoc()): ?>
                <div class="order-item">
                    <img src="images/<?php echo $row['item_image']; ?>" alt="<?php echo $row['item_name']; ?>">
                    <div class="order-info">
                        <h3><?php echo $row['item_name']; ?></h3>
                        <p>Tanggal Sewa: <?php echo $row['tanggal_sewa']; ?></p>
                        <p>Total Harga: Rp<?php echo number_format($row['total_harga'], 0, ',', '.'); ?></p>
                        <span class="status <?php echo strtolower($row['status']); ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Tidak ada pesanan yang ditemukan.</p>
        <?php endif; ?>
    </div>
</body>
</html>
