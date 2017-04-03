<?php
require_once './includes/init.php';
require './PHPMailer/PHPMailerAutoload.php';
use Sessions\AutoLogin;

$arr = get_defined_vars(); //CHECK ALL VULNERABLE VARS
print_r($arr);
//die();

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $error = 'Please input Username';

    if (isset($_POST['username'])) {
        //$username=hash('sha256', $username);
        $pwd = trim($_POST['pwd']);
        $pwd = hash('sha256', $pwd);
        $stmt = $db->prepare('SELECT pwd FROM users WHERE username = :username');
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $stmt = hash('sha256', $stmt->fetchColumn());

        //IF HASHED PASSWORD IS EQUAL TO STATEMENT AND PASSWORD NOT EQUAL TO BLANK HASH
        if ($pwd == $stmt && $pwd != hash('sha256', '')) {
            session_regenerate_id(true);
            $_SESSION['username'] = $username;
            $_SESSION['authenticated'] = true;

            $stmt = $db->prepare('SELECT company FROM users WHERE username = :username');
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $stmt = $stmt->fetchColumn();
            $_SESSION['company'] = $stmt;

            if (isset($_POST['remember'])) {
                $autologin = new AutoLogin($db);
                $autologin->persistentLogin();
            }

            //$login = $_SERVER[ "HTTP_REFERER" ];
            //echo $login;
            header("Location:" );
            exit;
        } else {
            $error = 'Login failed. Check username and password.';
        }
    } else {
    }
}
if (isset($_POST['fpass'])) {
    if ($_POST['username'] != '') {
        $error = 'Username not found.';
        $username = trim($_POST['username']);
        $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        if ($stmt->fetchColumn() != 0) {
            //EMAIL LOGIC
            $mail = new PHPMailer;
            //$mail->SMTPDebug = 3;                           // Enable verbose debug output
            $mail->isSMTP();                                  // Set mailer to use SMTP
            $mail->Host = 'ets-exch2010.etsexpress.local';    // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                           // Enable SMTP authentication
            $mail->Username = 'donotreply';                   // SMTP username
            $mail->Password = 'RkFl!p9';                      // SMTP password
            $mail->SMTPSecure = 'tls';                        // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                // TCP port to connect to
            $mail->setFrom('DoNotReply@EtsExpress.com', 'ETS EXPRESS'); // Who is this from?
            $mail->isHTML(true);                                              // Set email format to HTML

            $stmt = $db->prepare('SELECT email FROM users WHERE username = :username');
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $stored = $stmt->fetchColumn();
            $mail->addAddress($stored);              // TO BE EXTRACTED

            $stmt = $db->prepare('SELECT pwd FROM users WHERE username = :username');
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $stored = $stmt->fetchColumn();

            $mail->Subject = 'ETS EXPRESS REQUEST';
            $mail->Body =
                'Hello, ' . $username . ', We are sorry to hear you are having difficulty...
                <br>However, We are more than happy to assist you log in!
                <br>The password we have on file for you is:' . $stored . '
                <br>Please remember to keep this password in a safe place.
                <br>If this does not solve your problem please email...';
            $mail->AltBody = 'YOUR EMAIL DOES NOT SUPPORT HTML...';
            if (!$mail->send()) {
                $error = 'Message could not be sent.';
                //$error .= 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
                $error = 'Message has been sent';
            }
        }
    } else {
        $error = 'Password Reset failed. Please input Username.';
    }


}
//$arr = get_defined_vars(); //CHECK ALL VULNERABLE VARS
//print_r($arr);
//die();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Auto Login</title>
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body>
<h1>Persistent Login</h1>
<?php
if (isset($error)) {
    echo "<p>$error</p>";
}
?>
<form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
    <p>
        <label for="username">Username</label><br>
        <input type="text" name="username" id="username">
    </p>
    <p>
        <label for="password">Password</label><br>
        <input type="password" name="pwd" id="pwd">
    </p>
    <div class="g-recaptcha" data-sitekey="6LdxvRoUAAAAANHjjZi7zZT-zMSTph9nGXcbk8gS"></div>
    <p>
        <input type="checkbox" name="remember" id="remember">
        <label for="remember">Remember me </label>
    </p>
    <p>
        <input style="float: left;" type="submit" name="login" id="login" value="Log In">
    </p>
    <p>
        <input style="float: left;" type="submit" name="fpass" id="fpass" value="Password Reset">
    </p>

</form>
</body>
</html>