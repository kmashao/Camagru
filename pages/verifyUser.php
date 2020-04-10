<?php

require_once ("sessionRedirect.php");

require_once ("../config/userclass.php");


$user = new User();
//$username = $_SESSION['user_session'];

if ($user->loggedIn() ==  true)
{
	$user->redirect("home.php");

}
if (isset($_GET['id']) && isset($_GET['key']))
{
	$username = strip_tags($_GET['id']);
	$key_hash = $_GET['key'];

	$stmt = $user->query("SELECT username, email FROM users WHERE username=:username");
	$stmt->bindParam(":username", $username, PDO::PARAM_STR);
	$stmt->execute();
	$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($stmt->rowCount() == 1)
	{
		if(hash("whirlpool", $userRow['email']) == $key_hash)
		{
			$user->verifyEmail($username, $userRow['email']);
			$user->redirect("login.php");
		}
	}
}
