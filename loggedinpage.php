<?php
	
	session_start();

	if (array_key_exists("id", $_COOKIE)) {
		$_SESSION['id'] = $_COOKIE['id'];
		echo "cookie variable exists - ".$_SESSION['id']."<br>";
	}

	if (array_key_exists("id", $_SESSION)) {
		echo "<p>successfully logged in! <a href=\"index.php?logout=1\">Log out</a></p>";
		echo "session variable exists - ".$_SESSION['id']."<br>";
	} else {
		header("Location: index.php");
	}

?>