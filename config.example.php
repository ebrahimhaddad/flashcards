<?php
// ---------------------------------------------------------------------
// Database credentials — fill these in, then save this file as config.php
// (config.php is git-ignored and must never be committed)
// ---------------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'put db name here');
define('DB_USER', 'put db user here');
define('DB_PASS', 'put db password here');

// mysqli connection — used by cards.php and practice.php
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    error_log('mysqli connection failed: ' . mysqli_connect_error());
    die('Database connection error.');
}
mysqli_set_charset($db, 'utf8mb4');

// PDO connection — used by api_comment.php, login.php
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    error_log('PDO connection failed: ' . $e->getMessage());
    die('Database connection error.');
}

// ---------------------------------------------------------------------
// JWT — manual implementation. Replace JWT_SECRET with your own long,
// random string before running the app; never reuse the placeholder.
// ---------------------------------------------------------------------
define('JWT_SECRET', 'put a long random secret string here');

function base64url_encode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data)
{
    return base64_decode(strtr($data, '-_', '+/'));
}

function jwt_encode(array $payload)
{
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];

    $headerEncoded  = base64url_encode(json_encode($header));
    $payloadEncoded = base64url_encode(json_encode($payload));

    $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, JWT_SECRET, true);
    $signatureEncoded = base64url_encode($signature);

    return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
}

function jwt_decode($jwt)
{
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return null;

    [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

    $expectedSignature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, JWT_SECRET, true);
    $expectedSignatureEncoded = base64url_encode($expectedSignature);

    if (!hash_equals($expectedSignatureEncoded, $signatureEncoded)) {
        return null; // tampered or forged
    }

    $payload = json_decode(base64url_decode($payloadEncoded), true);

    if (isset($payload['exp']) && time() > $payload['exp']) {
        return null; // expired
    }

    return $payload;
}

// ---------------------------------------------------------------------
// Editor identity — reads the JWT cookie if present, defaults to 'user'.
// cards.php and practice.php are public and must work with no cookie at
// all; this always returns a usable value either way.
// ---------------------------------------------------------------------
function current_editor()
{
    if (empty($_COOKIE['auth_token'])) {
        return 'user';
    }

    $payload = jwt_decode($_COOKIE['auth_token']);

    if ($payload === null || !isset($payload['editor'])) {
        return 'user'; // invalid, tampered, or expired token — treat as anonymous
    }

    return $payload['editor'];
}

function current_editor_claims()
{
    if (empty($_COOKIE['auth_token'])) {
        return null;
    }
    return jwt_decode($_COOKIE['auth_token']); // null if invalid/expired
}

// ---------------------------------------------------------------------
// CSRF protection — double-submit cookie pattern.
// The CSRF token lives in a second, non-HttpOnly cookie. A cross-site
// forger can read neither this cookie (same-origin policy) nor the
// auth_token cookie (HttpOnly), so they can't produce a matching pair.
// ---------------------------------------------------------------------
if (empty($_COOKIE['csrf_token'])) {
    $csrfToken = bin2hex(random_bytes(32));
    setcookie('csrf_token', $csrfToken, [
        'expires'  => time() + 3600,
        'path'     => '/',
        'secure'   => true,
        'httponly' => false, // must be readable by the page's own JS/PHP to embed in forms
        'samesite' => 'Strict',
    ]);
    $_COOKIE['csrf_token'] = $csrfToken; // usable immediately in this same request
}

function verify_csrf_token($submittedToken)
{
    return isset($_COOKIE['csrf_token'])
        && hash_equals($_COOKIE['csrf_token'], $submittedToken ?? '');
}
