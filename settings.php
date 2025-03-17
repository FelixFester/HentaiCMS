<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Get all .css files in the themes folder
$themes = array_filter(scandir('themes'), function ($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'css';
});

$defaultTheme = 'default.css';
$currentTheme = isset($_COOKIE['hentaicms_theme']) ? htmlspecialchars($_COOKIE['hentaicms_theme']) : $defaultTheme;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Settings</title>
    <link rel="stylesheet" href="ヘンタイ CSS フレームワーク.css">
    <link id="themeCSS" rel="stylesheet" href="themes/<?php echo $currentTheme; ?>">
    <script>
        function changeTheme(theme) {
            document.getElementById('themeCSS').href = 'themes/' + theme;
            document.cookie = 'hentaicms_theme=' + theme + ';path=/;max-age=' + (86400 * 30);
            alert('Theme "' + theme + '" selected successfully!');
        }

        function restoreDefault() {
            document.cookie.split(';').forEach(function (c) {
                document.cookie = c.trim().split('=')[0] + '=;expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/';
            });
            alert('All settings restored to default!');
            location.reload();
        }
    </script>
</head>
<body>
    <div class="echo-content">
        <div style="text-align: center;">
            <?php if (count($themes) == 1) : ?>
                <!-- Display warning if there's only one theme -->
                <h1>Oops!</h1>
                <p>Website currently have only one theme. So there's nothing to do.</p>
                <button class="button" onclick="window.location.href='index.php'">Okay</button>
                <br><br>
            <?php else : ?>
                <!-- Display theme selection if there are multiple themes -->
                <h1>Website Settings</h1>
                <label for="theme">Select Theme:</label>
                <select id="theme" class="form-control" onchange="changeTheme(this.value)" style="max-width: 100%; overflow-x: auto;">
                    <?php foreach ($themes as $theme) : ?>
                        <?php $selected = ($theme === $currentTheme) ? 'selected' : ''; ?>
                        <option value="<?php echo $theme; ?>" <?php echo $selected; ?>><?php echo $theme; ?></option>
                    <?php endforeach; ?>
                </select>
                <br>
                <button class="button" onclick="restoreDefault()">Restore Default</button>
                <button class="button" onclick="window.location.href='index.php'">Go Back to Main Page</button>
                <br><br>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
