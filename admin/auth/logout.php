<?php
session_start();

$_SESSION = array();
define('LOGOUT_LOCATION', "https://websolutions.koeln/portfolio/admincp/");
unset($_SESSION);

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"],
        $params["domain"], $params["secure"], $params["httponly"]
    );
}

session_destroy();

header("Location: ". LOGOUT_LOCATION);
?>
