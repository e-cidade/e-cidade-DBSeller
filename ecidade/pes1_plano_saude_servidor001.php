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
require_once modification("libs/db_conecta.php");
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
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBAbasItem.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInputValor.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInputCpf.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <style>
        .aviso {
            margin: 15px auto 0 auto;
            background-color: #FFF;
            border-radius: 4px;
            font-weight: bold;
            padding: 15px;
            width: 500px;
            text-align: justify;
        }
    </style>
</head>
<body>
<div id="ctnAbas"></div>
<div>
    <div id="abaPlanoSaude">
        <!-- <div class="aviso">
            Para uso dessa rotina, o ponto já deve estar inicializado.
        </div> -->
        <?php require_once modification('forms/db_fromlancarplanosaudeservidor.php'); ?>
    </div>
    <div id="abaDependentes">
        <?php require_once modification('forms/db_fromplanosaudedependentes.php'); ?>
    </div>
</div>
<?php db_menu(); ?>
<script rel="script" type="text/javascript">
    const dbAbas = new DBAbas(document.querySelector('#ctnAbas'));
    const abaPlanoDeSaude = dbAbas.adicionarAba('Planos de Saúde', document.querySelector('#abaPlanoSaude'));
    const abaDependentes = dbAbas.adicionarAba('Dependentes', document.querySelector('#abaDependentes'));

    const formServidorOperadoraSaude = document.querySelector('#formServidorOperadoraSaude');
    const inputSequencialServidorOperadoraSaude = formServidorOperadoraSaude.querySelector('#sequencial');
    const inputCodigoServidor = formServidorOperadoraSaude.querySelector('#codigoServidor');
    const inputNomeServidor = formServidorOperadoraSaude.querySelector('#nomeServidor');
    const inputCodigoOperadora = formServidorOperadoraSaude.querySelector('#codigoOperadora');
    const inputNomeOperadora = formServidorOperadoraSaude.querySelector('#nomeOperadora');
    const inputCodigoRubrica = formServidorOperadoraSaude.querySelector('#codigoRubrica');
    const inputDescricaoRubrica = formServidorOperadoraSaude.querySelector('#descricaoRubrica');
    const inputSalvarServidorOperadoraSaude = formServidorOperadoraSaude.querySelector('#salvarServidorOperadoraSaude');

    const formDependente = document.querySelector('#formDependente');
    const inputSequencialDependente = formDependente.querySelector('#sequencial');
    const aDependente = formDependente.querySelector('#aDependente');
    const inputCodigoDependente = formDependente.querySelector('#codigoDependente');
    const inputNomeDependente = formDependente.querySelector('#nomeDependente');
    const selectTipoDependente = formDependente.querySelector('#tipoDependente');
    const inputCodigoRubricaDependente = formDependente.querySelector('#codigoRubricaDependente');
    const inputDescricaoRubricaDependente = formDependente.querySelector('#descricaoRubricaDependente');

    const inputSalvarDependente = formDependente.querySelector('#salvarDependente');

    const dbInputValorTitular = new DBInputValor(formServidorOperadoraSaude.querySelector('#valorServidor'));
    const dbInputValorDependente = new DBInputValor(formDependente.querySelector('#valorDependente'));

    const collectionServidorOperadoraSaude = new Collection().setId('sequencial');
    const dataGridCollectionServidorOperadoraSaude = DatagridCollection.create(collectionServidorOperadoraSaude).
        configure('order', false);
    const collectionServidorOperadoraSaudeDependente = new Collection().setId('sequencial');
    const dataGridCollectionServidorOperadoraSaudeDependente = DatagridCollection.create(
        collectionServidorOperadoraSaudeDependente).configure('order', false);

    const tiposDepentente = new Map();

    const dbLookUpServidor = new DBLookUp($('ancoraServidor'), inputCodigoServidor, inputNomeServidor, {
        'sArquivo': 'func_rhpessoal.php',
        'sObjetoLookUp': 'db_iframe_rhpessoal',
        'sLabel': 'Pesquisar Servidor',
        'aCamposAdicionais': ['z01_nome'],
        'atualizarParametros': true
    });

    new DBLookUp($('ancoraOperadora'), inputCodigoOperadora, inputNomeOperadora, {
        'sArquivo': 'func_operadorasaude.php',
        'sObjetoLookUp': 'db_iframe_operadorasaude',
        'sLabel': 'Pesquisar Operadoras de Plano de Saúde'
    });

    new DBLookUp($('ancoraRubrica'), inputCodigoRubrica, inputDescricaoRubrica, {
        'sArquivo': 'func_rhrubricas.php',
        'sObjetoLookUp': 'db_iframe_rhrubricas',
        'sLabel': 'Pesquisar Rubricas',
        'aParametrosAdicionais': ['tipo_rubrica=2']
    });

    const lookUpDependente = new DBLookUp(aDependente, inputCodigoDependente, inputNomeDependente, {
        'sArquivo': 'func_rhdepend.php',
        'sObjetoLookUp': 'db_iframe_rhdepend',
        'sLabel': 'Pesquisar Dependentes',
        'atualizarParametros': true
    });

    new DBLookUp($('ancoraRubricaDependente'), inputCodigoRubricaDependente, inputDescricaoRubricaDependente, {
        'sArquivo': 'func_rhrubricas.php',
        'sObjetoLookUp': 'db_iframe_rhrubricas',
        'sLabel': 'Pesquisar Rubricas',
        'aParametrosAdicionais': ['tipo_rubrica=2']
    });

    const dadosServidorValidos = () => {
        try {
            if (inputCodigoServidor.value === '') {
                throw 'É necessário informar o servidor.';
            }

            if (inputCodigoOperadora.value === '') {
                throw 'É necessário informar a operadora de saúde.';
            }

            if (inputCodigoRubrica.value === '') {
                throw 'É necessário informar a rubrica.';
            }
        } catch (e) {
            alert(e);
            return false;
        }

        return true;
    };

    const populaGridDependente = dependente => {
        collectionServidorOperadoraSaudeDependente.add({
            sequencial: dependente.sequencial,
            nome: dependente.dependente.nome,
            tipo: dependente.tipo,
            valor: dependente.valor,
            rubrica: dependente.rubrica,
            objeto: dependente
        });
    };

    const buscarDependentes = () => {
        dataGridCollectionServidorOperadoraSaudeDependente.clear();

        const parametros = new FormData(formServidorOperadoraSaude);
        parametros.append('acao', 'buscarDependentes');
        parametros.append('codigoPlanoSaudeServidor', inputSequencialServidorOperadoraSaude.value);

        HttpClient.post('pes4_planosaudeservidor.RPC.php', {body: parametros}).then(response => {
            if (response.erro) {
                alert(response.message);
                return;
            }
            response.dependentes.map(function(dependente) {
                populaGridDependente(dependente);
            });
            dataGridCollectionServidorOperadoraSaudeDependente.reload();
        });
    };

    const salvarServidorOperadoraSaude = () => {
        if (dadosServidorValidos() === false) {
            return;
        }

        const parametros = new FormData(formServidorOperadoraSaude);
        parametros.append('acao', 'salvarServidorOperadoraSaude');
        parametros.append('valor', dbInputValorTitular.getValue());

        HttpClient.post('pes4_planosaudeservidor.RPC.php', {body: parametros}).then(response => {
            alert(response.mensagem);

            if (response.erro) {
                return controlarAba(false);
            }

            collectionServidorOperadoraSaude.add({
                sequencial: response.servidorOperadoraSaude.sequencial,
                operadora: response.servidorOperadoraSaude.operadoraSaude.cgm.nome,
                rubrica: response.servidorOperadoraSaude.rubrica.descricao,
                valor: response.servidorOperadoraSaude.valor,
                objeto: response.servidorOperadoraSaude
            });

            inputSequencialServidorOperadoraSaude.value = response.servidorOperadoraSaude.sequencial;
            document.querySelector('#plano').defaultValue = response.servidorOperadoraSaude.operadoraSaude.cgm.nome;
            dataGridCollectionServidorOperadoraSaude.reload();
            abaPlanoDeSaude.setVisibilidade(false);
            abaDependentes.setVisibilidade(true);
            controlarAba(false);
            buscarDependentes();
        });
    };

    const buscarTipos = () => {
        const parametros = new FormData();
        parametros.append('acao', 'buscarTiposDependentes');

        HttpClient.post('pes4_planosaudeservidor.RPC.php', {body: parametros}).then(response => {
            if (response.erro) {
                return alert(response.mensagem);
            }
            response.tiposDepentente.map(tipoDependente => {
                tiposDepentente.set(tipoDependente.value, tipoDependente.label);
                selectTipoDependente.add(new Option(tipoDependente.label, tipoDependente.value));
            });
        });
    };

    const buscarServidorOperadoraSaude = () => {
        const parametros = new FormData();
        parametros.append('acao', 'buscarServidorOperadoraSaude');
        parametros.append('servidor', inputCodigoServidor.value);

        HttpClient.post('pes4_planosaudeservidor.RPC.php', {body: parametros}).then(response => {
            if (response.erro) {
                return alert(response.mensagem);
            }

            response.servidorOperadoraSaude.map(servidorOperadoraSaude => {
                collectionServidorOperadoraSaude.add({
                    sequencial: servidorOperadoraSaude.sequencial,
                    operadora: servidorOperadoraSaude.operadoraSaude.cgm.nome,
                    rubrica: servidorOperadoraSaude.rubrica.descricao,
                    valor: servidorOperadoraSaude.valor,
                    objeto: servidorOperadoraSaude
                });
            });

            dataGridCollectionServidorOperadoraSaude.reload();
        });
    };

    const excluirServidorOperadoraSaude = (event, linha) => {
        if (confirm(
            'Deseja excluir o vínculo do servidor com o plano de saúde ' + linha.objeto.operadoraSaude.cgm.nome +
            '? Todos dependentes do plano também serão excluídos.')) {
            const parametros = new FormData();
            parametros.append('acao', 'excluirServidorOperadoraSaude');
            parametros.append('sequencial', linha.objeto.sequencial);

            HttpClient.post('pes4_planosaudeservidor.RPC.php', {body: parametros}).then(response => {
                alert(response.mensagem);

                if (response.erro) {
                    return;
                }

                collectionServidorOperadoraSaude.remove(linha.objeto.sequencial);
                dataGridCollectionServidorOperadoraSaudeDependente.clear();
                dataGridCollectionServidorOperadoraSaude.reload();
                formServidorOperadoraSaude.reset();
                formDependente.reset();
                controlarAba(true);
            });
        }
    };

    const montarGridServidorOperadoraSaude = () => {
        dataGridCollectionServidorOperadoraSaude.addColumn('operadora', {
            label: 'Operadora',
            align: 'left',
            width: '300px'
        });
        dataGridCollectionServidorOperadoraSaude.addColumn('rubrica', {
            label: 'Rubrica',
            align: 'left',
            width: '300px'
        }).transform(function(valor, linha) {
            return linha.objeto.rubrica.codigo + ' - ' + linha.objeto.rubrica.descricao;
        });
        dataGridCollectionServidorOperadoraSaude.addColumn('valor', {
            label: 'Valor',
            align: 'left',
            width: '300px'
        }).transform('dinheiro');

        dataGridCollectionServidorOperadoraSaude.addAction('A', 'Alterar', (event, linha) => {
            document.querySelector('#plano').defaultValue = linha.objeto.operadoraSaude.cgm.nome;
            inputSequencialServidorOperadoraSaude.value = linha.objeto.sequencial;
            inputNomeOperadora.value = linha.objeto.operadoraSaude.cgm.nome;
            inputDescricaoRubrica.value = linha.objeto.rubrica.descricao;
            inputCodigoOperadora.value = linha.objeto.operadoraSaude.sequencial;
            inputCodigoRubrica.value = linha.objeto.rubrica.codigo;
            inputCodigoServidor.value = linha.objeto.servidor.matricula;
            dbInputValorTitular.setValue(linha.objeto.valor);
            lookUpDependente.setParametrosAdicionais(['servidor=' + linha.objeto.servidor.matricula]);
            controlarAba(false);
            buscarDependentes();
        });
        dataGridCollectionServidorOperadoraSaude.addAction('E', 'Excluir', excluirServidorOperadoraSaude);
        dataGridCollectionServidorOperadoraSaude.show(
            document.querySelector('#divDataGridCollectionServidorOperadoraSaude')
        );
    };

    const excluirServidorOperadoraSaudeDependente = (event, linha) => {
        if (confirm(
            'Deseja excluir o vínculo do dependente com o plano de saúde ' +
            linha.objeto.servidorOperadoraSaude.operadoraSaude.cgm.nome +
            '?')) {
            const parametros = new FormData();
            parametros.append('acao', 'excluirServidorOperadoraSaudeDependente');
            parametros.append('sequencial', linha.objeto.sequencial);

            HttpClient.post('pes4_planosaudeservidor.RPC.php', {body: parametros}).then(response => {
                alert(response.mensagem);

                if (response.erro) {
                    return;
                }

                collectionServidorOperadoraSaudeDependente.remove(linha.objeto.sequencial);
                dataGridCollectionServidorOperadoraSaudeDependente.reload();
                formDependente.reset();
            });
        }
    };

    const montarGridServidorOperadoraSaudeDependente = () => {
        dataGridCollectionServidorOperadoraSaudeDependente.addColumn('nome', {
            label: 'Nome',
            align: 'left',
            width: '350px'
        });
        dataGridCollectionServidorOperadoraSaudeDependente.addColumn('tipo', {
            label: 'Tipo',
            align: 'left',
            width: '200px'
        }).transform(function(valor, dado) {
            const tipo = tiposDepentente.get(dado.objeto.tipo);
            return '<label title="' + tipo + '">' + tipo + '</label>';
        });
        dataGridCollectionServidorOperadoraSaudeDependente.addColumn('rubrica', {
            label: 'Rubrica',
            align: 'left',
            width: '200px'
        }).transform(function(valor, dado) {
            return dado.objeto.rubrica.codigo + ' - ' + dado.objeto.rubrica.descricao;
        });

        dataGridCollectionServidorOperadoraSaudeDependente.addColumn('valor', {
            label: 'Valor',
            align: 'center',
            width: '120px'
        }).transform('dinheiro');

        dataGridCollectionServidorOperadoraSaudeDependente.addAction('A', 'Alterar', (event, linha) => {
            inputCodigoDependente.value = linha.objeto.dependente.codigo;
            inputSequencialDependente.value = linha.sequencial;
            inputNomeDependente.value = linha.nome;
            selectTipoDependente.value = linha.tipo;
            inputCodigoRubricaDependente.value = linha.rubrica.codigo;
            inputDescricaoRubricaDependente.value = linha.rubrica.descricao;
            dbInputValorDependente.setValue(linha.valor);
        });

        dataGridCollectionServidorOperadoraSaudeDependente.addAction('E', 'Excluir',
            excluirServidorOperadoraSaudeDependente);
        dataGridCollectionServidorOperadoraSaudeDependente.show(
            document.querySelector('#divDataGridCollectionServidorOperadoraSaudeDependente')
        );
    };

    const controlarAba = bloqueia => {
        abaDependentes.lBloqueada = bloqueia;
    };

    const callBackLookUpServidor = retorno => {
        const valido = retorno === false || typeof retorno === 'object';
        dataGridCollectionServidorOperadoraSaude.clear();
        controlarAba(true);

        inputSequencialServidorOperadoraSaude.value = '';
        inputCodigoOperadora.value = '';
        inputNomeOperadora.value = '';
        inputCodigoRubrica.value = '';
        inputDescricaoRubrica.value = '';
        dbInputValorTitular.setValue(0);

        if (valido) {
            buscarServidorOperadoraSaude();
        }
    };

    const validaDadosDependente = () => {
        try {
            if (inputSequencialServidorOperadoraSaude.value === '') {
                throw 'É necessário salvar o plano de saúde do servidor antes de adicionar os dependentes';
            }

            if (inputCodigoDependente.value === '') {
                throw 'O campo "Dependente" é obrigatório.';
            }

            if (selectTipoDependente.value === '') {
                throw 'O campo "Tipo" é obrigatório.';
            }

            if (inputCodigoRubrica.value === '') {
                throw 'O campo "Rubrica" é obrigatório.';
            }

            collectionServidorOperadoraSaudeDependente.itens.forEach((item) => {
                if (item.rubrica.codigo === inputCodigoRubricaDependente.value
                && item.objeto.dependente.codigo != inputCodigoDependente.value) {
                    throw 'Rubrica já cadastrada para outro dependente. Favor revisar.';
                }
            });



        } catch (e) {
            alert(e);
            return false;
        }

        return true;

    };

    const salvarDependentes = () => {
        if (validaDadosDependente() === false) {
            return;
        }

        const parametros = new FormData(formDependente);
        parametros.append('acao', 'salvarDependente');
        parametros.append('codigoPlanoSaudeServidor', inputSequencialServidorOperadoraSaude.value);
        parametros.append('valor', dbInputValorDependente.getValue());

        HttpClient.post('pes4_planosaudeservidor.RPC.php', {body: parametros}).then(response => {
            alert(response.mensagem);

            if (response.erro) {
                return;
            }

            populaGridDependente(response.dependente);
            formDependente.reset();
            dataGridCollectionServidorOperadoraSaudeDependente.reload();
        });
    };

    const buscarCompetencia = () => {
        const parametros = new FormData();
        parametros.append('acao', 'buscarCompetencia');

        HttpClient.post('pes4_planosaudeservidor.RPC.php', {body: parametros}).then(response => {
            if (response.erro) {
                return alert(response.mensagem);
            }

            document.querySelector('#mes').defaultValue = response.competencia.mes;
            document.querySelector('#ano').defaultValue = response.competencia.ano;
        });
    };

    (() => {
        'use strict';

        formServidorOperadoraSaude.addEventListener('reset', () => {
            Array.from(formServidorOperadoraSaude.querySelectorAll('input[type="hidden"]')).forEach(elemento => {
                elemento.value = '';
            });
        });

        formDependente.addEventListener('reset', () => {
            Array.from(formDependente.querySelectorAll('input[type="hidden"]')).forEach(elemento => {
                elemento.value = '';
            });
        });

        formServidorOperadoraSaude.querySelector('#limparServidorOperadoraSaude').addEventListener('click', () => {
            dataGridCollectionServidorOperadoraSaude.clear();
            dataGridCollectionServidorOperadoraSaudeDependente.clear();
            formDependente.reset();
            controlarAba(true);
        });

        inputCodigoServidor.addEventListener('input', () => controlarAba(inputCodigoServidor.value === ''));
        inputSalvarServidorOperadoraSaude.addEventListener('click', salvarServidorOperadoraSaude);
        inputSalvarDependente.addEventListener('click', salvarDependentes);

        dbLookUpServidor.setCallBack('onClick', callBackLookUpServidor);
        dbLookUpServidor.setCallBack('onChange', callBackLookUpServidor);

        controlarAba(true);

        buscarTipos();
        buscarCompetencia();
        montarGridServidorOperadoraSaude();
        montarGridServidorOperadoraSaudeDependente();
    })();
</script>
</body>
</html>
