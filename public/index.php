<?php
declare(strict_types=1);

require __DIR__ . '/../app/Support/app-init.php';

bootstrap_public_page($site, 'home');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && (($_POST['action'] ?? '') === 'contact-form/send')) {
    require __DIR__ . '/../app/Handlers/contact-form.php';
    exit;
}

if (roots_is_local_preview()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

$bodyPath = dirname(__DIR__) . '/app/Views/pages/home/home-body.php';

if (!is_readable($bodyPath)) {
    http_response_code(500);
    echo 'Homepage body missing.';
    exit;
}

require dirname(__DIR__) . '/app/Views/partials/head.php';
require $bodyPath;
?>
</body>
</html>
