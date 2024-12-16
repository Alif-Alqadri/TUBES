<?php
session_start();
include 'db.php';

// Validasi apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Ambil username dari sesi
$username = $_SESSION['username'];

// Fetch data biodata dari database
$sql_biodata = "SELECT * FROM biodata WHERE username = '$username'";
$result_biodata = $conn->query($sql_biodata);
$biodata = $result_biodata->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = $conn->real_escape_string($_POST['nama_lengkap']);
    $alamat = $conn->real_escape_string($_POST['alamat']);
    $no_hp = $conn->real_escape_string($_POST['no_hp']);
    $email = $conn->real_escape_string($_POST['email']);
    $tanggal_lahir = $conn->real_escape_string($_POST['tanggal_lahir']);

    if ($biodata) {
        // Jika biodata sudah ada, update data
        $sql_update = "
            UPDATE biodata 
            SET 
                nama_lengkap = '$nama_lengkap',
                alamat = '$alamat',
                no_hp = '$no_hp',
                email = '$email',
                tanggal_lahir = '$tanggal_lahir'
            WHERE username = '$username'
        ";
        $conn->query($sql_update);
    } else {
        // Jika biodata belum ada, tambahkan data baru
        $sql_insert = "
            INSERT INTO biodata (username, nama_lengkap, alamat, no_hp, email, tanggal_lahir) 
            VALUES ('$username', '$nama_lengkap', '$alamat', '$no_hp', '$email', '$tanggal_lahir')
        ";
        $conn->query($sql_insert);
    }

    // Redirect dengan parameter success
    header('Location: biodata.php?success=true');
    exit;
}


$user_logged_in = isset($_SESSION['username']) ? $_SESSION['username'] : '';
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
    <title>Biodata Saya - Rental Outdoor</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/default-background.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .content {
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            margin: 20px auto;
            max-width: 800px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        form label {
            margin-top: 10px;
        }
        form input, form textarea {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        form button {
            margin-top: 20px;
            padding: 10px;
            background-color: #004c8c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #003366;
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

    </style>
     <script>
        // Cek jika ada parameter "success" di URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            alert('Biodata berhasil disimpan!');
            // Hapus parameter "success" dari URL setelah ditampilkan
            const newUrl = window.location.href.split('?')[0];
            history.replaceState(null, null, newUrl);
        }
    </script>
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
        <h2>Biodata Saya</h2>
        <form method="POST" action="biodata.php">
            <label for="nama_lengkap">Nama Lengkap:</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo $biodata['nama_lengkap'] ?? ''; ?>" required>

            <label for="alamat">Alamat:</label>
            <textarea id="alamat" name="alamat" rows="3"><?php echo $biodata['alamat'] ?? ''; ?></textarea>

            <label for="no_hp">Nomor HP:</label>
            <input type="text" id="no_hp" name="no_hp" value="<?php echo $biodata['no_hp'] ?? ''; ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $biodata['email'] ?? ''; ?>" required>

            <label for="tanggal_lahir">Tanggal Lahir:</label>
            <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo $biodata['tanggal_lahir'] ?? ''; ?>">

            <button type="submit">Simpan Biodata</button>
        </form>
    </div>
</body>
</html>
