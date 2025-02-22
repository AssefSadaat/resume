<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';
require_once 'auth.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Use htmlspecialchars instead of deprecated FILTER_SANITIZE_STRING
        $username = htmlspecialchars(trim($_POST['username'] ?? ''), ENT_QUOTES, 'UTF-8');
        $password = $_POST['password'] ?? '';

        // Basic validation
        if (empty($username) || empty($password)) {
            throw new Exception("All fields are required");
        }

        // Query the admin_users table
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_id'] = $user['id'];

            // Redirect to dashboard
            header('Location: index.php');
            exit();
        } else {
            $error = "Invalid username or password";
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
    <title>Login | Portfolio Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a202c;
            --secondary-color: #2d3748;
            --accent-color: #4299e1;
            --text-color: #e2e8f0;
            --border-color: #4a5568;
            --hover-color: #3182ce;
            --error-color: #f56565;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--primary-color);
            color: var(--text-color);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.5;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 1rem;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
        }

        .header p {
            color: #718096;
            font-size: 0.875rem;
        }

        .login-card {
            background: var(--secondary-color);
            padding: 2rem;
            border-radius: 0.75rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #a0aec0;
            font-size: 0.875rem;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary-color);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            color: var(--text-color);
            font-size: 1rem;
            transition: all 0.2s;
        }

        input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
        }

        .error-message {
            background: var(--error-color);
            color: white;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        button:hover {
            background: var(--hover-color);
            transform: translateY(-1px);
        }

        .footer {
            text-align: center;
            margin-top: 2rem;
            color: #718096;
            font-size: 0.875rem;
        }

        .footer a {
            color: var(--accent-color);
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-code"></i>
            </div>
            <p>Sign in to access your admin dashboard</p>
        </div>

        <div class="login-card">
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required 
                           placeholder="Enter your username"
                           autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Enter your password"
                           autocomplete="current-password">
                </div>

                <button type="submit">
                    <i class="fas fa-sign-in-alt"></i> Sign in
                </button>
            </form>
        </div>

        <div class="footer">
            <p>Back to <a href="../../frontend/index.php">Resume</a></p>
        </div>
    </div>
</body>
</html>