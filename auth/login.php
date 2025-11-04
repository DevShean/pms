<?php
session_start();
require '../config/config.php';

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, role_id, full_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['full_name'] = $user['full_name'];

            // Redirect per role
            switch ($user['role_id']) {
                case 1: header("Location: ../roles/admin/index.php"); break;
                case 2: header("Location: ../roles/officer/index.php"); break;
                case 3: header("Location: ../roles/medical/index.php"); break;
                case 4: header("Location: ../roles/rehab/index.php"); break;
                case 5: header("Location: ../roles/visitor/index.php"); break;
                default:
                    echo "<script>alert('Invalid role!'); window.location='login.php';</script>";
                    break;
            }
            exit;
        } else {
            echo "<script>alert('Incorrect password!');</script>";
        }
    } else {
        echo "<script>alert('No account found with that email!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <img src="../assets/img/logo.png" alt="Prison Management System Logo" class="mx-auto mb-4 w-20 h-20">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
        <form action="" method="POST" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit"
                style="background-color:#059669;"
                class="w-full text-white py-2 px-4 rounded-md 
                        hover:opacity-90 focus:outline-none focus:ring-2 
                        focus:ring-offset-2 transition">
                Login
            </button>

        </form>
        <p class="mt-4 text-center text-sm text-gray-600">Don’t have an account? <a href="register.php" class="text-indigo-600 hover:text-indigo-500">Register here</a>.</p>
    </div>
</body>
</html>
