<?php
require_once '../includes/db.php';
require_once '../includes/compiler.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$code = $_POST['code'] ?? '';
$lang = strtolower($_POST['lang'] ?? '');

if (empty($code)) {
    echo json_encode(['success' => false, 'error' => 'No code provided']);
    exit;
}

$compiler = new Compiler($db);
$result = [
    'success' => false,
    'output' => '',
    'error' => ''
];

if ($lang === 'java') {
    $result = $compiler->compileAndRunJava($code);
} elseif ($lang === 'sql' || $lang === 'mysql') {
    $result = $compiler->runMySQL($code);
} else {
    $result['error'] = 'Unsupported language for prediction';
}

echo json_encode($result);
