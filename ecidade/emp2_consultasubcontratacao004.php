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
  require_once(modification("libs/db_utils.php"));
  require_once(modification("libs/db_app.utils.php"));
  require_once(modification("libs/db_conecta.php"));
  require_once(modification("libs/db_sessoes.php"));

?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
      db_app::load("scripts.js, strings.js, prototype.js, estilos.css");
    db_app::load("datagrid.widget.js, grid.style.css, DBHint.widget.js");
    ?>
  </head>
  <body style="background-color: #ccc;">
        <fieldset>
          <legend><b>Recolhimento</b></legend>

            <div id="ctnItensRecolhimento">
            </div>
          </fieldset>
          <div id='ajudaItem' style='position:absolute;border:1px solid #FFDD00; display:none; text-indent: 15px;
                                   background-color: #FFFFCC;width: 70%; '>
        </div>
  </body>
</html>

<script type="text/javascript">

  var oGet = js_urlToObject();

  var oGridItensRecolhimento          = new DBGrid('oGridItensRecolhimento');
  oGridItensRecolhimento.nameInstance = "oGridItensRecolhimento";
  oGridItensRecolhimento.setCellWidth(new Array('50%', '50%'));
  oGridItensRecolhimento.setCellAlign(new Array('center',
                                           'center'));
  oGridItensRecolhimento.setHeader(new Array('Slip',
                                        'Valor'));
  oGridItensRecolhimento.hasTotalizador = true;
  oGridItensRecolhimento.show($('ctnItensRecolhimento'));


  function carregarItensRecolhimento() {

    js_divCarregando("Aguarde, carregando dados dos recolhimentos...", "msgBox");

    var oJson = {"exec":"getItensRecolhimento", "iCodigoRecolhimento":oGet.e23_sequencial};
    new Ajax.Request("emp2_consultasubcontratacao.RPC.php",
                    {method: 'post',
                    async: false,
                    parameters: 'json='+Object.toJSON(oJson),
                    onComplete: preencherGrid });
  }

  /**
   * Preenche a grid com os itens encontrados
   */
  function preencherGrid(oAjax) {

    js_removeObj("msgBox");
    var oRetorno = JSON.parse(oAjax.responseText);
      oGridItensRecolhimento.clearAll(true);

    var nTotal = new Number(0);
    oRetorno.aItensRecolhimento.each(function (oItem, iIndice) {
      var aLinha = new Array();
      aLinha[0]  = oItem.slip;
      aLinha[1]  = js_formatar(oItem.valor, "f");
        oGridItensRecolhimento.addRow(aLinha);
        oGridItensRecolhimento.aRows[iIndice].aCells[0].sEvents += "onmouseover='mostrarAjuda(\""+aLinha[0]+"\", true)'";
        oGridItensRecolhimento.aRows[iIndice].aCells[0].sEvents += "onmouseout='mostrarAjuda(\"\", false)'";
        oGridItensRecolhimento.aRows[iIndice].aCells[1].sEvents += "onmouseover='mostrarAjuda(\""+aLinha[1]+"\", true)'";
        oGridItensRecolhimento.aRows[iIndice].aCells[1].sEvents += "onmouseout='mostrarAjuda(\"\", false)'";

      nTotal = nTotal + new Number(oItem.valor);
    });

      oGridItensRecolhimento.renderRows();
      oGridItensRecolhimento.oFooter.rows[0].cells[1].innerHTML = js_formatar(nTotal, "f");
  }

  function mostrarAjuda(sTexto, lShow) {

    if (lShow) {

      el     =  $('ctnItensRecolhimento');
      var x  = 0;
      var y  = el.offsetHeight;
          x += el.offsetLeft;
          y += el.offsetTop;
      $('ajudaItem').innerHTML     = sTexto;
      $('ajudaItem').style.display = '';
      $('ajudaItem').style.top     = $('ctnItensRecolhimento').scrollTop + 20;
      $('ajudaItem').style.left    = x;

    } else {
     $('ajudaItem').style.display = 'none';
    }
  }


  function iniciar() {
      carregarItensRecolhimento();
  }
  iniciar();

</script>
