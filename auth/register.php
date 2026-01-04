<?php
require '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $relationship = trim($_POST['relationship']);

    // Check if email exists
    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered!');</script>";
    } else {
        // Limit to 5 visitors
        $visitor_count = $conn->query("SELECT COUNT(*) AS total FROM visitors")->fetch_assoc();
        if ($visitor_count['total'] >= 5) {
            echo "<script>alert('Visitor registration limit reached (5 max). Contact admin.');</script>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $role_id = 5; // Visitor role

            $insert_user = $conn->prepare("INSERT INTO users (role_id, full_name, email, password) VALUES (?, ?, ?, ?)");
            $insert_user->bind_param("isss", $role_id, $full_name, $email, $hashed_password);

            if ($insert_user->execute()) {
                $user_id = $conn->insert_id;
                $insert_visitor = $conn->prepare("INSERT INTO visitors (user_id, relationship) VALUES (?, ?)");
                $insert_visitor->bind_param("is", $user_id, $relationship);
                $insert_visitor->execute();

                echo "<script>alert('Registration successful! You can now log in.'); window.location='login.php';</script>";
                exit;
            } else {
                echo "<script>alert('Registration failed. Try again.');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 50px 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.6s ease-out;
            transition: all 0.3s ease;
        }

        .register-card:hover {
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
        .form-group:nth-child(2) { animation-delay: 0.3s; }
        .form-group:nth-child(3) { animation-delay: 0.4s; }
        .form-group:nth-child(4) { animation-delay: 0.5s; }

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
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            background: #f8fafc;
            transition: all 0.3s ease;
            color: #1e293b;
            font-family: inherit;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus {
            outline: none;
            border-color: #059669;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
            transform: translateY(-2px);
        }

        input::placeholder {
            color: #94a3b8;
        }

        select option {
            color: #1e293b;
            background: #f8fafc;
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
        <div class="register-card">
            <img src="../assets/img/logo.png" alt="Prison Management System Logo" class="logo">
            <h2>Visitor Registration</h2>
            <p class="subtitle">Join our Prison Management System</p>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                </div>
                <div class="form-group">
                    <label for="relationship">Relationship to Inmate</label>
                    <select id="relationship" name="relationship" required>
                        <option value="">-- Select Relationship --</option>
                        <option value="Family">Family</option>
                        <option value="Friend">Friend</option>
                        <option value="Lawyer">Lawyer</option>
                        <option value="Spouse">Spouse</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Register</button>
            </form>
            <p class="footer-text">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
