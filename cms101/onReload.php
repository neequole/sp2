<?php
session_start();
if(isset( $_POST['content'])) $_SESSION['content'] = $_POST['content'];
echo $_SESSION['content'];
?>
