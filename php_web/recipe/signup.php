<?php
require_once("db.php");

function test_input($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

function newUserRecipeAccess($conn, $userId)
{
    // retrieve all recipe IDs
    $sql = "SELECT recipe_id FROM recipe";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $recipeIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Loop through each recipe ID and grant access to the new user
    foreach ($recipeIds as $recipeId) {
        $sql = "INSERT INTO recipe_access (access_status, recipe_id, user_id)
                VALUES (1, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$recipeId, $userId]);
    }
}

$errors = [];
$email = "";
$username = "";
$password = "";

// Check if a form was sent
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to the database
    try {
        $db = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("PDO Connect: " . $e->getMessage() . "\n<br />");
    }

    $usernameRegEx = "/^[a-zA-Z0-9_]+$/";
    $passwordRegEx = "/^(?=.*[^a-zA-Z ]).{6,}$/";

    // Email validation
    if (empty($_POST["email"])) {
        $errors["email"] = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Invalid email format";
        } else {
            // If email valid, check if it is already taken.
            $q = $db->prepare("SELECT 1 FROM users WHERE email = ?");
            $q->execute([$email]);
            $result = $q->fetchColumn();
            if ($result) {
                $errors["email"] = "Email address $email already exists.";
            }
        }
    }
    // Username validation
    if (empty($_POST["username"])) {
        $errors["username"] = "Username is required";
    } else {
        $username = test_input($_POST["username"]);
        if (!preg_match($usernameRegEx, $username)) {
            $errors["username"] = "Only letters and numbers allowed";
        } else {
            $q = $db->prepare("SELECT 1 FROM users WHERE username = ?");
            $q->execute([$username]);
            $result = $q->fetchColumn();
            if ($result) {
                $errors["username"] = "Username $username already exists.";
            }
        }
    }
    if (empty($_POST["password"])) {
        $errors["password"] = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
        if (!preg_match($passwordRegEx, $password)) {
            $errors["password"] = "Invalid password";
        }
    }

    // Directory where the avatars will be uploaded.
    $target_dir = "uploads/";
    $uploadOk = true;

    // Fetch the image filetype
    $check = getimagesize($_FILES["profilephoto"]["tmp_name"]);
    if ($check === false) {
        $errors["image"] = "File is not an image.";
        $uploadOk = false;
    }
    $imageFileType = strtolower(
        pathinfo($_FILES["profilephoto"]["name"], PATHINFO_EXTENSION)
    );
    // save the profile picture as username
    $target_file = $target_dir . $username . "." . $imageFileType;
    if (!empty($_FILES["profilephoto"]["name"])) {
        // Allow certain file formats
        if (
            $imageFileType != "jpg" &&
            $imageFileType != "png" &&
            $imageFileType != "jpeg" &&
            $imageFileType != "gif"
        ) {
            $errors["image"] = "$imageFileType is not valid. Only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = false;
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            $errors["image"] = "Sorry, file already exists.";
            $uploadOk = false;
        }
        // Check file size
        if ($_FILES["profilephoto"]["size"] > 3000000) {
            $errors["image"] = "Sorry, your file is too large.";
            $uploadOk = false;
        }
    } else {
        $errors["image"] = "Profile picture is required.";
        $uploadOk = false;
    }

    if ($uploadOk && empty($errors)) {
        $fileStatus = move_uploaded_file(
            $_FILES["profilephoto"]["tmp_name"],
            $target_file
        );
        // echo $_FILES["profilephoto"]["name"] . ' ' . $target_file . '</br>';
        if (!$fileStatus) {
            $errors["image"] = "Sorry, there was an error uploading your file.";
        }
    }
    if (empty($errors)) {
        $q = $db->prepare("INSERT INTO users(username, email, password, photo_url) VALUES (?, ?, ?, ?)");
        $result = $q->execute([$username, $email, $password, $target_file]);
        if ($result != false) {
            $user_id = $db->lastInsertId();
            newUserRecipeAccess($db, $user_id);
            $db = null;
            header("Location: login.php");
            exit();
        } else {
            $result = null;
            $errors["SQL"] = "Trouble adding new user to database!";
        }
    }
    $db = null;
    // foreach ($errors as $error){
    //     echo "$error </br>";
    // }
}
?>

