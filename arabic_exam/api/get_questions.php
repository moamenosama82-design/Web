<?php
// api/get_questions.php
header('Content-Type: application/json; charset=utf-8');

require 'db.php';

// Which level? default = 1
$level = isset($_GET['level']) ? (int)$_GET['level'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
// Use a prepared statement (safer + avoids weird SQL errors)
$stmt = $mysqli->prepare(
    "SELECT id, question_text, correct_answer, option2, option3, option4
     FROM questions
     WHERE level = ?
     limit ?"
);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Prepare failed: " . $mysqli->error], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param("ii", $level, $limit);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];

while ($row = $result->fetch_assoc()) {
    $options = [
        $row['correct_answer'],
        $row['option2'],
        $row['option3'],
        $row['option4']
    ];

    // remove null / empty
    $options = array_filter($options, function($opt) {
        return $opt !== null && $opt !== '';
    });

    shuffle($options);

    $questions[] = [
        "id"            => $row["id"],
        "question_text" => $row["question_text"],
        "options"       => array_values($options),
        "correct"       => $row["correct_answer"]
    ];
}

$stmt->close();

// Return JUST the array, no wrapper, to keep it simple
echo json_encode($questions, JSON_UNESCAPED_UNICODE);
?>