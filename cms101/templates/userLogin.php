<?php
session_start();
if(isset($_SESSION['content'])) unset($_SESSION['content']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Welcome to SyS CMS!</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="description" content="Fancy Sliding Form with jQuery" />
        <meta name="keywords" content="jquery, form, sliding, usability, css3, validation, javascript"/>
        <link rel="stylesheet" href="include/style2.css" type="text/css" media="screen"/>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
       	<script type="text/javascript" src="include/loginJS.js"></script>
    	<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
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
            <h1>SYS at its finest.</h1>
            <div id="wrapper">
                <div id="steps">
                    <form id="formElem" name="formElem" action="">
                        <fieldset class="step" id='log'>
                            <legend>Welcome Back!</legend>
                            <div id='errorLog'></div>
                            <p>
                                <label for="email">Email</label>
                                <input type="email" id="log_email"/>
                            </p>
                            <p>
                                <label for="password">Password</label>
                                <input type="password" id="log_pwd"/>
                            </p>
                            
                            <p class="submit">
                                <input type='button' class='loginButton' value='Log-in' style="background-color:#4797ED;" />
                            </p>
                            
                        </fieldset>
                        <fieldset class="step" id ='reg'>
                            <legend>Join us!</legend>
                            <div id='errorReg'></div>
                            <p>
                                <label for="name">E-mail</label>
                                <input type="email" name="email" id="reg_email"/>
                            </p>
                            <p>
                                <label for="country">Password</label>
                                <input type="password" name="password" id="reg_pwd"/>
                            </p>
                            <p>
                                <label for="phone">Firstname</label>
                                <input type="text" name="fname" id="reg_fname"/>
                            </p>
                            <p>
                                <label for="website">Middlename</label>
                               <input type="text" name="mname" id="reg_mname"/>
                            </p>
                            <p>
                                <label for="website">Lastname</label>
                               <input type="text" name="lname" id="reg_lname"/>
                            </p>
                            <p>
                                <label for="website">Enter code</label>
                                <input type="text" name="captcha" id="captcha"/><br/><br/>
                                <img src="include/captcha.php"/>
                            </p>
                            <p class="submit">
                                <input type='button' class='register' value='Submit' style="background-color:#4797ED;"/>
                            </p>
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