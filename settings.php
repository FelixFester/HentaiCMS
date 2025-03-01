<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

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
            <h1>Website Settings</h1>
            <label for="theme">Select Theme:</label>
            <select id="theme" onchange="changeTheme(this.value)">
                <?php
                foreach ($themes as $theme) {
                    $selected = ($theme === $currentTheme) ? 'selected' : '';
                    echo "<option value=\"$theme\" $selected>$theme</option>";
                }
                ?>
            </select>
            <br><br>
            <button onclick="restoreDefault()">Restore Default</button>
            <button onclick="window.location.href='index.php'">Go Back to Main Page</button>
            <br>
        </div>
    </div>
</body>
</html>
