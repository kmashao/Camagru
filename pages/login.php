<?php
session_start();
require_once ("../config/userclass.php");

    $user = new User();

    if($user->loggedIn())
    {
        $user->redirect("home.php");
    }

    if($_GET['user'] == "guest"){
        $_SESSION['user_session'] = $user->test_input($_GET['user']);
        $user->redirect('home.php');
    }

    if(isset($_POST['login-btn'])){

        $username = $user->test_input($_POST['userName']);
	    $password = $user->test_input($_POST['password']);
	    if ($user->isReg($username) == false)
	    {
		    $error = "You are not registered. Click sign up to register";
	    }
	    else if ($user->isVerified($username) == false)
	    {
		    $error = "You have not verified your account yet";
	    }
	    else if($user->login($username, $password) == true)
	    {
            $_SESSION['user_session'] = $username;

            $stmt = $user->query("SELECT * FROM users WHERE username=:username");
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            $stmt->execute();
            $details = $stmt->fetchAll();
            $notification = $details[0];
            $_SESSION['notifications'] = $notification['notifications'];
		    $user->redirect('home.php');
	    }
	    else
	    {
		    $error = "Incorrect details entered try again";
	    }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
</head>
<body>
    <section class="hero is-primary is-bold">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    Login
                </h1>
            </div>
        </div>
    </section>
    <div class="container">
        <form action="login.php" method="post">
        <div style="margin: auto; padding: 100px">
            <div class="field">
                <label class="label">Username</label>
                <div class="control has-icons-left has-icons-right">
                    <input class="input is-success" type="text" name="userName"
                           placeholder="Enter username" value="" required>
                        <span class="help is-danger"><?php if (isset($error)) echo $error;?></span>
                        <span class="icon is-small is-left">
                                <i class="fas fa-user"></i>
                        </span>
                </div>
            </div>

            <div class="field">
                <label class="label">Password</label>
                <div class="control has-icons-left has-icons-right">
                    <input class="input is-primary" type="password" name="password" placeholder="enter password"
                           value="" required>
                         <span class="help is-danger"><?php if (isset($error)) echo $error;?></span>
                        <span class="icon is-small is-left">
                                <i class="fas fa-lock"></i>
                        </span>
                </div>
            </div>

            <div class="control">
                <button class="button is-link is-light" name="login-btn"></> Login</button>
            </div>
            <br>
            <div class="field is-grouped">
                <p>Forgot your password?</p> <a href="resetPass.php" style="padding-left: 10px"> Reset Password</a>
            </div>
            <br>
            <div class="field is-grouped">
                <p>Don't Have an account?</p>
                <a href="../index.php" style="padding-left: 10px; padding-right: 10px;">Register Here</a>
                <p>or just</p>
                <a href='login.php?user=guest' style="padding-left: 10px">Login as a guest</a>
            </div>
        </div>
; 
        </form>
    </div>
    <?php include ("footer.php") ?>
</body>
</html>
