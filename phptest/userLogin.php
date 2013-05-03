<?php
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Welcome to UPLB Tickethub!</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="description" content="Fancy Sliding Form with jQuery" />
        <meta name="keywords" content="jquery, form, sliding, usability, css3, validation, javascript"/>
        <link rel="stylesheet" href="css/style2.css" type="text/css" media="screen"/>
		<script type="text/javascript" src="ajax2/jquerymin.js"></script>
       	<script type="text/javascript" src="js/loginJS.js"></script>
    	<link href="ajax2/jquery-ui.css" rel="stylesheet" type="text/css"/>
		<script src="ajax2/jquery.js"></script>
		<script src="ajax2/jquery2.js"></script>
		<script language="javascript" type="text/javascript" src="jquery.js"></script>
    </head>
    <style>
        span.reference{
            position:fixed;
            left:5px;
            top:5px;
            font-size:10px;
            text-shadow:1px 1px 1px #fff;
        }
        span.reference a{
            color:#555;
            text-decoration:none;
			text-transform:uppercase;
        }
        span.reference a:hover{
            color:#000;
            
        }
        h1{
            color:#ccc;
            font-size:36px;
            text-shadow:1px 1px 1px #fff;
            padding:20px;
        }
    </style>
    <body>
        <div id="content">
            <a href="index.php" style="text-decoration:none"><h1>The UPLB Tickethub</h1></a>
            <div id="wrapper">
                <div id="steps">
                    <form id="formElem" name="formElem" action="">
                        <fieldset class="step" id='log'>
                            <legend>Welcome Back!</legend>
                            <div id='errorLog'>
								<ul style="list-style:none">
								<li>Fields with (*) are required.</li>
								<li>Username and Password accepts only alphanumeric characters.</li>
								</ul>
							</div>
							<br>
                            <p>
                                <label for="uname">Username*</label>
                                <input type="text" id="log_uname" placeholder="Username" pattern="[a-zA-Z0-9]+" required/>
                            </p>
                            <p>
                                <label for="password">Password*</label>
                                <input type="password" id="log_pwd" placeholder="Password" pattern="[a-zA-Z0-9]+" required/>
                            </p>				
                            <p class="submit">
                                <input type='submit' class='loginButton' id='loginButton' value='Log-in' style="background-color:#4797ED;" />
                            </p>
                            <div id="ajax_result2">
							
							</div>
                        </fieldset>
					</form>
					<form name="form_reg" id="form_reg">
                        <fieldset class="step" id ='reg'>
                            <legend>Join us!</legend>
                            <div id='errorReg'>
								<ul style="list-style:none">
								<li>Fields with (*) are required.</li>
								<li>Username and Password accepts only alphanumeric characters.</li>
								<li>First, Middle and Last name accepts only alpha characters.</li>
								<li>College and Course accepts only alpha characters.</li>
								</ul>
							</div>
							<br/>
							<p>
                                <label for="username">Username*</label>
                                <input type="text" name="username" id="reg_uname" class="required" placeholder="Username" pattern="[a-zA-Z0-9]+" required autofocus/>
                            </p>
                            <p>
                                <label for="pwd">Password*</label>
                                <input type="password" name="password" id="reg_pwd" class="required" placeholder="Password" pattern="[a-zA-Z0-9]+" required/>
                            </p>
                            <p>
                                <label for="fname">First name*</label>
                                <input type="text" name="fname" id="reg_fname" class="required" placeholder="First Name" pattern="[A-Za-z ]+" required/>
                            </p>
                            <p>
                                <label for="mname">Middle name*</label>
                               <input type="text" name="mname" id="reg_mname" class="required" placeholder="Middle Name" pattern="[A-Za-z ]+" required/>
                            </p>
                            <p>
                                <label for="lname">Last name*</label>
                               <input type="text" name="lname" id="reg_lname" class="required" placeholder="Last Name" pattern="[A-Za-z ]+" required/>
                            </p>
							<p>
                                <label for="suffix">Suffix</label>
                               <input type="text" name="suffix" id="reg_suffix" placeholder="Suffix"/>
                            </p>
							
							<p>
                                <label for="email">E-mail*</label>
                                <input type="email" name="email" id="reg_email" class="required" placeholder="E-mail" required/>
                            </p>
							<p>
                                <label for="cnum">Contact No.*</label>
                                <input type="text" name="cnum" id="reg_cnum" class="required" pattern="[0-9]+" placeholder="Contact number" required/>
                            </p>
							<p>
                                <label for="sex">Sex*</label>
                                <input type="radio" name="sex" value="m" checked/>Male
								<input type="radio" name="sex" value="f"/>Female
                            </p>
							<p>
                                <label for="type">Register as*</label>
                                <input type="radio" name="type" value="admin" id="admintypeButton" checked/>Administrator
								<input type="radio" name="type" value="faculty" id="facultytypeButton"/>Faculty<br>
								<input type="radio" name="type" value="student" id="studtypeButton"/>Student
                            </p>
							<div id="usertype">
							
							</div>
							<p class="submit">
                                <input type='submit' class='register' value='Submit' style="background-color:#4797ED;"/>
                            </p>
							<div id="ajax_result">
							
							</div>
                        </fieldset>
                    </form>
                </div>
                <div id="navigation" style="display:none;">
                    <ul>
                        <li class="selected">
                            <a href="#">Log-in</a>
                        </li>
                        <li>
                            <a href="#">Register</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>