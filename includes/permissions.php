<?php
function hasPermission($conn, $feature) {
    if (!isset($_SESSION['user_id'])) return false;
    
    // Owners have full access to everything
    if ($_SESSION['role'] === 'owner') return true;
    
    $user_id = $_SESSION['user_id'];
    $feature = mysqli_real_escape_string($conn, $feature);
    
    $query = "SELECT can_access FROM user_permissions WHERE user_id = '$user_id' AND feature_name = '$feature'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return (bool)$row['can_access'];
    }
    
    // Default to no access if not defined
    return false;
}

function checkAccess($conn, $feature) {
    if (!hasPermission($conn, $feature)) {
        header("Location: dashboard.php?error=Access Denied to $feature");
        exit();
    }
}
?>
