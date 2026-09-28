<?php
// Settings for HentaiCMS
// Located in plugins/settings.php

// Prevent direct access
if (!defined('HENTAI_CMS_PLUGIN')) {
    header('Location: /index.php');
    exit();
}

// Enable error reporting for debugging
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
    <title>Settings</title>
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
                <!-- If one theme -->
                <h1>Oops!</h1>
                <p>Website currently have only one theme. So there's nothing to do.</p>
                <button class="button" onclick="window.location.href='index.php'">Okay</button>
                <br><br>
            <?php else : ?>
                <!-- If many themes -->
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
                <br>
                <hr>
                For changing theme, website will use cookies of your <script>
// Function to detect the browser name
function detectBrowser() {
    var userAgent = navigator.userAgent;
    if (userAgent.indexOf("Edg") > -1) {
        return "Microsoft Edge";
    } else if (userAgent.indexOf("Chrome") > -1) {
        return "Chrome";
    } else if (userAgent.indexOf("Firefox") > -1) {
        return "Firefox";
    } else if (userAgent.indexOf("Safari") > -1) {
        return "Safari";
    } else if (userAgent.indexOf("Opera") > -1) {
        return "Opera";
    } else if (userAgent.indexOf("Trident") > -1 || userAgent.indexOf("MSIE") > -1) {
        return "Internet Explorer";
    }

    return "Unknown";
}

// Get the browser name and display it
var browserName = detectBrowser();
document.write("" + browserName);
</script> browser! 🍪
                <br>
                If you're not going to allow to use your cookies, just don't change anything (or restore settings to default) and leave this page.
                <br><br>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
