<?php

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

?>
<html>
<head>
    <title>DBSeller Informática Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link rel="stylesheet" type="text/css" href="assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" type="text/css"
          href="./extension/package/Desktop/assets/vendors/select2/css/select2.min.css"/>
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/arrays.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script language="javascript" type="text/javascript" src="scripts/widgets/DBAbasItem.widget.js"></script>
    <script language="javascript" type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>

    <style type="text/css">
        span.legenda {
            padding: 5px;
            border: 1px solid #444;
        }
    </style>
</head>
<body class="body-default">
<div id='ctnAbas'></div>

<div id="abaFiltro" class="container">
    <form id="frmFiltros" method="post" action="">
        <fieldset>
            <legend>Manutenção no Referencial Curricular / BNCC</legend>
            <table class="form-container">
                <tr>
                    <td><label for="filtroAno">Ano:</label></td>
                    <td>
                        <select id="filtroAno" name="filtroAno">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="filtroEnsino">Ensino:</label></td>
                    <td>
                        <select id="filtroEnsino" name="filtroEnsino">
                            <option value="EI">Educação Infantil</option>
                            <option value="EF">Ensino Fudamental</option>
                        </select>
                    </td>
                </tr>
            </table>
        </fieldset>
        <button id="btnProximo" type="button">
            <i class="fas fa-arrow-right"></i>
            Próximo
        </button>
    </form>
</div>

<div id="abaManutencao" style="display: none">
    <div class="container" id="ctnEducacaoInfantil" style="display: none">
        <form id="frmManutencaoEI" name="frmManutencaoEI">
            <fieldset>
                <legend>Manutenção da Educação Infantil</legend>
                <table class="form-container">
                    <tr>
                        <td><label for="camposExperiencia">Campos de Experiência:</label></td>
                        <td>
                            <select id="camposExperiencia" name="camposExperiencia">
                                <option value="">Selecione</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="faixaEtaria">Faixa Etaria:</label></td>
                        <td>
                            <select id="faixaEtaria" name="faixaEtaria">
                                <option value="">Selecione</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
    <div class="container" id="ctnEnsinoFundamental" style="display: none; width: 400px">
        <form id="frmManutencaoEF" name="frmManutencaoEF">
            <fieldset>
                <legend>Manutenção do Ensino Fundamental</legend>
                <table class="form-container">
                    <tr>
                        <td><label for="disciplina">Disciplina / Componente:</label></td>
                        <td>
                            <select id="disciplina" name="disciplina">
                                <option value="">Selecione</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="unidadeTematica">Unidade Temática:</label></td>
                        <td>
                            <select id="unidadeTematica" name="unidadeTematica">
                                <option value="">Selecione</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="objetoConhecimento">Objeto de Conhecimento: </label></td>
                        <td>
                            <select id="objetoConhecimento" name="objetoConhecimento">
                                <option value="">Selecione</option>
                            </select>
                        </td>
                        <td id="tdEditaObjConhecimento" style="display: none">
                            <button href="" id="editaObjConhecimento" style="display: none"><i class="fas fa-cogs"></i></button>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>

    <div class="container" id="inserirObjConhecimento">
        <div class="container">
            <fieldset style="width: 400px;">
                <legend>Cadastrar Nova Habilidade / Objeto de Conhecimento</legend>


                <table class="form-container">
                    <tr>
                        <td><label for="objetoConhecimento">Disciplina: </label></td>
                        <td>
                            <input id="disciplinaModal" type="text" name="disciplinaModal" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="objetoConhecimento">Unidade Temática: </label></td>
                        <td>
                            <input id="unidadeTematicaModal" type="text" name="unidadeTematicaModal" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="objetoConhecimento">Etapa(s) / Ano(s): </label></td>
                        <td>
                            <input type="checkbox" name="etapas" value="1º">
                            <label>1º</label>
                            <input type="checkbox" name="etapas" value="2º">
                            <label>2º</label>
                            <input type="checkbox" name="etapas" value="3º">
                            <label>3º</label>
                            <input type="checkbox" name="etapas" value="4º">
                            <label>4º</label>
                            <input type="checkbox" name="etapas" value="5º">
                            <label>5º</label>
                            <input type="checkbox" name="etapas" value="6º">
                            <label>6º</label>
                            <input type="checkbox" name="etapas" value="7º">
                            <label>7º</label>
                            <input type="checkbox" name="etapas" value="8º">
                            <label>8º</label>
                            <input type="checkbox" name="etapas" value="9º">
                            <label>9º</label>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="objetoConhecimento">Código: </label></td>
                        <td>
                            <input type="text" id="codigoHabilidade">
                        </td>
                    </tr>
                    <tr>
                        <td><label for="objetoConhecimento">Objeto de Conhecimento: </label></td>
                        <td id="tdObjConhecimento">
                            <select id="objConhecimento" name="objConhecimento" style="width: 158px">
                                <option value="">Selecione</option>
                            </select>
                            <textarea type="text" id="novoObjetoConhecimento" style="display:none" placeholder="Descrição do novo objeto" rows="3" cols="70"></textarea>
                            <button href="" id="plusObjConhecimento">Add Novo Objeto</button>
                        </td>
                    </tr>
                </table>
                <fieldset class="separator">
                    <legend><label for="habilidadeModal">Habilidade</label></legend>
                    <textarea rel="ignore-css" id="habilidadeModal" rows="3" cols="70"></textarea>
                </fieldset>
                <br>
                <div class="subcontainer">
                    <button id="salvarHabilidadeModal" type="button">
                        <i class="fas fa-save"></i>
                        Salvar
                    </button>
                </div>
                <br>
            </fieldset>
        </div>
    </div>

    <div class="subcontainer">
        <button id="btnBuscarHabilidades" type="button">
            <i class="fas fa-search"></i>
            Buscar
        </button>
        <button id="btnAddHabilidades" type="button" style="display: none">
            <i class="fas fa-plus"></i>
            Adicionar Habilidade / Objeto de Conhecimento
        </button>
    </div>
    <fieldset style="width: 1200px;" class="container">
        <div class="alert alert-primary text-left" role="alert">
        A exclusão da Habilidade resultará na exclusão do vinculo de Registros de Aulas que utilizam esta Habilidade!
        </div>
        <legend>Habilidades / Objetos de Conhecimento / Objetivos de aprendizagem</legend>
        <div style="padding: 5px; margin-bottom: 10px;">
            <span class="legenda" style="background-color: #fff"><b>&nbsp;Habilidade BNCC não utilizada</b></span>
            <span class="legenda" style="background-color: #ccc"><b>&nbsp;Utiliza habilidade BNCC original</b></span>
            <span class="legenda" style="background-color: #8fcdf8">
                <b>&nbsp;Texto da habilidade editado/comentado</b>
            </span>
            <span class="legenda" style="background-color: #a7d09d"><b>&nbsp;Habilidade BNCC subdividida</b></span>
        </div>
        <div id="ctnGridHabilidades"> </div>
    </fieldset>
    <div class="container">
        <button id="btnSalvar"><i class="fas fa-save"></i> Salvar</button>
    </div>
