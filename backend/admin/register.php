<?php
require_once 'auth.php';
require_once 'db_connect.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRFToken();

    try {
        // Sanitize and validate username
        $username = trim(htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'));
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Enhanced validation
        if (empty($username) || empty($password) || empty($confirm_password)) {
            throw new Exception("All fields are required");
        }

        // Username validation
        if (strlen($username) < 3 || strlen($username) > 50) {
            throw new Exception("Username must be between 3 and 50 characters");
        }

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            throw new Exception("Username can only contain letters, numbers, underscores and hyphens");
        }

        // Password validation
        if (strlen($password) < 8) {
            throw new Exception("Password must be at least 8 characters long");
        }

        if (!preg_match('/[A-Z]/', $password)) {
            throw new Exception("Password must contain at least one uppercase letter");
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new Exception("Password must contain at least one lowercase letter");
        }

        if (!preg_match('/[0-9]/', $password)) {
            throw new Exception("Password must contain at least one number");
        }

        if ($password !== $confirm_password) {
            throw new Exception("Passwords do not match");
        }

        // Check if username already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Username already exists");
        }

        // Hash password and insert user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$username, $hashed_password]);

        // Clear sensitive data
        $password = $confirm_password = null;

        // Redirect to login page after successful registration
        header("Location: login.php?registered=1");
        exit;

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "Username already exists";
        } else {
            error_log("Database error: " . $e->getMessage());
            $error = "An error occurred during registration. Please try again.";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register Admin User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a202c;
            --secondary-color: #2d3748;
            --accent-color: #4299e1;
            --danger-color: #f56565;
            --success-color: #48bb78;
            --text-color: #e2e8f0;
            --border-color: #4a5568;
            --hover-color: #3182ce;
        }

        body {
            background-color: var(--primary-color);
            color: var(--text-color);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.5;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }

        form {
            background: var(--secondary-color);
            padding: 2rem;
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
        }

        h1 {
            text-align: center;
            color: white;
            font-size: 1.5rem;
            margin: 0 0 2rem 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-size: 0.875rem;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary-color);
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            color: var(--text-color);
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.2);
        }

        .password-hint {
            font-size: 0.75rem;
            color: #718096;
            margin-top: 0.5rem;
        }

        .message {
            padding: 0.75rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .success {
            background: var(--success-color);
            color: white;
        }

        .error {
            background: var(--danger-color);
            color: white;
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        button:hover {
            background: var(--hover-color);
            transform: translateY(-1px);
        }

        .login-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-color);
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        .login-link:hover {
            color: var(--accent-color);
        }
    </style>
</head>

<body>
    <div class="container">
        <form method="POST" id="registerForm">
            <h1><i class="fas fa-user-plus"></i> Register New Admin</h1>

            <?php if ($error): ?>
                <div class="message error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required minlength="3" maxlength="50"
                    pattern="[a-zA-Z0-9_-]+" placeholder="Choose a username"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8"
                    placeholder="Choose a password">
                <div class="password-requirements">
                    <div class="requirement" id="length">
                        <i class=""></i> 
                    </div>
                    
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                    placeholder="Confirm your password">
            </div>

            <button type="submit" id="submitBtn" disabled>
                <i class="fas fa-user-plus"></i> Register Account
            </button>

            <a href="login.php" class="login-link">
                Already have an account? Go to Dashboard
            </a>
        </form>
    </div>

    <script>
        // Password validation
        const password = document.getElementById("password");
        const confirm_password = document.getElementById("confirm_password");

        function validatePassword() {
            if (password.value !== confirm_password.value) {
                confirm_password.setCustomValidity("Passwords don't match");
            } else {
                confirm_password.setCustomValidity('');
            }
        }

        password.onchange = validatePassword;
        confirm_password.onkeyup = validatePassword;
    </script>
</body>

</html>