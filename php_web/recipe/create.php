<?php
require_once("db.php");
session_start();

function test_input($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

$errors = [];

// Check whether the user has logged in or not.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
} else {
    $username = $_SESSION["username"];
    $photo = $_SESSION["photo_url"];
    $user_id = $_SESSION["user_id"];
}
// Check whether the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dataOK = true;
    $recipe_name = test_input($_POST["name"]);
    $recipeRegex = '/^[^!@#$%^*()]+$/';
    if (empty($recipe_name) || strlen($recipe_name) == 0) {
        $errors["name"] = "Recipe name is required.";
        $dataOK = false;
    } else {
        if (strlen($recipe_name) > 256) {
            $errors["name"] = "Recipe name must be 256 or fewer characters.";
            $dataOK = false;
        }
        if (!preg_match($recipeRegex, $recipe_name)) {
            $errors["name"] .=
                "Recipe name must not include the characters !@#$%^*()";
            $dataOK = false;
        }
    }
    // Check whether the fields are not empty
    if ($dataOK) {
        // Connect to the database and verify the connection
        try {
            $db = new PDO(
                "mysql:host=$dbHost;dbname=$dbName",
                $dbUser,
                $dbPass
            );
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
        die("PDO Connect: " . $e->getMessage() . "\n<br />");
    }

        // check if that recipe name exists already
        $q = $db->prepare("SELECT 1 FROM recipe WHERE recipe_name = ?");
        $q->execute([$recipe_name]);
        $result = $q->fetchColumn();
        if ($result) {
            $errors["name"] = "Recipe name $recipe_name already exists.";
        }
    }
    // insert the recipe into the database
    if (empty($errors)) {
        $query1 = $db->prepare(
            "INSERT INTO recipe (recipe_name, creator_id, creation_time) VALUES (?, ?, NOW())"
        );
        $result1 = $query1->execute([$recipe_name, $user_id]);
        $query2 = $db->prepare("INSERT INTO recipe_access (access_status, recipe_id, user_id)
                                SELECT 
                                1 as access_status,
                                (SELECT recipe_id FROM recipe WHERE recipe_name = ?) as recipe_id,
                                user_id
                                FROM users");
        $result2 = $query2->execute([$recipe_name]);
        if (!$result1 || !$result2) {
            $result = null;
            $errors["SQL"] = "Trouble adding new recipe to database.";
        } else {
            $db = null;
            header("Location: list.php");
            exit();
        }
    }
    $db = null;
}
?>
<!Doctype html>
<html lang="en-us">
    <head>
        <title>Recipe Box</title>
        <meta name="referrer" content="unsafe-url" />
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script type="text/javascript" src="js/eventHandlers.js"></script>
    </head>
    <body class="bg-primary create">
        <div class="page-grid">
            
            <div class="header">
                <div class="section-title">Create your own Recipe</div>
            </div>
            
            
            <div class="left-aside"></div>
            <div class="main">
                <form action="" id="recipe-save" class="create-recipe" method="post" autocomplete="off" enctype="multipart/form-data">
                    <div class="create-input">
                        <div class="recipe-name">
                            <label for="name">Recipe Name</label>
                            <textarea name="name" id="name" rows="1" cols="56"
                                placeholder="Give your recipe a name" 
                                <?= isset($errors["name"])
                                    ? 'class="invalid"'
                                    : "" ?>></textarea>
                            <p id="charNum"></p>
                        </div>
                        <div class="err-msg <?= isset($errors["name"])
                            ? ""
                            : "hidden" ?>">
                            <?= isset($errors["name"]) ? $errors["name"] : "" ?>
                        </div>
                        
                        <div class="recipe-ingredients">
                            <label for="ingredients">Ingredients</label>
                            <textarea name="ingredients" id="ingredients" rows="20" cols="500"
                                placeholder="List your ingredients"></textarea>
                        </div>
                        
                        <div class="recipe-steps">
                            <label for="steps">Recipe Steps</label>
                            <textarea name="steps" id="steps" rows="40" cols="500"
                                placeholder="List the recipe steps"></textarea>
                        </div>
                        
                        <button type="submit" id="save-btn" class="btn recipe-save">
                            <span>Save Recipe</span>
                        </button>
                    </div>
                </form>
                
                <div class="err-msg <?= isset($errors["SQL"]) ? "" : "hidden" ?>">
                    <?= isset($errors["SQL"]) ? $errors["SQL"] : "" ?>
                </div>
            </div>
            <div class="right-aside"></div>
            
            <div class="footer-left"><a href="list.php">Return to Recipe List</a></div>
            <div class="footer-center"></div>
            <div class="footer-right"><a href="logout.php">Log out</a></div>
        </div>
        <script type="text/javascript" src="js/eventCreateRecipe.js"></script>
    </body>
</html>