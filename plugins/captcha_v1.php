<?php
// Captcha-like first-visit check for HentaiCMS
// Located in plugins/captcha.php

// Prevent direct access (though already handled by core)
if (!defined('HENTAI_CMS_PLUGIN')) {
    // Don't use header() here as output may have started
    // Instead, we'll handle this differently
    if (!headers_sent()) {
        header('Location: /index.php');
        exit();
    } else {
        // Output is already started, use meta refresh
        echo '<meta http-equiv="refresh" content="0;url=/index.php">';
        exit();
    }
}

$captcha_cookie_name = 'hentaicms_visited';
$captcha_duration    = 60 * 60 * 24 * 30; // 30 days

// If cookie exists, do nothing (skip captcha)
if (isset($_COOKIE[$captcha_cookie_name])) {
    // If this is being called as a normal plugin (via URL), show home page instead
    // We'll signal this by returning a special value
    if (isset($_GET['plugin']) && $_GET['plugin'] === 'captcha') {
        // Return special value to indicate "plugin doesn't exist"
        return 'PLUGIN_DOES_NOT_EXIST';
    }
    // If accessed via ?captcha parameter
    if (isset($_GET['captcha'])) {
        return 'PLUGIN_DOES_NOT_EXIST';
    }
    return; // Exit early, proceed to normal content
}

// Show captcha / "prove you're human" page
displayHentaiHeader();

echo '<div class="markdown-content" style="text-align:center; padding: 40px 20px; max-width: 600px; margin: 0 auto;">';

echo '<h1>Wait a minute</h1>';
echo '<p style="font-size:1.2em; margin: 1.5em 0;">Are you a human?</p>';

// Very simple example challenge (you can make it harder later)
$a = rand(3,12);
$b = rand(4,15);
$correct = $a + $b;

echo '<p style="font-size:1.4em; margin: 2em 0;">';
echo "$a + $b = ?<br>";
echo '<input type="number" id="answer" placeholder="" style="font-size:1.3em; width:120px; text-align:center; margin: 15px 0; padding:8px;">';
echo '</p>';

echo '<button onclick="checkCaptcha(' . $correct . ')" style="font-size:1.3em; padding:12px 30px;">Verify</button>';

echo '<p id="error" style="color:#ff5555; font-weight:bold; min-height:1.5em; margin-top:1em;"></p>';

echo '<script>
    function checkCaptcha(correct) {
        const ans = parseInt(document.getElementById("answer").value);
        const err = document.getElementById("error");
        if (ans === correct) {
            document.cookie = "' . $captcha_cookie_name . '=1; path=/; max-age=' . $captcha_duration . '; SameSite=Lax";
            location.reload();
        } else {
            err.textContent = "Wrong answer. Try again.";
        }
    }
</script>';

echo '</div>';

displayHentaiFooter();
exit(); // Stop further execution
?>