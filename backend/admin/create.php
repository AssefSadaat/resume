<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db_connect.php';
require_once '../auth.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ../login.php');
    exit();
}

// Fetch all education entries
try {
    $stmt = $pdo->query("SELECT * FROM education ORDER BY start_date DESC");
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Error fetching education entries: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Education Entries | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Add your CSS here -->
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-graduation-cap"></i> Education Entries</h1>
            <div class="button-group">
                <a href="../index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <a href="create.php" class="btn">
                    <i class="fas fa-plus"></i> Add New Entry
                </a>
            </div>
        </div>

        <div class="entries-grid">
            <?php if (!empty($entries)): foreach ($entries as $entry): ?>
                <div class="entry-card">
                    <h3><?php echo htmlspecialchars($entry['institution']); ?></h3>
                    <p><?php echo htmlspecialchars($entry['degree']); ?></p>
                    <p><?php echo htmlspecialchars($entry['field']); ?></p>
                    <p>
                        <?php echo date('M Y', strtotime($entry['start_date'])); ?> - 
                        <?php echo $entry['end_date'] ? date('M Y', strtotime($entry['end_date'])) : 'Present'; ?>
                    </p>
                    <div class="button-group">
                        <a href="edit.php?id=<?php echo $entry['id']; ?>" class="btn btn-secondary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="delete.php?id=<?php echo $entry['id']; ?>" class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this entry?')">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <p class="no-entries">No entries found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>