<?php
session_start(); // Memulai sesi
include 'db.php'; // Menghubungkan ke database

// Validasi apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
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

// Fetch Background Image and Logo from Database
$sql_bg = "SELECT value FROM settings WHERE name = 'background_image'";
$result_bg = $conn->query($sql_bg);
$background_image = $result_bg->num_rows > 0 ? $result_bg->fetch_assoc()['value'] : '';

$sql_logo = "SELECT value FROM settings WHERE name = 'logo'";
$result_logo = $conn->query($sql_logo);
$logo = $result_logo->num_rows > 0 ? $result_logo->fetch_assoc()['value'] : 'default-logo.png'; // Fallback ke default logo jika tidak ditemukan
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

        /* Modal Styles */
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
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .modal.show {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            width: 400px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            animation: modalIn 0.3s ease-out;
        }

        @keyframes modalIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        #modal-title {
            font-size: 24px;
            color: #004c8c;
            margin-bottom: 20px;
        }

        input[type="text"], input[type="number"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }

        input[type="text"]:focus, input[type="number"]:focus, input[type="file"]:focus {
            border-color: #004c8c;
            outline: none;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 10px 0;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        button[type="button"] {
            background-color: #dc3545;
        }

        button[type="button"]:hover {
            background-color: #c82333;
        }

        button[type="button"].close {
            background-color: transparent;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 15px;
            border: none;
            cursor: pointer;
        }

        button[type="button"].close:hover {
            color: black;
        }

        /* Responsiveness */
        @media screen and (max-width: 600px) {
            .modal-content {
                width: 90%;
            }
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
            <li><button onclick="window.location.href='index_admin.php'">Tentang Kami</button></li>
            <li><button onclick="window.location.href='produk2.php'">Produk</button></li>
            <li><button onclick="window.location.href='Kelola.php'">Kelola</button></li>
            <li><button onclick="window.location.href='logout.php'">Logout</button></li>
        </ul>
    </div>

    <!-- Content -->
    <div class="content">
        <h2>Produk Kami</h2>

        <?php
        // Fetch products from database
        $sql = "SELECT * FROM alat_outdoor";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "
                <div class='product-item' id='product" . $row['id'] . "'>
                    <img src='images/" . $row['image'] . "' alt='" . $row['name'] . "'>
                    <div class='product-info'>
                        <h3>" . $row['name'] . "</h3>
                        <p>Harga per hari: Rp " . number_format($row['price'], 0, ',', '.') . "</p>
                        <button onclick='editProduct(" . $row['id'] . ")'>Edit</button>
                        <button onclick='deleteProduct(" . $row['id'] . ")'>Delete</button>
                    </div>
                </div>";
            }
        } else {
            echo "<p>Tidak ada produk outdoor yang tersedia.</p>";
        }
        ?>
        <button onclick="showAddProductModal()">Tambah Produk</button>
    </div>

    <!-- Modal Pop-Up -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <button type="button" class="close" onclick="closeModal()">×</button>
            <h3 id="modal-title">Tambah Produk</h3>
            <form id="productForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="productId">
                <input type="text" name="name" id="productName" placeholder="Nama Produk" required>
                <input type="number" name="price" id="productPrice" placeholder="Harga" required>
                <input type="file" name="image" id="productImage" accept="image/*" required>
                <button type="submit">Simpan</button>
                <button type="button" onclick="closeModal()">Batal</button>
            </form>
        </div>
    </div>

    <script>
        // Show modal for adding new product
        function showAddProductModal() {
            document.getElementById('modal-title').textContent = "Tambah Produk";
            document.getElementById('productForm').reset();
            document.getElementById('modal').classList.add('show'); // Menambahkan class 'show' untuk memunculkan modal
        }

        // Show modal for editing existing product
        function editProduct(id) {
            document.getElementById('modal-title').textContent = "Edit Produk";
            fetch('get_produk.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('productId').value = data.id;
                    document.getElementById('productName').value = data.name;
                    document.getElementById('productPrice').value = data.price;
                    document.getElementById('modal').classList.add('show'); // Menambahkan class 'show' untuk memunculkan modal
                });
        }

        // Delete product
        function deleteProduct(id) {
            if (confirm("Apakah Anda yakin ingin menghapus produk ini?")) {
                fetch('delete_produk.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('product' + id).remove();
                        alert("Produk berhasil dihapus!");
                    } else {
                        alert("Terjadi kesalahan: " + data.message);
                    }
                });
            }
        }

        // Close modal
        function closeModal() {
            document.getElementById('modal').classList.remove('show'); // Menghapus class 'show' untuk menyembunyikan modal
        }

      // Handle form submission (add/edit product)
document.getElementById('productForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('save_produk.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Tampilkan konfirmasi sukses
            alert(data.message);
            closeModal();  // Tutup modal setelah berhasil
            location.reload(); // Refresh halaman setelah berhasil
        } else {
            alert("Terjadi kesalahan: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

    </script>
</body>
</html>
