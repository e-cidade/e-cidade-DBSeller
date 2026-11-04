<?php
$fp = fsockopen("192.168.1.17",4444);

fputs($fp,"alapucha tche\n");
$str = fgets($fp,256);
echo $str;
fclose($fp);
?>
