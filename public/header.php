<?php
// start session if it hasn't been started already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// check if user is logged in
if (!isset($_SESSION['user_id']) and !in_array(basename($_SERVER["SCRIPT_FILENAME"], ".php"), array("login", "register"))) {
    // redirect to login page if user is not logged in
    header('Location: login.php');
    exit;
}
?>