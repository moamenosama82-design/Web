<?php
// register.php
session_start();
require 'api/db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email    = ''; // we don't ask for it in the form anymore

    if ($username === '' || $password === '') {
        $errors[] = "Username and password are required.";
    }

    if (empty($errors)) {
        // Check if username already exists
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Username already taken. Please choose another one.";
        } else {
            $stmt->close();

            // For assignment we keep password as plain text (NOT for real life!)
            $stmt = $mysqli->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                $success = "Registration successful! <a href='login.php'>Login here</a>.";
            } else {
                $errors[] = "Error creating account. Please try again.";
            }
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - arabic exam</title>

    <!-- optional global css -->
    <link rel="stylesheet" href="css/style.css">

    <!-- page-specific styles (same style as login) -->
    <style>
        body.auth-body {
            background: linear-gradient(135deg, #162447, #1f4068, #1b1b2f);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 16px;
            padding: 24px 26px 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(6px);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 18px;
        }

        .auth-icon {
            font-size: 40px;
            margin-bottom: 6px;
        }

        .auth-header h1 {
            margin: 0;
            font-size: 24px;
            color: #1b1b2f;
        }

        .auth-subtitle {
            margin-top: 4px;
            color: #555;
            font-size: 13px;
        }

        .auth-form .form-group {
            margin-bottom: 14px;
        }

        .auth-form label {
            display: block;
            font-size: 13px;
            margin-bottom: 4px;
            color: #333;
        }

        .auth-form input[type="text"],
        .auth-form input[type="password"] {
            width: 100%;
            padding: 8px 9px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 14px;
        }

        .auth-form input[type="text"]:focus,
        .auth-form input[type="password"]:focus {
            outline: none;
            border-color: #2c7be5;
            box-shadow: 0 0 0 2px rgba(44, 123, 229, 0.2);
        }

        .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            text-decoration: none;
            border: none;
            background: #2c7be5;
            color: #fff;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.18s ease, transform 0.1s ease;
        }

        .btn:hover {
            background: #1b5bb8;
            transform: translateY(-1px);
        }

        .btn-full {
            width: 100%;
            text-align: center;
        }

        .msg {
            padding: 8px 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            font-size: 13px;
        }

        .msg.error {
            background: #ffe5e5;
            color: #b30000;
        }

        .msg.success {
            background: #e6ffea;
            color: #006600;
        }

        .auth-footer-text {
            margin-top: 14px;
            text-align: center;
            font-size: 13px;
            color: #444;
        }

        .auth-footer-text a {
            color: #2c7be5;
            text-decoration: none;
        }

        .auth-footer-text a:hover {
            text-decoration: underline;
        }

        .auth-footer-small {
            margin-top: 6px;
            text-align: center;
            font-size: 12px;
        }

        .auth-footer-small a {
            color: #777;
            text-decoration: none;
        }

        .auth-footer-small a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="auth-body">

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon"></div>
            <h1>Create Account</h1>
            <p class="auth-subtitle">Register to start your arabic exam</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="msg error">
                <?php foreach ($errors as $e): ?>
                    <p><?php echo htmlspecialchars($e); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="msg success">
                <p><?php echo $success; ?></p>
            </div>
        <?php endif; ?>

        <form method="post" action="register.php" class="auth-form">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Choose a username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Create a password" required>
            </div>

            <button type="submit" class="btn btn-full">Register</button>
        </form>

        <p class="auth-footer-text">
            Already have an account?
            <a href="login.php">Login here</a>
        </p>

        <p class="auth-footer-small">
            <a href="index.php">← Back to Home</a>
        </p>
    </div>
</div>

</body>
</html>
