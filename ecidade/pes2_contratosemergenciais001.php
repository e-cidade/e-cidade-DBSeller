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

$clrotulo = new rotulocampo;
$clrotulo->label('rh164_datainicio');
$clrotulo->label('rh164_datafim');
$clrotulo->label('rh01_regist');
$clrotulo->label('z01_nome');

?>
<html>
  <head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <?php
    db_app::load('scripts.js, 
                 prototype.js, 
                 strings.js,
                 dates.js, 
                 DBLookUp.widget.js, 
                 DBDownload.widget.js, 
                 EmissaoRelatorio.js');
    ?>
  </script>  
  <link href="estilos.css" rel="stylesheet" type="text/css" />
  </head>
  <body>
    <form id="form1" name="form1" action="" method="post" class="container">
      <fieldset>
        <legend>Buscar Contratos Emergenciais</legend>
        <table class="form-container">
          <tr>
            <td>
              <?php echo $Lrh164_datainicio; ?>
            </td>
            <td>
              <?php
                db_inputdata(
                    'rh164_datainicio',
                    @$rh164_datainicio_dia,
                    @$rh164_datainicio_mes,
                    @$rh164_datainicio_ano,
                    true,
                    'text',
                    2,
                    ""
                );
                ?>
            </td>
          </tr>
          <tr>
            <td>
              <?php echo $Lrh164_datafim; ?>
            </td>
            <td>
              <?php
                db_inputdata(
                    'rh164_datafim',
                    @$rh164_datafim_dia,
                    @$rh164_datafim_mes,
                    @$rh164_datafim_ano,
                    true,
                    'text',
                    2,
                    ""
                );
                ?>
            </td>
          </tr>
          <tr>
            <td>
              <a id="procurarMatricula"><?php echo $Lrh01_regist; ?></a>
            </td>
            <td>
              <?php
                db_input('rh01_regist', 10, '', true, 'text', 1);
                db_input('z01_nome', 50, '', true, 'text', 3);
                ?>
            </td>
          </tr>
          <tr>
            <td> Forma Emissão </td>
            <td>
              <?php
                  db_select('formaEmissao', ["pdf"=>"PDF", "csv"=>"CSV"], true, 1); ?>
            </td>
          </tr>          
        </table>
      </fieldset>
      <input type="button" id="buscar" name="buscar" value="Buscar" onClick="emite()" />
    </form>
    <script type="text/javascript">

      var iCodigoRelatorio = 29;

      var oLookupServidor = new DBLookUp($("procurarMatricula"), $("rh01_regist"), $("z01_nome"), {
        "sArquivo"              : "func_rhpessoal.php",
        "sObjetoLookUp"         : "db_iframe_rhpessoal",
        "aParametrosAdicionais" : ["testarescisao=true&contratosEmergenciais=1"]
      });

      $("rh01_regist").className = "";

      function emite() {

        var sDataInicio = '';
        var sDataFim    = '';

        if(document.form1.rh01_regist.value == "" && 
           document.form1.rh164_datainicio.value == "" && 
           document.form1.rh164_datafim.value == "") {
          if(!confirm("Tem certeza que deseja emitir o relatório com todos contratos emergenciais?")) {
            return false;
          }
        }

        if(document.form1.rh164_datainicio.value == "" && document.form1.rh164_datafim.value != "") {
          alert("Informe uma data inicial para o período.");
          return false;
        }

        if(document.form1.rh164_datainicio.value != '' && document.form1.rh164_datafim.value != ''){
          sDataInicio = getDateInDatabaseFormat(document.form1.rh164_datainicio.value);
          sDataFim    = getDateInDatabaseFormat(document.form1.rh164_datafim.value);
          if(sDataFim < sDataInicio) {
            alert("A data final deve ser maior que a data inicial.");
            return false;
          }
        }

        var oParametros = {
            regist: $F("rh01_regist")      || 0,
            dataInicio: $F("rh164_datainicio") || '1979-01-01',
            dataFim: $F("rh164_datafim")    || '2099-12-31',
            formaEmissao: $F("formaEmissao"),
        };

        var oRelatorio = new EmissaoRelatorio("pes2_contratosemergenciais002.php", oParametros);
        oRelatorio.open();

      }

      /**
       * Trata o retorno da função js_imprimeRelatorio
       */
      function downloadArquivo(file) {
        var oDBDownload = new DBDownload();
        oDBDownload.addFile(file,'Contratos Emergenciais');
        oDBDownload.show();
      }

    </script>
    <?php
    db_menu();
    ?>
  </body>
</html>