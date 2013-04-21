<?php
session_start();
include("../include/config.php");
echo "<h2 class='ribbonHeader'>ASSOCIATE CLASS . . .</h2>";

?>
<div id='class_result' class='returnBox'></div>
<form method="POST" action="php/createClass.php" id='assocClassForm'>
		<table class="table_center">
			<tr><th>Class Details</th><th>Fields with * are required</th></tr>
			<tr><td>Course Title:*</td><td><input type="text" name="cTitle" pattern="[a-zA-Z]+" required/></td></tr>
			<tr><td>Course Number:*</td><td><input type="text" name="cNum" pattern="[0-9]+" required/></td></tr>
			<tr><td>Lecture Section:*</td><td><input type="text" name="cLec" pattern="[a-zA-Z]+" required/></td></tr>
			<tr><td>Recit/Lab Section:</td><td><input type="text" name="cLab" pattern="[0-9][a-zA-Z]" /></td></tr>
			<tr><td>Term:*</td><td><select name="cTerm" required><option value="1st Semester">1st Semester</option><option value="2nd Semester">2nd Semester</option><option value="Summer">Summer</option></select></td></tr>
			<tr><td>Academic Year:*</td><td><select name="cAcadYear" required><option value="<?php echo date('Y')-1 ." - ". date('Y');?>"><?php echo date('Y')-1 ." - ". date('Y');?></option><option value="<?php echo date('Y') ." - ". date('Y',strtotime('+1 year'));?>"><?php echo date('Y') ." - ". date('Y',strtotime('+1 year'));?></option></td></tr>
			<tr><td>Comment:</td><td><textarea name="cComment"></textarea></td></tr>
			<tr><td>Events:*</td><td></td></tr>
			<?php
			$sql = mysql_query("SELECT * FROM event") or die(mysql_error());
			$count=mysql_num_rows($sql);
			if($count>0){
				while($row = mysql_fetch_array($sql)){
					echo "<tr><td><input type='checkbox' name='eventCourse[]' value='".$row['id']."'></td><td>".$row['title']."</td></tr>";
				}
			}
			?>
			<tr>
			<td>Faculty:*</td><td><select name='facultyAssoc' required>
			
			<?php
			$sql = mysql_query("SELECT * FROM user where type='faculty'") or die(mysql_error());
			$count=mysql_num_rows($sql);
			if($count>0){
				while($row = mysql_fetch_array($sql)){
					echo "<option value='".$row['id']."'>Prof.".substr($row['fname'],0,1).".".substr($row['mname'],0,1).".".$row['lname']."</option>";
				}
			}
			?>
			</select></td></tr>
			<tr><td></td><td><input type="submit" value="Create Class"/></td></tr>
		</table>
		
	</form>