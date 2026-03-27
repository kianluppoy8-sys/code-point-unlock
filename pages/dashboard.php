<?php
require_once '../includes/db.php';
$pageTitle = 'Dashboard';

if (!isLoggedIn()) {
    redirect('pages/login.php');
}

$userId = $_SESSION['user_id'];
$user = $db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);

// Fetch levels and user progress
$levels = $db->fetchAll("SELECT * FROM levels ORDER BY display_order ASC");
$completedCount = $db->fetch("SELECT COUNT(DISTINCT question_id) as count FROM user_progress WHERE user_id = ?", [$userId])['count'];
$totalQuestions = $db->fetch("SELECT COUNT(*) as count FROM questions")['count'];

require_once '../includes/header.php';
?>

<div class="dashboard scale-in">
    <div class="dashboard-header animate-fade-in">
        <div class="user-welcome">
            <span class="premium-badge">WELCOME BACK</span>
            <h1>Hello, <span class="gradient-text"><?php echo sanitize($user['username']); ?></span></h1>
            <p class="text-muted">You're doing great! Keep solving challenges to climb the ranks.</p>
        </div>
        
        <div class="user-stats-summary">
            <div class="stat-v-card">
                <span class="stat-v-label">Total Points</span>
                <span class="stat-v-value gradient-text"><?php echo number_format($user['total_points']); ?></span>
            </div>
            <div class="stat-v-card">
                <span class="stat-v-label">Progress</span>
                <span class="stat-v-value"><?php echo $completedCount; ?>/<?php echo $totalQuestions; ?></span>
            </div>
        </div>
    </div>

    <div class="section-divider">
        <h2 class="section-title">Available Levels</h2>
        <div class="divider-line"></div>
    </div>

    <div class="levels-grid">
        <?php foreach ($levels as $index => $level): 
            $isLocked = $user['total_points'] < $level['required_points'];
            $levelProgress = $db->fetch(
                "SELECT COUNT(*) as count FROM user_progress up 
                 JOIN questions q ON up.question_id = q.id 
                 WHERE up.user_id = ? AND q.level_id = ?",
                [$userId, $level['id']]
            )['count'];
            $levelTotal = $db->fetch("SELECT COUNT(*) as count FROM questions WHERE level_id = ?", [$level['id']])['count'];
        ?>
            <div class="level-card animate-fade-in <?php echo $isLocked ? 'locked' : ''; ?>" style="animation-delay: <?php echo ($index * 0.1); ?>s">
                <?php if ($isLocked): ?>
                    <span class="level-badge locked-badge">Locked · <?php echo number_format($level['required_points']); ?> PTS</span>
                <?php else: ?>
                    <span class="level-badge">Level <?php echo $level['display_order']; ?></span>
                <?php endif; ?>
                
                <div class="level-card-inner">
                    <div class="level-icon">
                        <?php echo $isLocked ? '🔒' : '🚀'; ?>
                    </div>
                    <h3 class="level-title"><?php echo sanitize($level['name']); ?></h3>
                    <p class="level-desc">Master the basics of this track and earn rewards.</p>
                </div>

                <div class="level-footer">
                    <div class="level-progress-info">
                        <?php if (!$isLocked): ?>
                            <span class="stat-txt"><?php echo $levelProgress; ?> / <?php echo $levelTotal; ?> Solved</span>
                        <?php else: ?>
                            <span class="stat-txt">Need <?php echo number_format($level['required_points'] - $user['total_points']); ?> more pts</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!$isLocked): ?>
                        <a href="level.php?id=<?php echo $level['id']; ?>" class="btn btn-primary btn-block">
                            Enter Level
                        </a>
                    <?php else: ?>
                        <button class="btn btn-glass btn-block" disabled>
                            Keep Coding
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.section-divider {
    margin-bottom: 32px;
    display: flex;
    align-items: center;
    gap: 20px;
}

.divider-line {
    flex: 1;
    height: 1px;
    background: var(--border-color);
}

.section-title {
    font-size: 1.1rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: var(--text-muted);
}

.scale-in {
    animation: scaleIn 0.4s ease-out forwards;
}

@keyframes scaleIn {
    from { opacity: 0; transform: scale(0.98); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<?php require_once '../includes/footer.php'; ?>
