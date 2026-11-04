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

use ECidade\Saude\Laboratorio\Repository\Parametros;

require_once(modification("dbforms/db_funcoes.php"));
require_once(modification('libs/db_stdlibwebseller.php'));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification('libs/db_utils.php'));
require_once(modification('libs/db_app.utils.php'));

$cllab_labsetor = new cl_lab_labsetor;
$cllab_requisicao = new cl_lab_requisicao;
$cllab_exame = new cl_lab_exame;
$clrotulo = new rotulocampo;

$clrotulo->label("la08_c_descr");
$clrotulo->label("la21_i_codigo");
$clrotulo->label("la23_i_codigo");
$clrotulo->label("la23_c_descr");
$clrotulo->label("la24_i_codigo");

/**
 * Função para descobrir o laboratorio que o usuario esta logado
 * @return integer Codigo do laboratorio logado
 */
function laboratorioLogado()
{
    $iUsuario = db_getsession('DB_id_usuario');
    $iDepto = db_getsession('DB_coddepto');
    $oLab_labusuario = new cl_lab_labusuario();
    $oLab_labdepart = new cl_lab_labdepart();

    $sCampos = 'la02_i_codigo, la02_c_descr';
    $sql = $oLab_labusuario->sql_query(null, $sCampos, "la02_i_codigo", " la05_i_usuario = {$iUsuario}");
    $rResult = $oLab_labusuario->sql_record($sql);

    if ($oLab_labusuario->numrows == 0) {
        $sCampos = 'la02_i_codigo, la02_c_descr';
        $where = "la03_i_departamento = {$iDepto}";
        $sql = $oLab_labdepart->sql_query(null, $sCampos, "la02_i_codigo",$where);
        $rResult = $oLab_labdepart->sql_record($sql);

        if ($oLab_labdepart->numrows == 0) {
            return false;
        }
    }

    $oLab = db_utils::getCollectionByRecord($rResult);
    return $oLab[0]->la02_i_codigo;
}

$iLaboratorioLogado = laboratorioLogado();

