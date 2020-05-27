<?php 
	include("include.php");

	function showTable($name){
		global $mysqli;

		echo "<font size=4>";
		echo '<table width="1000" border="1">';
  		echo '<tr><th>No.</th><th>Project Name</th><th>Description</th><th>Create Time</th></tr>';

		if($name == "all") {
  			$result = $mysqli->query("select pid, pname, pdescription, pcreatTime from project");
  		}
  		else{
  			$result = $mysqli->query("select p.pid, p.pname, p.pdescription, p.pcreatTime from user u left outer join lead l on u.uid = l.adminid left outer join project p on l.pid = p.pid where u.username = '$name' ");
  		}

		$i = 1;
		while ($row = $result->fetch_row()){
			if($row[0] != ""){
				echo '<tr>';
				echo '<td>'.$i.'</td>';
				echo '<td><a href=issue.php?pid='.$row[0].'>'.$row[1].'</a></td>';
				echo '<td>'.$row[2].'</td>';
				echo '<td>'.$row[3].'</td>';
				$i++;
			}
		}
  		echo '</table>';
  		echo "</font>";
	}

	function deleteProject($uid, $name){
		global $mysqli; 
		$result = $mysqli->query("select p.pid, pname from project p, lead l where p.pid = l.pid and l.adminid = '$uid' ");

		if($result->num_rows == 0){
            echo '<script language="javascript">';
            echo 'alert("You are not authorized to delete any project.")';
            echo '</script>';

            showTable($name);
        }
        else{
			echo "<font size=4>";
			echo '<form method="POST" onSubmit="return getVal(this)">';
	        echo '<fieldset>';
	        echo '<legend>Please select the projects to delete: </legend>';
	        while ($row = $result->fetch_row()){
	            echo '<input type="radio" name="projectid" value='.$row[0].'><label>'.$row[1].'</label><br>';
	        }
	        echo '</fieldset>';
	  		echo '<input type="submit" name="delete" value="Delete" id="btn_add">';
	  		echo '<a href=index.php id="btn_can">Cancel</a>';
	        echo '</form>';
	        echo "</font>";
        }
	}


	if(!isset($_SESSION["username"])) {
		if(isset($_SESSION["signup"]) && $_SESSION["signup"] == "success") {
            unset($_SESSION['signup']);
            echo '<body>';
            echo '<div style="text-align:center;background-color:#f1f1f1;font-family:monospace;height:100%;">';
            echo '<font color="black" size=20>';
			echo "Sign up success.<br>";
			echo 'Click <a href="index.php">here</a> to Login again.';
            echo '</font>';
            echo '</div>';
            echo '</body>';
		}
		else {	
            if(isset($_SESSION["login"]) && $_SESSION["login"] == "fail"){
                unset($_SESSION['login']);
                echo '<script language="javascript">';
                echo 'alert("Your email or password is incorrect, please try again.")';
                echo '</script>';
            }
            else if(isset($_SESSION["signup"]) && $_SESSION["signup"] == "fail"){
                unset($_SESSION['signup']);
                if(isset($_SESSION["check_email"]) && $_SESSION["check_email"] == "fail"){
                    unset($_SESSION['check_email']);
                    echo '<script language="javascript">';
                    echo 'alert("Please enter a valid email!")';
                    echo '</script>';
                }
                if(isset($_SESSION["check_repwd"]) && $_SESSION["check_repwd"] == "fail"){
                    unset($_SESSION['check_repwd']);
                    echo '<script language="javascript">';
                    echo 'alert("The passwords you entered do not match.")';
                    echo '</script>';
                }
                if(isset($_SESSION["check_pwd"]) && $_SESSION["check_pwd"] == "fail"){
                    unset($_SESSION['check_pwd']);
                    echo '<script language="javascript">';
                    echo 'alert("Use 6 or more characters for your password!")';
                    echo '</script>';
                }
                if(isset($_SESSION["exist_email"]) && $_SESSION["exist_email"] == "fail"){
                    unset($_SESSION['exist_email']);
                    echo '<script language="javascript">';
                    echo 'alert("That email already exists.")';
                    echo '</script>';
                }
                if(isset($_SESSION["exist_username"]) && $_SESSION["exist_username"] == "fail"){
                    unset($_SESSION['exist_username']);
                    echo '<script language="javascript">';
                    echo 'alert("That username already exists.")';
                    echo '</script>';
                }
            }

            //login
            echo '<body>';
            echo '<div style="text-align:left;background-color:rgb(0, 0, 255, 0.7);font-family:monospace;padding:30px;">';
            echo '<table align="right">';
			echo '<font color="white" size=20>DB-project</font>';
            echo '<form method="post" action="login.php">';
            echo '<tr><td><font color="white" size=5>Email:</font></td> <td><font color="white" size=5>Password:</font></td></tr>';
            echo '<tr><td><input type="text" name="email" size="40" required="required"/></td>';
            echo '<td><input type="password" name="password" size="40" required="required"/></td>';
            echo '<td><input type="submit" name="login" value="Login" id="btn_login"/></td></tr>';
            echo '</form>';
            echo '</table>';
            echo '</div>';

            //register
            echo '<div style="text-align:center;background-color:#f1f1f1;font-family:monospace;height:100%;">';
            echo '<table align="center" id="sign_up">';
            echo '<tr><td><font color="black" size=20>Sign Up</font></td></tr>';
            echo '<form method="post" action="register.php">';
            echo '<tr><td><font color="black" size=5>Email:</font></td><td><input type="text" name="email" size="40" required="required"/></td></tr>';
            echo '<tr><td><font color="black" size=5>Username:</font></td><td><input type="text" name="username" size="40" required="required"/></td></tr>';
            echo '<tr><td><font color="black" size=5>Password:</font></td><td><input type="password" name="password" placeholder="Use 6 or more characters" size="40" required="required"/></td></tr>';
            echo '<tr><td><font color="black" size=5>Confirm Password:</font></td><td><input type="password" name="repassword" placeholder="Use 6 or more characters" size="40" required="required"/></td></tr>';
            echo '<tr><td><input type="submit" name="signup" value="Register" id="btn_signup"/></td></tr>';
            echo '</form>';
            echo '</table>';
            echo '</div>';
            echo '</body>';
		}
	}
	else {
  		echo '<input type="button" value="Logout" onclick="location.href=\'logout.php\'" style="position:absolute;right:5px;top:5px;width:100px;height:30px;font-size:20px;font-family:monospace;" >';
		
		$uid = htmlspecialchars($_SESSION["uid"], ENT_QUOTES);
		$username = htmlspecialchars($_SESSION["username"], ENT_QUOTES);
        echo '<font size=5>Welcome '.$username.'</font><br><br>';    
        echo "<font size=4>";
        echo '<form method="POST">';
        echo '<input type="submit" value="My Projects" name="mypro" style="width:120px;height:40px;font-size:20px;">';
        echo '<input type="submit" value="All Projects" name="allpro" style="width:120px;height:40px;font-size:20px;">';
        echo '<input type="button" value="New Project" onclick="location.href=\'newProject.php\'"  style="position:absolute;left:925px;top:70px;width:90px;height:25px;background-color:#FF6347;color:#F5FFFA;">';
        echo '<input type="submit" value="Delete Project" name="delpro" style="position:absolute;left:830px;top:70px;width:90px;height:25px;background-color:#FF6347;color:#F5FFFA;">';
        echo '</form>';
  		echo "</font>";


  		//create a new project
  		if(isset($_POST["create"])){
			$pname = $_SESSION["pname"];
			$pdescription = $_SESSION["pdescription"];
			if($pname == ""){
				$pname = "New project";
			}
			$leaduid = $_SESSION['userid'];
            $status = $_SESSION['status'];
            $sid = array();
            
            //First, insert into status
            foreach ($status as $sname) {
            	$stmt = $mysqli->prepare("insert into status (sname) values (?)");
                $stmt->bind_param("s", $sname);
	    		$stmt->execute();
                array_push($sid, $mysqli->insert_id);
            }
            $workflow_start = $sid[0];
            $workflow_end = $sid[count($sid)-1];

            //Second, insert into legalTransition
			foreach ($status as $sname) {
                $i = array_search($sname, $status);
                $currentStatus = $sid[$i];
                $sname = preg_replace('/\s/', '', $sname);
            	$cbox = $_POST["{$sname}"];
                if(count($cbox) != 0){
                    foreach ($cbox as $key) {
                        $possibleStatus = $sid[$key];
                        $stmt = $mysqli->prepare("insert into legalTransition (currentStatus, possibleStatus) values (?, ?) ");
	                    $stmt->bind_param("ii", $currentStatus, $possibleStatus);
			    		$stmt->execute();
                    }
                }
                echo "<br>";
            }

            //Third, insert into project
		    $stmt = $mysqli->prepare("insert into project (pname, pdescription, pcreatTime, workflow_start, workflow_end) values (?, ?, NOW(), ?, ?)");
		    $stmt->bind_param("ssii", $pname, $pdescription, $workflow_start, $workflow_end);
		    $stmt->execute();
		    
			//Last, insert into the lead table           
            $pid = $mysqli->insert_id;
			$stmt = $mysqli->prepare("insert into lead (adminid, pid) values (?, ?)");
            $stmt->bind_param("ii", $uid, $pid);
            $stmt->execute();
            if(count($leaduid) != 0){
                foreach ($leaduid as $id) {
                    $stmt = $mysqli->prepare("insert into lead (adminid, pid) values (?, ?)");
                    $stmt->bind_param("ii", $id, $pid);
		    		$stmt->execute();
                }
            }

  			echo '<script language="javascript">';
  			echo 'alert("Project is created successfully!")';
  			echo '</script>';

  			showTable($username);
  		}
  		//check to delete any project
  		else if(isset($_POST["delpro"])){
  			deleteProject($uid, $username);
  		}
  		//delete the project
  		else if(isset($_POST["delete"])){
            $projectid = $_POST["projectid"];
            $stmt = $mysqli->prepare("delete from project where pid = ?");
            $stmt->bind_param("i", $projectid);
    		$stmt->execute();
            echo '<script language="javascript">';
            echo 'alert("Delete project success.")';
            echo '</script>';

            showTable($username);
  		}
  		//show all project
		else if(isset($_POST["allpro"])){
  			showTable("all");
  		}
  		//show the user's project
  		else {
  			showTable($username);
  		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>DB-project</title>
</head>
<style>

    body{ 
        margin: 0; 
        padding: 0; 
    }

    #sign_up td{
        height:40px;
    }

    #btn_login {
        color: white;
        background-color: blue;
        border-color: black;
        width: 45px;
        height: 25px;
        font-size: small;
    }

    #btn_signup {
        color: white;
        background-color: green;
        border-color: black;
        padding: 7px 14px;
        text-decoration:none;
        width: 100px;
        height: 30px;
        font-size: small;
    }

    #btn_add {
        margin: 11px 10px;
        color: #fff;
        background-color: #5bc0de;
        border-color: #5bc0de;
        padding: 5px 8px;
        width: 60px;
        height: 30px;
        font-size: small;
    }

    #btn_can {
        margin: 9px 10px;
        color: #fff;
        background-color: #5bc0de;
        border-color: #5bc0de;
        padding: 7px 10px;
        text-decoration:none;
        width: 60px;
        height: 30px;
        font-size: small;
    }

</style>

<script>
function getVal() {
  	if(confirm("Are you sure you want to delete this project?")){      	
  		return true;
  	}
  	else{
  		return false;
  	}
}
</script>

</html>