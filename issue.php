<?php
    include("include.php");

    function showIssue($attr, $key, $pid){
        global $mysqli;

        echo "<font size=4>";
        echo '<table width="1000" border="1">';
        echo '<tr><th>No.</th><th>Issue Title</th><th>Description</th><th>Current Status</th><th>Reporter</th><th>Report Time</th></tr>';
        if($key == ""){
            $result = $mysqli->prepare("select iid, title, idescription, sname, username, reportTime from issue i, user u, status s where pid = ? and u.uid = i.reporter and i.currentStatus = s.sid");
            $result->bind_param("i", $pid);
            $result->execute();
            $result->bind_result($iid, $title, $idescription, $sname, $username, $reportTime);
        }
        else{
            $query = "select iid, title, idescription, sname, username, reportTime from issue i, user u, status s where pid = ? and u.uid = i.reporter and i.currentStatus = s.sid and (";
            $arr = array();
            array_push($arr, $pid);
            $search = '%'.$key.'%';
            if(count($attr) != 0){
                foreach ($attr as $t) {
                    if($t == "all"){
                        $query = $query."title LIKE ? or idescription LIKE ? or sname LIKE ? or username LIKE ? or year(reportTime) = ?";
                        break;
                    }
                    else{
                        if(substr($query, -1) != '('){
                            $query = $query." or ";
                        }

                        if($t == "title"){
                            $query = $query."title LIKE ?";
                            array_push($arr, $search);
                        }
                        if($t == "description"){
                            $query = $query."idescription LIKE ?";
                            array_push($arr, $search);
                        }
                        if($t == "status"){
                            $query = $query."sname LIKE ?";
                            array_push($arr, $search);
                        }
                        if($t == "reporter"){
                            $query = $query."username LIKE ?";
                            array_push($arr, $search);
                        }
                        if($t == "time"){
                            $query = $query."year(reportTime) = ?";
                            array_push($arr, $key);
                        }
                    }
                }
            }
            else{
                $query = $query."title LIKE ? or idescription LIKE ? or sname LIKE ? or username LIKE ? or year(reportTime) = ?";
            }
            $query = $query.")";
            $result = $mysqli->prepare($query);
            if(count($attr) != 0){
                if(in_array("all", $attr)){
                    $result->bind_param("issssi", $pid, $search, $search, $search, $search, $key);
                }
                else{
                    if(in_array("time", $attr)){
                        $result->bind_param('i'.str_repeat('s', count($attr)-1).'i', ...$arr);
                    }
                    else{
                        $result->bind_param('i'.str_repeat('s', count($attr)), ...$arr);
                    }
                }
            }
            else{
                $result->bind_param("issssi", $pid, $search, $search, $search, $search, $key);
            }
            $result->execute();
            $result->bind_result($iid, $title, $idescription, $sname, $username, $reportTime);
        }

        $i = 1;
        while($result->fetch()){
            echo '<tr>';
            echo '<td>'.$i.'</td>';
            echo '<td><a href=history.php?iid='.$iid.'>'.$title.''.'</td>';
            echo '<td>'.$idescription.'</td>';
            echo '<td>'.$sname.'</td>';
            echo '<td>'.$username.'</td>';
            echo '<td>'.$reportTime.'</td>';
            $i++;
        }
        
        echo "</table>";
        echo '<br><a href=index.php id="btn_goback">Go Back</a>';

        echo '<form method="post" style="position:absolute;top:90px;left:1020px;">';
        echo '<input type="checkbox" name="search[]" value="all"><label>Search All</label><br>';
        echo '<input type="checkbox" name="search[]" value="title"><label>Issue Title</label><br>';
        echo '<input type="checkbox" name="search[]" value="description"><label>Description</label><br>';
        echo '<input type="checkbox" name="search[]" value="status"><label>Current Status</label><br>';
        echo'<input type="checkbox" name="search[]" value="reporter"><label>Reporter</label><br>';
        echo '<input type="checkbox" name="search[]" value="time"><label>Time(Year)</label><br>';
        echo '<input type="text" name="keyword" placeholder="Keyword" />';
        echo '<input type="hidden" name="action" value="Search">';
        echo '<input type="image" src="search.png" alt="Search" style="float:right" width="20" height="20"/>';
        echo '</form>';
        echo "</font>";
    }

    function newIssue($pid){
        global $mysqli;
        //get the project workflow start id
        $result = $mysqli->query("select sid, sname from project, status where pid = '$pid' and workflow_start = sid ");
        $row = $result->fetch_assoc();
        $sid = $row["sid"];
        $sname = $row["sname"];

        echo "<font size=4>";
        echo '<form method="POST">';
        echo "<table>";
        echo '<tr><td><label for="title"> Issue Title: </label></td>';
        echo '<td><input type="text" name="title"></td></tr>';

        echo '<tr><td style="vertical-align:text-top;"><label for="idescription"> Description: </label></td>';
        echo '<td><textarea cols="100" rows="10" name="idescription" placeholder="Enter your description here"></textarea></td></tr>';

        echo '<tr><td><label for="status">Choose the status: </label></td>';
        echo '<td><select name="status" required>';
        echo '<option value="">--Please choose the status--</option>';
        echo '<option value='.$sid.'>'.$sname.'</option>';
        echo '</select></td></tr>';

        echo "</table>";
        echo '<input type="submit" name="add" value="Create" id="btn_add">';
        echo '<a href=issue.php?pid='.$pid.' id="btn_can">Cancel</a>';
        echo '</form>';
        echo "</font>";
    }

    function assignLead($pid){
        global $mysqli;
        //get the user who is not the lead or the assignee
        $result = $mysqli->query("select uid, username from user where uid not in (select adminid from lead where pid = '$pid')");
        
        if($result->num_rows == 0){
            echo '<script language="javascript">';
            echo 'alert("All users are already lead this project.")';
            echo '</script>';

            showIssue("", "", $pid);
        }
        else{
            echo "<font size=4>";
            echo '<form method="POST">';

            echo '<fieldset>';
            echo '<legend>Please select the users to assign: </legend>';
            while ($row = $result->fetch_row()){
                echo '<input type="checkbox" name="userid[]" value='.$row[0].'><label>'.$row[1].'</label><br>';
            }
            echo '</fieldset>';

            echo '<input type="submit" name="ass" value="Assign" id="btn_add">';
            echo '<a href=issue.php?pid='.$pid.' id="btn_can">Cancel</a>';
            echo '</form>';
            echo "</font>";
        }
    }

    if(isset($_GET['pid'])) {
        echo '<input type="button" value="Logout" onclick="location.href=\'logout.php\'" style="position:absolute;right:5px;top:5px;width:100px;height:30px;font-size:20px;font-family:monospace;" >';      
        echo '<form method="POST">';
        echo '<input type="submit" value="Assign Lead" name="asslead" style="position:absolute;left:805px;top:80px;width:90px;height:25px;background-color:#FF6347;color:#F5FFFA;">';
        echo '<input type="submit" value="Report Issue" name="addiss" style="position:absolute;left:900px;top:80px;width:90px;height:25px;background-color:#FF6347;color:#F5FFFA;">';
        echo '</form>';

        $pid = htmlspecialchars($_GET['pid'], ENT_QUOTES);
        $uid = htmlspecialchars($_SESSION["uid"], ENT_QUOTES);
        $username = htmlspecialchars($_SESSION["username"], ENT_QUOTES);
        echo "<font size=4>";
        echo "Welcome $username. <br><br>";
        echo '<a href="index.php">Home</a> / Project\'s Issue <br>';
        echo "</font>";

        //if pid is valid
        if($result = $mysqli->query("select pname from project where pid = '$pid'")){
            echo '<font size=4>'.$result->fetch_row()[0]." Listing: </font><br><br>";

            //check if user is the lead of this project
            $lead_result = $mysqli->query("select adminid from lead where adminid = '$uid' and pid = '$pid' ");

            //check if this user can assign other users on this issue
            if(isset($_POST["asslead"])) {
                if($lead_result->num_rows != 0){
                    assignLead($pid);
                }
                else{
                    echo '<script language="javascript">';
                    echo 'alert("You are not authorized to assign any user to be the lead on this project.")';
                    echo '</script>';

                    showIssue("", "", $pid);
                }
            }
            //assign user
            else if(isset($_POST["ass"])) {
                $userid = $_POST["userid"];
                if(count($userid) != 0){
                    foreach ($userid as $id) {
                        $stmt = $mysqli->prepare("insert into assignee (uid, iid, adminid, assignTime) values (?, ?, ?, NOW())");
                        $stmt->bind_param("iii", $id, $iid, $uid);
                        $stmt->execute();
                    }
                }
                showIssue("", "", $pid);
            }
            //search
            else if($_POST['action'] == "Search"){
                $attr = $_POST['search'];
                $key = htmlspecialchars($_POST['keyword'], ENT_QUOTES);
                showIssue($attr, $key, $pid);
            }
            //start to report a new issue
            else if(isset($_POST["addiss"])) {
                newIssue($pid);
            }
            //report a new issue
            else if(isset($_POST["add"])) {
                $title = $_POST["title"];
                $idescription = $_POST["idescription"];
                $sid = $_POST["status"];
                if($title == ""){
                    $title = "New issue";
                }

                $stmt = $mysqli->prepare("insert into issue (pid, reporter, reportTime, title, idescription, currentStatus) values (?, ?, NOW(), ?, ?, ?)");
                $stmt->bind_param("iissi", $pid, $uid, $title, $idescription, $sid);
                $stmt->execute();

                //insert into the history table
                $iid = $mysqli->insert_id;
                $t = $mysqli->query("select reportTime from issue where iid = '$iid'");
                $time = $t->fetch_row()[0];
                $stmt = $mysqli->prepare("insert into history (iid, htitle, hdescription, update_to_sid, uid, updateTime) values (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issiis", $iid, $title, $idescription, $sid, $uid, $time);
                $stmt->execute();

                echo '<script language="javascript">';
                echo 'alert("Issue is reported successfully!")';
                echo '</script>';

                showIssue("", "", $pid);
            }
            else{
                showIssue("", "", $pid);
            }
        }
    } 

?>

<!DOCTYPE html>
<html>
<head>
    <title>Issue</title>
</head>
<style>

    #btn_goback {
        color: #fff;
        background-color: #5bc0de;
        border-color: #5bc0de;
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

</html>


