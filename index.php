<?php

session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
  // Redirect to the list page
  header('Location: public/list.php');
  exit();
} else {
  // Redirect to the login page
  header('Location: public/login.php');
  exit();
}

?>
