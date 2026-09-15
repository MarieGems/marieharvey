<?php
// contact-handler.php — backend for the marieharvey.com contact form.
// Receives a POST from contact.html's #contactForm and emails marie@mariegems.com.

header('Content-Type: application/json');

function respond($ok, $error = null) {
    http_response_code($ok ? 200 : 400);
    echo json_encode($ok ? ['ok' => true] : ['ok' => false, 'error' => $error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed.']);
    exit;
}

// Honeypot: real visitors never see or fill this field. Bots that fill every
// field will trip it — pretend success so they don't learn to skip it.
if (!empty($_POST['website'])) {
    respond(true);
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
    respond(false, 'Please fill in your name, email, and message.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'That email address doesn\'t look right.');
}

if (mb_strlen($message) > 5000) {
    respond(false, 'That message is too long — please trim it down.');
}

// Strip anything that could inject extra mail headers.
$name = str_replace(["\r", "\n"], '', $name);
$email = str_replace(["\r", "\n"], '', $email);

$to = 'marie@mariegems.com';
$subject = 'New contact form message from ' . $name;

$body = "You've got a new message from the marieharvey.com contact form.\n\n"
    . "Name: $name\n"
    . "Email: $email\n\n"
    . "Message:\n$message\n\n"
    . '---' . "\n"
    . 'Sent: ' . date('Y-m-d H:i:s') . "\n"
    . 'From IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";

$domain = preg_replace('/^www\./', '', $_SERVER['SERVER_NAME'] ?? 'marieharvey.com');
$fromAddress = 'noreply@' . $domain;

$headers = "From: Marie Harvey Contact Form <$fromAddress>\r\n"
    . "Reply-To: $name <$email>\r\n"
    . "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = mail($to, $subject, $body, $headers);

if (!$sent) {
    respond(false, "Something went wrong sending that — please try again, or email me directly.");
}

respond(true);
