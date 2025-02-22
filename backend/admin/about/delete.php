<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db_connect.php';
require_once '../auth.php';

// Check if user is logged in
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate ID
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid ID provided');
        }

        // Check if entry exists
        $stmt = $pdo->prepare("SELECT id FROM about WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            throw new Exception('Entry not found');
        }

        // Delete the entry
        $stmt = $pdo->prepare("DELETE FROM about WHERE id = ?");
        $stmt->execute([$id]);

        // Set success message
        $_SESSION['success_message'] = "Entry deleted successfully";
    } catch (Exception $e) {
        // Set error message
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
}

// Redirect back to list page
header('Location: list.php');
exit();