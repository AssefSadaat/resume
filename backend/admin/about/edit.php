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
    $stmt = $pdo->prepare("SELECT * FROM about WHERE id = ?");
    $stmt->execute([$id]);
    $entry = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$entry) {
        header('Location: list.php');
        exit();
    }
} catch(PDOException $e) {
    $error = "Error fetching entry: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
        $bio = htmlspecialchars(trim($_POST['bio']), ENT_QUOTES, 'UTF-8');

        if (empty($name) || empty($title) || empty($bio)) {
            throw new Exception("All fields are required");
        }

        $sql = "UPDATE about SET name = ?, title = ?, bio = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $title, $bio, $id]);

        $success = "Entry updated successfully!";
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit About Entry | Admin</title>
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

        .dashboard {
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
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            color: white;
        }

        .btn-primary {
            background: var(--accent-color);
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

        .form-grid {
            background: var(--secondary-color);
            padding: 2rem;
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
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

        input, textarea {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary-color);
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            color: var(--text-color);
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
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
            .dashboard {
                padding: 1rem;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .header-left {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="header">
            <div class="header-left">
                <h1><i class="fas fa-user"></i> Edit About Entry</h1>
                <a href="list.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
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
                <label for="name">Name</label>
                <input type="text" id="name" name="name" 
                       value="<?php echo htmlspecialchars($entry['name']); ?>" 
                       placeholder="Enter your full name"
                       required>
            </div>

            <div class="form-group">
                <label for="title">Professional Title</label>
                <input type="text" id="title" name="title" 
                       value="<?php echo htmlspecialchars($entry['title']); ?>" 
                       placeholder="e.g. Full Stack Developer"
                       required>
            </div>

            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" rows="6" 
                          placeholder="Write a brief description about yourself"
                          required><?php echo htmlspecialchars($entry['bio']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </form>
    </div>
</body>
</html>