if (isset($requisicao)) {
    if ($sSituacao != '6 - importado') {
        $sParametros = "requisicao={$requisicao}&requiitem={$requiitem}&iLabSetor={$iLabSetor}&iModelo=$iModelo";
        $sParametros .= "&liberarPorExame={$liberarPorExame}&iAtributo={$iAtributo}";

        echo "<script>
            jan = window.open( 'lab4_emissaoresultnovo002.php?{$sParametros}', '', 'width=1000,height=600' );
          </script>";
    } else {
        $oDaoLabEmissao = new cl_lab_emissao();
        $sWhere = "la34_i_requiitem = {$requiitem}";

        if (!empty($iLabSetor)) {
            $sWhere .= " and la24_i_codigo = {$iLabSetor}";
        }

        $sCampos = "la34_o_laudo, la34_c_nomearq";
        $sSql = $oDaoLabEmissao->sql_query_labsetor(null, $sCampos, "la34_d_data desc, la34_c_hora desc", $sWhere);
        $rsEmissao = $oDaoLabEmissao->sql_record($sSql);

        if ($oDaoLabEmissao->numrows > 0) {
            $oEmissao = db_utils::fieldsmemory($rsEmissao, 0);
            db_inicio_transacao();
            if (pg_lo_export($oEmissao->la34_o_laudo, 'tmp/' . $oEmissao->la34_c_nomearq, $conn)) {
                ?>
                <script>
                    jan = window.open('tmp/<?=$oEmissao->la34_c_nomearq?>',
                        '',
                        'width=' + (screen.availWidth - 5) +
                        ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 ');
                </script>
                <?php
            } else {
                db_msgbox("Erro durante abertura do arquivo!");
            }

            db_fim_transacao();
        } else {
            db_msgbox("Arquivo importado, porém não à registro de emissão!");
        }
    }
}

try {
    $parametrosRepository = Parametros::getInstancia();
    $parametros = $parametrosRepository->buscar();
} catch (Exception $erro) {
    db_msgbox($erro->getMessage());
}

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/DBFormCache.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/DBFormSelectCache.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script language="JavaScript" type="text/javascript"
            src="scripts/classes/saude/laboratorio/ViewNumeroControleInterno.classe.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <!-- PLUGIN AUTENTICADORA - Adicionando script scripts/autenticadora-client.js -->
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default">
<?php
if ($iLaboratorioLogado == 0) { ?>
    <table width='100%'>
        <tr>
            <td align='center'>
                <br><br>
                <font color='#FF0000' face='arial'>
                    <b>Usuário ou departamento não consta como laboratório!<br>
                    </b>
                </font>
            </td>
        </tr>
    </table>
    </center>
    <?php
    db_menu(
        db_getsession("DB_id_usuario"),
        db_getsession("DB_modulo"),
        db_getsession("DB_anousu"),
        db_getsession("DB_instit")
    );
    exit;
}
?>
<div class='container'>
    <fieldset>
        <legend>Emissão/Reemissão de Resultado</legend>
        <form name='form1'>

            <input type="hidden" id="cgsId">

            <table class="form-container">
                <tr>
                    <td id="viewNumeroControleInterno" colspan="2"></td>
                </tr>
                <tr>
                    <td title="<?= @$Tla22_i_codigo ?>">
                        <?php
                        db_ancora('<b>Requisição:</b>', "js_pesquisala22_i_codigo(true);", ""); ?>
                    </td>
                    <td>
                        <?php
                        db_input(
                            'la22_i_codigo',
                            10,
                            @$Ila22_i_codigo,
                            true,
                            'text',
                            "",
                            " onchange='js_pesquisala22_i_codigo(false);'"
                        ) ?>
                        <?php
                        db_input('z01_v_nome2', 50, @$Iz01_v_nome, true, 'text', 3, '') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="la23_i_codigo"><?php
                            db_ancora('<b>Setor:</b>', "js_pesquisala23_i_codigo(true);", ""); ?></label>
                    </td>
                    <td>
                        <?php
                        db_input(
                            'la23_i_codigo',
                            10,
                            $Ila23_i_codigo,
                            true,
                            'text',
                            "",
                            " onchange='js_pesquisala23_i_codigo(false);'"
                        ) ?>
                        <?php
                        db_input('la23_c_descr', 50, $Ila23_c_descr, true, 'text', 3, '') ?>
                        <?php
                        db_input('la24_i_codigo', 10, $Ila24_i_codigo, true, 'hidden', "", "") ?>
                    </td>
                </tr>
                <tr>
                    <td title="requiitem">
                        <label for='la08_i_codigo'><?php
                            db_ancora('<b>Exame:</b>', "js_pesquisala21_i_codigo(true);", ""); ?></label>
                    </td>
                    <td>
                        <?php
                        db_input(
                            'la08_i_codigo',
                            10,
                            @$Ila08_i_codigo,
                            true,
                            'text',
                            "",
                            " onchange='js_pesquisala21_i_codigo(false);'"
                        ) ?>
                        <?php
                        db_input('la21_i_codigo', 10, @$Ila21_i_codigo, true, 'hidden', "", "") ?>
                        <?php
                        db_input('la21_c_situacao', 10, @$Ila21_c_situacao, true, 'hidden', "", "") ?>
                        <?php
                        db_input('la08_c_descr', 50, @$Ila08_c_descr, true, 'text', 3, '') ?>
                    </td>
                </tr>
                <tr>
                    <td><label for="modelo">Modelo: </label></td>
                    <td>
                        <select id='modelo'
                                class="readonly"
                                aria-disabled="true"
                                tabindex="-1"
                                style="pointer-events: none; touch-action: none;">>
                            <option <?= (int)$parametros->getModeloLaudo() === 1 ? 'selected' : '' ?> value="1">MODELO
                                1
                            </option>
                            <option <?= (int)$parametros->getModeloLaudo() === 2 ? 'selected' : '' ?> value="2">MODELO
                                2
                            </option>
                            <option <?= (int)$parametros->getModeloLaudo() === 3 ? 'selected' : '' ?> value="3">MODELO
                                3
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="atributos">Tipo de Impressão: </label></td>
                    <td>
                        <select id='atributos'>
                            <option value="1">PDF</option>
                            <option value="2">MATRICIAL</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>Exibir Liberação por Exame: </span>
                    </td>
                    <td>
                        <select name="liberarPorExame"
                                id="liberarPorExame"
                                class="readonly"
                                aria-disabled="true"
                                tabindex="-1"
                                style="pointer-events: none; touch-action: none;">
                            <option <?= $parametros->isExibeLiberacaoPorExame() === 'f' ? 'selected' : '' ?> value="0">
                                NÃO
                            </option>
                            <option <?= $parametros->isExibeLiberacaoPorExame() === 't' ? 'selected' : '' ?> value="1">
                                SIM
                            </option>
                        </select>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
    <input name='start' type='button' value='Gerar' onclick="js_mandaDados()">    
</div>
<div id="gridExames" class="container gridExames" style="margin-top:10px;width:1300px"></div>
<?php
db_menu(
    db_getsession("DB_id_usuario"),
    db_getsession("DB_modulo"),
    db_getsession("DB_anousu"),
    db_getsession("DB_instit")
); ?>
</body>
</html>
<script>

    modeloImpressaoSelect = $('atributos'),
        modeloSelect = $('modelo');

    let collectionExames = new Collection().setId('codigo');
    let gridExames = DatagridCollection.create(collectionExames);
    gridExames.configure({order: false, height: 200});
    gridExames.addColumn('descricao', {label: 'Exame', width: '40%'});
    gridExames.addColumn('situacao', {label: 'Situação', width: '20%'});
    gridExames.addColumn('motivo', {label: 'Motivo', width: '40%'});
    gridExames.show($('gridExames'));

    function buscaExames() {
        const form = new FormData();

        form.append('acao', 'buscaExamesPorRequisicao');
        form.append('codigoRequisicao', $('la22_i_codigo').value);

        HttpClient.post('lab3_statusmaterialexame.RPC.php', {body: form}).then(response => {
            if (response.erro) {
                return alert(response.mensagem);
            }

            var chave = 0;
            var exames = [];

            for (var i = 0; i < response.listaExames.length; i++) {
                exames.push({
                    id: chave,
                    la21_c_situacao: response.listaExames[i].la21_c_situacao,
                    la08_c_descr: response.listaExames[i].la08_c_descr,
                    la21_motivonovacoleta: response.listaExames[i].la21_motivonovacoleta || ''
                });

                chave += 1;
            }
            setGridExames(exames);
        });
    }

    const setGridExames = (listaExames) => {
        gridExames.clear();
        var liberado = true;

        for (var i = 0; i < listaExames.length; i++) {
            var motivo = '';
            if (listaExames[i].la21_motivonovacoleta.length > 75) {
                motivo = listaExames[i].la21_motivonovacoleta.substr(0, 75);
                motivo += '...'
            } else if (listaExames[i].la21_motivonovacoleta != '') {
                motivo = listaExames[i].la21_motivonovacoleta;
            }
            var obj = {
                codigo: listaExames[i].id,
                situacao: listaExames[i].la21_c_situacao,
                descricao: listaExames[i].la08_c_descr,
                motivo: motivo
            };

            collectionExames.add(obj, 1);
        }

        gridExames.reload();

        const trs = gridExames.target.childNodes[0].childNodes[0].childNodes[1].childNodes[0].childNodes[0].children;
        for (var i = 0; i < trs.length; i++) {
            if (trs[i].children[1].childNodes[0].data == '9 - Nova Coleta' || trs[i].children[1].childNodes[0].data == '40 - Nova Coleta') {
                trs[i].setAttribute('style', 'background-color: gold');
            } else if (trs[i].children[1].childNodes[0].data == '7 - Conferido' || trs[i].children[1].childNodes[0].data == '60 - Conferido') {
                trs[i].setAttribute('style', 'background-color: #80b554');
            } else if(trs[i].children[1].childNodes[0].data == '45 - Em processamento'){
                trs[i].setAttribute('style', 'background-color: lightblue');
            } else if(trs[i].children[1].childNodes[0].data == '80 - Amostra Rejeitada - Nova Coleta'){
                trs[i].setAttribute('style', 'background-color: lightcoral');
            }

            if (trs[i].children[2].childNodes[0].data != "") {
                trs[i].children[2].setAttribute('title', listaExames[i].la21_motivonovacoleta);
            }
            ;
        }
    }

    var viewNumeroControleInterno = new ViewNumeroControleInterno('viewNumeroControleInterno', true);
    viewNumeroControleInterno.setRequisicaoElemento($('la22_i_codigo'));
    viewNumeroControleInterno.show($('viewNumeroControleInterno'));

    if (viewNumeroControleInterno.getParametroAtivo()) {
        viewNumeroControleInterno.inputNumero.setAttribute('style', 'margin-left:16px');
    }

    modeloImpressaoSelect.addEventListener("change", (event) => {
        if (modeloImpressaoSelect.value == 2) {
            modeloSelect.value = 1;
            modeloSelect.disabled = true;
        } else {
            modeloSelect.disabled = false;
        }
        // PLUGIN saudeassinatura - Adicionado validação para matricial
    });

    function js_limpaCamposTrocaReq() {

        $('la08_i_codigo').value = '';
        $('la21_i_codigo').value = '';
        $('la21_c_situacao').value = '';
        $('la08_c_descr').value = '';
        $('la23_i_codigo').value = '';
        $('la23_c_descr').value = '';
        $('la24_i_codigo').value = '';
    }

    function js_pesquisala22_i_codigo(mostra) {

        if (mostra == true) {
            js_OpenJanelaIframe(
                '',
                'db_iframe_lab_requisicao',
                'func_lab_requisicao.php?autoriza=2'
                + '&funcao_js=parent.js_mostrarequisicao1|la22_i_codigo'
                + '|z01_v_nome|z01_i_cgsund',
                'Pesquisa',
                true
            );
        } else {

            if (document.form1.la22_i_codigo.value != '') {
                js_OpenJanelaIframe(
                    '',
                    'db_iframe_lab_requisicao',
                    'func_lab_requisicao.php?autoriza=2'
                    + '&pesquisa_chave=' + document.form1.la22_i_codigo.value
                    + '&funcao_js=parent.js_mostrarequisicao',
                    'Pesquisa',
                    false
                );
            } else {
                document.form1.z01_v_nome2.value = '';
                apagaIdadeCompleta();
            }
        }
    }

    function js_mostrarequisicao(chave, erro, cgs) {

        document.form1.z01_v_nome2.value = chave;
        apagaIdadeCompleta();

        if (erro == true) {

            document.form1.la22_i_codigo.focus();
            document.form1.la22_i_codigo.value = '';
        } else {
            document.getElementById('cgsId').value = cgs;
            existeCgsUnd();
        }

        if (viewNumeroControleInterno.getParametroAtivo() && $('la22_i_codigo').value != '') {
            viewNumeroControleInterno.getNumeroControleInternoPorRequisicao($('la22_i_codigo').value);
        }

        js_limpaCamposTrocaReq();

        buscaExames();
    }

    function js_mostrarequisicao1(chave1, chave2, cgs) {

        document.form1.la22_i_codigo.value = chave1;
        document.form1.z01_v_nome2.value = chave2;
        db_iframe_lab_requisicao.hide();
        apagaIdadeCompleta();

        if (viewNumeroControleInterno.getParametroAtivo() && $('la22_i_codigo').value != '') {
            viewNumeroControleInterno.getNumeroControleInternoPorRequisicao($('la22_i_codigo').value);
        }

        js_limpaCamposTrocaReq();

        buscaExames();

        document.getElementById('cgsId').value = cgs;
        existeCgsUnd();
    }

    function js_pesquisala21_i_codigo(mostra) {

        if (document.form1.la22_i_codigo.value == '') {

            alert('Escolha uma requisição primeiro.');
            js_limpaCamposTrocaReq();
            return false;
        }

        sPesq = 'la21_i_requisicao=' + document.form1.la22_i_codigo.value;
        if (document.form1.la23_i_codigo.value != '') {
            sPesq += '&la24_i_setor=' + document.form1.la23_i_codigo.value;    
        }         
        sPesq += '&sSituacao=|60 - Conferido|,|6 - importado|,|70 - Entregue|';

        if (mostra == true) {
            js_OpenJanelaIframe(
                '',
                'db_iframe_lab_requiitem',
                'func_lab_requiitem.php?' + sPesq + '&funcao_js=parent.js_mostrarequiitem1|la08_i_codigo|la08_c_descr'
                + '|la21_i_codigo|la21_c_situacao',
                'Pesquisa',
                true
            );
        } else {

            if (document.form1.la08_i_codigo.value != '') {
                js_OpenJanelaIframe(
                    '',
                    'db_iframe_lab_requiitem',
                    'func_lab_requiitem.php?' + sPesq + '&pesquisa_chave=' + document.form1.la08_i_codigo.value
                    + '&funcao_js=parent.js_mostrarequiitem',
                    'Pesquisa',
                    false
                );
            } else {

                document.form1.la08_c_descr.value = '';
                document.form1.la21_c_situacao.value = '';
            }
        }
    }

    function js_mostrarequiitem(chave, erro, requiitem, situacao) {

        document.form1.la08_c_descr.value = chave;

        if (erro == true) {

            document.form1.la08_i_codigo.focus();
            document.form1.la08_i_codigo.value = '';
        } else {

            document.form1.la21_i_codigo.value = requiitem;
            document.form1.la21_c_situacao.value = situacao;
        }
    }

    function js_mostrarequiitem1(chave1, chave2, requiitem, situacao) {

        document.form1.la08_i_codigo.value = chave1;
        document.form1.la08_c_descr.value = chave2;
        document.form1.la21_i_codigo.value = requiitem;
        document.form1.la21_c_situacao.value = situacao;
        db_iframe_lab_requiitem.hide();
    }

    function js_mandaDados() {

        oF = document.form1;

        if (!js_validaDados()) {
            return false;
        }

        oDBFormCache.save();

        var sParametros = 'requisicao=' + oF.la22_i_codigo.value;
        sParametros += '&requiitem=' + oF.la21_i_codigo.value;
        sParametros += '&sSituacao=' + oF.la21_c_situacao.value;
        sParametros += '&iLabSetor=' + $F('la23_i_codigo');
        sParametros += '&iModelo=' + $F('modelo');
        sParametros += '&liberarPorExame=' + $F('liberarPorExame');
        sParametros += '&iAtributo=' + oF.atributos.value;

        if (modeloImpressaoSelect.value == 1) {
            let url = 'lab4_emissaoresult001.php?' + sParametros;

            location.href = url;
        } else if (modeloImpressaoSelect.value = 2) {
            new AjaxRequest('lab4_emissaoresultnovo002.php?' + sParametros, {}, function (retorno, erro) {

                if (erro || !retorno.utilizarAutenticadoraNova) {
                    alert(retorno.mensagem);
                    return false;
                }

                // PLUGIN AUTENTICADORA - Conectando com AutenticadoraClient
            }).setMessage("Aguarde, gerando os dados para impressão...").execute();
        } else {
            alert('Modelo de impressão inválido');
            return false;
        }
    }

    function js_validaDados() {

        oF = document.form1;

        if (oF.la22_i_codigo.value == '') {

            alert('Preencha os dados do formulario.');
            return false;
        }

        return true;
    }

    /**
     * Função para buscar os setores cadastrados para o laboratório que o usuário está logado
     * @param  {boolean} lMostra
     */
    function js_pesquisala23_i_codigo(lMostra) {

        if (empty($F('la22_i_codigo'))) {

            alert("Preencha primeiro a Requisição.");
            $('la23_i_codigo').value = '';
            return;
        }

        var sGet = "la22_i_codigo= " + $F('la22_i_codigo');        
        if($('la08_i_codigo').value.length > 0){            
            sGet += "&exames="+$('la08_i_codigo').value;
        }

        if (lMostra) {

            sGet += '&funcao_js=parent.js_mostralab_labsetor1|la24_i_setor|la23_c_descr|la24_i_codigo';
            js_OpenJanelaIframe('', 'db_iframe_lab_labsetor', 'func_lab_labsetor.php?' + sGet, 'Pesquisa', true);
        } else {

            if ($F('la23_i_codigo') != '') {

                sGet += '&pesquisa_chave=' + $F('la23_i_codigo') + '&funcao_js=parent.js_mostralab_labsetor';
                js_OpenJanelaIframe('', 'db_iframe_lab_labsetor', 'func_lab_labsetor.php?' + sGet, 'Pesquisa', false);
            } else {
                $('la23_c_descr').value = '';
            }
        }
    }

    /**
     * Função chamada após selecionar o setor desejado.
     * Altera os valores do código do setor e da descrição pelo selecionado.
     * @param  {integer} iCodigoSetor
     * @param  {string}  sDescricaoSetor
     */
    function js_mostralab_labsetor1(iCodigoSetor, sDescricaoSetor, iCodigoLabSetor) {

        $('la23_i_codigo').value = iCodigoSetor;
        $('la23_c_descr').value = sDescricaoSetor;
        $('la24_i_codigo').value = iCodigoLabSetor;
        db_iframe_lab_labsetor.hide();
    }

    /**
     * Função chamada após digitado código do setor desejado.
     * Caso código exista, preenche o campo descrição do setor com o valor referênte.
     * @param  {string}  sDescricaoSetor
     * @param  {boolean} lErro
     */
    function js_mostralab_labsetor(sDescricaoSetor, lErro, iCodigoLabSetor) {

        $('la23_c_descr').value = sDescricaoSetor;
        $('la24_i_codigo').value = iCodigoLabSetor;

        if (lErro) {

            $('la23_i_codigo').focus();
            $('la23_i_codigo').value = '';
            $('la24_i_codigo').value = '';
            return;
        }
    }

    var oDBFormCache = new DBFormCache('oDBFormCache', 'lab4_emissaoresult001.php');
    (function () {

        oDBFormCache.setElements([$('atributos')]);
        oDBFormCache.load();
        // PLUGIN saudeassinatura - Adicionado validação para matricial

        $('la22_i_codigo').focus();
    })();

    function existeCgsUnd() {
        let cgsUnd = document.getElementById('cgsId').value;

        if (cgsUnd !== '') {
            let parametros = {
                'sExecucao': 'buscarDadosCgs',
                'iCgs': cgsUnd
            };

            let dadosRequisicao = {};
            dadosRequisicao.method = 'post';
            dadosRequisicao.parameters = 'json=' + Object.toJSON(parametros);
            dadosRequisicao.onComplete = function (resposta) {

                let retorno = JSON.parse(resposta.responseText);

                retornaIdadePaciente(retorno);
            };

            const rpc = 'sau4_cgs.RPC.php';
            new Ajax.Request(rpc, dadosRequisicao);
            return;
        }

        return;
    }

    function retornaIdadePaciente(retorno) {
        let idadeCompleta = retorno.oCgs.sIdadeCompleta.urlDecode();

        let divNumeroControleInterno = document.querySelector("#viewNumeroControleInterno > div");

        $('viewNumeroControleInterno').insertAdjacentHTML(
            'beforeend',
            `<p id="idadeCompleta">Idade: <span style="color: red;">${idadeCompleta}</span></p>`);

        document.querySelector("#idadeCompleta").style.cssText = 'margin-left: 415px; font-weight: normal;';

        if (divNumeroControleInterno.style.display !== 'none') {
            document.querySelector("#viewNumeroControleInterno > div").style.cssText = 'display:inline-block;';
            document.querySelector("#idadeCompleta").style.cssText = 'display:inline-block; margin-left: 120px; font-weight: normal;';
        }

        return;
    }

    function apagaIdadeCompleta() {
        let tagIdadeCompleta = document.querySelector("#idadeCompleta");

        if (tagIdadeCompleta) {
            tagIdadeCompleta.remove();
        }
    }
</script>
