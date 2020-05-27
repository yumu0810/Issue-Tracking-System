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
    else if(isset($_POST["signup"])){
        $check = 0;
        $_SESSION["signup"] = "fail";
        //check if email is valid
        $check_email = $_POST["email"];
        if(!filter_var($check_email, FILTER_VALIDATE_EMAIL)){
            $_SESSION["check_email"] = "fail";
            $check++;
	    }    	
	    // check password == confirm password
        if($_POST["password"] != $_POST["repassword"]){
            $_SESSION["check_repwd"] = "fail";
            $check++;
        }
    	//check for the password length
        if(strlen($_POST["password"]) < 6) {
            $_SESSION["check_pwd"] = "fail";
            $check++;
        }

        if($stmt = $mysqli->prepare("select email, username from user where email = ? or username = ? limit 1")){
            $stmt->bind_param("ss", $_POST["email"], $_POST["username"]);
	      	$stmt->execute();
	      	$stmt->bind_result($email, $username);

	      	if($stmt->fetch()){
	            //check if email already exists in database
	            if($email == $_POST["email"]){
                    $_SESSION["exist_email"] = "fail";
                    $check++;
	            }
	            //check if username already exists in database
	            if($username == $_POST["username"]){
                    $_SESSION["exist_username"] = "fail";
                    $check++;
	            }
	        }
	        else if($check == 0){
	            //encrypt the password before saving in the database
	            if($stmt = $mysqli->prepare("insert into user (email, username, password) values (?, ?, ?)")){
	            	$stmt->bind_param("sss", $_POST["email"], $_POST["username"], md5($_POST["password"]));
	          		$stmt->execute();
	                $_SESSION["signup"] = "success";
	            }
	        }
	    }
        toIndex();
    }
?>

<!DOCTYPE html>
<html>
<head>
  <title>Sign Up</title>
</head>
</html>