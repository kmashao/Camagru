<?php
require "database.php";

/**
 * @var PDO
 **/
$conn = null;

/**
 * @var PDO
 **/
$db = null;

try
{
	$db = new PDO("mysql:host=$DB_DSN", $DB_USER, $DB_PASSWORD);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
	echo "Failed to connect to host: " . $e->getMessage();
}

try{
	$sql = "CREATE DATABASE IF NOT EXISTS ".$DB_NAME;
	if($db->exec($sql)){
		try {
			$conn = new PDO("mysql:host=$DB_DSN;dbname=$DB_NAME", $DB_USER, $DB_PASSWORD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "database successfully created";
		}
		catch(PDOException $e)
		{
			echo "Failed to connect to database: " . $e->getMessage();
		}
	}
}
catch(PDOException $e) {
	echo "Failed to create to database: " . $e->getMessage();
}


    $image_table = "CREATE TABLE IF NOT EXISTS images(
    image_id INT(6) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL, 
    image_name TEXT NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );";

    $comments_table = "CREATE TABLE IF NOT EXISTS comments(
    comment_id INT(6) AUTO_INCREMENT PRIMARY KEY,
    image_id INT(6) NOT NULL,
    username VARCHAR(30) NOT NULL,
    comment TEXT NOT NULL,
    comment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );";

    $likes_table = "CREATE TABLE IF NOT EXISTS likes(
    like_id INT(6) AUTO_INCREMENT PRIMARY KEY,
    image_id INT(6) NOT NULL,
    username VARCHAR(30) NOT NULL,
    date_liked TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );";

    $users_table = "CREATE TABLE IF NOT EXISTS users(
    `user_id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
    firstname VARCHAR(30) NOT NULL,
    lastname VARCHAR(30) NOT NULL,
    username VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL,
    `password` VARCHAR(100) NOT NULL,
    notifications VARCHAR(3) DEFAULT 'Yes',
    verified VARCHAR(3) DEFAULT 'NO',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );";

try {

		$conn->exec($users_table);

} catch (PDOException $exception) {
	echo("Failed to create users table " . $exception->getMessage());
}

try {

	$conn->exec($image_table);

} catch (PDOException $exception) {
	echo("Failed to images table " . $exception->getMessage());
}

try {

	$conn->exec($comments_table);

} catch (PDOException $exception) {
	echo("Failed to create comments table " . $exception->getMessage());
}

try {

	$conn->exec($likes_table);

} catch (PDOException $exception) {
	echo("Failed to create table " . $exception->getMessage());
}



?>