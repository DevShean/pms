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
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <img src="../assets/img/logo.png" alt="Prison Management System Logo" class="mx-auto mb-4 w-20 h-20">
        <h2 class="text-2xl font-bold text-center mb-6">Visitor Registration</h2>
        <form action="" method="POST" class="space-y-4">
            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name:</label>
                <input type="text" id="full_name" name="full_name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="relationship" class="block text-sm font-medium text-gray-700">Relationship to Inmate:</label>
                <select id="relationship" name="relationship" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Select Relationship --</option>
                    <option value="Family">Family</option>
                    <option value="Friend">Friend</option>
                    <option value="Lawyer">Lawyer</option>
                    <option value="Spouse">Spouse</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <button type="submit"
                style="background-color:#059669;"
                class="w-full text-white py-2 px-4 rounded-md 
                        hover:opacity-90 focus:outline-none focus:ring-2 
                        focus:ring-offset-2 transition">
                Register
            </button>
        </form>
        <p class="mt-4 text-center text-sm text-gray-600">Already have an account? <a href="login.php" class="text-indigo-600 hover:text-indigo-500">Login here</a>.</p>
    </div>
</body>
</html>
