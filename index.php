<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Detect available themes from the 'themes' folder
$themes = array_filter(scandir('themes'), function ($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'css';
});

// Default theme
$defaultTheme = 'themes/default.css';

// Cookie-based theme selection
if (isset($_COOKIE['hentaicms_theme']) && in_array($_COOKIE['hentaicms_theme'], $themes)) {
    $themeFileForHentaiCMS = 'themes/' . $_COOKIE['hentaicms_theme'];
} else {
    $themeFileForHentaiCMS = $defaultTheme; // Fallback to default theme
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your website name</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($themeFileForHentaiCMS); ?>">
</head>
<body>
  <?php include 'cms/hentaicms.php'; ?>
</body>
</html>