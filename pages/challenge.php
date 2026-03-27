<?php
require_once '../includes/db.php';
require_once '../includes/compiler.php';
$pageTitle = 'Challenge';

if (!isLoggedIn()) {
    redirect('pages/login.php');
}

$questionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId = $_SESSION['user_id'];

$question = $db->fetch(
    "SELECT q.*, l.required_points, l.name as level_name 
     FROM questions q 
     JOIN levels l ON q.level_id = l.id 
     WHERE q.id = ?", 
    [$questionId]
);

if (!$question) {
    redirect('dashboard.php');
}

if ($_SESSION['total_points'] < $question['required_points']) {
    redirect('dashboard.php');
}

$pageTitle = $question['title'];
$bodyClass = 'challenge-body';

$alreadyCompleted = $db->fetch(
    "SELECT id FROM user_progress WHERE user_id = ? AND question_id = ?",
    [$userId, $questionId]
);

$result = null;
$compiler = new Compiler($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedCode = $_POST['code'] ?? '';
    $lang = strtolower($question['language']);
    
    $executionResult = [
        'success' => false,
        'output' => '',
        'error' => ''
    ];

    if ($lang === 'php') {
        $executionResult = $compiler->runPHP($submittedCode);
    } elseif ($lang === 'java') {
        $executionResult = $compiler->compileAndRunJava($submittedCode);
    } elseif ($lang === 'sql' || $lang === 'mysql') {
        $executionResult = $compiler->runMySQL($submittedCode);
    } else {
        $executionResult = [
            'success' => trim($submittedCode) === trim($question['expected_answer']),
            'output' => $submittedCode,
            'error' => ''
        ];
    }
    
    $isCorrect = false;
    if ($executionResult['success']) {
        $actualOutput = trim($executionResult['output']);
        $expectedOutput = trim($question['expected_output'] ?: $question['expected_answer']);
        
        if ($lang === 'sql' || $lang === 'mysql') {
            $actualData = json_decode($actualOutput, true);
            $expectedData = json_decode($expectedOutput, true);
            
            if ($actualData !== null && $expectedData !== null) {
                $expectedKeys = !empty($expectedData) ? array_keys($expectedData[0]) : [];
                $normalize = function($data) use (&$normalize) {
                    if (!is_array($data)) return (string)$data;
                    ksort($data);
                    foreach ($data as $k => $v) {
                        $data[$k] = $normalize($v);
                    }
                    return $data;
                };
                
                $normalizedActual = array_map(function($row) use ($normalize, $expectedKeys) {
                    if (!empty($expectedKeys)) {
                        $row = array_intersect_key($row, array_flip($expectedKeys));
                    }
                    return $normalize($row);
                }, $actualData);
                
                $normalizedExpected = array_map($normalize, $expectedData);
                $isCorrect = ($normalizedActual === $normalizedExpected);
            } else {
                $isCorrect = ($actualOutput === $expectedOutput);
            }
        } else {
            if (strtolower($question['language']) === 'html') {
                $isCorrect = (trim(strtolower($submittedCode)) === trim(strtolower($question['expected_answer'])));
            } else {
                $isCorrect = (strtolower($actualOutput) === strtolower($expectedOutput));
            }
        }
    }
    
    $db->insert(
        "INSERT INTO submissions (user_id, question_id, code, is_correct) VALUES (?, ?, ?, ?)",
        [$userId, $questionId, $submittedCode, $isCorrect ? 1 : 0]
    );
    
    if ($isCorrect && !$alreadyCompleted) {
        $db->insert(
            "INSERT INTO user_progress (user_id, question_id) VALUES (?, ?)",
            [$userId, $questionId]
        );
        
        $newPoints = $_SESSION['total_points'] + $question['points'];
        $db->execute(
            "UPDATE users SET total_points = ? WHERE id = ?",
            [$newPoints, $userId]
        );
        $_SESSION['total_points'] = $newPoints;
        
        $result = [
            'correct' => true,
            'message' => 'Mission Successful! +' . $question['points'] . ' pts',
            'output' => $executionResult['output'],
            'error' => $executionResult['error']
        ];
    } elseif ($isCorrect) {
        $result = [
            'correct' => true,
            'message' => 'Correct! (Already solved)',
            'output' => $executionResult['output'],
            'error' => $executionResult['error']
        ];
    } else {
        $result = [
            'correct' => false,
            'message' => $executionResult['error'] ? 'Execution failed. Check your logic.' : 'Output mismatch. Try again!',
            'output' => $executionResult['output'],
            'error' => $executionResult['error']
        ];
    }
}

