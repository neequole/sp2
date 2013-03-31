<?php
$dt = date('y-m-d_H-s');
#echo $dt;die();
$fp = fopen("schedule_log/{$dt}_file.txt",'a');
fwrite($fp,"test");
fclose($fp);
?>