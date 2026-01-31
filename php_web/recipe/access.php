<?php
require_once("db.php");
session_start();

function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$errors = array();
$success_msg = "";

// Check whether the user has logged in or not.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
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

// Connect to the database
try {
    $db = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("PDO Connect: " . $e->getMessage() . "\n<br />");
}

// Get recipe details and verify that the user is the creator
$recipeQuery = $db->prepare("SELECT recipe_name, creator_id FROM recipe WHERE recipe_id = ?");
$recipeQuery->execute([$recipe_id]);
$recipe = $recipeQuery->fetch();

if (!$recipe) {
    header("Location: list.php");
    exit();
}

// Check if current user is the creator of this recipe
if ($recipe['creator_id'] != $user_id) {
    header("Location: list.php");
    exit();
}

// Get all users and their current access status for this recipe
$usersQuery = $db->prepare("SELECT users.user_id, users.username, users.photo_url, recipe_access.access_status
                           FROM users
                           JOIN recipe_access ON users.user_id = recipe_access.user_id
                           WHERE recipe_access.recipe_id = ? AND users.user_id != ?
                           ORDER BY users.username");
$usersQuery->execute([$recipe_id, $user_id]); // Exclude the recipe creator
$allUsers = $usersQuery->fetchAll();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_users = isset($_POST['access']) ? $_POST['access'] : []; // Array of selected user IDs
    
    try {
        $db->beginTransaction();
        
        // Update access for all users (except the creator)
        foreach ($allUsers as $user) {
            $target_user_id = $user['user_id'];
            $new_access_status = in_array($target_user_id, $selected_users) ? 1 : 0;
            
            // Check if record exists
            $checkQuery = $db->prepare("SELECT access_id FROM recipe_access WHERE recipe_id = ? AND user_id = ?");
            $checkQuery->execute([$recipe_id, $target_user_id]);
            $existing_record = $checkQuery->fetch();
            
            if ($existing_record) {
                // Update existing record
                $updateQuery = $db->prepare("UPDATE recipe_access SET access_status = ? WHERE recipe_id = ? AND user_id = ?");
                $updateQuery->execute([$new_access_status, $recipe_id, $target_user_id]);
            } else {
                // Insert new record
                $insertQuery = $db->prepare("INSERT INTO recipe_access (recipe_id, user_id, access_status) VALUES (?, ?, ?)");
                $insertQuery->execute([$recipe_id, $target_user_id, $new_access_status]);
            }
        }
        
        $db->commit();
        $success_msg = "Access permissions updated successfully!";
        
        // REload the user data to show updated access
        $usersQuery->execute([$recipe_id, $user_id]);
        $allUsers = $usersQuery->fetchAll();
        
    } catch (Exception $e) {
        $db->rollback();
        $errors['general'] = "Failed to update access permissions. Please try again.";
    }
}

$db = null;
?>

<!Doctype html>
<html lang="en-us">
    <head>
        <title>Recipe Box - Access Control</title>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
    </head>
    <body class="bg-primary">
        <div class="access">
            <div class="page-grid">
                <div></div>
                <div class="header">
                    <div class="section-title">
                        <?=$username ?>'s Recipe Box Access
                    </div>
                </div>
                <div></div>
                <div class="left-aside"></div>
                <div class="main access-main">
                    
                    <?php if (!empty($success_msg)): ?>
                        <div class="success-msg">
                            <?= $success_msg ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($errors['general'])): ?>
                        <div class="err-msg">
                            <?= $errors['general'] ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="" method="post" id="access">
                        <fieldset>
                            <legend>Change Access for <?= htmlspecialchars($recipe['recipe_name']) ?> Recipe:</legend>
                            
                            <?php foreach ($allUsers as $user): ?>
                            <div class="friend">
                                <img src="<?= htmlspecialchars($user['photo_url']) ?>" class="profile-pic" 
                                    alt="profile pic of <?= htmlspecialchars($user['username']) ?>" />
                                <label for="user_<?= $user['user_id'] ?>"><?= htmlspecialchars($user['username']) ?></label>
                                <input type="checkbox" 
                                    <?= $user['access_status'] == 1 ? 'checked="checked"' : '' ?>
                                    name="access[]" 
                                    id="user_<?= $user['user_id'] ?>" 
                                    value="<?= $user['user_id'] ?>"
                                    class="access-checkbox" />
                            </div>
                            <?php endforeach; ?>
                            
                            <?php if (empty($allUsers)): ?>
                                <p class ="err-msg">
                                    No other users found in the system.
                                </p>
                            <?php endif; ?>
                            
                            <button type="submit" class="btn"><span>Submit Changes</span></button>
                        </fieldset>
                    </form>
                </div>
                <div class="right-aside"></div>
                <div class="footer-left">
                    <a href="view-recipe.php?id=<?= $recipe_id ?>">Return to Recipe</a>
                </div>
                <div class="footer-center">
                    <a href="list.php">Return to Recipe List</a>
                </div>
                <div class="footer-right">
                    <a href="logout.php">Log out</a>
                </div>
            </div>
        </div>
    </body>
</html>