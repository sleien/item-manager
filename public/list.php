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

if (isset($_GET["wishlist"])) {
  $_SESSION["wishlist"] = $_GET["wishlist"];
}

// Prepare the SQL statement to retrieve the user's items and their stats
$stmt = $conn->prepare("SELECT items.*, user_items.*, GROUP_CONCAT(tags.name) as tag_names
FROM user_items
INNER JOIN items ON user_items.item_id = items.id
LEFT JOIN item_tags ON items.id = item_tags.item_id
LEFT JOIN tags ON item_tags.tag_id = tags.id
WHERE user_items.user_id = :user_id
AND wishlist = :wishlist
GROUP BY items.id
");


// Bind the user ID to the prepared statement
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->bindParam(':wishlist', $_SESSION["wishlist"]);

// Execute the statement
$stmt->execute();

// Fetch the items and their stats from the result
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include 'head.php'; ?>
<section class="content">
  <?php if($_SESSION["wishlist"] == 0) : ?>
    <h1>Items</h1>
  <?php else : ?>
    <h1>Wishlist</h1>
  <?php endif; ?>
  <input type="text" id="search" placeholder="Search for tag.." oninput="filterTable('mainList', this.value)">
  <?php if (count($items) > 0) : ?>
    <table id="mainList">
      <thead>
        <tr>
          <th>Name</th>
          <th>Description</th>
          <th>Price</th>
          <th>Quantity</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item) : ?>
          <tr data-tags=" <?php echo $item["tag_names"]?>">
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
              <?php if ($item['link'] != "") : ?>
                <a href=<?php echo htmlspecialchars($item['link']); ?>><?php echo str_replace("www.", "", parse_url($item['link'])["host"]) ?></a>
              <?php endif; ?>
            </td>
            <?php if ($item['main_user_id'] == $_SESSION['user_id']) : ?>
              <td><a href="share.php?item_id=<?php echo $item['id']; ?>"><button>Share</button></a></td>
              <td><a href="edit_item.php?item_id=<?php echo $item['id']; ?>"><button>Edit</button></a></td>
              <td><a href="delete_item.php?item_id=<?php echo $item['id']; ?>"><button>Delete</button></a></td>
            <?php else : ?>
              <td>
                Shared with you.
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  <?php else : ?>

    <p>You don't have any items yet.</p>

  <?php endif; ?>
</section>

<?php include 'footer.php'; ?>