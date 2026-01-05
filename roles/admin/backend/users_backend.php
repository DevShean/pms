<?php
require '../../../includes/session_check.php';
require '../../../config/config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'An error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $role_id = intval($_POST['role_id']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ($role_id && $role_id !== 5 && !empty($full_name) && !empty($email) && !empty($_POST['password'])) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $response['message'] = 'Email already exists.';
        } else {
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO users (role_id, full_name, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $role_id, $full_name, $email, $password);

            if ($stmt->execute()) {
                $new_user_id = $stmt->insert_id;

                // Fetch the newly created user's data to send back
                $sql = "SELECT u.*, r.role_name 
                        FROM users u 
                        JOIN roles r ON u.role_id = r.role_id 
                        WHERE u.user_id = ?";
                $stmt_fetch = $conn->prepare($sql);
                $stmt_fetch->bind_param("i", $new_user_id);
                $stmt_fetch->execute();
                $result = $stmt_fetch->get_result();
                $new_user = $result->fetch_assoc();

                // Format date
                $new_user['created_at'] = date('M d, Y', strtotime($new_user['created_at']));

                $response['success'] = true;
                $response['message'] = 'User added successfully.';
                $response['user'] = $new_user;

                // Log the action
                $action = "Added new user";
                $details = "User $full_name (ID: $new_user_id) added with role $role_id.";
                $user_id = $_SESSION['user_id'] ?? null;
                $conn->query("INSERT INTO system_logs (action, details, user_id) VALUES ('$action', '$details', $user_id)");
            } else {
                $response['message'] = 'Failed to add user.';
            }
        }
        $stmt->close();
    } else {
        $response['message'] = 'Please fill in all required fields.';
    }
}

echo json_encode($response);
?>
