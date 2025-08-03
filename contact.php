<?php
// contact.php - Handle contact form submissions
header('Content-Type: application/json');

// Enable CORS if needed
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get form data
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (empty($input['email']) || empty($input['message'])) {
    echo json_encode(['success' => false, 'message' => 'Email and message are required']);
    exit;
}

$firstName = htmlspecialchars(trim($input['firstName'] ?? ''));
$lastName = htmlspecialchars(trim($input['lastName'] ?? ''));
$email = filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL);
$phone = htmlspecialchars(trim($input['phone'] ?? ''));
$message = htmlspecialchars(trim($input['message']));

// Validate email
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
    exit;
}

// Create PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'rickygweth828@gmail.com'; // Your Gmail
    $mail->Password   = 'ktor rcbb velw zsam';        // Your Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom($email, $firstName . ' ' . $lastName);
    $mail->addAddress('rickygweth828@gmail.com', 'Ricky Gweth'); // Your email
    $mail->addReplyTo($email, $firstName . ' ' . $lastName);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'New Contact Form Submission from Portfolio';
    
    $mail->Body = "
    <html>
    <head>
        <title>New Contact Form Submission</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #007bff; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f8f9fa; }
            .field { margin: 10px 0; }
            .label { font-weight: bold; color: #555; }
            .value { margin-left: 10px; }
            .message-box { 
                background: white; 
                padding: 15px; 
                border-left: 4px solid #007bff; 
                margin: 15px 0;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Contact Form Submission</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>Name:</span>
                    <span class='value'>" . $firstName . " " . $lastName . "</span>
                </div>
                <div class='field'>
                    <span class='label'>Email:</span>
                    <span class='value'>" . $email . "</span>
                </div>
                <div class='field'>
                    <span class='label'>Phone:</span>
                    <span class='value'>" . ($phone ?: 'Not provided') . "</span>
                </div>
                <div class='field'>
                    <span class='label'>Message:</span>
                    <div class='message-box'>" . nl2br($message) . "</div>
                </div>
                <p><small>Submitted on: " . date('Y-m-d H:i:s') . "</small></p>
            </div>
        </div>
    </body>
    </html>";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);

} catch (Exception $e) {
    error_log("Contact form error: " . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'message' => 'Sorry, there was an error sending your message. Please try again later.']);
}
?>