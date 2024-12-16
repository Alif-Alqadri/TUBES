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
    <title>Kelola Produk - Rental Outdoor</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Style untuk body */
        body {
            background: url('<?php echo $background_image; ?>') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
        }

        /* Produk item container */
        .content {
            margin: 20px;
        }

        /* Grid layout untuk produk, 3 kolom */
        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        /* Setiap item produk */
        .product-item {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: white;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Gambar produk */
        .product-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        /* Info produk */
        .product-info {
            margin-bottom: 10px;
        }

        .product-info h3 {
            font-size: 18px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .product-info p {
            font-size: 14px;
            color: #555;
            margin: 0;
        }

        /* Tombol status produk */
        .status-button {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            margin-top: 10px;
        }

        .status-button.available {
            background-color: #28a745;
        }

        .status-button.unavailable {
            background-color: #dc3545;
        }

        /* Kotak pencarian */
        .search-box {
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .search-box input {
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 70%;
        }

        .search-box button {
            padding: 5px 10px;
            font-size: 14px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
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
            <li><button onclick="window.location.href='index_admin.php'">Tentang Kami</button></li>
            <li><button onclick="window.location.href='produk2.php'">Produk </button></li>
            <li><button onclick="window.location.href='kelola.php'">Kelola </button></li>
            <li><button onclick="window.location.href='logout.php'">Logout</button></li>
        </ul>
    </div>

    <!-- Content -->
    <div class="content">
    <h2>Kelola Produk</h2>

    <!-- Daftar Produk -->
    <div class="product-list">
        <?php
        // Fetch items based on search query or all items
        if ($search_query) {
            $sql = "SELECT * FROM alat_outdoor WHERE name LIKE '%$search_query%' OR description LIKE '%$search_query%'";
        } else {
            $sql = "SELECT * FROM alat_outdoor";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $status_button_class = $row['status'] == 'tersewa' ? 'unavailable' : 'available';
                $status_text = $row['status'] == 'tersewa' ? 'Tersewa' : 'Tersedia';
                echo "
                <div class='product-item'>
                    <img src='images/" . $row['image'] . "' alt='" . $row['name'] . "' />
                    <div class='product-info'>
                        <h3>" . $row['name'] . "</h3>
                        <p>" . $row['description'] . "</p>
                    </div>
                    <form method='POST' style='display:inline;'>
                        <input type='hidden' name='product_id' value='" . $row['id'] . "'>
                        <button type='submit' name='update_status' class='status-button " . $status_button_class . "'>
                            " . $status_text . "
                        </button>
                    </form>
                </div>";
            }
        } else {
            echo "<p>Tidak ada produk outdoor yang tersedia.</p>";
        }

        // Handle status update
        if (isset($_POST['update_status'])) {
            $product_id = $_POST['product_id'];
            $current_status_sql = "SELECT status FROM alat_outdoor WHERE id = $product_id";
            $current_status_result = $conn->query($current_status_sql);
            $current_status = $current_status_result->fetch_assoc()['status'];

            // Toggle status
            $new_status = ($current_status == 'tersewa') ? 'tersedia' : 'tersewa';

            $update_status_sql = "UPDATE alat_outdoor SET status = '$new_status' WHERE id = $product_id";
            if ($conn->query($update_status_sql)) {
                echo "<script>alert('Status produk berhasil diperbarui!');</script>";
                echo "<script>window.location.href='kelola.php';</script>"; // Refresh the page
            } else {
                echo "<script>alert('Terjadi kesalahan saat memperbarui status produk.');</script>";
            }
        }
        ?>
    </div>
</body>
</html>
