<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('pages/login.php');
}

$pageTitle = 'Admin Dashboard';

$stats = [
    'total_users' => $db->fetch("SELECT COUNT(*) as count FROM users WHERE is_admin = 0")['count'] ?? 0,
    'total_levels' => $db->fetch("SELECT COUNT(*) as count FROM levels")['count'] ?? 0,
    'total_questions' => $db->fetch("SELECT COUNT(*) as count FROM questions")['count'] ?? 0,
    'total_submissions' => $db->fetch("SELECT COUNT(*) as count FROM submissions")['count'] ?? 0,
];

include '../includes/header.php';
?>

<div class="admin-page">
    <div class="admin-header">
        <h1>Admin Dashboard</h1>
        <p class="text-muted">Manage your game content and settings</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <span class="stat-value"><?php echo number_format($stats['total_users']); ?></span>
                <span class="stat-label">Players</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-info">
                <span class="stat-value"><?php echo number_format($stats['total_levels']); ?></span>
                <span class="stat-label">Levels</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">❓</div>
            <div class="stat-info">
                <span class="stat-value"><?php echo number_format($stats['total_questions']); ?></span>
                <span class="stat-label">Questions</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📝</div>
            <div class="stat-info">
                <span class="stat-value"><?php echo number_format($stats['total_submissions']); ?></span>
                <span class="stat-label">Submissions</span>
            </div>
        </div>
    </div>

    <div class="admin-nav-grid">
        <a href="levels.php" class="admin-nav-card">
            <div class="admin-nav-icon">📊</div>
            <h3>Manage Levels</h3>
            <p>Add, edit, delete levels and set required points</p>
        </a>
        <a href="questions.php" class="admin-nav-card">
            <div class="admin-nav-icon">❓</div>
            <h3>Manage Questions</h3>
            <p>Create and manage coding challenges</p>
        </a>
        <a href="categories.php" class="admin-nav-card">
            <div class="admin-nav-icon">🏷️</div>
            <h3>Categories</h3>
            <p>Manage programming language categories</p>
        </a>
        <a href="users.php" class="admin-nav-card">
            <div class="admin-nav-icon">👥</div>
            <h3>Manage Users</h3>
            <p>View and manage player accounts</p>
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
