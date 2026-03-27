<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Core Stylesheet with Cache Busting -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="<?php echo isset($bodyClass) ? $bodyClass : ''; ?>">
    <nav class="navbar">
        <div class="nav-container">
            <a href="<?php echo SITE_URL; ?>" class="nav-logo">
                <div class="logo-icon">&#60;/&#62;</div>
                <span>CODE POINT</span>
            </a>
            
            <div class="nav-links">
                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>/pages/dashboard.php" class="nav-link <?php echo ($pageTitle == 'Dashboard') ? 'active' : ''; ?>">Dashboard</a>
                    <a href="<?php echo SITE_URL; ?>/pages/leaderboard.php" class="nav-link <?php echo ($pageTitle == 'Leaderboard') ? 'active' : ''; ?>">Leaderboard</a>
                    
                    <?php if (isAdmin()): ?>
                        <a href="<?php echo SITE_URL; ?>/admin/" class="nav-link">Admin</a>
                    <?php endif; ?>
                    
                    <div class="nav-user-profile">
                        <div class="user-info">
                            <span class="u-points"><?php echo number_format($_SESSION['total_points'] ?? 0); ?> <span>PTS</span></span>
                            <span class="u-name"><?php echo sanitize($_SESSION['username']); ?></span>
                        </div>
                        <a href="<?php echo SITE_URL; ?>/pages/logout.php" class="btn btn-glass btn-sm">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/pages/login.php" class="nav-link">Login</a>
                    <a href="<?php echo SITE_URL; ?>/pages/register.php" class="btn btn-primary btn-sm">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="main-content">
