<?php

function getConn(){
	require ("database.php");
	$conn = null;
	try {
		$conn = new PDO("mysql:host=$DB_DSN;dbname=$DB_NAME", $DB_USER, $DB_PASSWORD,$options);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e)
	{
		echo "Failed to connect to database: " . $e->getMessage();
	}
	return $conn;
	}