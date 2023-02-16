<?php
include 'header.php';
include 'config.php';


// check if item_id is set and numeric
if (!isset($_GET['item_id']) || !is_numeric($_GET['item_id'])) {
  header('Location: list.php');
  exit;
}

// Connect to the database
try {
  $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . "", DB_USER, DB_PASS);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
  exit;
}

// get the item information
$item_id = $_GET['item_id'];
$stmt = $conn->prepare('SELECT * FROM items WHERE id = ?');
$stmt->execute([$item_id]);

$item = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare('SELECT tag.id, tag.name, CASE WHEN item.item_id IS NOT NULL THEN 1 ELSE 0 END AS is_selected FROM tags tag LEFT JOIN item_tags item ON tag.id = item.tag_id AND item.item_id = ?');
$stmt->execute([$item_id]);

$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

// check if item was found
if (!$item) {
  header('Location: list.php');
  exit;
}

// check if user is the main user for the item
if ($_SESSION['user_id'] != $item['main_user_id']) {
  header('Location: list.php');
  exit;
}

// check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  foreach ($tags as $tag) {
    if (isset($_POST['tag_' . $tag['id']]) and $tag['is_selected'] == 0) {
      $stmt = $conn->prepare('INSERT INTO item_tags (item_id, tag_id) VALUES (?, ?)');
      $stmt->execute([$item_id, $tag['id']]);
    } elseif (!isset($_POST['tag_' . $tag['id']]) and $tag['is_selected'] == 1) {
      $stmt = $conn->prepare('DELETE FROM item_tags WHERE item_id = ? AND tag_id = ?');
      $stmt->execute([$item_id, $tag['id']]);
    }
  }
  $stmt = $conn->prepare('UPDATE items SET name = :name, description = :description, price = :price, quantity = :quantity, link = :link, wishlist = :wishlist WHERE id = ' . $item_id);
  // Bind the form data to the prepared statement
  $stmt->bindParam(':name', $_POST['name']);
  $stmt->bindParam(':description', $_POST['description']);
  $stmt->bindParam(':price', $_POST['price']);
  $stmt->bindParam(':quantity', $_POST['quantity']);
  $link = $_POST['link'] != "" ? (substr($_POST['link'], 0, 4) === 'http') ? $_POST['link'] : 'https://' . $_POST['link'] : "";
  $stmt->bindParam(':link', $link);
  $wishlist = isset($_POST['wishlist']) ? 1 : 0;
  $stmt->bindParam(':wishlist', $wishlist);
  // update the item in the database
  if (!$stmt->execute()) {
    // Item adding failed
    echo "Updating item failed. Please try again.";
  }

  // redirect to the item list page
  header('Location: list.php');
  exit;
}
?>

<?php include 'head.php'; ?>
<section class="content">
  <h1>Edit Item</h1>

  <form method="POST">
    <div>
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" value="<?php echo $item['name']; ?>">
    </div>

    <div>
      <label for="description">Description:</label>
      <textarea id="description" name="description"><?php echo $item['description']; ?></textarea>
    </div>
    <div>
      <label for="description">Price:</label>
      <input type="number" step="0.01" name="price" id="price" required value="<?php echo $item['price']; ?>">
    </div>
    <div>
      <label for="description">Quantity:</label>
      <input type="number" step="1" name="quantity" id="quantity" required value="<?php echo $item['quantity']; ?>">
    </div>

    <div>
      <label for="link">Link:</label>
      <input type="text" name="link" id="link" value="<?php echo $item['link']; ?>" placeholder="https://example.com"
        pattern="^(https?://)?([\w-]+\.)+[\w-]+(/[\w- ./?%&=]*)?$">
    </div>

    <div class="form-checkbox">
      <label for="wishlist">Wishlist:</label>
      <input type="checkbox" name="wishlist" id="wishlist" <?php if ($item['wishlist'] == 1)
        echo 'checked="checked"'; ?>>
    </div>

    <div>
      <select multiple multiselect-search="true" id="sel1">
        <?php foreach ($tags as $tag): ?>
          <option id="tag_<?php echo $tag['id'];?>"<?php if($tag['is_selected'] == 1) : echo " selected"; endif?>><?php echo $tag['name']; ?></option>
        <?php endforeach ?>
      </select>
    </div>
    <button type="submit">Save</button>
  </form>

</section>
<?php include 'footer.php'; ?>