<?php
session_start();
include 'db.php'; // Koneksi ke database

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // Login User/Admin
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $role = $_POST['role'];

        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password' AND role = '$role'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Login berhasil, set session
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            
            // Arahkan ke halaman yang sesuai berdasarkan peran
            if ($role === 'user') {
                header("Location: index_user.php"); // Redirect ke halaman user
            } else if ($role === 'admin') {
                header("Location: index_admin.php"); // Redirect ke halaman admin
            }
            exit();
        } else {
            $error = "Username atau password salah!";
        }
    }

    if (isset($_POST['register'])) {
        // Daftar User
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', 'user')";
        if ($conn->query($sql) === TRUE) {
            $success = "Pendaftaran berhasil. Silakan login!";
        } else {
            $error = "Pendaftaran gagal. Silakan coba lagi.";
        }
    }

    if (isset($_POST['forgot_password'])) {
        // Lupa Password
        $email = $_POST['email'];
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Email ditemukan, generate token untuk reset password
            $token = md5(uniqid(rand(), true)); // Generate unique token
            $updateToken = "UPDATE users SET reset_token = '$token' WHERE email = '$email'";
            if ($conn->query($updateToken) === TRUE) {
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_token'] = $token;
                header("Location: reset_password.php");
                exit();
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi.";
            }
        } else {
            $error = "Email tidak ditemukan.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom right, #004c8c, #0066cc);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .login-container {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #004c8c;
        }

        .login-container input,
        .login-container select,
        .login-container button {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-container button {
            background: #004c8c;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .login-container button:hover {
            background: #005fa3;
        }

        .login-container select {
            background: #f4f4f4;
            cursor: pointer;
        }

        .login-container select:focus {
            outline: 2px solid #004c8c;
        }

        .login-container .links {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-top: 10px;
        }

        .login-container .links a {
            color: #004c8c;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .login-container .links a:hover {
            text-decoration: underline;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            width: 350px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            transform: scale(0.8);
            animation: scaleUp 0.3s ease-in-out;
        }

        @keyframes scaleUp {
            from {
                transform: scale(0.8);
            }
            to {
                transform: scale(1);
            }
        }

        .modal-content h3 {
            color: #004c8c;
            margin-bottom: 20px;
        }

        .modal-content input,
        .modal-content button {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .modal-content button {
            background: #004c8c;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .modal-content button:hover {
            background: #005fa3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="" disabled selected>Pilih Role</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" name="login">Login</button>
        </form>
        <div class="links">
            <a href="#" onclick="showModal('forgot-password-modal')">Lupa Password</a>
            <a href="#" onclick="showModal('register-modal')">Daftar Akun</a>
        </div>
    </div>

    <!-- Modal Lupa Password -->
    <div id="forgot-password-modal" class="modal">
        <div class="modal-content">
            <h3>Lupa Password</h3>
            <form method="POST">
                <input type="email" name="email" placeholder="Masukkan Email" required>
                <button type="submit" name="forgot_password">Kirim</button>
            </form>
            <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        </div>
    </div>

    <!-- Modal Daftar Akun -->
    <div id="register-modal" class="modal">
        <div class="modal-content">
            <h3>Daftar Akun</h3>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="register">Daftar</button>
            </form>
        </div>
    </div>

    <script>
        function showModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach((modal) => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        };
    </script>
</body>
</html>
