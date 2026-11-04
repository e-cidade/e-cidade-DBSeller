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
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
</head>
<body class="body-default">
<button style="top: 3px; right: 15px; position: fixed; z-index: 9999" rel='ignore-css' class="retornar" type="button">
    <i class="fas fa-arrow-left"></i>
    Retornar
</button>

<div id="abaReceita">
    <div class="alert alert-primary text-left" role="alert">
        O sistema salvará o valor de contas dedutoras como negativo automaticamente, não é necessário informar o -
        (menos) na frente do valor.
    </div>
    <form id="frmReceita" class="container">
        <fieldset>
            <legend>Manutenção</legend>
            <table class="form-container">
                <tr>
                    <td><label>Planejamento:</label></td>
                    <td>
                        <input type="text" name="planejamento" id="planejamento" readonly
                               class="readonly field-size-max">
                    </td>
                </tr>
                <tr>
                    <td><label id="ancoraNatureza" for="natureza"><a href="#">Natureza:</a></label></td>
                    <td>
                        <input type="hidden" id="codigoNatureza" name="orcfontes_id" lang="db_codigo">
                        <input type="text" id="natureza" lang="o57_fonte" class="field-size3 readonly" readonly>
                        <input type="text" id="descricaoNatureza" lang="o57_descr" class="readonly field-size7 readonly"
                               readonly>
                    </td>
                </tr>
                <tr>
                    <td><label for="recurso">Fonte Recurso:</label></td>
                    <td>
                        <input type="hidden" id="codigoRecurso" name="recurso_id">
                        <input type="text" id="recurso" lang="o15_recurso" class="field-size2 readonly" readonly>
                        <input type="text" id="descricaoRecurso" lang="o15_descr" class="readonly field-size8 readonly"
                               readonly>
                    </td>
                </tr>
                <tr>
                    <td><label>Complemento:</label></td>
                    <td>
                        <input type="text" id="descricaoComplemento" class="readonly field-size-max readonly"
                               readonly>
                    </td>
                </tr>
                <tr>
                    <td><label>Instituição:</label></td>
                    <td>
                        <input type="text" id="descricaoInstituicao" class="readonly field-size-max readonly"
                               readonly>
                    </td>
                </tr>
                <tr>
                    <td><label id="ancoraUnidade" for="unidade"><a href="#">Unidade:</a></label></td>
                    <td>

                        <input type="hidden" id="orgao" name="orcorgao_id">
                        <input type="text" id="unidade" name="orcunidade_id" lang="o41_unidade" readonly
                               class="field-size2 readonly">
                        <input type="text" id="descricaoUnidade" lang="o41_descr" class="readonly field-size8 readonly"
                               readonly>
                    </td>
                </tr>
                <tr>
                    <td><label id="ancoraCaracteristica" for="caracteristica">
                            <a href="#">Caracteristica Peculiar:</a>
                        </label>
                    </td>
                    <td>
                        <input type="text" id="caracteristica" name="concarpeculiar_id" lang="c58_sequencial"
                               class="field-size2">
                        <input type="text" id="descricaoCaracteristica" lang="c58_descr"
                               class="readonly field-size8 readonly" readonly>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="esferaorcamentaria">Esfera Orçamentária:</label>
                    </td>
                    <td>
                        <select id="esferaorcamentaria" name="esferaorcamentaria">
                            <option value="">Selecione uma esfera orçamentária</option>
                            <option value="10">F - Orçamento Fiscal</option>
                            <option value="20">S - Orçamento da Seguridade Social</option>
                            <option value="30">I - Orçamento de Investimento</option>
                        </select>
                    </td>
                </tr>
            </table>

            <fieldset class="separator ">
                <legend>Valores previstos</legend>
                <div id="containerValores"></div>
            </fieldset>
        </fieldset>

        <input type="hidden" id="codigo" name="id">
        <input type="hidden" id="codigoInstituicao" name="instituicao_id">
        <button type="button" id="btnSalvarEstimativa">
            <i class="far fa-save"></i>
            Salvar
        </button>

        <button type="button" id="btnNovaEstimativa">
            <i class="far fa-file"></i>
            Novo
        </button>
    </form>
</div>
<div class="container">
</div>


<?php db_menu() ?>

<script type="text/javascript" src="scripts/classes/planejamento/planejamento.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInputValor.widget.js"></script>
<script type="text/javascript" src="scripts/classes/planejamento/valores.js"></script>