$initialCode = $question['code_snippet'] ?: '';

function getLangIcon($lang) {
    $icons = [
        'php' => '🐘',
        'sql' => '🗄️',
        'mysql' => '🗄️',
        'java' => '☕',
        'html' => '🌐',
        'javascript' => '📜'
    ];
    return $icons[strtolower($lang)] ?? '📝';
}

require_once '../includes/header.php';
?>

<div class="challenge-layout">
    <!-- Sidebar -->
    <aside class="challenge-sidebar">
        <div class="sidebar-header">
            <a href="level.php?id=<?php echo $question['level_id']; ?>" class="back-link">← <?php echo sanitize($question['level_name']); ?></a>
            <h2 class="q-sidebar-title"><?php echo sanitize($question['title']); ?></h2>
            <div class="q-sidebar-meta">
                <span class="m-badge"><?php echo strtoupper($question['language'] ?? 'TEXT'); ?></span>
                <span class="m-badge points"><?php echo $question['points']; ?> PTS</span>
            </div>
        </div>
        
        <div class="sidebar-content">
            <div class="instruction-group">
                <h3>Objective</h3>
                <p class="instruction-text"><?php echo nl2br(sanitize($question['description'])); ?></p>
            </div>
            
            <?php if (!empty($question['expected_output']) && strtolower($question['language']) !== 'html'): ?>
                <div class="instruction-group">
                    <h3>Target Output</h3>
                    <div class="target-box">
                        <pre><code><?php echo sanitize($question['expected_output']); ?></code></pre>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="sidebar-footer">
            <button type="submit" form="code-form" class="btn btn-primary btn-block btn-run">
                <span class="run-icon">▶</span> Run Protocol
            </button>
        </div>
    </aside>

    <!-- Main Workspace -->
    <main class="editor-section">
        <div class="monaco-wrapper">
            <div class="editor-tabs">
                <div class="tab active">
                    <span class="tab-icon"><?php echo getLangIcon($question['language']); ?></span>
                    main.<?php echo strtolower($question['language'] == 'javascript' ? 'js' : $question['language']); ?>
                </div>
            </div>
            
            <form id="code-form" method="POST" style="height: calc(100% - 40px);">
                <textarea name="code" id="code-editor" class="code-textarea" spellcheck="false"><?php 
                    echo isset($_POST['code']) ? htmlspecialchars($_POST['code']) : htmlspecialchars($initialCode); 
                ?></textarea>
            </form>
        </div>

        <!-- Terminal Output -->
        <div class="terminal-section">
            <div class="terminal-header">
                <span class="terminal-title">System Console</span>
                <?php if ($result): ?>
                    <span class="terminal-status <?php echo $result['correct'] ? 'success' : 'error'; ?>">
                        <?php echo $result['correct'] ? 'STATUS: OK' : 'STATUS: FAILED'; ?>
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="terminal-body" id="terminal-content">
                <?php if ($result): ?>
                    <div class="terminal-msg <?php echo $result['correct'] ? 'success' : 'error'; ?>">
                        <?php echo $result['message']; ?>
                    </div>
                    
                    <?php if (!empty($result['error'])): ?>
                        <div class="terminal-err">
                            <span class="err-label">ERROR_LOG:</span>
                            <pre><?php echo sanitize($result['error']); ?></pre>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($result['output'])): ?>
                        <div class="terminal-out">
                            <span class="out-label">STDOUT:</span>
                            <pre><?php echo sanitize($result['output']); ?></pre>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="terminal-placeholder">
                        <span class="cursor-blink">_</span>
                        Awaiting code execution...
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<style>
/* Integrated IDE Styles */
.q-sidebar-title {
    font-size: 1.4rem;
    font-weight: 800;
    margin: 12px 0 16px;
    background: linear-gradient(135deg, #fff 0%, #a5b4fc 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.m-badge {
    font-size: 0.65rem;
    font-weight: 800;
    padding: 2px 8px;
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 4px;
    color: var(--text-muted);
}

.m-badge.points {
    color: var(--accent);
    border-color: rgba(6, 182, 212, 0.2);
}

.instruction-group {
    margin-bottom: 32px;
}

.instruction-group h3 {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--text-muted);
    margin-bottom: 12px;
}

