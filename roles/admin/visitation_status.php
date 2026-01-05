<?php
require '../../includes/session_check.php';
require '../../config/config.php';
// require '../../config/mail_config.php'; // Commented out due to composer issue

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'];

    // Fetch visitor + inmate data first
    $sql = "SELECT u.email, u.full_name, i.first_name AS inmate_first, i.last_name AS inmate_last, v.scheduled_date
            FROM visitations v
            JOIN inmates i ON v.inmate_id = i.inmate_id
            JOIN visitors vis ON v.visitor_id = vis.visitor_id
            JOIN users u ON vis.user_id = u.user_id
            WHERE v.visit_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if ($data) {
        $visitorEmail = $data['email'];
        $visitorName  = $data['full_name'];
        $inmateName   = $data['inmate_first'] . ' ' . $data['inmate_last'];
        $visitDate    = date('F j, Y', strtotime($data['scheduled_date']));

        // Update visitation status in DB
        $update = $conn->prepare("UPDATE visitations SET status = ? WHERE visit_id = ?");
        $update->bind_param("si", $status, $id);
        $update->execute();

        // Log the action
        $action = ucfirst($status) . " visitation";
        $details = "Visitation request for inmate $inmateName on $visitDate was $status.";
        $user_id = $_SESSION['user_id'] ?? null;
        $conn->query("INSERT INTO system_logs (action, details, user_id) VALUES ('$action', '$details', $user_id)");

        // Define color + message per status
        if ($status === 'Approved') {
            $color = '#27ae60';
            $icon  = '✅';
            $statusMsg = "Your visitation request has been <strong style='color:{$color};'>approved</strong>. 
                          Please arrive on your scheduled date with a valid ID for verification.";
        } else {
            $color = '#e74c3c';
            $icon  = '❌';
            $statusMsg = "Your visitation request has been <strong style='color:{$color};'>denied</strong>. 
                          For inquiries, you may contact the facility administration for more information.";
        }

        // Email subject + formatted HTML
        $subject = "Visitation Request " . ucfirst($status);
        $bodyHTML = "
            <p>Dear <strong>{$visitorName}</strong>,</p>
            <p>Your visitation request for <strong>{$inmateName}</strong> scheduled on
            <strong>{$visitDate}</strong> has been processed.</p>
            <p>{$icon} {$statusMsg}</p>
            <p>Thank you for your understanding and cooperation.</p>
        ";

        // Send email
        // sendEmail($visitorEmail, $visitorName, $subject, $bodyHTML); // Commented out due to composer issue
    }

    header("Location: visitation.php?msg=Status updated successfully");
    exit();
}
?>
