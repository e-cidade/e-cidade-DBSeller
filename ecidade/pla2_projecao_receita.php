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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification('libs/db_conecta.php');
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
</head>
<body>
<div class="container">
    <fieldset>
        <legend>Demonstrativo das Projeções da Receita</legend>
        <table class="form-container">
            <tr class="text-left">
                <td><label class="bold" for="planejamento">Planejamento:</label></td>
                <td>
                    <select id="planejamento" class="field-size8">
                        <option value="">Selecione um plano</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label class="bold" for="ano_inicial">Ano inicial:</label></td>
                <td>
                    <input type="text" name="ano_inicial" id="ano_inicial" class="field-size2 readonly" readonly>
                </td>
            </tr>
            <tr>
                <td><label class="bold" for="ano_final">Ano final:</label></td>
                <td>
                    <input type="text" name="ano_final" id="ano_final" class="field-size2 readonly" readonly>
                </td>
            </tr>
        </table>
        <fieldset class="separator">
            <legend>Filtros para impressão</legend>
            <table class="form-container">
                <tr>
                    <td><label for="naturezaReceita">Natureza da Receita:</label></td>
                    <td>
                        <input type="text" id="naturezaReceita" class="field-size4" maxlength="15"
                               oninput="js_ValidaCampos(this,1,'Natureza da Receita:','f','t',event);">
                    </td>
                </tr>
                <tr>
                    <td><label for="agruparPorRecurso">Agrupar por Recurso:</label></td>
                    <td>
                       <select id="agruparPorRecurso">
                           <option value="0" selected>Não</option>
                           <option value="1" >Sim</option>
                       </select>
                    </td>
                </tr>
                <tr id="linhaFiltroTipoPlano">
                    <td><label for="ementario">Selecione o plano que deseja agrupar os dados:</label></td>
                    <td>
                        <select name="ementario" id="ementario">
                            <option value="ecidade" checked>Plano e-Cidade</option>
                            <option value="uniao">Plano União/Federação</option>
                            <option value="estadual">Plano Estadual/Regional</option>
                        </select>
                    </td>
                </tr>
                <tr >
                    <td id="ctnInstituicao" colspan="2" style="font-weight: normal">
                        <input type="hidden" name="db_selinstit" id="db_selinstit" value="">
                    </td>
                </tr>
            </table>
        </fieldset>
    </fieldset>
    <button id="emitir" type="button">
        <i class="fas fa-print"></i>
        Emitir
    </button>

    <button id="emitirConferenciaPorRecurso" type="button" style="margin-left: 2px">
        <i class="fas fa-print"></i>
        Emitir Compatibilidade de Recursos
    </button>

    <button id="emitirComDesdobramento" type="button" style="margin-left: 2px">
        <i class="fas fa-print"></i>
        Emitir com desdobramento de receita
    </button>
</body>
<?php db_menu() ?>
<script type="text/javascript" src="scripts/session.js"></script>
<script rel="script" type="text/javascript" src="scripts/classes/planejamento/planejamento.js"></script>
<script rel="script" type="text/javascript" src="scripts/widgets/DBViewInstituicao.widget.js"></script>
<script>

    const rota = 'financeiro/planejamento/relatorios/projecao-receita';

    const planejamento = new Planejamento(document.getElementById('planejamento'));
    const inputAnoInicial = document.getElementById('ano_inicial');
    const inputAnoFinal = document.getElementById('ano_final');
    const natureza = document.getElementById('naturezaReceita');
    const agruparPorRecurso = document.getElementById('agruparPorRecurso');
    const inputEmentario = document.getElementById('ementario')
    const linhaFiltroTipoPlano = document.getElementById('linhaFiltroTipoPlano')
    const btnEmitir = document.getElementById('emitir');
    const btnEmitirConferenciaPorRecurso = document.getElementById('emitirConferenciaPorRecurso');
    const btnEmitirComDesdobramento = document.getElementById('emitirComDesdobramento');

    var viewInstituicao = new DBViewInstituicao('viewInstituicao', $('ctnInstituicao'));
    viewInstituicao.show();

    PHPSession.loadData().then(() => {
        planejamento.load();
    });

    planejamento.getElement().addEventListener('change', () => {
        if (planejamento.getValue() == '') {
            inputAnoInicial.value = '';
            inputAnoFinal.value = '';
            return
        }

        inputAnoInicial.value = planejamento.getPlano().pl2_ano_inicial;
        inputAnoFinal.value = planejamento.getPlano().pl2_ano_final;
    });

    agruparPorRecurso.addEventListener('change', () => {
        linhaFiltroTipoPlano.style.display = '';
        inputEmentario.value = 'ecidade';

        if (agruparPorRecurso.value == 1) {
            linhaFiltroTipoPlano.style.display = 'none';
        }
    });

    const valida = () => {
        try {
            if (empty(planejamento.getValue())) {
                throw 'Selecione o planejamento.';
            }

            if (!empty(natureza.value)) {
                const codigosValidacao = [4, 9];
                let codigo = Number(natureza.value.substr(0, 1))
                if (!codigosValidacao.includes(codigo)) {
                    throw 'Código da natureza inválido. O código da natureza deve iniciar em 4 ou 9';
                }
            }

            if (viewInstituicao.getInstituicoesSelecionadas(true).length === 0) {
                throw 'Selecione ao menos uma instituição';
            }
        } catch (e) {
            alert(e);
            return false;
        }

        return true;
    };

    btnEmitir.addEventListener('click', () => {
        emitir(0)
    });

    btnEmitirComDesdobramento.addEventListener('click', () => {
        emitir(1)
    });

    function emitir(forcarDesdobramento) {
        if (!valida()) {
            return
        }

        const formData = new FormData();
        formData.append('planejamento_id', planejamento.getValue());
        formData.append('natureza', natureza.value);
        formData.append('agruparPorRecurso', agruparPorRecurso.value);
        formData.append('ementario', inputEmentario.value);
        formData.append('forcarDesdobramento', forcarDesdobramento);
        for (let codigo of viewInstituicao.getInstituicoesSelecionadas(true)) {
            formData.append('instituicoes[]', codigo);
        }

        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${rota}`, {body: formData}).then(response => {

            if (response.error) {
                alert(response.message);
                return;
            }

            const download = new DBDownload();
            download.addFile(response.data.pdf, "Projeção da receita - PDF");
            download.addFile(response.data.csv, "Projeção da receita - CSV");
            download.show();
        });
    }

    btnEmitirConferenciaPorRecurso.addEventListener('click', () => {

        if (!valida()) {
            return
        }

        const formData = new FormData();
        formData.append('planejamento_id', planejamento.getValue());
        formData.append('natureza', natureza.value);
        formData.append('agruparPorRecurso', agruparPorRecurso.value);
        for (let codigo of viewInstituicao.getInstituicoesSelecionadas(true)) {
            formData.append('instituicoes[]', codigo);
        }

        PHPSession.appendFormData(formData);

        let rout = 'financeiro/planejamento/relatorios/projecao-receita-recurso';
        HttpClient.post(`${PHPSession.requestApi}/${rout}`, {body: formData}).then(response => {

            if (response.error) {
                alert(response.message);
                return;
            }

            const download = new DBDownload();
            download.addFile(response.data.pdf, "Projeção da receita - PDF");
            download.show();
        });
    });

</script>
