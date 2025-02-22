<?php
require_once '../auth.php';
require_once '../db_connect.php';

$message = '';
$error = '';
$contact = null;

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        $type = $_POST['type'] ?? '';
        $value = $_POST['value'] ?? '';
        $icon = $_POST['icon'] ?? '';
        $link = $_POST['link'] ?? '';
        $order_index = $_POST['order_index'] ?? 0;

        if (empty($type) || empty($value) || empty($icon)) {
            throw new Exception("All fields are required");
        }

        $stmt = $pdo->prepare("UPDATE contact_info SET type = ?, value = ?, icon = ?, link = ?, order_index = ? WHERE id = ?");
        $stmt->execute([$type, $value, $icon, $link, $order_index, $id]);

        $_SESSION['message'] = "Contact updated successfully";
        header('Location: contact.php');
        exit;
    }

    // Get contact data for editing
    $id = $_GET['id'] ?? null;
    if (!$id) {
        throw new Exception('No ID provided');
    }

    $stmt = $pdo->prepare("SELECT * FROM contact_info WHERE id = ?");
    $stmt->execute([$id]);
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$contact) {
        throw new Exception('Contact not found');
    }

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Contact</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #00b4b3;
            --dark: #1f2937;
            --darker: #111827;
            --light: #f3f4f6;
            --gray: #6b7280;
            --success: #10b981;
            --danger: #ef4444;
        }

        body {
            background: var(--darker);
            color: var(--light);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            margin: 0;
        }

        .container {
            max-width: 768px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .card {
            background: var(--dark);
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: var(--light);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            color: white;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(0, 180, 179, 0.25);
        }

        .form-group small {
            display: block;
            margin-top: 0.5rem;
            color: var(--gray);
            font-size: 0.75rem;
        }

        .form-group small a {
            color: var(--primary);
            text-decoration: none;
        }

        .form-group small a:hover {
            text-decoration: underline;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
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
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #009e9d;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert.error {
            background: var(--danger);
            color: white;
        }

        @media (max-width: 640px) {
            .container {
                padding: 1rem;
            }

            .card {
                padding: 1.5rem;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<div class="container">
    <div class="header">
        <h1>Edit Contact Information</h1>
        <a href="contact.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert error">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($contact): ?>
        <div class="card">
            <form method="POST" class="form">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($contact['id']); ?>">

                <div class="form-group">
                    <label for="type">Contact Type</label>
                    <input type="text" id="type" name="type" required 
                           value="<?php echo htmlspecialchars($contact['type']); ?>"
                           placeholder="e.g., Phone, Email, WhatsApp">
                </div>

                <div class="form-group">
                    <label for="value">Contact Value</label>
                    <input type="text" id="value" name="value" required 
                           value="<?php echo htmlspecialchars($contact['value']); ?>"
                           placeholder="e.g., +1234567890, example@email.com">
                </div>

                <div class="form-group">
                    <label for="icon">Icon Class</label>
                    <input type="text" id="icon" name="icon" required 
                           value="<?php echo htmlspecialchars($contact['icon']); ?>"
                           placeholder="fas fa-phone">
                    <small>Need icons? Browse <a href="https://fontawesome.com/icons" target="_blank">Font Awesome Icons →</a></small>
                </div>

                <div class="form-group">
                    <label for="link">Contact Link</label>
                    <input type="text" id="link" name="link" 
                           value="<?php echo htmlspecialchars($contact['link']); ?>"
                           placeholder="tel:+1234567890">
                    <small>Format examples: tel:+1234567890, mailto:email@example.com, https://wa.me/1234567890</small>
                </div>

                <div class="form-group">
                    <label for="order_index">Display Order</label>
                    <input type="number" id="order_index" name="order_index" 
                           value="<?php echo htmlspecialchars($contact['order_index']); ?>" min="0">
                    <small>Lower numbers appear first</small>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="contact.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>
</body>
</html>