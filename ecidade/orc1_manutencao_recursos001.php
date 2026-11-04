<?php

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
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <style>
        #descricao {
            width: 95%;
        }
        #atualizarNome {
            padding-left: 2px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="alert alert-primary text-left" role="alert">
    - Antes de acessar essa rotina, certifique-se de ter realizado o depara dos exercício de 2021 para 2022 através da
    rotina
    <b>DB:FINANCEIRO > Orçamento > Cadastros > Tipos de Recursos > De Para Recursos 2021 - 2022</b><br>
    - A manutenção dos recursos nessa rotina, altera os dados do recurso <b>apenas para o exercício atual</b>. Portanto,
    certifique-se de estar no exercício correto.<br>
    - O código do <b>Recurso</b> é o recurso Gestão, aquele utilizado para a Execução orçamentária de 2023 em diante.<br>
    - O código do <b>Subrecurso</b> é o recurso antigo, aquele utilizado na execução Orçamentária de 2022 para trás.<br>
    - Para atualizar a descrição do recurso conforme o SICONFI clique em <kdb><i class="fas fa-sync "></i></kdb>.<br>
    - <b>Não é permitido alterar o complemento de uma fonte de recurso já existente, para complementos novos,
        deve-se criar um recurso novo.</b>
</div>
<div class="container" style="width: 850px">
    <form id="formulario">
        <fieldset>
            <legend>Cadastro de recursos.</legend>
            <table class="form-container">
                <tr>
                    <td class="field-size3"><label for="codificacao">Codificação:</label></td>
                    <td colspan="3">
                        <select name="codificacao" id="codificacao">
                            <option value="1">1 - Recursos do Exercício Corrente</option>
                            <option value="2">2 - Recursos de Exercícios Anteriores</option>
                            <option value="9">9 - Recursos Condicionados</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="field-size3"><label for="classificacao">Classificação:</label></td>
                    <td colspan="3">
                        <select name="classificacao" id="classificacao">
                            <option value="">Selecione a classificação</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="recursoSiconfi">Recursos Siconfi:</label></td>
                    <td colspan="3">
                        <select name="recursoSiconfi" id="recursoSiconfi">
                            <option value="">Selecione o Recurso Siconfi</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="recursoGestao">Recurso:</label></td>
                    <td>
                        <input type="text" id="recursoGestao" name="recursoGestao" maxlength="5" class="field-size2">
                    </td>
                    <td>
                        <label for="subRecurso">Subrecurso:</label>
                    </td>
                    <td>
                        <input type="text" id="subRecurso" name="subRecurso" maxlength="5" class="field-size2">
                    </td>
                </tr>
                <tr>
                    <td><label for="descricao">Descrição:</label></td>
                    <td colspan="3">
                        <input name="descricao" id="descricao" >
                        <span id="atualizarNome">
                            <i class="fas fa-sync "></i>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><label for="complemento">Complemento:</label></td>
                    <td colspan="3">
                        <select name="complemento" id="complemento">
                            <option value="">Selecione o complemento</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="tipoDetalhamento">Tipo de Detalhamento:</label></td>
                    <td colspan="3"><select id="tipoDetalhamento"> </select></td>
                </tr>
                <tr>
                    <td><label for="tipoRecurso">Tipo do Recurso:</label></td>
                    <td colspan="3">
                        <select name="tipoRecurso" id="tipoRecurso">
                            <option value="1">Recurso Livre</option>
                            <option value="2" selected>Recurso Vinculado</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <fieldset class="separator">
                            <legend><label for="finalidade">Finalidade</label></legend>
                            <textarea rows="3" cols="50" id="finalidade" name="finalidade"></textarea>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </fieldset>

        <input type="hidden" id="codigo" name="codigo">
        <button type="button" id="btnSalvar" class="btn btn-light">
            <i class="fas fa-save"></i>
            Salvar
        </button>
        <button type="button" id="btnPesquisar" class="btn btn-light">
            <i class="fas fa-search"></i>
            Pesquisar
        </button>
    </form>
</div>
</body>
<?php db_menu() ?>
<script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
<script type="text/javascript" src="scripts/classes/http/http.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>

