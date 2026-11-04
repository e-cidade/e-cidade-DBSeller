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
require_once(modification("classes/db_inssirf_classe.php"));
require_once(modification("classes/db_rhcadregime_classe.php"));
require_once(modification("classes/db_rhrubricas_classe.php"));
require_once(modification("classes/db_rhregime_classe.php"));

$clrotulo = new rotulocampo;
$clinssirf = new cl_inssirf;
$clrhregime = new cl_rhregime;
$clrhcadrefime = new cl_rhcadregime;
$clrhrubricas = new cl_rhrubricas;

db_postmemory($_POST);
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<style type="text/css">
  fieldset table tr td:first-child {
    text-align:left;
  }
</style>
</head>
<body>
<div class="container">
<form name="form1" method="post" action="">
  <fieldset>

    <legend><strong>Relação da previdência</strong></legend>

    <table align="center">

      <?php
      if(!isset($tipo)){
        $tipo = "l";
      }
      if(!isset($filtro)){
        $filtro = "i";
      }
      if(!isset($anofolha) || (isset($anofolha) && trim($anofolha) == "")){
        $anofolha = db_anofolha();
      }
      if(!isset($mesfolha) || (isset($mesfolha) && trim($mesfolha) == "")){
        $mesfolha = db_mesfolha();
      }
      include(modification("dbforms/db_classesgenericas.php"));
      $geraform = new cl_formulario_rel_pes;

      $geraform->usalota = true;                      // PERMITIR SELEÇÃO DE LOTAÇÕES
      $geraform->selecao = true;                      // PERMITIR SELEÇÃO DE LOTAÇÕES

      $geraform->lo1nome = "lotaci";                  // NOME DO CAMPO DA LOTAÇÃO INICIAL
      $geraform->lo2nome = "lotacf";                  // NOME DO CAMPO DA LOTAÇÃO FINAL
      $geraform->lo3nome = "sellot";                  // NOME DO CAMPO DE SELEÇÃO DE LOTAÇÕES

      $geraform->trenome = "tipo";                    // NOME DO CAMPO TIPO DE RESUMO
      $geraform->tfinome = "filtro";                  // NOME DO CAMPO TIPO DE FILTRO

      $geraform->resumopadrao = "l";                  // DEFAULT DO TIPO DE FOLHA
      $geraform->filtropadrao = "i";                  // DEFAULT DO FILTRO

      $geraform->strngtipores = "gl";                 // OPÇÕES PARA MOSTRAR NO TIPO DE RESUMO g - geral,
                                                      //                                       l - lotação,

      $geraform->tipofol = true;                      // MOSTRAR DO CAMPO PARA TIPO DE FOLHA
      $geraform->arr_tipofol = array(
                                     "r14"=>"Salário",
                                     "r48"=>"Complementar",
                                     "r35"=>"13o Salário",
                                     "r20"=>"Rescisão",
                                     "todas"=>"Todas"
                                    );
      $geraform->complementar = "r48";
                      // VALUE DA COMPLEMENTAR PARA BUSCAR SEMEST 

      $geraform->campo_auxilio_lota = "faixa_lotac";  // NOME DO DAS LOTAÇÕES SELECIONADAS

      $geraform->onchpad = true;                      // MUDAR AS OPÇÕES AO SELECIONAR OS TIPOS DE FILTRO OU RESUMO
      $geraform->gera_form($anofolha,$mesfolha);
      ?>
      <tr>
        <td title="Tabela de Previdência">
        <strong>Tabela de Previdência:</strong>
        </td>
        <td>
          <?php
          $res = $clinssirf->sql_record($clinssirf->sql_query_file(null,db_getsession('DB_instit'),"distinct (cast(r33_codtab as integer) - 2) as r33_codtab,r33_nome","r33_codtab"," r33_anousu = ".$anofolha." and r33_mesusu = ".$mesfolha." and r33_codtab > 2"));
          db_selectrecord('prev', $res, true, 4);
          ?>
        </td>
      </tr>
      <tr>
        <td title="Cálculo">
        <strong>Cálculo:</strong>
        </td>
        <td>
          <?php
          $opcalculo = array(1=>"Com Cálculo", 2=>"Sem Cálculo", 3=>"Todos");
          db_select('calc',$opcalculo,true,4,"");
          ?>
        </td>
      </tr>
      <tr>
        <td title="Filtro">
        <strong>Filtro:</strong>
        </td>
        <td>
          <?php
            $aFiltro = array("0"=>"Todos",  
                             "1"=>"Somente Servidores",
                             "2"=>"Somente Autônomos");
            
            db_select('filtro_rel',$aFiltro,true,4,"");
          ?>
        </td>
      </tr>  
      <tr>
        <td title="Troca de Página">
        <strong>Quebra Página:</strong>
        </td>
        <td>
          <?php
          $quebra_pagina = array("n"=>"Não", "s"=>"Sim");
          db_select('quebra_pagina',$quebra_pagina,true,4,"");
          ?>
        </td>
      </tr>
      <tr>
        <td colspan="2" >
              <fieldset>
                <Legend>
                  <b>Selecione os Vinculos</b>
                </Legend>
                <?php
                db_input("valor", 3, 0, true, 'hidden', 3);
                db_input("colunas_sselecionados", 3, 0, true, 'hidden', 3);
                db_input("colunas_nselecionados", 3, 0, true, 'hidden', 3);
                 if(!isset($result_regime)){
                    $result_regime = $clrhregime->sql_record($clrhregime->sql_query_file(null, "rh30_codreg, rh30_codreg||'-'||rh30_descr as rh30_descr", "rh30_codreg" , " rh30_instit = ".db_getsession('DB_instit') ));
                    for($x=0; $x<$clrhregime->numrows; $x++){
                         db_fieldsmemory($result_regime,$x);
                         $arr_colunas[$rh30_codreg]= $rh30_descr;
                    }
                  }
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
                 db_multiploselect("rh30_codreg","rh30_descr", "nselecionados", "sselecionados", $arr_colunas_inicial, $arr_colunas_final, 6, 250, "", "", true, "js_complementar('c');");
                 ?>
              </fieldset>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <fieldset>
            <legend><b>Salário família para dedução</b></legend>
              <table>
                <?php
                $result_dados_rubricas = $clrhrubricas->sql_record($clrhrubricas->sql_query_file(null,db_getsession('DB_instit'),"rh27_rubric, rh27_descr","rh27_rubric"," rh27_rubric in ('R918','R919','R920') and rh27_instit = ".db_getsession('DB_instit')));
                if($clrhrubricas->numrows > 0){
                  for($i=0; $i<$clrhrubricas->numrows; $i++){
                    db_fieldsmemory($result_dados_rubricas, $i);
                    echo "
                          <tr>
                            <td>
                              <input type='checkbox' name='".$rh27_rubric."' value='".$rh27_rubric."'>".$rh27_rubric." - ".$rh27_descr."
                            </td>
                          </tr>
                         ";
                  }
                }
                ?>
              </table>
          </fieldset>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <fieldset id="fieldsetFiltroFaixaValor">
            <legend><b>Filtro por faixa de valor</b></legend>
              <table>
                <tr>
                  <td>
                    <input type='checkbox' name='faixa1' id='faixa1' value='faixa1'> 1ª Faixa de R$ 0,00 até R$ 2.424,00
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type='checkbox' name='faixa2' id='faixa2' value='faixa2'> 2ª Faixa de R$ 2.424,01 até R$ 3.636,00
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type='checkbox' name='faixa3' id='faixa3' value='faixa3'> 3ª Faixa de R$ 3.636,01 até R$ 4.848,00
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type='checkbox' name='faixa4' id='faixa4' value='faixa4'> 4ª Faixa de R$ 4.848,01 até R$ 6.060,00
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type='checkbox' name='faixa5' id='faixa5' value='faixa5'> 5ª Faixa de R$ 6.060,01 até R$ 7.087,22
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type='checkbox' name='faixa6' id='faixa6' value='faixa6'> 6ª Faixa de R$ 7.087,23 até R$ 12.120,00
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type='checkbox' name='faixa7' id ='faixa7' value='faixa7'> 7ª Faixa acima de R$ 12.120,01
                  </td>
                </tr>
              </table>
          </fieldset>
        </td>
      </tr>      
    </table>
  </fieldset>
  <input name="emite" id="emite" type="button" value="Processar" onclick="js_emite();" >
