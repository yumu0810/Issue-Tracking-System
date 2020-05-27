<?php
	session_start();
	session_destroy();
  	header("refresh: 5; index.php");
	echo '<body>';
    echo '<div style="text-align:center;background-color:#f1f1f1;font-family:monospace;height:100%;">';
    echo '<font color="black" size=20>';
	echo "You are logged out. <br> You will be redirected in 5 seconds. <br>";
	echo 'If not, click <a href="index.php">here</a> back to homepage.';
    echo '</font>';
    echo '</div>';
    echo '</body>';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
</head>
<style>
    body{ 
        margin: 0; 
        padding: 0; 
    }
</style>
</html>