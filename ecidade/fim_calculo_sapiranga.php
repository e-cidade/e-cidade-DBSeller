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



///// CALCULA A MARGEM CONSIGNAVEL

if($opcao_geral == 1 ){

$sql_margem = "
update gerfsal set r14_valor = round(margem.margem,2) 
from
(
select r14_regist as regist, ((B037-B038)/100*30) - B039 as margem
from
(
select ger.r14_regist, 
       coalesce((select sum(r53_valor) 
        from gerffx  fx  
        where fx.r53_regist = ger.r14_regist 
          and fx.r53_anousu = ger.r14_anousu 
          and fx.r53_mesusu = ger.r14_mesusu 
          and fx.r53_rubric in (select r09_rubric 
                             from basesr 
                             where r09_anousu = $anousu
                               and r09_mesusu = $mesusu
                               and r09_instit = $DB_instit
                               and r09_base   = 'B037'
                             )  
       ),0) as B037,
       coalesce((select sum(r14_valor) 
        from gerfsal sal 
        where sal.r14_regist = ger.r14_regist 
          and sal.r14_anousu = ger.r14_anousu 
          and sal.r14_mesusu = ger.r14_mesusu 
          and sal.r14_rubric in (select r09_rubric 
                             from basesr 
                             where r09_anousu = $anousu
                               and r09_mesusu = $mesusu
                               and r09_instit = $DB_instit
                               and r09_base   = 'B038'
                             ) 
       ),0) as B038,
       coalesce((select sum(r14_valor) 
        from gerfsal sal 
        where sal.r14_regist = ger.r14_regist 
          and sal.r14_anousu = ger.r14_anousu 
          and sal.r14_mesusu = ger.r14_mesusu 
          and sal.r14_rubric in (select r09_rubric 
                             from basesr 
                             where r09_anousu = $anousu 
                               and r09_mesusu = $mesusu
                               and r09_instit = $DB_instit 
                               and r09_base   = 'B039'
                             ) 
       ),0) as B039
from gerfsal as ger
     inner join rhpessoalmov on rh02_regist = r14_regist
                            and rh02_anousu = r14_anousu
                            and rh02_mesusu = r14_mesusu
where ger.r14_anousu = $anousu
  and ger.r14_mesusu = $mesusu 
  and ger.r14_rubric = 'R803'
  $where_regist_fim
) as x
) as margem

where r14_anousu = $anousu and r14_mesusu = $mesusu and r14_rubric = 'R803' and r14_regist = margem.regist
";
//echo $sql_margem;
$res_margem = pg_query($sql_margem) or die($sql_margem);

 $sql_margem_neg = "delete from gerfsal where r14_anousu = $anousu and r14_mesusu = $mesusu and r14_rubric = 'R803' and (r14_valor <= 0 or r14_valor is null)";
 $res_margem_neg = pg_query($sql_margem_neg) or die($sql_margem_neg);


  $sql_arred = "update gerfsal set r14_valor = round(r14_valor,2) 
                from rhpessoalmov
                where r14_anousu = rh02_anousu 
                  and r14_mesusu = rh02_mesusu 
                  and r14_regist = rh02_regist
                  and r14_anousu = $anousu 
                  and r14_mesusu = $mesusu
                  and r14_instit = $DB_instit
                  $where_regist_fim ";
  $res_arred = pg_query($sql_arred) or die($sql_arred);

 if($DB_instit != 2 ){

   if($DB_instit == 1){
  	 $valor_plano_saude  = 184.98;
	 $valor_plano_saude1 = 123.32;
   }elseif($DB_instit == 3){
	 $valor_plano_saude  = 308.3;
	 $valor_plano_saude1 = 0;
   }
   $sql_plano_saude = "
                      update gerfsal set r14_valor = $valor_plano_saude
                      where r14_anousu = $ano
                        and r14_mesusu = $mes
                        and r14_instit = $DB_instit
                        and r14_rubric = '1240'
                        and r14_regist in
                            (select distinct r14_regist
                             from gerfsal
                                  inner join rhpessoal on rh01_regist = r14_regist
                             where r14_anousu = $ano
                               and r14_mesusu = $mes
                               and r14_instit = $DB_instit
                               and rh01_numcgm in
                                   (select rh01_numcgm
                                    from gerfsal
                                         inner join rhpessoal on rh01_regist = r14_regist
                                    where r14_anousu = $ano
                                      and r14_mesusu = $mes
                                      and r14_rubric = 'R985'
                                      and r14_instit = $DB_instit
                                    group by rh01_numcgm
                                    having count(distinct rh01_regist) > 1
                                       and sum(r14_valor) > 4252.40
                                   ))";
  $res_plano_saude = pg_query($sql_plano_saude) or die($sql_plano_saude);
  
  $sql_plano_saude1 = "
                      update gerfsal set r14_valor = $valor_plano_saude1
                      where r14_anousu = $ano
                        and r14_mesusu = $mes
                        and r14_instit = $DB_instit
                        and r14_rubric = 'R500'
                        and r14_regist in
                            (select distinct r14_regist
                             from gerfsal
                                  inner join rhpessoal on rh01_regist = r14_regist
                             where r14_anousu = $ano
                               and r14_mesusu = $mes
                               and r14_instit = $DB_instit
                               and rh01_numcgm in
                                   (select rh01_numcgm
                                    from gerfsal
                                         inner join rhpessoal on rh01_regist = r14_regist
                                    where r14_anousu = $ano
                                      and r14_mesusu = $mes
                                      and r14_rubric = 'R985'
                                      and r14_instit = $DB_instit
                                    group by rh01_numcgm
                                    having count(distinct rh01_regist) > 1
                                       and sum(r14_valor) > 4252.40
                                   ))";
  $res_plano_saude1 = pg_query($sql_plano_saude1) or die($sql_plano_saude1);

 }


$sql_update_r928 = "          update gerfsal set r14_valor = round(difere.difere,2) 
                            from (select r14_regist as regist, provento, desconto , (desconto-provento) as difere
                                  from 
                                     ( select r14_regist, 
                                              round(sum(case when r14_pd = 1 and r14_rubric not in ('R928', 'R918') then r14_valor else 0 end),2) as provento
                                              round(sum(case when r14_pd = 2 then r14_valor else 0 end ),2) as desconto 
                                       from gerfsal 
                                       where r14_anousu = $anousu
                                         and r14_mesusu = $mesusu
                                         and r14_instit = $DB_instit
                                       group by r14_regist
                                     ) as x 
                                     inner join rhpessoal on rh01_regist = r14_regist 
                                     inner join cgm on z01_numcgm = rh01_numcgm                        
                                  where provento < desconto 
                                  ) as difere 
                            where r14_anousu = $anousu 
                              and r14_mesusu = $mesusu
                              and r14_instit = $DB_instit
                              and r14_rubric = 'R928' 
                              and r14_regist = difere.regist";

//$res_update_r928 = pg_query($sql_update_r928) or die($sql_upate_r928); 

}


if($opcao_geral == 1 ){
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
