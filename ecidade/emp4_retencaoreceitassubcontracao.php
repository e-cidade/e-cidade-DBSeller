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

?>
<!Doctype html>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/session.js"></script>

    <!-- bootstrap table -->
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
</head>
<body>
    <form name='form1' action='javascript:;' id="form1">
        <div class="container">
            <fieldset>
                <legend>Retenção Principal</legend>
                <table class="form-container">
                    <tr>
                        <td><b>Valor: </b></td>
                        <td><input type="text" name="valorRetencaoPrincipal" id="valorRetencaoPrincipal" disabled></td>
                    </tr>
                    <tr>
                        <td><b>Saldo: </b></td>
                        <td><input type="text" name="saldoRetencao" id="saldoRetencao" disabled></td>
                    </tr>
                </table>
            </fieldset>
            <fieldset style="width: 40%">
                <legend>Dados</legend>

                <table class="form-container">
                    <input type="hidden" name="163_sequencial" id="e163_sequencial" value="">
                    <input type="hidden" name="index" id="index" value="">

                    <!-- subcontratado -->
                    <tr>
                        <td title="e163_numcgm">
                            <label for="ancoraCgm">
                                <a href="#" id="ancoraCgm">Subcontratado: </a>
                            </label>
                        </td>
                        <td>
                            <input type="text" name="z01_numcgm" id="z01_numcgm">
                            <input type="text" name="z01_nome" id="z01_nome">
                        </td>
                    </tr>

                    <!-- tipo retencao -->
                    <tr>
                        <td title="e163_retencaotiporec">
                            <label for="ancoraRetencao">
                                <a href="#" id="ancoraRetencao">Retenção: </a>
                            </label>
                        </td>
                        <td>
                            <input type="text" name="e21_sequencial" id="e21_sequencial">
                            <input type="text" name="e21_descricao" id="e21_descricao">
                        </td>
                    </tr>

                    <!-- valor base -->
                    <tr>
                        <td><label for="valor">Valor Base: </label></td>
                        <td><input type="text" name="valorbase" id="valorbase" onkeyup="js_ValidaCampos(this, 4,'(Valor)','f','f',event);"></td>
                    </tr>

                    <!-- valor -->
                    <tr>
                        <td><label for="valor">Valor Retenção: </label></td>
                        <td><input type="text" name="valor" id="valor" onkeyup="js_ValidaCampos(this, 4,'(Valor)','f','f',event);"></td>
                    </tr>
                </table>
            </fieldset>
            <button name="Adicionar" id="actionButton" onClick="addSubcontratacao()">Salvar</button>
        </div>
        <br>
    </form>
    <div style="width: 70%" class="subcontainer">
        <fieldset>
            <legend>Subcontratações</legend>
            <table id="gridSubcontratacoes" class="table table-sm" data-height="250" data-virtual-scroll="true" style="width: 100%;">
            </table>
        </fieldset>
    </div>
    <script src="assets/jquery/jquery-3.5.1.min.js"></script>
    <script src="assets/bootstrap-table/bootstrap-table.min.js"></script>
    <script src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
    <script>
        $.noConflict();

        /**
         * Loockup do cgm
         * */
        const lookUpCgm = new DBLookUp($('ancoraCgm'), $('z01_numcgm'), $('z01_nome'), {
            'sArquivo': 'func_cgm.php',
            'sLabel': 'Pesquisar Cgm',
            'sObjetoLookUp': "db_iframe_cgm"
        });

        /**
         * Loockup Retencao
         * */
        const lookUpRetencao = new DBLookUp($('ancoraRetencao'), $('e21_sequencial'), $('e21_descricao'), {
            'sArquivo': 'func_retencaotiporec.php',
            'sLabel': 'Pesquisar Retenção',
            'sObjetoLookUp': "db_iframe_retencao",
            'aParametrosAdicionais': ['tipoCalc=1,2']
        });

        /**
         * El grid
         */
        const gridSubcontratacoes = jQuery('#gridSubcontratacoes');

        /**
         * Entrypoint
         */
        var subcontratacoes = Array();
        var API_URL = null;

        PHPSession.loadData().then(async () => {
            API_URL = PHPSession.requestApi;
        });

        getSession();
        buildGrid();
        updateSaldo();

        // funcao para salvar subcontratacao
        function addSubcontratacao() {

            // data
            const sequencial      = $F('e163_sequencial');
            const numcgm          = $F('z01_numcgm');
            const retencaotiporec = $F('e21_sequencial');
            const valor           = Number($F('valor')).toFixed(2);
            const valorbase       = Number($F('valorbase')).toFixed(2);
            const subcontratado   = $F('z01_nome');
            const retencao        = $F('e21_descricao');
            const id              = numcgm + '-' + retencaotiporec;
            const index           = $F('index');

            let data = {
                'e163_sequencial': sequencial,
                'e163_numcgm': numcgm,
                'e163_retencaotiporec': retencaotiporec,
                'e163_valor': valor,
                'e163_valorbase': valorbase,
                'subcontratado': subcontratado,
                'retencao': retencao,
                'id': id,
                'index': index
            }

            if (!validation(data)) {
                return false;
            }

            // update em base
            if (sequencial) {
                return updateSubcontratacao(data);
            }

            // update - insert em session
            if (index) {
                subcontratacoes[Number(index)] = data;
                alert('Retenção Alterada com sucesso.');
            } else {
                subcontratacoes.push(data);
                alert('Retenção Lançada com sucesso.');
            }

            putSession();
            buildGrid();
            clearForm();
            updateSaldo();
        }

        // funcao para update da subcontratacao
        async function updateSubcontratacao(data) {
            const url = `${API_URL}/financeiro/empenho/retencaosubcontratacao/update`;
            const formData = new FormData;

            formData.append('e163_sequencial', data.e163_sequencial);
            formData.append('e163_numcgm', data.e163_numcgm);
            formData.append('e163_retencaotiporec', data.e163_retencaotiporec);
            formData.append('e163_valor', data.e163_valor);
            formData.append('e163_valorbase', data.e163_valorbase);

            PHPSession.appendFormData(formData);

            HttpClient
            .post(url, {body: formData})
            .then(response => {
                if (response.error) {
                    alert(response.message);
                    return false;
                }

                subcontratacoes[Number(data.index)] = data;
                alert('Retenção Alterada com sucesso.');

                putSession();
                buildGrid();
                clearForm();
                updateSaldo();
            })
            .catch(error => {
                alert('Erro ao excluir.')
                console.error(error)
            });
        }

        // coloca os dados em sessao
        function putSession() {
            sessionStorage.removeItem('subcontratacoes');
            sessionStorage.setItem('subcontratacoes', JSON.stringify(subcontratacoes));
        }

        // recupera os dados da sessao
        function getSession() {
            if (sessionStorage.getItem('subcontratacoes')) {
                let received = sessionStorage.getItem('subcontratacoes');
                subcontratacoes = JSON.parse(received);
            } else {
                subcontratacoes = Array();
            }
        }

        // pupula o formulario para update
        function editSubcontratacao(index) {
            subcontratacao = subcontratacoes[index];

            $('e163_sequencial').setValue(subcontratacao.e163_sequencial);
            $('z01_numcgm').setValue(subcontratacao.e163_numcgm);
            $('e21_sequencial').setValue(subcontratacao.e163_retencaotiporec);
            $('valor').setValue(subcontratacao.e163_valor);
            $('valorbase').setValue(subcontratacao.e163_valorbase);

            $('z01_nome').setValue(subcontratacao.subcontratado);
            $('e21_descricao').setValue(subcontratacao.retencao);
            $('index').setValue(index);

            updateSaldo();
            saldoUpdated = (Number($F(saldoRetencao)) + Number(subcontratacao.e163_valor)).toFixed(2)
            $('saldoRetencao').setValue(saldoUpdated);
        }

        function removeSubcontratacao(index) {
            let confirmMsg = confirm('Tem certeza que deseja excluir?');
            if (!confirmMsg) {
                return false;
            }

            // delete em session
            subcontratacao = subcontratacoes[index];
            if (!subcontratacao.e163_sequencial) {
                subcontratacoes = subcontratacoes.filter((i, key) => key != index );
                putSession();
                buildGrid();
                updateSaldo();
                return;
            }

            // delete em base
            const url = `${API_URL}/financeiro/empenho/retencaosubcontratacao/delete`;
            const formData = new FormData;
            formData.append('e163_sequencial', subcontratacao.e163_sequencial);
            PHPSession.appendFormData(formData);
            HttpClient
            .post(url, {body: formData})
            .then(response => {
                if (response.error) {
                    alert(response.message);
                    return false;
                }

                subcontratacoes = subcontratacoes.filter((i, key) => key != index );
                putSession();
                buildGrid();
                updateSaldo();
            })
            .catch(error => {
                alert('Erro ao excluir.')
                console.error(error)
            });
        }

        function clearForm() {
            document.querySelector('#form1').reset();
            $('index').setValue('');
            $('e163_sequencial').setValue('');
        }

        function validation(data) {
            const valorRetencaoPrincipal = Number(parent.document.getElementById('e23_valorretencao').value).toFixed(2);
            let valorTotalSubcontratacao = subcontratacoes.reduce((a, el, i) => {
                    return a + Number(el.e163_valor).toFixed(2);
            }, data.e163_valor);

            // desconta o valor da retencao a ser alterada
            if (data.index) {
                valorTotalSubcontratacao -= subcontratacoes[data.index].e163_valor;
            }

            // verificar valor total
            if (valorTotalSubcontratacao.toFixed(2) > valorRetencaoPrincipal.toFixed(2)) {
                alert(`Somatório dos valores não pode ser superior à ${valorRetencaoPrincipal}`);
                return false;
            }

            // numcgm
            if (!data.e163_numcgm) {
                alert('Subcontratado deve ser informado.');
                return false;
            }

            // retencao
            if (!data.e163_retencaotiporec) {
                alert('Retencao deve ser informado');
                return false;
            }

            // subcontratado nome
            if (!data.subcontratado) {
                alert('Descrição do subcontrado deve ser informado.');
                return false;
            }

            // tipo retencao nome
            if (!data.retencao) {
                alert('Descrição da retenção deve ser informado.');
                return false;
            }

            //valor base
            if (!data.e163_valorbase) {
                alert('Valor base deve ser informado.');
                return false;
            }

            // verifica de subcontrado ja possui a retencao lancada
            const checkLancado = subcontratacoes.some(el => {
                return (!data.index
                    && el.e163_numcgm == data.e163_numcgm
                    && el.e163_retencaotiporec == data.e163_retencaotiporec
                );
            });

            if(checkLancado) {
                alert('Subcontratado já cadastrado para essa retenção.');
                return false;
            }

            return true;
        }

        function updateSaldo() {
            const valorRetencaoPrincipal = Number(parent.document.getElementById('e23_valorretencao').value).toFixed(2);
            const valorTotalSubcontratacao = subcontratacoes.reduce((a, el) => {
                return a + Number(el.e163_valor).toFixed(2);
            }, 0).toFixed(2);

            const saldo = (valorRetencaoPrincipal - valorTotalSubcontratacao).toFixed(2);

            $('valorRetencaoPrincipal').setValue(valorRetencaoPrincipal);
            $('saldoRetencao').setValue(saldo);
        }

        /**
         * Grid
         */
        function buildGrid() {
            let data = [];

            // header grid
            const columns = [
                {
                    field: "subcontratado",
                    title: "Subcontratado",
                    align: "center"
                },
                {
                    field: "retencao",
                    title: "Retenção",
                    align: "center"
                },
                {
                    field: "valorbase",
                    title: "Valor Base",
                    align: "center"
                },
                {
                    field: "valor",
                    title: "Valor Retenção",
                    align: "center"
                },
                {
                    field: "acao",
                    title: "Ação",
                    align: "center"
                }
            ];

            // body grid
            subcontratacoes.forEach((item, index) => {
                let row = {};

                row.subcontratado = item.subcontratado;
                row.retencao = item.retencao;
                row.valor = js_formatar(item.e163_valor, 'f');
                row.valorbase = js_formatar(item.e163_valorbase, 'f');
                row.acao = `<button onclick="editSubcontratacao('${index}')">Alterar</button> `;
                row.acao += `<button onclick="removeSubcontratacao('${index}')">Excluir</button>`;

                data.push(row);
            });

            gridSubcontratacoes.bootstrapTable('destroy');
            gridSubcontratacoes.bootstrapTable({
                data: data,
                locale: 'pt-BR',
                search: true,
                searchHighlight: true,
                columns: columns,
            });
        }
    </script>
</body>

</html>
