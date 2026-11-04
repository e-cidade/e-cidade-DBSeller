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
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_libcontabilidade.php"));
require_once(modification("dbforms/db_funcoes.php"));
$iEscola = db_getsession("DB_coddepto");

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/arrays.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        select {
            min-width: 250px;
        }
    </style>
</head>
<body style="margin-top: 25px">
<div class="container">
    <div style="display:table;margin-top: 25px;min-width: 500px;">
        <form name="form1" id='frmDiarioClasse' method="post">
            <input type="hidden" name="iEscola" id="iEscola" value="<?= $iEscola ?>">
            <input type="hidden" name="tipoTurma" id="tipoTurma" value="">
            <fieldset>
                <legend style="font-weight: bold">Diário de Classe - Turmas de AC e AEE</legend>
                <table border='0' width="100%">
                    <tr>
                        <td nowrap>
                            <b>Calendarios: </b>
                        </td>
                        <td nowrap>
                            <select name="cboCalendario" id="cboCalendario" style="width: 100%">
                                <option value="" selected>Selecione</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap>
                            <b>Turma: </b>
                        </td>
                        <td nowrap>
                            <select name="cboTurma" id="cboTurma" style="width: 100%">
                                <option value="" selected>Selecione</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap>
                            <b>Atividade: </b>
                        </td>
                        <td nowrap>
                            <select name="cboAtividade" id="cboAtividade" style="width: 100%">
                                <option value="" selected>Selecione</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap>
                            <b>Profissional: </b>
                        </td>
                        <td nowrap>
                            <select name="cboProfissional" id="cboProfissional" style="width: 100%">
                                <option value="" selected>Selecione</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap>
                            <b>Exibir Pontos: </b>
                        </td>
                        <td nowrap>
                            <select name="cboPontos" id="cboPontos" style="width: 100%">
                                <option value="1" selected>Sim</option>
                                <option value="0" selected>Não</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap>
                            <b>Quantidade de Colunas <br/> (Presenças):</b>
                        </td>
                        <td nowrap="nowrap" id='ctnNumeroColunas'>
                            <?php
                            $aColunas = array();
                            for ($i = 40; $i <= 70; $i++) {
                                $aColunas[$i] = $i;
                            }
                            db_select('numeroColunas', $aColunas, true, 1, "style='width:100%;'");
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input name="btnImprimir" id="btnImprimir" type="button" value="Imprimir">
        </form>
    </div>
</div>
<?php
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/classes/http/http.js"></script>
</body>
</html>

