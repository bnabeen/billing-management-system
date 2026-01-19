<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

$business_id = $_SESSION['business_id'];
$current_role = $_SESSION['role'];

// Only Owner can see this page? Or Staff can see list but not add/delete?
// Requirement: "give the level of access as well"
// Let's allow Owner to do everything. Staff can maybe just view or Profile.
// For now, listing is visible to all authenticated users of the busines (like a team directory), but actions restricted.

$users_list = [];
$q = "SELECT * FROM users WHERE business_id = '$business_id'";
$res = mysqli_query($conn, $q);
while ($row = mysqli_fetch_assoc($res)) {
    $users_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users - Kirana Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <!-- Navbar -->
    <?php $page = 'users'; include '../includes/navbar.php'; ?>

    <div class="container">
        <div class="products-header">
            <div style="display: flex; align-items: center; gap: 15px;">
                 <a href="dashboard.php" class="btn-back" style="text-decoration: none; color: #666; font-size: 24px;">&larr;</a>
                 <h1>Team Management</h1>
            </div>
            
            <?php if ($current_role === 'owner'): ?>
                <button onclick="openUserModal()" class="btn-add">+ Add Staff</button>
            <?php endif; ?>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <?php if ($current_role === 'owner'): ?><th>Actions</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users_list as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                            <td>
                                <span class="role-badge" style="background: <?php echo $user['role'] === 'owner' ? '#e6f7ff' : '#fff7e6'; ?>; color: <?php echo $user['role'] === 'owner' ? '#1890ff' : '#fa8c16'; ?>; padding: 2px 8px; border-radius: 4px; border: 1px solid currentColor;">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td>
                                <span style="color: <?php echo $user['status'] === 'active' ? 'green' : 'orange'; ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </td>
                            <?php if ($current_role === 'owner'): ?>
                            <td>
                                <?php if ($user['role'] !== 'owner'): // Cannot delete owner (self) easily ?>
                                    <a href="permissions.php?user_id=<?php echo $user['id']; ?>" class="btn-edit" style="margin-right: 5px;">Permissions</a>
                                    <a href="../controllers/userController.php?action=delete&id=<?php echo $user['id']; ?>" class="btn-delete" onclick="return confirm('Remove this user?');">Remove</a>
                                    
                                    <?php if ($user['status'] === 'pending'): ?>
                                        <a href="../controllers/userController.php?action=approve&id=<?php echo $user['id']; ?>" class="btn-edit" style="margin-right: 5px;">Approve</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Staff</h2>
                <button class="modal-close" onclick="closeUserModal()">&times;</button>
            </div>
            <form action="../controllers/userController.php" method="POST" class="modal-form">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeUserModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Add Staff</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const userModal = document.getElementById('userModal');
        function openUserModal() { userModal.style.display = 'flex'; }
        function closeUserModal() { userModal.style.display = 'none'; }
        
        // Close on outside click
        userModal.addEventListener('click', function(event) {
            if (event.target === userModal) closeUserModal();
        });
    </script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
