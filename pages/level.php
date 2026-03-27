<?php
require_once '../includes/db.php';

if (!isLoggedIn()) {
    redirect('pages/login.php');
}

$levelId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId = $_SESSION['user_id'];

$level = $db->fetch("SELECT * FROM levels WHERE id = ?", [$levelId]);

if (!$level) {
    redirect('dashboard.php');
}

if ($_SESSION['total_points'] < $level['required_points']) {
    redirect('dashboard.php');
}

$pageTitle = $level['name'];

$questions = $db->fetchAll(
    "SELECT q.*, (SELECT id FROM user_progress WHERE user_id = ? AND question_id = q.id) as completed 
     FROM questions q 
     WHERE q.level_id = ? 
     ORDER BY q.id ASC",
    [$userId, $levelId]
);

require_once '../includes/header.php';
?>

<div class="dashboard scale-in">
    <div class="dashboard-v2">
        <div class="back-btn">
            <a href="dashboard.php" class="nav-link" style="display: flex; align-items: center; gap: 8px;">
                <span>←</span> Back to Dashboard
            </a>
        </div>

        <div class="dashboard-header">
            <div class="user-welcome">
                <span class="premium-badge">MISSION TRACK</span>
                <h1><?php echo sanitize($level['name']); ?></h1>
                <p class="text-muted">Master the challenges below to unlock higher tiers.</p>
            </div>
            
            <div class="stat-v-card">
                <span class="stat-v-label">Total Points</span>
                <span class="stat-v-value gradient-text"><?php echo number_format($level['required_points']); ?>+</span>
            </div>
        </div>

        <div class="questions-grid">
            <?php foreach ($questions as $index => $q): 
                $isCompleted = !empty($q['completed']);
            ?>
                <div class="question-v-card animate-fade-in <?php echo $isCompleted ? 'completed' : ''; ?>" style="animation-delay: <?php echo ($index * 0.08); ?>s">
                    <div class="q-header">
                        <span class="q-lang-badge"><?php echo strtoupper($q['language']); ?></span>
                        <span class="q-pts-badge"><?php echo $q['points']; ?> PTS</span>
                    </div>
                    
                    <h3 class="q-title">
                        <?php echo sanitize($q['title']); ?>
                        <?php if ($isCompleted): ?>
                            <span class="check-icon">✓</span>
                        <?php endif; ?>
                    </h3>
                    <p class="q-desc"><?php echo sanitize(substr($q['description'], 0, 100)) . (strlen($q['description']) > 100 ? '...' : ''); ?></p>
                    
                    <div class="q-footer">
                        <a href="challenge.php?id=<?php echo $q['id']; ?>" class="btn <?php echo $isCompleted ? 'btn-glass' : 'btn-primary'; ?> btn-block">
                            <?php echo $isCompleted ? 'Review Challenge' : 'Start Mission'; ?>
                        </a>
                    </div>
                    
                    <?php if ($isCompleted): ?>
                        <div class="completed-ribbon">COMPLETED</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($questions)): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 100px; background: var(--bg-card); border-radius: 24px;">
                    <span style="font-size: 3rem; display: block; margin-bottom: 20px;">🚧</span>
                    <h3>Curriculum incoming...</h3>
                    <p class="text-muted">No challenges have been added to this level yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.question-v-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 24px;
    padding: 32px;
    position: relative;
    overflow: hidden;
    transition: var(--transition);
    display: flex;
    flex-direction: column;
}

.question-v-card:hover {
    transform: translateY(-8px);
    border-color: var(--primary);
    box-shadow: 0 15px 30px rgba(0,0,0,0.3);
}

.question-v-card.completed {
    border-color: var(--success);
    background: linear-gradient(135deg, var(--bg-card), rgba(16, 185, 129, 0.02));
}

.q-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 24px;
}

.q-lang-badge {
    font-size: 0.7rem;
    font-weight: 800;
    color: var(--text-muted);
    border: 1px solid var(--border-color);
    padding: 4px 10px;
    border-radius: 6px;
}

.q-pts-badge {
    font-size: 0.75rem;
    font-weight: 800;
    color: var(--accent);
}

.q-title {
    font-size: 1.25rem;
    font-weight: 800;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.q-desc {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-bottom: 24px;
    flex: 1;
}

.check-icon {
    color: var(--success);
    font-size: 1.1rem;
}

.completed-ribbon {
    position: absolute;
    top: 15px;
    left: -40px;
    background: var(--success);
    color: white;
    font-size: 0.6rem;
    font-weight: 900;
    padding: 4px 40px;
    transform: rotate(-45deg);
    box-shadow: 0 2px 10px var(--success-glow);
}
</style>

<?php require_once '../includes/footer.php'; ?>
