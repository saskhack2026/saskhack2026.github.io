<?php
require_once("db.php");
session_start();

header('Content-Type: application/json');

// Simple test
echo json_encode([
    'success' => true,
    'message' => 'AJAX file is working',
    'user_id' => $_SESSION['user_id'] ?? 'not logged in',
    'recipe_id' => $_GET['recipe_id'] ?? 'no recipe id',
    'post_data' => $_POST
]);
?>