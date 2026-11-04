<?php

$x = fsockopen("192.168.0.155",4445);

fputs($x,chr(27).chr(66)."001");
fputs($x,chr(27).chr(67)."MUNICIPIO TESTE 7890$");
fputs($x,chr(27).chr(68).'010107');
fputs($x,chr(27).chr(70)."1234567890123456789012345678901234567890123456789012345678901$");
fputs($x,chr(27).chr(86)."00000000012299$");

fclose($x);

?>
