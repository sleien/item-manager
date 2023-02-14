<?php
include 'header.php';
include 'config.php';

// Get the item ID and the user ID to share with
$item_id = $_GET['item_id'];
$share_user_id = $_GET['share_user_id'];


// Connect to the database
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . "", DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
// get main user id for the item
$query = "SELECT main_user_id FROM items WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$item_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($stmt->rowCount() > 0) {
    // item found, get main user id
    $main_user_id = $result['main_user_id'];
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

// Insert a new record into the user_items table
$stmt = $conn->prepare('INSERT INTO user_items (user_id, item_id) VALUES (?, ?)');
$stmt->execute([$share_user_id, $item_id]);

// Redirect back to the list page
header('Location: list.php');
include 'footer.php';
exit;

?>