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
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_protelac_classe.php"));
include(modification("dbforms/db_funcoes.php"));
use App\Domain\RecursosHumanos\Pessoal\Repository\Helper\CompetenciaHelper;

$competencia = CompetenciaHelper::get();
?>
<html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/classes/DBViewFormularioFolha/CompetenciaFolha.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
        <link href="estilos.css" rel="stylesheet" type="text/css">
    </head>

    <body onload="preencheCompetencia()">
        <div class="container">
            <fieldset>
                <form action="" name="form1" id="form1">
                    <legend>Processar Contagem de Tempo</legend>
                    <table cellpadding="0" cellspacing="0" class="form-container">
                        <tr>
                            <td width="125px">
                                <label>Competência: </label>
                            </td>
                            <td>
                                <input type="text" id="ano" name="ano" style="width: 50px; margin-left: -17px;" placeholder="Ano" disabled/>
                                /
                                <input type="text" id="mes" name="mes" style="width: 50px; margin-left: 1px;" placeholder="Mês" disabled/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php db_ancora('Rubrica de Triênio:',"pesquisarubrica(true)",2);?>
                            </td>
                            <td>
                                <input type="text" id="rubrica" name="rubrica" style="width: 50px; margin-left: -17px;" placeholder="Código" onchange="pesquisarubrica(false)"/>
                                <input type="text" id="descricao" name="descricao" style="width: 250px; margin-left: 1px;" placeholder="" disabled/>
                            </td>
                        </tr>

                    </table>
                </form>
                <input type="button" id="btn_processar" onclick="js_processar()" value="Processar" />
            </fieldset>
            <div id="modalRelatorio" class="container">
                <fieldset>
                    <table class="form-container">
                        <tr>
                            <td>
                                <label for="tamanho">
                                    Selecione o tipo de Relatório: 
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="opcao" id="tiporelatorio">
                                    <option value="todos" selected>Todos</option>
                                    <option value="alterados">Somente Alterados na Competência atual</option>
                                </select>
                            </td>
                        </tr>
                        </tr>
                    </table>
                </fieldset>
                <button class="btn btn-light" id="geraRelatorio">
                    <i class="fa fa-file-csv" aria-hidden="true"></i>
                    Gerar
                </button>
            </div>
        </div>

        <script type="text/javascript">
            const sPathRPC  = 'rec1_protelacoes.RPC.php';
            var relatorio = [];

            function js_processar()
            {
                if (document.getElementById('rubrica').value == '') {
                    alert("Rubrica não informada");
                    return false;
                }
                var oParametro = {
                    exec : 'processar',
                    ano : document.getElementById('ano').value,
                    mes: document.getElementById('mes').value,
                    rubrica: document.getElementById('rubrica').value,
                };

                new AjaxRequest(
                    sPathRPC,
                    oParametro,
                    function (oRetorno, lErro) {
                        if (lErro) {
                            alert("Erro ao executar o Processamento das Protelações.");
                        } else {
                            relatorio = oRetorno.dados;
                            if (confirm("Processamento das Protelações executada com sucesso!\nGostaria de emitir o Relatório dos dados processados do triênio?")) {
                                windowRelatorio.show(0, 0, true);

                            };
                        }
                    }
                ).setMessage('Aguarde, processando...').execute();
            }

            function preencheCompetencia() {
                document.getElementById('ano').value = <?= $competencia->getAno();?>;
                document.getElementById('mes').value = '<?= $competencia->getMes();?>';
            } 

            function emiteRelatorio()
            {
                var dados = relatorio;
                var opcao = document.getElementById('tiporelatorio').value;

                if (opcao == "alterados") {
                    dados = [];
                    relatorio.forEach(function(dado) {
                        if (dado.alterado === true) {
                            dados.push(dado);
                        }
                    });
                }

                var dadosCsv = [
                    [
                        "Matrícula",
                        "Nome",
                        "Cargo",
                        "Lotação",
                        "Data de Admissão",
                        "Data do Triênio antes do Processamento",
                        "Data do Triênio depois do Processamento",
                        "Percentual Atual",
                        "Data previsão próximo Triênio",
                        "Tempo Total (anos)",
                        "Afastado No Momento"
                    ]
                ];

                dados.forEach(function(dado) {
                    var dadoLinha = [
                        dado.matricula,
                        dado.nome,
                        dado.cargo,
                        dado.lotacao,
                        dado.dataadmissao,
                        dado.datatrienionova,
                        dado.datatrienioatual,
                        dado.percentualtrienioatual,
                        dado.dataprevisaotrienio,
                        dado.totaltempo, 
                        dado.afastado
                    ];
                    dadosCsv.push(dadoLinha);
                });
                let csvContent = "data:text/csv;charset=utf-8," + dadosCsv.map(e => e.join(";")).join("\n");

                var encodedUri = encodeURI(csvContent);
                window.open(encodedUri);

            }

            function pesquisarubrica(mostra)
            {
                if (mostra == true) {
                    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rubricas','func_rhrubricas.php?funcao_js=parent.mostrarubrica011|rh27_rubric|rh27_descr&instit=<?=(db_getsession("DB_instit"))?>','Pesquisa',true);
                } else {
                    if (document.form1.rubrica.value != '') { 
                        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rubricas01','func_rhrubricas.php?pesquisa_chave=' + document.form1.rubrica.value + '&funcao_js=parent.mostrarubrica01&instit=<?=(db_getsession("DB_instit"))?>','Pesquisa',false);
                    } else {
                        document.form1.descricao.value = '';
                    }
                }
            }

            function mostrarubrica01(descricao, erro)
            {
                document.form1.descricao.value = descricao; 
                if (erro == true) { 
                    document.form1.rubrica.focus(); 
                    document.form1.rubrica.value = ''; 
                }
            }

            function mostrarubrica011(codigo, descricao)
            {
                document.form1.rubrica.value = codigo;
                document.form1.descricao.value = descricao;
                db_iframe_rubricas.hide();
            }


            const modalRelatorio = document.getElementById('modalRelatorio');
            const hideWindowRelatorio = () => {                    
                windowRelatorio.destroy();
            }

            var windowRelatorio = new windowAux('windowRelatorio', 'Emitir Relatório', 700, 400);
            windowRelatorio.setContent(modalRelatorio);
            windowRelatorio.allowCloseWithEsc(true);
            windowRelatorio.setShutDownFunction(function () {
                hideWindowRelatorio();
            });
            const btnRelatorio = document.getElementById('geraRelatorio');
            btnRelatorio.on('click', () => {
                emiteRelatorio();
            });
        </script>
        <?php db_menu(); ?>
    </body>
</html>
