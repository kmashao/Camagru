<?php
require_once("../config/userclass.php");
require_once("../config/dbConn.php");
require_once("./sessionRedirect.php");

$user = new User();
$username = $_SESSION['user_session'];
$notifications = $_SESSION['notifications'];
$conn = getConn();

		$stmt = $user->query("SELECT * FROM users WHERE username=:username LIMIT 1");
    	$stmt->bindParam(":username", $username, PDO::PARAM_STR);
    	$stmt->execute();
    	$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
    	if ($stmt->rowCount() == 1){
			$firstName = $userRow['firstname'];
			$lastName = $userRow['lastname'];
			$email = $userRow['email'];
		}

		if(isset($_POST['submit-btn']))
		{
			$newUsername = $user->test_input($_POST['username']);
			$newEmail = $user->test_input($_POST['email']);
			$newPassword = $user->test_input($_POST['newPassword']);
			$confirmPassword = $user->test_input($_POST['confirmPassword']);
			if(isset($_POST['notifications']) && $_POST['notifications'] == "Yes"){
				$notifications = "Yes";
				$_SESSION['notifications'] = "Yes";
			}else{
				$notifications = "No";
				$_SESSION['notifications'] = "No";
			}
			
			if(empty($newEmail)){
				$newEmail = $email;
			}
			if(empty($newUsername)){
				$newUsername = $username;
			}
			if(strlen($newEmail) > 0 && !filter_var($newEmail, FILTER_VALIDATE_EMAIL))	{
				$error = 'Please enter a valid email address';
			}
			else if (strlen($newPassword) > 0 && strlen($newPassword) < 6){
				$error = 'Password must be atleast 6 characters long';
			}else if(strlen($newPassword) > 0 && ($newPassword != $confirmPassword)){
				$error = 'Passwords do not match';
			}else{

				if(strlen($newPassword) > 0){
					$user->updatePassword($username,$newPassword);
				}
				$user->updateDetails($username,$newUsername,$newEmail,$notifications);
				$success = "User details updated";
				$headers = 'From:noreply@camagru.com' . "\r\n";
				$mail = "
				Good day $firstName $lastName
				you have updated some of your information on you profile
				if it wasn't you, consider changing your password.";

				mail($email,"Account Details",$mail,$headers);
				$_SESSION['user_session'] = $newUsername;
				$user->redirect("profile.php");
			}
			
		}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Profile: <?php print($_SESSION['user_session']) ?></title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.2/css/bulma.min.css">
	<link rel="stylesheet" href="../css/styles.css">
	<script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
</head>

<body class="has-navbar-fixed-top">
	<?php include_once "navbar.php" ?>
	<div id="body-container" class="container is-fullhd">
		<section class="section is-fullwidth">
		<div class="container">
			<?php
				if(isset($success)){
					echo"<div class='modal'>
					<div class='modal-background'></div>
					<div class='modal-content'>
					  <div class='box'>
						<p class='text is-2 has-text-primary'>" . $success. "</p>
					  </div>
					</div>
					<button class='modal-close is-large' aria-label='close'></button>
				  </div>
				  <script src='../js/modal.js'></script>";
				}
			?>
			
			</div>
			<section class="hero is-light">
				<div class="hero-body">
					<div class="container">
						<h3 class="title">
							Account Settings
						</h3>
						<h3 class="subtitle">
							Edit your username, email and password
						</h3>
					</div>
				</div>
			</section>
			<br />
			<section name="user-info">
				<div id="profile-form" class="container">
					<div class="tile is-ancestor">
						<div id="prof-form-div" class="tile is-parent">
							<div class="tile is-child is-4">

									<div class="field is grouped">
										<label class="label">Username :</label>
										<p><?php print($username);?></p>
									</div>
									<br>
									<div class="field is grouped">
										<label class="label">First Name : </label>
										<p><?php print($firstName);?></p>
									</div>
									<br>
									<div class="field is grouped">
										<label class="label">Last Name : </label>
										<p><?php print($lastName);?></p>
									</div>
									<br>
									<div class="field is grouped">
										<label class="label">Email : </label>
										<p><?php print($email);?></p>
									</div>
							</div>
						</div>
						<div id="prof-form-div"class="tile is-parent">
							<div class="tile is-child">
								<form action="<?php $_SERVER['PHP_SELF']?>" method="post">
									<div class="container">
										<div class="field">
											<label class="label">Username</label>
											<div class="control has-icons-left has-icons-right">
												<input class="input is-primary" type="text" name="username"
													placeholder="enter new username" value="" autocomplete="off">
												<span class="icon is-small is-left">
													<i class="fas fa-user"></i>
												</span>
											</div>
										</div>

										<div class="field">
											<label class="label">Enter new Email</label>
											<div class="control has-icons-left has-icons-right">
												<input class="input is-primary" name="email" type="email"
													placeholder="Enter new email" value="">
												<span class="icon is-small is-left">
													<i class="fas fa-envelope"></i>
												</span>
											</div>
										</div>

										<div class="field">
											<label class="label">Enter new password</label>
											<div class="control has-icons-left has-icons-right">
												<input class="input is-primary" type="password" name="newPassword"
													placeholder="enter new password" autocomplete="off"
													pattern="(?=\S*\d)(?=\S*[a-z])(?=\S*[A-Z])\S*">
												<span
													class="help is-danger"><?php if (isset($error)) echo $error;?></span>
												<span class="icon is-small is-left">
													<i class="fas fa-lock"></i>
												</span>
											</div>

											<div class="field">
												<label class="label">Confirm password</label>
												<div class="control has-icons-left has-icons-right">
													<input class="input is-primary" type="password"
														name="confirmPassword" placeholder="re-enter new password"
														value="" pattern="(?=\S*\d)(?=\S*[a-z])(?=\S*[A-Z])\S*"
														>
													<span
														class="help is-danger"><?php if (isset($error)) echo $error;?></span>
													<span class="icon is-small is-left">
														<i class="fas fa-lock"></i>
													</span>
												</div>
											</div>
											<div class="field">
											<label class="checkbox">
												<input type="checkbox" name="notifications"
												value="Yes" <?php if($notifications == "Yes") echo "checked"?>>
													notifications.
											</label>
											</div>

											<div>
												<button class="button is-primary is-light " name="submit-btn">
													Submit
												</button>
											</div>
											<form>
										</div>

									</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</section>
	</div>
	<?php include("footer.php"); ?>
</body>

</html>