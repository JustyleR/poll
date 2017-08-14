<html>
	
	<head>
		
		<title>Система за анкети</title>
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		
		<meta charset="utf-8"> 
		
		<style>
			body {
				padding-top: 70px;
			}
		</style>
		
	</head>
	
	<body>
		
		<?php
		
		define('fileAccess', TRUE);
		
		// Include the files
		
		require('func/conf.php');
		require('func/core.php');
		
		echo '
		<div class="row ">
			<div class="col-md-4 col-md-offset-4">
				';
				if(!isset($_GET['p'])) { echo '<div class="panel panel-default">
					<center>
						<a href="index.php?p=archive">Архив</a> | 
						<a href="index.php?p=add">Добави</a>
					</center>
				</div>
				'; } else if(isset($_GET['p']) && ($_GET['p'] == 'archive')) { echo '<div class="panel panel-default">
					<center>
						<a href="index.php">Начало</a> | 
						<a href="index.php?p=add">Добави</a>
					</center>
				</div>
				'; }
				'
					';
		
		pages($conn);
		
		echo '
			</div>
		</div>
		';
		?>
		
	</body>
	
</html>