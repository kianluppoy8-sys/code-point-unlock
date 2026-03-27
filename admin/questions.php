<?php
require_once '../includes/db.php';
require_once '../includes/compiler.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('pages/login.php');
}

$pageTitle = 'Manage Questions';
$error = '';
$success = '';

$compiler = new Compiler($db);
$categories = $db->fetchAll("SELECT * FROM categories ORDER BY name") ?: [];
$defaultLanguages = ['sql', 'java'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $level_id = (int)($_POST['level_id'] ?? 0);
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $code_snippet = $_POST['code_snippet'] ?? '';
        $expected_answer = $_POST['expected_answer'] ?? '';
        $expected_output = $_POST['expected_output'] ?? '';
        $type = $_POST['type'] ?? 'fix-code';
        $points = (int)($_POST['points'] ?? 100);
        $language = sanitize($_POST['language'] ?? 'java');
        
        if (empty($title) || empty($description) || empty($expected_answer) || $level_id <= 0) {
            $error = 'Please fill in all required fields';
        } else {
            $db->insert(
                "INSERT INTO questions (level_id, title, description, code_snippet, expected_answer, expected_output, type, points, language) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$level_id, $title, $description, $code_snippet, $expected_answer, $expected_output, $type, $points, $language]
            );
            $success = 'Question added successfully';
        }
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $level_id = (int)($_POST['level_id'] ?? 0);
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $code_snippet = $_POST['code_snippet'] ?? '';
        $expected_answer = $_POST['expected_answer'] ?? '';
        $expected_output = $_POST['expected_output'] ?? '';
        $type = $_POST['type'] ?? 'fix-code';
        $points = (int)($_POST['points'] ?? 100);
        $language = sanitize($_POST['language'] ?? 'java');
        
        if (empty($title) || empty($description) || empty($expected_answer) || $id <= 0 || $level_id <= 0) {
            $error = 'Please fill in all required fields';
        } else {
            $db->execute(
                "UPDATE questions SET level_id = ?, title = ?, description = ?, code_snippet = ?, expected_answer = ?, expected_output = ?, type = ?, points = ?, language = ? WHERE id = ?",
                [$level_id, $title, $description, $code_snippet, $expected_answer, $expected_output, $type, $points, $language, $id]
            );
            $success = 'Question updated successfully';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $db->execute("DELETE FROM questions WHERE id = ?", [$id]);
            $success = 'Question deleted successfully';
        }
    }
}

$levels = $db->fetchAll("SELECT * FROM levels ORDER BY display_order, id");
$selectedLevel = isset($_GET['level']) ? (int)$_GET['level'] : 0;

if ($selectedLevel > 0) {
    $questions = $db->fetchAll(
        "SELECT q.*, l.name as level_name FROM questions q JOIN levels l ON q.level_id = l.id WHERE q.level_id = ? ORDER BY q.id",
        [$selectedLevel]
    );
} else {
    $questions = $db->fetchAll(
        "SELECT q.*, l.name as level_name FROM questions q JOIN levels l ON q.level_id = l.id ORDER BY l.display_order, q.id"
    );
}

$editQuestion = null;
if (isset($_GET['edit'])) {
    $editQuestion = $db->fetch("SELECT * FROM questions WHERE id = ?", [(int)$_GET['edit']]);
}

include '../includes/header.php';
?>

