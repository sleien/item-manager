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

<!DOCTYPE html>
<html>

<head>
    <title>Item Manager</title>
    <link rel="icon" type="image/svg+xml" href="./img/icon.svg" sizes="any">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="js/script.js"></script>
    <script src="js/multiselect-dropdown.js"></script>

    <style>
        <?php include 'css/style.css'; ?>
    </style>
</head>

<body>
    <div class="wrapper">

        <?php if (isset($_SESSION['user_id'])): ?>
            <nav>
                <ul>
                    <li><a href="list.php?wishlist=0">Items</a></li>
                    <li><a href="list.php?wishlist=1">Wishlist</a></li>
                    <li><a href="tags.php">Tags</a></li>
                    <li><a href="add_item.php">Add Item</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        <?php endif; ?>