<?php
require(__DIR__ . "/partials/nav.php");

// Function to check if an email already exists in the database
function isEmailTaken($email)
{
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM Users WHERE email = :email");
    $stmt->execute([":email" => $email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return !empty($result);
}

// Function to check if a username is already taken
function isUsernameTaken($username)
{
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM Users WHERE username = :username");
    $stmt->execute([":username" => $username]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return !empty($result);
}

if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"]) && isset($_POST["username"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);
    $username = se($_POST, "username", "", false);
    $confirm = se($_POST, "confirm", "", false);

    $hasError = false;

    if (empty($email)) {
        echo "Email must not be empty";
        $hasError = true;
    }
    //sanitize
    $email = sanitize_email($email);
    //validate
    if (!is_valid_email($email)) {
        echo "Invalid email address";
        $hasError = true;
    }
    if (empty($password)) {
        echo "password must not be empty";
        $hasError = true;
    }
    if (empty($confirm)) {
        echo "Confirm password must not be empty";
        $hasError = true;
    }
    if (strlen($password) < 8) {
        echo "Password too short";
        $hasError = true;
    }
    if ($password !== $confirm) {
        echo "Passwords must match";
        $hasError = true;
    }

// Validate if the email is available
    if (isEmailTaken($email)) {
        echo "Email is already registered. Please use a different email.";
        $hasError = true;
    }

    // Validate if the username is available
    if (isUsernameTaken($username)) {
        echo "Username is already taken. Please choose a different username.";
        $hasError = true;
    }

    if (!$hasError) {
        echo "Welcome, $email";
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES(:email, :password, :username)");
        try {
            $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
            echo "Successfully registered!";
        } catch (Exception $e) {
            echo "There was a problem registering";
            echo "<pre>" . var_export($e, true) . "</pre>";
        }
    }
}
?>

<form onsubmit="return validate(this)" method="POST">
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" required />
    </div>
    <div>
        <label for="username">Username</label>
        <input type="text" name="username" required />
    </div>
    <div>
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <div>
        <label for="confirm">Confirm</label>
        <input type="password" name="confirm" required minlength="8" />
    </div>
    
    <input type="submit" class="btn-register" value="Register" />

</form>
<script>
    function validate(form) {
        //TODO: implement JavaScript validation
        //ensure it returns false for an error and true for success
        return true;
    }
</script>