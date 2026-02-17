<?php
header('Content-Type: application/json; charset=utf-8');

function resp($ok, $msg){
    echo json_encode(['success' => $ok, 'message' => $msg]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    resp(false, 'Invalid request method.');
}

$name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$subjectInput = trim((string) filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$message = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

if (!$name || !$email || !$subjectInput || !$message) {
    resp(false, 'Please fill in name, valid email, subject, and message.');
}

$to = 'sofhiabestil@gmail.com';
$subject = 'Portfolio inquiry: ' . $subjectInput;
$body = "Name: $name\nEmail: $email\nSubject: $subjectInput\n\nMessage:\n$message\n";
$headers = "From: $email\r\nReply-To: $email\r\n";

$sent = false;

// Load PHPMailer automatically if Composer autoload exists.
$autoload = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

// SMTP-ready settings via environment variables.
$mailHost = getenv('MAIL_HOST') ?: '';
$mailPort = (int) (getenv('MAIL_PORT') ?: 587);
$mailUser = getenv('MAIL_USERNAME') ?: '';
$mailPass = getenv('MAIL_PASSWORD') ?: '';
$mailFrom = getenv('MAIL_FROM') ?: $mailUser;
$mailFromName = getenv('MAIL_FROM_NAME') ?: 'Portfolio Contact Form';

if (
    $mailHost !== '' &&
    $mailUser !== '' &&
    $mailPass !== '' &&
    class_exists('\PHPMailer\PHPMailer\PHPMailer')
) {
    try {
        $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = $mailHost;
        $mailer->Port = $mailPort;
        $mailer->SMTPAuth = true;
        $mailer->Username = $mailUser;
        $mailer->Password = $mailPass;
        $mailer->SMTPSecure = ($mailPort === 465)
            ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
            : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mailer->CharSet = 'UTF-8';

        $mailer->setFrom($mailFrom, $mailFromName);
        $mailer->addAddress($to);
        $mailer->addReplyTo($email, $name);
        $mailer->Subject = $subject;
        $mailer->Body = $body;

        $sent = $mailer->send();
    } catch (Throwable $e) {
        $sent = false;
    }
}

if (!$sent && function_exists('mail')) {
    $sent = @mail($to, $subject, $body, $headers);
}

if ($sent) {
    resp(true, 'Message sent successfully.');
} else {
    // Fallback: save to messages.txt for local development
    $log = date('c') . "\t" . $name . "\t" . $email . "\t" . $subjectInput . "\t" . str_replace("\n", "\\n", $message) . "\n";
    @file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'messages.txt', $log, FILE_APPEND | LOCK_EX);
    resp(true, 'Message received (mail not configured).');
}
