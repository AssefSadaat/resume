<?php
require_once '../auth.php';
require_once '../db_connect.php';

try {
    // Fetch all education records
    $stmt = $pdo->prepare("SELECT * FROM education ORDER BY start_date DESC");
    $stmt->execute();
    $educationData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching education data: " . $e->getMessage());
    $educationData = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'] ?? null;
        $institution = $_POST['institution'] ?? '';
        $degree = $_POST['degree'] ?? '';
        $field = $_POST['field'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?: null;
        $description = $_POST['description'] ?? '';

        $stmt = $pdo->prepare("
            UPDATE education 
            SET institution = ?, degree = ?, field = ?, 
                start_date = ?, end_date = ?, description = ?
            WHERE id = ?
        ");

        $stmt->execute([$institution, $degree, $field, $start_date, $end_date, $description, $id]);
        $message = "Education updated successfully!";
        
    } catch (PDOException $e) {
        error_log("Error updating education: " . $e->getMessage());
        $error = "Failed to update education entry.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Education | Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #00b4b3;
            --primary-dark: #009e9d;
            --secondary: #818cf8;
            --dark: #1f2937;
            --darker: #111827;
            --darkest: #0f172a;
            --light: #f3f4f6;
            --gray: #94a3b8;
            --success: #10b981;
            --danger: #ef4444;
        }

        body {
            background: linear-gradient(135deg, var(--darker) 0%, var(--darkest) 100%);
            color: var(--light);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
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

        .header h1 {
            font-size: 1.5rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: white;
        }

        .header i {
            color: var(--primary);
        }

        .education-list {
            display: grid;
            gap: 2rem;
        }

        .education-item {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 1rem;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .education-item:hover {
            transform: translateY(-2px);
            border-color: var(--primary);
        }

        .edit-form {
            display: grid;
            gap: 1.5rem;
        }

        .form-group {
            display: grid;
            gap: 0.5rem;
        }

        .form-group label {
            color: var(--gray);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            color: white;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(0, 180, 179, 0.25);
            background: rgba(255, 255, 255, 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
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

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease;
        }

        .success {
            background: var(--success);
            color: white;
        }

        .error {
            background: var(--danger);
            color: white;
        }

        @keyframes slideIn {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
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

            .education-item {
                padding: 1.5rem;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .education-item:hover {
                transform: none;
            }
            
            .btn:hover {
                transform: none;
            }

            .alert {
                animation: none;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="header">
            <div class="header-left">
                <h1>Edit Education</h1>
                <a href="../index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert success">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="education-list">
            <?php foreach ($educationData as $education): ?>
                <div class="education-item">
                    <form method="POST" class="edit-form">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($education['id']); ?>">
                        
                        <div class="form-group">
                            <label for="institution_<?php echo $education['id']; ?>">Institution</label>
                            <input type="text" id="institution_<?php echo $education['id']; ?>" 
                                   name="institution" value="<?php echo htmlspecialchars($education['institution']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="degree_<?php echo $education['id']; ?>">Degree</label>
                            <input type="text" id="degree_<?php echo $education['id']; ?>" 
                                   name="degree" value="<?php echo htmlspecialchars($education['degree']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="field_<?php echo $education['id']; ?>">Field of Study</label>
                            <input type="text" id="field_<?php echo $education['id']; ?>" 
                                   name="field" value="<?php echo htmlspecialchars($education['field']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="start_date_<?php echo $education['id']; ?>">Start Date</label>
                            <input type="date" id="start_date_<?php echo $education['id']; ?>" 
                                   name="start_date" value="<?php echo htmlspecialchars($education['start_date']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="end_date_<?php echo $education['id']; ?>">End Date</label>
                            <input type="date" id="end_date_<?php echo $education['id']; ?>" 
                                   name="end_date" value="<?php echo htmlspecialchars($education['end_date'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="description_<?php echo $education['id']; ?>">Description</label>
                            <textarea id="description_<?php echo $education['id']; ?>" 
                                    name="description" rows="3"><?php echo htmlspecialchars($education['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="button-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>