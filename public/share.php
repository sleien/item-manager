<?php
include 'header.php';
include 'config.php';

// check if item id is provided
if (!isset($_GET['item_id'])) {
    // redirect to list page if item id is not provided
    header('Location: list.php');
    exit();
}

// get item id from query string
$item_id = $_GET['item_id'];

// Connect to the database
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . "", DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
// prepare and execute the query to get main user id for the item
$query = 'SELECT main_user_id FROM items WHERE id = :item_id';
$stmt = $conn->prepare($query);
$stmt->bindParam(':item_id', $item_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    // item found, get main user id
    $main_user_id = $row['main_user_id'];
} else {
    // item not found, redirect to list page
    header('Location: list.php');
    exit();
}

// check if user is the main user for the item
if ($_SESSION['user_id'] != $main_user_id) {

    // user is not the main user, redirect to list page
    header('Location: list.php');
    exit();
}

// get list of users who are connected to the item
$query = "SELECT users.id, users.username, users.email FROM users LEFT JOIN (SELECT user_id FROM user_items WHERE item_id = $item_id) AS shared_users ON users.id = shared_users.user_id WHERE shared_users.user_id IS NULL";
$result = $conn->query($query);
$query = "SELECT users.id, users.username, users.email FROM users LEFT JOIN (SELECT user_id FROM user_items WHERE item_id = $item_id and user_id != $main_user_id) AS shared_users ON users.id = shared_users.user_id WHERE shared_users.user_id IS NOT NULL";
$result2 = $conn->query($query);

?>

<?php include 'head.php'; ?>
<section class="content">
    <h1>Share Item</h1>

    <?php if ($result->rowCount() > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Share with:</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                    <tr>
                        <td>
                            <?php echo $row['username'] ?> -
                            <?php echo $row['email'] ?>
                        </td>
                        <td><a href="share_action.php?item_id=<?php echo $item_id ?>&share_user_id=<?php echo $row['id'] ?>"><button
                                    type="button">Share</button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>There is no one to share the item with!</p>
    <?php endif; ?>

    <?php if ($result2->rowCount() > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Shared with:</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result2 as $row): ?>
                    <tr>
                        <td>
                            <?php echo $row['username'] ?> -
                            <?php echo $row['email'] ?>
                        </td>
                        <td><a href="unshare.php?item_id=<?php echo $item_id ?>&unshare_user_id=<?php echo $row['id'] ?>"><button
                                    type="button">Unshare</button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Not yet shared.</p>
    <?php endif; ?>

    <p><a href="list.php">Back to list</a></p>

</section>
<?php include 'footer.php'; ?>