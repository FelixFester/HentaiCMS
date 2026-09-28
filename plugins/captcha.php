<?php
// Captcha-like first-visit check for HCMS
//
// - Cloudflare Turnstile handles the actual bot/human decision.
// - The "passed" cookie is a signed token (HMAC)
//
// Located in plugins/captcha.php

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
// Configuration. To generate random secret, use "php -r "echo bin2hex(random_bytes(32));"". ---------------------------------------------------------------------
$turnstile_site_key   = 'YOUR_TURNSTILE_SITE_KEY';
$turnstile_secret_key = 'YOUR_TURNSTILE_SECRET_KEY';
$session_hmac_key     = 'CHANGE_ME_TO_A_LONG_RANDOM_SECRET'; // different from the Turnstile keys

$captcha_cookie_name = 'hcms_session';
$captcha_duration    = 60 * 60 * 24 * 30; // 30 days

// ---------------------------------------------------------------------
// Signed cookie helpers
// ---------------------------------------------------------------------
function make_session_token(string $key, int $ttl): string {
    $payload = base64_encode(json_encode(['exp' => time() + $ttl]));
    $sig = hash_hmac('sha256', $payload, $key);
    return $payload . '.' . $sig;
}

function verify_session_token(?string $token, string $key): bool {
    if (!$token || strpos($token, '.') === false) return false;
    [$payload, $sig] = explode('.', $token, 2);
    if (!hash_equals(hash_hmac('sha256', $payload, $key), $sig)) return false;
    $data = json_decode(base64_decode($payload), true);
    return is_array($data) && isset($data['exp']) && $data['exp'] > time();
}

// ---------------------------------------------------------------------
// Turnstile server-side verification
// ---------------------------------------------------------------------
function verify_turnstile(string $token, string $secret, string $remoteip): bool {
    $fields = http_build_query([
        'secret'   => $secret,
        'response' => $token,
        'remoteip' => $remoteip,
    ]);

    if (function_exists('curl_init')) {
        $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        curl_close($ch);
    } else {
        // Fallback for hosts without the curl extension
        $ctx = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $fields,
                'timeout' => 10,
            ],
        ]);
        $result = @file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $ctx);
    }

    if ($result === false) return false;
    $data = json_decode($result, true);
    return isset($data['success']) && $data['success'] === true;
}

// ---------------------------------------------------------------------
// Handle a submitted verification
// ---------------------------------------------------------------------
$turnstile_error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cf-turnstile-response'])) {
    // Honeypot: real users/browsers never fill this in
    if (!empty($_POST['hp_field'])) {
        $turnstile_error = true; // silently treat as failed, no specific reason given
    } else {
        $remoteip = $_SERVER['REMOTE_ADDR'] ?? '';
        if (verify_turnstile($_POST['cf-turnstile-response'], $turnstile_secret_key, $remoteip)) {
            $token = make_session_token($session_hmac_key, $captcha_duration);
            setcookie($captcha_cookie_name, $token, time() + $captcha_duration, '/', '', false, true);
            $_COOKIE[$captcha_cookie_name] = $token; // reflect for this same request
        } else {
            $turnstile_error = true;
        }
    }
}

// If a valid signed cookie exists, skip captcha
if (verify_session_token($_COOKIE[$captcha_cookie_name] ?? null, $session_hmac_key)) {
    if (isset($_GET['plugin']) && $_GET['plugin'] === 'captcha') {
        return 'PLUGIN_DOES_NOT_EXIST';
    }
    if (isset($_GET['captcha'])) {
        return 'PLUGIN_DOES_NOT_EXIST';
    }
    return;
}

// Show captcha / "prove you're human" page
displayHentaiHeader();

echo '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';

echo '<div class="markdown-content" style="text-align:center; padding: 40px 20px; max-width: 600px; margin: 0 auto;">';

echo '<h1>Wait a minute</h1>';
echo '<p style="font-size:1.2em; margin: 1.5em 0;">Just checking you\'re not a bot — this only happens once.</p>';

if ($turnstile_error) {
    echo '<p style="color:#ff5555; font-weight:bold; margin-bottom:1em;">Verification failed, please try again.</p>';
}

echo '<form method="POST" action="" id="tsForm">';
echo '<input type="text" name="hp_field" id="hp_field" value="" autocomplete="off" tabindex="-1" aria-hidden="true"
        style="position:absolute; left:-9999px; width:1px; height:1px; opacity:0;">';
echo '<div class="cf-turnstile" data-sitekey="' . htmlspecialchars($turnstile_site_key) . '" data-callback="hcmsTurnstileDone" style="display:flex; justify-content:center; margin: 1.5em 0;"></div>';
echo '</form>';

echo '<script>
function hcmsTurnstileDone(token) {
    document.getElementById("tsForm").submit();
}
</script>';

echo '<details style="margin-top: 2.5em; text-align: left; font-size: 0.9em; color: #999;">';
echo '<summary style="cursor:pointer; text-align:center; color:#bbb;">What is this?</summary>';
echo '<div style="margin-top: 1em; line-height: 1.6;">';
echo '<p>This page runs a quick check (via Cloudflare Turnstile) to confirm you\'re a real visitor and not an automated bot or scraper. ';
echo 'It usually passes automatically in the background — there\'s nothing to solve or type.</p>';
echo '<p>Once it passes, a small signed cookie is saved in your browser so you won\'t see this again for 30 days.</p>';
echo '</div>';
echo '</details>';

echo '</div>';

displayHentaiFooter();
exit();
?>
