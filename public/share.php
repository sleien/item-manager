<?php
// start session if it hasn't been started already
if (!isset($_SESSION)) {
    session_start();
}

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // redirect to login page if user is not logged in
    header('Location: login.php');
    exit();
}

// check if item id is provided
if (!isset($_GET['item_id'])) {
    // redirect to list page if item id is not provided
    header('Location: list.php');
    exit();
}

// get item id from query string
$item_id = $_GET['item_id'];

// Include database credentials
include 'config.php';

// Connect to the database
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . "", DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
// prepare and execute the query to get main user id for the item
$query = 'SELECT main_user_id FROM items WHERE id = :item_id';
$stmt = $conn->prepare($query);
$stmt->bindParam(':item_id', $item_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    // item found, get main user id
    $main_user_id = $row['main_user_id'];
} else {
    // item not found, redirect to list page
    header('Location: list.php');
    exit();
}

// check if user is the main user for the item
if ($_SESSION['user_id'] != $main_user_id) {
    
    // user is not the main user, redirect to list page
    header('Location: list.php');
    exit();
}

// get list of users who are connected to the item
$query = "SELECT users.id, users.username, users.email FROM users LEFT JOIN (SELECT user_id FROM user_items WHERE item_id = $item_id) AS shared_users ON users.id = shared_users.user_id WHERE shared_users.user_id IS NULL";
$result = $conn->query($query);
$query = "SELECT users.id, users.username, users.email FROM users LEFT JOIN (SELECT user_id FROM user_items WHERE item_id = $item_id and user_id != $main_user_id) AS shared_users ON users.id = shared_users.user_id WHERE shared_users.user_id IS NOT NULL";
$result2 = $conn->query($query);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Share Item</title>
</head>
<body>
    <h1>Share Item</h1>

    <p>Share this item with:</p>

    <?php if ($result->rowCount() > 0): ?>
        <ul>
            <?php foreach ($result as $row): ?>
                <li><?php echo $row['username'] ?> (<?php echo $row['email'] ?>) <a href="share_action.php?item_id=<?php echo $item_id ?>&share_user_id=<?php echo $row['id'] ?>">Share</a></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No users not connected to this item.</p>
    <?php endif; ?>
    
    <p>Unshare this item with:</p>
    <?php if ($result2->rowCount() > 0): ?>
        <ul>
            <?php foreach ($result2 as $row): ?>
                <li><?php echo $row['username'] ?> (<?php echo $row['email'] ?>) <a href="unshare.php?item_id=<?php echo $item_id ?>&unshare_user_id=<?php echo $row['id'] ?>">Unshare</a></li>
                <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Not yet shared.</p>
    <?php endif; ?>

    <p><a href="list.php">Back to list</a></p>
</body>
</html>