<script type="text/javascript">

    const get = js_urlToObject();
    // rotas
    const routs = {
        plano: 'financeiro/planejamento/consulta/plano',
        estimativa: {
            show: 'financeiro/planejamento/receita/previsao',
            salvar: 'financeiro/planejamento/receita/previsao/salvar'
        }
    }

    var plano = {};
    var estimativa;

    const formEstimativa = {
        form: document.getElementById('frmReceita'),
        planejamento: document.getElementById('planejamento'),
        codigo: document.getElementById('codigo'),
        codigoNatureza: document.getElementById('codigoNatureza'),
        natureza: document.getElementById('natureza'),
        descricaoNatureza: document.getElementById('descricaoNatureza'),
        codigoRecurso: document.getElementById('codigoRecurso'),
        recurso: document.getElementById('recurso'),
        descricaoRecurso: document.getElementById('descricaoRecurso'),
        descricaoComplemento: document.getElementById('descricaoComplemento'),
        caracteristica: document.getElementById('caracteristica'),
        descricaoCaracteristica: document.getElementById('descricaoCaracteristica'),
        orgao: document.getElementById('orgao'),
        descricaoInstituicao: document.getElementById('descricaoInstituicao'),
        codigoInstituicao: document.getElementById('codigoInstituicao'),
        unidade: document.getElementById('unidade'),
        descricaoUnidade: document.getElementById('descricaoUnidade'),
        esferaOrcamentaria: document.getElementById('esferaorcamentaria'),
        containerValores: document.getElementById('containerValores'),
        salvar: document.getElementById('btnSalvarEstimativa'),
        novo: document.getElementById('btnNovaEstimativa'),
    };

    valoresEstimativa = new Valores();

    const lookUpNatureza = new DBLookUp(
        document.getElementById('ancoraNatureza'),
        formEstimativa.natureza,
        formEstimativa.descricaoNatureza, {
            'sArquivo': 'func_naturezareceita.php',
            'sLabel': 'Pesquisar Natureza da Receita',
            'sObjetoLookUp': "db_iframe_natureza",
            'aCamposAdicionais': [
                'db_codigo',
                'gestao',
                'descricao',
                'o200_descricao',
                'nomeinst',
                'db_instit',
                'db_idrecurso'
            ]
        });

    lookUpNatureza.setCallBack('onClick', (retorno) => {
        formEstimativa.codigoNatureza.value = retorno[2];
        formEstimativa.recurso.value = retorno[3];
        formEstimativa.descricaoRecurso.value = retorno[4];
        formEstimativa.descricaoComplemento.value = retorno[5];
        formEstimativa.descricaoInstituicao.value = retorno[6];
        formEstimativa.codigoInstituicao.value = retorno[7];
        formEstimativa.codigoRecurso.value = retorno[8];

        lookUpUnidade.setParametrosAdicionais([`ano=${get.exercicio}`, `instituicao=${retorno[7]}`]);
    });

    const lookupCaracteristicaPeculiar = new DBLookUp(
        document.getElementById('ancoraCaracteristica'),
        formEstimativa.caracteristica,
        formEstimativa.descricaoCaracteristica,
        {
            'sArquivo': 'func_concarpeculiar.php',
            'sLabel': 'Pesquisa Caracteristica Peculiar',
            'sObjetoLookUp': "db_iframe_concarpeculiar"
        }
    );

    const lookUpUnidade = new DBLookUp(
        document.getElementById('ancoraUnidade'),
        formEstimativa.unidade,
        formEstimativa.descricaoUnidade, {
            'sArquivo': 'func_orcunidade_nova.php',
            'sLabel': 'Pesquisar Unidade',
            'sObjetoLookUp': "db_iframe_orcunidade",
            'aCamposAdicionais': ['o41_orgao']
        }
    );

    lookUpUnidade.setCallBack('onClick', retorno => {
        formEstimativa.unidade.value = String(retorno[0]).padStart(2, '0');
        formEstimativa.orgao.value = retorno[2];
    });

    const setAnoLockups = () => {
        lookUpNatureza.setParametrosAdicionais([
            `anoFuturo=${get.exercicio}`
        ]);

        lookUpUnidade.setParametrosAdicionais([
            `ano=${get.exercicio}`,
            `instituicao=${formEstimativa.codigoInstituicao.value}`
        ]);
    }

    PHPSession.loadData().then(() => {
        let urlNovo = 'pl4_receita_manutencao.php?';

        if (get.exercicio) {
            setAnoLockups(get.exercicio);
            urlNovo += `exercicio=${get.exercicio}`;
        }

        if (get.planejamento) {
            urlNovo += `&planejamento=${get.planejamento}`;
            HttpClient.get(`${PHPSession.requestApi}/${routs.plano}/${get.planejamento}`).then(response => {
                plano = response.data;
                formEstimativa.planejamento.value = plano.pl2_titulo;
                valoresEstimativa.criaInputValores(formEstimativa.containerValores, plano);
            });
        }

        if (get.codigo) {
            HttpClient.get(`${PHPSession.requestApi}/${routs.estimativa.show}/${get.codigo}`).then(response => {

                estimativa = response.data;
                plano = estimativa.planejamento;
                urlNovo += `&planejamento=${plano.pl2_codigo}`;
                formEstimativa.planejamento.value = plano.pl2_titulo;
                valoresEstimativa.criaInputValores(formEstimativa.containerValores, plano);

                setDadosFormulario();
            });
        }

        formEstimativa.novo.addEventListener('click', () => {
            location.href = urlNovo;
        });
    });

    const setDadosFormulario = () => {
        formEstimativa.planejamento.value = plano.pl2_titulo;
        formEstimativa.codigo.value = estimativa.id;
        formEstimativa.codigoNatureza.value = estimativa.orcfontes_id;
        formEstimativa.natureza.value = estimativa.natureza_receita.o57_fonte;
        formEstimativa.descricaoNatureza.value = estimativa.natureza_receita.o57_descr;
        formEstimativa.codigoRecurso.value = estimativa.recurso.o15_codigo;
        formEstimativa.recurso.value = estimativa.recurso.fonteRecurso.gestao;
        formEstimativa.descricaoRecurso.value = estimativa.recurso.fonteRecurso.descricao;
        formEstimativa.descricaoComplemento.value = estimativa.recurso.complemento.descricao
        formEstimativa.caracteristica.value = estimativa.caracteristica_peculiar.c58_sequencial;
        formEstimativa.descricaoCaracteristica.value = estimativa.caracteristica_peculiar.c58_descr;
        formEstimativa.orgao.value = estimativa.orcorgao_id;
        formEstimativa.descricaoInstituicao.value = estimativa.instituicao.nomeinst;
        formEstimativa.codigoInstituicao.value = estimativa.instituicao_id;
        formEstimativa.unidade.value = String(estimativa.orcunidade_id).padStart(2, '0');
        formEstimativa.descricaoUnidade.value = estimativa.unidade.o41_descr;
        formEstimativa.esferaOrcamentaria.value = estimativa.esfera;
        estimativa.valores.each(valor => {
            if (valor.pl10_valor < 0) {
                valor.pl10_valor *= -1;
            }
            valoresEstimativa.set(valor.pl10_ano, valor.pl10_valor);
        });
    };


    const validaFormEstimativa = () => {
        try {
            if (formEstimativa.codigoNatureza.value == '') {
                throw 'Informe a "Natureza da Receita".';
            }
            if (formEstimativa.unidade.value == '') {
                throw 'Informe a "Unidade Orçamentária".';
            }
            if (formEstimativa.caracteristica.value == '') {
                throw 'Informe a "Caracteristica Peculiar".';
            }
            if (formEstimativa.natureza.value.substr(0, 1) == 9 && formEstimativa.caracteristica.value == '000') {
                throw 'Natureza de Receita dedutora, não pode possuir "Caracteristica Peculiar" 000 - Não se Aplica.';
            }
            if (formEstimativa.esferaOrcamentaria.value == '') {
                throw 'Selecione a "Esfera Orçamentária".';
            }
            if (valoresEstimativa.existeValoresNaoInformados()) {
                throw 'Você deve informar o valor de todos exercícios.';
            }
        } catch (e) {
            alert(e);
            return false;
        }
        return true;
    }

    formEstimativa.salvar.addEventListener('click', () => {
        if (!validaFormEstimativa()) {
            return;
        }

        const formData = new FormData(formEstimativa.frm);

        formData.append('id', formEstimativa.codigo.value);
        formData.append('orcunidade_id', Number(formEstimativa.unidade.value));
        formData.append('anoorcamento', plano.pl2_ano_inicial);
        formData.append('planejamento_id', plano.pl2_codigo);
        formData.append('orcfontes_id', formEstimativa.codigoNatureza.value);
        formData.append('natureza', formEstimativa.natureza.value);
        formData.append('orcorgao_id', formEstimativa.orgao.value);
        formData.append('orcunidade_id', Number(formEstimativa.unidade.value));
        formData.append('recurso_id', formEstimativa.codigoRecurso.value);
        formData.append('instituicao_id', formEstimativa.codigoInstituicao.value);
        formData.append('concarpeculiar_id', formEstimativa.caracteristica.value);
        formData.append('esferaorcamentaria', formEstimativa.esferaOrcamentaria.value);
        formData.append('valores', JSON.stringify(valoresEstimativa.getValores()));
        formData.append('inclusaomanual', true);

        PHPSession.appendFormData(formData);

        const parametros = {
            body: formData,
            reportMessage: `Aguarde, salvando estimativa.`
        }

        HttpClient.post(`${PHPSession.requestApi}/${routs.estimativa.salvar}`, parametros).then(response => {

            alert(response.message);
            if (response.error) {
                return;
            }

            estimativa = response.data;
            formEstimativa.codigo.value = response.data.id;
        });
    });

    /**
     * Botão de retorno
     */
    document.querySelectorAll('.retornar').forEach(el => el.addEventListener('click', event => {
        location.href = `pla4_receita.php`;
    }));


</script>
</body>
