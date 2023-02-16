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
$stmt = $conn->prepare("SELECT * FROM tags;");

// Execute the statement
$stmt->execute();

// Fetch the items and their stats from the result
$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create tag
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $tag_name = $_POST['name'];
    $stmt = $conn->prepare("INSERT INTO tags (name) VALUES (:tag_name)");
    $stmt->bindParam(":tag_name", $tag_name);
    $stmt->execute();
    header('location: tags.php');
    exit();
}

// Delete tag
if (isset($_GET['delete_tag'])) {
    $tag_id = $_GET['delete_tag'];
    $stmt = $conn->prepare("DELETE FROM tags WHERE id=:tag_id");
    $stmt->bindParam(":tag_id", $tag_id);
    $stmt->execute();
    header('location: tags.php');
    exit();
}
?>

<?php include 'head.php'; ?>
<section class="content">
    <h1>Tags</h1>
    <form method="post">
        <div>
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>
        </div>
        <button type="submit">Add Tag</button>
    </form>
    <?php if (count($tags) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tags as $tag): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($tag['name']); ?>
                        </td>
                        <td><a href="edit_tag.php?tag_id=<?php echo $tag['id']; ?>"><button>Edit</button></a></td>
                        <td><a href="tags.php?delete_tag=<?php echo $tag['id']; ?>"><button>Delete</button></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>

        <p>You don't have any items yet.</p>

    <?php endif; ?>
</section>

<?php include 'footer.php'; ?>