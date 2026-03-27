<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('pages/login.php');
}

$pageTitle = 'Manage Categories';
$error = '';
$success = '';

$isPostgres = DB_TYPE === 'pgsql';
$checkTable = $isPostgres 
    ? $db->fetch("SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'categories')")
    : $db->fetch("SHOW TABLES LIKE 'categories'");

$tableExists = $isPostgres ? ($checkTable['exists'] ?? false) : !empty($checkTable);

if (!$tableExists) {
    if ($isPostgres) {
        $db->getConnection()->exec("
            CREATE TABLE categories (
                id SERIAL PRIMARY KEY,
                name VARCHAR(50) UNIQUE NOT NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    } else {
        $db->getConnection()->exec("
            CREATE TABLE categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) UNIQUE NOT NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }
    $defaultCategories = ['HTML', 'CSS', 'JavaScript', 'SQL', 'Java', 'Python', 'PHP'];
    foreach ($defaultCategories as $cat) {
        $db->insert("INSERT INTO categories (name) VALUES (?)", [$cat]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $name = sanitize($_POST['name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        
        if (empty($name)) {
            $error = 'Category name is required';
        } else {
            $exists = $db->fetch("SELECT id FROM categories WHERE LOWER(name) = LOWER(?)", [$name]);
            if ($exists) {
                $error = 'Category already exists';
            } else {
                $db->insert("INSERT INTO categories (name, description) VALUES (?, ?)", [$name, $description]);
                $success = 'Category added successfully';
            }
        }
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        
        if (empty($name) || $id <= 0) {
            $error = 'Invalid data';
        } else {
            $db->execute("UPDATE categories SET name = ?, description = ? WHERE id = ?", [$name, $description, $id]);
            $success = 'Category updated successfully';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $db->execute("DELETE FROM categories WHERE id = ?", [$id]);
            $success = 'Category deleted successfully';
        }
    }
}

$categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");
$editCategory = null;
if (isset($_GET['edit'])) {
    $editCategory = $db->fetch("SELECT * FROM categories WHERE id = ?", [(int)$_GET['edit']]);
}

include '../includes/header.php';
?>

<div class="admin-page">
    <div class="admin-header">
        <a href="index.php" class="back-link">← Back to Dashboard</a>
        <h1>Manage Categories (Languages)</h1>
        <p class="text-muted">Manage programming language categories for questions</p>
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
                <h2><?php echo $editCategory ? 'Edit Category' : 'Add New Category'; ?></h2>
            </div>
            <form method="POST" class="admin-form">
                <input type="hidden" name="action" value="<?php echo $editCategory ? 'edit' : 'add'; ?>">
                <?php if ($editCategory): ?>
                    <input type="hidden" name="id" value="<?php echo $editCategory['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Category Name (Language)</label>
                    <input type="text" name="name" value="<?php echo $editCategory ? sanitize($editCategory['name']) : ''; ?>" placeholder="e.g., Python, Ruby, Go" required>
                </div>
                
                <div class="form-group">
                    <label>Description (Optional)</label>
                    <textarea name="description" rows="3"><?php echo $editCategory ? sanitize($editCategory['description'] ?? '') : ''; ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?php echo $editCategory ? 'Update Category' : 'Add Category'; ?></button>
                    <?php if ($editCategory): ?>
                        <a href="categories.php" class="btn btn-outline">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="admin-section">
            <div class="section-header">
                <h2>All Categories (<?php echo count($categories); ?>)</h2>
            </div>
            
            <?php if (empty($categories)): ?>
                <p class="text-muted">No categories created yet.</p>
            <?php else: ?>
                <div class="categories-grid">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-item">
                            <div class="category-info">
                                <strong><?php echo sanitize($category['name']); ?></strong>
                                <?php if (!empty($category['description'])): ?>
                                    <p class="text-muted"><?php echo sanitize($category['description']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="category-actions">
                                <a href="?edit=<?php echo $category['id']; ?>" class="btn-icon" title="Edit">✏️</a>
                                <form method="POST" class="inline-form" onsubmit="return confirm('Delete this category?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
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
