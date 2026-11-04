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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('libs/db_app.utils.php');
require_once modification('libs/db_utils.php');
require_once modification('dbforms/db_funcoes.php');

?>
<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="iso-8859-1">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>DBSeller Serviços de Informática Ltda</title>
        <link href="estilos.css" rel="stylesheet" type="text/css">
        <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
        <script src="scripts/scripts.js" rel="script" type="text/javascript"></script>
        <script src="scripts/prototype.js" rel="script" type="text/javascript"></script>
        <script src="scripts/object.js" rel="script" type="text/javascript"></script>
        <script src="scripts/widgets/Input/DBInput.widget.js" rel="script" type="text/javascript"></script>
        <script src="scripts/widgets/DBInputHora.widget.js" rel="script" type="text/javascript"></script>
        <script src="scripts/widgets/Input/DBInputCep.widget.js" rel="script" type="text/javascript"></script>
        <script src="scripts/widgets/Input/DBInputCNPJ.js" rel="script" type="text/javascript"></script>
        <script src="scripts/widgets/Input/DBInputCpf.widget.js" rel="script" type="text/javascript"></script>
        <script src="scripts/widgets/Input/DBInputDate.widget.js" rel="script" type="text/javascript"></script>
        <script src="scripts/widgets/Input/DBInputInteger.widget.js" rel="script" type="text/javascript"></script>
        <script src="scripts/widgets/Input/DBInputTelefone.widget.js" rel="script" type="text/javascript"></script>
        <script src="scripts/widgets/Input/DBInputValor.widget.js" rel="script" type="text/javascript"></script>
        <script src="scripts/widgets/Input/DBInputCheckboxRadio.widget.js" rel="script" type="text/javascript"></script>
        <script src="scripts/widgets/Input/DBCheckBox.widget.js" rel="script" type="text/javascript"></script>
        <script src="scripts/widgets/Input/DBRadio.widget.js" rel="script" type="text/javascript"></script>
        <script src="scripts/widgets/Collection.widget.js" rel="script" type="text/javascript"></script>
        <script src="scripts/widgets/DBLancador.widget.js" rel="script" type="text/javascript"></script>
        <script src="scripts/classes/avaliacao/DBViewFormulario.classe.js" rel="script" type="text/javascript"></script>
        <script src="scripts/classes/avaliacao/DBViewGrupoPerguntas.classe.js" rel="script" type="text/javascript"></script>
        <script src="scripts/classes/avaliacao/DBViewPergunta.classe.js" rel="script" type="text/javascript"></script>
        <script src="scripts/classes/avaliacao/DBViewResposta.classe.js" rel="script" type="text/javascript"></script>
        <script src="scripts/AjaxRequest.js" rel="script" type="text/javascript"></script>
        <script src="scripts/classes/http/http.js" rel="script" type="text/javascript"></script>
        <script src="scripts/classes/DBViewFormularioFolha/CompetenciaFolha.js" rel="script"
            type="text/javascript"></script>
        <style>
            #competenciaTr input[type="text"] {
                width: inherit;
            }
        </style>
    </head>
    <body>
        <form class="container">
            <fieldset>
                <legend>Carga de Rubricas de Adiantamento de 13º</legend>
                <table class='form-container'>
                    <tr id="tr_empregador" class="d-none">
                        <td>
                            <label for="empregador">Empregador:</label>
                        </td>
                        <td>
                            <select name="empregador" id="empregador"></select>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input type="button" id="processarDadosForcados" value="Processamento de Dados">
                <div>
                    <br><br>
                    <p style="text-align: left"><strong>Nota:</strong></p>
                    <p style="text-align: left"><strong>A rotina irá clonar os preenchimentos das rubricas de 13º para gerar</strong> </p>
                    <p style="text-align: left"><strong>as rubricas de adiantamento de 13º, conforme o layout do eSocial.</strong></p>
                </div>

            <script rel="script" type="text/javascript">
                var text = '';

                (() => {
                    const trEmpregador = document.getElementById('tr_empregador');
                    const selectEmpregador = document.getElementById('empregador');
                    const buttonProcessar = document.getElementById('processarDadosForcados');

                    const validar = () => {
                        return new Promise((resolve, reject) => {
                        if (selectEmpregador.value === '') {
                            return reject('O campo "Empregador" é obrigatório.');
                        }
                            resolve();
                        });
                    };

                    const clickButtonProcessar = () => {
                        validar().then(() => {
                            const formData = new FormData();
                            const parametros = {
                                exec: 'gerarCargaAdiantamento',
                                cgm: selectEmpregador.value
                            };
                        formData.append('json', JSON.stringify(parametros));

                        HttpClient.post('eso_cargapreenchimentos.RPC.php', {
                            body: formData
                        }).then(response => {
                            alert(response.sMessage);
                        });
                    }).catch(e => alert(e));
                };

                const adicionarListeners = () => {
                    buttonProcessar.addEventListener('click', clickButtonProcessar);
                };


                const inicializar = () => {

                    const formData = new FormData();
                    formData.append('acao', 'inicializar');
                    formData.append('integracao', 2);

                    HttpClient.post('sped02_preenchimento.RPC.php', {
                        body: formData
                    }).then(response => {
                        if (response.erro) {
                            throw response.mensagem;
                        }

                        response.empregadores.map((empregadorOption, chave) => {
                            const selecionado = chave === 0;

                            selectEmpregador.add(
                                new Option(empregadorOption.nome, empregadorOption.cgm),
                                selecionado,
                                selecionado
                            );
                        });
                        trEmpregador.classList.remove('d-none');
                    }).then(adicionarListeners).catch(mensagem => alert(mensagem));
                };
                inicializar();
            })();
        </script>
    </body>
</html>