<div class="admin-page">
    <div class="admin-header">
        <a href="index.php" class="back-link">← Back to Dashboard</a>
        <h1>Manage Questions</h1>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="admin-grid-full">
        <div class="admin-section">
            <div class="section-header">
                <h2><?php echo $editQuestion ? 'Edit Question' : 'Add New Question'; ?></h2>
            </div>
            <form method="POST" class="admin-form">
                <input type="hidden" name="action" value="<?php echo $editQuestion ? 'edit' : 'add'; ?>">
                <?php if ($editQuestion): ?>
                    <input type="hidden" name="id" value="<?php echo $editQuestion['id']; ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Level *</label>
                        <select name="level_id" required>
                            <option value="">Select Level</option>
                            <?php foreach ($levels as $level): ?>
                                <option value="<?php echo $level['id']; ?>" <?php echo ($editQuestion && $editQuestion['level_id'] == $level['id']) ? 'selected' : ''; ?>>
                                    <?php echo sanitize($level['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Question Type *</label>
                        <select name="type" required>
                            <option value="fix-code" <?php echo ($editQuestion && $editQuestion['type'] == 'fix-code') ? 'selected' : ''; ?>>Fix the Code</option>
                            <option value="write-code" <?php echo ($editQuestion && $editQuestion['type'] == 'write-code') ? 'selected' : ''; ?>>Write Code</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Programming Language *</label>
                        <select name="language" required>
                            <?php foreach ($defaultLanguages as $lang): ?>
                                <option value="<?php echo $lang; ?>" <?php echo ($editQuestion && $editQuestion['language'] == $lang) ? 'selected' : ''; ?>>
                                    <?php echo strtoupper($lang); ?>
                                </option>
                            <?php endforeach; ?>
                            <?php foreach ($categories as $cat): ?>
                                <?php if (!in_array(strtolower($cat['name']), $defaultLanguages)): ?>
                                    <option value="<?php echo strtolower($cat['name']); ?>" <?php echo ($editQuestion && $editQuestion['language'] == strtolower($cat['name'])) ? 'selected' : ''; ?>>
                                        <?php echo strtoupper($cat['name']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Points *</label>
                        <input type="number" name="points" value="<?php echo $editQuestion ? $editQuestion['points'] : 100; ?>" min="1" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Question Title *</label>
                    <input type="text" name="title" value="<?php echo $editQuestion ? sanitize($editQuestion['title']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" rows="3" required><?php echo $editQuestion ? sanitize($editQuestion['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Code Snippet (for fix-code type)</label>
                    <textarea name="code_snippet" rows="8" class="code-textarea-sm"><?php echo $editQuestion ? htmlspecialchars($editQuestion['code_snippet']) : ''; ?></textarea>
                    <small class="text-muted">The broken code that players need to fix. Leave empty for write-code type.</small>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Expected Solution (Correct Code)</label>
                        <textarea name="expected_answer" id="expected_answer" rows="6" class="code-textarea-sm" required><?php echo $editQuestion ? htmlspecialchars($editQuestion['expected_answer']) : ''; ?></textarea>
                        <small class="text-muted">The correct code for reference.</small>
                        <div style="margin-top: 8px;">
                            <button type="button" id="predict-output" class="btn btn-sm btn-outline">
                                ⚡ Run & Predict Output
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Expected Output (Correct Output) *</label>
                        <textarea name="expected_output" id="expected_output" rows="6" class="code-textarea-sm" required><?php echo $editQuestion ? htmlspecialchars($editQuestion['expected_output']) : ''; ?></textarea>
                        <small class="text-muted">For Java: the text output. For SQL: JSON array of results.</small>
                    </div>
                </div>

                <div id="prediction-result" style="display:none; margin-top: 10px; padding: 10px; border-radius: 4px;"></div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?php echo $editQuestion ? 'Update Question' : 'Add Question'; ?></button>
                    <?php if ($editQuestion): ?>
                        <a href="questions.php<?php echo $selectedLevel ? '?level='.$selectedLevel : ''; ?>" class="btn btn-outline">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="admin-section">
            <div class="section-header">
                <h2>Questions (<?php echo count($questions); ?>)</h2>
                <div class="filter-group">
                    <select onchange="window.location.href='?level='+this.value">
                        <option value="0">All Levels</option>
                        <?php foreach ($levels as $level): ?>
                            <option value="<?php echo $level['id']; ?>" <?php echo $selectedLevel == $level['id'] ? 'selected' : ''; ?>>
                                <?php echo sanitize($level['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <?php if (empty($questions)): ?>
                <p class="text-muted">No questions found.</p>
            <?php else: ?>
                <div class="questions-list">
                    <?php foreach ($questions as $question): ?>
                        <div class="question-item">
                            <div class="question-info">
                                <strong><?php echo sanitize($question['title']); ?></strong>
                                <span class="tag"><?php echo strtoupper($question['language']); ?></span>
                                <span class="tag"><?php echo $question['type']; ?></span>
                                <span class="tag points"><?php echo $question['points']; ?> pts</span>
                                <div class="question-meta">
                                    Level: <?php echo sanitize($question['level_name']); ?>
                                </div>
                            </div>
                            <div class="question-actions">
                                <a href="?edit=<?php echo $question['id']; ?><?php echo $selectedLevel ? '&level='.$selectedLevel : ''; ?>" class="btn btn-sm btn-outline">Edit</a>
                                <form method="POST" class="inline-form" onsubmit="return confirm('Delete this question?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $question['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
document.getElementById('predict-output').addEventListener('click', function() {
    const code = document.getElementById('expected_answer').value;
    const lang = document.querySelector('select[name="language"]').value;
    const resultDiv = document.getElementById('prediction-result');
    const outputField = document.getElementById('expected_output');
    
    if (!code.trim()) {
        alert('Please enter some code in the Expected Solution box first.');
        return;
    }

    resultDiv.style.display = 'block';
    resultDiv.style.background = '#f0f0f0';
    resultDiv.style.color = '#333';
    resultDiv.innerHTML = 'Executing code... please wait...';
    
    const formData = new FormData();
    formData.append('code', code);
    formData.append('lang', lang);

    fetch('predict_output.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            outputField.value = data.output;
            resultDiv.style.background = '#dcfce7';
            resultDiv.style.color = '#166534';
            resultDiv.innerHTML = '✅ Success! Output predicted and filled below.';
        } else {
            resultDiv.style.background = '#fee2e2';
            resultDiv.style.color = '#991b1b';
            resultDiv.innerHTML = '❌ Error: ' + data.error;
            if (data.output) {
                resultDiv.innerHTML += '<br><pre style="margin-top:5px; font-size:12px;">' + data.output + '</pre>';
            }
        }
    })
    .catch(error => {
        resultDiv.style.background = '#fee2e2';
        resultDiv.style.color = '#991b1b';
        resultDiv.innerHTML = '❌ Connection Error: ' + error;
    });
});
</script>
