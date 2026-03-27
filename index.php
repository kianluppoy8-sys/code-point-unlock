<?php
require_once 'includes/config.php';
$pageTitle = 'The Future of Coding';

if (isLoggedIn()) {
    redirect('pages/dashboard.php');
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="landing-page-v2">
    <!-- Animated Background -->
    <div class="bg-glow"></div>
    
    <div class="hero-section">
        <div class="hero-container">
            <div class="hero-text-content">
                <div class="badge-wrapper">
                    <span class="premium-badge">ICT CLUB OF ACLC COLLEGE OF TAYTAY</span>
                </div>
                <h1 class="hero-main-title">
                    Master the <span class="gradient-text">Art of Code</span>
                    <br>Unlock Your Potential
                </h1>
                <p class="hero-subline">
                    The ultimate coding challenge platform created by <strong>Kian Luppoy</strong>. 
                    Solve complex puzzles, climb the leaderboard, and become a professional developer 
                    through hands-on practice.
                </p>
                <div class="cta-group">
                    <a href="<?php echo SITE_URL; ?>/pages/register.php" class="btn btn-primary btn-glow btn-xl">
                        Get Started
                        <span class="btn-icon">→</span>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/login.php" class="btn btn-glass btn-xl">
                        Member Login
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-num">10+</span>
                        <span class="stat-txt">Curated Levels</span>
                    </div>
                    <div class="stat-separator"></div>
                    <div class="stat-item">
                        <span class="stat-num">50+</span>
                        <span class="stat-txt">Challenges</span>
                    </div>
                    <div class="stat-separator"></div>
                    <div class="stat-item">
                        <span class="stat-num">PHP7+</span>
                        <span class="stat-txt">MySQL Driven</span>
                    </div>
                </div>
            </div>
            
            <div class="hero-ide-preview">
                <div class="floating-code-card card-1">
                    <div class="card-header">
                        <div class="dots"><span class="red"></span><span class="yellow"></span><span class="green"></span></div>
                        <span class="tab">index.php</span>
                    </div>
                    <div class="card-body">
                        <pre><code><span class="kw">while</span>($player->isCoding()) {
    $player-><span class="fn">learn</span>();
    $player-><span class="fn">levelUp</span>();
}</code></pre>
                    </div>
                </div>
                <div class="floating-code-card card-2">
                    <div class="card-header">
                        <div class="dots"><span class="red"></span><span class="yellow"></span><span class="green"></span></div>
                        <span class="tab">query.sql</span>
                    </div>
                    <div class="card-body">
                        <pre><code><span class="kw">SELECT</span> * <span class="kw">FROM</span> users 
<span class="kw">WHERE</span> status = <span class="str">'legend'</span>;</code></pre>
                    </div>
                </div>
                <div class="glow-sphere"></div>
            </div>
        </div>
    </div>

    <div class="features-section">
        <div class="section-header">
            <h2 class="section-title">Why Join <span class="gradient-text">Code Point</span>?</h2>
            <p class="section-desc">Experience the most interactive way to learn web development.</p>
        </div>
        
        <div class="feature-grid-v2">
            <div class="premium-feature-card">
                <div class="icon-box">&#128187;</div>
                <h3>Real-World Challenges</h3>
                <p>No simple multiple choice. Write actual PHP, SQL, and JS code directly in your browser.</p>
            </div>
            <div class="premium-feature-card">
                <div class="icon-box">&#128275;</div>
                <h3>Unlockable Progression</h3>
                <p>Earn experience points for every solution and unlock more difficult tech stacks.</p>
            </div>
            <div class="premium-feature-card">
                <div class="icon-box">&#127942;</div>
                <h3>Global Leaderboard</h3>
                <p>Compete with other students from ACLC College of Taytay and claim the top spot.</p>
            </div>
        </div>
    </div>

    <footer class="premium-footer">
        <div class="footer-content">
            <p>© 2026 Code Point Unlock. Created with &#10084; by Kian Luppoy.</p>
            <div class="footer-links">
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
                <a href="#">Support</a>
            </div>
        </div>
    </footer>
</div>

<style>
/* Landing Page V2 Premium Styles */
.landing-page-v2 {
    position: relative;
    overflow: hidden;
    background-color: var(--bg-primary);
}

.bg-glow {
    position: absolute;
    top: -10%;
    right: -10%;
    width: 60%;
    height: 60%;
    background: radial-gradient(circle, rgba(61, 89, 161, 0.1) 0%, transparent 70%);
    z-index: 0;
    pointer-events: none;
}

.hero-section {
    padding: 120px 24px 80px;
    max-width: 1400px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.hero-container {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: 80px;
    align-items: center;
}

.badge-wrapper {
    margin-bottom: 24px;
}

.premium-badge {
    background: rgba(61, 89, 161, 0.1);
    color: var(--primary);
    padding: 8px 16px;
    border-radius: 100px;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    border: 1px solid rgba(61, 89, 161, 0.2);
}

.hero-main-title {
    font-family: 'Outfit', sans-serif;
    font-size: 4.5rem;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 32px;
    color: #fff;
}

.hero-subline {
    font-size: 1.25rem;
    color: var(--text-primary);
    line-height: 1.6;
    margin-bottom: 48px;
    max-width: 600px;
    opacity: 0.8;
}

.cta-group {
    display: flex;
    gap: 20px;
    margin-bottom: 64px;
}

.btn-xl {
    padding: 18px 40px;
    font-size: 1.1rem;
    border-radius: 12px;
}

.btn-glow {
    box-shadow: 0 0 30px rgba(61, 89, 161, 0.3);
}

.btn-glow:hover {
    box-shadow: 0 0 50px rgba(61, 89, 161, 0.5);
    transform: translateY(-2px);
}

.btn-glass {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #fff;
    backdrop-filter: blur(10px);
}

.btn-glass:hover {
    background: rgba(255, 255, 255, 0.08);
}

.hero-stats {
    display: flex;
    align-items: center;
    gap: 40px;
}

.stat-item {
    display: flex;
    flex-direction: column;
}

.stat-num {
    font-size: 2rem;
    font-weight: 800;
    color: #fff;
}

.stat-txt {
    font-size: 0.85rem;
    color: var(--text-muted);
}

.stat-separator {
    width: 1px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
}

/* IDE Preview */
.hero-ide-preview {
    position: relative;
    height: 500px;
}

.floating-code-card {
    position: absolute;
    background: var(--bg-card);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    width: 380px;
    overflow: hidden;
    animation: float 6s ease-in-out infinite;
}

.card-1 {
    top: 50px;
    left: 0;
    z-index: 10;
}

.card-2 {
    bottom: 50px;
    right: 0;
    z-index: 5;
    animation-delay: -3s;
}

.card-header {
    background: rgba(0, 0, 0, 0.2);
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.dots {
    display: flex;
    gap: 6px;
}

.dots span {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.red { background: #ff5f56; }
.yellow { background: #ffbd2e; }
.green { background: #27c93f; }

.tab {
    font-size: 0.75rem;
    color: var(--text-muted);
    font-family: 'JetBrains Mono', monospace;
}

.card-body {
    padding: 24px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.9rem;
    line-height: 1.6;
}

.kw { color: #c084fc; }
.fn { color: #3b82f6; }
.str { color: #86efac; }

.glow-sphere {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.05) 0%, transparent 70%);
    z-index: 0;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

/* Features Section */
.features-section {
    padding: 100px 24px;
    max-width: 1280px;
    margin: 0 auto;
}

.section-header {
    text-align: center;
    margin-bottom: 80px;
}

.section-title {
    font-family: 'Outfit', sans-serif;
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 20px;
}

.section-desc {
    font-size: 1.1rem;
    color: var(--text-secondary);
}

.feature-grid-v2 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
}

.premium-feature-card {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 24px;
    padding: 48px;
    transition: all 0.3s;
    text-align: center;
}

.premium-feature-card:hover {
    background: rgba(255, 255, 255, 0.04);
    border-color: var(--primary);
    transform: translateY(-8px);
}

.icon-box {
    font-size: 3rem;
    margin-bottom: 24px;
}

/* Footer */
.premium-footer {
    padding: 60px 24px;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    text-align: center;
}

.footer-content {
    max-width: 1280px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.footer-links {
    display: flex;
    gap: 32px;
}

.footer-links a {
    color: var(--text-muted);
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.2s;
}

/* Responsive */
@media (max-width: 1024px) {
    .hero-container {
        grid-template-columns: 1fr;
        text-align: center;
    }
    .hero-text-content {
        margin: 0 auto;
    }
    .hero-ide-preview {
        display: none;
    }
    .cta-group {
        justify-content: center;
    }
    .hero-stats {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .feature-grid-v2 {
        grid-template-columns: 1fr;
    }
    .hero-main-title {
        font-size: 3rem;
    }
    .footer-content {
        flex-direction: column;
        gap: 24px;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
