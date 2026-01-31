<?php
require_once("db.php");
session_start();

function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function displayRecipes($recipes, $user_id){
    foreach ($recipes as $recipe): ?>
        <div class="list-recipe" 
             data-recipe='<?= json_encode($recipe) ?>' 
             data-creation-time="<?= $recipe['creation_time'] ?>">
            
            <div class="recipe-title"><?= htmlspecialchars($recipe['recipe_name']) ?></div>
            <div class="recipe-details">
                <?php if ($recipe['creator_id'] != $user_id): ?>
                    Creator: <?= htmlspecialchars($recipe['username']) ?><br />
                <?php endif; ?>
                Created: <?= htmlspecialchars($recipe['creation_time']) ?><br />
                Last Note: <?= $recipe['last_note_date'] ? htmlspecialchars($recipe['last_note_date']) : 'No notes yet' ?><br />
                Notes: <?= isset($recipe['note_count']) ? $recipe['note_count'] : 0 ?><br />
            </div>
            <?php if ($recipe['creator_id'] == $user_id): ?>
                <a href="access.php?id=<?= urlencode($recipe['recipe_id']) ?>" class="btn recipe-access">
                    Access
                </a>
                <a href="view-recipe.php?id=<?= urlencode($recipe['recipe_id']) ?>" class="btn recipe-access">
                    View
                </a>
            <?php else: ?>
                <a href="view-recipe.php?id=<?= urlencode($recipe['recipe_id']) ?>" class="btn recipe-view">
                    View
                </a>
            <?php endif; ?>
        </div>
    <?php endforeach;
}
?>

<?php
// Check whether the user has logged in or not.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION["username"];
$photo = $_SESSION["photo_url"];
$user_id = $_SESSION["user_id"]; 

$errors = array();

$recipeQuery = "SELECT recipe.recipe_id, recipe.recipe_name, recipe.creation_time, 
            recipe.creation_time, recipe.creator_id, users.username,
            (SELECT COUNT(*) FROM note WHERE note.recipe_id = recipe.recipe_id) as note_count,
            (SELECT MAX(note.creation) FROM note WHERE note.recipe_id = recipe.recipe_id) as last_note_date
            FROM recipe
            JOIN recipe_access ON recipe.recipe_id = recipe_access.recipe_id
            JOIN users ON recipe.creator_id = users.user_id
            WHERE recipe_access.user_id = ? AND recipe_access.access_status = 1 AND creation_time > ?
            ORDER BY recipe.creation_time DESC";

if(isset($_GET['ajax']) && $_GET['ajax'] == 'new_recipes'){
    header('Content-Type: application/json');

    // Check for records in the last 90 seconds
    $sinceTime = isset($_GET['since']) ? $_GET['since'] : null;

    if(!$sinceTime){
        echo json_encode(['error' => 'Missing the sinceTime parameter']);
        exit();
    }
// Connect to the database and verify the connection
    try {
        $db = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
        if ($sinceTime) {
            // Get only new records since the last check
            $newCheck = $db->prepare($recipeQuery);
            $newCheck->execute([$user_id, $sinceTime]);
            $newRecipes = $newCheck->fetchAll();
        }
        echo json_encode(['success' => true, 'new_recipes' => $newRecipes, 'count' => count($newRecipes)]);
        exit();

    }
    catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        exit();
    }


}

try{
    $db = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    //Create an empty array                       
    $allRecipes = array();
    //get the list of the recipes the user has access to, creation_time set to before the site was live.
    $q = $db->prepare($recipeQuery);
    $q->execute([$user_id, '2024-01-15 00:00:00']);
    while($recipe = $q->fetch()){
        $allRecipes[] = $recipe;
    }

}
catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

?>

<!Doctype html>
<html lang="en-us">
    <head>
        <title>Recipe Box</title>
        <meta name="referrer" content="unsafe-url" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
        <script type="text/javascript" src="js/eventHandlers.js"></script>
        <meta charset="utf-8" />
    </head>
    <body class="bg-primary list">
        <div class="page-grid">
            <div></div>
            <div class="list-header">
                <div class="section-title">
                    <?=$username?>'s Recipe Box
                    <?php if ($errors): ?>
                        <div class="error">
                            Error: <?= htmlspecialchars($errors) ?> </br>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="list-pic">
                <img src="<?=$photo?>" class="profile-pic" alt="picture of <?=$username?>"/>
            </div>
            <div class="left-aside"></div>
            <div class="list-main" id="list-main">
                <a href="create.php" class="btn create-btn">
                    <span>Create a new recipe</span>
                    <i class="fa-solid fa-square-plus fa-2xl plus-sign"></i>
                </a>
                <?php displayRecipes($allRecipes, $user_id); ?>
            </div>
            <div class="right-aside"></div>
            <div class="footer-left">
                <a href="logout.php">Log out</a>
            </div>
            <div class="footer-center">
                <div class="status" id="statusBar">
                Last updated: <span id="lastUpdated"><?= date('M j, Y g:i:s A') ?></span>
                </div>
            </div>
            <div class="footer-right"></div>
        </div>
        <script>
            let totalRecipeCount = <?= count($allRecipes) ?>;
            let currentUserId = <?= $user_id ?>;
        </script>
        <script type="text/javascript" src="js/eventRecipeList.js"></script>
    </body>
</html>