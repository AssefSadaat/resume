<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_connect.php';
require_once 'auth.php';

requireLogin();

$success = '';
$error = '';

// Fetch all users
try {
    $stmt = $pdo->query("SELECT id, username FROM admin_users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching users: " . $e->getMessage();
}

// Replace the existing POST handler section with this code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    try {
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

        // Check if password fields are set
        if (!isset($_POST['new_password']) || !isset($_POST['confirm_password'])) {
            throw new Exception("Password fields are required");
        }

        $newPassword = trim($_POST['new_password']);
        $confirmPassword = trim($_POST['confirm_password']);

        if (!$userId) {
            throw new Exception("Please select a user");
        }

        if (empty($newPassword) || empty($confirmPassword)) {
            throw new Exception("All fields are required");
        }

        if (strlen($newPassword) < 8) {
            throw new Exception("New password must be at least 8 characters long");
        }

        if ($newPassword !== $confirmPassword) {
            throw new Exception("Passwords do not match");
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);

        $success = "Password updated successfully!";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
// Add this code after the existing POST handler and before the HTML
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

        if (!$userId) {
            throw new Exception("Invalid user selected");
        }

        // Prevent deleting the last admin user
        $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
        $totalUsers = $stmt->fetchColumn();

        if ($totalUsers <= 1) {
            throw new Exception("Cannot delete the last administrator account");
        }

        // Prevent self-deletion
        if ($userId == $_SESSION['admin_id']) {
            throw new Exception("You cannot delete your own account");
        }

        $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
        $stmt->execute([$userId]);

        $success = "User deleted successfully";

        // Refresh users list
        $stmt = $pdo->query("SELECT id, username FROM admin_users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Users | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a202c;
            --secondary-color: #2d3748;
            --accent-color: #4299e1;
            --text-color: #e2e8f0;
            --border-color: #4a5568;
            --danger-color: #f56565;
            --success-color: #48bb78;
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
        }

        .container {
            max-width: 800px;
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

        .title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .card {
            background: var(--secondary-color);
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: rgba(0, 0, 0, 0.1);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            font-weight: 500;
        }

        .card-body {
            padding: 1.5rem;
        }

        .user-select {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
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

        .input-group {
            position: relative;
        }

        select,
        input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: var(--primary-color);
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            color: var(--text-color);
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        select:focus,
        input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-primary {
            background: var(--accent-color);
            color: white;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-secondary {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-color);
        }

        .btn:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #718096;
            cursor: pointer;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-success {
            background: var(--success-color);
            color: white;
        }

        .alert-error {
            background: var(--danger-color);
            color: white;
        }

        .help-text {
            font-size: 0.75rem;
            color: #718096;
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .user-select {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Update the user-select styles */
        .user-select {
            display: flex;
            align-items: flex-end;
            /* Align items to bottom */
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .user-select .form-group {
            flex: 1;
            margin-bottom: 0;
            /* Remove bottom margin since it's handled by user-select */
        }

        .user-select .btn-danger {
            height: 45px;
            /* Match height with select input */
            padding: 0 1.5rem;
            white-space: nowrap;
        }

        /* Update media query for mobile */
        @media (max-width: 768px) {
            .user-select {
                flex-direction: column;
                align-items: stretch;
            }

            .user-select .btn-danger {
                height: auto;
                margin-top: 0.5rem;
            }
        }
        a{
            text-decoration: none;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1 class="title">
                <i class="fas fa-users-cog"></i>
                Manage Users
            </h1>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                Change User Password
            </div>
            <div class="card-body">
                <form method="POST" id="passwordForm">
                    <div class="user-select">
                        <div class="form-group">
                            <label for="user_id">Select User</label>
                            <select id="user_id" name="user_id" required>
                                <option value="">Choose a user...</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>">
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="button" onclick="deleteUser()" class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                            Delete User
                        </button>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <div class="input-group">
                            <input type="password" id="new_password" name="new_password"
                                placeholder="Enter new password" minlength="8" required>
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('new_password')"></i>
                        </div>
                        <div class="help-text">Must be at least 8 characters long</div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" id="confirm_password" name="confirm_password"
                                placeholder="Confirm new password" required>
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password')"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        function deleteUser() {
            const select = document.getElementById('user_id');
            const userId = select.value;

            if (!userId) {
                alert('Please select a user first');
                return;
            }

            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="user_id" value="${userId}">
            `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>