<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('pages/login.php');
}

$pageTitle = 'Manage Levels';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $name = sanitize($_POST['name'] ?? '');
        $required_points = (int)($_POST['required_points'] ?? 0);
        $display_order = (int)($_POST['display_order'] ?? 1);
        
        if (empty($name)) {
            $error = 'Level name is required';
        } else {
            $db->insert(
                "INSERT INTO levels (name, required_points, display_order) VALUES (?, ?, ?)",
                [$name, $required_points, $display_order]
            );
            $success = 'Level added successfully';
        }
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');
        $required_points = (int)($_POST['required_points'] ?? 0);
        $display_order = (int)($_POST['display_order'] ?? 1);
        
        if (empty($name) || $id <= 0) {
            $error = 'Invalid data';
        } else {
            $db->execute(
                "UPDATE levels SET name = ?, required_points = ?, display_order = ? WHERE id = ?",
                [$name, $required_points, $display_order, $id]
            );
            $success = 'Level updated successfully';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $db->execute("DELETE FROM levels WHERE id = ?", [$id]);
            $success = 'Level deleted successfully';
        }
    }
}

$levels = $db->fetchAll("SELECT * FROM levels ORDER BY display_order, id");
$editLevel = null;
if (isset($_GET['edit'])) {
    $editLevel = $db->fetch("SELECT * FROM levels WHERE id = ?", [(int)$_GET['edit']]);
}

include '../includes/header.php';
?>

<div class="admin-page">
    <div class="admin-header">
        <a href="index.php" class="back-link">← Back to Dashboard</a>
        <h1>Manage Levels</h1>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="admin-grid">
        <div class="admin-section">
            <div class="section-header">
                <h2><?php echo $editLevel ? 'Edit Level' : 'Add New Level'; ?></h2>
            </div>
            <form method="POST" class="admin-form">
                <input type="hidden" name="action" value="<?php echo $editLevel ? 'edit' : 'add'; ?>">
                <?php if ($editLevel): ?>
                    <input type="hidden" name="id" value="<?php echo $editLevel['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Level Name</label>
                    <input type="text" name="name" value="<?php echo $editLevel ? sanitize($editLevel['name']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Required Points to Unlock</label>
                    <input type="number" name="required_points" value="<?php echo $editLevel ? $editLevel['required_points'] : 0; ?>" min="0" required>
                </div>
                
                <div class="form-group">
                    <label>Display Order</label>
                    <input type="number" name="display_order" value="<?php echo $editLevel ? $editLevel['display_order'] : (count($levels) + 1); ?>" min="1" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?php echo $editLevel ? 'Update Level' : 'Add Level'; ?></button>
                    <?php if ($editLevel): ?>
                        <a href="levels.php" class="btn btn-outline">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="admin-section">
            <div class="section-header">
                <h2>All Levels (<?php echo count($levels); ?>)</h2>
            </div>
            
            <?php if (empty($levels)): ?>
                <p class="text-muted">No levels created yet.</p>
            <?php else: ?>
                <div class="levels-list">
                    <?php foreach ($levels as $level): ?>
                        <div class="level-item">
                            <div class="level-info">
                                <strong><?php echo sanitize($level['name']); ?></strong>
                                <span class="level-pts"><?php echo number_format($level['required_points']); ?> pts required | Order: <?php echo $level['display_order']; ?></span>
                            </div>
                            <div class="level-actions">
                                <a href="?edit=<?php echo $level['id']; ?>" class="btn-icon" title="Edit">✏️</a>
                                <form method="POST" class="inline-form" onsubmit="return confirm('Delete this level? All questions in this level will also be deleted.')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $level['id']; ?>">
                                    <button type="submit" class="btn-icon delete" title="Delete">🗑️</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
