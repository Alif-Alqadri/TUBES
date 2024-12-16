<?php
include 'db.php';

// Fetch Background Image from Database
$sql_bg = "SELECT value FROM settings WHERE name = 'background_image'";
$result_bg = $conn->query($sql_bg);
$background_image = $result_bg->num_rows > 0 ? $result_bg->fetch_assoc()['value'] : '';

// Fetch Logo from Database
$sql_logo = "SELECT value FROM settings WHERE name = 'logo'";
$result_logo = $conn->query($sql_logo);
$logo = $result_logo->num_rows > 0 ? $result_logo->fetch_assoc()['value'] : 'default-logo.png'; // Fallback to default logo if not found

// Handle search query
$search_query = '';
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
    // Sanitize the search input to prevent SQL injection
    $search_query = $conn->real_escape_string($search_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Rental Outdoor</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Dynamic Background */
        body {
            background: url('<?php echo $background_image; ?>') no-repeat center center fixed;
            background-size: cover;
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

        <!-- Search Box -->
        <form method="POST" class="search-box">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Cari alat outdoor...">
            <button type="submit">Cari</button>
        </form>

        <!-- Sidebar Menu -->
        <ul>
            <li><button onclick="window.location.href='index.php'">Tentang Kami</button></li>
            <li><button onclick="window.location.href='produk.php'">Produk Kami</button></li>
            <li><button onclick="window.location.href='promo.php'">Promo</button></li>
            <li><button onclick="window.location.href='login.php'">Login</button></li>
        </ul>
    </div>

    <!-- Content -->
    <div class="content">
        <h2>Tentang Kami</h2>
        <p>
            Rental Outdoor adalah penyedia jasa sewa perlengkapan outdoor berkualitas untuk berbagai kegiatan seperti camping, hiking, hingga perjalanan petualangan lainnya. Kami menawarkan berbagai jenis alat outdoor, mulai dari tenda, sleeping bag, hingga alat masak portable yang dapat menunjang kenyamanan aktivitas Anda di alam bebas. Semua perlengkapan yang kami sediakan telah teruji dan dijaga dalam kondisi terbaik untuk memastikan pengalaman outdoor Anda menjadi lebih menyenangkan dan aman.
        </p>

        <!-- Garis pemisah -->
        <div class="separator"></div>

        <?php
        // Fetch items based on search query
        if ($search_query) {
            $sql = "SELECT * FROM outdoor_items WHERE name LIKE '%$search_query%' OR description LIKE '%$search_query%'";
        } else {
            $sql = "SELECT * FROM outdoor_items";
        }
        
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "
                <div class='zigzag'>
                    <img src='images/" . $row['image'] . "' alt='" . $row['name'] . "'>
                    <div class='description'>
                        <h2>" . $row['name'] . "</h2>
                        <p>" . $row['description'] . "</p>
                    </div>
                </div>";
            }
        } else {
            echo "<p>Tidak ada data alat outdoor yang cocok dengan pencarian Anda.</p>";
        }
        ?>
    </div>
</body>
</html>
