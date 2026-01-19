<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../public/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $permissions = $_POST['permissions'] ?? [];
    $business_id = $_SESSION['business_id'];

    // Verify user belongs to business
    $check = mysqli_query($conn, "SELECT id FROM users WHERE id = '$user_id' AND business_id = '$business_id'");
    if (mysqli_num_rows($check) == 0) {
        header("Location: ../public/users.php?error=Unauthorized");
        exit();
    }

    // Clear old permissions
    mysqli_query($conn, "DELETE FROM user_permissions WHERE user_id = '$user_id'");

    // Insert new permissions
    $features = ['dashboard', 'sales', 'products', 'udharo', 'reports', 'users', 'suppliers'];
    foreach ($features as $feature) {
        $can_access = isset($permissions[$feature]) ? 1 : 0;
        $q = "INSERT INTO user_permissions (user_id, feature_name, can_access) VALUES ('$user_id', '$feature', '$can_access')";
        mysqli_query($conn, $q);
    }

    header("Location: ../public/permissions.php?user_id=$user_id&success=1");
    exit();
}
?>
