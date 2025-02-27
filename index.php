<?php
// Detect available themes from the 'themes' folder
$themes = array_filter(scandir('themes'), function($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'css';
});

// Get the current page (default to 'home')
$page = isset($_GET['page']) ? $_GET['page'] : 'home'; // Default page is 'home'
$themeFile = "themes/{$page}.css"; // Check if a page-specific theme exists

// If the page-specific theme doesn't exist, fall back to a default theme
if (!file_exists($themeFile)) {
    $themeFile = 'themes/default.css'; // Fallback to a default theme if specific theme doesn't exist
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your website title</title>
  <!-- Super cool Hentai CMS theme -->
  <link rel="stylesheet" href="<?php echo $themeFile; ?>">
</head>
<body>

  <!-- Include content from Hentai CMS core -->
  <?php include 'cms/hentaicms.php'; ?>

</body>
</html>