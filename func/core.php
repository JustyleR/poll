<?php

if(!defined('fileAccess')) { header('Location: index.php'); }

function pages($conn) {
	
	if(isset($_GET['p'])) {
		
		$page = $_GET['p'];
		
		if($page == 'archive') {
			
			archive($conn);
			
		} else if($page == 'add') {
			
			add($conn);
			
		} else if($page == 'edit') {
			
			edit($conn);
			
		} else if($page == 'delete') {
			
			del($conn);
			
		}
		
		else { header('Location: index.php'); }
		
	} else {
		
		indexPage($conn);
		
	}
	
}

function indexPage($conn) {
	
	$checkPoll = mysqli_query($conn, "SELECT * FROM poll ORDER BY poll_id DESC LIMIT 1");
	if(mysqli_num_rows($checkPoll) > 0) {
		
		$row = mysqli_fetch_assoc($checkPoll);
		
		$checkVote = mysqli_query($conn, "SELECT ip FROM poll_votes WHERE poll_id='".$row['poll_id']."' AND ip='".$_SERVER['REMOTE_ADDR']."'");
		if(mysqli_num_rows($checkVote) > 0) {
			
			indexResults($conn, $row['poll_id']);
			
		} else {
			
			indexVote($conn, $row);
			
		}
		
	} else {
		
		echo '<div class="panel-heading">
							В момента няма създадени анкети!
						</div>';
		
	}
	
}

function indexVote($conn, $row) {
		
		echo '<div class="panel-heading">
							'.$row['title'].' ( 
							<a href="index.php?p=edit&id='.$row['poll_id'].'">Редактирай</a> / 
							<a href="index.php?p=delete&id='.$row['poll_id'].'">Изтрий</a> )
						</div>
						<div class="panel-body">
						<form action="" method="POST">';
							
							for($i = 1; $i < 7; $i++) {
			
								$r = 'answer' . $i;
								
								if(!empty($row['answer' . $i])) {
									
									echo '
							 <div class="radio">
								<label>
									<input type="radio" name="answer" value="'.$i.'">
									'.$row['answer'.$i].'
								</label>
							</div>';
									
								}
								
							}
							
							echo '
						</div>
						<div class="panel-footer">
							<input type="submit" name="vote" value="Гласувай!" class="btn btn-success btn-sm" />
							&nbsp;&nbsp;';
							
							if(isset($_POST['vote'])) {
								
								$answer = $_POST['answer'];
								$id		= $row['poll_id'];
								$ip		= $_SERVER['REMOTE_ADDR'];
								
								if(empty($answer)) {
									
									echo 'Моля, изберете отговор!';
									
								} else {
									
									mysqli_query($conn, "INSERT INTO poll_votes (poll_id, answer, ip) VALUES ('$id', '$answer', '$ip')");
									
									header('refresh:2;');
									
									echo 'Вие гласувахте успешно!';
									
								}
								
							}
							
							echo '
						</div>
						</form>';
	
}

function indexResults($conn, $id) {
	
	$checkVotes = mysqli_query($conn, "SELECT * FROM poll_votes WHERE poll_id = $id");
	if(mysqli_num_rows($checkVotes) > 0) {
		
		$all = mysqli_num_rows($checkVotes);
		
		$getPoll = mysqli_query($conn, "SELECT * FROM poll WHERE poll_id='$id'");
		$row	 = mysqli_fetch_assoc($getPoll);
		
		echo '<strong>'.$row['title'].'</strong> ( 
							<a href="index.php?p=edit&id='.$row['poll_id'].'">Редактирай</a> / 
							<a href="index.php?p=delete&id='.$row['poll_id'].'">Изтрий</a> ) <br />
		<h5 class="text-danger">Резултати :</h5>
		
					<hr />
						';
		
		for($i = 1; $i < 7; $i++) {

			$r = 'answer' . $i;
			
			if(!empty($row['answer' . $i])) {

				$get = mysqli_query($conn, "SELECT answer FROM poll_votes WHERE answer='$i' and poll_id='$id'");
				
				$num = mysqli_num_rows($get);
				
				$final = ($num / $all) * 100;
				$final = round($final);
				
				echo '
				'.$row['answer' . $i] . ' ('.$num.' гласа) :
				<div class="progress">
					<div class="progress-bar" role="progressbar" style="width: '.$final.'%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">'.$final.'%</div>
				</div>';
				
			}
			
		}
			
		
	} else {
		
		echo '<div class="panel-heading">
							В момента няма отговори в анкетата!
						</div>';
		
	}
	
}

function archive($conn) {
	
	$getPolls = mysqli_query($conn, "SELECT * FROM poll ORDER BY poll_id DESC");
	if(mysqli_num_rows($getPolls) > 0) {
		
		
		while($row = mysqli_fetch_array($getPolls)) {
			
			echo '<strong>'.$row['title'].'</strong> ( 
							<a href="index.php?p=edit&id='.$row['poll_id'].'">Редактирай</a> / 
							<a href="index.php?p=delete&id='.$row['poll_id'].'">Изтрий</a> )<br />';
			
			for($i = 1; $i < 7; $i++) {
				
				$get = mysqli_query($conn, "SELECT answer FROM poll_votes WHERE answer='$i' and poll_id='".$row['poll_id']."'");
				
				$num = mysqli_num_rows($get);
				
				echo $row['answer'.$i].' (Гласове: '.$num.')<br />';
				
			}
			
			echo '<hr />';
			
		}
		
	} else {
		
		echo '<div class="panel-heading">
							В момента няма създадени анкети!
						</div>';
		
	}
	
}

