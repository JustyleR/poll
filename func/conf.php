<?php

if(!defined('fileAccess')) { header('Location: index.php'); }

// Не показва грешки
//error_reporting(0);

$db_host	=	'localhost';
$db_user	=	'root';
$db_pass	=	'';
$db_db		=	'poll';

// Свързваме се с базата данни и променяме charset-а
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_db);
		if(mysqli_connect_errno($conn)) {
			
			die('Не могат да се свържа с базата данни!');
			
		} else {
			
			mysqli_set_charset($conn, "utf8");
			
		}