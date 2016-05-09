<?php 
	
	session_start();

	$error = "";

	if (array_key_exists('logout', $_GET)) {

		$_SESSION = Array();
		setcookie("id", "", time() - 60*60);
		$_COOKIE["id"] = "";

	} else if ((array_key_exists('id', $_SESSION) AND $_SESSION['id']) OR (array_key_exists('id', $_COOKIE) AND $_COOKIE['id'])) {

		header("Location: loggedinpage.php");
	}

	if (array_key_exists("submit", $_POST)) {

		$server = "<server_name>";
		$username = "<user_name>";
		$password = "<password>";
		$dbname = "<database_name>";

		$link = mysqli_connect($server, $username, $password, $dbname);

		if (mysqli_connect_error()) {
			die("Error in connection to database");
		}

		if (!$_POST['email']) {
			$error .= "An email address is required!<br>";
		}
		if (!$_POST['password']) {
			$error .= "password field cannot be kept empty";
		}

		if ($error != "") {
			$error = "<p>There were some error(s) in the form :<p>".$error;
		} else {

			if ($_POST['signUp'] == '1') {

				$email = mysqli_real_escape_string($link, $_POST['email']);
				$password = mysqli_real_escape_string($link, $_POST['password']);

				$query = "SELECT id FROM `".$dbname."` WHERE email = '$email' LIMIT 1";

				$result = mysqli_query($link, $query);

				if (mysqli_num_rows($result) > 0) {
					$error = "That email address is already taken";
				} else {

					$query = "INSERT INTO `".$dbname."` (`email`, `password`) VALUES ('$email', '$password')";

					if (!mysqli_query($link, $query)) {
						$error = "<p>Could not sign you up, pls try again later.<p>";
					} else {

						$id = intVal(mysqli_insert_id($link));
						$hashedPassword = md5(md5(mysqli_insert_id($link)).$_POST['password']);

						$query = "UPDATE `".$dbaname."` SET password = '$hashedPassword' WHERE id = $id LIMIT 1";

						mysqli_query($link, $query);

						$_SESSION['id'] = $id;

						if ($_POST['stayLoggedIn'] == 1) {

							setcookie("id", $id, time() + 60*60*24*365);

						}

						header("Location: loggedinpage.php");

					}

				}

			}

			else if ($_POST['signUp'] == '0') {

				$email = mysqli_real_escape_string($link, $_POST['email']);
				$query = "SELECT * FROM `".$dbaname."` WHERE email = '$email'";

				$result = mysqli_query($link, $query);
				$row = mysqli_fetch_array($result);


				if (isset($row)) {

					$hashedPassword = md5(md5(($row['id'])).$_POST['password']);

					if ($hashedPassword == $row['password']) {
						$_SESSION['id'] = $row['id'];
						if ($_POST['stayLoggedIn'] == '1') {
							setcookie("id", $row['id'], time() + 60*60*24*365);
						}
					}

					header("Location: loggedinpage.php");
				} else {

					$error = "email/password combination could not be found :(";

				}

			}

		}

	}




?>

<div id="error"><?php echo $error ?></div>

<form method="post">

	<input type="email" name="email" placeholder="Email"></input>

	<input type="password" name="password"	placeholder="password"></input>

	<input type="checkbox" name="stayLoggedIn" value="1"></input>

	<input type="hidden" name="signUp" value="1"></input>

	<input type="submit" name="submit" value="Sign Up"></input>

</form>

<form method="post">

	<input type="email" name="email" placeholder="Email"></input>

	<input type="password" name="password"	placeholder="password"></input>

	<input type="checkbox" name="stayLoggedIn" value="1"></input>

	<input type="hidden" name="signUp" value="0"></input>

	<input type="submit" name="submit" value="Log In"></input>

</form>
