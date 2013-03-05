<?php
session_start();
?>
<form name="form_login" method="post" action="php/checklogin.php">
                    Username: <input type="text" name="myusername" id="myusername"/>
                    Password: <input type="password" name="mypwd" id="mypwd"/>
                    <input name="login" type="submit" value="Login"/>
					<a href="register.php">Register here!</a>
</form>
<?php
					if(isset($_GET['error']) && strcmp($_GET['error'],'login')==0)
						echo "Incorrect username or password.";
?>