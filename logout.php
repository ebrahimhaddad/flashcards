<?php
// Clear the auth cookie by setting it with an expiration in the past.
// The options here must match what was used when the cookie was set
// (path, secure, httponly, samesite) — otherwise the browser treats
// this as a different cookie and won't actually overwrite/clear it.
setcookie('auth_token', '', [
   'expires'  => time() - 3600,
   'path'     => '/',
   'secure'   => true,
   'httponly' => true,
   'samesite' => 'Strict',
]);

header('Location: cards.php');
exit;