<script type="text/javascript">
    const codigoEscola = document.getElementById("iEscola");
    const inputTipoTurma = document.getElementById("tipoTurma");
    const cboCalendario = document.getElementById("cboCalendario");
    const cboTurma = document.getElementById("cboTurma");
    const cboAtividade = document.getElementById("cboAtividade");
    const cboProfissional = document.getElementById("cboProfissional");
    const cboPontos = document.getElementById('cboPontos');
    const cboColunas = document.getElementById("numeroColunas");

    var turmasEspeciais = [];

    const limparSelect = (cbo) => {
        cbo.options.length = 0;
        cbo.add(new Option('Selecione', ''));
    };

    sUrlRpc = "edu_educacaobase.RPC.php";

    /**
     * Buscamos os calendario da escola
     */
    (async () => {
        await PHPSession.loadData();

        HttpClient.get(`${PHPSession.requestApi}/educacao/escola/${codigoEscola.value}/calendario`).then(response => {
            response.data.each(function (oCalendario) {
                cboCalendario.add(new Option(oCalendario.nome.urlDecode(), oCalendario.codigo));
                if (response.data.length == 1) {
                    cboCalendario.setValue(oCalendario.codigo);
                    cboCalendario.dispatchEvent(new Event('change'));
                }
            });
        });
    })();

    /**
     * Buscamos as Turmas vinculadas ao Calendario
     */
    cboCalendario.addEventListener('change', () => {
        limparSelect(cboTurma);
        limparSelect(cboAtividade);
        limparSelect(cboProfissional);

        if (cboCalendario.value == "") {
            return false;
        }

        js_divCarregando('Aguarde, pesquisando turmas', 'msgBox');

        var oParametros = new Object();
        oParametros.exec = "buscarTurmasEspeciais";
        oParametros.calendario = cboCalendario.value;

        new Ajax.Request(sUrlRpc,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParametros),
                onComplete: (response) => {
                    js_removeObj('msgBox');
                    var retorno = JSON.parse(response.responseText);

                    turmasEspeciais.length = 0;

                    retorno.turmas.each(function (turma) {
                        turmasEspeciais.push(turma);
                        cboTurma.add(new Option(turma.descricao.urlDecode(), turma.codigo));
                    });

                    if (retorno.turmas.length == 1) {
                        cboTurma.value = retorno.turmas[0].codigo;
                        cboTurma.dispatchEvent(new Event('change'));
                    }
                }
            });
    });


    cboTurma.addEventListener('change', (event) => {
        limparSelect(cboAtividade);
        limparSelect(cboProfissional);

        const turmaSelecionada = event.target.value;

        if (turmaSelecionada == '') {
            return;
        }

        turmasEspeciais.map((turma) => {
            if (turma.codigo === turmaSelecionada) {
                inputTipoTurma.value = turma.tipo;
            }
        });
        if (inputTipoTurma.value == 1) {
            buscarAtividades(turmaSelecionada, inputTipoTurma.value);
            cboAtividade.parentNode.parentNode.classList.remove('hide');
        }
        if (inputTipoTurma.value == 4) {
            buscarAtividades(turmaSelecionada, inputTipoTurma.value);
            buscarProfissionais(turmaSelecionada);
            cboAtividade.parentNode.parentNode.classList.remove('hide');
        }
        if (inputTipoTurma.value == 5) {
            cboAtividade.parentNode.parentNode.classList.add('hide');
            buscarProfissionais(turmaSelecionada, inputTipoTurma.value);
        }
    });

    cboAtividade.addEventListener('change', (event) => {
        if (inputTipoTurma.value != 1) {
            return;
        }
        limparSelect(cboProfissional);

        const atividadeSelecionada = event.target.value;
        if (atividadeSelecionada == "") {
            limparSelect(cboProfissional);
            return;
        }
        buscarProfissionais(cboTurma.value, atividadeSelecionada);
    });

    const buscarAtividades = (turma, inputTipoTurma) => {
        var oParametros = new Object();
        oParametros.exec = "buscarAtividades";
        oParametros.turma = turma;
        oParametros.tipoTurma = inputTipoTurma;
        new Ajax.Request(sUrlRpc,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParametros),
                onComplete: (response) => {
                    js_removeObj('msgBox');
                    var retorno = JSON.parse(response.responseText);

                    retorno.atividades.each(function (atividade) {
                        cboAtividade.add(new Option(atividade.descricao.urlDecode(), atividade.codigo_atividade));
                    });

                    if (retorno.atividades.length == 1) {
                        cboAtividade.value = retorno.atividades[0].codigo_atividade;
                        cboAtividade.dispatchEvent(new Event('change'));
                    }
                }
            });
    };

    const buscarProfissionais = (turma, atividade) => {
        var oParametros = new Object();
        oParametros.exec = "buscarProfissionais";
        oParametros.turma = turma;
        oParametros.atividade = atividade;
        oParametros.tipoTurma = inputTipoTurma.value;

        new Ajax.Request(sUrlRpc,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParametros),
                onComplete: (response) => {
                    js_removeObj('msgBox');
                    var retorno = JSON.parse(response.responseText);

                    retorno.profissionais.each(function (profissional) {
                        cboProfissional.add(new Option(profissional.nome.urlDecode(), profissional.nome.urlDecode()));
                    });

                    if (retorno.profissionais.length == 1) {
                        cboProfissional.value = retorno.profissionais[0].nome.urlDecode();
                        cboProfissional.dispatchEvent(new Event('change'));
                    }
                }
            });
    };

    /**
     * Chama o programa para gerar o relatorio
     */
    $('btnImprimir').observe('click', function () {
        const baseUrl = "<?php echo ECIDADE_REQUEST_PATH;?>" + 'v4/api/educacao/escola';
        var atividadeComplementar = cboAtividade.value;

        if (cboTurma.value == 0 || cboTurma.value == "") {
            alert('Selecione uma turma.');
            return false;
        }

        const formData = new FormData();
        PHPSession.appendFormData(formData);
        console.log(cboPontos.value);
        formData.append('calendario', cboCalendario.value);
        formData.append('turma', cboTurma.value);
        formData.append('tipo_turma', inputTipoTurma.value);
        formData.append('atividade_complementar', atividadeComplementar);
        formData.append('regente', cboProfissional.value);
        formData.append('colunas', cboColunas.value);
        formData.append('exibirPontos', cboPontos.value);
        formData.append('modelo', 1);

        HttpClient.post(baseUrl + '/relatorios/diarioclasse/turmasEspeciais', {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return false;
            }

            jan = window.open(response.data, '','scrollbars=1,location=0 ');
            jan.moveTo(0, 0);
        });
    });
</script>