</div>

<div id="ctnEdicao" style="display: none" >
    <fieldset>
        <legend>Comentar Habilidade</legend>
        <table class="form-container">
            <tr>
                <td class="field-size2 bold"><label for="inputCodigo">Código: </label></td>
                <td><input type="text" class="readonly field-size3" id="inputCodigo" disabled="true"/></td>
                <td><input type="hidden" class="field-size3" id="inputCodigoHiden"/></td>
            </tr>
            <tr id='linhaEtapaEditar' style='display: none;'>
                <td><label>Etapas: </label></td>
                <td id="ctnEtapasEditar"></td>
            </tr>
        </table>
        <fieldset class="separator">
            <legend><label for="habilidadeEditada">Habilidade</label></legend>
            <textarea id='habilidadeEditada'></textarea>
        </fieldset>
        <div class="container">
            <button id="btnRestaurar"><i class="fas fa-undo"></i> Restaurar Bncc</button>
            <button id="btnEditar"><i class="fas fa-save"></i> Editar</button>
        </div>
    </fieldset>
</div>

<div id="ctnEdicaoObj" style="display: none" >
    <div class="container">
        <fieldset style="width: 350px;" class="subcontainer">
            <legend>Editar Objetos de Conhecimento</legend>
            <table class="subcontainer">
                <tr>
                    <td><label for="objetoConhecimento">Disciplina / Componente: </label></td>
                    <td>
                        <input type="text" id="editaDisciplina" disabled>
                    </td>
                </tr>
                <tr>
                    <td><label for="objetoConhecimento">Unidade Temática: </label></td>
                    <td>
                        <input type="text" id="editaUndTematica" disabled>
                    </td>
                </tr>
                <tr>
                    <td><label for="objetoConhecimento">Objeto de Conheicmento: </label></td>
                    <td>
                        <textarea type="text" id="editaObjetoConhecimento" disabled="true"></textarea>
                        <input type="hidden" id="editaObjetoConhecimentoHidden">
                    </td>
                </tr>
            </table>
        </fieldset>
        <br>
        <div class="subcontainer">
            <button id="btnEditaObj" disabled="true"><i class="fas fa-save"></i> Editar</button>
        </div>
        <br>
        <fieldset >
            <div id="ctnGridEditaObj" style="width: 900px;"class="subcontainer"></div>
        </fieldset>
    </div>
</div>

<div id="ctnReferencial" style="display: none">
    <fieldset>
        <legend>Adicionar sub-nível na Habilidade da BNCC</legend>
        <table class="form-container">
            <tr>
                <td class="field-size3"><label for="inputCodigoReferencial">Informar Código:</label></td>
                <td>
                    <input type="text" class="field-size3" id="inputCodigoReferencial" maxlength="20" />
                    <input type="hidden" class="field-size3" id="inputCodigoHabilidade" />
                </td>
            </tr>
            <tr id='linhaEtapaReferencial' style='display: none;'>
                <td><label>Etapas / Anos: </label></td>
                <td id="ctnEtapasReferencial"> </td>
            </tr>
        </table>
        <fieldset class="separator">
            <legend><label for="habilidadeReferencial">Habilidade Referencial</label></legend>
            <textarea rel="ignore-css" id='habilidadeReferencial' rows="3" cols="135"></textarea>
        </fieldset>
    </fieldset>
    <div class="container">
        <button id="btnAdicionar"><i class="fas fa-plus"></i> Adicionar</button>
    </div>

    <fieldset>
        <legend>Habilidades Adicionadas no Referencial Estadual</legend>
        <div class="alert alert-primary text-left" role="alert">
        A exclusão da Habilidade resultará na exclusão do vinculo de Registros de Aulas que utilizam esta Habilidade!
        </div>
        <div id="ctnGridReferencial"></div>
    </fieldset>
    <div class="container">
        <button id="btnFechar"><i class="far fa-window-close"></i> Fechar</button>
    </div>

</div>

</body>
<?php db_menu() ?>

<script src="assets/jquery/jquery-3.5.1.min.js"></script>
<script src="extension/package/Desktop/assets/vendors/select2/js/select2.min.js"></script>
<script src="extension/package/Desktop/assets/vendors/select2/js/i18n/pt-BR.js"></script>

