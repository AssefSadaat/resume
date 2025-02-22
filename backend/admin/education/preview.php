<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db_connect.php';
require_once '../auth.php';

requireLogin();

try {
    $stmt = $pdo->query("SELECT * FROM education ORDER BY start_date DESC");
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Preview Education | Admin</title>
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

        .preview-grid {
            display: grid;
            gap: 1.5rem;
        }

        .preview-card {
            background: var(--secondary-color);
            border-radius: 0.5rem;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: transform 0.2s;
        }

        .preview-card:hover {
            transform: translateY(-2px);
        }

        .preview-card h3 {
            font-size: 1.25rem;
            color: white;
            margin-bottom: 0.5rem;
        }

        .degree {
            color: var(--accent-color);
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }

        .field {
            color: #a0aec0;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .date {
            color: #718096;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .description {
            color: #e2e8f0;
            font-size: 0.95rem;
            line-height: 1.6;
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
            padding: 3rem 2rem;
            background: var(--secondary-color);
            border-radius: 0.5rem;
            color: #a0aec0;
        }

        .no-data i {
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .preview-container {
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
    <div class="preview-container">
        <div class="header">
            <h1><i class="fas fa-graduation-cap"></i> Preview Education</h1>
            <a href="../index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if (!empty($entries)): ?>
            <div class="preview-grid">
                <?php foreach ($entries as $entry): ?>
                    <div class="preview-card">
                        <h3><?php echo htmlspecialchars($entry['institution']); ?></h3>
                        <p class="degree"><?php echo htmlspecialchars($entry['degree']); ?></p>
                        <p class="field"><?php echo htmlspecialchars($entry['field']); ?></p>
                        <p class="date">
                            <i class="far fa-calendar-alt"></i>
                            <?php echo date('M Y', strtotime($entry['start_date'])); ?> - 
                            <?php echo $entry['end_date'] ? date('M Y', strtotime($entry['end_date'])) : 'Present'; ?>
                        </p>
                        <?php if (!empty($entry['description'])): ?>
                            <p class="description">
                                <?php echo nl2br(htmlspecialchars($entry['description'])); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-graduation-cap fa-3x"></i>
                <p>No education entries found. Please add some content first.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>