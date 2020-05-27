<?php 
    include("include.php");

    if(!isset($_SESSION["username"])){
        header("Location: index.php");
    }
    else {
        $uid = htmlspecialchars($_SESSION["uid"], ENT_QUOTES);
        $username = htmlspecialchars($_SESSION["username"], ENT_QUOTES);
        echo "<font size=4>";
        echo "Welcome $username. <br><br>";
        echo "</font>";
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>New Project</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript">

function add_row()
{
 $rowno = $("#pro tr").length;
 $rowno = $rowno + 1;
 $("#pro tr:last").after("<tr id='row" + $rowno + "'><td></td><td><input type='text' name='status[]' placeholder='Enter status name' required><input type='button' value='DELETE' onclick=delete_row('row" + $rowno + "')></td></tr>");
}

function delete_row(rowno)
{
 $('#'+rowno).remove();
}
function getVal() {
    if(confirm("Are you sure to go to the next step?")){       
        return true;
    }
    else{
        return false;
    }
}

</script>
</head>

<body>
    <input type="button" value="Logout" onclick="location.href='logout.php'" style="position:absolute;right:5px;top:5px;width:100px;height:30px;font-size:20px;font-family:monospace;" >

    <form method="post" action="transition.php" onSubmit="return getVal(this)">
        <table id="pro">
            <tr>
            <td><label for="pname"> Project Name: </label></td>
            <td><input type="text" name="pname"></td>
            </tr>

            <tr>
                <td style="vertical-align:text-top;"><label for="description"> Description: </label></td>
                <td><textarea cols="100" rows="10" name="pdescription" placeholder="Enter your description here"></textarea></td>
            </tr>

            <tr>
                <td><label for="leader"> Lead of the project: </label></td>
                <?php 
                    $uid = htmlspecialchars($_SESSION["uid"]);
                    $result = $mysqli->query("select uid, username from user where uid != '$uid'");
                    echo '<td>';
                    while ($row = $result->fetch_row()){
                        echo '<input type="checkbox" name="userid[]" value='.$row[0].'><label>'.$row[1].'</label>';
                    }
                    echo '</td>';
                ?>
            </tr>

            <tr>
                <td id="status"><label for="status"> Workflow's status: </label></td>
                <td><input type="text" name="status[]" placeholder="Enter status name" required></td>                
            </tr>
        </table>
        <input type="button" onclick="add_row();" value="Add status" style="position:absolute;left:350px;top:245px;background-color:#F5F5F5;color:#483D8B;">
        <input type="submit" name="next" value="Next">
        <input type="button" value="Cancel" onclick="location.href='index.php'">
    </form>
</body>
</html>