.target-box {
    background: rgba(0,0,0,0.3);
    border: 1px dashed var(--border-color);
    border-radius: 12px;
    padding: 16px;
}

.target-box pre {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.85rem;
    color: var(--accent);
}

.sidebar-footer {
    padding: 24px;
    border-top: 1px solid var(--border-color);
}

.btn-run {
    font-family: 'Outfit', sans-serif;
    letter-spacing: 0.5px;
    height: 50px;
}

.run-icon {
    font-size: 0.7rem;
    margin-right: 4px;
}

/* Editor Area */
.editor-tabs {
    height: 40px;
    background: #08080c;
    display: flex;
    border-bottom: 1px solid var(--border-color);
}

.tab {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0 24px;
    background: #030305;
    border-right: 1px solid var(--border-color);
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.8rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.tab.active {
    background: #0d0d12;
    color: #fff;
    border-bottom: 2px solid var(--primary);
}

.code-textarea {
    width: 100%;
    height: 100%;
    background: #050508;
    border: none;
    color: #cad3f5;
    font-family: 'JetBrains Mono', monospace;
    font-size: 1rem;
    line-height: 1.6;
    padding: 32px;
    resize: none;
    outline: none;
}

/* Terminal Area */
.terminal-status {
    font-size: 0.7rem;
    font-weight: 800;
    padding: 4px 12px;
    border-radius: 4px;
}

.terminal-status.success { color: var(--success); background: rgba(16, 185, 129, 0.1); }
.terminal-status.error { color: var(--error); background: rgba(239, 68, 68, 0.1); }

.terminal-msg {
    margin-bottom: 16px;
    font-weight: 700;
}

.terminal-msg.success { color: var(--success); }
.terminal-msg.error { color: var(--error); }

.terminal-err pre, .terminal-out pre {
    background: rgba(255, 255, 255, 0.02);
    padding: 12px;
    border-radius: 8px;
    margin-top: 8px;
    font-size: 0.85rem;
    white-space: pre-wrap;
}

.err-label, .out-label {
    font-size: 0.7rem;
    font-weight: 800;
    color: var(--text-muted);
}

.cursor-blink {
    animation: blink 1s step-end infinite;
}

@keyframes blink {
    50% { opacity: 0; }
}
</style>

<script>
// Tab mechanism for textarea
document.getElementById('code-editor').addEventListener('keydown', function(e) {
    if (e.key === 'Tab') {
        e.preventDefault();
        var start = this.selectionStart;
        var end = this.selectionEnd;
        this.value = this.value.substring(0, start) + '    ' + this.value.substring(end);
        this.selectionStart = this.selectionEnd = start + 4;
    }
});

// Auto-focus the editor
window.onload = function() {
    document.getElementById('code-editor').focus();
    
    // Smooth scroll terminal if there's a result
    <?php if ($result): ?>
        const terminal = document.querySelector('.terminal-section');
        terminal.scrollIntoView({ behavior: 'smooth' });
    <?php endif; ?>
};
</script>

<?php require_once '../includes/footer.php'; ?>
