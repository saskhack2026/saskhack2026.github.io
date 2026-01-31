<?php
require_once("db.php");

session_start();

function test_input($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}
$errors = [];

// Check whether the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dataOK = true;

    // Get and validate the email and password fields
    if (empty($_POST["email"])) {
        $errors["email"] = "Email is required";
        $dataOK = false;
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Invalid email format";
            $dataOK = false;
        }
    }
    if (empty($_POST["password"])) {
        $errors["password"] = "Password is required";
        $dataOK = false;
    } else {
        $password = test_input($_POST["password"]);
        $passwordRegex = "/^.{8}$/";
        if (!preg_match($passwordRegex, $password)) {
            $errors["password"] = "Invalid Password";
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

        // get the user_id, username, and photo_url of the user that matches the email and password
        $query = $db->prepare("SELECT user_id, username, photo_url FROM users WHERE email = ? AND password = ?");
        $query->execute([$email, $password]);
        $result = $query->fetch();
        if (!$result) {
            // query has an error
            $errors["Login Failed"] =
                "That email/password combination does not exist.";
        } else {
            // If there's a match and login is successful, store the user_id, username, and photo_url fields
            $_SESSION["user_id"] = $result["user_id"];
            $_SESSION["username"] = $result["username"];
            $_SESSION["photo_url"] = $result["photo_url"];
            $db = null;

            header("Location: list.php");
            exit();
        }
    } else {
        $errors["Login Failed"] =
            "You entered an invalid email or password while logging in.";
    }
    $db = null;
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
    <body class="bg-primary">
        <div class="login-grid">
            <div class="login-main">
                <div class="page-title">Recipe Box</div>
                <p>create. save. share.</p>
                <form action="" method="post" id="login" autocomplete="off" enctype="multipart/form-data">
                    <div class="form-input-grid">
                        <label for="email" class="form-label">email</label>
                        <input type="email" id="email" name="email" class="form-input"
                        <?= isset($errors["email"])
                            ? 'class="form-input invalid"'
                            : 'class="form-input"' ?> autocomplete="off" />
                        &nbsp;
                        <div class="err-msg <?= isset($errors["email"])
                            ? ""
                            : "hidden" ?>">
                            <?= isset($errors["email"]) ? $errors["email"] : "" ?>
                        </div>
                        <label for="password" class="form-label">password</label>
                        <input type="password" id="password" name="password" class="form-input" 
                        <?= isset($errors["password"])
                            ? 'class="form-input invalid"'
                            : 'class="form-input"' ?> autocomplete="off" />
                        &nbsp;
                        <div class="err-msg <?= isset($errors["password"])
                            ? ""
                            : "hidden" ?>">
                            <?= isset($errors["password"])
                                ? $errors["password"]
                                : "" ?>
                        </div>
                    </div>
                    <button type="submit" class="btn" value="labels">
                        <span>login</span>
                    </button>
                </form>
                <div class="err-msg"><?php if (isset($errors["Database Error"])) {
                    echo $errors["Database Error"];
                } ?></div>
                <div class="err-msg"><?php if (isset($errors["Login Failed"])) {
                    echo $errors["Login Failed"];
                } ?></div>
                
            </div>
            <div class="login-footer">
                <a href="signup.php">Sign up for an account</a>
            </div>
        </div>
        <script type="text/javascript" src="js/eventRegisterLogin.js"></script>
    </body>
</html>