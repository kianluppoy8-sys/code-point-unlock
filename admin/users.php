<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('pages/login.php');
}

$pageTitle = 'Manage Users';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0 && $id != $_SESSION['user_id']) {
            $db->execute("DELETE FROM users WHERE id = ? AND is_admin = 0", [$id]);
            $success = 'User deleted successfully';
        } else {
            $error = 'Cannot delete this user';
        }
    } elseif ($action === 'reset_progress') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $db->execute("DELETE FROM user_progress WHERE user_id = ?", [$id]);
            $db->execute("DELETE FROM submissions WHERE user_id = ?", [$id]);
            $db->execute("UPDATE users SET total_points = 0 WHERE id = ?", [$id]);
            $success = 'User progress has been reset';
        }
    } elseif ($action === 'toggle_admin') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0 && $id != $_SESSION['user_id']) {
            $user = $db->fetch("SELECT is_admin FROM users WHERE id = ?", [$id]);
            if ($user) {
                $newStatus = $user['is_admin'] ? 0 : 1;
                $db->execute("UPDATE users SET is_admin = ? WHERE id = ?", [$newStatus, $id]);
                $success = $newStatus ? 'User is now an admin' : 'Admin privileges removed';
            }
        }
    } elseif ($action === 'update_points') {
        $id = (int)($_POST['id'] ?? 0);
        $points = (int)($_POST['points'] ?? 0);
        if ($id > 0 && $points >= 0) {
            $db->execute("UPDATE users SET total_points = ? WHERE id = ?", [$points, $id]);
            $success = 'Points updated successfully';
        }
    }
}

$users = $db->fetchAll("
    SELECT u.*, 
           (SELECT COUNT(*) FROM user_progress WHERE user_id = u.id) as questions_solved,
           (SELECT COUNT(*) FROM submissions WHERE user_id = u.id) as total_submissions
    FROM users u 
    ORDER BY u.total_points DESC, u.created_at
");

include '../includes/header.php';
?>

<div class="admin-page">
    <div class="admin-header">
        <a href="index.php" class="back-link">← Back to Dashboard</a>
        <h1>Manage Users</h1>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="admin-section full-width">
        <div class="section-header">
            <h2>All Users (<?php echo count($users); ?>)</h2>
        </div>
        
        <?php if (empty($users)): ?>
            <p class="text-muted">No users found.</p>
        <?php else: ?>
            <div class="users-table-wrapper">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Points</th>
                            <th>Solved</th>
                            <th>Submissions</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr class="<?php echo $user['is_admin'] ? 'admin-row' : ''; ?>">
                                <td>
                                    <strong><?php echo sanitize($user['username']); ?></strong>
                                    <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                        <span class="you-badge">You</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo sanitize($user['email']); ?></td>
                                <td>
                                    <form method="POST" class="inline-points-form">
                                        <input type="hidden" name="action" value="update_points">
                                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                        <input type="number" name="points" value="<?php echo $user['total_points']; ?>" min="0" class="points-input">
                                        <button type="submit" class="btn-icon" title="Update Points">💾</button>
                                    </form>
                                </td>
                                <td><?php echo $user['questions_solved']; ?></td>
                                <td><?php echo $user['total_submissions']; ?></td>
                                <td>
                                    <?php if ($user['is_admin']): ?>
                                        <span class="role-badge admin">Admin</span>
                                    <?php else: ?>
                                        <span class="role-badge player">Player</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <form method="POST" class="inline-form">
                                                <input type="hidden" name="action" value="toggle_admin">
                                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline" title="Toggle Admin">
                                                    <?php echo $user['is_admin'] ? 'Remove Admin' : 'Make Admin'; ?>
                                                </button>
                                            </form>
                                            <form method="POST" class="inline-form" onsubmit="return confirm('Reset all progress for this user?')">
                                                <input type="hidden" name="action" value="reset_progress">
                                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline" title="Reset Progress">Reset</button>
                                            </form>
                                            <?php if (!$user['is_admin']): ?>
                                                <form method="POST" class="inline-form" onsubmit="return confirm('Delete this user permanently?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
