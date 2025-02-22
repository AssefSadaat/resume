<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db_connect.php';
require_once '../auth.php';

requireLogin();

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
    <!-- Include your CSS styles here -->
</head>
<body>
    <div class="dashboard">
        <div class="header">
            <div class="header-left">
                <h1><i class="fas fa-graduation-cap"></i> Education Entries</h1>
                <a href="../index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Entry
            </a>
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
            <?php if (!empty($entries)): foreach ($entries as $entry): ?>
                <div class="entry-card">
                    <h3><?php echo htmlspecialchars($entry['institution']); ?></h3>
                    <p class="degree"><?php echo htmlspecialchars($entry['degree']); ?></p>
                    <p class="field"><?php echo htmlspecialchars($entry['field']); ?></p>
                    <p class="date">
                        <?php echo date('M Y', strtotime($entry['start_date'])); ?> - 
                        <?php echo $entry['end_date'] ? date('M Y', strtotime($entry['end_date'])) : 'Present'; ?>
                    </p>
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
                <p class="no-entries">No education entries found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>