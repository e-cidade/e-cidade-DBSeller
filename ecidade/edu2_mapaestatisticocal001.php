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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$lDbOpcao    = 1;
$dDataIniDia = isset($_GET['dtInicio']) ? date('d',strtotime(str_replace("/","-",$_GET['dtInicio'])))   :'01';
$dDataIniMes = isset($_GET['dtInicio']) ? date('m',strtotime(str_replace("/","-",$_GET['dtInicio'])))   :'01';
$dDataIniAno = isset($_GET['dtInicio']) ? date('Y',strtotime(str_replace("/","-",$_GET['dtInicio'])))   :date('Y');
$dDataFimDia = isset($_GET['dtFim'])    ? date('d',strtotime(str_replace("/","-",$_GET['dtFim'])))      :'31';
$dDataFimMes = isset($_GET['dtFim'])    ? date('m',strtotime(str_replace("/","-",$_GET['dtFim'])))      :'12';
$dDataFimAno = isset($_GET['dtFim'])    ? date('Y',strtotime(str_replace("/","-",$_GET['dtFim'])))      :date('Y');

if(!isset($_GET['calendario_selecionado'])) {
    $bCalendario = "SELECT distinct(ed52_c_descr) as nomecal
                  FROM calendario
                 WHERE ed52_i_ano = " . db_getsession("DB_anousu") . "
              ORDER BY ed52_c_descr";
    $sCalendario = db_query($bCalendario);
    $nCalendario = pg_num_rows($sCalendario);
}else {
    $bCalendario = "SELECT distinct(ed52_c_descr) as nomecal
                  FROM calendario
                 WHERE ed52_i_ano = {$_GET['calendario_selecionado']}
              ORDER BY ed52_c_descr";
    $sCalendario = db_query($bCalendario);
    $nCalendario = pg_num_rows($sCalendario);
}

?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("scripts.js,
                  prototype.js");

    db_app::load("estilos.css,
                  grid.style.css"
                );
    ?>
  </head>
  <body style='margin-top: 25px' bgcolor="#cccccc">
  <form name="form1" id='frmDiarioClasse' method="post">
    <center>
      <div style='display:table;' id='ctnForm'>
        <fieldset>
        <legend style="font-weight: bold">Mapa Estatístico por Calendário</legend>
          <table class="tabela" border='0' width="100%">
            <tr>
              <td>
                <b>Período:</b>
              </td>
              <td>
                <?php db_inputdata('dtInicio', "$dDataIniDia", "$dDataIniMes", "$dDataIniAno", true, 'text', $lDbOpcao, "")?>
                <b>até</b>
                <?php db_inputdata('dtFim', "$dDataFimDia", "$dDataFimMes", "$dDataFimAno", true, 'text', $lDbOpcao,"")?>
              </td>
                <td>
                    <input type="button" name="btnFiltrar" id="btnFiltrar" value="filtrar"  style="cursor: pointer" />
                </td>
            </tr>
         <tr>
         <td>
          <b>Calendários:</b><br>
         </td>
         <td>
          <select name="calendario" id="calendario" multiple onchange="js_Assinatura()"
                  style="font-size:9px;width:200px;height:180px;" >
           <?php
           for($abc = 0; $abc < $nCalendario; $abc++){
               db_fieldsmemory($sCalendario, $abc);
           ?>
             <option value='<?php echo $nomecal; ?>'><?php echo $nomecal; ?></option>
           <?php
           }
           ?>
          </select>
         </td>
         <td>
          <fieldset style="align:center">
           Para selecionar mais de um calendário,<br>mantenha pressionada a tecla CTRL
           <br>e clique sobre os nomes.
          </fieldset>
         </td>
        </tr>

          </table>
        </fieldset>
      </div>
      <input name="btnImprimir" id="btnImprimir" type="button" value="Imprimir">
    </center>
  </form>
  <?php
    db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
  ?>
</body>

<script type="text/javascript">

    $('btnFiltrar').observe('click', function () {

      if(!validaDatas()){
          return false;
      }

      var dtInicio = $F('dtInicio');
      var dtFim    = $F('dtFim');

      var sUrlRelatorio = 'edu2_mapaestatisticocal001.php';
      sUrlRelatorio    += '?dtInicio=' + dtInicio;
      sUrlRelatorio    += '&dtFim=' + dtFim;
      sUrlRelatorio    += '&calendario_selecionado=' + $('dtInicio_ano').value;

     window.location.href= sUrlRelatorio;

    });



  $('btnImprimir').observe('click', function () {

    if ($('calendario').value == "") {
      alert("Informe o Calendário desejado!");
      return false;
    }

    if ( validaDatas() ) {

      var dtInicio = $F('dtInicio');
      var dtFim    = $F('dtFim');
      var nomeCal    = $F('calendario');

      var sUrlRelatorio = 'edu2_mapaestatisticocal002.php';
      sUrlRelatorio    += '?dtInicio=' + dtInicio;
      sUrlRelatorio    += '&dtFim=' + dtFim;
      sUrlRelatorio    += '&nomeCal=' + nomeCal;

      oWindow = window.open(sUrlRelatorio, '',
                        'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0');
      oWindow.moveTo(0,0);
    }
  })


  function validaDatas() {

    var oDtInicio = new Date($('dtInicio_ano').value, $('dtInicio_mes').value, $('dtInicio_dia').value);
    var oDtFinal  = new Date($('dtFim_ano').value, $('dtFim_mes').value, $('dtFim_dia').value);

    if ($('dtInicio_dia').value == "" || $('dtInicio_mes').value == "" || $('dtInicio_ano').value == ""
        ||$('dtFim_dia').value == "" || $('dtFim_mes').value == "" || $('dtFim_ano').value == "") {

      alert("Preencha as datas corretamente!");
      return false;
    }

    if ( oDtInicio.getTime() > oDtFinal.getTime() ) {

      alert("Início do período não pode ser maior que o final do período!");
      return false;
    }

    if ( $('dtInicio_ano').value != $('dtFim_ano').value ) {

      alert ("O período deve estar dentro do mesmo ano.");
      return false;
    }

    return true;
  }
</script>
</html>
