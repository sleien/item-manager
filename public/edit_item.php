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
  // sanitize input data
  $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
  $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
  $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
  $quantity = filter_var($_POST['quantity'], FILTER_SANITIZE_STRING);

  // update the item in the database
  $stmt = $conn->prepare('UPDATE items SET name = ?, description = ?, price = ?, quantity = ? WHERE id = ?');
  $stmt->execute([$name, $description, $price, $quantity, $item_id]);

  // redirect to the item list page
  header('Location: list.php');
  exit;
}
?>

<section class="content">
  <h1>Edit Item</h1>

  <form method="POST">
    <div>
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" value="<?php echo $item['name']; ?>"><br>
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
    <button type="submit">Save</button>
  </form>

</section>
<?php include 'footer.php'; ?>