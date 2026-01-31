<?php
require_once("db.php");
session_start();
function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
$errors = array();
// Check whether the user has logged in or not.
if (!isset($_SESSION["user_id"])) {
    header("Location: loginote.php");
    exit();
}
$username = $_SESSION["username"];
$photo = $_SESSION["photo_url"];
$user_id = $_SESSION["user_id"];
// Check if recipe ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php");
    exit();
}
$recipe_id = (int)$_GET['id'];
try {
    $db = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
// Check if user has access to this recipe
$accessQuery = $db->prepare("SELECT 1 FROM recipe_access 
                            WHERE recipe_id = ? AND user_id = ? AND access_status = 1");
$accessQuery->execute([$recipe_id, $user_id]);
if (!$accessQuery->fetchColumn()) {
    header("Location: list.php");
    exit();
}
// Get recipe details
$recipeQuery = $db->prepare("SELECT recipe.recipe_id, recipe.recipe_name, recipe.creation_time, recipe.creator_id,
                                   users.username as creator_username, users.photo_url as creator_photo
                            FROM recipe
                            JOIN users ON recipe.creator_id = users.user_id
                            WHERE recipe.recipe_id = ?");
$recipeQuery->execute([$recipe_id]);
$recipe = $recipeQuery->fetch();
if (!$recipe) {
    header("Location: list.php");
    exit();
}
// Get existing notes for this recipe
$notesQuery = $db->prepare("SELECT note.note_id, note.content, note.creation, users.username, users.photo_url
                           FROM note
                           JOIN users ON note.note_creator_id = users.user_id
                           WHERE note.recipe_id = ?
                           ORDER BY note.creation ASC");
$notesQuery->execute([$recipe_id]);
$notes = $notesQuery->fetchAll();
$db = null;
?>
<!Doctype html>
<html lang="en-us">
    <head>
        <title>Recipe Box - <?= htmlspecialchars($recipe['recipe_name']) ?></title>
        <meta name="referrer" content="unsafe-url" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <meta charset="utf-8" />
        <script type="text/javascript" src="js/eventHandlers.js"></script>
    </head>
    <body class="bg-primary">
        <div class="view-recipe-grid">
            <div class="bg-secondary view-recipe-card">
                <div></div>
                <div class="created-recipe-header">
                    <div class="creator-pic">
                        <img src="<?= htmlspecialchars($recipe['creator_photo']) ?>" class="profile-pic"
                            alt="profile picture of <?= htmlspecialchars($recipe['creator_username']) ?>" />
                    </div>
                    <div class="created-details">
                        <ul>
                            <li>Creator: <?= $recipe['creator_id'] == $user_id ? 'You' : htmlspecialchars($recipe['creator_username']) ?></li>
                            <li>Created: <?= htmlspecialchars($recipe['creation_time']) ?></li>
                        </ul>
                    </div>
                    <div class="recipe-title"><?= htmlspecialchars($recipe['recipe_name']) ?></div>
                </div>
                
                <div class="recipe">
                    <div class="recipe-ingredients">
                        <div class="recipe-section-title">Ingredients</div>
                        <div class="ingredients-content">
                            <p>Ingredients will be added later</p>
                        </div>
                    </div>
                    <div class="recipe-steps">
                        <div class="recipe-section-title">Steps</div>
                        <div class="steps-content">
                            <p>Recipe steps will be added later</p>
                        </div>
                    </div>
                </div>
                <div></div>
                <div class="recipe-notes-section">
                    <?php foreach ($notes as $note): ?>
                    <div class="note-container">
                        <div class="note-profile">
                            <img src="<?= htmlspecialchars($note['photo_url']) ?>" class="profile-pic" 
                                alt="profile picture of <?= htmlspecialchars($note['username']) ?>" />
                            <div class="created-details">
                                <ul>
                                    <li><?= htmlspecialchars($note['username']) ?></li>
                                    <li><?= htmlspecialchars($note['creation']) ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="note">
                            <?= nl2br(htmlspecialchars($note['content'])) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="add-note">
                        <form method="post" action="" id="note-form">
                            <label for="add-note">Add a new note</label>
                            <textarea name="add-note" id="add-note" rows="5"></textarea>
                            <p id="charNum">1300 characters remaining</p>
                            <div id="err-note" class="err-msg hidden"></div>
                            <button type="submit" class="btn"><span>Submit</span></button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="footer-left"><a href="list.php">Return to Recipe List</a></div>
            <div class="footer-center"></div>
            <div class="footer-right"><a href="logout.php">Log out</a></div>
        <script>
            let recipePage = <?= $recipe_id ?>;
        </script>
        <script type="text/javascript" src="js/eventNote.js"></script>
    </body>
</html>