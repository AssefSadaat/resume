<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db_connect.php';
require_once '../auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid ID provided');
        }

        $stmt = $pdo->prepare("DELETE FROM education WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['success_message'] = "Education entry deleted successfully";
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
}

header('Location: list.php');
exit();