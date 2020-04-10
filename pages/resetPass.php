<?php
    require_once("../config/userclass.php");

    $user = new User();

    if(isset($_POST['link-btn'])){

        $email = $user->test_input($_POST['email']);

        if(!filter_var($email, FILTER_VALIDATE_EMAIL))	{
            $error = 'Please enter a valid email address';
        }
        else{
            try{                
                $stmt = $user->query("SELECT `username`, `email` FROM users WHERE `email`=:email");
                $stmt->execute(array(':email'=>$email));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if ($stmt->rowCount() >= 1)
                {
                    $username = $row['username'];

                    $hash = hash("whirlpool", $email);
                    $subject = "Password Reset";
                    $headers = 'From:noreply@camagru.com' . "\r\n";
                    $link = "http://localhost:8080/Camagru/pages/passwordReset.php?id=$username&key=$hash";
                    $message = " 
                    Seems you have forgotten your password!
                    You can reset your account password by pressing the url below.

                    ------------------------
                    Username: '.$username.'
                    ------------------------

                    Please click this link to Reset your password'.

                    $link

                    if you didn't request a password reset just ignore this email.";

                    mail($email, $subject, $message, $headers);

                    $user->redirect('./login.php?joined');
                }
            }catch(PDOException $exception){
                echo 'Error : '.$exception->getMessage();
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
</head>

<body>
    <div id="body-container" class="container is-fullhd">
        <section class="section is-fullwidth">
            <section class="hero is-primary is-bold">
                <div class="hero-body">
                    <div class="container">
                        <h3 class="title">
                            Reset Password
                        </h3>
                    </div>
                </div>
            </section>
            <div id="login-form" class="container is-fluid">
                <div class="notification is-centered">
                    <label class="label"> Trouble logging in?</label>
                    <p style="text-aligh: center;">
                        Enter your email and we'll send you a link
                    </p>
                    <p style="text-aligh: center">
                        to get back into your account.
                    </p>
                </div>
                <form action="" method="post" fieldset>
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input is-primary" type="email" name="email" placeholder="enter email address"
                                value="" required>
                            <span class="help is-danger"><?php if (isset($error)) echo $error;?></span>
                            <span class="icon is-small is-left">
                                <i class="fas fa-envelope"></i>
                            </span>
                        </div>
                    </div>

                    <div style="margin: auto;">
                        <button class="button is-primary is-light" name="link-btn">
                            Send Login link
                        </button>
                    </div>

                    <br>
                    <div class="field is-grouped">
                        <p>Don't need to reset?</p> <a href="./login.php" style="padding-left: 10px"> Back to login </a>
                    </div>
                </form>
            </div>

        </section>
        <div class="push"></div>
    </div>
    <?php include "footer.php"?>
</body>

</html>