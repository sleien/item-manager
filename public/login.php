<?php
include 'header.php';
include 'config.php';

// start session if it hasn't been started already
if (!isset($_SESSION)) {
    session_start();
}

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to the main page
    header('Location: list.php');
    exit();
}

// Check if the login form was submitted
if (isset($_POST['login'])) {

    // Connect to the database
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . "", DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit;
    }

    // Prepare the SQL statement to find the user by username and password
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = :username");

    // Bind the values to the prepared statement
    $stmt->bindParam(':username', $_POST['username']);

    // Execute the statement
    $stmt->execute();

    // Fetch the user record from the result
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify the password
    if ($user && password_verify($_POST['password'], $user['password'])) {
        // Set the user ID in the session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['wishlist'] = 0;

        // Redirect to the main page
        header('Location: list.php');
        exit;
    } else {
        // Display an error message
        $error = "Invalid username or password.";
    }

    // Close the database connection
    $conn = null;
}

?>

<?php include 'head.php'; ?>
<section class="content login">
    <h1>Login</h1>

    <?php if (isset($error)): ?>
        <p>
            <?php echo $error; ?>
        </p>
    <?php endif; ?>

    <form method="post">
        <div>
            <label>Username:</label>
            <input type="text" name="username" required>
        </div>

        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" name="login">Login</button>
        <p>Don't have an account?</p>
        <a href="register.php"><button type="button">Register</button></a>
    </form>

</section>

<?php include 'footer.php'; ?>