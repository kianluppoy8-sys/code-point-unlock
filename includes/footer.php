    </main>
    
    <?php if (!isset($bodyClass) || $bodyClass !== 'challenge-body'): ?>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="logo-icon small">&#60;/&#62;</div>
                    <span class="brand-text">The Code Point Unlock</span>
                    <p class="brand-desc">Empowering the next generation of developers from ICT Club ACLC Taytay.</p>
                </div>
                <div class="footer-links-group">
                    <h4>Platform</h4>
                    <a href="<?php echo SITE_URL; ?>/pages/dashboard.php">Dashboard</a>
                    <a href="<?php echo SITE_URL; ?>/pages/leaderboard.php">Leaderboard</a>
                </div>
                <div class="footer-links-group">
                    <h4>Legal</h4>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Use</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Code Point · All Rights Reserved · Created by Kian Luppoy</p>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>

<style>
.footer {
    border-top: 1px solid var(--border-color);
    padding: 80px 40px 40px;
    background: var(--bg-primary);
}

.footer-container {
    max-width: 1400px;
    margin: 0 auto;
}

.footer-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 80px;
    margin-bottom: 60px;
}

.footer-brand .brand-text {
    font-weight: 800;
    font-size: 1.1rem;
    margin-bottom: 12px;
    display: inline-block;
}

.brand-desc {
    color: var(--text-muted);
    font-size: 0.9rem;
    max-width: 300px;
}

.logo-icon.small {
    width: 28px;
    height: 28px;
    font-size: 0.8rem;
    margin-bottom: 16px;
}

.footer-links-group h4 {
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--text-muted);
    margin-bottom: 24px;
}

.footer-links-group a {
    display: block;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.9rem;
    margin-bottom: 12px;
    transition: var(--transition);
}

.footer-links-group a:hover {
    color: #fff;
    transform: translateX(4px);
}

.footer-bottom {
    border-top: 1px solid var(--border-color);
    padding-top: 32px;
    text-align: center;
    color: var(--text-muted);
    font-size: 0.85rem;
}

@media (max-width: 768px) {
    .footer-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }
}
</style>
