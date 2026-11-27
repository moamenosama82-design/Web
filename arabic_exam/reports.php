<?php
// reports.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'api/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $mysqli->prepare("SELECT level, score, total_questions, taken_at FROM results WHERE user_id = ? ORDER BY taken_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Results - arabic exam</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>My Results</h1>
    <p>
        Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> |
        <a href="game.php">Play Game</a> |
        <a href="index.php">Home</a> |
        <a href="logout.php">Logout</a>
    </p>

    <?php if (empty($rows)): ?>
        <p>You have no results yet. Play a level first!</p>
    <?php else: ?>
        <table class="results-table">
            <tr>
                <th>Date</th>
                <th>Level</th>
                <th>Score</th>
                <th>Percentage</th>
            </tr>
            <?php foreach ($rows as $r): ?>
                <?php
                    $percent = 0;
                    if ($r['total_questions'] > 0) {
                        $percent = round(($r['score'] / $r['total_questions']) * 100);
                    }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['taken_at']); ?></td>
                    <td><?php echo (int)$r['level']; ?></td>
                    <td><?php echo (int)$r['score'] . " / " . (int)$r['total_questions']; ?></td>
                    <td><?php echo $percent . "%"; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
