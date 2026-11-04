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

use ECidade\Enum\Educacao\Escola\FormaObtencaoEnum;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_edu_parametros_classe.php"));

$parametros = db_utils::postMemory($_GET);
db_inicio_transacao();

$regencia = new Regencia($parametros->regencia);
$etapa = $regencia->getEtapa();
$turma = $regencia->getTurma();

$avisoNota = "";

$formasObtencao = [];
$formaAvaliacao = $regencia->getProcedimentoAvaliacao()->getFormaAvaliacao();
$mensagem = "";

//Pesquisar Parametros
$cledu_parametros    = new cl_edu_parametros;
$escola              = db_getsession("DB_coddepto");
$sSqlParametros      = $cledu_parametros->sql_query(null,"ed233_alterarprocedimentonotaparcial",null," ed233_i_escola = $escola");
$sResultParametros   = $cledu_parametros->sql_record($sSqlParametros);
$bAlterarProcedimentoNotaParcial = db_utils::fieldsMemory($sResultParametros, 0)->ed233_alterarprocedimentonotaparcial;
if ($bAlterarProcedimentoNotaParcial == "t") {
    $bAlterarProcedimentoNotaParcial = true;
} else {
    $bAlterarProcedimentoNotaParcial = false;
}

switch ($formaAvaliacao->getTipo()) {
    case 'NOTA':
        $formasObtencao = [
            FormaObtencaoEnum::MEDIA_ARITMETICA => new FormaObtencaoEnum(FormaObtencaoEnum::MEDIA_ARITMETICA),
            FormaObtencaoEnum::SOMA => new FormaObtencaoEnum(FormaObtencaoEnum::SOMA),
            FormaObtencaoEnum::MAIOR_NOTA => new FormaObtencaoEnum(FormaObtencaoEnum::MAIOR_NOTA),
            FormaObtencaoEnum::ULTIMA_NOTA => new FormaObtencaoEnum(FormaObtencaoEnum::ULTIMA_NOTA)
        ];
        $menorValor = ArredondamentoNota::formatar($formaAvaliacao->getMenorValor(), db_getsession("DB_anousu"));
        $maiorValor = ArredondamentoNota::formatar($formaAvaliacao->getMaiorValor(), db_getsession("DB_anousu"));
        $avisoNota = sprintf("%s %s",
            "*O Resultado não deve ser maior do que o indicado no procedimento de avaliação.",
            "(Notas de {$menorValor} até {$maiorValor} )"
        );
        break;
    default:
        $mensagem = "Tipo de avaliação <b>\"{$formaAvaliacao->getTipo()}\"</b> deve ser lançado diretamente no diário.";
        $mensagem .= "<br><br>EDUCAÇÃO > Escola > Procedimentos > Diário de Classe > Lançamento Por Turma";
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <title>DBSeller Informática Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <style>
        .containerConteudo {
            border: 1px solid #A8A8A8;
            padding: 5px;
        }

        .columns > .btn {
            margin: 0 5px;
        }
        .disabled {
            background-color: #EEE;
        }
    </style>
</head>
<body>
<?php
if ($mensagem !== "") {
?>
<div class="alert alert-danger text-center" role="alert">
    <p><?=$mensagem?></p>
</div>
<?php
die;
}
?>
<div style="float: right; margin: 8px">
    <label for="formaObtencaoResultado"><strong>Resultado: </strong></label>
    <select id="formaObtencaoResultado">
        <?php
        foreach ($formasObtencao as $key => $formaObtencao) {
            ?>
            <option value="<?= $key ?>"><?= $formaObtencao->name() ?></option>
            <?php
        }
        ?>
    </select>
</div>
<div style="width: 950px; clear: both">
    <table id="data-table" class="table table-sm" data-height="350" style="width: 100%;">
    </table>
</div>
<div class="container">
    <?php
    if ($avisoNota !== "") {
    ?>
    <p style="clear: both; font-size: 10px; font-weight: bold;"><?=$avisoNota?></p>
    <?php
    }
    ?>
    <button class="btn btn-light" id="btnSalvarAproveitamentos">
        <i class="fa fa-save"></i>
        Salvar
    </button>
    <button class="btn btn-light" onclick="parent.parent.iframe_lancamento_notas.hide()">
        <i class="fas fa-times"></i>
        Fechar
    </button>
</div>

<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    const btnSalvarAproveitamentos = document.getElementById('btnSalvarAproveitamentos');
    const selectFormaObtencaoResultado = document.getElementById('formaObtencaoResultado');

    const turmaEncerrada = <?=$parametros->encerrada?>;
    let urlApi = "";
    let urlEscola = "";
    let resultadoParcial = "";
    let permiteLancarNotas = false;

    PHPSession.loadData().then(() => {
        urlApi = PHPSession.requestApi;
        urlEscola = `${PHPSession.requestApi}/educacao/escola`;
        buscarAvaliacoesPeriodo();
    });

    const buttons = () => {
        return {
            btnAdd: {
                text: 'Adicionar avaliação',
                icon: 'fa-plus',
                event: function () {
                    adicionarAvaliacaoPeriodo();
                },
                attributes: {
                    title: 'Clique para adicionar elementos de avaliação'
                },
                render: !turmaEncerrada
            }
        }
    }

    window.events = {
        'change .avaliacao': function (e, value, row, index) {
            e.target.value = formatValue(e.target.value);
            const valor = e.target.value;
            const {field} = e.target.dataset;

            const avalicaoAluno = row.avaliacoes.find(avaliacao => avaliacao.field == field);
            avalicaoAluno.aproveitamento.ed341_valornota = valor;
        },
        'keyup .avaliacao': function (e, value, row, index) {
            const inputValue = e.target.value.replace(',', '.');
            const regex = /^\d+(\.\d?)?$/;
            if (!regex.test(inputValue)) {
                e.target.value = '';
            } else {
                e.target.value = inputValue;
            }
        },
        'change .observacao': function (e, value, row, index) {
            row.resultado.ed343_observacao = e.target.value;
        }
    }
    const formatterNomeAluno = (value, row, index, field) => {
        let situacao = '';
        if (row.situacao != 'MATRICULADO') {
            situacao = `<br><span style="font-size: 9px; font-weight: bold">(${row.situacao} ${row.data_modificacao})</span>`;
        } else if (row.isAvaliadoPorParecer) {
            situacao = `<br><span style="font-size: 9px; font-weight: bold">(NEE - Parecer)</span>`;
        }
        return `<span style="font-size: 11px; width: 320px">${value} ${situacao}</span>`;
    }
    const formatterAvaliacao = (value, row, index, field) => {
        const multiplicador = field.split('_')[1];
        const tabindex = (multiplicador * 100) + index;

        const avalicaoAluno = row.avaliacoes.find(avaliacao => avaliacao.field == field);
        let aproveitamento = '';

        if (!empty(avalicaoAluno.aproveitamento.ed341_valornota)) {
            aproveitamento = avalicaoAluno.aproveitamento.ed341_valornota;
        }

        let disabled = '';
        if (turmaEncerrada ||
            !permiteLancarNotas ||
            row.situacao != 'MATRICULADO' ||
            row.resultado.ed343_encerrado ||
            row.concluida ||
            row.isAvaliadoPorParecer) {
            disabled = 'disabled';
        }
        return `<input type="text" class="field-size1 avaliacao" tabindex="${tabindex}" data-field="${field}" value="${aproveitamento}" ${disabled} />`;
    }

    const formatterResultado = (value, row) => {
        let valornota = '';
        if (row.resultado.ed343_valornota != null) {
            valornota = row.resultado.ed343_valornota;
        }

        return `<input type="text" class="field-size1 readonly" value="${valornota}" disabled />`;
    }

    const formatterObservacao = (value, row) => {
        let observacao = '';
        if (row.resultado.ed343_observacao != null) {
            observacao = row.resultado.ed343_observacao;
        }
        return `<input type="text" class="field-size5 observacao" value="${observacao}" maxlength="100" />`;
    }

    const excluirAvaliacao = (codigo, ordem) => {
        if (!confirm('Você deseja mesmo excluir a "Avaliação ' + ordem + '"? \n Todas as notas lançadas serão excluidas!')) {
            return;
        }
        const formData = new FormData();
        formData.append('avaliacaoParcial', codigo);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlEscola}/regencia/excluir-avaliacao`, {body: formData}).then(response => {
            alert(response.message);
            if (response.error) {
                return;
            }
            buscarAvaliacoesPeriodo();
        });
    }

    function rowStyle(row) {
        if (row.situacao != "MATRICULADO" || row.resultado.ed343_encerrado) {
            return {
                classes: 'disabled'
            }
        }
        return {}
    }

    const footerFormatter = (codigo, ordem) => {
        if (turmaEncerrada) {
            return '';
        }
        return `<a href="javascript: void(0); excluirAvaliacao(${codigo}, ${ordem});">Excluir</a>`;
    }

    var table = jQuery('#data-table');

    const buscarAvaliacoesPeriodo = () => {
        const formData = new FormData();
        formData.append('regencia', <?=$regencia->getCodigo()?>);
        formData.append('periodoavaliacao', <?=$parametros->periodoavaliacao?>);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlEscola}/regencia/avaliacoes-periodo`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            recriarTabela(response.data.avaliacoes);
            resultadoParcial = response.data.resultado;
            selectFormaObtencaoResultado.value = resultadoParcial.ed342_formaobtencao;
            //selectFormaObtencaoResultado.disabled = turmaEncerrada;
            <?php if ($bAlterarProcedimentoNotaParcial == false) { ?>
                selectFormaObtencaoResultado.disabled = true;
                selectFormaObtencaoResultado.classList.add('readonly');
            <?php } ?>
            permiteLancarNotas = response.data.permiteLancarNotas;
        });
    }

    const adicionarAvaliacaoPeriodo = () => {
        const formData = new FormData();
        formData.append('regencia', <?=$regencia->getCodigo()?>);
        formData.append('periodoavaliacao', <?=$parametros->periodoavaliacao?>);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlEscola}/regencia/adicionar-avaliacoes-periodo`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            buscarAvaliacoesPeriodo();
        });
    }

    const buscarAlunosMatriculados = () => {
        const formData = new FormData();
        formData.append('regencia', <?=$regencia->getCodigo()?>);
        formData.append('periodoavaliacao', <?=$parametros->periodoavaliacao?>);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${PHPSession.requestApi}/educacao/escola/notas-parciais/periodo`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            table.bootstrapTable('load', response.data);
        });
    }

    const recriarTabela = (response) => {
        table.bootstrapTable('destroy');
        colunas = [];
        colunas.push({
            title: 'Aluno',
            field: 'aluno',
            align: 'left',
            width: 300,
            formatter: formatterNomeAluno,
        });

        response.forEach(item => {
            colunas.push({
                title: `Avaliação ${item.ed340_ordem}`,
                field: item.field,
                align: 'center',
                width: 80,
                formatter: formatterAvaliacao,
                footerFormatter: () => footerFormatter(item.ed340_codigo, item.ed340_ordem),
                events: events
            })
        })

        colunas.push({
            title: 'Resultado',
            field: 'resultado',
            align: 'left',
            width: 80,
            formatter: formatterResultado,
            events: events
        });
        colunas.push({
            title: 'Observação',
            field: 'observacao',
            align: 'left',
            formatter: formatterObservacao,
            events: events
        });

        table.bootstrapTable({
            locale: 'pt-BR',
            search: true,
            showButtonText: true,
            buttons: buttons,
            class: "table table-sm",
            buttonsAlign: 'left',
            showFooter: true,
            rowStyle: rowStyle,
            columns: colunas
        });

        buscarAlunosMatriculados();
    }

    btnSalvarAproveitamentos.addEventListener('click', () => {
        const dadosAvaliacoes = table.bootstrapTable('getData');
        const formData = new FormData();
        formData.append('dados', JSON.stringify(dadosAvaliacoes));
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlEscola}/aproveitamento/salvar`, {body: formData}).then(response => {
            alert(response.message);
            if (response.error) {
                return;
            }
            buscarAlunosMatriculados();
        });
    });

    selectFormaObtencaoResultado.addEventListener('change', () => {
        const formData = new FormData();
        formData.append('codigoResultadoParcial', resultadoParcial.ed342_codigo);
        formData.append('formaObtencao', selectFormaObtencaoResultado.value);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlEscola}/avaliacaoparcial/salvar-formaobtencao`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
            }
            btnSalvarAproveitamentos.dispatchEvent(new Event('click'));
        });
    })

    function formatValue(value) {
        const newValue = value.replace(',', '.');
        const decimalIndex = newValue.indexOf('.');
        if (newValue === '') {
            return '';
        }
        if (decimalIndex === -1) {
            return `${newValue}.0`;
        }
        const decimalPlaces = newValue.length - decimalIndex - 1;
        if (decimalPlaces === 0) {
            return `${newValue}0`;
        }
        return newValue;
    }
</script>
</body>
</html>