<!Doctype html>
<html lang="en-us">
    <head>
        <title>Recipe Box</title>
        <meta name="referrer" content="unsafe-url" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <meta charset="utf-8" />
        <script type="text/javascript" src="js/eventHandlers.js"></script>
    </head>
    <body class="bg-secondary signup">
        <div class="page-grid">
            <div class="header">
                <div class="section-title">
                    Create your own Recipe Box
                </div>
            </div>
            <div class="left-aside"></div>
            <div class="main">
                <form action="" method="post" id="signup" enctype="multipart/form-data">
                    <div class="form-input-grid">
                        <label for="email" class="form-label">email</label>
                        <div class="input-container">
                            <input type="email" id="email" name="email" class="form-input" value="<?= $email ?>"
                            <?= isset($errors["email"])
                                ? 'class="form-input invalid"'
                                : 'class="form-input"' ?> autocomplete="off" />
                            <div class="err-msg <?= isset($errors["email"]) ? "" : "hidden" ?>">
                                <?= isset($errors["email"]) ? $errors["email"] : "" ?>
                            </div>
                        </div>
                        <label for="confirm-email" class="form-label">confirm email</label>
                        <div class="input-container">
                            <input type="email" id="confirm-email" name="confirm-email" class="form-input" autocomplete="off" />
                            <div id="err-confirm-email" class="err-msg hidden"></div>
                        </div>
                        <label for="password" class="form-label">password</label>
                        <div class="input-container">
                            <input type="password" id="password" name="password" class="form-input" 
                            <?= isset($errors["password"])
                                ? 'class="form-input invalid"'
                                : 'class="form-input"' ?> autocomplete="off" />
                            <div class="err-msg <?= isset($errors["password"])? "": "hidden" ?>">
                                <?= isset($errors["password"])? $errors["password"]: "" ?>
                            </div>
                        </div>
                        <label for="confirm-password" class="form-label">confirm password</label>
                        <div class="input-container">
                            <input type="password" id="confirm-password" name="confirm-password" class="form-input" autocomplete="off" />
                            <div id="err-confirm-password" class="err-msg hidden"></div>
                        </div>
                        <label for="username" class="form-label">username</label>
                        <div class="input-container">
                            <input type="text" id="username" name="username" class="form-input" value="<?= $username ?>"
                            <?= isset($errors["username"]) ? 'class="form-input invalid"': 'class="form-input"' ?> autocomplete="off" />
                            <div class="err-msg <?= isset($errors["username"])? "": "hidden" ?>">
                                <?= isset($errors["username"]) ? $errors["username"]: "" ?>
                            </div>
                        </div>
                        <label for="profilephoto" class="form-label">profile pic</label>
                            <label for="profilephoto" class="signup-profile"><img src="images/profile-pic-upload.svg"
                                    alt="Upload profile picture" /></label>
                        <div class="input-container">
                            <input type="file" id="profilephoto" name="profilephoto" class="hide-input" accept="image/*" />
                            <div class="err-msg <?= isset($errors["image"]) ? "": "hidden" ?>">
                                <?= isset($errors["image"])? $errors["image"]: "" ?></div>
                        </div>
                            <button type="submit" class="btn" value="signup">
                                <span>submit</span>
                            </button>
                        <div class="err-msg"><?php if (isset($errors["SQL"])) {echo $errors["SQL"];} ?></div>
                    </div>
                </form>
            </div>
            <div class="right-aside"></div>
            <div class="footer-left"></div>
            <div class="footer-center"></div>
            <div class="footer-right">
                <a href="login.php">Have an account? Login</a>
            </div>
        </div>
        <script type="text/javascript" src="js/eventRegisterSignup.js"></script>
    </body>
</html>