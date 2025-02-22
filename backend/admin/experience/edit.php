<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db_connect.php';
require_once '../auth.php';

requireLogin();

$error = '';
$success = '';
$entry = null;

// Get entry ID from URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: list.php');
    exit();
}

// Fetch existing entry
try {
    $stmt = $pdo->prepare("SELECT * FROM experience WHERE id = ?");
    $stmt->execute([$id]);
    $entry = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$entry) {
        header('Location: list.php');
        exit();
    }
} catch (PDOException $e) {
    $error = "Error fetching entry: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $company = htmlspecialchars(trim($_POST['company']), ENT_QUOTES, 'UTF-8');
        $position = htmlspecialchars(trim($_POST['position']), ENT_QUOTES, 'UTF-8');
        $start_date = $_POST['start_date'];
        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        $responsibilities = htmlspecialchars(trim($_POST['responsibilities']), ENT_QUOTES, 'UTF-8');
        $technologies = htmlspecialchars(trim($_POST['technologies']), ENT_QUOTES, 'UTF-8');

        if (empty($company) || empty($position) || empty($start_date)) {
            throw new Exception("Company, position, and start date are required");
        }

        $sql = "UPDATE experience SET company = ?, position = ?, start_date = ?, 
        end_date = ?, responsibilities = ?, technologies = ? WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$company, $position, $start_date, $end_date, $responsibilities, $technologies, $id]);

        $success = "Experience entry updated successfully!";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Experience | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            min-height: 100vh;
        }

        .dashboard {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .header {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 1.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
        }

        .header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: white;
        }

        .header h1 i {
            color: var(--primary);
            font-size: 1.5rem;
        }

        .form-grid {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 1.5rem;
            padding: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 2rem;
        }

        label {
            display: block;
            color: var(--gray);
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.025em;
        }

        input,
        textarea {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:hover,
        textarea:hover {
            border-color: rgba(59, 130, 246, 0.5);
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
            background: rgba(255, 255, 255, 0.08);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.75rem;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary:hover {
            box-shadow: 0 6px 8px -1px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .message {
            padding: 1.25rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }

        .success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #34d399;
        }

        .error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
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
                margin: 1rem auto;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header h1 {
                justify-content: center;
            }

            input,
            textarea {
                font-size: 16px;
                /* Prevent zoom on mobile */
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <div class="header">
            <h1><i class="fas fa-briefcase"></i> Edit Experience</h1>
            <a href="../index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if ($success): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-grid">
            <div class="form-group">
                <label for="company">Company Name</label>
                <input type="text" id="company" name="company"
                    value="<?php echo htmlspecialchars($entry['company']); ?>" required>
            </div>

            <div class="form-group">
                <label for="position">Position</label>
                <input type="text" id="position" name="position"
                    value="<?php echo htmlspecialchars($entry['position']); ?>" required>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date"
                    value="<?php echo htmlspecialchars($entry['start_date']); ?>" required>
            </div>

            <div class="form-group">
                <label for="end_date">End Date (Leave empty if current)</label>
                <input type="date" id="end_date" name="end_date"
                    value="<?php echo htmlspecialchars($entry['end_date'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="technologies">Technologies Used</label>
                <input type="text" id="technologies" name="technologies"
                    value="<?php echo htmlspecialchars($entry['technologies']); ?>"
                    placeholder="e.g., PHP, MySQL, JavaScript">
            </div>

            <div class="form-group">
                <label for="responsibilities">Responsibilities</label>
                <textarea id="responsibilities" name="responsibilities" rows="6" required><?php
                echo htmlspecialchars($entry['responsibilities'] ?? '');
                ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Save Changes
    </button>
    </form>
    </div>
</body>

</html>