<?php

require ("../config/userclass.php");
require_once ("sessionRedirect.php");

$user = new User();

if ($user->loggedIn() ==  true)
{
	$user->redirect("home.php");

}	

if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['token']))
{
	$username = $user->test_input($_GET['id']);
	$token = $_GET['token'];
;

	$stmt = $user->query("SELECT username, token FROM users WHERE username=:username AND token=:token");
	$stmt->bindParam(":username", $username, PDO::PARAM_STR);
	$stmt->bindParam(":token", $token, PDO::PARAM_STR);
	$stmt->execute();
	$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($stmt->rowCount() == 1)
	{
		$user->verifyUser($username, $token);
		$user->redirect("login.php?verified");
	
	}
}
