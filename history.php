<?php
    include("include.php");

    function showHistory($pid, $iid){
        global $mysqli;

        echo "<font size=4>";
        echo '<table width="1000" border="1">';
        echo '<tr><th>Issue Title</th><th>Description</th><th>Update to Status</th><th>Updater</th><th>Update Time</th></tr>';
        $result = $mysqli->query("select iid, htitle, hdescription, sname, username, updateTime from history h, status s, user u where h.iid = '$iid' and h.update_to_sid = s.sid and h.uid = u.uid order by updateTime desc");

        if($result){
            while ($row = $result->fetch_row()){
                if($row[0] != ""){
                    echo '<tr>';
                    echo '<td>'.$row[1].'</td>';
                    echo '<td>'.$row[2].'</td>';
                    echo '<td>'.$row[3].'</td>';
                    echo '<td>'.$row[4].'</td>';
                    echo '<td>'.$row[5].'</td>';
                }
            }
        }
        echo "</table>";
        echo '<br><a href=issue.php?pid='.$pid.' id="btn_goback">Go Back</a>';
        echo "</font>";
    }

    function newHistory($pid, $iid){
        global $mysqli;
        //get the issue
        $result = $mysqli->query("select title, idescription, currentStatus from issue where iid = '$iid' ");       
        $row = $result->fetch_assoc();
        $sid = $row['currentStatus'];
        $result_sid = $mysqli->query("select sid, sname from status where sid in (select possibleStatus from legalTransition where currentStatus = '$sid') ");
        $status_table = $mysqli->query("select distinct sid, sname from project, status where pid = '$pid' and sid >= workflow_start and sid <= workflow_end");

        echo "<font size=4>";
        echo '<form method="POST">';
        echo "<table>";
        echo '<tr><td><label for="title"> Issue Title: </label></td>';
        echo "<td><input type=text name=title value=\"".$row["title"]."\"></td></tr>";

        echo '<tr><td style="vertical-align:text-top;"><label for="idescription"> Description: </label></td>';
        echo '<td><textarea cols=100 rows=10 name=idescription>';
        echo $row["idescription"];
        echo '</textarea></td></tr>';
        echo "</table>";

        echo '<tr><td><label for="status">Choose the status: </label></td>';
        echo '<td><select name="status" required><option value="">--Please choose the status--</option>';
        
        while ($row_s = $result_sid->fetch_row()){
            if($row_s[0] != ""){
                echo '<option value='.$row_s[0].'>'.$row_s[1].'</option>';
            }
        }
        echo '</select></td></tr>';

        echo "<br>";
        echo '<input type="submit" name="edit" value="Edit" id="btn_add">';
        echo '<a href=history.php?iid='.$iid.' id="btn_can">Cancel</a>';

        echo '<table width="500" border="1" style="margin-left:auto; margin-right:auto;top:300px;text-align:center;font-family:monospace;">';
        echo '<tr><th>Current Status</th><th>Next Possible Status</th></tr>';
        while ($t = $status_table->fetch_row()){
            $row = "";
            $i = 1;
            echo '<tr><td><label for="cur_sname">'.$t[1].'</label></td>';
            $result_sid = $mysqli->query("select sname from status where sid in (select possibleStatus from legalTransition where currentStatus = ".$t[0].") ");
            if($result_sid){
                while ($row_s = $result_sid->fetch_row()){
                    if($i){
                        $row = $row_s[0];
                        $i--;
                        continue;
                    }
                    $row = $row." or ".$row_s[0];
                }
            }
            echo '<td><label for="next_sname">'.$row.'</label></td></tr>';
        }
            
        echo "</table>";
        echo '</form>';
        echo "</font>";
    }

    function assignUser($pid, $iid){
        global $mysqli;
        //get the user who is not the lead or the assignee
        $result = $mysqli->query("select uid, username from user where uid not in (select uid from assignee where iid = '$iid') and uid not in (select adminid from lead where pid = '$pid')");
        
        if($result->num_rows == 0){
            echo '<script language="javascript">';
            echo 'alert("All users are already authorized.")';
            echo '</script>';

            showHistory($pid, $iid);
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
            echo '<a href=history.php?iid='.$iid.' id="btn_can">Cancel</a>';
            echo '</form>';
            echo "</font>";
        }
    }

    if(isset($_GET['iid'])) {

        echo '<input type="button" value="Logout" onclick="location.href=\'logout.php\'" style="position:absolute;right:5px;top:5px;width:100px;height:30px;font-size:20px;font-family:monospace;">';
        echo '<form method="POST">';
        echo '<input type="submit" value="Assign User" name="assuser" style="position:absolute;left:830px;top:80px;width:90px;height:25px;background-color:#FF6347;color:#F5FFFA;">';
        echo '<input type="submit" value="Edit Issue" name="editiss" style="position:absolute;left:925px;top:80px;width:90px;height:25px;background-color:#FF6347;color:#F5FFFA;">';
        echo '</form>';

        $iid = htmlspecialchars($_GET['iid'], ENT_QUOTES);
        $uid = htmlspecialchars($_SESSION["uid"], ENT_QUOTES);
        $username = htmlspecialchars($_SESSION["username"], ENT_QUOTES);
        echo "<font size=4>";
        echo "Welcome $username. <br><br>";
        echo '<a href="index.php">Home</a> / ';
        echo "</font>"; 

        //if iid is valid
        if($result = $mysqli->query("select pid, title from issue where iid = '$iid' ")){
            $row = $result->fetch_assoc();
            $pid = $row['pid'];
            $title = $row['title'];
            echo "<font size=4>";
            echo '<a href=issue.php?pid='.$pid.'>Porject\'s Issue</a> / History <br>';
            echo $title.' History: <br><br>';
            echo "</font>";     

            //check if user is the lead of this project
            $lead_result = $mysqli->query("select adminid from lead where adminid = '$uid' and pid = '$pid' ");
            //check if user is the assignee of this issue
            $assignee_result = $mysqli->query("select uid from assignee where uid = '$uid' and iid = '$iid' ");

            //check if this user can assign other users on this issue
            if(isset($_POST["assuser"])) {
                if($lead_result->num_rows != 0){
                    assignUser($pid, $iid);
                }
                else{
                    echo '<script language="javascript">';
                    echo 'alert("You are not authorized to assign any user on this project.")';
                    echo '</script>';

                    showHistory($pid, $iid);
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

                showHistory($pid, $iid);
            }
            //start to edit the issue
            else if(isset($_POST["editiss"])) {
                if($lead_result->num_rows != 0 || $assignee_result->num_rows != 0) {
                    newHistory($pid, $iid);
                }
                else{
                    echo '<script language="javascript">';
                    echo 'alert("You are not authorized to edit this issue.")';
                    echo '</script>';

                    showHistory($pid, $iid);
                }
            }
            //edit the issue
            else if(isset($_POST["edit"])) {
                $title = $_POST["title"];
                $idescription = $_POST["idescription"];
                $sid = $_POST["status"];
                
                $stmt = $mysqli->prepare("insert into history (iid, htitle, hdescription, update_to_sid, uid, updateTime) values (?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("issis", $iid, $title, $idescription, $sid, $uid);
                $stmt->execute();

                $stmt = $mysqli->prepare("update issue set title = ?, idescription = ?, currentStatus = ? where iid = ? ");
                $stmt->bind_param("ssii", $title, $idescription, $sid, $iid);
                $stmt->execute();

                echo '<script language="javascript">';
                echo 'alert("Issue is edited successfully!")';
                echo '</script>';

                showHistory($pid, $iid);
            }
            else{
                showHistory($pid, $iid);
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>History</title>
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