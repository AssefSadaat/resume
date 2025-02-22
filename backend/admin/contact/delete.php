<?php
require_once '../auth.php';
require_once '../db_connect.php';

try {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        throw new Exception('No ID provided');
    }

    $stmt = $pdo->prepare("DELETE FROM contact_info WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['message'] = "Contact deleted successfully";
    header('Location: contact.php');
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: contact.php');
    exit;
}