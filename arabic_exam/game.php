<?php
// game.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$level = isset($_GET['level']) ? (int)$_GET['level'] : 1;
$level_names = [1 => 'Beginner', 2 => 'Intermediate', 3 => 'Advanced'];
$level_name = $level_names[$level] ?? 'Beginner';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arabic Competence Exam - Level <?php echo $level; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/game.js" defer></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #0f172a, #1e293b, #020617);
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .game-wrapper {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .game-card {
            background: rgba(15, 23, 42, 0.96);
            border-radius: 18px;
            padding: 20px 24px 22px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(148, 163, 184, 0.25);
        }
        
        .game-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.25);
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        
        .game-title-block {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .game-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 20%, #facc15, #f97316);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .game-title-text h1 {
            margin: 0;
            font-size: 20px;
            color: #f9fafb;
        }
        
        .game-title-text span {
            display: block;
            font-size: 12px;
            color: #9ca3af;
        }
        
        .game-user-info {
            text-align: right;
            font-size: 13px;
            color: #cbd5f5;
        }
        
        .level-selector {
            background: rgba(0,0,0,0.3);
            padding: 5px 10px;
            border-radius: 20px;
            margin-top: 5px;
        }
        
        .level-selector a {
            color: #facc15;
            text-decoration: none;
            margin: 0 5px;
        }
        
        .level-selector a:hover {
            text-decoration: underline;
        }
        
        .game-main {
            display: grid;
            grid-template-columns: minmax(0, 3fr) minmax(0, 2fr);
            gap: 18px;
        }
        
        @media (max-width: 768px) {
            .game-main {
                grid-template-columns: 1fr;
            }
        }
        
        .question-area {
            background: rgba(15, 23, 42, 0.9);
            border-radius: 14px;
            padding: 14px 16px;
            border: 1px solid rgba(51, 65, 85, 0.9);
        }
        
        #question-text {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 20px;
            padding: 10px;
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
        }
        
        .option {
            display: block;
            margin: 10px 0;
            padding: 10px 12px;
            border-radius: 10px;
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(75, 85, 99, 0.8);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .option:hover {
            background: rgba(30, 64, 175, 0.5);
            border-color: #facc15;
        }
        
        .option input {
            margin-right: 10px;
            transform: scale(1.1);
        }
        
        .game-controls {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .game-btn {
            padding: 8px 18px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.2s ease;
        }
        
        .game-btn.primary {
            background: #1d4ed8;
            color: white;
        }
        
        .game-btn.secondary {
            background: #4b5563;
            color: white;
        }
        
        .game-btn.warning {
            background: #dc2626;
            color: white;
        }
        
        .game-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }
        
        .game-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .side-panel {
            background: radial-gradient(circle at top, rgba(30, 64, 175, 0.6), rgba(15, 23, 42, 0.95));
            border-radius: 14px;
            padding: 14px 16px;
            border: 1px solid rgba(30, 64, 175, 0.7);
        }
        
        .side-panel h3 {
            margin-bottom: 10px;
            color: #facc15;
        }
        
        .progress-bar-bg {
            width: 100%;
            height: 10px;
            border-radius: 10px;
            background: rgba(15, 23, 42, 0.9);
            overflow: hidden;
            margin-bottom: 8px;
        }
        
        .progress-bar-fill {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #22c55e, #eab308);
            transition: width 0.3s ease;
        }
        
        .result-box {
            margin-top: 15px;
            padding: 10px;
            background: rgba(0,0,0,0.3);
            border-radius: 10px;
            text-align: center;
        }
        
        .stars {
            font-size: 24px;
            letter-spacing: 5px;
        }
        
        .feedback {
            margin-top: 10px;
            font-size: 14px;
        }
        
        .saving {
            animation: pulse 1s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        
        ul {
            padding-left: 20px;
            margin-top: 10px;
        }
        
        ul li {
            margin-bottom: 8px;
            font-size: 13px;
        }
    </style>
</head>
<body>
<div class="game-wrapper">
    <div class="game-card">
        <div class="game-header">
            <div class="game-title-block">
                <div class="game-icon"></div>
                <div class="game-title-text">
                    <h1>Arabic Competence Exam</h1>
                    <span>Level <?php echo $level; ?> - <?php echo $level_name; ?></span>
                </div>
            </div>
            <div class="game-user-info">
                <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong><br>
                <div class="level-selector">
                    Change Level:
                    <a href="?level=1">1 Beginner</a> |
                    <a href="?level=2">2 Intermediate</a> |
                    <a href="?level=3">3 Advanced</a>
                </div>
                <a href="index.php" style="color:#93c5fd;">Home</a> |
                <a href="reports.php" style="color:#93c5fd;">My Results</a> |
                <a href="logout.php" style="color:#93c5fd;">Logout</a>
            </div>
        </div>

        <div class="game-main">
            <div class="question-area">
                <div id="question-container">
                    <div style="text-align:center; padding:40px;">Loading questions...</div>
                </div>
                <div class="game-controls">
                    <button id="prevBtn" class="game-btn secondary" disabled>Previous</button>
                    <button id="nextBtn" class="game-btn primary" disabled>Next </button>
                    <button id="resetBtn" class="game-btn warning">Reset</button>
                    <button id="submitBtn" class="game-btn primary" disabled>Submit Exam</button>
                </div>
            </div>

            <div class="side-panel">
                <h3>Progress</h3>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" id="progressFill"></div>
                </div>
                <div id="progressText" style="font-size:13px;">0 of 0</div>

                <div id="resultContainer" style="display:none;">
                    <div class="result-box">
                        <div id="resultStars" class="stars"></div>
                        <div id="resultScore" style="font-size:20px; font-weight:bold;"></div>
                        <div id="resultFeedback" class="feedback"></div>
                        <div id="saveStatus"></div>
                    </div>
                </div>

                <h3>Instructions</h3>
                <ul>
                    <li>Read each question carefully</li>
                    <li>Choose the correct answer from 4 options</li>
                    <li>You can navigate between questions before submitting</li>
                    <li>Your score will appear after clicking "Submit Exam"</li>
                    <li>Results will be saved automatically</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Pass PHP variables to JavaScript
    const GAME_LEVEL = <?php echo $level; ?>;
    const TOTAL_QUESTIONS = 10;
    
    console.log('Loading level: ' + GAME_LEVEL);
</script>
</body>
</html>