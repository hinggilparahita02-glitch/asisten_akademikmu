<?php
/**
 * Session Configuration
 * Asisten Akademik Harian
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user ID
 * @return int|null
 */
function getCurrentUserId()
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user data
 * @return array|null
 */
function getCurrentUser()
{
    if (!isLoggedIn())
        return null;

    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? '',
        'class' => $_SESSION['user_class'] ?? ''
    ];
}

/**
 * Set user session after login
 * @param array $user User data from database
 */
function setUserSession($user)
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_class'] = $user['class'];
    $_SESSION['login_time'] = time();
}

/**
 * Destroy user session (logout)
 */
function destroySession()
{
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Require login - redirect to index if not logged in
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Redirect if already logged in
 */
function redirectIfLoggedIn()
{
    if (isLoggedIn()) {
        header('Location: dashboard.php');
        exit;
    }
}
