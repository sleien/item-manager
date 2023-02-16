<!DOCTYPE html>
<html>

<head>
    <title>Item Manager</title>
    <link rel="icon" type="image/svg+xml" href="./img/icon.svg" sizes="any">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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