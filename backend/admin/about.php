<?php
require_once 'db_connect.php';
require_once 'auth.php';

// Require login
requireLogin();

$message = '';
$error = '';

function validateInput($data) {
    return htmlspecialchars(trim($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    validateCSRFToken();

    try {
        // Validate and sanitize input
        $name = validateInput($_POST['name']);
        $title = validateInput($_POST['title']);
        $bio = validateInput($_POST['bio']);

        // Validate required fields
        if (empty($name) || empty($title) || empty($bio)) {
            throw new Exception("All fields are required");
        }

        // Additional validation
        if (strlen($name) > 100) {
            throw new Exception("Name must be less than 100 characters");
        }
        if (strlen($title) > 200) {
            throw new Exception("Title must be less than 200 characters");
        }
        if (strlen($bio) > 1000) {
            throw new Exception("Bio must be less than 1000 characters");
        }

        $stmt = $pdo->prepare("INSERT INTO about (name, title, bio, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$name, $title, $bio]);
        $message = "About information added successfully!";
        
        // Clear form after successful submission
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
    <title>Add About Information</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--primary-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-card {
            background: var(--secondary-color);
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 2rem;
        }

        label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 500;
            color: #fff;
            font-size: 0.95rem;
        }

        input, textarea {
            width: 100%;
            padding: 0.875rem 1rem;
            background: var(--primary-color);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            color: #fff;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(0, 178, 255, 0.1);
        }

        .char-count {
            font-size: 0.8rem;
            color: #6b7280;
            text-align: right;
            margin-top: 0.5rem;
        }

        .message {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
        }

        .success {
            background: rgba(0, 178, 255, 0.1);
            border: 1px solid var(--success-color);
            color: var(--success-color);
        }

        .error {
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid var(--error-color);
            color: var(--error-color);
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2.5rem;
        }

        button {
            padding: 0.875rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        button[type="submit"] {
            background: var(--accent-color);
            color: #fff;
            border: none;
        }

        button[type="submit"]:hover {
            background: #0099dd;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: transparent;
            color: #fff;
            border: 2px solid var(--border-color);
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background: var(--border-color);
            transform: translateY(-1px);
        }

        button[type="reset"] {
            background: transparent;
            border: 2px solid var(--border-color);
            color: #fff;
        }

        button[type="reset"]:hover {
            background: var(--border-color);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-plus"></i> Add About Information</h1>
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

        <form method="POST" class="form-card">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" 
                       maxlength="100" required 
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                       onkeyup="updateCharCount(this, 'nameCount')">
                <div class="char-count" id="nameCount">0/100</div>
            </div>

            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" 
                       maxlength="200" required
                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                       onkeyup="updateCharCount(this, 'titleCount')">
                <div class="char-count" id="titleCount">0/200</div>
            </div>

            <div class="form-group">
                <label for="bio">Bio:</label>
                <textarea id="bio" name="bio" rows="5" required
                          maxlength="1000"
                          onkeyup="updateCharCount(this, 'bioCount')"><?php echo isset($_POST['bio']) ? htmlspecialchars($_POST['bio']) : ''; ?></textarea>
                <div class="char-count" id="bioCount">0/1000</div>
            </div>

            <div class="btn-group">
                <button type="submit">
                    <i class="fas fa-save"></i> Save Information
                </button>
                <button type="reset" class="btn-secondary">
                    <i class="fas fa-undo"></i> Reset Form
                </button>
            </div>
        </form>
    </div>

    <script>
        function updateCharCount(input, counterId) {
            const maxLength = input.getAttribute('maxlength');
            const currentLength = input.value.length;
            document.getElementById(counterId).textContent = `${currentLength}/${maxLength}`;
        }

        // Initialize character counts
        document.addEventListener('DOMContentLoaded', function() {
            updateCharCount(document.getElementById('name'), 'nameCount');
            updateCharCount(document.getElementById('title'), 'titleCount');
            updateCharCount(document.getElementById('bio'), 'bioCount');
        });
    </script>
</body>
</html>