<script type="text/javascript">
    $.noConflict();
    const ctnAbaFiltro = document.getElementById('abaFiltro');
    const ctnAbaManutencao = document.getElementById('abaManutencao');
    const ctnAbas = new DBAbas(document.getElementById('ctnAbas'));
    const ctnGridObjConhecimento = document.getElementById('inserirObjConhecimento');
    const containerModalEdicao = document.getElementById('ctnEdicao');
    const containerModalReferencial = document.getElementById('ctnReferencial');
    const editaObjConhecimento = document.getElementById('editaObjConhecimento');

    /**
     * Função utilizada para fechar uma window aux
     * @param  windowAux instância da window
     * @param idMsgBoard id de uma instância de DBMessageBoard
     */
    const closeWindowAux = (windowAux, idMsgBoard) => {
        if (idMsgBoard !== undefined) {
            let msgBoard = document.getElementById(idMsgBoard);
            if (msgBoard) {
                msgBoard.parentNode.removeChild(msgBoard);
            }
        }

        if (!!windowAux.oDBMask) {
            windowAux.oDBMask.destroy();
        }
        windowAux.hide();
    };

    const windowEdicao = new windowAux('windowEdicao', 'Comentar Habilidade', 800, 400);
    windowEdicao.setContent(containerModalEdicao);
    windowEdicao.setShutDownFunction(() => {
        closeWindowAux(windowEdicao);
    });

    const ctnEdicaoObj = document.querySelector("#ctnEdicaoObj");
    const windowEdicaoObj = new windowAux('windowEdicaoObj', 'Editar Objetos de Conhecimento', 1000, 600);
    windowEdicaoObj.setContent(ctnEdicaoObj);
    windowEdicaoObj.setShutDownFunction(() => {
        document.querySelector('#editaDisciplina').value = "";
        document.querySelector('#editaUndTematica').value = "";
        document.querySelector('#editaObjetoConhecimentoHidden').value = "";
        document.querySelector('#editaObjetoConhecimento').value = "";
        document.querySelector('#editaObjetoConhecimento').disabled = true;
        gridHabilidades.clear();
        closeWindowAux(windowEdicaoObj);
    });

    const windowReferencial = new windowAux('windowReferencial', 'Adicionar Habilidade do Referencial Estadual', 1000, 700);
    windowReferencial.setContent(containerModalReferencial);
    windowReferencial.setShutDownFunction(() => {
        selecionaLinha();
        fechaModalReferencial()
    });

    const abaFiltros = ctnAbas.adicionarAba("Filtros", ctnAbaFiltro);
    const abaManutencao = ctnAbas.adicionarAba("Manutenção", ctnAbaManutencao);
    abaManutencao.fCallback = () => {
        // sempre que ativar a aba, executa a mesma ação do click no btn próximo
        if (abaManutencao.getSeletor().classList.contains('abaAtiva')) {
            clickProximo();
        }
    };
    abaManutencao.lBloqueada = true;

    // Elementos aba Filtros
    const cboAno = document.getElementById('filtroAno');
    const cboEnsino = document.getElementById('filtroEnsino');
    const btnProximo = document.getElementById('btnProximo');

    // Elementos aba Manutenção
    const ctnEducacaoInfantil = document.getElementById('ctnEducacaoInfantil');
    const ctnEnsinoFundamental = document.getElementById('ctnEnsinoFundamental');
    const btnBuscarHabilidades = document.getElementById('btnBuscarHabilidades');
    const btnSalvar = document.getElementById('btnSalvar');

    // Educação Infantil
    const cboCamposExperiencia = document.getElementById('camposExperiencia');
    const cboFaixaEtaria = document.getElementById('faixaEtaria');
    const frmEducacaoInfantil = document.getElementById('frmManutencaoEI');

    // Ensino Fundamental
    const cboDisciplina = document.getElementById('disciplina');
    const cboUnidadeTematica = document.getElementById('unidadeTematica');
    const cboObjetoConhecimento = document.getElementById('objetoConhecimento');
    const cboObjConhecimento = document.getElementById('objConhecimento');
    const frmEnsinoFundamental = document.getElementById('frmManutencaoEF');

    // Elementos modal edição
    const inputCodigo = document.getElementById('inputCodigo');
    const inputCodigoHiden =  document.getElementById('inputCodigoHiden');
    const txtHabilidadeEditada = document.getElementById('habilidadeEditada');
    const btnRestaurar = document.getElementById('btnRestaurar');
    const btnEditar = document.getElementById('btnEditar');
    const ctnEtapasEditar = document.getElementById('ctnEtapasEditar');
    const linhaEtapaEditar = document.getElementById('linhaEtapaEditar');

    // Elementos modal Referencial
    const inputCodigoReferencial = document.getElementById('inputCodigoReferencial');
    const inputCodigoHabilidade = document.getElementById('inputCodigoHabilidade');
    const txtHabilidadeReferencial = document.getElementById('habilidadeReferencial');
    const linhaEtapaReferencial = document.getElementById('linhaEtapaReferencial');
    const ctnEtapasReferencial = document.getElementById('ctnEtapasReferencial');
    const btnAdicionar = document.getElementById('btnAdicionar');
    const ctnGridReferencial = document.getElementById('ctnGridReferencial');
    const btnFechar = document.getElementById('btnFechar');
    const ctnGridEditaObj = document.getElementById('ctnGridEditaObj');
    const novoObjetoConhecimento = document.querySelector('#novoObjetoConhecimento');
    // elementos globais
    var filtros, habilidades, configuracao;

    const collectionEditaObj = new Collection().setId('nome');
    var gridEditaObj = new DatagridCollection(collectionEditaObj).configure({
        order: false,
        height: 300
    });

    gridEditaObj.addColumn('nome', {label: "Objeto de Conhecimento", width: '90%', align: 'center'});

    gridEditaObj.addAction('Editar', 'Editar', (event, linha) => {
        document.querySelector('#btnEditaObj').disabled = false;
        document.querySelector('#editaObjetoConhecimento').disabled = false;
        document.querySelector('#editaObjetoConhecimentoHidden').value = linha.nome;
        document.querySelector('#editaObjetoConhecimento').value = linha.nome;
    }, true, 'fa-edit');

    gridEditaObj.addAction('Excluir', 'Excluir', (event, linha) => {
        const formData = new FormData();
        var disciplina = document.querySelector('#editaDisciplina').value;
        var unidade_tematica = document.querySelector('#editaUndTematica').value;
        formData.append('acao', 'excluirObjetoConhecimento');
        formData.append('objeto', linha.nome);
        if(confirm("Esta ação acarretará na exclusão de todas as habilidades cadastradas nesse objeto de conhecimento. Deseja Continuar?")) {
            gridEditaObj.clear();
            HttpClient.post('edu4_manutencao_bncc.RPC.php', {body: formData}).then(async response => {
                alert(response.mensagem);
                if (response.erro) {
                    return;
                }
                for (let i = 0; i< cboObjetoConhecimento.options.length; i++) {
                    if(cboObjetoConhecimento.options[i].value == linha.nome) {
                        cboObjetoConhecimento.options[i].remove();
                    }
                }
                await setCollectionObjetosConhecimento(unidade_tematica, disciplina);
                gridEditaObj.reload();
            });
        }
    }, true, 'fa-trash');

    document.querySelector('#btnEditaObj').addEventListener('click', () =>{
        let c = 0;
        collectionEditaObj.itens.each(item => {
            if (document.querySelector('#editaObjetoConhecimento').value == item.nome) {
                c++;
            }
        })
        if (c > 0) {
            alert("Objeto de Conhecimento já existente");
            return;
        }
        var formData = new FormData();
        var disciplina = document.querySelector('#editaDisciplina').value;
        var unidade_tematica = document.querySelector('#editaUndTematica').value;
        formData.append('ed148_ano', cboAno.value);
        formData.append('ed148_objeto_conhecimento', document.querySelector('#editaObjetoConhecimentoHidden').value);
        formData.append('ed148_disciplina', disciplina);
        formData.append('ed148_unidade_tematica', unidade_tematica);
        formData.append('acao', 'editarObjetoConhecimento');
        formData.append('novoNome', document.querySelector('#editaObjetoConhecimento').value);


        HttpClient.post('edu4_manutencao_bncc.RPC.php', {body: formData}).then(async response => {
                gridEditaObj.clear();
                if (response.erro) {
                    alert(response.mensagem);
                }
                await setCollectionObjetosConhecimento(unidade_tematica, disciplina);
                for (let i = 0; i< cboObjetoConhecimento.options.length; i++) {
                    if(cboObjetoConhecimento.options[i].value == document.querySelector('#editaObjetoConhecimentoHidden').value) {
                        cboObjetoConhecimento.options[i].remove();
                    }
                }
                cboObjetoConhecimento.add(new Option(document.querySelector('#editaObjetoConhecimento').value));
                document.querySelector('#editaObjetoConhecimentoHidden').value = "";
                document.querySelector('#editaObjetoConhecimento').value = "";
                document.querySelector('#editaObjetoConhecimento').disabled = true;
                document.querySelector('#btnEditaObj').disabled = true;
                gridEditaObj.reload();
        });
    });



    document.querySelector('#plusObjConhecimento').addEventListener('click', event => {
        event.preventDefault();
        document.querySelector('#plusObjConhecimento').style.display = "none";
        novoObjetoConhecimento.style.display = "inline";
        document.querySelector('#objConhecimento').value = "";
        document.querySelector('#objConhecimento').style.display = 'none';
    })

    const collectionHabilidades = new Collection().setId('codigo');
    var gridHabilidades = new DatagridCollection(collectionHabilidades).configure({
        order: false,
        height: 300
    });

    const collectionHabilidadesReferencial = new Collection().setId('codigoReferencial');
    var gridHabilidadesReferencial = new DatagridCollection(collectionHabilidadesReferencial).configure({
        order: false,
        height: 200
    });

    const collectionObjConhecimento = new Collection().setId('codigo');
    var gridObjConhecimento = new DatagridCollection(collectionHabilidadesReferencial).configure({
        order: false,
        height: 200
    });

    const windowObjConhecimento = new windowAux('windowObjConhecimento', 'Cadastrar Nova Habilidade / Objeto de Conhecimento', 800, 500);
    windowObjConhecimento.setContent(ctnGridObjConhecimento);

    btnAddHabilidades.addEventListener('click', () => {
        windowObjConhecimento.show(0, 0, true);
        cboObjConhecimento.style.display = "inline";
        novoObjetoConhecimento.style.display = "none";
        if (cboDisciplina.value == "") {
            alert('Selecione a Disciplina / Componente!');
            return;
        }
        if (cboUnidadeTematica.value == "") {
            alert('Selecione a Unidade Temática!');
            return;
        }

        document.querySelector('#disciplinaModal').value = cboDisciplina.value;
        document.querySelector('#unidadeTematicaModal').value = cboUnidadeTematica.value;
    })

    editaObjConhecimento.addEventListener('click', async event => {
        event.preventDefault();
        gridEditaObj.clear();
        windowEdicaoObj.show(0, 0, true);
        ctnEdicaoObj.style.display= "block";
        document.querySelector('#editaDisciplina').value = cboDisciplina.value;
        document.querySelector('#editaUndTematica').value = cboUnidadeTematica.value;
        await setCollectionObjetosConhecimento(cboUnidadeTematica.value, cboDisciplina.value);
        gridEditaObj.reload();
        gridEditaObj.show(ctnGridEditaObj);

    })

    document.querySelector('#salvarHabilidadeModal').addEventListener('click', event => {
        event.preventDefault();
        var formData = new FormData();
        var habilidade = {};
        formData.append('acao', 'salvarHabilidadeEF');
        formData.append('ano', cboAno.value);
        var values = document.querySelectorAll('[name=etapas]:checked');
        var etapas = [];
        var disciplina = document.querySelector('#disciplinaModal');
        var unidadeTematica = document.querySelector('#unidadeTematicaModal');
        var codigo = document.querySelector('#codigoHabilidade');
        var descricao = document.querySelector('#habilidadeModal');

        if (codigo.value == "") {
            alert("Digite um Código");
            return;
        }

        if (descricao.value == "") {
            alert("Descreva a Habilidade");
            return;
        }

        for (var i = 0; i < values.length; i++) {
            etapas.push(values[i].value);
        }
        if (etapas.length == 0) {
            alert("Selecione Pelo Menos um Ano / Etapa");
            return;
        }

        habilidade.objetoConhecimento = novoObjetoConhecimento.value == "" ? removeNovaLinha(cboObjConhecimento.value) : removeNovaLinha(novoObjetoConhecimento.value);
        let c = 0;
        collectionEditaObj.itens.each(item =>{
            if(item.nome == novoObjetoConhecimento.value) {
                c++;
            }
        })
        if (c > 0) {
            alert("Objeto de Conhecimento já existente");
            return;
        }

        habilidade.etapas = etapas;
        habilidade.disciplina = disciplina.value;
        habilidade.codigo = codigo.value;
        habilidade.unidadeTematica  = unidadeTematica.value;
        habilidade.habilidade = `(${codigoHabilidade.value}) ${removeNovaLinha(descricao.value)}`;
        habilidade.etapas = etapas.join(',');
        formData.append('habilidades[]', JSON.stringify(habilidade));
        formData.append('novaHabilidade', true);
        HttpClient.post('edu4_manutencao_bncc.RPC.php', {body: formData}).then(response => {
            alert(response.mensagem);
            if (response.erro) {
                return
            }
            let values = document.querySelectorAll('[name=etapas]');
            for (var i = 0; i < values.length; i++) {
                values[i].checked = false;
            }
            opts = cboObjetoConhecimento.options;
            var cont = 0;
            for (let i = 0;  i < opts.length; i++) {
                if (opts[i].value == habilidade.objetoConhecimento) {
                    cont++
                }
            }
            if (cont == 0) {
                setCollectionObjetosConhecimento(unidadeTematica.value, disciplina.value);
                cboObjetoConhecimento.add(new Option(habilidade.objetoConhecimento,habilidade.objetoConhecimento ));
                cboObjConhecimento.add(new Option(habilidade.objetoConhecimento,habilidade.objetoConhecimento ));
            }
            let novaHabilidade = {
                id: null,
                codigoHabilidade: habilidade.codigo,
                codigo: habilidade.codigo,
                codigoReferencial: habilidade.codigo,
                habilidade: removeNovaLinha(habilidade.habilidade),
                objetoConhecimento: removeNovaLinha(habilidade.objetoConhecimento),
                etapa: etapas.join(','),
                ensino: cboEnsino.value,
                ano: cboAno.value
            };

            const formData2 = new FormData();
            formData2.append('acao', 'adicionarReferencial');
            formData2.append('novaHabilidade', JSON.stringify(novaHabilidade));

            HttpClient.post('edu4_manutencao_bncc.RPC.php', {body: formData2}).then(response => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                collectionHabilidadesReferencial.add(response.habilidadeReferencial);
                gridHabilidadesReferencial.reload();
            });

            disciplina.value = "";
            unidadeTematica.value = "";
            codigo.value = "";
            descricao.value = "";
            cboObjetoConhecimento.value = habilidade.objetoConhecimento;
            cboObjConhecimento.display = "inline";
            novoObjetoConhecimento.display = "none";
            document.querySelector('#plusObjConhecimento').style.display = "inline";
            fechaModalObjConhecimento();
            autalizaGridHabilidades();
        });

    })

     windowObjConhecimento.setShutDownFunction(() => {
        fechaModalObjConhecimento();
    });

    btnProximo.addEventListener('click', () => {
        clickProximo();
    });

    const fechaModalObjConhecimento = () => {
        let values = document.querySelectorAll('[name=etapas]');
        let disciplina = document.querySelector('#disciplinaModal');
        let unidadeTematica = document.querySelector('#unidadeTematicaModal');
        let codigo = document.querySelector('#codigoHabilidade');
        let descricao = document.querySelector('#habilidadeModal');
        document.querySelector('#plusObjConhecimento').style.display = "inline";
        for (var i = 0; i < values.length; i++) {
            values[i].checked = false;
        }
        cboObjConhecimento.value = "";
        disciplina.value = "";
        unidadeTematica.value = "";
        codigo.value = "";
        descricao.value = "";
        novoObjetoConhecimento.value = "";
        closeWindowAux(windowObjConhecimento);
    }

    const configuracaoLinhas = () => {
        let configuracaoLinha = {}
        switch (configuracao.value) {
            case 1:
                configuracaoLinha.widthHabilidade = '85%'
                if (isEnsinoFundamental()) {
                    configuracaoLinha.widthHabilidade = '70%'
                }
                break;
            case 2:
            case 3:
                configuracaoLinha.widthHabilidade = '70%'
                if (isEnsinoFundamental()) {
                    configuracaoLinha.widthHabilidade = '55%'
                }
            break;
        }
        return configuracaoLinha;
    };

    const clickProximo = () => {
        collectionHabilidades.clear();
        let configuracaoLinha = configuracaoLinhas();

        abaManutencao.lBloqueada = false;
        abaFiltros.setVisibilidade(false);
        abaManutencao.setVisibilidade(true);
        gridHabilidades = new DatagridCollection(collectionHabilidades).configure({
            order: false,
            height: 300
        });
        gridHabilidades.getGrid().setCheckbox(0);
        gridHabilidades.addColumn('codigo', {label: "Código", width: '15%', align: 'center'});
        gridHabilidades.addColumn('habilidade_editada',
            {label: "Habilidade / Objetos de Conhecimento", 'width': configuracaoLinha.widthHabilidade});
        if (cboEnsino.value === 'EI') {
            ctnEducacaoInfantil.style.display = '';
            ctnEnsinoFundamental.style.display = 'none';
            btnAddHabilidades.style = "display: none";
            initEducacaoInfantil()
        } else {
            ctnEducacaoInfantil.style.display = 'none';
            ctnEnsinoFundamental.style.display = '';
            if (configuracao.value === 3) {
                if (isEnsinoFundamental()) {
                    inputCodigo.removeAttribute('disabled');
                    inputCodigo.classList.remove('readonly');
                    document.querySelector('#tdEditaObjConhecimento').style =  "display: box";
                    btnAddHabilidades.style = "display: box";
                }
            }
            gridHabilidades.addColumn('etapas_editada', {label: "Etapa(s) / Ano(s)", 'width': '15%'});
            initEnsinoFundamental();
        }

        if (configuracao.value === 2 || configuracao.value === 3) {
            if (configuracao.value === 3) {
                gridHabilidades.addAction('Editar', 'Editar', (event, linha) => {
                    if (isEnsinoFundamental()) {
                        criarCheckboxEtapas(linhaEtapaEditar, ctnEtapasEditar, linha, false);
                    }
                    inputCodigoHiden.value = linha.codigo;
                    inputCodigo.value = linha.codigo;
                    txtHabilidadeEditada.value = removeNovaLinha(linha.habilidade_editada);
                    btnRestaurar.style.display = 'none';

                    btnEditar.onclick = () => {
                        atualizaHabilidade();
                    };

                    windowEdicao.show(0, 0, true);
                }, true, 'fa-edit');
            } else {
                gridHabilidades.addAction('Editar', 'Editar', (event, linha) => {
                    if (isEnsinoFundamental()) {
                        criarCheckboxEtapas(linhaEtapaEditar, ctnEtapasEditar, linha, false);
                    }

                    inputCodigo.value = linha.codigo;
                    txtHabilidadeEditada.value = removeNovaLinha(linha.habilidade_editada);

                    btnRestaurar.onclick = () => {
                        txtHabilidadeEditada.value = linha.habilidade;
                        if (isEnsinoFundamental()) {
                            criarCheckboxEtapas(linhaEtapaEditar, ctnEtapasEditar, linha, true);
                        }
                    };

                    btnEditar.onclick = () => {
                        atualizaHabilidade();
                    };

                windowEdicao.show(0, 0, true);
                }, true, 'fa-edit');
            }
        }

        if (configuracao.value === 3) {
            gridHabilidades.addAction('Adicionar Sub-nível', 'Adicionar Sub-nível', (event, linha) => {
                if (isEnsinoFundamental()) {
                    criarCheckboxEtapas(linhaEtapaReferencial, ctnEtapasReferencial, linha, false);
                }
                inputCodigoHabilidade.value = linha.codigo;
                collectionHabilidadesReferencial.clear();
                if (linha.habilidadeComentada != null) {
                     if (linha.habilidadeComentada.habilidadeReferencial != undefined) {
                        linha.habilidadeComentada.habilidadeReferencial.map((referencial) => {
                            collectionHabilidadesReferencial.add({...referencial});
                        });
                     } else {
                        linha.habilidadeComentada.map((referencial) => {
                            collectionHabilidadesReferencial.add({...referencial});
                        });
                     }
                }

                windowReferencial.show(0, 0, true);
                montaGridReferencial();
            }, true, 'fa-sitemap');

            gridHabilidades.addAction('Excluir', 'Excluir', (event, linha) => {
                if (confirm('Esta ação acarretará na exclusão dos subníveis da mesma. Deeja continuar?')) {
                    const formData = new FormData();
                    formData.append('acao', 'excluirHabilidadeEF');
                    formData.append('ano',  cboAno.value);
                    formData.append('codigo', linha.codigo);
                    formData.append('objetoConhecimento', linha.objetoConhecimento);
                    HttpClient.post('edu4_manutencao_bncc.RPC.php', {body: formData}).then(response => {
                        alert(response.mensagem);
                        if (response.erro) {
                            return;
                        }
                        autalizaGridHabilidades();
                    })
                }
            }, true, 'fa-trash');
        }

        gridHabilidades.addAction('Informação', 'Informação', (event, linha) => {
            alert(linha.habilidade_editada);
        }, true, 'fa-info-circle');

        gridHabilidades.show(document.getElementById('ctnGridHabilidades'));

        gridHabilidades.setEvent('onbeforerenderrows', function() {
            gridHabilidades.setSelectedItens([]);
            collectionHabilidades.get().map(function (linha)  {
                if (configuracao.value == 3) {
                    if(linha.habilidadeComentada != null) {
                        if (!empty(linha.habilidadeComentada)) {
                            gridHabilidades.addSelectedItens(linha.codigo);
                        }
                    }
                } else {
                    if (!empty(linha.habilidadeComentada)) {
                        gridHabilidades.addSelectedItens(linha.codigo);
                    }
                }
            });
        });

        gridHabilidades.setEvent('onafterrenderrows', function() {
            selecionaLinha();
        });
        gridHabilidades.getGrid().selectAll = () => {
            return;
        }
    };

    const criaHabilidadeComentada = (habilidade) => {
        if (isEnsinoFundamental()) {
            return {
                id: habilidade.id,
                disciplina: habilidade.disciplina,
                etapas: habilidade.etapas,
                codigo: habilidade.codigo,
                unidadeTematica: habilidade.unidadeTematica,
                objetoConhecimento: habilidade.objetoConhecimento,
                habilidade: habilidade.habilidade,
                habilidadeReferencial: []
            }
        }
        return {
                id: habilidade.id,
                disciplina: habilidade.disciplina,
                faixaEtaria: habilidade.faixaEtaria,
                codigo: habilidade.codigo,
                habilidade: habilidade.habilidade,
                habilidadeReferencial: []
        }
    }

    const atualizaHabilidade = () => {
        var codigo = ""
        if( configuracao.value == 3) {
            codigo = inputCodigoHiden.value;

        } else {
            codigo = inputCodigo.value;

        }
        let habilidade = collectionHabilidades.get(codigo);
        if (!habilidade.habilidadeComentada) {
            habilidade.habilidadeComentada = criaHabilidadeComentada(habilidade);
        }
        txtHabilidadeEditada.value = removeNovaLinha(txtHabilidadeEditada.value);
        habilidade.habilidade_editada = txtHabilidadeEditada.value;
        habilidade.habilidadeComentada.habilidade = txtHabilidadeEditada.value;
        if (configuracao.value == 3) {
            habilidade.codigo = inputCodigo.value;
            habilidade.ID = inputCodigo.value;
            habilidade.habilidade = txtHabilidadeEditada.value.replace(`(${inputCodigoHiden.value})`, `(${inputCodigo.value})`);
            habilidade.editar = inputCodigoHiden.value;
        }
        if (isEnsinoFundamental()) {
            let etapasSelecionada = buscaEtapasSelecionadas();

            habilidade.etapas_editada = etapasSelecionada;
            habilidade.habilidadeComentada.etapas = etapasSelecionada;
        }
        if (configuracao.value != 3) {
            verificaSeHabilidadeFoiEditada(habilidade);
        }
        gridHabilidades.reload();
        closeWindowAux(windowEdicao);
    }

    const estilizaLinha = (linha) => {
        document.getElementById(linha.sId).className = 'normal';

        if (linha.isSelected) {
            linha.addClassName('marcado');
            if (!empty(linha.itemCollection.habilidadeComentada) && linha.itemCollection.editada
            ) {
                linha.addClassName('info');
            }

            if (!empty(linha.itemCollection.habilidadeComentada) &&
                !empty(linha.itemCollection.habilidadeComentada.habilidadeReferencial)) {
                linha.addClassName('sucess');
            }


            if (configuracao.value == 3) {
                if (!empty(linha.itemCollection.habilidadeComentada)) {
                    linha.addClassName('sucess');
                }
            }
        }
    };

    const atualizarLinha = (linhaGrid) => {
        let itemCollection = linhaGrid.itemCollection
        estilizaLinha(linhaGrid);
        if (linhaGrid.isSelected) {
            if (empty(itemCollection.habilidadeComentada)) {
                itemCollection.habilidadeComentada = criaHabilidadeComentada(itemCollection);
            }
        } else {
            itemCollection.habilidade_editada = itemCollection.habilidade;
            itemCollection.habilidadeComentada = null;
            itemCollection.editada = false;
        }
    };

    const selecionaLinha = () => {
        const linhasGrid = gridHabilidades.getGrid().aRows;
        for (let linha of linhasGrid) {
            linha.aCells[2].addClassName('elipse');
            let elementoTR = document.getElementById(linha.sId);

            elementoTR.firstChild.firstChild.addEventListener('click', () => {
                atualizarLinha(linha);
                gridHabilidades.reload();
            });
            estilizaLinha(linha);

            if (configuracao.value === 1) {
                document.getElementById(linha.aCells[0].getId()).firstChild.setAttribute('disabled', 'disabled');
            }
        }
    };



    const initEducacaoInfantil = () => {
        const formData = new FormData();
        formData.append('acao', 'buscarFiltrosEnsinoInfantil');
        HttpClient.post('edu4_manutencao_bncc.RPC.php', {body: formData}).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            filtros = response.filtros;
            montaFiltrosCamposExperiencia();
        });
    };

    /**
     * com base no retorno dos filtros monta a lógica dos select para buscar as habilidades
     */
    const montaFiltrosCamposExperiencia = () => {
        resetSelect(cboCamposExperiencia, 'Selecione');
        resetSelect(cboFaixaEtaria, 'Selecione');

        filtros.map((camposExperiencia) => {
            cboCamposExperiencia.add(new Option(camposExperiencia.nome));
        });

        cboCamposExperiencia.addEventListener('change', () => {
            resetSelect(cboFaixaEtaria, 'Selecione');
            if (cboCamposExperiencia === '') {
                return;
            }
            const camposExperiencia = filtraArray(filtros, cboCamposExperiencia);
            camposExperiencia.faixas_etaria.map((faixa_etaria) => {
                cboFaixaEtaria.add(new Option(faixa_etaria.nome));
            });
        });
    };

    const montaFiltrosCamposExperienciaModal = () => {
        resetSelect(cboCamposExperiencia, 'Selecione');
        resetSelect(cboFaixaEtaria, 'Selecione');

        filtros.map((camposExperiencia) => {
            cboCamposExperiencia.add(new Option(camposExperiencia.nome));
        });

        cboCamposExperiencia.addEventListener('change', () => {
            resetSelect(cboFaixaEtaria, 'Selecione');
            if (cboCamposExperiencia === '') {
                return;
            }
            const camposExperiencia = filtraArray(filtros, cboCamposExperiencia);
            camposExperiencia.faixas_etaria.map((faixa_etaria) => {
                cboFaixaEtaria.add(new Option(faixa_etaria.nome));
            });
        });
    };

    const initEnsinoFundamental = () => {
        const formData = new FormData();
        formData.append('acao', 'buscarFiltrosEnsinoFundamental');
        formData.append('opcao', configuracao.value);
        formData.append("ano", cboAno.value);
        HttpClient.post('edu4_manutencao_bncc.RPC.php', {body: formData}).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            filtros = response.filtros;
            montaFiltrosEnsinoFundamental();
        });
    };

    const montaFiltrosEnsinoFundamental = () => {
        resetSelect(cboDisciplina, 'Selecione');
        resetSelect(cboUnidadeTematica, 'Selecione');
        resetSelect(cboObjConhecimento, 'Selecione');
        resetSelect(cboObjetoConhecimento, 'Selecione');
        filtros.map((filtro) => {
            cboDisciplina.options.add(new Option(filtro.nome));
        });

        cboDisciplina.addEventListener('change', () => {
            collectionEditaObj.clear();
            resetSelect(cboUnidadeTematica, 'Selecione');
            resetSelect(cboObjConhecimento, 'Selecione');
            resetSelect(cboObjetoConhecimento, 'Selecione')
            if (cboDisciplina.value === '') {
                editaObjConhecimento.style.display = "none";
                return;
            }

            const disciplina = filtraArray(filtros, cboDisciplina);
            disciplina.unidades_tematicas.map((unidadeTematica) => {
                cboUnidadeTematica.add(new Option(unidadeTematica.nome));
            });
        });

        cboUnidadeTematica.addEventListener('change', () => {
            collectionEditaObj.clear();
            editaObjConhecimento.style.display = "block";
            resetSelect(cboObjConhecimento, 'Selecione');
            resetSelect(cboObjetoConhecimento, 'Selecione');
            if (cboUnidadeTematica.value === '') {
                editaObjConhecimento.style.display = "none";
                return;
            }

            const disciplina = filtraArray(filtros, cboDisciplina);
            const unidadeTematica = filtraArray(disciplina.unidades_tematicas, cboUnidadeTematica);
            if (configuracao.value == 3) {
                setCollectionObjetosConhecimento(cboUnidadeTematica.value, cboDisciplina.value);
            }
            unidadeTematica.objetos.map((objeto) => {
                cboObjConhecimento.add(new Option(objeto.nome, objeto.nome));
                cboObjetoConhecimento.add(new Option(objeto.nome, objeto.nome));
            });
        });
    };
    const filtraArray = (dados, elementoComparacao) => {
        return dados.filter((filtro) => {
            return filtro.nome === elementoComparacao.value;
        }).shift();
    }

    const resetSelect = (select, label) => {
        select.options.length = 0;
        select.add(new Option(label, ''));
    }

    btnBuscarHabilidades.addEventListener('click', () => {
        autalizaGridHabilidades();
    });

    const setCollectionObjetosConhecimento = async (unidadeTematica, disciplina) => {
        collectionEditaObj.clear();
        const formData = new FormData();
        formData.append("acao", 'buscarFiltrosEnsinoFundamental');
        formData.append("ano", cboAno.value);
        await HttpClient.post('edu4_manutencao_bncc.RPC.php', {body: formData}).then(response => {
            response.filtros.map(disc => {
                if (disc.nome == disciplina) {
                    disc.unidades_tematicas.map(undTematica => {
                       if(undTematica.nome == unidadeTematica) {
                            undTematica.objetos.map(objt => {
                                collectionEditaObj.add(objt);
                            })
                       }
                    })
                }
            });
        });
    }

    const autalizaGridHabilidades = () => {
        const parametros = {};
        if (cboEnsino.value === 'EI') {
            if (cboCamposExperiencia.value === '') {
                alert ('Informe um campo de experiência.');
                return ;
            }
            parametros.body = new FormData(frmEducacaoInfantil);
            parametros.body.append('acao', 'buscarHabilidadesEI');
        } else {
            if (cboDisciplina.value === '') {
                alert ('Informe uma Disciplina.');
                return ;
            }
            parametros.body = new FormData(frmEnsinoFundamental);
            parametros.body.append('acao', 'buscarHabilidadesEF');
            if( configuracao.value == 3 ) {
                parametros.body.append('referencial', true);
            }
        }

        parametros.body.append('ano', cboAno.value);

        HttpClient.post('edu4_manutencao_bncc.RPC.php', parametros).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            collectionHabilidades.clear();
            response.habilidades.map((habilidade) => {

                habilidade.habilidade_editada = habilidade.habilidade;
                habilidade.etapas_editada = habilidade.etapas;

                if (habilidade.habilidadeComentada) {
                    habilidade.habilidade_editada = habilidade.habilidadeComentada.habilidade;
                    habilidade.etapas_editada = habilidade.habilidadeComentada.etapa;
                }

                if (configuracao.value == 3) {
                    habilidade.habilidade_editada = habilidade.habilidade;
                    habilidade.etapas_editada = habilidade.etapa;
                }


                if (configuracao.value != 3) {
                    verificaSeHabilidadeFoiEditada(habilidade);
                }
                collectionHabilidades.add(habilidade);
            });
            gridHabilidades.reload();
        });
    }

    btnSalvar.addEventListener('click', () => {
        const formData = new FormData();
        if (configuracao.value == 3 && isEnsinoFundamental()) {
            formData.append("acao", `salvarHabilidadeEF`);
        } else {
            formData.append("acao", `salvarHabilidades${cboEnsino.value}`);
        }
        collectionHabilidades.get().map((habilidade) => {
            formData.append("habilidades[]", JSON.stringify(habilidade.build()));
        });
        formData.append("ano", cboAno.value);
        HttpClient.post('edu4_manutencao_bncc.RPC.php', {body: formData}).then((response) => {
            alert(response.mensagem);
        });
    });

    window.addEventListener('load', () => {
        const formData = new FormData();
        formData.append('acao', 'buscarConfiguracao');
        HttpClient.post('edu4_manutencao_bncc.RPC.php', {body: formData}).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }

            ctnAbaManutencao.style.display = 'block';
            containerModalEdicao.style.display = 'block';
            containerModalReferencial.style.display = 'block';
            configuracao = response.configuracao;

            if (configuracao.value === 1) {
                btnSalvar.setAttribute('disabled', 'disabled');
            }

            response.anos.map(ano => {
                let selected = false;
                if (ano == new Date().getFullYear()){
                    selected = true;
                }
                cboAno.add(new Option(ano, ano, false, selected));
            });
        });
    });

    const verificaSeHabilidadeFoiEditada = (item) => {
        item.editada = false;
        if (configuracao.value === 2 && item.habilidadeComentada) {
            if (isEnsinoFundamental()) {
                if (item.habilidade != item.habilidadeComentada.habilidade ||
                    item.etapas != item.habilidadeComentada.etapas) {
                    item.editada = true;
                }
            } else {
                item.editada = item.habilidade != item.habilidadeComentada.habilidade;
            }
        }

        return item;
    };

    const criarCheckboxEtapas = (linhaTable, colunaEtapas, linha, original) => {
        if (original) {
            if (configuracao.value == 3) {
                if(linha.habilidadeComentada != null) {
                    linha.habilidadeComentada.etapas = linha.etapas;
                    linha.etapas_editada = linha.etapas;
                }
            } else {
                linha.habilidadeComentada.etapas = linha.etapas;
                linha.etapas_editada = linha.etapas;

            }
        }
        if (configuracao.value == 2) {
            if(linha.habilidadeComentada != null) {
                linha.habilidadeComentada.etapas = linha.etapas;
            }
            linha.etapas_editada = linha.etapas;
        }

        if (configuracao.value == 3) {
            if(linha.habilidadeComentada != null) {
                linha.habilidadeComentada.etapas = linha.etapa;
            }
            linha.etapas_editada = linha.etapa;
            linha.etapas = linha.etapa;
        }

        const etapas = linha.etapas.replaceAll(' ', '').split(',');
        let etapasComentada = [];
        if (configuracao.value == 3) {
            if(linha.habilidadeComentada != null || linha.habilidadeComentada != "") {
                if (linha.habilidadeComentada) {
                    etapasComentada = linha.habilidadeComentada.etapas.replaceAll(' ', '').split(',');
                }
            }
        } else {
            if (linha.habilidadeComentada) {
                etapasComentada = linha.habilidadeComentada.etapas.replaceAll(' ', '').split(',');
            }
        }

        linhaTable.style.display = 'table-row';
        colunaEtapas.innerHTML = '';

        etapas.map((etapa) => {
            let inputCheck = document.createElement('input');
            inputCheck.type = 'checkbox';
            inputCheck.value = etapa;
            inputCheck.className = 'checkEtapa';

            inputCheck.checked = etapasComentada.includes(etapa)

            let inputLabel = document.createElement('label');
            inputLabel.innerHTML = etapa;

            colunaEtapas.append(inputCheck);
            colunaEtapas.append(inputLabel);
        });
    };

    const isEnsinoFundamental = () => {
        return cboEnsino.value === 'EF';
    }

    const buscaEtapasSelecionadas = () => {
        const elementos = Array.from(document.getElementsByClassName('checkEtapa'));
        return elementos.filter((elemento) => {
            if (elemento.checked) {
                return true;
            }
        }).map((elemento) => {
            return elemento.value;
        }).join(', ');
    }

    const montaGridReferencial = () => {
        let configuracaoLinha = configuracaoLinhas();

        gridHabilidadesReferencial = new DatagridCollection(collectionHabilidadesReferencial).configure({
            order: false,
            height: 200
        });

        gridHabilidadesReferencial.addColumn('codigoReferencial', {label: "Código", width: '15%', align: 'center'});
        gridHabilidadesReferencial.addColumn('habilidade',
                {label: "Habilidade / Objetos de Conhecimento", 'width': configuracaoLinha.widthHabilidade});

        if (isEnsinoFundamental()) {
            gridHabilidadesReferencial.addColumn('etapa', {label: "Etapa(s) / Ano(s)", 'width': '15%'});
        }

        gridHabilidadesReferencial.addAction('Excluir', 'Excluir', (event, linha) => {
            if (!confirm("Confirma exclusão?")) {
                return;
            }

            const formData = new FormData();
            formData.append('acao', 'removerReferencial');
            formData.append('codigo', linha.id);
            HttpClient.post('edu4_manutencao_bncc.RPC.php', {body: formData}).then(response => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }

                collectionHabilidadesReferencial.remove(linha.ID);
                gridHabilidadesReferencial.reload();
            });
        }, true, 'fa-trash-alt');

        gridHabilidadesReferencial.addAction('Informação', 'Informação', (event, linha) => {
            alert(linha.habilidade);
        }, true, 'fa-info-circle');

        gridHabilidadesReferencial.setEvent('onafterrenderrows', function() {
            const linhasGridReferencial = gridHabilidadesReferencial.getGrid().aRows;
            for (let linhaReferencial of linhasGridReferencial) {
                linhaReferencial.aCells[1].addClassName('elipse');
            }
            selecionaLinha();
        });

        gridHabilidadesReferencial.show(ctnGridReferencial);
    }

    btnFechar.onclick = () => {
        fechaModalReferencial();
    };

    btnAdicionar.onclick = () => {
        if (empty(inputCodigoReferencial.value)) {
            alert("Informe o código da Habilidade.");
            return;
        }
        if (empty(txtHabilidadeReferencial.value)) {
            alert("Informe o texto da Habilidade.");
            return;
        }

        let novaHabilidade = {
            id: null,
            codigoHabilidade: inputCodigoHabilidade.value,
            codigo: inputCodigoReferencial.value,
            codigoReferencial: inputCodigoReferencial.value,
            habilidade: removeNovaLinha(txtHabilidadeReferencial.value),
            etapa: buscaEtapasSelecionadas(),
            ensino: cboEnsino.value,
            ano: cboAno.value,
            objetoConhecimento: cboObjetoConhecimento.value
        };
        
        const formData = new FormData();
        formData.append('acao', 'adicionarReferencial');
        formData.append('novaHabilidade', JSON.stringify(novaHabilidade));

        HttpClient.post('edu4_manutencao_bncc.RPC.php', {body: formData}).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            if (configuracao.value == 3) {
                response.habilidadeReferencial.habilidadeComentada = [];
            }
            collectionHabilidadesReferencial.add(response.habilidadeReferencial);
            gridHabilidadesReferencial.reload();
            inputCodigoReferencial.value = '';
            txtHabilidadeReferencial.value = '';
        });
    };

    const fechaModalReferencial = () => {
            let habilidadesReferencial = collectionHabilidadesReferencial.get().map((habilidadeItem) => {
                const habilidadeReferencial = habilidadeItem.build();
                return {...habilidadeReferencial, id: habilidadeItem.id};
            });

            var habilidade = collectionHabilidades.get(inputCodigoHabilidade.value);
            if (configuracao.value == 3) {
                habilidade.habilidadeComentada = habilidadesReferencial
            } else {
                habilidade.habilidadeComentada.habilidadeReferencial = habilidadesReferencial
            }

            inputCodigoReferencial.value = '';
            inputCodigoHabilidade.value = '';
            txtHabilidadeReferencial.value = '';
            linhaEtapaReferencial.style.display = 'none';
            ctnEtapasReferencial.innerHTML = '';
            gridHabilidadesReferencial.clear();
            closeWindowAux(windowReferencial, 'msgBoardHabilidades');
            gridHabilidades.reload();

    }

    const removeNovaLinha = (texto) => {
        return texto.replace(/(\n|\r)+/g ," ");
    }

    inputToUpperCase(inputCodigoReferencial);
</script>
