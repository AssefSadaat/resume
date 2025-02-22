<?php
require_once 'db_connect.php';
require_once 'auth.php';

requireLogin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRFToken();

    try {
        // Validate and sanitize input
        $company = htmlspecialchars(trim($_POST['company']));
        $position = htmlspecialchars(trim($_POST['position']));
        $start_date = $_POST['start_date'];
        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        $responsibilities = htmlspecialchars(trim($_POST['responsibilities']));
        $technologies = htmlspecialchars(trim($_POST['technologies']));

        // Validate required fields
        if (empty($company) || empty($position) || empty($start_date) || empty($responsibilities)) {
            throw new Exception("All required fields must be filled out");
        }

        // Insert into database
        $sql = "INSERT INTO experience (
            company, 
            position, 
            start_date, 
            end_date, 
            responsibilities, 
            technologies, 
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $company,
            $position,
            $start_date,
            $end_date,
            $responsibilities,
            $technologies
        ]);

        $message = "Experience added successfully!";
        
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
    <title>Add Experience</title>
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

        .form-card {
            background: var(--secondary-color);
            padding: 2rem;
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 500;
        }

        input, textarea {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary-color);
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            color: var(--text-color);
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
            color: #718096;
            font-size: 0.875rem;
        }

        .message {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
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

        button {
            padding: 0.75rem 1.5rem;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        button:hover {
            background: var(--hover-color);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--secondary-color);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: #4a5568;
        }

        .technologies-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: var(--primary-color);
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
        }

        .tech-tag {
            background: var(--accent-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tech-tag .remove {
            cursor: pointer;
            opacity: 0.75;
            transition: opacity 0.2s;
        }

        .tech-tag .remove:hover {
            opacity: 1;
        }
        a{
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-briefcase"></i> Add Experience</h1>
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
            
            <div class="form-row">
                <div class="form-group">
                    <label for="company">Company:</label>
                    <input type="text" id="company" name="company" required 
                           value="<?php echo isset($_POST['company']) ? htmlspecialchars($_POST['company']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="position">Position:</label>
                    <input type="text" id="position" name="position" required 
                           value="<?php echo isset($_POST['position']) ? htmlspecialchars($_POST['position']) : ''; ?>">
                </div>
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
                    <small class="help-text">Leave empty if currently employed</small>
                </div>
            </div>

            <div class="form-group">
                <label for="responsibilities">Responsibilities:</label>
                <textarea id="responsibilities" name="responsibilities" rows="4" required
                          placeholder="List your key responsibilities..."><?php echo isset($_POST['responsibilities']) ? htmlspecialchars($_POST['responsibilities']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="technologies">Technologies:</label>
                <input type="text" id="tech-input" placeholder="Type and press Enter to add technologies">
                <div id="technologies-container" class="technologies-container"></div>
                <input type="hidden" id="technologies" name="technologies" 
                       value="<?php echo isset($_POST['technologies']) ? htmlspecialchars($_POST['technologies']) : ''; ?>">
            </div>

            <div class="btn-group">
                <button type="submit">
                    <i class="fas fa-save"></i> Save Experience
                </button>
                <button type="reset" class="btn-secondary">
                    <i class="fas fa-undo"></i> Reset Form
                </button>
            </div>
        </form>
    </div>

    <script>
        const techInput = document.getElementById('tech-input');
        const techsContainer = document.getElementById('technologies-container');
        const techsHiddenInput = document.getElementById('technologies');
        let techs = [];

        techInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const tech = this.value.trim();
                if (tech && !techs.includes(tech)) {
                    techs.push(tech);
                    updateTechs();
                }
                this.value = '';
            }
        });

        function updateTechs() {
            techsContainer.innerHTML = techs.map(tech => `
                <span class="tech-tag">
                    ${tech}
                    <span class="remove" onclick="removeTech('${tech}')">&times;</span>
                </span>
            `).join('');
            techsHiddenInput.value = techs.join(', ');
        }

        function removeTech(tech) {
            techs = techs.filter(t => t !== tech);
            updateTechs();
        }
    </script>
</body>
</html>