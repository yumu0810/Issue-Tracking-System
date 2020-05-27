<?php 
	include("include.php");

    function toIndex(){
    	header("Location: index.php");
    }

	//if the user is already logged in, redirect them back to homepage
	if(isset($_SESSION["username"])) {
      	header("refresh: 3; url=index.php");
		echo "<font size=4>";
		echo "You are already logged in. <br>";
		echo 'You will be redirected in 3 seconds or click <a href="index.php">here</a>. <br><br>';	
		echo "</font>";	
	}
	else if(isset($_POST["login"])) {
		if ($stmt = $mysqli->prepare("select uid, username from user where email = ? and password = ? ")){
			$stmt->bind_param("ss", $_POST["email"], md5($_POST["password"]));
	      	$stmt->execute();
	      	$stmt->bind_result($uid, $username);

			//log_in success
			if($stmt->fetch()){
	            $_SESSION["uid"] = $uid;
			  	$_SESSION["username"] = $username;
			  	//store clients IP address to help prevent session hijack
			  	$_SESSION["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"]; 
	          	toIndex();
			}
			else {
				//pause a bit to help prevent brute force attacks
			  	sleep(1);
			  	$_SESSION["login"] = "fail";
	            toIndex();
			}
			$stmt->close();
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
</head>
</html>