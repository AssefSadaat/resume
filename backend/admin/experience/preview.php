<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db_connect.php';
require_once '../auth.php';

requireLogin();

try {
    $stmt = $pdo->query("SELECT * FROM experience ORDER BY start_date DESC");
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Preview Experience | Admin</title>
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
        }

        .preview-card h3 {
            font-size: 1.25rem;
            color: white;
            margin-bottom: 0.5rem;
        }

        .company {
            color: var(--accent-color);
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .date {
            color: #a0aec0;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .responsibilities {
            color: #e2e8f0;
            margin-bottom: 1rem;
            white-space: pre-line;
        }

        .technologies {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .tech-tag {
            background: var(--primary-color);
            color: var(--accent-color);
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            border: 1px solid var(--border-color);
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
            background: var(--secondary-color);
            border-radius: 0.5rem;
            color: #a0aec0;
        }

        .no-data i {
            margin-bottom: 1rem;
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
            <h1>Preview Experience</h1>
            <a href="../index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if (!empty($entries)): ?>
            <div class="preview-grid">
                <?php foreach ($entries as $entry): ?>
                    <div class="preview-card">
                        <h3><?php echo htmlspecialchars($entry['position']); ?></h3>
                        <p class="company"><?php echo htmlspecialchars($entry['company']); ?></p>
                        <p class="date">
                            <?php echo date('M Y', strtotime($entry['start_date'])); ?> -
                            <?php echo $entry['end_date'] ? date('M Y', strtotime($entry['end_date'])) : 'Present'; ?>
                        </p>
                        <?php if (isset($entry['responsibilities']) && !empty($entry['responsibilities'])): ?>
                            <div class="responsibilities">
                                <?php echo nl2br(htmlspecialchars($entry['responsibilities'] ?? '')); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($entry['technologies'])): ?>
                            <div class="technologies">
                                <?php foreach (explode(',', $entry['technologies']) as $tech): ?>
                                    <span class="tech-tag"><?php echo htmlspecialchars(trim($tech)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-briefcase fa-2x"></i>
                <p>No experience entries found. Please add some content first.</p>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>