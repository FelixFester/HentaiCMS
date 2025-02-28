<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

global $themeFileForHentaiCMS;

// Special theme detection for system pages
if (isset($_COOKIE['hentaicms_theme'])) {
    $themeFileForHentaiCMS = '../themes/' . $_COOKIE['hentaicms_theme'];
} else {
    $themeFileForHentaiCMS = '../themes/default.css'; // Special fallback
}

include 'Parsedown.php';
$Parsedown = new Parsedown();

// Security: Block direct access to core PHP files
$requestedFile = $_SERVER['REQUEST_URI'];
if (preg_match('/\/cms\/.*\.php$/i', $requestedFile)) {
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<link rel="stylesheet" href="' . htmlspecialchars($themeFileForHentaiCMS) . '">';
    echo '<div class="echo-content"><h1>What are you trying to find here?</h1></div>';
    exit();
}

// Maintenance mode
$maintenanceMode = false;

if ($maintenanceMode) {
    $maintenancePage = 'content/maintenance.md';
    if (file_exists($maintenancePage)) {
        $content = fopen($maintenancePage, 'r') or die('File Open Error');
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<link rel="stylesheet" href="' . htmlspecialchars($themeFileForHentaiCMS) . '">';
        echo '<div class="markdown-content">' . $Parsedown->text(fread($content, filesize($maintenancePage))) . '</div>';
        fclose($content);
        exit();
    } else {
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<link rel="stylesheet" href="' . htmlspecialchars($themeFileForHentaiCMS) . '">';
        echo '<div class="echo-content"><h1>Maintenance Mode</h1><p>The site is currently under maintenance. Please check back later.</p></div>';
        exit();
    }
}

$page = isset($_GET['page']) ? trim($_GET['page'], '/ ') : 'home';
$path1 = 'content/' . $page . '/index.md';
$path2 = 'content/' . $page . '.md';

function hentaiShowMarkdown($path) {
    global $Parsedown, $themeFileForHentaiCMS;
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<link rel="stylesheet" href="' . htmlspecialchars($themeFileForHentaiCMS) . '">';
    $content = fopen($path, 'r') or die('File Open Error');
    echo '<div class="markdown-content">' . $Parsedown->text(fread($content, filesize($path))) . '</div>';
    fclose($content);
}

if ($page === 'home' && file_exists('content/index.md')) {
    hentaiShowMarkdown('content/index.md');
} elseif (file_exists($path1)) {
    hentaiShowMarkdown($path1);
} elseif (file_exists($path2)) {
    hentaiShowMarkdown($path2);
} else {
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<link rel="stylesheet" href="' . htmlspecialchars($themeFileForHentaiCMS) . '">';
    echo '<div class="echo-content"><h1>404 - Page Not Found</h1></div>';
}
?>