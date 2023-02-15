<?php
include 'header.php';
include 'config.php';


// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // redirect to login page if user is not logged in
    header('Location: login.php');
    exit();
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connect to the database
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . "", DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit;
    }

    // Prepare the SQL statement to insert a new item into the items table
    $stmt = $conn->prepare("
    INSERT INTO items (name, description, price, quantity, main_user_id, wishlist)
    VALUES (:name, :description, :price, :quantity, :main_user_id, :wishlist)
  ");

    // Bind the form data to the prepared statement
    $stmt->bindParam(':name', $_POST['name']);
    $stmt->bindParam(':description', $_POST['description']);
    $stmt->bindParam(':price', $_POST['price']);
    $stmt->bindParam(':quantity', $_POST['quantity']);
    $stmt->bindParam(':main_user_id', $_SESSION['user_id']);
    $wishlist = isset($_POST['wishlist']) ? 1 : 0;
    $stmt->bindParam(':wishlist', $wishlist);
    // Execute the statement and get the ID of the newly inserted item
    if ($stmt->execute()) {
        $item_id = $conn->lastInsertId();
    } else {
        // Item adding failed
        echo "Adding item failed. Please try again.";
    }

    // Link the user to the newly added item
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("
        INSERT INTO user_items (user_id, item_id)
        VALUES (:user_id, :item_id)
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':item_id', $item_id);
    $stmt->execute();

    // Redirect the user to the list page
    header('Location: list.php');
    exit();
}

?>

<section class="content">
    <h1>Add Item</h1>

    <form method="post">
        <div>
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>
        </div>

        <div>
            <label for="description">Description:</label>
            <textarea name="description" id="description"></textarea>
        </div>

        <div>
            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" id="price" required value="0">
        </div>

        <div>
            <label for="quantity">Quantity:</label>
            <input type="number" step="1" name="quantity" id="quantity" required value="1">
        </div>

        <div class="form-checkbox">
            <label for="wishlist">Wishlist:</label>
            <input type="checkbox" name="wishlist" id="wishlist">
        </div>

        <button type="submit">Add Item</button>
    </form>

</section>


<?php include 'footer.php'; ?>