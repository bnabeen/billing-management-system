<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    // Only owners can access this controller 
    // (Except maybe for profile update, but we are doing staff mgmt here)
    header("Location: ../public/index.php");
    exit();
}

$business_id = $_SESSION['business_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['action'] == 'create') {
        $username = $_POST['username'];
        $phone = $_POST['phone'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = 'staff';
        $status = 'active'; // Added by owner, so valid

        // Check duplicate
        $check = mysqli_query($conn, "SELECT id FROM users WHERE business_id = '$business_id' AND username = '$username'");
        if (mysqli_num_rows($check) > 0) {
            header("Location: ../public/users.php?error=Username already exists");
            exit();
        }

        $query = "INSERT INTO users (business_id, username, phone, password, role, status) VALUES ('$business_id', '$username', '$phone', '$password', '$role', '$status')";
        
        if (mysqli_query($conn, $query)) {
            header("Location: ../public/users.php?success=Staff added successfully");
        } else {
            header("Location: ../public/users.php?error=Failed to add staff");
        }
    }
} elseif (isset($_GET['action'])) {
    $id = $_GET['id'];
    
    // Security: Ensure user belongs to this business
    // And is not self (though UI handles it, backend should too)
    if ($id == $_SESSION['user_id']) {
         header("Location: ../public/users.php?error=Cannot delete yourself");
         exit();
    }

    if ($_GET['action'] == 'delete') {
         $query = "DELETE FROM users WHERE id = '$id' AND business_id = '$business_id'";
         if (mysqli_query($conn, $query)) {
             header("Location: ../public/users.php?success=User removed");
         } else {
             header("Location: ../public/users.php?error=Delete failed");
         }
    }
    
    if ($_GET['action'] == 'approve') {
         $query = "UPDATE users SET status='active' WHERE id = '$id' AND business_id = '$business_id'";
         if (mysqli_query($conn, $query)) {
             header("Location: ../public/users.php?success=User approved");
         } else {
             header("Location: ../public/users.php?error=Approval failed");
         }
    }
}
