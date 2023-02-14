<?php
include 'header.php';
include 'config.php';

// start session if it hasn't been started already
if (!isset($_SESSION)) {
    session_start();
  }

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// Get the item ID and the user ID to unshare with
$item_id = $_GET['item_id'];
$unshare_user_id = $_GET['unshare_user_id'];


// Connect to the database
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . "", DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Get the main user ID for the item
$query = "SELECT main_user_id FROM items WHERE id = $item_id";
$result = $conn->query($query);

if ($result->rowCount() > 0) {
    // Item found, get main user ID
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $main_user_id = $row['main_user_id'];
} else {
    // Item not found, redirect to list page
    header('Location: list.php');
    exit();
}

// Check if the user is the main user for the item
if ($_SESSION['user_id'] != $main_user_id) {
    // User is not the main user, redirect to list page
    header('Location: list.php');
    exit();
}

// Remove the item from the user's list
$stmt = $conn->prepare('DELETE FROM user_items WHERE user_id = ? AND item_id = ?');
$stmt->execute([$unshare_user_id, $item_id]);

// Redirect back to the list page
header('Location: list.php');
include 'footer.php';
exit;
?>
