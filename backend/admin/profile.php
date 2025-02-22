<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_connect.php';
require_once 'auth.php';

requireLogin();

$success = '';
$error = '';

try {
    // Fetch current user data
    $stmt = $pdo->prepare("SELECT username, email FROM admin_users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = htmlspecialchars(trim($_POST['username']));
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        if (empty($username) || empty($email)) {
            throw new Exception("All fields are required");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Check if email is already in use by another user
        $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['admin_id']]);
        if ($stmt->fetch()) {
            throw new Exception("Email is already in use");
        }

        $stmt = $pdo->prepare("UPDATE admin_users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $_SESSION['admin_id']]);
        
        $success = "Profile updated successfully!";
        $_SESSION['admin_username'] = $username;
    }
} catch(Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Settings | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a202c;
            --secondary-color: #2d3748;
            --accent-color: #4299e1;
            --text-color: #e2e8f0;
            --border-color: #4a5568;
            --hover-color: #3182ce;
            --success-color: #48bb78;
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
        }

        .settings-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
        }

        .form-card {
            background: var(--secondary-color);
            border-radius: 0.5rem;
            padding: 2rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #a0aec0;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary-color);
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            color: var(--text-color);
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: var(--accent-color);
            color: white;
        }

        .btn-secondary {
            background: var(--secondary-color);
            border: 1px solid var(--border-color);
            color: var(--text-color);
        }

        .btn:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        .message {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
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
            background: var(--error-color);
            color: white;
        }

        @media (max-width: 768px) {
            .settings-container {
                padding: 1rem;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <div class="header">
            <h1><i class="fas fa-user-cog"></i> Profile Settings</h1>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if ($success): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" 
                           placeholder="Enter your username"
                           required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" 
                           placeholder="Enter your email address"
                           required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
</body>
</html>