<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009  DBSeller Servicos de Informatica             
 *                            www.dbseller.com.br                     
 *                         e-cidade@dbseller.com.br                   
 *                                                                    
 *  Este programa e software livre; voce pode redistribui-lo e/ou     
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme  
 *  publicada pela Free Software Foundation; tanto a versao 2 da      
 *  Licenca como (a seu criterio) qualquer versao mais nova.          
 *                                                                    
 *  Este programa e distribuido na expectativa de ser util, mas SEM   
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de              
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM           
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais  
 *  detalhes.                                                         
 *                                                                    
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU     
 *  junto com este programa; se nao, escreva para a Free Software     
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA          
 *  02111-1307, USA.                                                  
 *  
 *  Copia da licenca no diretorio licenca/licenca_en.txt 
 *                                licenca/licenca_pt.txt 
 */

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_selecao_classe.php"));
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>

</script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0"  topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
<table>
<tr height=25><td>&nbsp;</td></tr>
</table>
<?php
db_postmemory($HTTP_POST_VARS);
db_criatermometro('termometro','Concluido...','blue',1);
flush();
$wh = '';
$clselecao = null;

$xseparador = '';
$yseparador = '';
if($separador == 'S'){
  $xseparador = "||';'";
  $yseparador = ';';
}

db_sel_instit();

//echo "nomeinst --> $nomeinst   db21_tipoinstit --> $db21_tipoinstit ";exit;

if ($_POST["r44_selec"] != ''){

 $clselecao = new cl_selecao;
 $rsselec   =  $clselecao->sql_record($clselecao->sql_query($r44_selec));
 db_fieldsmemory($rsselec,0);
 $wh  =  "and $r44_where";

}
if ($_POST["vinculo"] == "f"){
  if($separador == 'N'){ 
    $arq = "tmp/folha.txt";
  }else{
    $arq = "tmp/folha.csv";
  }
  $arquivo = fopen($arq,'w');  

  $sql = "
select 'P41 '
     $xseparador
     ||cpf
     $xseparador
     ||lpad(cargo,2,'0')
     $xseparador
     ||rpad(r70_descr, 80,' ')
     $xseparador
     ||lpad(translate(to_char(bruto,'9999999999.99'),'.',''),10,'0')
     $xseparador
     ||lpad(mes,2,'0')
     $xseparador
     ||ano
     $xseparador
     ||tipo
     $xseparador
     ||situacao as todo    
from 
(
select x.*,
       r70_descr,
       lpad(coalesce(z01_cgccpf,'0'),11,'0') as cpf,
       case when rh30_vinculo <> 'A' then 0
        else case when rh02_codreg in (2, 21, 29, 39) then 1
          else case when rh02_codreg in (5, 16, 18, 20, 28) then 5
            else case when rh02_funcao in (8, 9) or rh02_codreg = 35 then 2
              else 3
            end
          end
        end
       end as situacao ,
       case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%prof%') then 0
        else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%advo%') then 1
         else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%procur%') then 2
          else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%engenh%') then 3
           else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike 'medico%') then 4
            else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%fisiotera%') then 5
             else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%dentista%') then 6
              else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%enfermeiro%') then 7
               else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%te%enfermagem%') then 8
                else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%au%enfermagem%') then 9
                 else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%ag%admi%') then 10
                  else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%aux%admi%') then 11
                   else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%contador%') then 12
                    else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%fiscal%') then 13
                     else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%arquiteto%') then 14
                      else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%anal%sist%') then 15
                       else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%tec%inform%') then 16
                        else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%programador%') then 17
                         else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%insp%esco%') then 18
                          else case when rh37_funcao in (select rh37_funcao from rhfuncao where rh37_descr ilike '%farmac%') then 19
                           else 20
                          end
                         end
                        end
                       end
                      end
                     end
                    end
                   end
                  end
                 end
                end
               end
              end
             end
            end
           end
          end
         end
        end 
       end  as cargo
 
from 
(
select 1 as tipo,
       r14_anousu as ano,
       r14_mesusu as mes,
       r14_regist as regist,
       r14_instit as instit,
       round(sum(r14_valor),2) as bruto
from gerfsal
where r14_anousu between $anoini and $anofim
  and r14_pd = 1
group by r14_anousu, r14_mesusu, r14_regist, r14_instit

union all

select 2 as tipo,
       r48_anousu as ano,
       r48_mesusu as mes,
       r48_regist as regist,
       r48_instit,
       round(sum(r48_valor),2) as bruto
from gerfcom
where r48_anousu between $anoini and $anofim
  and r48_pd = 1
group by r48_anousu, r48_mesusu, r48_regist, r48_instit

union all

select 2 as tipo,
       r35_anousu as ano,
       r35_mesusu as mes,
       r35_regist as regist,
       r35_instit,
       round(sum(r35_valor),2) as bruto
from gerfs13
where r35_anousu between $anoini and $anofim
  and r35_pd = 1
group by r35_anousu, r35_mesusu, r35_regist, r35_instit
) as x
inner join rhpessoal    on rh01_regist = regist
inner join cgm          on rh01_numcgm = z01_numcgm
inner join rhpessoalmov on rh02_anousu = ano
                       and rh02_mesusu = mes
                       and rh02_regist = regist
                       and rh02_instit = instit
inner join rhlota       on r70_codigo  = rh02_lota
inner join rhregime     on rh30_codreg = rh02_codreg
inner join rhfuncao     on rh37_funcao = rh02_funcao
                       and rh37_instit = rh02_instit
order by ano, mes, tipo, regist  
) as xx

