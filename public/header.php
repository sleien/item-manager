<?php
// start session if it hasn't been started already
if (!isset($_SESSION)) {
    session_start();
}

// check if user is logged in
if (!isset($_SESSION['user_id']) and !in_array(basename($_SERVER["SCRIPT_FILENAME"], ".php"), array("login", "register"))) {
    // redirect to login page if user is not logged in
    header('Location: login.php');
    exit();
}
?>
<style>
    <?php include 'css/style.css'; ?>
</style>

<!DOCTYPE html>
<html>

<head>
    <title>Item Manager</title>
</head>

<body>
    <div class="wrapper">

        <?php if (isset($_SESSION['user_id'])): ?>
            <nav>
                <ul>
                    <li><a href="list.php?wishlist=0">Items</a></li>
                    <li><a href="list.php?wishlist=1">Wishlist</a></li>
                    <li><a href="add_item.php">Add Item</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        <?php endif; ?>