<?php
require_once '../includes/db.php';
$pageTitle = 'Login';
$error = '';

if (isLoggedIn()) {
    redirect('pages/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $user = $db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['total_points'] = $user['total_points'];
            
            redirect('pages/dashboard.php');
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

require_once '../includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-decor"></div>
        <h1 class="auth-title">Welcome Back</h1>
        <p class="auth-subtitle">Continue your coding journey at Code Point.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <span class="alert-icon">⚠️</span>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="name@example.com"
                       value="<?php echo isset($_POST['email']) ? sanitize($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <div style="display: flex; justify-content: space-between;">
                    <label for="password">Password</label>
                    <a href="#" style="font-size: 0.8rem; color: var(--primary); text-decoration: none;">Forgot?</a>
                </div>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 10px;">
                Sign In
            </button>
        </form>
        
        <div class="auth-footer">
            Don't have an account? <a href="<?php echo SITE_URL; ?>/pages/register.php">Create Account</a>
        </div>
    </div>
</div>

<style>
.auth-decor {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: 0 10px 30px var(--primary-glow);
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.auth-decor::after {
    content: '</>';
    color: white;
    font-family: 'JetBrains Mono', monospace;
    font-weight: 800;
}
</style>

<?php require_once '../includes/footer.php'; ?>
