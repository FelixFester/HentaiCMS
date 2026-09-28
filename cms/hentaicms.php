<?php
// Enable error reporting for debugging (uncomment for development)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Dynamically determine the root directory
$scriptPath = __DIR__;
$documentRoot = $_SERVER['DOCUMENT_ROOT'];

if (strpos($scriptPath, $documentRoot) === 0) {
    define('ROOT_DIR', $documentRoot);
} else {
    define('ROOT_DIR', $scriptPath);
}

// Prevent direct access to hentaicms.php
if (preg_match('/\/cms\/hentaicms\.php$/i', $_SERVER['REQUEST_URI'])) {
    header('Location: /index.php');
    exit();
}

// ──────────────────────────────────────────────────────────────
// Constants and Configuration
// ──────────────────────────────────────────────────────────────

define('THEMES_DIR', ROOT_DIR . '/themes');
define('CONTENT_DIR', ROOT_DIR . '/content');
define('PLUGINS_DIR', ROOT_DIR . '/plugins');
define('DEFAULT_THEME', 'default.css');
define('MAINTENANCE_FILE', CONTENT_DIR . '/maintenance.md');

$maintenanceEnabled = false; // Set this to true to enable maintenance mode

// List of pages that should show NSFW warning
$nsfwPages = [
    'explicity',
];

// Security: Disable PHP info exposure
ini_set('expose_php', 'Off');

include 'Parsedown.php';
$Parsedown = new Parsedown();

// ──────────────────────────────────────────────────────────────
// Run Captcha before anything else
// Additional pseudo-ddos protection
// ──────────────────────────────────────────────────────────────

// Define minimal functions needed for captcha
function displayHentaiHeaderMinimal() {
    // Use default theme for captcha since theme detection hasn't run yet
    $defaultTheme = defined('DEFAULT_THEME') ? DEFAULT_THEME : 'default.css';
    $themePath = '/themes/' . htmlspecialchars($defaultTheme, ENT_QUOTES, 'UTF-8');
    
    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<link rel="stylesheet" href="ヘンタイ CSS フレームワーク.css">';
    echo '<link rel="stylesheet" href="' . $themePath . '">';
    echo '</head>';
    echo '<body>';
}

function displayHentaiFooterMinimal() {
    echo '<div class="footer"><p>Powered by Hentai CMS</p></div>';
    echo '</body></html>';
}

// Check for captcha plugin and run it before everything
$captchaPluginPath = PLUGINS_DIR . '/captcha.php';
if (file_exists($captchaPluginPath)) {
    // Don't run captcha if user is trying to access the plugin directly
    $isTryingToAccessCaptcha = 
        (isset($_GET['plugin']) && $_GET['plugin'] === 'captcha') ||
        (isset($_GET['captcha']));
    
    if (!$isTryingToAccessCaptcha) {
        // Define minimal constant for captcha
        if (!defined('HENTAI_CMS_PLUGIN')) {
            define('HENTAI_CMS_PLUGIN', true);
        }
        
        // Start output buffering to capture captcha behavior
        ob_start();
        include $captchaPluginPath;
        $captchaOutput = ob_get_clean();
        
        // If captcha returned the "does not exist" signal, continue
        if ($captchaOutput === 'PLUGIN_DOES_NOT_EXIST') {
            // Continue with normal processing
        } elseif (!empty($captchaOutput)) {
            // Captcha produced output (showing captcha), display it and exit
            echo $captchaOutput;
            exit();
        }
        // If no output (cookie exists), continue normally
    }
}

// Now continue with normal CMS initialization...

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
// Plugin System
// ──────────────────────────────────────────────────────────────

