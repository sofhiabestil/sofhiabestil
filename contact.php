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
$message = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

if (!$name || !$email || !$message) {
    resp(false, 'Please fill in name, valid email, and message.');
}

$to = 'hello@yourdomain.com';
$subject = 'Portfolio contact from ' . $name;
$body = "Name: $name\nEmail: $email\n\nMessage:\n$message\n";
$headers = "From: $email\r\nReply-To: $email\r\n";

$sent = false;
if (function_exists('mail')) {
    $sent = @mail($to, $subject, $body, $headers);
}

if ($sent) {
    resp(true, 'Message sent successfully.');
} else {
    // Fallback: save to messages.txt for local development
    $log = date('c') . "\t" . $name . "\t" . $email . "\t" . str_replace("\n", "\\n", $message) . "\n";
    @file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'messages.txt', $log, FILE_APPEND | LOCK_EX);
    resp(true, 'Message received (mail not configured).');
}
