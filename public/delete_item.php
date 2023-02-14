<?php
include 'header.php';
include 'config.php';

// Get the item ID to delete
$item_id = $_GET['item_id'];

// Connect to the database
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . "", DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
try {
  // Check if the user is the main user of the item
  $stmt = $conn->prepare('SELECT main_user_id FROM items WHERE id = ?');
  $stmt->execute([$item_id]);
  $row = $stmt->fetch();
  $main_user_id = $row['main_user_id'];

  if ($_SESSION['user_id'] != $main_user_id) {
    // User is not the main user, redirect to list page
    header('Location: list.php');
    exit;
  }
  // Clean up the user_items table by deleting all records with the deleted item's ID
  $stmt = $conn->prepare('DELETE FROM user_items WHERE item_id = ?');
  $stmt->execute([$item_id]);

  // Delete the item from the items table
  $stmt = $conn->prepare('DELETE FROM items WHERE id = ?');
  $stmt->execute([$item_id]);


  // Redirect back to the list page
  header('Location: list.php');
  include 'footer.php';
  exit;
} catch (PDOException $e) {
  echo 'Error: ' . $e->getMessage();
  exit;
}
