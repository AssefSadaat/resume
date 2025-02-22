<?php
require_once 'db_connect.php';
require_once 'auth.php';

function validateInput($data) {
    return htmlspecialchars(trim($data));
}
requireLogin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRFToken();

    try {
        $institution = validateInput($_POST['institution']);
        $degree = validateInput($_POST['degree']);
        $field = validateInput($_POST['field']);
        $start_date = validateInput($_POST['start_date']);
        $end_date = validateInput($_POST['end_date']);
        $description = validateInput($_POST['description']);

        if (empty($institution) || empty($degree) || empty($field) || empty($start_date)) {
            throw new Exception("Required fields cannot be empty");
        }
         // Additional validation
         if (strlen($institution) > 100) {
            throw new Exception("Name must be less than 100 characters");
        }
        if (strlen($degree) > 200) {
            throw new Exception("Title must be less than 200 characters");
        }
        if (strlen($field) > 200) {
            throw new Exception("Title must be less than 200 characters");
        }
        if (strlen($start_date) > 200) {
            throw new Exception("Title must be less than 200 characters");
        }
        if (strlen($end_date) > 200) {
            throw new Exception("Title must be less than 200 characters");
        }
        if (strlen($description) > 1000) {
            throw new Exception("Bio must be less than 1000 characters");
        }

        $stmt = $pdo->prepare("INSERT INTO education (institution, degree, field, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$institution, $degree, $field, $start_date, $end_date, $description]);
        $message = "Education information added successfully!";
        $_POST = array();
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Education</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Copy the same styles from about.php -->
    <style>
        /* ... existing styles from about.php ... */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-graduation-cap"></i> Add Education</h1>
            <a href="index.php" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if ($message): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Education</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a202c;
            --secondary-color: #2d3748;
            --accent-color: #4299e1;
            --danger-color: #f56565;
            --success-color: #48bb78;
            --text-color: #e2e8f0;
            --border-color: #4a5568;
            --hover-color: #3182ce;
        }

        body {
            background-color: var(--primary-color);
            color: var(--text-color);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.5;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--secondary-color);
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-card {
            background: var(--secondary-color);
            padding: 2rem;
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-color);
        }

        input, textarea {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary-color);
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            color: var(--text-color);
            font-size: 0.975rem;
            transition: all 0.2s;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.2);
        }

        .help-text {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #718096;
        }

        .message {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .success {
            background: var(--success-color);
            color: #fff;
        }

        .error {
            background: var(--danger-color);
            color: #fff;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        button, .btn-secondary {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            font-size: 0.975rem;
        }

        button {
            background: var(--accent-color);
            color: white;
        }

        button:hover {
            background: var(--hover-color);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--secondary-color);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: #4a5568;
            transform: translateY(-1px);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        input[type="date"] {
            color-scheme: dark;
        }
    </style>
</head>
<body>
        <form method="POST" class="form-card">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="institution">Institution:</label>
                <input type="text" id="institution" name="institution" required 
                       value="<?php echo isset($_POST['institution']) ? htmlspecialchars($_POST['institution']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="degree">Degree:</label>
                <input type="text" id="degree" name="degree" required 
                       value="<?php echo isset($_POST['degree']) ? htmlspecialchars($_POST['degree']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="field">Field of Study:</label>
                <input type="text" id="field" name="field" required 
                       value="<?php echo isset($_POST['field']) ? htmlspecialchars($_POST['field']) : ''; ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required 
                           value="<?php echo isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" 
                           value="<?php echo isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : ''; ?>">
                    <small class="help-text">Leave empty if currently studying</small>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>

            <div class="btn-group">
                <button type="submit">
                    <i class="fas fa-save"></i> Save Education
                </button>
                <button type="reset" class="btn-secondary">
                    <i class="fas fa-undo"></i> Reset Form
                </button>
            </div>
        </form>
    </div>
</body>
</html>