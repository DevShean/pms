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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #059669 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(5, 150, 105, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            top: -100px;
            left: -100px;
            animation: float 6s ease-in-out infinite;
            z-index: 1;
        }

        body::after {
            content: '';
            position: fixed;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(15, 23, 42, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -150px;
            right: -100px;
            animation: float 8s ease-in-out infinite reverse;
            z-index: 1;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) translateX(0px);
            }
            50% {
                transform: translateY(50px) translateX(30px);
            }
        }

        .container {
            position: relative;
            z-index: 10;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 50px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.6s ease-out;
            transition: all 0.3s ease;
        }

        .login-card:hover {
            box-shadow: 0 30px 80px rgba(5, 150, 105, 0.4);
            transform: translateY(-5px);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 30px;
            display: block;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        h2 {
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease-out backwards;
        }

        .form-group:nth-child(1) { animation-delay: 0.2s; }
        .form-group:nth-child(2) { animation-delay: 0.4s; }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            background: #f8fafc;
            transition: all 0.3s ease;
            color: #1e293b;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #059669;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
            transform: translateY(-2px);
        }

        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: #94a3b8;
        }

        .submit-btn {
            width: 100%;
            padding: 14px 20px;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.3);
            animation: slideUp 0.6s ease-out 0.6s backwards;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.3s ease;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(5, 150, 105, 0.4);
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        .footer-text {
            margin-top: 25px;
            text-align: center;
            font-size: 14px;
            color: #64748b;
        }

        .footer-text a {
            color: #059669;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .footer-text a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #059669;
            transition: width 0.3s ease;
        }

        .footer-text a:hover {
            color: #047857;
        }

        .footer-text a:hover::after {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <img src="../assets/img/logo.png" alt="Prison Management System Logo" class="logo">
            <h2>Login</h2>
            <p class="subtitle">Welcome back to Prison Management System</p>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="submit-btn">Login</button>
            </form>
            <p class="footer-text">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
