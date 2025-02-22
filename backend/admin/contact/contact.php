<?php
require_once '../auth.php';
require_once '../db_connect.php';

$message = '';
$error = '';

// Add this after the error variable declaration
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $type = $_POST['type'] ?? '';
        $value = $_POST['value'] ?? '';
        $icon = $_POST['icon'] ?? '';
        $link = $_POST['link'] ?? '';
        $order_index = $_POST['order_index'] ?? 0;

        if (empty($type) || empty($value) || empty($icon)) {
            throw new Exception("All fields are required");
        }

        $stmt = $pdo->prepare("INSERT INTO contact_info (type, value, icon, link, order_index) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$type, $value, $icon, $link, $order_index]);

        $message = "Contact information added successfully!";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$contacts = $pdo->query("SELECT * FROM contact_info ORDER BY COALESCE(order_index, id)")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Contacts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #00b4b3;
            --dark: #1f2937;
            --darker: #111827;
            --light: #f3f4f6;
            --danger: #ef4444;
            --success: #10b981;
        }

        body {
            background: var(--darker);
            color: var(--light);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
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
            font-size: 1.875rem;
            font-weight: 600;
            color: white;
            margin: 0;
        }

        .card {
            background: var(--dark);
            border-radius: 0.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #e5e7eb;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-size: 1rem;
        }

        .form-group small {
            display: block;
            margin-top: 0.5rem;
            color: #9ca3af;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            color: #e5e7eb;
            font-weight: 500;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #9ca3af;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert.success {
            background: var(--success);
            color: white;
        }

        .alert.error {
            background: var(--danger);
            color: white;
        }

        .icon-preview {
            font-size: 1.25rem;
            color: var(--primary);
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        a {
            color: white;
        }

        // Add to the existing styles in contact.php and edit.php
        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            background: #009e9d;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .btn-danger:hover {
            background: #dc2626;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Contact Management</h1>
            <a href="../index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if ($message): ?>
            <div class="alert success">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" class="form">
                <div class="form-group">
                    <label for="type">Contact Type</label>
                    <input type="text" id="type" name="type" required placeholder="e.g., Phone, Email, WhatsApp">
                </div>

                <div class="form-group">
                    <label for="value">Contact Value</label>
                    <input type="text" id="value" name="value" required
                        placeholder="e.g., +1234567890, example@email.com">
                </div>

                <div class="form-group">
                    <label for="icon">Icon Class</label>
                    <input type="text" id="icon" name="icon" required placeholder="fas fa-phone">
                    <small>Available icons: <a href="https://fontawesome.com/icons" target="_blank">Font Awesome
                            Icons</a></small>
                </div>

                <div class="form-group">
                    <label for="link">Contact Link</label>
                    <input type="text" id="link" name="link" placeholder="tel:+1234567890">
                    <small>Format: tel:number, mailto:email, https://wa.me/number</small>
                </div>

                <div class="form-group">
                    <label for="order_index">Display Order</label>
                    <input type="number" id="order_index" name="order_index" value="0" min="0">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Contact
                </button>
            </form>
        </div>

        <div class="card">
            <h2>Contact List</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Icon</th>
                            <th>Link</th>
                            <th>Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($contact['type']); ?></td>
                                <td><?php echo htmlspecialchars($contact['value']); ?></td>
                                <td class="icon-preview"><i class="<?php echo htmlspecialchars($contact['icon']); ?>"></i>
                                </td>
                                <td><?php echo htmlspecialchars($contact['link']); ?></td>
                                <td><?php echo htmlspecialchars($contact['order_index']); ?></td>
                                <td class="actions">
                                    <a href="edit.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this contact?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>