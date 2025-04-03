<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Dynamically determine the root directory
$scriptPath = __DIR__;
$documentRoot = $_SERVER['DOCUMENT_ROOT'];

if (strpos($scriptPath, $documentRoot) === 0) {
    define('ROOT_DIR', $documentRoot);
} else {
    define('ROOT_DIR', $scriptPath);
}

// ──────────────────────────────────────────────────────────────
// Constants and Configuration
// ──────────────────────────────────────────────────────────────

define('THEMES_DIR', ROOT_DIR . '/themes');
define('CONTENT_DIR', ROOT_DIR . '/content');
define('DEFAULT_THEME', 'default.css');
define('MAINTENANCE_FILE', CONTENT_DIR . '/ouch.md');

$maintenanceEnabled = false; // Set this to true to enable maintenance mode

// List of pages that should show NSFW warning
$nsfwPages = [
    'nsfw-stuff',
    'arts/hentai',
    'anime-arts/girl/nsfw',
    'explicit'
];

// Security: Disable PHP info exposure
ini_set('expose_php', 'Off');

include 'Parsedown.php';
$Parsedown = new Parsedown();

// ──────────────────────────────────────────────────────────────
// Theme Detection & Cookies
// ──────────────────────────────────────────────────────────────

if (!is_dir(THEMES_DIR) || !is_readable(THEMES_DIR)) {
    displayHentaiError('Where is "themes" directory?');
    exit();
}

$themes = array_filter(scandir(THEMES_DIR), function ($file) {
    return is_file(THEMES_DIR . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'css';
});

if (empty($themes)) {
    displayHentaiError('Add some themes in "themes" folder already!');
    exit();
}

$themeCookie = $_COOKIE['hentaicms_theme'] ?? '';
$themeFile = in_array($themeCookie, $themes) ? '/themes/' . $themeCookie : '/themes/' . DEFAULT_THEME;

// Ensure $themeFile is never null
$themeFile = $themeFile ?: '/themes/' . DEFAULT_THEME;

// ──────────────────────────────────────────────────────────────
// Maintenance Mode Logic
// ──────────────────────────────────────────────────────────────

if ($maintenanceEnabled) {
    if (file_exists(MAINTENANCE_FILE)) {
        $maintenanceContent = file_get_contents(MAINTENANCE_FILE);
        if (!empty(trim($maintenanceContent))) {
            displayHentaiMarkdown(MAINTENANCE_FILE, $Parsedown);
            exit();
        } else {
            displayHentaiError('Maintenance page is empty.');
            exit();
        }
    } else {
        displayHentaiError('Maintenance page is not found.');
        exit();
    }
}

// ──────────────────────────────────────────────────────────────
// Page Rendering
// ──────────────────────────────────────────────────────────────

if (preg_match('/\/cms\/hentaicms\.php$/i', $_SERVER['REQUEST_URI'])) {
    displayHentaiError('What are you trying to find here?');
    exit();
}

$page = isset($_GET['page']) ? preg_replace('/[^a-zA-Z0-9\-\/]/', '', trim($_GET['page'], '/ ')) : 'home';

// Check if the requested page is the maintenance page and not in maintenance mode
if ($page === 'ouch' && !$maintenanceEnabled) {
    displayHentaiError('404 - Page Not Found');
    exit();
}

$markdownPaths = [
    CONTENT_DIR . '/' . $page . '/index.md',
    CONTENT_DIR . '/' . $page . '.md',
];

if ($page === 'home' && file_exists(CONTENT_DIR . '/index.md')) {
    displayHentaiMarkdown(CONTENT_DIR . '/index.md', $Parsedown);
    exit();
}

foreach ($markdownPaths as $path) {
    if (file_exists($path)) {
        // Check if this is an NSFW page and not maintenance.md or index.md
        $filename = basename($path);
        if (in_array($page, $nsfwPages) && $filename !== 'maintenance.md' && $filename !== 'index.md') {
            displayNsfwWarning($path, $Parsedown);
            exit();
        }
        displayHentaiMarkdown($path, $Parsedown);
        exit();
    }
}

displayHentaiError('404 - Page Not Found');

// ──────────────────────────────────────────────────────────────
// Functions
// ──────────────────────────────────────────────────────────────

function displayNsfwWarning($path, $Parsedown) {
    global $themeFile;
    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<link rel="stylesheet" href="ヘンタイ CSS フレームワーク.css">';
    echo '<link rel="stylesheet" href="' . htmlspecialchars($themeFile, ENT_QUOTES, 'UTF-8') . '">';
    echo '<script>';
    echo 'function proceedToPage() {';
    echo '  document.getElementById("warning").style.display = "none";';
    echo '  document.getElementById("content").style.display = "block";';
    echo '}';
    echo '</script>';
    echo '</head>';
    echo '<body>';
    echo '<div id="warning" style="text-align: center; padding: 20px;">';
    echo '<h1>NSFW CONTENT WARNING</h1>';
    echo '<p>This page contains adult content. Are you sure you want to proceed?</p>';
    echo '<button onclick="proceedToPage()">Yes</button> <a href="https://www.youtube.com/watch?v=cd5QuZq5jmg"><button>No</button></a>';
    echo '</div>';
    echo '<div id="content" style="display: none;">';
    echo '<div class="markdown-content">' . $Parsedown->text(file_get_contents($path)) . '</div>';
    echo '<div class="footer"><p>' . htmlspecialchars(getHentaiFooterText(), ENT_QUOTES, 'UTF-8') . '</p></div>';
    echo '</div>';
    echo '</body></html>';
}

function displayHentaiError($message) {
    displayHentaiHeader();
    echo '<div class="echo-content"><h1>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</h1></div>';
    displayHentaiFooter();
}

function displayHentaiMarkdown($path, $Parsedown) {
    if (!file_exists($path)) {
        displayHentaiError('404 - Page Not Found');
        return;
    }

    $content = file_get_contents($path);
    if (empty(trim($content))) {
        displayHentaiError('Did you make an empty index.md page in "content" folder or what?');
        return;
    }

    displayHentaiHeader();
    echo '<div class="markdown-content">' . $Parsedown->text($content) . '</div>';
    displayHentaiFooter();
}

function displayHentaiHeader() {
    global $themeFile;
    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<link rel="stylesheet" href="ヘンタイ CSS フレームワーク.css">';
    echo '<link rel="stylesheet" href="' . htmlspecialchars($themeFile, ENT_QUOTES, 'UTF-8') . '">';
    echo '</head>';
    echo '<body>';
}

function displayHentaiFooter() {
    echo '<div class="footer"><p>' . htmlspecialchars(getHentaiFooterText(), ENT_QUOTES, 'UTF-8') . '</p></div>';
    echo '</body></html>';
}

function getHentaiFooterText() {
    return 'Powered by Hentai CMS';
}
?>