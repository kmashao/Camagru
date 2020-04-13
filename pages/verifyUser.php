<?php

require ("../config/userclass.php");
require_once ("sessionRedirect.php");

$user = new User();

if ($user->loggedIn() ==  true)
{
	$user->redirect("home.php");

}	

if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['key']))
{
	$username = $user->test_input($_GET['id']);
	$key_hash = $_GET['key'];
;

	$stmt = $user->query("SELECT username, token FROM users WHERE username=:username AND token=:token");
	$stmt->bindParam(":username", $username, PDO::PARAM_STR);
	$stmt->bindParam(":token", $key_hash, PDO::PARAM_STR);
	$stmt->execute();
	$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($stmt->rowCount() == 1)
	{
		$user->verifyEmail($username, $key_hash);
		$user->redirect("login.php?verified");
	
	}
}
