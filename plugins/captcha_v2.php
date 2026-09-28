<?php
// Self-contained proof-of-work check for HentaiCMS — the challenge is HMAC-signed so it can be verified statelessly.
// Located in plugins/captcha.php

// Prevent direct access (though already handled by core)
if (!defined('HENTAI_CMS_PLUGIN')) {
    if (!headers_sent()) {
        header('Location: /index.php');
        exit();
    } else {
        echo '<meta http-equiv="refresh" content="0;url=/index.php">';
        exit();
    }
}

// ---------------------------------------------------------------------
// CONFIG — change this to a long random secret, keep it private.
// Generate one with: php -r "echo bin2hex(random_bytes(32));"
// ---------------------------------------------------------------------
$pow_hmac_key    = 'CHANGE_ME_TO_A_LONG_RANDOM_SECRET';
$pow_maxnumber   = 50000; // how far the browser has to brute-force; raise = slower/harder
$pow_ttl         = 300;   // seconds the challenge stays valid

$captcha_cookie_name = 'hentaicms_visited';
$captcha_duration    = 60 * 60 * 24 * 30; // 30 days

// ---------------------------------------------------------------------
// Handle a submitted solution (normal POST back to the same URL the
// visitor was already on — no special routing/endpoint needed).
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pow_salt'], $_POST['pow_challenge'], $_POST['pow_expires'], $_POST['pow_signature'], $_POST['pow_number'])) {

    $salt      = $_POST['pow_salt'];
    $challenge = $_POST['pow_challenge'];
    $expires   = (int)$_POST['pow_expires'];
    $signature = $_POST['pow_signature'];
    $number    = $_POST['pow_number'];

    $expectedSignature = hash_hmac('sha256', $challenge . '.' . $expires, $pow_hmac_key);

    $valid = hash_equals($expectedSignature, $signature)
        && $expires >= time()
        && ctype_digit((string)$number)
        && hash_equals($challenge, hash('sha256', $salt . $number));

    if ($valid) {
        setcookie($captcha_cookie_name, '1', time() + $captcha_duration, '/', '', false, true);
        // Reflect it for the rest of THIS request too, so the page
        // renders immediately instead of needing a second reload.
        $_COOKIE[$captcha_cookie_name] = '1';
    }
    // If invalid, we just fall through and the captcha page renders again below.
}

// If cookie exists, do nothing (skip captcha)
if (isset($_COOKIE[$captcha_cookie_name])) {
    if (isset($_GET['plugin']) && $_GET['plugin'] === 'captcha') {
        return 'PLUGIN_DOES_NOT_EXIST';
    }
    if (isset($_GET['captcha'])) {
        return 'PLUGIN_DOES_NOT_EXIST';
    }
    return; // Exit early, proceed to normal content
}

// ---------------------------------------------------------------------
// Generate a fresh challenge for this pageview
// ---------------------------------------------------------------------
$salt      = bin2hex(random_bytes(8));
$secretNum = random_int(0, $pow_maxnumber);
$challenge = hash('sha256', $salt . $secretNum);
$expires   = time() + $pow_ttl;
$signature = hash_hmac('sha256', $challenge . '.' . $expires, $pow_hmac_key);

// Show captcha / "prove you're human" page
displayHentaiHeader();

echo '<div class="markdown-content" style="text-align:center; padding: 40px 20px; max-width: 600px; margin: 0 auto;">';

echo '<h1>Wait a minute</h1>';
echo '<p style="font-size:1.2em; margin: 1.5em 0;">Just checking you\'re not a bot — this only happens once.</p>';

echo '<form method="POST" action="" id="powForm">';
echo '<input type="hidden" name="pow_salt" value="' . htmlspecialchars($salt) . '">';
echo '<input type="hidden" name="pow_challenge" value="' . htmlspecialchars($challenge) . '">';
echo '<input type="hidden" name="pow_expires" value="' . htmlspecialchars($expires) . '">';
echo '<input type="hidden" name="pow_signature" value="' . htmlspecialchars($signature) . '">';
echo '<input type="hidden" name="pow_number" id="pow_number" value="">';
echo '<button type="button" id="verifyBtn" style="font-size:1.3em; padding:12px 30px;">Verify</button>';
echo '</form>';

echo '<p id="error" style="color:#ff5555; font-weight:bold; min-height:1.5em; margin-top:1em;"></p>';

echo '<script>
(function () {
    var maxnumber = ' . (int)$pow_maxnumber . ';
    var salt = ' . json_encode($salt) . ';
    var challenge = ' . json_encode($challenge) . ';
    var btn = document.getElementById("verifyBtn");
    var err = document.getElementById("error");

    async function sha256Hex(text) {
        var buf = await crypto.subtle.digest("SHA-256", new TextEncoder().encode(text));
        var bytes = new Uint8Array(buf);
        var hex = "";
        for (var i = 0; i < bytes.length; i++) hex += bytes[i].toString(16).padStart(2, "0");
        return hex;
    }

    async function solve() {
        if (!window.crypto || !window.crypto.subtle) {
            err.textContent = "Your browser/connection does not support this check (needs HTTPS). Please contact the site owner.";
            return;
        }
        btn.disabled = true;
        btn.textContent = "Verifying...";
        for (var n = 0; n <= maxnumber; n++) {
            var hash = await sha256Hex(salt + n);
            if (hash === challenge) {
                document.getElementById("pow_number").value = n;
                document.getElementById("powForm").submit();
                return;
            }
        }
        btn.disabled = false;
        btn.textContent = "Verify";
        err.textContent = "Could not verify, please reload the page and try again.";
    }

    btn.addEventListener("click", solve);
})();
</script>';

// Plain-language explainer for regular (non-technical) visitors
echo '<details style="margin-top: 2.5em; text-align: left; font-size: 0.9em; color: #999;">';
echo '<summary style="cursor:pointer; text-align:center; color:#bbb;">What is this?</summary>';
echo '<div style="margin-top: 1em; line-height: 1.6;">';
echo '<p>This page runs a quick check in your browser to confirm you\'re a real visitor and not an automated bot or scraper. ';
echo 'Clicking "Verify" does a small bit of math locally — there\'s nothing to solve or type.</p>';
echo '<p>Once it passes, a cookie is saved in your browser so you won\'t see this again for 30 days. ';
echo 'No personal data is collected and nothing is sent to a third party — the whole check runs on this site\'s own server.</p>';
echo '</div>';
echo '</details>';

echo '</div>';

displayHentaiFooter();
exit(); // Stop further execution
?>
