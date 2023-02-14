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

// Connect to the database
// Include database credentials
include 'config.php';

// Connect to the database
try {
  $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . "", DB_USER, DB_PASS);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
  exit;
}
// Prepare the SQL statement to retrieve the user's items and their stats
$stmt = $conn->prepare("
  SELECT items.*, user_items.*
  FROM user_items
  INNER JOIN items ON user_items.item_id = items.id
  WHERE user_items.user_id = :user_id
");

// Bind the user ID to the prepared statement
$stmt->bindParam(':user_id', $_SESSION['user_id']);

// Execute the statement
$stmt->execute();

// Fetch the items and their stats from the result
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>
  <title>My Items - Item Manager</title>
</head>

<body>

  <h1>My Items</h1>

  <p>Add Item: <a href="add_item.php">Add</a></p>

  <?php if (count($items) > 0) : ?>

    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Description</th>
          <th>Price</th>
          <th>Quantity</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item) : if ($item['main_user_id'] == $_SESSION['user_id']) : ?>
            <tr>
              <td><?php echo htmlspecialchars($item['name']); ?></td>
              <td><?php echo htmlspecialchars($item['description']); ?></td>
              <td><?php echo htmlspecialchars($item['price']); ?></td>
              <td><?php echo htmlspecialchars($item['quantity']); ?></td>
              <td><a href="share.php?item_id=<?php echo $item['id']; ?>">Share</a></td>
              <td><a href="delete_item.php?item_id=<?php echo $item['id']; ?>">Delete</a></td>
            </tr>
          <?php else : ?>
            <tr>
              <td><?php echo htmlspecialchars($item['name']); ?></td>
              <td><?php echo htmlspecialchars($item['description']); ?></td>
              <td><?php echo htmlspecialchars($item['price']); ?></td>
              <td><?php echo htmlspecialchars($item['quantity']); ?></td>
            </tr>
        <?php endif;
        endforeach; ?>
      </tbody>
    </table>

  <?php else : ?>

    <p>You are not connected to any items.</p>

  <?php endif; ?>

</body>

</html>