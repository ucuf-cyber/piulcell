<?php
// PHP built-in server router for /app/piulcell/public
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files from /assets and /uploads
if (preg_match('#^/(assets|uploads)/#', $uri)) {
    $file = realpath(__DIR__ . '/..' . $uri);
    if ($file && file_exists($file) && is_file($file)) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $mimes = [
            'css'=>'text/css','js'=>'application/javascript','json'=>'application/json',
            'png'=>'image/png','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','gif'=>'image/gif',
            'svg'=>'image/svg+xml','webp'=>'image/webp','ico'=>'image/x-icon',
            'pdf'=>'application/pdf','woff'=>'font/woff','woff2'=>'font/woff2',
            'zip'=>'application/zip','gz'=>'application/gzip','tar'=>'application/x-tar',
        ];
        if (isset($mimes[$ext])) header('Content-Type: '.$mimes[$ext]);
        // Force download for archive files
        if (in_array($ext, ['zip','gz','tar'])) {
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        }
        readfile($file);
        return true;
    }
    http_response_code(404);
    return true;
}

// Serve public archive files with force download
if (preg_match('#\.(zip|gz|tar)$#i', $uri)) {
    $file = realpath(__DIR__ . $uri);
    if ($file && file_exists($file)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        return true;
    }
}

// Serve public files (favicon etc.)
if ($uri !== '/' && file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    return false; // serve as-is
}

// Route everything else through index.php
require __DIR__ . '/index.php';
