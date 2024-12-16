<?php
session_start(); // Memulai atau melanjutkan sesi
include 'db.php';

// Validasi apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, redirect ke halaman login
    header('Location: login.php');
    exit;
}

// Ambil nama pengguna dari sesi
$username = $_SESSION['username'];

// Ambil informasi pengguna dari database
$sql_user = "SELECT * FROM users WHERE username = '$username'";
$result_user = $conn->query($sql_user);

if ($result_user->num_rows > 0) {
    $user_data = $result_user->fetch_assoc();

} else {
    $fullname = $username; // Default ke username jika nama lengkap tidak ditemukan
}

// Handle search query
$search_query = '';
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
    // Sanitasi input pencarian
    $search_query = $conn->real_escape_string($search_query);
}
$user_logged_in = isset($_SESSION['username']) ? $_SESSION['username'] : '';
// Fetch Background Image and Logo from Database
$sql_bg = "SELECT value FROM settings WHERE name = 'background_image'";
$result_bg = $conn->query($sql_bg);
$background_image = $result_bg->num_rows > 0 ? $result_bg->fetch_assoc()['value'] : '';

$sql_logo = "SELECT value FROM settings WHERE name = 'logo'";
$result_logo = $conn->query($sql_logo);
$logo = $result_logo->num_rows > 0 ? $result_logo->fetch_assoc()['value'] : 'default-logo.png'; // Fallback to default logo if not found
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Kami - Rental Outdoor</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>

        /* Dynamic Background */
        body {
            background: url('<?php echo $background_image; ?>') no-repeat center center fixed;
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
            background-color: white;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .product-item {
            display: flex;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            background-color: white;
        }

        .product-item img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
        }

        .product-item .product-info {
            max-width: 500px;
        }

        .product-item h3 {
            color: #004c8c;
            margin-bottom: 10px;
        }

        .product-item form input[type='number'] {
            padding: 10px;
            margin-right: 10px;
            width: 80px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .product-item form button {
            background-color: #004c8c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            text-align: center;
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
        <form method="POST" class="search-box">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Cari alat outdoor...">
            <button type="submit">Cari</button>
        </form>



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
    <div class="content">
        <h2>Produk Kami</h2>

        <?php
       
        // Fetch products and exclude those already rented
        $sql = "SELECT * FROM alat_outdoor WHERE status = 'tersedia'";
        if ($search_query) {
            $sql = "SELECT * FROM alat_outdoor WHERE name LIKE '%$search_query%' OR description LIKE '%$search_query%'";
        } else {
            $sql = "SELECT * FROM alat_outdoor";
        }
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $isTersewa = $row['status'] == 'tersewa';
                echo "
                <div class='product-item'>
                    <img src='images/" . $row['image'] . "' alt='" . $row['name'] . "'>
                    <div class='product-info'>
                        <h3>" . $row['name'] . "</h3>
                        <p>Harga per hari: Rp " . number_format($row['price'], 0, ',', '.') . "</p>
                        <form id='sewaForm" . $row['id'] . "'>
                            <input type='number' name='jumlah_hari' id='jumlah_hari" . $row['id'] . "' placeholder='Jumlah hari' min='1' required " . ($isTersewa ? 'disabled' : '') . ">
                            <button type='button' class='sewa' data-item-id='" . $row['id'] . "' data-name='" . $row['name'] . "' data-price='" . $row['price'] . "' " . ($isTersewa ? 'disabled' : '') . ">" . ($isTersewa ? 'Tersewakan' : 'Sewa') . "</button>
                        </form>
                    </div>
                </div>";
            }
        } else {
            echo "<p>Tidak ada produk outdoor yang tersedia.</p>";
        }
        ?>
    </div>

    <!-- Modal Pop-Up -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <h3 id="modal-title">Total Harga</h3>
            <p id="modal-total"></p>
            <button id="confirm-button">Konfirmasi</button>
            <button id="cancel-button">Batal</button>
        </div>
    </div>

    <script>
        document.querySelectorAll('.sewa').forEach(button => {
    button.addEventListener('click', function () {
        const itemId = this.dataset.itemId;
        const itemName = this.dataset.name;
        const price = parseInt(this.dataset.price);
        const jumlahHari = document.getElementById('jumlah_hari' + itemId).value;

        if (jumlahHari === '' || jumlahHari <= 0) {
            alert('Masukkan jumlah hari yang valid!');
            return;
        }

        const totalHarga = price * jumlahHari;

        // Tampilkan modal
        const modal = document.getElementById('modal');
        const modalTotal = document.getElementById('modal-total');
        const confirmButton = document.getElementById('confirm-button');

        modalTotal.innerText = `Total untuk ${itemName}: Rp ${new Intl.NumberFormat('id-ID').format(totalHarga)}`;
        modal.style.display = 'flex';

        // Handle Konfirmasi
        confirmButton.onclick = function () {
            modal.style.display = 'none';

            // Kirim data ke server
            fetch('sewa.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    item_id: itemId,
                    jumlah_hari: jumlahHari,
                    total_harga: totalHarga
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Sewa berhasil disimpan ke tabel penyewaan!');

                        // Ubah tombol menjadi "Tersewakan"
                        const button = document.querySelector(`[data-item-id='${itemId}']`);
                        button.textContent = 'Tersewakan';
                        button.disabled = true;

                        // Reload jika diperlukan (opsional)
                        // location.reload();
                    } else {
                        alert('Terjadi kesalahan: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        };

        // Handle Batal
        document.getElementById('cancel-button').onclick = function () {
            modal.style.display = 'none';
        };
    });
});

    </script>
</body>
</html>