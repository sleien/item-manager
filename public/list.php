<?php
include 'header.php';
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

<section class="content">
  <h1>My Items</h1>
  <div class="spacer"></div>
  <a href="add_item.php"><button>Add Item</button></a>

  <?php if (count($items) > 0): ?>

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
        <?php foreach ($items as $item):
          if ($item['main_user_id'] == $_SESSION['user_id']): ?>
            <tr>
              <td>
                <?php echo htmlspecialchars($item['name']); ?>
              </td>
              <td>
                <?php echo htmlspecialchars($item['description']); ?>
              </td>
              <td>
                <?php echo htmlspecialchars($item['price']); ?>
              </td>
              <td>
                <?php echo htmlspecialchars($item['quantity']); ?>
              </td>
              <td><a href="share.php?item_id=<?php echo $item['id']; ?>"><button>Share</button></a></td>
              <td><a href="edit_item.php?item_id=<?php echo $item['id']; ?>"><button>Edit</button></a></td>
              <td><a href="delete_item.php?item_id=<?php echo $item['id']; ?>"><button>Delete</button></a></td>
            </tr>
          <?php else: ?>
            <tr>
              <td>
                <?php echo htmlspecialchars($item['name']); ?>
              </td>
              <td>
                <?php echo htmlspecialchars($item['description']); ?>
              </td>
              <td>
                <?php echo htmlspecialchars($item['price']); ?>
              </td>
              <td>
                <?php echo htmlspecialchars($item['quantity']); ?>
              </td>
              <td>
                Shared with you
              </td>
            </tr>
          <?php endif;
        endforeach; ?>
      </tbody>
    </table>

  <?php else: ?>

    <p>You are not connected to any items.</p>

  <?php endif; ?>
</section>

<?php include 'footer.php'; ?>