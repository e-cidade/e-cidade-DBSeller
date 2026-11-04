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
include(modification("dbforms/db_funcoes.php"));

$oGet = db_utils::postMemory($_GET);
$instit = db_getsession("DB_instit");
$anosu  = db_getsession("DB_anousu");

$sSqlTipoInscricao = "select k03_tipoinscricao from caixa.numpref where k03_anousu = {$anosu} and k03_instit = {$instit}";
$resultTipoInscricao = db_query($sSqlTipoInscricao);
if ($resultTipoInscricao || db_numrows($resultTipoInscricao) > 0) {
  $k03_tipoinscricao = db_utils::fieldsMemory($resultTipoInscricao, 0)->k03_tipoinscricao;
} else {
    db_erro("Configure o parametro de tipo inscrição");
}
?>
<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
  <script type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default">
<form method="post" id="frmVinculo" name="frmVinculo">
  <div class="container">
    <fieldset>
      <legend>Vínculo Receita Procedência</legend>
      <table>
        <tr>
            <td nowrap>
                <input type="hidden" id="k03_tipoinscricao" name="k03_tipoinscricao" value="<?= $k03_tipoinscricao ?>">
                <?php
                db_ancora("<b>Procedência:</b>", "js_pesquisaProced(true);", 1);
                ?>
            </td>
            <td nowrap>
                <?php
                db_input('proced', 10, 1,true,'text', 3);
                db_input('descricao_proced', 50, 0,true,'text',3,'');
                ?>
            </td>
        </tr>
      </table>
    </fieldset>
    <input type="button" id='btnSalvar' value="<?= ($oGet->db_opcao == 1) ? 'Salvar' : 'Alterar' ?>" onclick="js_salvar();"/>
  </div>
</form>
</body>
</html>
<script>
    // init
    js_getDados();

    function js_pesquisaProced(mostra) {
        const k03_tipoinscricao = document.getElementById('k03_tipoinscricao').value;
        if (!k03_tipoinscricao || k03_tipoinscricao == '0') {
            alert("Configure o parametro de tipo inscrição");
            return false;
        }

        let sUrl = '';
        if (k03_tipoinscricao == 1) {
            sUrl = 'func_procdiver.php?funcao_js=parent.js_mostraproced|dv09_procdiver|dv09_descr&k03_tipoinscricao='+k03_tipoinscricao;
        }

        if (k03_tipoinscricao == 2) {
            sUrl = 'func_proced.php?funcao_js=parent.js_mostraproced|v03_codigo|v03_descr';
        }

        if (sUrl.length == 0) {
            alert("Configure o parametro de tipo inscrição");
            return false;
        }

        js_OpenJanelaIframe('','db_iframe_proced', sUrl, 'Pesquisar Procedência', mostra);
    }

    function js_mostraproced() {
        db_iframe_proced.hide();

        let descricao_proced = document.getElementById('descricao_proced');
        let proced = document.getElementById('proced');

        descricao_proced.value = '';
        proced.value = '';

        if (typeof(arguments[1]) == 'boolean') {
            descricao_proced.value = arguments[0];
            if (arguments[1] == true) {

                proced.focus();
                proced.value = '';
            }

            return ;
        }

        descricao_proced.value = arguments[1];
        proced.value = arguments[0];
    }

    async function js_salvar() {
        const urlParams = js_urlToObject();
        const proced = document.getElementById('proced');
        const tipoinscricao = document.getElementById('k03_tipoinscricao');

        if (proced.value == '') {
            alert('Selecione uma Procedência');
            return false;
        }

        try {
            js_divCarregando('Aguarde, salvando os dados', 'msgBox');

            const params = {
                receita: urlParams.k02_codigo,
                procedencia: proced.value,
                tipoinscricao: tipoinscricao.value
            }
            const req = await CurrentWindow.axios.post('v4/api/tributario/caixa/vinculoreceitaprocedencia', params);
            const { data: resp } = req;

            alert(resp.message);
        } catch (error) {
            console.error(error);
            alert(error.response.data.message);
        } finally {
            js_removeObj('msgBox');
        }
    }

    async function js_getDados()
    {
        const urlParams = js_urlToObject();
        const params = {
            receita: urlParams.k02_codigo
        }

        if (!urlParams.k02_codigo) {
            return false;
        }

        try {
            js_divCarregando('Aguarde, buscando dados de procedência', 'msgBox');

            const params = {receita: urlParams.k02_codigo}
            const req = await CurrentWindow.axios.get('v4/api/tributario/caixa/vinculoreceitaprocedencia', {params});
            const { data: resp } = req;

            const vinculo = resp.data;

            if (vinculo) {
                const proced = document.getElementById('proced');
                const descricao_proced = document.getElementById('descricao_proced');

                proced.value = vinculo.proced;
                descricao_proced.value = vinculo.descricao_proced;
            }
        } catch (error) {
            console.error(error);
            alert(error.response.data.message);
        } finally {
            js_removeObj('msgBox');
        }
    }
</script>
