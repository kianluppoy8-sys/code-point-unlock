<?php
require_once '../includes/db.php';
$pageTitle = 'Create Account';
$error = '';

if (isLoggedIn()) {
    redirect('pages/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Check if exists
        $exists = $db->fetch("SELECT id FROM users WHERE email = ? OR username = ?", [$email, $username]);
        if ($exists) {
            $error = 'Email or username already registered.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $userId = $db->insert(
                "INSERT INTO users (username, email, password) VALUES (?, ?, ?)",
                [$username, $email, $hashed]
            );
            
            if ($userId) {
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['is_admin'] = 0;
                $_SESSION['total_points'] = 0;
                
                redirect('pages/dashboard.php');
            } else {
                $error = 'Registration failed. Try again.';
            }
        }
    }
}

require_once '../includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-decor"></div>
        <h1 class="auth-title">Get Started</h1>
        <p class="auth-subtitle">Join the ICT Club community and start coding.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <span class="alert-icon">⚠️</span>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="kian_dev"
                       value="<?php echo isset($_POST['username']) ? sanitize($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="name@example.com"
                       value="<?php echo isset($_POST['email']) ? sanitize($_POST['email']) : ''; ?>">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 10px;">
                Create Account
            </button>
        </form>
        
        <div class="auth-footer">
            Already a member? <a href="<?php echo SITE_URL; ?>/pages/login.php">Login here</a>
        </div>
    </div>
</div>

<style>
.auth-decor {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--accent), var(--primary));
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: 0 10px 30px rgba(6, 182, 212, 0.2);
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.auth-decor::after {
    content: '{ }';
    color: white;
    font-family: 'JetBrains Mono', monospace;
    font-weight: 800;
}
</style>

<?php require_once '../includes/footer.php'; ?>
