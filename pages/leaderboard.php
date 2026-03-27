<?php
require_once '../includes/db.php';
$pageTitle = 'Leaderboard';

if (!isLoggedIn()) {
    redirect('pages/login.php');
}

$users = $db->fetchAll(
    "SELECT username, total_points 
     FROM users 
     WHERE is_admin = 0 
     ORDER BY total_points DESC, username ASC 
     LIMIT 50"
);

require_once '../includes/header.php';
?>

<div class="dashboard animate-fade-in">
    <div class="dashboard-header">
        <div class="user-welcome">
            <span class="premium-badge">HALL OF FAME</span>
            <h1>Global <span class="gradient-text">Leaderboard</span></h1>
            <p class="text-muted">The top programmers contributing to the ICT Club ecosystem.</p>
        </div>
    </div>

    <div class="leaderboard-container">
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th style="width: 100px;">Rank</th>
                    <th>Codepoint Master</th>
                    <th style="text-align: right;">Total Power</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $index => $user): 
                    $rank = $index + 1;
                    $rankClass = ($rank <= 3) ? 'rank-' . $rank : '';
                ?>
                    <tr class="animate-fade-in" style="animation-delay: <?php echo ($index * 0.05); ?>s">
                        <td>
                            <div class="rank-badge <?php echo $rankClass; ?>">
                                <?php echo $rank; ?>
                            </div>
                        </td>
                        <td>
                            <div class="lb-user">
                                <div class="lb-avatar">
                                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                </div>
                                <span class="lb-user-name">
                                    <?php echo sanitize($user['username']); ?>
                                    <?php if ($rank === 1): ?> 👑 <?php endif; ?>
                                </span>
                            </div>
                        </td>
                        <td style="text-align: right;">
                            <span class="lb-points"><?php echo number_format($user['total_points']); ?></span>
                            <span class="text-muted" style="font-size: 0.75rem; font-weight: 700; margin-left: 4px;">PTS</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 60px; color: var(--text-muted);">
                            No masters found in the hall of fame yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.lb-avatar {
    width: 32px;
    height: 32px;
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 800;
    color: var(--primary);
}

tr:hover .lb-avatar {
    background: var(--primary-glow);
    border-color: var(--primary);
}

.leaderboard-table tr {
    transition: var(--transition);
}

.leaderboard-table tr:hover {
    background: rgba(255, 255, 255, 0.02);
}
</style>

<?php require_once '../includes/footer.php'; ?>