";
// echo $sql;exit;
  $result = db_query($sql);
  $num = pg_numrows($result);
  for($x = 0;$x < pg_numrows($result);$x++){
    
		db_atutermometro($x,$num,'termometro');
	  flush();

    $matric = pg_result($result,$x,'matricula');
    
  fputs($arquivo,pg_result($result,$x,'todo')."\r\n");
  }
  fclose($arquivo);

}elseif ($_POST["vinculo"] == "s"){
  if($separador == 'N'){ 
    $arq = "tmp/serv.txt";
  }else{
    $arq = "tmp/serv.csv";
  }
  
  $arquivo = fopen($arq,'w');  

  $sql = "
select 'P41 '
     $xseparador
     ||rpad(z01_nome,80,' ')
     $xseparador
     ||rh01_sexo
     $xseparador
     ||rpad(z01_mae,80,' ')
     $xseparador
     ||rpad(z01_pai,80,' ')
     $xseparador
     ||lpad(z01_cgccpf,11,0)
     $xseparador
     ||lpad(z01_ident,15,0)
     $xseparador
     ||'SSP-RJ    '
     $xseparador
     ||lpad(rh44_codban,3,'0')
     $xseparador
     ||lpad(rh44_conta||rh44_dvconta,12,' ')
     $xseparador
     ||lpad(trim(rh44_agencia)||trim(rh44_dvagencia),6,' ')
     $xseparador
     ||lpad(to_char(rh01_nasc,'YYYYmmdd'),8,'0')
     $xseparador
     ||case rh21_instru 
            when 1 then 1
            when 2 then 2
            when 3 then 2
            when 4 then 2
            when 5 then 2
            when 6 then 3
            when 7 then 3
            when 8 then 4
            when 9 then 5
            else 7
       end
     $xseparador  
     ||rpad(rh16_titele,12,'0')
     $xseparador
     ||rpad(rh16_zonael,3,'0')
     $xseparador
     ||rpad(z01_ender,255,' ')
     $xseparador
     ||lpad(z01_numero,5,'0')
     $xseparador
     ||rpad(z01_compl,10,' ')
     $xseparador
     ||rpad(z01_bairro,40,' ')
     $xseparador
     ||rpad(z01_cep,8,' ')
     $xseparador
     ||rpad(z01_munic,40,' ')
     $xseparador
     ||rpad(z01_uf,2,' ')
     $xseparador
     ||rpad(coalesce(z01_telef,'0'),10,' ')
     $xseparador
     ||rpad(coalesce(z01_telcel,'0'),10,' ')
     $xseparador
     ||'          '
     $xseparador
     ||'          '
     $xseparador
     ||lpad(to_char(rh01_admiss,'YYYYmmdd'),8,'0')
     as todo
from rhpessoal 
     inner join cgm            on rh01_numcgm = z01_numcgm 
     inner join rhpessoalmov   on rh02_anousu = 2012 
                              and rh02_mesusu = 6 
                              and rh02_regist = rh01_regist 
     left  join rhpesrescisao  on rh05_seqpes = rh02_seqpes 
     left  join rhpesbanco     on rh44_seqpes = rh02_seqpes
     inner join rhinstrucao    on rh01_instru = rh21_instru
     left  join rhpesdoc       on rh16_regist = rh01_regist

where rh02_regist in
  (
  select distinct regist from
    (
    select r14_regist as regist from gerfsal where r14_anousu between $anoini and $anofim
    union all  
    select r48_regist from gerfcom where r48_anousu between $anoini and $anofim
    union all  
    select r35_regist as regist from gerfs13 where r35_anousu between $anoini and $anofim
    ) as x
  )
order by rh02_regist

";
// echo $sql;exit;
  $result = db_query($sql);
  $num = pg_numrows($result);
  for($x = 0;$x < pg_numrows($result);$x++){
    
		db_atutermometro($x,$num,'termometro');
	  flush();

    $matric = pg_result($result,$x,'matricula');
    
  fputs($arquivo,pg_result($result,$x,'todo')."\r\n");
  }
  fclose($arquivo);

}
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));

?>
<form name='form1' id='form1'></form>
<script>js_montarlista("<?=$arq?>#Arquivo gerado em: <?=$arq?>",'form1');
function js_manda(){
		location.href='pes4_geratcerj001.php?banco=001';
}
setTimeout(js_manda,300);
</script>
</body>
</html>