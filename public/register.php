<?php

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Prepare the SQL statement to insert a new user into the users table
    $stmt = $conn->prepare("
    INSERT INTO users (username, password, email)
    VALUES (:username, :password, :email)
  ");

    // Bind the form data to the prepared statement
    $stmt->bindParam(':username', $_POST['username']);
    $stmt->bindParam(':password', password_hash($_POST['password'], PASSWORD_DEFAULT));
    $stmt->bindParam(':email', $_POST['email']);

    // Execute the statement
    if ($stmt->execute()) {
        // Registration successful
        header('Location: login.php');
        exit();
    } else {
        // Registration failed
        echo "Registration failed. Please try again.";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Register - Item Manager</title>
</head>

<body>

    <h1>Register</h1>

    <form method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        <br>
        <button type="submit">Register</button>
    </form>

</body>

</html>