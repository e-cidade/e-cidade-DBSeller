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
          <legend><b>Subcontratações</b></legend>

            <div id="ctnItensSubcontratacao">
            </div>
          </fieldset>
          <div id='ajudaItem' style='position:absolute;border:1px solid #FFDD00; display:none; text-indent: 15px;
                                   background-color: #FFFFCC;width: 70%; '>
        </div>
  </body>
</html>

<script type="text/javascript">

  var oGet = js_urlToObject();

  var oGridItensSubcontratacao          = new DBGrid('oGridItensSubcontratacao');
  oGridItensSubcontratacao.nameInstance = "oGridItensSubcontratacao";
  oGridItensSubcontratacao.setCellWidth(new Array('40%', '15%', '15%', '10%', '10%'));
  oGridItensSubcontratacao.setCellAlign(new Array('left',
                                           'left',
                                           'center',
                                           'center',
                                           'right'));
  oGridItensSubcontratacao.setHeader(new Array('SUBCONTRATADO',
                                        'CPF/CNPJ',
                                        'RETENÇÃO',
                                        'CÓDIGO DO IRRF',
                                        'VALOR RETIDO'));
  oGridItensSubcontratacao.hasTotalizador = true;
  oGridItensSubcontratacao.show($('ctnItensSubcontratacao'));


  function carregarItensSubcontratacao() {

    js_divCarregando("Aguarde, carregando dados da subcontratação...", "msgBox");

    var oJson = {"exec":"getItensSubcontratacao", "iCodigoSubcontratacao":oGet.e23_sequencial};
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
      oGridItensSubcontratacao.clearAll(true);

    var nTotal = new Number(0);
    oRetorno.aItensSubcontratacao.each(function (oItem, iIndice) {
      var aLinha = new Array();
      aLinha[0]  = oItem.nome_subcontratado;
      aLinha[1]  = oItem.cpf_cnpj_subcontratado;
      aLinha[2]  = oItem.retencao_descricao;
      aLinha[3]  = oItem.codigo_retencao,
      aLinha[4]  = js_formatar(oItem.valorirrfretido_subcontratado, "f");
      oGridItensSubcontratacao.addRow(aLinha);
      oGridItensSubcontratacao.aRows[iIndice].aCells[2].sEvents += "onmouseover='mostrarAjuda(\""+aLinha[2]+"\", true)'";
      oGridItensSubcontratacao.aRows[iIndice].aCells[2].sEvents += "onmouseout='mostrarAjuda(\"\", false)'";
      oGridItensSubcontratacao.aRows[iIndice].aCells[0].sEvents += "onmouseover='mostrarAjuda(\""+aLinha[0]+"\", true)'";
      oGridItensSubcontratacao.aRows[iIndice].aCells[0].sEvents += "onmouseout='mostrarAjuda(\"\", false)'";

      nTotal = nTotal + new Number(oItem.valorirrfretido_subcontratado);
    });

      oGridItensSubcontratacao.renderRows();
      oGridItensSubcontratacao.oFooter.rows[0].cells[4].innerHTML = js_formatar(nTotal, "f");
  }

  function mostrarAjuda(sTexto, lShow) {

    if (lShow) {

      el     =  $('ctnItensSubcontratacao');
      var x  = 0;
      var y  = el.offsetHeight;
          x += el.offsetLeft;
          y += el.offsetTop;
      $('ajudaItem').innerHTML     = sTexto;
      $('ajudaItem').style.display = '';
      $('ajudaItem').style.top     = $('ctnItensSubcontratacao').scrollTop + 20;
      $('ajudaItem').style.left    = x;

    } else {
     $('ajudaItem').style.display = 'none';
    }
  }


  function iniciar() {
    carregarItensSubcontratacao();
  }
  iniciar();

</script>
