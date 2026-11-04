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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));

$oGet                   = db_utils::postMemory($_GET);
$oDaoProcessosApensados = db_utils::getDao('processosapensados');
$oJson                  = new Services_JSON();
$sCampos                = 'p58_dtproc as data_processo, p30_procapensado as codigo_processo, z01_nome as titular,';
$sCampos               .= 'p51_descr as tipo_processo, extract(year from p58_dtproc) as ano_processo, p58_numero as numero_processo';
$sWhere                 = "p30_procprincipal = {$oGet->codigo_processo}";
$sSqlProcessosApensados = $oDaoProcessosApensados->sql_query_processo_apensado(null, $sCampos, "p58_codproc", $sWhere);
$rsProcessosApensados   = $oDaoProcessosApensados->sql_record($sSqlProcessosApensados);
$aProcessosApensados    = db_utils::getCollectionByRecord($rsProcessosApensados, true, false, true);

?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
     db_app::load('scripts.js, prototype.js, strings.js, datagrid.widget.js, DBHint.widget.js');
     db_app::load('estilos.css, grid.style.css');
    ?>
  </head>
  <body style='background-color: #cccccc'>
    <div>
      <fieldset>
         <legend>
           <b>Processos Apensados</b>
         </legend>
          <div id='ctnDataGridApensados' style="width: 100%;"></div>
      </fieldset>
    </div>
  </body>
</html>
<script>

var sListaProcessosApensados    = '<?php echo $oJson->encode($aProcessosApensados)?>';
var oDataGridApensados          = new DBGrid('gridApensados');
oDataGridApensados.nameInstance = 'oDataGridApensados';
var aHeaders                    = new Array('Processo',
                                            'Data',
                                            'Titular',
                                            'Tipo'
                                            );
oDataGridApensados.setCellWidth(new Array('10%', '10%', '60%', '20%'));
oDataGridApensados.setCellAlign(new Array('center', 'center'));
oDataGridApensados.setHeader(aHeaders);
oDataGridApensados.setHeight(250);
oDataGridApensados.show($('ctnDataGridApensados'));
oDataGridApensados.clearAll(true);
var aLinhasProcessosApensados = eval("("+sListaProcessosApensados+")");

aLinhasProcessosApensados.each(function(oProcesso, iSeq) {
  var aLinha  = new Array();
  aLinha[0]   = `
  <a
      onclick="js_consultaProcesso(${oProcesso.codigo_processo.urlDecode()})"
      class="dbancora"
      value="${oProcesso.codigo_processo.urlDecode()}">
      ${oProcesso.numero_processo.urlDecode()}/${oProcesso.ano_processo.urlDecode()}
  </a>`;
  aLinha[1]   = oProcesso.data_processo.urlDecode();
  aLinha[2]   = oProcesso.titular.urlDecode();
  aLinha[3]   = oProcesso.tipo_processo.urlDecode();
  oDataGridApensados.addRow(aLinha);

});
oDataGridApensados.renderRows();

function js_consultaProcesso(codigoProcesso) {
    js_OpenJanelaIframe(
        'CurrentWindow.corpo',
        'db_iframe_processo',
        "pro3_consultaprocesso002.php?codproc=" + codigoProcesso,
        'Consulta de Processo',
        true
    );
}
</script>
