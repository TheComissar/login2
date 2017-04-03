<?php

//require_once './includes/authenticate.php';

$errors = [];
if (isset($_POST['register'])) {
    require_once './includes/db_connect.php';
    $expected = ['username', 'pwd', 'confirm', 'email', 'phone', 'company', 'site_reference'];
    // Assign $_POST variables to simple variables and check all fields have values
    foreach ($_POST as $key => $value) {
        if (in_array($key, $expected)) {
            $$key = trim($value);
            if (empty($$key)) {
                $errors[$key] = 'This field requires a value.';
            }
        }
    }
    // Proceed only if there are no errors
    if (!$errors) {
        if ($pwd != $confirm) {
            $errors['nomatch'] = 'Passwords do not match.';
        } else {
            // Check that the username hasn't already been registered
            $sql = 'SELECT COUNT(*) FROM users WHERE username = :username';
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            if ($stmt->fetchColumn() != 0) {
                $errors['failed'] = "$username is already registered. Choose another name.";
            } else {
                try {
                    // Generate a random 8-character user key and insert values into the database
                    $user_key = hash('crc32', microtime(true) . mt_rand() . $username);
                    $sql = 'INSERT INTO users (user_key, username, pwd, email, phone, company, site_reference)
                            VALUES (:key, :username, :pwd, :email, :phone, :company, :site_reference)';
                    $stmt = $db->prepare($sql);

                    $stmt->bindParam(':key', $user_key);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindValue(':pwd', $pwd);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':phone', $phone);
                    $stmt->bindParam(':company', $company);
                    $stmt->bindParam(':site_reference', $site_reference);
                    $stmt->execute();
                } catch (\PDOException $e) {
                    if (0 === strpos($e->getCode(), '23')) {
                        // If the user key is a duplicate, regenerate, and execute INSERT statement again
                        $user_key = hash('crc32', microtime(true) . mt_rand() . $username);
                        if (!$stmt->execute()) {
                            throw $e;
                            echo 'what?';
                        }
                    }
                }
                // The rowCount() method returns 1 if the record is inserted,
                // so redirect the user to the login page
                if ($stmt->rowCount()) {
                    header('Location: login.php');
                    exit;
                }
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Designate Account</title>
    <link href="../css/styles.css" rel="stylesheet" type="text/css">
</head>

<body id="create">
<h1>Register</h1>
<h4>Create new account.</h4>
<hr>
<form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
    <p>
        <label for="username">Username</label><br>
        <input type="text" name="username" id="username"
        <?php
        if (isset($username) && !isset($errors['username'])) {
            echo 'value="' . htmlentities($username) . '">';
        } else {
            echo '>';
        }
        if (isset($errors['username'])) {
            echo $errors['username'];
        } elseif (isset($errors['failed'])) {
            echo $errors['failed'];
        }
        ?>
    </p>
    <p>
        <label for="pwd">Password</label><br>
        <input type="password" name="pwd" id="pwd">
        <?php
        if (isset($errors['pwd'])) {
            echo $errors['pwd'];
        }
        ?>
    </p>
    <p>
        <label for="confirm">Confirm Password</label><br>
        <input type="password" name="confirm" id="confirm">
        <?php
        if (isset($errors['confirm'])) {
            echo $errors['confirm'];
        } elseif (isset($errors['nomatch'])) {
            echo $errors['nomatch'];
        }
        ?>
    </p>
    <p>
        <label for="email">Email</label><br>
        <input type="email" name="email" id="email">
        <?php
        if (isset($errors['email'])) {
            echo $errors['email'];
        }
        ?>
    </p>
    <p>
        <label for="phone">Phone</label><br>
        <input type="phone" name="phone" id="phone">
        <?php
        if (isset($errors['phone'])) {
            echo $errors['phone'];
        }
        ?>
    </p>
    <p>
        <label for="company">Company</label><br>
        <input type="company" name="company" id="company">
        <?php
        if (isset($errors['company'])) {
            echo $errors['company'];
        }
        ?>
    </p>
    <p>
        <label for="site_reference">Site Reference</label><br>
        <input type="site_reference" name="site_reference" id="site_reference">
        <?php
        if (isset($errors['site_reference'])) {
            echo $errors['site_reference'];
        }
        ?>
    </p>
    <p>
        <input type="submit" name="register" id="register" value="Register">
    </p>
</form>
</body>
</html>