<?php
// Session authentication check
if (!isset($_SESSION)) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit;
}
?>
