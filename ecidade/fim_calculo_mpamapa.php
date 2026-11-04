<?php


///  global $regist_afa, $dias_afa, $rubrica_af, $res_afasta , $sql_afasta; 
  ///// insere as rubricas de afastamento e proporcionaliza os valores
// global $area;
// echo "<br><br> opcao_geral --> $opcao_geral   r110_regisf --> $r110_regisf  r110_regisi --> $r110_regisi opcao_gml --> $opcao_gml faixa_regis --> $faixa_regis r110_lotaci --> $r110_lotaci r110_lotacf --> $r110_lotacf  faixa_lotac --> $faixa_lotac <br> ";

//// ACERTA AS RUBRICAS 0009 - LICENÇA PREMIO PARA QUEM TEM O ASSENTAMENTO 21 (LPG) 

$where_regist_fim = '';
if($opcao_gml == 'm'){
  if( $r110_regisi == $r110_regisf){
    $where_regist_fim = " and rh02_regist in ($faixa_regis) ";
  }else{
    $where_regist_fim = "and rh02_regist between $r110_regisi and $r110_regisf";
  }
}elseif( $opcao_gml == 'l'){
  if($r110_lotaci == $r110_lotacf){
    $where_regist_fim = " and rh02_lota in ($faixa_lotac) ";
  }else{
    $where_regist_fim = " and rh02_lota between $r110_lotaci and $r110_lotacf ";
  }
}



if($opcao_geral == 1){
  $sql_del_molestia = "delete from gerfsal 
                       where r14_rubric in ('R913', 'R914', 'R915') 
                        and  r14_regist in (select rh02_regist 
                                            from rhpessoalmov 
                                            where rh02_anousu = $anousu
                                              and rh02_mesusu = $mesusu
                                              and rh02_portadormolestia = true ) 
                         and r14_anousu = $anousu
                         and r14_mesusu = $mesusu";
  $res_del_molestia = pg_query($sql_del_molestia) or die($sql_del_molestia);
}elseif($opcao_geral == 3 ){
  $sql_del_molestia = "delete from gerffer 
                       where r31_rubric in ('R913', 'R914', 'R915') 
                        and  r31_regist in (select rh02_regist 
                                            from rhpessoalmov 
                                            where rh02_anousu = $anousu
                                              and rh02_mesusu = $mesusu
                                              and rh02_portadormolestia = true ) 
                         and r31_anousu = $anousu
                         and r31_mesusu = $mesusu";
  $res_del_molestia = pg_query($sql_del_molestia) or die($sql_del_molestia);
}elseif($opcao_geral == 4 ){
  $sql_del_molestia = "delete from gerfres 
                       where r20_rubric in ('R913', 'R914', 'R915') 
                        and  r20_regist in (select rh02_regist 
                                            from rhpessoalmov 
                                            where rh02_anousu = $anousu
                                              and rh02_mesusu = $mesusu
                                              and rh02_portadormolestia = true ) 
                         and r20_anousu = $anousu
                         and r20_mesusu = $mesusu";
  $res_del_molestia = pg_query($sql_del_molestia) or die($sql_del_molestia);
}elseif($opcao_geral == 5 ){
  $sql_del_molestia = "delete from gerfs13 
                       where r35_rubric in ('R913', 'R914', 'R915') 
                        and  r35_regist in (select rh02_regist 
                                            from rhpessoalmov 
                                            where rh02_anousu = $anousu
                                              and rh02_mesusu = $mesusu
                                              and rh02_portadormolestia = true ) 
                         and r35_anousu = $anousu
                         and r35_mesusu = $mesusu";
  $res_del_molestia = pg_query($sql_del_molestia) or die($sql_del_molestia);
}elseif($opcao_geral == 8 ){
  $sql_del_molestia = "delete from gerfcom 
                       where r48_rubric in ('R913', 'R914', 'R915') 
                        and  r48_regist in (select rh02_regist 
                                            from rhpessoalmov 
                                            where rh02_anousu = $anousu
                                              and rh02_mesusu = $mesusu
                                              and rh02_portadormolestia = true ) 
                         and r48_anousu = $anousu
                         and r48_mesusu = $mesusu";
  $res_del_molestia = pg_query($sql_del_molestia) or die($sql_del_molestia);
}

?>
