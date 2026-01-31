<?php
require_once("db.php");
session_start();
function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
// Check whether the user has logged in or not.
if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}
$user_id = $_SESSION["user_id"];
// Check if recipe ID is provided
if (!isset($_GET['recipe_id']) || empty($_GET['recipe_id'])) {
    echo json_encode(['success' => false, 'error' => 'No recipe ID provided']);
    exit();
}
$recipe_id = (int)$_GET['recipe_id'];
try {
    $db = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}
// Check if user has access to this recipe
$accessQuery = $db->prepare("SELECT 1 FROM recipe_access 
                            WHERE recipe_id = ? AND user_id = ? AND access_status = 1");
$accessQuery->execute([$recipe_id, $user_id]);
if (!$accessQuery->fetchColumn()) {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit();
}
if (isset($_POST['add-note'])) {
    $note_text = test_input($_POST['add-note']);
    
    if (!empty($note_text) && strlen($note_text) <= 1300) {
        $insertNote = $db->prepare("INSERT INTO note (recipe_id, note_creator_id, content, creation) 
                                   VALUES (?, ?, ?, NOW())");
        $result = $insertNote->execute([$recipe_id, $user_id, $note_text]);
        
        if ($result) {
            // Get the new note data
            $newNoteId = $db->lastInsertId();
            $newNoteQuery = $db->prepare("SELECT note.content, note.creation, users.username, users.photo_url
                                         FROM note
                                         JOIN users ON note.note_creator_id = users.user_id
                                         WHERE note.note_id = ?");
            $newNoteQuery->execute([$newNoteId]);
            $newNote = $newNoteQuery->fetch();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'newNote' => [
                    'content' => $newNote['content'],
                    'author' => $newNote['username'],
                    'timestamp' => $newNote['creation'],
                    'photo_url' => $newNote['photo_url']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to add note']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Note must be between 1 and 1300 characters']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
$db = null;
?>