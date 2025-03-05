<?php
// Enable error reporting for debugging
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Dynamically determine the root directory
$scriptPath = __DIR__; // Directory of the current script (e.g., /www/cms)
$documentRoot = $_SERVER['DOCUMENT_ROOT']; // Web server's document root (e.g., /www)

// If the script is in a subdirectory (e.g., /www/cms), adjust the root directory
if (strpos($scriptPath, $documentRoot) === 0) {
    define('ROOT_DIR', $documentRoot);
} else {
    define('ROOT_DIR', $scriptPath);
}

// Define the themes directory
define('THEMES_DIR', ROOT_DIR . '/themes');

// Security: Ensure the 'themes' directory exists and is readable
if (!file_exists(THEMES_DIR) || !is_dir(THEMES_DIR)) {
    hentaiHeader();
    echo '<div class="echo-content"><h1>Where is "themes" directory?</h1></div>';
    hentaiFooter();
    exit();
}

// Detect available themes
$themes = scandir(THEMES_DIR);
if ($themes === false) {
    hentaiHeader();
    echo '<div class="echo-content"><h1>Sorry, but I cannot scan "themes" folder.</h1></div>';
    hentaiFooter();
    exit();
}

$themes = array_filter($themes, function ($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'css';
});

if (empty($themes)) {
    hentaiHeader();
    echo '<div class="echo-content"><h1>Add some themes in "themes" folder already!</h1></div>';
    hentaiFooter();
    exit();
}

// Default theme
$defaultTheme = 'default.css';
$defaultThemePath = '/themes/' . $defaultTheme;

// Cookie-based theme selection
if (isset($_COOKIE['hentaicms_theme']) && in_array($_COOKIE['hentaicms_theme'], $themes)) {
    $themeFileForHentaiCMS = '/themes/' . $_COOKIE['hentaicms_theme'];
} else {
    $themeFileForHentaiCMS = $defaultThemePath;
}

// Header
function hentaiHeader() {
    global $themeFileForHentaiCMS;
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<link rel="stylesheet" href="https://unpkg.com/sakura.css/css/sakura.css">'; 
    echo '<link rel="stylesheet" href="' . htmlspecialchars($themeFileForHentaiCMS, ENT_QUOTES, 'UTF-8') . '">'; 
}

// Footer function
function hentaiFooter() {
    echo '<div class="footer"><p>' . htmlspecialchars(getFooterText(), ENT_QUOTES, 'UTF-8') . '</p></div>';
}

// Footer text
function getFooterText() {
    return 'Powered by Hentai CMS';
}

// Handle markdown content
include 'Parsedown.php';
$Parsedown = new Parsedown();

// Block direct access to hentaicms.php
$requestedFile = $_SERVER['REQUEST_URI'];
if (preg_match('/\/cms\/hentaicms\.php$/i', $requestedFile)) {
    hentaiHeader();
    echo '<div class="echo-content"><h1>What are you trying to find here?</h1></div>';
    hentaiFooter();
    exit();
}

// Sanitize the 'page' parameter
$page = isset($_GET['page']) ? trim($_GET['page'], '/ ') : 'home';
$safePage = preg_replace('/[^a-zA-Z0-9\-\/]/', '', $page);

// Define paths for markdown files
$contentDir = ROOT_DIR . '/content';
$path1 = $contentDir . '/' . $safePage . '/index.md';
$path2 = $contentDir . '/' . $safePage . '.md';

// Check for maintenance mode
$maintenanceFile = $contentDir . '/maintenance.md';
$maintenanceEnabled = false;

if (file_exists($maintenanceFile)) {
    $maintenanceContent = file_get_contents($maintenanceFile);
    if (trim($maintenanceContent) === 'true') {
        $maintenanceEnabled = true;
    }
}

// Function to render markdown content
function hentaiShowMarkdown($path) {
    global $Parsedown;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        if (empty(trim($content))) {
            hentaiHeader();
            echo '<div class="echo-content"><h1>Did you make an empty index.md page in "content" folder or what?</h1></div>';
            hentaiFooter();
        } else {
            hentaiHeader();
            echo '<div class="markdown-content">' . $Parsedown->text($content) . '</div>';
            hentaiFooter();
        }
    } else {
        hentaiHeader();
        echo '<div class="echo-content"><h1>404 - Page Not Found</h1></div>';
        hentaiFooter();
    }
}

// If maintenance mode is enabled, show the maintenance page
if ($maintenanceEnabled) {
    hentaiShowMarkdown($maintenanceFile);
} else {
    // Handle home page or specific pages
    if ($safePage === 'home') {
        $homePagePath = $contentDir . '/index.md';
        if (file_exists($homePagePath)) {
            hentaiShowMarkdown($homePagePath);
        } else {
            hentaiHeader();
            echo '<div class="echo-content"><h1>Where is index.md page in "content" folder?</h1></div>';
            hentaiFooter();
        }
    } elseif (file_exists($path1)) {
        hentaiShowMarkdown($path1);
    } elseif (file_exists($path2)) {
        hentaiShowMarkdown($path2);
    } else {
        hentaiHeader();
        echo '<div class="echo-content"><h1>404 - Page Not Found</h1></div>';
        hentaiFooter();
    }
}
?>