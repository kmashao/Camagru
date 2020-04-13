<?php
session_start();

require_once '../config/userclass.php';
$session = new User();

// if user session is not active(not logged in) this page will help 'home.php and profile.php' to redirect to login page
// put this file within secured pages that users (users can't access without login)

if (!$session->loggedIn()) {
	// if session is not set it redirects to login page
	$session->redirect('login.php');
}