<script>
    const formulario = document.getElementById('formulario');
    const codigo = document.getElementById('codigo');

    const cboCodificacao = document.getElementById('codificacao');
    const cboClassificacao = document.getElementById('classificacao');
    const cboRecurso = document.getElementById('recursoSiconfi');
    const recursoGestao = document.getElementById('recursoGestao');
    const subRecurso = document.getElementById('subRecurso');
    const cboComplemento = document.getElementById('complemento');
    const cboTipoDetalhamento = document.getElementById('tipoDetalhamento');
    const descricao = document.getElementById('descricao');
    const finalidade = document.getElementById('finalidade');
    const btnSalvar = document.getElementById('btnSalvar');
    const btnPesquisar = document.getElementById('btnPesquisar');
    const btnAtualizarNome = document.getElementById('atualizarNome');

    const routs = {
        classificacao: 'financeiro/orcamento/classificacao/com-siconfi',
        tipoDetalhamento: 'financeiro/orcamento/tipos-detalhamento',
        complementos: 'financeiro/orcamento/cadastro/complemento',
        salvar: 'financeiro/orcamento/cadastro/recurso/salvar-atualizado',
        getRecurso: 'financeiro/orcamento/cadastro/recurso'
    }

    const listaClassificacao = [];

    const limpaDadosRecurso = () => {
        cboRecurso.options.length = 0;
        cboRecurso.add(new Option('Selecione o Recurso Siconfi', ''));
        if (codigo.value === '') {
            descricao.value = '';
        }
    }

    const getClassificacaoSelecionada = () => {
        return listaClassificacao.filter(classificacao => {
            return classificacao.id == cboClassificacao.value;
        }).shift();
    }

    const getRecursoSelecionado = () => {
        let classificacao = getClassificacaoSelecionada();
        return classificacao.fontes_siconfi.filter((fonte) => {
            return fonte.codigo_siconfi == cboRecurso.value;
        }).shift()
    }

    cboCodificacao.addEventListener('change', () => {
        limpaDadosRecurso();
    });

    cboClassificacao.addEventListener('change', () => {
        limpaDadosRecurso();
        if (cboClassificacao.value == '') {
            return;
        }

        let classificacao = getClassificacaoSelecionada();
        for (let recurso of classificacao.fontes_siconfi) {
            cboRecurso.add(new Option(`${recurso.codigo_siconfi} - ${recurso.descricao}`, recurso.codigo_siconfi));
        }
    });

    cboRecurso.addEventListener('change', () => {
        if (cboRecurso.value == '') {
            descricao.value = '';
            return;
        }

        if (codigo.value === '') {
            let recurso = getRecursoSelecionado();
            descricao.value = recurso.descricao;
            recursoGestao.value = `${cboCodificacao.value}${recurso.codigo_siconfi}`;
            finalidade.value = recurso.finalidade;
        }
    })

    const buscarClassificacoes = () => {
        HttpClient.get(`${PHPSession.requestApi}/${routs.classificacao}`).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            for (let classificaco of response.data) {
                listaClassificacao.push(classificaco);
                cboClassificacao.add(new Option(classificaco.descricao, classificaco.id));
            }
        });
    };

    const buscarTiposDetalhamento = () => {
        HttpClient.get(`${PHPSession.requestApi}/${routs.tipoDetalhamento}`).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            for (let detalhamento of response.data) {
                cboTipoDetalhamento.add(
                    new Option(`${detalhamento.codigo} - ${detalhamento.descricao}`, detalhamento.codigo)
                );
            }
        });
    };

    const buscarComplementos = () => {
        HttpClient.get(`${PHPSession.requestApi}/${routs.complementos}`).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            for (let complemento of response.data) {
                cboComplemento.add(new Option(`${complemento.codigo} - ${complemento.descricao}`, complemento.codigo));
            }
        });
    };

    PHPSession.loadData().then(() => {
        buscarClassificacoes();
        buscarTiposDetalhamento();
        buscarComplementos();
    });

    const validarDados = () => {
        try {
            if (cboRecurso.value == '') {
                throw 'Selecione o Recurso do SICONF.';
            }
            if (cboTipoDetalhamento.value == '') {
                throw 'Selecione o Detalhamento.';
            }
            if (descricao.value == '') {
                throw 'Informe a descrição do Recurso.';
            }
            if (cboComplemento.value == '') {
                throw 'Selecione o Complemento do Recurso.';
            }
            if (finalidade.value == '') {
                throw 'Informe a Finalidade do Recurso.';
            }
        } catch (e) {
            alert(e);
            return false;
        }

        return true;
    };

    btnSalvar.addEventListener('click', () => {
        if (!validarDados()) {
            return;
        }

        let recurso = getRecursoSelecionado();

        const formData = new FormData(formulario);
        formData.append('nomeRecursoSiconfi', recurso.descricao);
        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${routs.salvar}`, {body: formData}).then(response => {
            alert(response.message);
            if (response.error) {
                return;
            }

            formulario.reset();
            location.reload();
        });
    });

    btnPesquisar.addEventListener('click', () => {
        formulario.reset();
        var sUrl = 'func_novosRecursos.php?funcao_js=parent.preenchePesquisa|o15_codigo&ativo=1';
        js_OpenJanelaIframe('', 'db_iframe_orctiporec', sUrl, 'Pesquisa', true);
    });

    function preenchePesquisa(o15_codigo) {
        db_iframe_orctiporec.hide();
        let excicio = PHPSession.getValueSession('DB_anousu');
        HttpClient.get(`${PHPSession.requestApi}/${routs.getRecurso}/${o15_codigo}/${excicio}`).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            let recurso = response.data;

            if (recurso.invalido) {
                alert('O recurso selecionado ainda esta com os dados do siconfi de 2021. Realize o De Para ou selecione a classificação e recurso de 2022 e clique em salvar.');
            } else {
                cboClassificacao.value = recurso.fonte_recurso.classificacaofr_id;
                cboClassificacao.dispatchEvent(new Event('change'));
                setTimeout(() => {
                    cboRecurso.value =  recurso.codigo_siconfi
                }, 100);
            }

            recursoGestao.value = recurso.fonte_recurso.gestao;
            descricao.value = recurso.fonte_recurso.descricao;
            cboComplemento.value = recurso.complemento.codigo;
            cboTipoDetalhamento.value = recurso.fonte_recurso.tipo_detalhamento;
            finalidade.value = recurso.o15_finali;
            codigo.value = recurso.o15_codigo;
            subRecurso.value = recurso.o15_recurso;
        });
    }

    btnAtualizarNome.addEventListener('click', () => {
        if (cboRecurso.value !=='') {
            let recurso = getRecursoSelecionado();
            descricao.value = recurso.descricao;
        }
    });
</script>
