<?php
// Staff accounts (add/delete)
require_once '../config/db.php';

// Add User
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'] ?? 'staff';
    
    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $password, $role);
    
    if ($stmt->execute()) {
        header("Location: ../public/users.php?success=1");
    } else {
        header("Location: ../public/users.php?error=1");
    }
    exit;
}

// Delete User
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $sql = "DELETE FROM users WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: ../public/users.php?deleted=1");
    } else {
        header("Location: ../public/users.php?error=1");
    }
    exit;
}
?>
