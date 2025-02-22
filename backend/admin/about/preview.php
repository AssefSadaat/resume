<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db_connect.php';
require_once '../auth.php';

// Check if user is logged in
requireLogin();

try {
    // Fetch latest about entry
    $stmt = $pdo->query("SELECT * FROM about ORDER BY created_at DESC LIMIT 1");
    $about = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Preview About | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a202c;
            --secondary-color: #2d3748;
            --accent-color: #4299e1;
            --text-color: #e2e8f0;
            --border-color: #4a5568;
            --hover-color: #3182ce;
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

        .preview-container {
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

        .preview-card {
            background: var(--secondary-color);
            border-radius: 0.5rem;
            padding: 2rem;
            border: 1px solid var(--border-color);
        }

        .preview-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .preview-name {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
        }

        .preview-title {
            font-size: 1.25rem;
            color: var(--accent-color);
        }

        .preview-bio {
            color: #a0aec0;
            font-size: 1rem;
            line-height: 1.7;
            white-space: pre-line;
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
            color: white;
        }

        .btn-secondary {
            background: var(--secondary-color);
            border: 1px solid var(--border-color);
        }

        .btn:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        .no-data {
            text-align: center;
            padding: 2rem;
            color: #a0aec0;
        }

        @media (max-width: 768px) {
            .preview-container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="header">
            <h1>Preview About</h1>
            <a href="../index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if ($about): ?>
        <div class="preview-card">
            <div class="preview-header">
                <div class="preview-name"><?php echo htmlspecialchars($about['name']); ?></div>
                <div class="preview-title"><?php echo htmlspecialchars($about['title']); ?></div>
            </div>
            <div class="preview-bio">
                <?php echo nl2br(htmlspecialchars($about['bio'])); ?>
            </div>
        </div>
        <?php else: ?>
        <div class="no-data">
            <i class="fas fa-info-circle fa-2x mb-2"></i>
            <p>No about information found. Please add some content first.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>