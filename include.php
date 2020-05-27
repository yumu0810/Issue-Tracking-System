<?php
// start session, check session IP with client IP, if no match start a new session
session_start();
if(isset($SESSION["REMOTE_ADDR"]) && $SESSION["REMOTE_ADDR"] != $SERVER["REMOTE_ADDR"]) {
  	session_destroy();
  	session_start();
}

$mysqli = new mysqli("host.docker.internal", "root", "1234", "tracking_sys", 3306);
if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

?>
