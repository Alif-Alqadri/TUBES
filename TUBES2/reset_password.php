<?php
session_start();
include 'db.php';

// Validasi token dan email
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_token'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['reset_email'];
$token = $_SESSION['reset_token'];

$sql = "SELECT * FROM users WHERE email = '$email' AND reset_token = '$token'";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "Token tidak valid atau sudah kadaluarsa.";
    exit();
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $new_password = md5($_POST['new_password']);
    $confirm_password = md5($_POST['confirm_password']);

    if ($new_password === $confirm_password) {
        $sql = "UPDATE users SET password = '$new_password', reset_token = NULL WHERE email = '$email'";
        if ($conn->query($sql) === TRUE) {
            echo "<p>Password berhasil direset. Silakan login kembali.</p>";
            session_destroy(); // Hapus session terkait reset password
            header("Location: login.php");
        } else {
            $error = "Terjadi kesalahan saat mereset password.";
        }
    } else {
        $error = "Password tidak cocok.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Rental Outdoor</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .reset-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
        }
        .reset-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #004c8c;
        }
        .reset-container form {
            display: flex;
            flex-direction: column;
        }
        .reset-container input, .reset-container button {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .reset-container button {
            background-color: #004c8c;
            color: white;
            border: none;
            cursor: pointer;
        }
        .reset-container button:hover {
            background-color: #005a9c;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h2>Reset Password</h2>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <form method="POST">
            <input type="password" name="new_password" placeholder="Password Baru" required>
            <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
    </div>
</body>
</html>
