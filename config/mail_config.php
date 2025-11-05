<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // adjust if needed

function sendEmail($toEmail, $toName, $subject, $bodyHTML) {
    $mail = new PHPMailer(true);

    try {
        
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'deniseclaire.lledo@evsu.edu.ph';  // your Gmail or EVSU account
        $mail->Password   = 'phsx sibn kmqo fwej';              // your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        $mail->Encoding   = 'base64';

        $mail->setFrom('deniseclaire.lledo@evsu.edu.ph', 'Prison Management System');
        $mail->addReplyTo('deniseclaire.lledo@evsu.edu.ph', 'Prison Management System');
        $mail->addAddress($toEmail, $toName);

        // Optional: unique Message-ID
        $mail->MessageID = '<' . uniqid() . '@evsu.edu.ph>';

        $mail->isHTML(true);
        $mail->Subject = $subject;

        // Main HTML layout (no duplicate greeting)
        $emailTemplate = "
        <html>
        <body style='font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;'>
            <div style='max-width: 600px; margin: 0 auto; background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);'>
                <h2 style='color: #2c3e50; margin-bottom: 5px;'>Prison Management System</h2>
                <hr style='border: none; border-top: 2px solid #27ae60; width: 60px; margin: 10px 0;'>
                
                <div style='font-size: 15px; color: #333; line-height: 1.6;'>
                    {$bodyHTML}
                </div>

                <p style='margin-top: 25px; font-size: 14px; color: #555;'>
                    Regards,<br>
                    <strong>Prison Management System</strong><br>
                    <a href='mailto:deniseclaire.lledo@evsu.edu.ph' style='color:#27ae60;'>deniseclaire.lledo@evsu.edu.ph</a>
                </p>
            </div>
        </body>
        </html>";

        $mail->Body    = $emailTemplate;
        $mail->AltBody = strip_tags($bodyHTML); // plain text fallback

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