$activePlugins = [];
if (is_dir(PLUGINS_DIR) && is_readable(PLUGINS_DIR)) {
    $pluginFiles = array_filter(scandir(PLUGINS_DIR), function ($file) {
        return is_file(PLUGINS_DIR . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php';
    });
    
    foreach ($pluginFiles as $file) {
        $pluginName = pathinfo($file, PATHINFO_FILENAME);
        $activePlugins[$pluginName] = PLUGINS_DIR . '/' . $file;
    }
}

// Prevent direct access to plugin files
if (preg_match('/\/plugins\/.*\.php$/i', $_SERVER['REQUEST_URI'])) {
    displayHentaiError('Direct access to plugin files is not allowed.');
    exit();
}

// ──────────────────────────────────────────────────────────────
// Define remaining functions
// ──────────────────────────────────────────────────────────────

function displayHentaiHeader() {
    global $themeFile;
    // Make sure $themeFile is not null
    $themePath = $themeFile ?? '/themes/' . (defined('DEFAULT_THEME') ? DEFAULT_THEME : 'default.css');
    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<link rel="stylesheet" href="ヘンタイ CSS フレームワーク.css">';
    echo '<link rel="stylesheet" href="' . htmlspecialchars($themePath, ENT_QUOTES, 'UTF-8') . '">';
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

// ──────────────────────────────────────────────────────────────
// Maintenance Mode Logic (AFTER captcha check)
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

$pluginRequested = null;

// Check for standard plugin parameter first
if (isset($_GET['plugin'])) {
    $pluginRequested = preg_replace('/[^a-zA-Z0-9\-]/', '', trim($_GET['plugin']));
} else {
    // Check ALL GET parameters to see if any match a plugin name
    foreach ($_GET as $key => $value) {
        // Skip 'page' parameter as it's for normal pages
        if ($key === 'page') continue;
        
        // Check if this parameter key matches a plugin name
        if (array_key_exists($key, $activePlugins)) {
            $pluginRequested = $key;
            break;
        }
        
        // Also check the legacy behavior (empty value)
        if (empty($value) && array_key_exists($key, $activePlugins)) {
            $pluginRequested = $key;
            break;
        }
    }
}

if ($pluginRequested) {
    if (isset($activePlugins[$pluginRequested])) {
        // Only define if not already defined
        if (!defined('HENTAI_CMS_PLUGIN')) {
            define('HENTAI_CMS_PLUGIN', true); // Signal plugin is loaded via CMS
        }
        
        // Store the original parameter for the plugin to use
        $pluginParam = $_GET[$pluginRequested] ?? null;
        
        // Start output buffering to capture any plugin output
        ob_start();
        include $activePlugins[$pluginRequested];
        $pluginOutput = ob_get_clean();
        
        // Check if plugin returned "does not exist" signal
        if ($pluginOutput === 'PLUGIN_DOES_NOT_EXIST') {
            // Treat as if plugin doesn't exist - show home page
            $page = 'home';
        } elseif (!empty($pluginOutput)) {
            // Plugin produced output, display it
            echo $pluginOutput;
            exit();
        } else {
            // Plugin didn't produce output (blank page), show home page
            $page = 'home';
        }
    } else {
        // If plugin doesn't exist, show home page
        $page = 'home';
    }
}

// Regular page handling
if (!isset($page)) {
    $page = isset($_GET['page']) ? preg_replace('/[^a-zA-Z0-9\-\/]/', '', trim($_GET['page'], '/ ')) : 'home';
}

// Check if the requested page is the maintenance page and not in maintenance mode
if ($page === 'maintenance' && !$maintenanceEnabled) {
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
// Remaining Functions
// ──────────────────────────────────────────────────────────────

function displayNsfwWarning($path, $Parsedown) {
    global $themeFile;
    // Make sure $themeFile is not null
    $themePath = $themeFile ?? '/themes/' . (defined('DEFAULT_THEME') ? DEFAULT_THEME : 'default.css');
    
    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<link rel="stylesheet" href="ヘンタイ CSS フレームワーク.css">';
    echo '<link rel="stylesheet" href="' . htmlspecialchars($themePath, ENT_QUOTES, 'UTF-8') . '">';
    echo '<script>';
    echo 'function proceedToPage() {';
    echo '  document.getElementById("warning").style.display = "none";';
    echo '  document.getElementById("content").style.display = "block";';
    echo '}';
    echo '</script>';
    echo '<body>';
    echo '<div id="warning" style="text-align: center; padding: 20px;">';
    echo '<h1>NSFW CONTENT WARNING</h1>';
    echo '<p>This page contains adult content. As detected, you have JavaScript <noscript>disabled. This is smart decision, but unfortunately, you cannot use this page like that, because many things on this website requiring you to have JavaScript. So you cannot proceed, because button "Yes" will not even work. Sorry!</noscript>
<script>document.write(`enabled. Are you sure you want to proceed?`)</script>
</p>';
    echo '<button onclick="proceedToPage()">Yes</button> <a href="index.php"><button>No</button></a>';
    echo '<br>';
    echo '</div>';
    echo '<div id="content" style="display: none;">';
    echo '<div class="markdown-content">' . $Parsedown->text(file_get_contents($path)) . '</div>';
    echo '<center><div class="footer"><p>' . htmlspecialchars(getHentaiFooterText(), ENT_QUOTES, 'UTF-8') . '</p></div></center>';
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
?>