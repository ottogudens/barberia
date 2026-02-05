<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generates a CSRF token if one does not exist or if explicitly requested.
 * @return string The CSRF token.
 */
function generateCsrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies the CSRF token.
 * @param string $token The token to verify.
 * @return bool True if valid, false otherwise.
 */
function verifyCsrfToken($token)
{
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        return true;
    }
    return false;
}

/**
 * Renders the CSRF token input field.
 */
function csrfInput()
{
    $token = generateCsrfToken();
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}
?>