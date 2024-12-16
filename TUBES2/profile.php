<?php
session_start();
include 'db.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect jika belum login
    exit();
}

// Ambil data pengguna dari session
$username = $_SESSION['username'];

// Ambil data pengguna dari database
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $new_username = $_POST['new_username'];
    $new_password = $_POST['new_password'];
    $current_password = md5($_POST['current_password']); // Enkripsi password lama

    // Validasi password lama
    if ($current_password === $user['password']) {
        // Jika password lama benar, update username dan password
        if (!empty($new_username)) {
            $sql_update_username = "UPDATE users SET username = '$new_username' WHERE username = '$username'";
            $conn->query($sql_update_username);
            $_SESSION['username'] = $new_username; // Update session username
            $username = $new_username; // Update variable username
        }

        if (!empty($new_password)) {
            $new_password_encrypted = md5($new_password); // Enkripsi password baru
            $sql_update_password = "UPDATE users SET password = '$new_password_encrypted' WHERE username = '$username'";
            $conn->query($sql_update_password);
            $success_message = "Nama akun dan password berhasil diperbarui!";
        }
    } else {
        $error_message = "Password saat ini salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Rental Outdoor</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .profile-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 400px;
            text-align: center;
        }

        .profile-container h2 {
            margin-bottom: 20px;
            color: #004c8c;
        }

        .profile-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .profile-container button {
            width: 100%;
            padding: 12px;
            background-color: #004c8c;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 10px; /* Adding space between buttons */
        }

        .profile-container button:hover {
            background-color: #005a9c;
        }

        .error-message {
            color: red;
            margin-bottom: 20px;
        }

        .success-message {
            color: green;
            margin-bottom: 20px;
        }

        .link-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        .link-group a {
            display: inline-block;
            padding: 12px;
            background-color: #004c8c;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
            font-size: 16px;
        }

        .link-group a:hover {
            background-color: #005a9c;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            background-color: #004c8c;
            color: white;
        }

        .navbar a {
            color: white;
            text-decoration: none;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        /* Styling for the link group */
        .link-group a {
            display: block;
            padding: 10px 0;
            color: #004c8c;
            text-decoration: none;
            font-size: 16px;
            text-align: center;
            border-radius: 4px;
        }

        .link-group a:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>

    <!-- Profile Form -->
    <div class="profile-container">
        <h2>Profile Anda</h2>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="current_password">Password Saat Ini:</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_username">Nama Pengguna Baru (opsional):</label>
            <input type="text" id="new_username" name="new_username" value="<?php echo htmlspecialchars($username); ?>">

            <label for="new_password">Password Baru (opsional):</label>
            <input type="password" id="new_password" name="new_password">

            <button type="submit">Perbarui Akun</button>
        </form>

        <!-- Link Logout -->
            <a href="index_user.php">Home |</a>
            <a href="logout.php">Logout</a>
        
    </div>

</body>
</html>
