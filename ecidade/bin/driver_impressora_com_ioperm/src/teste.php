<?php
  $r = "abcdefghijklmn";

  set_time_limit(0);
  ob_implicit_flush();
  //$fp = fsockopen("localhost", 5001, $errno, $errstr, 30);
  //echo im_imp("lkjlkj");    
  im_conectar("localhost",5001);
  echo im_imp("TESTE DE IMP SEM N");
  echo im_imprimir("aaaaaaaaaaaaaaaaaaaa");
  echo im_imprimir("bbbbbbbbbbbbbbbbbbbb");
  echo im_imprimir("cccccccccccccccccccc");
  echo im_imprimir("dddddddddddddddddddd");
  echo im_imprimir("eeeeeeeeeeeeeeeeeeee");
  echo im_imprimir("ffffffffffffffffffff");
  im_fechar();
/*
  if (!$fp)
    echo "$errstr ($errno)'\n";
  else {   
      $j = 10; 
     for($i = 0;$i < 5;$i++) {
       fputs($fp,substr($r,0,$j--));   
       sleep(1);
     }
  }    
  fclose($fp);
*/
?>
