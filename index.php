<?php
session_start();
require ("./config/userclass.php");
$user = new User();

    if($user->loggedIn() == true){
        $user->redirect("./pages/home.php");
    }

    if(isset($_POST['register-btn'])) {
    $firstName = $user->test_input($_POST['firstName']);
    $lastName = $user->test_input($_POST['lastName']);
    $userName = $user->test_input($_POST['userName']);
    $email = $user->test_input($_POST['email']);
    $password = $user->test_input($_POST['password']);
    $confirmPassword = $user->test_input($_POST['confirmPassword']);

    if (!preg_match("/\s/",$firstName)) {
        $firstNameError = "Only letters allowed";}
    }

    if (!preg_match("/\s/",$lastName)) {
        $lastNameError = "Only letters allowed";
    }

    if (empty($firstName)){
        $firstNameError = "Please provide your first name";
    }
    else if (empty($lastName)){
        $lastNameError = "Please provide your last name";
    }
    else if(empty($userName)){
        $userNameError = "Please provide your username";
    }
    else if(empty($email))	{
        $emailError = "Provide email your email address";
    }
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL))	{
        $emailError = 'Please enter a valid email address';
    }
    else if(empty($password))	{
        $passwordError = "Please provide a password";
    }
    else if(strlen($password) < 6){
        $passwordError = "Password must be atleast 6 characters";
    }
    else if (empty($confirmPassword)){
        $confirmPassErr = "Please confirm your password";
    }
    else if ($confirmPassword != $password) {
        $confirmPassErr = "Passwords do not match";
    }
    else{
        try{
	        $stmt = $user->query("SELECT `username`, `email` FROM users WHERE `username`=:username OR `email`=:email");
	        $stmt->execute(array(':username'=>$userName, ':email'=>$email));
	        $row = $stmt->fetch(PDO::FETCH_ASSOC);
	        if ($stmt->rowCount() >= 1)
	        {	if($row['username'] == $userName) {
		            $userNameError = "Username already taken!";
	            }
	            else if($row['email'] == $email) {
		            $emailError = "Email is already taken!";
	            }
	        }
	        else
	        {
		        if (isset($userNameError) || isset($emailError))
			        $user->redirect("index.php");
		        if($user->regUser($firstName, $lastName, $userName, $email, $password))
		        {
			        $hash = hash("whirlpool", $email);
                    $subject = "Camagru account confirmation";
                    $headers = 'From:noreply@camagru.com' . "\r\n";
			        $link = "http://localhost:8080/Camagru/pages/verifyUser.php?id=$userName&key=$hash";
			        $message = " 
                    Thanks for signing up!
                    Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.
                     
                    ------------------------
                    Username: '.$userName.'
                    Password: '.$password.'
                    ------------------------
                     
                    Please click this link to activate your account:'.
                    $link

                    ";
			        mail($email, $subject, $message, $headers);
			        $user->redirect('pages/login.php?joined');
		        }
	        }
        }catch(PDOException $exception){
            echo $exception->getMessage();
        }
    }
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
</head>

<body>
    <div id="body-container" class="container is-fullhd">
        <section class="section is-fullwidth">
            <section class="hero is-primary is-bold">
                <div class="hero-body">
                    <div class="container">
                        <h3 class="title">
                            Registration
                        </h3>
                    </div>
                </div>
            </section>
            <br />
            <div class="container is-fluid" id="registration-form" autofill="off">
                <form action="index.php" method="post" fieldset="enabled">
                            <div class="field">
                                <label class="label">First name</label>
                                <div class="control has-icons-left">
                                    <input class="input is-primary" type="text" name="firstName"
                                        placeholder="Enter name" value="" pattern='[a-zA-Z\-]+'
                                        title="Enter letters only" required>
                                    <span class="help is-danger"><?php if (isset($firstNameError)) echo $error;?></span>
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Last name</label>
                                <div class="control has-icons-left">
                                    <input class="input is-primary" type="text" name="lastName"
                                        placeholder="Enter last name" value="" pattern='[a-zA-Z\-]+'
                                        title="Enter letters only" required>
                                    <span class="help is-danger"><?php if (isset($lastNameError)) echo $error;?></span>
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Username</label>
                                <div class="control has-icons-left">
                                    <input class="input is-success" type="text" name="userName"
                                        placeholder="Enter public name" value="" pattern="\w+"
                                        title="Enter letters and symbols" required>
                                    <span class="help is-danger"><?php if (isset($userNameError)) echo $error;?></span>
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>
                                <!--  <p class="help is-success">This username is available</p> -->
                            </div>

                            <div class="field">
                                <label class="label">Email</label>
                                <div class="control has-icons-left">
                                    <input class="input is-primary" type="email" name="email"
                                        placeholder="enter email address" value="" required>
                                    <span class="help is-danger"><?php if (isset($error)) echo $emailError;?></span>
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Password</label>
                                <div class="control has-icons-left has-icons-right">
                                    <input class="input is-primary" type="password" minlength="6" name="password"
                                        placeholder="enter password" value=""
                                        pattern="(?=\S*\d)(?=\S*[a-z])(?=\S*[A-Z])\S*"
                                        title="Enter a strong password with 6 or more characters" required>
                                    <span class="help is-danger"><?php if (isset($error)) echo $passwordError;?></span>
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Confirm Password</label>
                                <div class="control has-icons-left has-icons-right">
                                    <input class="input is-primary" type="password" minlength="6" name="confirmPassword"
                                        placeholder="confirm password" value=""
                                        pattern="(?=\S*\d)(?=\S*[a-z])(?=\S*[A-Z])\S*" title="Re-enter your password"
                                        required>
                                    <span class="help is-danger"><?php if (isset($confirmPassErr)) echo $error;?></span>
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                </div>

                            </div>

                            <div class="control">
                                <button class="button is-link" name="register-btn">Register</button>
                            </div>

                            <p style="padding-top: 20px">
                                Already a member? <a href="pages/login.php">Sign in</a>
                            </p>
                        </div>
                </form>
            </div>
        </section>
        <div class="push"></div>
    </div>
    <?php include "pages/footer.php"?>
</body>

</html>