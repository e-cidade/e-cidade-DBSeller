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

$oGet = db_utils::postMemory($_GET);
$oDaoProcesso = new cl_protprocesso;
$oJson = new Services_JSON();

$campos = '
    p58_dtproc as data_processo, p58_codproc as codigo_processo, z01_nome as titular, p51_descr as tipo_processo,
    cast((p58_numero || \'/\' || p58_ano) as varchar) as numero_processo
';
$where = "p58_processopai = {$oGet->codigo_processo}";

$sqlVolumes = "
    SELECT
        {$campos}
    FROM protprocesso
    INNER JOIN cgm 
        ON cgm.z01_numcgm = protprocesso.p58_numcgm
    INNER JOIN tipoproc
        ON tipoproc.p51_codigo = protprocesso.p58_codigo
    WHERE
        {$where}
    ORDER BY
        p58_dtproc DESC
";

$rsVolumes = $oDaoProcesso->sql_record($sqlVolumes);
$arrayVolumes = db_utils::getCollectionByRecord($rsVolumes, true, false, true);
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
           <b>Volumes</b>
         </legend>
          <div id='ctnDataGridVolumes' style="width: 100%;"></div>
      </fieldset>
    </div>
  </body>
</html>
<script>

var listaVolumes = '<?php echo $oJson->encode($arrayVolumes)?>';
var oDataGridVolumes = new DBGrid('gridVolumes');
var aHeaders = new Array(
    'Nº do Processo',
    'Processo',
    'Data',
    'Titular',
    'Tipo'
);

oDataGridVolumes.nameInstance = 'oDataGridVolumes';
oDataGridVolumes.setCellWidth(new Array('20%', '10%', '10%', '40%', '20%'));
oDataGridVolumes.setCellAlign(new Array('center', 'center'));
oDataGridVolumes.setHeader(aHeaders);
oDataGridVolumes.setHeight(250);
oDataGridVolumes.show($('ctnDataGridVolumes'));
oDataGridVolumes.clearAll(true);

var linhasVolumes = eval("(" + listaVolumes + ")");
var codigosVolumes = [];

linhasVolumes.each(function(oProcesso, iSeq) {
  var aLinha = new Array();
  aLinha[0] = oProcesso.numero_processo.urlDecode();
  aLinha[1] = oProcesso.codigo_processo.urlDecode();
  aLinha[2] = oProcesso.data_processo.urlDecode(); 
  aLinha[3] = oProcesso.titular.urlDecode();
  aLinha[4] = oProcesso.tipo_processo.urlDecode();
  
  oDataGridVolumes.addRow(aLinha);
  codigosVolumes.push(oProcesso.codigo_processo);
});

oDataGridVolumes.renderRows();

function consultaVolume(codproc){
    var sURL = 'pro3_consultaprocesso002.php?codproc='+codproc;
    js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_volume', sURL, 'Consulta Volume', true);
    parent.db_iframe_consultaprocesso.hide();
}

for (var i = 0; i < codigosVolumes.length; i++) {
    (function () {
        var codproc = codigosVolumes[i];
        $(`gridVolumesrowgridVolumes${i}`).addEventListener(
            'click',
            function() {
                consultaVolume(codproc)
            }
        );
    }()); 
}
</script>