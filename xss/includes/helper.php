<?php
// includes/helper.php - Common security utilities, session state, and escaping functions

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize security level (low = 脆弱, medium = 不完全な対策, high = 安全)
if (!isset($_SESSION['sec_level'])) {
    $_SESSION['sec_level'] = 'low';
}

// Handle security level change via GET parameter
if (isset($_GET['set_level']) && in_array($_GET['set_level'], ['low', 'medium', 'high'], true)) {
    $_SESSION['sec_level'] = $_GET['set_level'];
    
    // Redirect back without set_level param
    $uri = strtok($_SERVER['REQUEST_URI'], '?');
    $query = $_GET;
    unset($query['set_level']);
    $queryString = http_build_query($query);
    $redirectUrl = $uri . ($queryString ? '?' . $queryString : '');
    header('Location: ' . $redirectUrl);
    exit;
}

// Set demonstration cookies for learning HttpOnly mechanisms
if (!isset($_COOKIE['demo_user_cookie'])) {
    setcookie('demo_user_cookie', 'visible_token_xyz987_user', [
        'expires' => time() + 86400,
        'path' => '/',
        'httponly' => false, // Accessible via JavaScript document.cookie
        'samesite' => 'Lax'
    ]);
}
if (!isset($_COOKIE['secret_auth_cookie'])) {
    setcookie('secret_auth_cookie', 'PROTECTED_SECRET_SESSION_TOKEN_ABC123', [
        'expires' => time() + 86400,
        'path' => '/',
        'httponly' => true, // Protected against JavaScript document.cookie access
        'samesite' => 'Lax'
    ]);
}

function get_sec_level() {
    return $_SESSION['sec_level'] ?? 'low';
}

function get_sec_level_badge() {
    $lvl = get_sec_level();
    switch ($lvl) {
        case 'low':
            return ['class' => 'badge-danger', 'label' => '🔴 脆弱モード (Low / Vulnerable)', 'desc' => '対策なし（入力値・出力をそのまま反映）'];
        case 'medium':
            return ['class' => 'badge-warning', 'label' => '🟡 不完全対策モード (Medium / Weak Filter)', 'desc' => '単純なブラックリスト置換など不十分な対策'];
        case 'high':
            return ['class' => 'badge-success', 'label' => '🟢 安全モード (High / Secure)', 'desc' => '適切なエスケープ（htmlspecialchars / コンテキスト対応）'];
        default:
            return ['class' => 'badge-secondary', 'label' => 'Unknown', 'desc' => ''];
    }
}

/**
 * Proper HTML Escaping
 */
function h($string) {
    if ($string === null) return '';
    return htmlspecialchars((string)$string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Filter for Medium Level (Demonstrating flawed blacklist sanitization)
 */
function weak_filter($string) {
    if ($string === null) return '';
    // Naive case-sensitive or non-recursive tag stripping (easy to bypass)
    $filtered = str_replace('<script>', '', (string)$string);
    $filtered = str_replace('</script>', '', $filtered);
    return $filtered;
}

/**
 * URL sanitizer to prevent javascript: pseudo-protocol
 */
function sanitize_url_safe($url) {
    $url = trim((string)$url);
    if ($url === '') return '';
    
    // Check if protocol is safe (http, https, mailto, relative path)
    if (preg_match('/^(https?:\/\/|\/|mailto:)/i', $url)) {
        return h($url);
    }
    // If unsafe protocol like javascript:, return safe fallback or empty
    return '#unsafe-url-blocked';
}

/**
 * Apply Content Security Policy header if requested
 */
function apply_csp_if_enabled() {
    if (isset($_SESSION['csp_enabled']) && $_SESSION['csp_enabled'] === true) {
        // Enforce strict CSP blocking inline scripts without nonce or unsafe-inline
        header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:;");
    }
}
