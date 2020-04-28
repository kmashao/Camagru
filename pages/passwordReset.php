<?php

require_once ("../config/userclass.php");

$user = new User();

if ($user->loggedIn() ==  true)
{
	$user->redirect("home.php");
}

if (isset($_POST['reset-btn']))
{
    $newPassword = $user->test_input($_POST['newPassword']);
    $confirmPass = $user->test_input($_POST['confirmPassword']);

    
    if(!preg_match("/.*^(?=.{6,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$/",$newPassword)) {
        $passwordError = "password too weak";
    }
    else if($confirmPass != $newPassword){
        $error = "Passwords don't match";
    }
    else if (isset($_GET['id']) && isset($_GET['token']))
    {
    	$username = $user->test_input($_GET['id']);
    	$token = $_GET['token'];
    
    	$stmt = $user->query("SELECT token FROM users WHERE username=:username");
    	$stmt->bindParam(":username", $username, PDO::PARAM_STR);
    	$stmt->execute();
    	$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
    	if ($stmt->rowCount() == 1)
    	{
    		if($userRow['token'] == $token)
    		{
    			$user->changePass($username, $newPassword);
    			$user->redirect("login.php");
    		}
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
            <section class="hero is-primary is-bold" style="padding-bottom: 10px">
                <div class="hero-body">
                    <div class="container">
                        <h1 class="title">
                            Reset Password
                        </h1>
                    </div>
                </div>
            </section>
            <div id="login-form" class="container is-fluid">
                <div class="notification is-centered">
                    <label class="label"> Time to reset that password</label>
                    <p style="text-aligh: center;">
                        Enter your new password make sure you remember it this time.
                    </p>
                </div>
                <form action="" method="post" fieldset>

                    <div class="field">
                        <label class="label">Enter new password</label>
                        <div class="control has-icons-left has-icons-right">
                            <input class="input is-primary" type="password" name="newPassword"
                                placeholder="enter new password" value="" required>
                            <span class="help is-danger"><?php if (isset($passwordError)) echo $passwordError;?></span>
                            <span class="icon is-small is-left">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Confirm password</label>
                        <div class="control has-icons-left has-icons-right">
                            <input class="input is-primary" type="password" name="confirmPassword"
                                placeholder="re-enter new password" value="" required>
                            <span class="help is-danger"><?php if (isset($error)) echo $error;?></span>
                            <span class="icon is-small is-left">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                    </div>

                    <div style="margin: auto;">
                        <button class="button is-primary is-light" name="reset-btn">
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </section>
        <div class="push"></div>
    </div>
    <?php include "footer.php"?>
</body>

</html>