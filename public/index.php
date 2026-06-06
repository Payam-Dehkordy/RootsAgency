<?php
declare(strict_types=1);

require __DIR__ . '/../app/Support/bootstrap.php';

bootstrap_public_page($site, 'home');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && (($_POST['action'] ?? '') === 'contact-form/send')) {
    require __DIR__ . '/../app/Handlers/contact-form.php';
    exit;
}

$host = $_SERVER['HTTP_HOST'] ?? '';
$isLocalPreview = str_contains($host, '127.0.0.1') || str_contains($host, 'localhost');
if ($isLocalPreview) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

$bodyPath = dirname(__DIR__) . '/app/Views/pages/home/rhythm-influence-body.php';

if (!is_readable($bodyPath)) {
    http_response_code(500);
    echo 'Template body missing. Run dev capture workflow first.';
    exit;
}

require dirname(__DIR__) . '/app/Views/partials/head.php';
require $bodyPath;
?>
</body>
</html>
