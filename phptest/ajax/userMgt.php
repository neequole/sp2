<?php
session_start();
include("../include/config.php");
echo "<h2 class='ribbonHeader'>USER MANAGEMENT . . .</h2>";

//student
$result = mysql_query("SELECT * FROM user u LEFT JOIN user_stud s ON (u.id=s.id) where u.type='student';") or die(mysql_error());
echo "<div id='loading2'></div>";
// Mysql_num_row is counting table row
$count=mysql_num_rows($result);
echo "<fieldset><legend>Student</legend>";
if($count>0){
echo "<table>";
echo "<tr><th>Username</th><th>Password</th><th>Name</th><th>Status</th><th>Action</th></tr>";
while($row = mysql_fetch_array($result)){
	if($row['stud_status']=="deactivated") echo "<tr id=".$row['id']."><td>".$row['usrname']."</td><td>".$row['pwd']."</td><td>".$row['fname']." ".$row['mname']." ".$row['lname']."</td><td>".$row['stud_status']."</td><td><input type='image' src='images/approve.jpg' alt='approve' id='approve_stud' width='10' height='10'/></td><td><input type='image' src='images/delete2.gif' name='".$row['id']."' class='delete_stud' width='10' height='10' alt='Delete'></td></tr>";
	else echo "<tr id=".$row['id']."><td>".$row['usrname']."</td><td>".$row['pwd']."</td><td>".$row['fname']." ".$row['mname']." ".$row['lname']."</td><td>".$row['stud_status']."</td><td><input type='image' src='images/delete2.gif' name='".$row['id']."' class='delete_stud' width='10' height='10' alt='Delete'></td></tr>";

	//print_r($row);
					//echo "<br/>";
}
echo "</table>";
}
else echo "No items available.";
echo "</fieldset>";

//faculty
$result = mysql_query("SELECT * FROM user u where u.type='faculty';") or die(mysql_error());

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);

echo "<fieldset><legend>Faculty</legend>";
echo "<div id='loading3'></div>";
if($count>0){
echo "<table id='facList'>";
echo "<tr><th>Username</th><th>Password</th><th>Name</th><th>Action</th></tr>";
while($row = mysql_fetch_array($result)){
	echo "<tr id=".$row['id']."><td>".$row['usrname']."</td><td>".$row['pwd']."</td><td>".$row['fname']." ".$row['mname']." ".$row['lname']."</td><td><input type='image' src='images/delete2.gif' name='".$row['id']."' class='delete_fac' width='10' height='10' alt='Delete'></td></tr>";
}
echo "</table>";
}
else echo "No items available.";
?>

<fieldset><legend>Add Faculty</legend>
<form method='POST' id='addFaculty'>
<table class='table_center'>
							<tr>
								<td><label for="username">Username*</label></td>
                                <td><input type="text" name="username"  class="required" placeholder="Username" pattern="[a-zA-Z0-9]+" required autofocus/></td>
                            </tr>
							<tr>
                                <td><label for="pwd">Password*</label></td>
								<td><input type="password" name="password"  class="required" placeholder="Password" pattern="[a-zA-Z0-9]+" required/></td>
                            </tr>
                            <tr>
                                <td><label for="fname">First name*</label></td>
                                <td><input type="text" name="fname"  class="required" placeholder="First Name" pattern="[A-Za-z ]+" required/></td>
                            </tr>
                            <tr>
                                <td><label for="mname">Middle name*</label></td>
                               <td><input type="text" name="mname"  class="required" placeholder="Middle Name" pattern="[A-Za-z ]+" required/></td>
                            </tr>
                            <tr>
                                <td><label for="lname">Last name*</label></td>
                               <td><input type="text" name="lname"  class="required" placeholder="Last Name" pattern="[A-Za-z ]+" required/></td>
                            </tr>
							<tr>
                                <td><label for="suffix">Suffix</label></td>
                               <td><input type="text" name="suffix"  placeholder="Suffix"/></td>
                            </tr>
							
							<tr>
                                <td><label for="email">E-mail*</label></td>
                                <td><input type="email" name="email"  class="required" placeholder="E-mail" required/></td>
                            </tr>
							<tr>
                                <td><label for="cnum">Contact No.*</label></td>
                                <td><input type="text" name="cnum"  class="required" pattern="[0-9]+" placeholder="Contact number" required/></td>
                            </tr>
							<tr>
                                <td><label for="sex">Sex*</label></td>
                                <td><input type="radio" name="sex" value="m" checked/>Male
								<input type="radio" name="sex" value="f"/>Female</td>
                            </tr>
							<tr>
								<td><input type='hidden' name='type' value='faculty'/></td>
							</tr>
							<tr>
							<td><input type='submit' value='Add'/></td>
							</tr>
</table>
</form>
</fieldset>
<div id='fac_result'></div>
</fieldset>

<div id="dialog-confirm3" title="Delete User?">
</div>
