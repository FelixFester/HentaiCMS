<?php
include 'Parsedown.php';
$Parsedown = new Parsedown();

// Maintenance mode flag
$maintenanceMode = false; // Set this to true to enable maintenance mode

// If maintenance mode is enabled, redirect to the maintenance page
if ($maintenanceMode) {
    $maintenancePage = 'content/maintenance.md'; // Path to your maintenance page
    if (file_exists($maintenancePage)) {
        $content = fopen($maintenancePage, 'r') or die('File Open Error');
        echo '<div class="markdown-content">' . $Parsedown->text(fread($content, filesize($maintenancePage))) . '</div>';
        fclose($content);
        exit(); // Stop further execution
    } else {
        echo '<h1>Maintenance Mode</h1><p>The site is currently under maintenance. Please check back later.</p>';
        exit(); // Stop further execution
    }
}

// Get the page from query parameter or default to 'home'
$page = isset($_GET['page']) ? trim($_GET['page'], '/ ') : 'home';

// Define paths for .md files
$path1 = 'content/' . $page . '/index.md';
$path2 = 'content/' . $page . '.md';

// Check if the markdown file exists and render it
if ($page === 'home') {
    // Check for index.md on the homepage
    $homePath = 'content/index.md';
    if (file_exists($homePath)) {
        $content = fopen($homePath, 'r') or die('File Open Error');
        echo '<div class="markdown-content">' . $Parsedown->text(fread($content, filesize($homePath))) . '</div>';
        fclose($content);
    } else {
        echo '<h1>404 - Page Not Found</h1>';
    }
} elseif (file_exists($path1)) {
    // Handle nested page paths like 'content/about/index.md'
    $content = fopen($path1, 'r') or die('File Open Error');
    echo '<div class="markdown-content">' . $Parsedown->text(fread($content, filesize($path1))) . '</div>';
    fclose($content);
} elseif (file_exists($path2)) {
    // Handle simple page paths like 'content/about.md'
    $content = fopen($path2, 'r') or die('File Open Error');
    echo '<div class="markdown-content">' . $Parsedown->text(fread($content, filesize($path2))) . '</div>';
    fclose($content);
} else {
    echo '<h1>404 - Page Not Found</h1>';
}
?>