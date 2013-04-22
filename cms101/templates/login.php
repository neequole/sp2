<?php
/*
*SHOWS THE LOGIN DIV which includes the LOG-IN and REGISTER tab
*/
	include('include/myScript.jsp');
?>
<div id="tabs">
     <ul>
     <li><a href="#tabs-1">Log-in</a></li>
     <li><a href="#tabs-2">Join us!</a></li>
     </ul>
     <div id="tabs-1">
     	
     	<p>Log-in to your account.</p>
        <form>
        <table>
        <tr><td><h5>E-mail:</h5></td><td><input type="text" id="log_email"/></td></tr>
        <tr><td><h5>Password:</h5></td><td><input type="password" id="log_pwd"/></td></tr>
        <tr><td><input type="button" class="loginButton" value="Log-in"/></td></tr>
        </table>
        </form>
     </div>
     <div id="tabs-2">
        <p>Create an account.</p>
        <p class='smallText'>Creating a SyS account is easy!</p>
        <form>
        <table>
        <tr><td>E-mail:</td><td><input type="text" name="email" id="reg_email"/></td></tr>
    	<tr><td>Password:</td><td><input type="password" name="password" id="reg_pwd"/></td></tr>
       	<tr><td>Firstname:</td><td><input type="text" name="fname" id="reg_fname"/></td></tr>
        <tr><td>Middlename:</td><td><input type="text" name="mname" id="reg_mname"/></td></tr>
    	<tr><td>Lastname:</td><td><input type="text" name="lname" id="reg_lname"/></td></tr>
    	<tr><td>Enter Code:</td><td><input type="text" name="captcha" id="captcha"/></td><td><img src="include/captcha.php"></td></tr>
        <tr><td><input type="button" class='register' value='Join!'/></td></tr>
        </table>
        </form>
        <p class='smallText'>*Password must be at least 6 characters long.</h6>
     </div>
</div>