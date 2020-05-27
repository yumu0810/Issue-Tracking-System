<?php 
    include("include.php");

    if(!isset($_SESSION["username"])){
        header("Location: index.php");
    }
    else if(isset($_POST["next"])){
        $username = htmlspecialchars($_SESSION["username"], ENT_QUOTES);
        echo "<font size=4>";
        echo "Welcome $username. <br><br>";


        echo "Set your next possible status: <br>";
        unset($_SESSION['pname']);
        unset($_SESSION['pdescription']);
        unset($_SESSION['userid']);
        unset($_SESSION['status']);
        $_SESSION["pname"] = $_POST["pname"];
        $_SESSION["pdescription"] = $_POST["pdescription"];
        $_SESSION['userid'] = $_POST['userid'];
        $_SESSION['status'] = $_POST['status'];
        $status = $_POST['status'];
        echo '<form method="post" onSubmit="return getVal(this)" action="index.php"><table width=1000>';

        foreach ($status as $sid) {
            $sid = preg_replace('/\s/', '', $sid);
            echo '<tr><td>'.$sid.': </td>';
            foreach ($status as $t => $key) {
                echo '<td><input type="checkbox" name="'.$sid.'[]" value='.$t.'><label>'.$key.'</label></td>';
            }
        }
        echo "</table>";
        echo '<input type="submit" name="create" value="Create">';
        echo '<input type="button" value="Cancel" onclick="location.href=\'index.php\'">';
        echo "</form>";
        echo "</font>";
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Set Status</title>
</head>
<script>
function getVal() {
    if(confirm("Are you sure to finish this step?")){       
        return true;
    }
    else{
        return false;
    }
}
</script>
<body>
    <input type="button" value="Logout" onclick="location.href='logout.php'" style="position:absolute;right:5px;top:5px;width:100px;height:30px;font-size:20px;font-family:monospace;" >
</body>
</html>