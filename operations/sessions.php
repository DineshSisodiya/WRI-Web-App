<?php 
session_start();
if (empty($_SESSION['session_life']) or empty($_SESSION['loggedInUser'])) {
  header('Location: login.php');
} else {
	if ($_SESSION['loggedInUser'] == md5(session_id()) and time() < $_SESSION['session_life']) {
  		$_SESSION['session_life']=time()+60; //increase session life by 1min
	}
}

?>