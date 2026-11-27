<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Arabic Competence Exam</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
  <div class="hero">
        <div class="hero-icon"></div>
    <h1>Arabic Competence Exam</h1>
    <p>test your Arabic language skills through a variety of questions in reading, comprehension, grammar, and spelling.</p>
	</div>
 <div class="info-grid">
        <div class="info-card">
            <h3>Exam Duration</h3>
            <p>30 minutes per level</p>
            <p>Complete levels at your own pace</p>
        </div>
	<div class="info-card">
            <h3>Levels</h3>
            <div class="level-badge">Level 1 - Beginner</div>
            <div class="level-badge">Level 2 - Intermediate</div>
            <div class="level-badge">Level 3 - Advanced</div>
            <p style="margin-top: 10px;">10 questions per level</p>
        </div>
		<div class="info-card">
            <h3>Skills Tested</h3>
            <div class="skills-list">
                <span class="skill">Reading & Comprehension</span>
                <span class="skill">Grammar</span>
                <span class="skill">Spelling</span>
                <span class="skill">Vocabulary</span>
                <span class="skill">Rhetoric</span>
            </div>
        </div>
	 <div class="info-card">
            <h3>Grading System</h3>
            <p>80-100%: ★★★★★ Excellent</p>
            <p>60-79%: ★★★★☆ Very Good</p>
            <p>40-59%: ★★★☆☆ Good</p>
            <p>Below 40%: ★★☆☆☆ Needs Improvement</p>
        </div>
    </div>
	
 <div class="hero">
        <h2>Start the Exam Now</h2>
        <p>Choose your level and start testing your language skills</p>	
	
    <?php if (isset($_SESSION['user_id'])): ?>
          <div class="btn-group">
                <a href="game.php?level=1" class="btn btn-primary">Level 1 (Beginner)</a>
                <a href="game.php?level=2" class="btn btn-primary">Level 2 (Intermediate)</a>
                <a href="game.php?level=3" class="btn btn-primary">Level 3 (Advanced)</a>
                <a href="reports.php" class="btn btn-secondary">My Results</a>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
            <div class="welcome-user">
                Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            </div>
    <?php else: ?>
         <div class="btn-group">
            <a href="login.php" class="btn">Login</a>
            <a href="register.php" class="btn">Register</a>
        </div>
		           <p style="margin-top: 15px;">Login to save your progress and results</p>
        <?php endif; ?>
    </div>

    <div class="hero">
        <h2>About the Exam</h2>
        <p>This exam is designed to measure your proficiency in Arabic according to modern educational standards.</p>
        <p>The exam contains various questions covering different aspects of the language: reading and comprehension, grammar and morphology, spelling, and vocabulary.</p>
        <p>After completing each level, your score will appear immediately with a star rating, and the result will be saved for later viewing on the results page.</p>
    </div>

</div>
</body>
</html>
