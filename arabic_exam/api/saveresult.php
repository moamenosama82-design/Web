<?php
// api/save_result.php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

require 'db.php';

$user_id = $_SESSION['user_id'];
$level   = isset($_POST['level']) ? (int)$_POST['level'] : 1;
$score   = isset($_POST['score']) ? (int)$_POST['score'] : 0;
$total   = isset($_POST['total']) ? (int)$_POST['total'] : 0;

if ($total <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid total questions']);
    exit;
}

$stmt = $mysqli->prepare("INSERT INTO results (user_id, level, score, total_questions) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiii", $user_id, $level, $score, $total);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'message' => 'Result saved']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'DB insert failed']);
}

$stmt->close();
?>
