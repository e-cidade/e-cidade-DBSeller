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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_cfautent_classe.php"));
require_once(modification("classes/db_saltes_classe.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

$rotulocampo = new rotulocampo;

$dia_configuracao = date('d', db_getsession("DB_datausu"));
$mes_configuracao = date('m', db_getsession("DB_datausu"));
$ano_configuracao = date('Y', db_getsession("DB_datausu"));

$estiloDBSelect = "style=width:100px;";
?>

<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>    
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">

</head>

<body >
<div class = "container">
    <table width="790" height="100%"  cellspacing="0" 
        cellpadding="0"  style="margin-top: 15px;">
        <tr>
            <td align="center">
                
                <fieldset style="width: 430px;">
                <legend><b>Extrato Bancário</b></legend>    
                <form name="form1" id = "formulario" method="post" action="">
                    <table  cellspacing="3" cellpadding="0">
                        <tr>
                            <td align="right" >
                                <strong>Data inicial:</strong>
                            </td>
                            <td>
                                <?=db_inputdata(
                                    "dataInicio",
                                    $dia_configuracao,
                                    $mes_configuracao,
                                    $ano_configuracao,
                                    true,
                                    "text",
                                    1
                                );?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" ><strong>Data final:</strong></td>
                            <td>
                                <?=db_inputdata(
                                    "dataFinal",
                                    $dia_configuracao,
                                    $mes_configuracao,
                                    $ano_configuracao,
                                    true,
                                    "text",
                                    1
                                ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <fieldset>
                                <legend>Conta Bancária</legend>
                                <table align="center">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <?php db_ancora(
                                                    "Conta Bancaria:",
                                                    "js_pesquisacontabancaria();",
                                                    1
                                                );?>
                                            </td>
                                            <td>
                                              <?php db_input("contabancaria_codigo", 5, 3, true, "text", 3);?>
                                              <?php db_input("contabancaria_descricao", 26, 3, true, "text", 3);?>
                                            </td>
                                        </tr>
                                        <tr>
                                          <td title="Agencia">
                                            <legend>Agencia:</legend>
                                            <?php db_input("agencia", 10, 3, true, "text", 3); ?>
                                         </td>
    
                                         <td style="padding: 5px;" title="Conta">
                                             <legend>Conta:</legend>
                                             <?php db_input("conta", 10, 3, true, "text", 3); ?>
                                          </td>
                                        </tr>
                                    </tbody>
                                </table>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" title="<?="Agrupamentos das receitas"?>">
                                <b>Agrupamento das receitas:</b>
                            </td>
                            <td align="left" ><?php
                                $x = array(
                                    1=>"Analítico",
                                    2=>"Pela conta de receita",
                                    3=>"Pelos códigos de empenho e receita"
                                );
                                db_select("agrupapor", $x, true, 1);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" nowrap title="<?="Receitas por baixa bancária"?>">
                                <b>Receitas por baixa bancária:</b>
                            </td>
                            <td align="left" nowrap>
                               <?php
                                    $x = array(
                                        1=>"Não agrupar pela classificação",
                                        2=>"Agrupar pela classificação"
                                    );
                                    db_select("receitaspor", $x, true, 1);
                                    ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" nowrap title="<?="Pagamentos de empenhos"?>">
                                <b>Pagamentos de empenhos:</b>
                            </td>
                            <td align="left" nowrap>
                                <?php
                                    $x = array(1=>"Detalhar",2=>"Agrupar");
                                    db_select("pagempenhos", $x, true, 1, $estiloDBSelect);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap align=right><b>Somente contas com movimento:</b></td>
                            <td>
                                <?php $matriz = array("n"=>"Não","s"=>"Sim");
                                    $somente_contas_com_movimento = "s";
                                    db_select("somente_contas_com_movimento", $matriz, true, 1, $estiloDBSelect);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap align=right><b>Totalizador diário:</b></td>
                            <td>
                                <?php
                                    $matriz = array("s"=>"Sim","n"=>"Não");
                                    db_select("totalizador_diario", $matriz, true, 1, $estiloDBSelect);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap align=right><b>Imprime histórico:</b></td>
                            <td>
                                <?php
                                    $matriz = array("s"=>"Sim","n"=>"Não");
                                    db_select("imprime_historico", $matriz, true, 1, $estiloDBSelect);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap align=right><b>Tipo Impressão:</b></td>
                            <td>
                                <?php
                                    $matriz = array("a"=>"Analítico","s"=>"Sintético");
                                    db_select("imprime_analitico", $matriz, true, 1, $estiloDBSelect);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap align="right"><b>Somente contas bancárias:</b></td>
                            <td>
                                <?php
                                    $matriz = array("s"=>"Sim","n"=>"Não");
                                    db_select("somente_contas_bancarias", $matriz, true, 1, $estiloDBSelect);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap align="right"><b>Formato do relatório:</b></td>
                            <td><?php
                                $matriz = array("p"=>"PDF","t"=>"CSV");
                                db_select("imprime_pdf", $matriz, true, 1, $estiloDBSelect);
                            ?></td>
                        </tr>
                    </table>
                </form>
                </fieldset>
            </td>
        </tr>

        <tr>
            <td align="center" valign="top">
                <button id="emitir" type="button" onClick="js_relatorio2()">
                    <i class="fas fa-print"></i>
                    Emitir
                </button>   
            </td>
        </tr>
    </table>
</div>

<?php
db_menu();
?>
</body>
</html>

<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">

    const formulario = document.getElementById('formulario');
    const rota = 'financeiro/tesouraria/relatorio/ExtratoContaBancaria';
    const inputDataInicio = document.form1.dataInicio;
    const inputDataFinal = document.form1.dataFinal;

    const validarInputs = () => {
        try {
            if (js_comparadata(inputDataInicio.value, inputDataFinal.value, '>')) {
                throw 'Data de inicio deve ser menor que a data final.';
            }
            return true;
        } catch (e) {
            alert(e);
            return false;
        }
    }

    function js_relatorio2() {

        if (!validarInputs()) {
            return;
        }

        var formData = new FormData(formulario);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${PHPSession.requestApi}/${rota}`, {body: formData}).then(response => {
           if (response.error) {
               alert(response.message);
               return;
           }
           const download = new DBDownload();
           download.addFile(response.data.path, response.data.name);
           download.show();
        });
    }
        
    function js_pesquisacontabancaria(){
        
        js_OpenJanelaIframe('CurrentWindow.corpo',
        'db_iframe_contabancaria',
        'func_contabancariacadastro.php?funcao_js=parent.js_mostracontabancaria'+
        '|db83_sequencial|db83_descricao|db89_codagencia|db89_digito|db83_conta|db83_dvconta',
        'Pesquisa',true,'0');
    }

    function js_mostracontabancaria(){

        $("contabancaria_codigo").value = arguments[0];
        $("contabancaria_descricao").value = arguments[1];
        $("agencia").value = arguments[2]+'-'+arguments[3];
        $("conta").value = arguments[4]+'-'+arguments[5];     
        db_iframe_contabancaria.hide();
    }

</script>