<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

if ($_SESSION['role'] !== 'owner') {
    header("Location: dashboard.php");
    exit;
}

$business_id = $_SESSION['business_id'];
$staff_id = $_GET['user_id'] ?? null;

if (!$staff_id) {
    header("Location: users.php");
    exit;
}

// Verify staff belongs to this business
$check = mysqli_query($conn, "SELECT username FROM users WHERE id = '$staff_id' AND business_id = '$business_id' AND role = 'staff'");
if (mysqli_num_rows($check) == 0) {
    header("Location: users.php?error=Invalid user");
    exit;
}
$staff = mysqli_fetch_assoc($check);

// Available features
$features = ['dashboard', 'sales', 'products', 'udharo', 'reports', 'users', 'suppliers'];

// Fetch current permissions
$current_perms = [];
$res = mysqli_query($conn, "SELECT feature_name, can_access FROM user_permissions WHERE user_id = '$staff_id'");
while ($row = mysqli_fetch_assoc($res)) {
    $current_perms[$row['feature_name']] = $row['can_access'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Permissions - <?php echo htmlspecialchars($staff['username']); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        .permission-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .perm-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .perm-item:last-child { border-bottom: none; }
        .perm-info h3 { margin: 0; font-size: 16px; text-transform: capitalize; color: #333; }
        .perm-info p { margin: 5px 0 0; font-size: 13px; color: #777; }
        
        /* Toggle Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider { background-color: #2ecc71; }
        input:checked + .slider:before { transform: translateX(24px); }
    </style>
    <script>
        function toggleAll(source) {
            checkboxes = document.getElementsByName('permissions[]');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
</head>
<body>
    <?php $page = 'users'; include '../includes/navbar.php'; ?>
    
    <div class="container" style="max-width: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
             <div>
                <a href="users.php" style="text-decoration: none; color: #666;">&larr; Back to Staff</a>
                <h1 style="margin: 5px 0 0;">Access Control</h1>
                <p style="color: #666; font-size: 14px; margin: 0;">Manage what <b><?php echo htmlspecialchars($staff['username']); ?></b> can see and do.</p>
             </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Permissions updated successfully!</div>
        <?php endif; ?>

        <form action="../controllers/permissionController.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $staff_id; ?>">
            
            <div class="permission-container">
                <?php 
                $descriptions = [
                    'dashboard' => 'View daily stats and key metrics.',
                    'sales' => 'Create new bills and manage daily sales.',
                    'products' => 'Add, edit, or delete inventory items.',
                    'udharo' => 'Manage credit interactions with customers.',
                    'reports' => 'Access sensitive financial reports.',
                    'users' => 'Manage other staff accounts.',
                    'suppliers' => 'View supplier ledgers and transactions.'
                ];
                
                foreach ($features as $feature): 
                ?>
                <div class="perm-item">
                    <div class="perm-info">
                        <h3><?php echo $feature; ?></h3>
                        <p><?php echo $descriptions[$feature] ?? 'Access this feature.'; ?></p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="permissions[<?php echo $feature; ?>]" value="1" <?php echo ($current_perms[$feature] ?? 0) ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" class="btn" style="width: 100%; background: #2ecc71; font-size: 16px;">Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>
