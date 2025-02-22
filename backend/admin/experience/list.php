<?php
declare(strict_types=1);
session_start();

require_once '../db_connect.php';
require_once '../auth.php';

function logError(string $message): void
{
    error_log($message);
}

requireLogin();

try {
    $stmt = $pdo->query("SELECT * FROM experience ORDER BY start_date DESC");
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    logError("Error fetching experience entries: " . $e->getMessage());
    $entries = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Experience Entries | Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --secondary: #818cf8;
            --dark: #1e293b;
            --darker: #0f172a;
            --darkest: #020617;
            --light: #f8fafc;
            --gray: #94a3b8;
            --success: #10b981;
            --danger: #ef4444;
        }

        body {
            background: linear-gradient(135deg, var(--darker) 0%, var(--darkest) 100%);
            color: var(--light);
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            margin: 0;
            min-height: 100vh;
        }

        .dashboard {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 1rem;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        h1 {
            font-size: 1.5rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: white;
        }

        h1 i {
            color: var(--primary);
        }

        .entries-list {
            display: grid;
            gap: 1.5rem;
        }

        .entry-card {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .entry-card:hover {
            transform: translateY(-2px);
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .entry-card h3 {
            margin: 0 0 0.5rem;
            color: white;
            font-size: 1.25rem;
        }

        .company {
            color: var(--primary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .date {
            color: var(--gray);
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .responsibilities {
            color: var(--gray);
            margin-bottom: 1rem;
        }

        .technologies {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .tech-tag {
            background: rgba(79, 70, 229, 0.1);
            color: var(--secondary);
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            border: 1px solid rgba(79, 70, 229, 0.2);
        }

        .button-group {
            display: flex;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .message {
            padding: 1rem 1.25rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease;
        }

        .success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #34d399;
        }

        .no-entries {
            text-align: center;
            color: var(--gray);
            padding: 3rem;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        @keyframes slideIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .dashboard {
                padding: 1rem;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .header-left {
                flex-direction: column;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .entry-card:hover {
                transform: none;
            }

            .btn:hover {
                transform: none;
            }

            .message {
                animation: none;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <div class="header">
            <div class="header-left">
                <h1><i class="fas fa-briefcase"></i> Experience Entries</h1>
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

        <div class="entries-list">
            <?php if (!empty($entries)):
                foreach ($entries as $entry): ?>
                    <div class="entry-card">
                        <h3><?php echo htmlspecialchars($entry['position']); ?></h3>
                        <p class="company"><?php echo htmlspecialchars($entry['company']); ?></p>
                        <p class="date">
                            <?php echo date('M Y', strtotime($entry['start_date'])); ?> -
                            <?php echo $entry['end_date'] ? date('M Y', strtotime($entry['end_date'])) : 'Present'; ?>
                        </p>
                        <p class="responsibilities">
                            <?php echo nl2br(htmlspecialchars($entry['responsibilities'] ?? '')); ?>
                        </p>
                        <?php if (!empty($entry['technologies'])): ?>
                            <div class="technologies">
                                <?php foreach (explode(',', $entry['technologies']) as $tech): ?>
                                    <span class="tech-tag"><?php echo htmlspecialchars(trim($tech)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="button-group">
                            <a href="edit.php?id=<?php echo $entry['id']; ?>" class="btn btn-secondary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="delete.php" class="delete-form" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this entry?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                <p class="no-entries">No experience entries found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>