<?php 

require_once('../config/userclass.php');
require_once('./sessionRedirect.php');

$user = new User();

if(isset($_GET['logout']) && $_GET['logout'] == "true")
{
    $user->logOut();
    $user->redirect('login.php');
}