</form>
</div>
<?php
db_menu();
?>
</body>
</html>
<script type="text/javascript">

var oToogle1 = new DBToogle('fieldsetFiltroFaixaValor', false);


function js_emite() {

  if(document.form1.anofolha.value == ""){
    alert("Informe o ano a pesquisar.");
    document.form1.anofolha.focus();
  }else if(document.form1.mesfolha.value == ""){
    alert("Informe o mês a pesquisar.");
    document.form1.mesfolha.focus();
  }else{

    selecionados = "";
    virgula_ssel = "";
    for(var i=0; i<document.form1.sselecionados.length; i++){
      selecionados+= virgula_ssel + document.form1.sselecionados.options[i].value;
      virgula_ssel = ",";
    }

    lotaci = "";
    lotacf = "";
    sellot = "";
    qry = "?ano="+document.form1.anofolha.value;
    qry+= "&mes="+document.form1.mesfolha.value;
    qry+= "&codreg="+selecionados;
    qry+= "&prev="+document.form1.prev.value;
    qry+= "&tfol="+document.form1.tipofol.value;
    qry+= "&calc="+document.form1.calc.value;
    qry+= "&selecao="+document.form1.selecao.value;
    qry+= "&filtro="+document.form1.filtro_rel.value;
    qry+= "&quebra_pagina="+document.form1.quebra_pagina.value;
    if(document.form1.lotaci){
      qry+= "&lotaci="+document.form1.lotaci.value;
      qry+= "&lotacf="+document.form1.lotacf.value;
    }else if(document.form1.sellot){
      qry+= "&sellot="+js_campo_recebe_valores();
    }
    if(document.form1.R918 && document.form1.R918.checked){
      qry+= "&R918=true";
    }
    if(document.form1.R919 && document.form1.R919.checked){
      qry+= "&R919=true";
    }
    if(document.form1.R920 && document.form1.R920.checked){
      qry+= "&R920=true";
    }

    qry+= "&faixa1="+$("faixa1").checked;
    qry+= "&faixa2="+$("faixa2").checked;
    qry+= "&faixa3="+$("faixa3").checked;
    qry+= "&faixa4="+$("faixa4").checked;
    qry+= "&faixa5="+$("faixa5").checked;
    qry+= "&faixa6="+$("faixa6").checked;
    qry+= "&faixa7="+$("faixa7").checked;

    /**
     * se folha for complementar passamos o numero da complementar, o r48_semest
     */
    if ( document.form1.tipofol.value == 'r48' ) {
      var sComplementar = 0;
      if (document.form1.complementar) {
        sComplementar = document.form1.complementar.value;
      }
      qry += '&complementar=' + sComplementar;
    }

    jan = window.open('pes2_previdencia002.php'+qry,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
    jan.moveTo(0,0);
  }
}


function js_complementar(opcao) {

  selecionados = "";
  virgula_ssel = "";

  for(var i=0; i<document.form1.sselecionados.length; i++){
    selecionados+= virgula_ssel + document.form1.sselecionados.options[i].value;
    virgula_ssel = ",";
  }
  document.form1.colunas_sselecionados.value = selecionados;

  selecionados = "";
  virgula_ssel = "";
  for(var i=0; i<document.form1.nselecionados.length; i++){
    selecionados+= virgula_ssel + document.form1.nselecionados.options[i].value;
    virgula_ssel = ",";
  }
  document.form1.colunas_nselecionados.value = selecionados;

}
</script>