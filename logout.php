<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Set a session cookie with a short expiration time
setcookie(session_name(), '', time() - 3600, '/');

// Redirect to the home page (or any other desired location)
header("Location: index.php");
exit();
?>