function add($conn) {
	
	echo '<div class="panel panel-default">
					<center>
						<a href="index.php">Начало</a>
					</center>
				</div>
				<form action="" method="POST">
					<input type="text" name="title" 	class="form-control" placeholder="Заглавие на анкетата" required />';
		
		for($i = 1; $i < 7; $i++) {
			
			echo '
					<input type="text" name="answer'.$i.'"	class="form-control" placeholder="Отговор" />';
			
		}
		
					echo '
					<input type="submit" name="create" value="Създай" />
				</form>
				';
	
	if(isset($_POST['create'])) {
		
		$title		= mysqli_real_escape_string($conn, $_POST['title']);
		$answer1	= mysqli_real_escape_string($conn, $_POST['answer1']);
		$answer2	= mysqli_real_escape_string($conn, $_POST['answer2']);
		$answer3	= mysqli_real_escape_string($conn, $_POST['answer3']);
		$answer4	= mysqli_real_escape_string($conn, $_POST['answer4']);
		$answer5	= mysqli_real_escape_string($conn, $_POST['answer5']);
		$answer6	= mysqli_real_escape_string($conn, $_POST['answer6']);
		
		$checkTitle = mysqli_query($conn, "SELECT title FROM poll WHERE title='$title'");
		if(mysqli_num_rows($checkTitle) > 0) {
			
			echo 'Вече има анкета с това заглавие!';
			
		} else {
			
			if(empty($answer1) || (empty($answer2))) {
				
				echo 'Трябва да напишете поне 2 отговора!';
				
			} else {
				
				mysqli_query($conn, "INSERT INTO poll (title, answer1, answer2, answer3, answer4, answer5, answer6) VALUES 
				('$title','$answer1','$answer2','$answer3','$answer4','$answer5','$answer6')");
				
				echo 'Успешно създадохте анкетата!';
				
				header('refresh:2;url=index.php');
				
			}
			
		}
		
	}
}

function edit($conn) {
	
	if(isset($_GET['id'])) {
		
		$checkPoll = mysqli_query($conn, "SELECT * FROM poll WHERE poll_id='".$_GET['id']."'");
		if(mysqli_num_rows($checkPoll) > 0) {
			
			$row = mysqli_fetch_assoc($checkPoll);
			
				echo '<div class="panel panel-default">
					<center>
						<a href="index.php">Начало</a>
					</center>
				</div>
				<form action="" method="POST">
					<input type="text" name="title" 	class="form-control" value="'.$row['title'].'" required />';
		
			for($i = 1; $i < 7; $i++) {
				
				echo '
						<input type="text" name="answer'.$i.'"	class="form-control" value="'.$row['answer'.$i].'" />';
				
			}
		
					echo '
					<input type="submit" name="edit" value="Редактирай" />
				</form>
				';
			
			if(isset($_POST['edit'])) {
				
				$title		= mysqli_real_escape_string($conn, $_POST['title']);
				$answer1	= mysqli_real_escape_string($conn, $_POST['answer1']);
				$answer2	= mysqli_real_escape_string($conn, $_POST['answer2']);
				$answer3	= mysqli_real_escape_string($conn, $_POST['answer3']);
				$answer4	= mysqli_real_escape_string($conn, $_POST['answer4']);
				$answer5	= mysqli_real_escape_string($conn, $_POST['answer5']);
				$answer6	= mysqli_real_escape_string($conn, $_POST['answer6']);
				
				$checkTitle = mysqli_query($conn, "SELECT title FROM poll WHERE title='$title'");
				if(mysqli_num_rows($checkTitle) > 0 && $title != $row['title']) {
					
					echo 'Вече има анкета с това заглавие!';
					
				} else {
					
					if(empty($answer1) || (empty($answer2))) {
						
						echo 'Трябва да напишете поне 2 отговора!';
						
					} else {
						
						mysqli_query($conn, "UPDATE poll SET title='$title', answer1='$answer1', answer2='$answer2', answer3='$answer3', answer4='$answer4', 
						answer5='$answer5', answer6='$answer6' WHERE poll_id='".$_GET['id']."'");
						
						echo 'Успешно променихте анкетата!';
						
						header('refresh:2;url=index.php?p=edit&id='.$_GET['id'].'');
						
					}
					
				}
				
			}
			
		} else {
			
			echo 'Няма такава анкета!';
			
			header('refresh:2;url=index.php');
			
		}
		
	} else { header('Location: index.php'); }
	
}

function del($conn) {
	
	if(isset($_GET['id'])) {
		
		$checkPoll = mysqli_query($conn, "SELECT * FROM poll WHERE poll_id='".$_GET['id']."'");
		if(mysqli_num_rows($checkPoll) > 0) {
			
			mysqli_query($conn, "DELETE FROM poll WHERE poll_id='".$_GET['id']."'");
			mysqli_query($conn, "DELETE FROM poll_votes WHERE poll_id='".$_GET['id']."'");
			
			echo 'Успешно изтрихте анкетата!';
			
			header('refresh:2;url=index.php');
			
		} else {
			
			echo 'Няма такава анкета!';
			
			header('refresh:2;url=index.php');
			
		}
		
	} else { header('Location: index.php'); }
	
}