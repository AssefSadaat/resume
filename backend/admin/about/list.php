<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db_connect.php';
require_once '../auth.php';

requireLogin();

try {
    $stmt = $pdo->query("SELECT * FROM about ORDER BY created_at DESC");
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching about entries: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>About Entries | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a202c;
            --secondary-color: #2d3748;
            --accent-color: #4299e1;
            --text-color: #e2e8f0;
            --border-color: #4a5568;
            --hover-color: #3182ce;
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
            min-height: 100vh;
        }

        .dashboard {
            max-width: 1200px;
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

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--accent-color);
            color: white;
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        .entries-list {
            display: grid;
            gap: 1rem;
        }

        .entry-card {
            background: var(--secondary-color);
            border-radius: 0.5rem;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.2s;
        }

        .entry-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .entry-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: white;
        }

        .entry-card p {
            color: #a0aec0;
            margin-bottom: 1rem;
        }

        .button-group {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .no-entries {
            text-align: center;
            padding: 2rem;
            background: var(--secondary-color);
            border-radius: 0.5rem;
            color: #a0aec0;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .header-left {
                flex-direction: column;
                align-items: flex-start;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
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
            background: var(--danger-color);
            color: white;
        }

        .delete-form {
            margin: 0;
        }

        .delete-form button {
            border: none;
            font-family: inherit;
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <div class="header">
            <div class="header-left">
                <h1><i class="fas fa-user"></i> About Entries</h1>
                <a href="../index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i>
                <?php
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i>
                <?php
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>
        <div class="entries-list">
            <?php if (!empty($entries)):
                foreach ($entries as $entry): ?>
                    <div class="entry-card">
                        <h3><?php echo htmlspecialchars($entry['name']); ?></h3>
                        <p><?php echo htmlspecialchars($entry['title']); ?></p>
                        <p class="text-sm text-gray-400">
                            Last updated: <?php echo date('M j, Y', strtotime($entry['created_at'])); ?>
                        </p>
                        <div class="button-group">
                            <a href="edit.php?id=<?php echo $entry['id']; ?>" class="btn btn-secondary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="delete.php" class="delete-form" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this entry? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                <p class="no-entries">
                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                    No entries found
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>