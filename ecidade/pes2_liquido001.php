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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

db_postmemory($_POST);

$gform = new cl_formulario_rel_pes;
$clrotulo = new rotulocampo;
$clrotulo->label('DBtxt23');
$clrotulo->label('DBtxt25');
$clrotulo->label('DBtxt27');
$clrotulo->label('DBtxt28');
// relatório tambem serve para ver a pirâmide salarial.
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>

<script>

function js_emite(){
  selecionados = "";
  virgula_ssel = "";
  for(var i=0; i<document.form1.sselecionados.length; i++){
    selecionados+= virgula_ssel + document.form1.sselecionados.options[i].value;
    virgula_ssel = ",";
  }

  if(document.form1.sselecionados.length==0){
    alert('Escolhe pelo menos uma coluna . Verifique !.');
    return false;
  }

  if(document.form1.faixa1.value.trim().length==0 || document.form1.faixa2.value.trim().length==0){
    alert('Informe valores inicial/final corretamente na faixa.');
    return false;
  }

  qry  = "?colunas="+selecionados;
  qry += '&ano='+document.form1.anofolha.value;
  qry += '&qtd='+document.form1.qtd.value;
  qry += '&mes='+document.form1.mesfolha.value;
  qry += '&func_lota='+document.form1.func_lota.value;
  qry += '&faixa1='+document.form1.faixa1.value;
  qry += '&faixa2='+document.form1.faixa2.value;
  qry += '&tipo_faixa='+document.form1.tipo_faixa.value;
  qry += '&selecao='+document.form1.selecao.value;
  
  if(document.form1.sellotac){
    valores = '';
    virgula = '';
    for(i=0; i < document.form1.sellotac.length; i++){
      valores+= virgula+document.form1.sellotac.options[i].value;
      virgula = ',';
    }
    document.form1.faixa_lotac.value = valores;
    document.form1.sellotac.selected = 0;
  qry  += '&faixa_lotac='+document.form1.faixa_lotac.value;
  }else if(document.form1.lotaci){
     qry += '&lotaci='+document.form1.lotaci.value;
     qry += '&lotacf='+document.form1.lotacf.value;
  }
  if(document.form1.selregis){
    valores = '';
    virgula = '';
    for(i=0; i < document.form1.selregis.length; i++){
      valores+= virgula+document.form1.selregis.options[i].value;
      virgula = ',';
    }
    document.form1.faixa_regis.value = valores;
    document.form1.selregis.selected = 0;
    qry += '&faixa_regis='+document.form1.faixa_regis.value;
  }else if(document.form1.regisi){
    qry += '&regisi='+document.form1.regisi.value;
    qry += '&regisf='+document.form1.regisf.value;
  }
  qry += '&ordem='+document.form1.xordem.value;
  qry += '&asc='+document.form1.xasc.value;
  
  jan = window.open('pes2_liquido002.php'+qry,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
  jan.moveTo(0,0);
}

</script>  
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
  <div class="container">
    <form name="form1" method="post" action="">
    <fieldset>
    <legend>Faixa de Bruto/Líquido/Descontos</legend>
    <table class="form-container">
     <?php
       $gform->strngtipores = "gml";              // OPÇÕES PARA MOSTRAR NO TIPO DE RESUMO g - geral,
       $gform->tipores = true;
     
     
       $gform->usalota = true;                      // PERMITIR SELEÇÃO DE LOTAÇÕES
       $gform->usaregi = true;                      // PERMITIR SELEÇÃO DE LOTAÇÕES
       $gform->selecao = true;                      // PERMITIR SELEÇÃO DE SELEÇÃO
     
       $gform->lo1nome = "lotaci";                  // NOME DO CAMPO DA LOTAÇÃO INICIAL
       $gform->lo2nome = "lotacf";                  // NOME DO CAMPO DA LOTAÇÃO FINAL
       $gform->lo3nome = "sellotac";
     
       $gform->trenome = "func_lota";               // NOME DO CAMPO TIPO DE RESUMO
       $gform->tfinome = "opcao_filtro";            // NOME DO CAMPO TIPO DE FILTRO
     
       $gform->re1nome = "regisi";                  // NOME DO CAMPO DA MATRÍCULA INICIAL
       $gform->re2nome = "regisf";                  // NOME DO CAMPO DA MATRÍCULA FINAL
       $gform->re3nome = "selregis";
     
       $gform->resumopadrao = "g";                  // TIPO DE RESUMO PADRÃO
       $gform->tipresumo = "Filtro Por ";
       $gform->campo_auxilio_lota = "faixa_lotac";  // NOME DO DAS LOTAÇÕES SELECIONADAS
       $gform->campo_auxilio_regi = "faixa_regis";  // NOME DO DAS MATRÍCULAS SELECIONADAS
     
       $gform->onchpad = true;                      // MUDAR AS OPÇÕES AO SELECIONAR OS TIPOS DE FILTRO OU RESUMO
     
       $gform->desabam = false;
       $gform->manomes = true;
       $gform->gera_form(db_anofolha(),db_mesfolha());
     ?>
     <tr>
       <td colspan="2" >
         <fieldset>
            <legend>Selecione as Colunas a imprimir</legend>
              <?php
                db_input("valor", 3, 0, true, 'hidden', 3);
                db_input("colunas_sselecionados", 3, 0, true, 'hidden', 3);
                db_input("colunas_nselecionados", 3, 0, true, 'hidden', 3);
                $arr_colunas = Array( "l" =>"Liquido", "p" =>"Provento", "d" =>"Desconto");
                $arr_colunas_final   = Array();
                $arr_colunas_inicial = Array();
                if(isset($colunas_sselecionados) && $colunas_sselecionados != ""){
                   $colunas_sselecionados = explode(",",$colunas_sselecionados);
                   for($Ic=0;$Ic < count($colunas_sselecionados);$Ic++){
                      $arr_colunas_final[$colunas_sselecionados[$Ic]] = $arr_colunas[$colunas_sselecionados[$Ic]]; 
                   }
                }
                if(isset($colunas_nselecionados) && $colunas_nselecionados != ""){
                   $colunas_nselecionados = explode(",",$colunas_nselecionados);
                   for($Ic=0;$Ic < count($colunas_nselecionados);$Ic++){
                      $arr_colunas_inicial[$colunas_nselecionados[$Ic]] = $arr_colunas[$colunas_nselecionados[$Ic]]; 
                   }
                }
                if(!isset($colunas_sselecionados) || !isset($colunas_sselecionados) || $colunas_sselecionados == ""){
                   $arr_colunas_final  = Array();
                   $arr_colunas_inicial = $arr_colunas;
                }
                db_multiploselect("valor","descr", "nselecionados", "sselecionados", $arr_colunas_inicial, $arr_colunas_final, 6, 250, "", "", true, "js_complementar('c');");
	          ?>
         </fieldset>
       </td>
     </tr>
     <tr>
        <td title="Tipo de de Faixa" >Tipo de Faixa :</td>
        <td>
          <?php
          $xvx = $arr_colunas_final;
          db_select('tipo_faixa',$xvx,true,4,'');
          ?>
        </td>
      </tr>
      <tr>
        <td title="Faixa Inicial e Final" >Faixa :</td>
        <td>
          <?php
           if(!isset($faixa1)){
	            $faixa1 = 0;
           }
	         db_input("faixa1",10,"",true,3,'');
          ?>
	         &nbsp;&nbsp;Até&nbsp;&nbsp;
          <?php
           if(!isset($faixa2)){
	            $faixa2 = 9999999999;
           }
	         db_input("faixa2",10,"",true,3,'');
          ?>
        </td>
      </tr>
      <tr>
        <td>Quantidade de Registros : </td>
        <td>
        <?php
         db_input("qtd",10,"",true,3,'');
        ?>
        </td>
      </tr>
      <tr>
        <td title="Ordem do relatório" >Ordem :</td>
        <td>
          <?php
          $v = array("a"=>"Alfabética", "n"=>"Numérica");
          $v = array_merge($v,$arr_colunas_final);
          db_select('xordem',$v,true,4,"");
          ?>
        </td>
      </tr>
      <tr>
        <td title="Tipo de Ordem do relatório" >Tipo  de Ordem :</td>
        <td>
          <?php
          if(!isset($xv)){
             $xv = array("a"=>"Ascendente", "d"=>"Descendente");
          }
          db_select('xasc',$xv,true,4,"");
          ?>
        </td>
      </tr>
    </table>
    <font style="font-weight: bold; color:red;">Relatório emitido apartir da Geração em Disco!</b></font>
    </fieldset>
    <input  name="emite2" id="emite2" type="button" value="Processar" onclick="js_emite();" >
  </form>
<?php
  db_menu();
?>
</body>
</html>
 
<script>

function js_complementar(opcao){
  selecionados = "";
  virgula_ssel = "";

  for(var i=0; i<document.form1.sselecionados.length; i++){
    document.form1.tipo_faixa.options[i] = new Option(document.form1.sselecionados.options[i].text,document.form1.sselecionados.options[i].value);
    if(document.form1.func_lota.value != "l"){
       document.form1.xordem.options[i+2] = new Option(document.form1.sselecionados.options[i].text,document.form1.sselecionados.options[i].value);
    }   
    selecionados+= virgula_ssel + document.form1.sselecionados.options[i].value;
    virgula_ssel = ",";
  }
  if(document.form1.sselecionados.length == 0){
    for(var i=0; i<=document.form1.tipo_faixa.length; i++){
      document.form1.tipo_faixa.options[i] = null;
      if(document.form1.func_lota.value != "l"){
         document.form1.xordem.options[i+2] = null;
      }
    }
    document.form1.tipo_faixa.options[document.form1.sselecionados.length] = null;
    document.form1.xordem.options[document.form1.sselecionados.length+2]= null;
  }else{
    document.form1.tipo_faixa.options[document.form1.sselecionados.length] = null;
    document.form1.xordem.options[document.form1.sselecionados.length+2]= null;
  }
  document.form1.colunas_sselecionados.value = selecionados;

  selecionados = "";
  virgula_ssel = "";
  for(var i=0; i<document.form1.nselecionados.length; i++){
    selecionados+= virgula_ssel + document.form1.nselecionados.options[i].value;
    virgula_ssel = ",";
  }
  document.form1.colunas_nselecionados.value = selecionados;

  x = document.form1;
  erro = 0;
  for(i=0; i<x.objeto2.length; i++){
    if(x.objeto2.options[i].value == 5){
      erro ++;
      break;
    }
  }
  if((erro == 0 && x.complementares) || (erro > 0 && !x.complementares) || opcao == 'am'){
    for(i=0; i<x.objeto1.length; i++){
      x.objeto1.options[i].selected = true;
    }
    for(i=0; i<x.objeto2.length; i++){
      x.objeto2.options[i].selected = true;
    }
    x.submit();
  }
}

</script>
