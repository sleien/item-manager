<?php
include 'header.php';
include 'config.php';


// check if tag_id is set and numeric
if (!isset($_GET['tag_id']) || !is_numeric($_GET['tag_id'])) {
  header('Location: tags.php');
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

// get the tag information
$tag_id = $_GET['tag_id'];
$stmt = $conn->prepare('SELECT * FROM tags WHERE id = ?');
$stmt->execute([$tag_id]);

$tag = $stmt->fetch(PDO::FETCH_ASSOC);

// check if tag was found
if (!$tag) {
  header('Location: tags.php');
  exit;
}

// check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $stmt = $conn->prepare('UPDATE tags SET name = :name WHERE id = ' . $tag_id);
  // Bind the form data to the prepared statement
  $stmt->bindParam(':name', $_POST['name']);
  // update the item in the database
  if (!$stmt->execute()) {
    // Item adding failed
    echo "Updating item failed. Please try again.";
  }

  // redirect to the item list page
  header('Location: tags.php');
  exit;
}
?>

<section class="content">
  <h1>Edit Tag</h1>

  <form method="POST">
    <div>
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" value="<?php echo $tag['name']; ?>">
    </div>
    <button type="submit">Save</button>
  </form>

</section>
<?php include 'footer.php'; ?>