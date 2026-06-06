<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'method_not_allowed']);
    exit;
}

if (trim((string) ($_POST['honey'] ?? '')) !== '') {
    echo json_encode(['success' => true]);
    exit;
}

$name = trim((string) ($_POST['fromName'] ?? ''));
$email = trim((string) ($_POST['fromEmail'] ?? ''));
$company = trim((string) (($_POST['message'] ?? [])['company'] ?? ''));
$message = trim((string) (($_POST['message'] ?? [])['message'] ?? ''));

$errors = [];
if ($name === '') {
    $errors[] = 'name';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'email';
}
if ($message === '') {
    $errors[] = 'message';
}

if ($errors !== []) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'validation', 'fields' => $errors]);
    exit;
}

$payload = [
    'submitted_at' => gmdate('c'),
    'name' => $name,
    'email' => $email,
    'company' => $company,
    'message' => $message,
    'ip' => (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
    'user_agent' => (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''),
];

$storageDir = dirname(__DIR__, 2) . '/storage/contact';
if (!is_dir($storageDir) && !mkdir($storageDir, 0755, true) && !is_dir($storageDir)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'storage_unavailable']);
    exit;
}

$logFile = $storageDir . '/submissions.jsonl';
$written = file_put_contents($logFile, json_encode($payload, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);

if ($written === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'write_failed']);
    exit;
}

$to = (string) ($site['contact_to_email'] ?? '');
if ($to !== '' && function_exists('mail')) {
    $subject = 'Roots Agency contact — ' . $name;
    $body = "Name: {$name}\nEmail: {$email}\nCompany: {$company}\n\n{$message}\n";
    $headers = 'From: noreply@' . preg_replace('/^www\./', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
    @mail($to, $subject, $body, $headers);
}

echo json_encode(['success' => true]);
