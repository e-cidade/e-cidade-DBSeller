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

require_once(modification("classes/db_iptubase_classe.php"));
require_once(modification("classes/db_issbase_classe.php"));
require_once(modification("classes/db_arreprescr_classe.php"));
require_once(modification("classes/db_cgm_classe.php"));
require_once(modification("classes/db_numpref_classe.php"));
require_once(modification("classes/db_termoanu_classe.php"));
require_once(modification("classes/db_fiscal_classe.php"));
require_once(modification("classes/db_levanta_classe.php"));
require_once(modification("classes/db_db_config_classe.php"));

require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_sql.php"));

require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));

parse_str($HTTP_SERVER_VARS ['QUERY_STRING']);

if (session_is_registered("DB_tipodebitoparcel")) {
    db_putsession("DB_tipodebitoparcel", "");
}

$clcgm = new cl_cgm();
$cldb_config = new cl_db_config();
$clcgm->rotulo->label();
$clrotulo = new rotulocampo();
$clrotulo->label('j01_matric');
$clrotulo->label('q02_inscr');

$iInstitSessao = db_getsession('DB_instit');
$result = $cldb_config->sql_record($cldb_config->sql_query_file($iInstitSessao, "cgc, db21_codcli"));
db_fieldsmemory($result, 0);

$clnumpref = new cl_numpref();
$resnumpref = $clnumpref->sql_record($clnumpref->sql_query_file(db_getsession("DB_anousu"), db_getsession('DB_instit'), "k03_certissvar"));
if ($resnumpref == false || $clnumpref->numrows == 0) {
    db_msgbox("Tabela de parâmetro (numpref) não configurada! Verifique com administrador");
    db_redireciona("corpo.php");
    exit();
} else {
    db_fieldsmemory($resnumpref, 0);
}


$j18_nomefunc = "func_iptubase.php";


?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>

    <style type="text/css">
        #janelaRecibo {
            -moz-user-select: none;
        }
    </style>

    <link href="estilos.css" rel="STYLESHEET" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default">
<div id="DDD"></div>
<div id="processando"
     style="position: absolute; left: 05px; top: 113px; width: 99%; height: 235px; z-index: 1; visibility: hidden; background-color: #FFFFFF; layer-background-color: #FFFFFF; border: 1px none #000000;">
    <Table width="99%">
        <tr>
            <td align="center" valign="middle" id="processandoTD"
                onclick="document.getElementById('processando').style.visibility='hidden'"></td>
        </tr>
    </Table>
</div>

<div class="container">
    <form name="form1" method="POST" action="arr2_extratocontribuinte002.php">
        <fieldset style="width:600px">
            <legend>Extrato Contribuinte</legend>
            <table>
                <!-- Select Situação do Débito -->
                <tr>
                    <td>
                        <label>Situação do Débito</label>
                        <select id="selectSituacaoDebito" name="selectSituacaoDebito">
                            <option value="">Todas</option>
                            <option value="pendente">Pendente</option>
                            <option value="pago">Pago</option>
                            <option value="prescrito">Prescrito</option>
                            <option value="cancelado">Cancelado</option>
                            <option value="parcelado">Parcelado</option>
                            <option value="suspenso">Suspenso</option>
                            <option value="inscrito em cob adm">Inscrito em cobrança Adm</option>
                            <option value="inscrito em divida">Inscrito em Dívida</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label>Tipo de Arquivo</label>
                        <select id="tipoArquivo" name="tipoArquivo"">
                        <option value="1">PDF</option>
                        <option value="2">XLS</option>
                        </select>
                    </td>
                </tr>

                <!-- Select Filtro de Busca -->
                <tr>
                    <td>
                        <label>Filtro de Busca</label>
                        <select id="selectFiltroBusca" name="selectFiltroBusca" onchange="js_filtroBusca()">
                            <option value="">Selecione</option>
                            <option value="cgm">CGM</option>
                            <option value="matric">Matrícula</option>
                            <option value="inscr">Inscrição</option>
                        </select>
                    </td>
                </tr>

                <tr id="camposCgm" style="display:none">
                        <td title="<?php echo $Tz01_nome; ?>">
                            <?php db_ancora($Lz01_nome, 'js_mostranomes(true);', 4); ?>
                        </td>
                        <td>
                            <input type="text" name="z01_numcgm" id="z01_numcgm" maxlength="8" size="8"
                                   autocomplete="off" onkeyup="js_ValidaCampos(this,1,'Numcgm','t','f',event);"
                                   onblur="js_ValidaMaiusculo(this,'f',event);" onchange="js_mostranomes(false);"
                                   title="Numero de Identificação do Contribuinte ou Empresa no Cadastro geral do Município Campo:z01_numcgm "/>
                            <?php db_input("nomecgm", 40, $Iz01_nome, true, 'text', 5); ?>
                        </td>
                </tr>

                <tr id="camposMatric" style="display:none">
                <td title="<?php echo $Tj01_matric; ?>">
                            <?php db_ancora($Lj01_matric, "js_mostramatricula(true,'$j18_nomefunc');", 2); ?>
                        </td>
                        <td>
                            <input type="text" name="j01_matric" id="j01_matric" maxlength="8" size="8"
                                   autocomplete="off"
                                   onkeyup="js_ValidaCampos(this,1,'Matrícula do Imóvel','t','f',event);"
                                   onblur="js_ValidaMaiusculo(this,'f',event);"
                                   onchange="js_mostramatricula(false,'<?= $j18_nomefunc ?>')"
                                   title="Codigo da matrícula do imovel para identificar o proprietário de um determinado lote. Campo:j01_matric "/>
                                   <?php db_input("nomematric", 40, $Iz01_nome, true, 'text', 5); ?>
                        </td>
                </tr>

                <tr id="camposInscr" style="display:none">
                <td title="<?php echo $Tq02_inscr; ?>">
                                <?php db_ancora($Lq02_inscr, 'js_mostrainscricao(true);', 4); ?>
                            </td>
                            <td>
                                <input type="text" name="q02_inscr" id="q02_inscr" maxlength="8" size="8"
                                       autocomplete="off"
                                       onkeyup="js_ValidaCampos(this,1,'Inscrição Municipal','t','f',event);"
                                       onblur="js_ValidaMaiusculo(this,'f',event);" onchange="js_mostrainscricao(false)"
                                       title="Inscricao Municipal no cadastro de alvará Campo:q02_inscr "/>
                                       <?php db_input("nomeinscr", 40, $Iz01_nome, true, 'text', 5); ?>
                            </td>
                </tr>

            </table>

            <br>

            <fieldset style="width:55%; margin:0 auto">
                <legend>Intervalo de Exercício</legend>
                <table>
                    <tr>
                        <td>
                            <label for="exercicio_inicial"><strong>Inicial:</strong></label>
                            <?db_input('exercicio_inicial', 4, 1, true, 'text', "", "", "", "", "", 4)?>
                        </td>
                        <td style="width:10%"></td>
                        <td>
                            <label for="exercicio_final"><strong>Final:</strong></label>
                            <?db_input('exercicio_final', 4, 1, true, 'text', "", "", "", "", "", 4)?>
                        </td>
                    </tr>
                </table>
            </fieldset>

            <br>

        </fieldset>
        <input onClick="js_enviarForm()" type="button" value="Pesquisar" name="pesquisar">
    </form>
    <?php
    db_menu(db_getsession("DB_id_usuario"),
        db_getsession("DB_modulo"),
        db_getsession("DB_anousu"),
        db_getsession("DB_instit"));
    ?>
</div>

</body>
</html>
<script>

function js_mostranomes(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_nomes', 'func_nome.php?funcao_js=parent.js_preenche|0|1', 'Pesquisa', true);
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_nomes', 'func_nome.php?pesquisa_chave=' + document.form1.z01_numcgm.value + '&funcao_js=parent.js_preenche1', 'Pesquisa', false);
        }
    }

    function js_preenche(chave, chave1) {
        console.log(chave, chave1)
        document.form1.z01_numcgm.value = chave;
        document.form1.nomecgm.value = chave1;
        db_iframe_nomes.hide();
    }

    function js_preenche1(chave, chave1) {
        console.log(chave, chave1)
        document.form1.nomecgm.value = chave1;
        if (chave == true) {
            document.form1.z01_numcgm.value = "";
            document.form1.z01_numcgm.focus();
        }
    }

function js_mostramatricula(mostra, nome_func) {
        document.form1.z01_numcgm.value = "";
        document.form1.q02_inscr.value = "";
        if (mostra == true) {
            if (nome_func != "func_iptubase.php") {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_matric', nome_func + '?funcao_js=parent.js_preenchematricula|0|1', 'Pesquisa', true);
            } else {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_matric', nome_func + '?funcao_js=parent.js_preenchematricula3|0|1|2|3|4', 'Pesquisa', true);
            }
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_matric', nome_func + '?pesquisa_chave=' + document.form1.j01_matric.value + '&funcao_js=parent.js_preenchematricula2', 'Pesquisa', false);
        }
    }

    function js_preenchematricula3(chave, chave1, chave2, chave3, chave4) {
        console.log(chave, chave4); 
        document.form1.j01_matric.value = chave;
        document.form1.nomematric.value = chave4;
        db_iframe_matric.hide();

    }

    function js_preenchematricula(chave, chave1) {

        document.form1.j01_matric.value = chave;
        document.form1.nomematric.value = chave1;
        db_iframe_matric.hide();

    }

    function js_preenchematricula2(chave, chave1) {

        if (chave1 == false) {
            document.form1.nomematric.value = chave;
            db_iframe_matric.hide();
        } else {
            document.form1.nomematric.value = chave;
            document.form1.j01_matric.value = "";
            db_iframe_matric.hide();
        }
        if (document.form1.j01_matric.value == '' && document.form1.nomematric.value == '') {
            document.form1.nomematric.value = '';
        }
    }


function js_mostrainscricao(mostra) {
        document.form1.j01_matric.value = "";
        document.form1.z01_numcgm.value = "";
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe', 'func_issbase.php?funcao_js=parent.js_mostra|q02_inscr|z01_nome|q02_dtbaix&todas_inscricoes=true', 'Pesquisa', true);
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe', 'func_issbase.php?pesquisa_chave=' + document.form1.q02_inscr.value + '&funcao_js=parent.js_mostra&todas_inscricoes=true', 'Pesquisa', false);
        }
    }

    function js_mostra(chave1, chave2, baixa) {

        if (chave2 != false) {
            document.form1.q02_inscr.value = chave1;
            document.form1.nomeinscr.value = chave2;
            db_iframe.hide();
        } else {
            document.form1.nomeinscr.value = chave1;
        }

        if (document.form1.q02_inscr.value == '') {
            document.form1.nomeinscr.value = '';
        }

        if (typeof (baixa) == "undefined" && chave2 == true) {
            
            document.form1.nomeinscr.value = chave1;
            document.form1.q02_inscr.value = '';
        }

        db_iframe.hide();
    }

    function js_filtroBusca() {
        const selectFiltroBusca = document.form1.selectFiltroBusca.value;

        // Oculta os campos todos para posteriormente filtrar pelo campo do select
        document.getElementById('camposCgm').style.display = 'none';
        document.getElementById('camposMatric').style.display = 'none';
        document.getElementById('camposInscr').style.display = 'none';
        document.form1.q02_inscr.value  = "";
        document.form1.nomeinscr.value  = "";
        document.form1.j01_matric.value = "";
        document.form1.nomematric.value = "";
        document.form1.z01_numcgm.value = "";
        document.form1.nomecgm.value    = "";
        // Filtra pelo campo do select, mostrando apenas os campos necessários
        if (selectFiltroBusca == 'cgm') {
            document.getElementById('camposCgm').style.display = 'block';
        } else if (selectFiltroBusca == 'matric') {
            document.getElementById('camposMatric').style.display = 'block';
        } else if (selectFiltroBusca == 'inscr') {
            document.getElementById('camposInscr').style.display = 'block';
        }
    }

    function js_enviarForm()
    {
        if (js_validaForm()) {
            const obj = document.form1;

            var sQuery = "?";
            sQuery += "selectSituacaoDebito="+obj.selectSituacaoDebito.value;
            sQuery += "&selectFiltroBusca="+obj.selectFiltroBusca.value;
            sQuery += "&tipoArquivo="+obj.tipoArquivo.value;
            sQuery += "&numcgm="+obj.z01_numcgm.value;
            sQuery += "&matric="+obj.j01_matric.value;
            sQuery += "&inscr="+obj.q02_inscr.value;
            sQuery += "&exercicio_inicial="+obj.exercicio_inicial.value;
            sQuery += "&exercicio_final="+obj.exercicio_final.value;

            window.open("arr2_extratocontribuinte002.php"+sQuery, "_blank");
        }
    }

    function js_validaForm() {
        // VALIDAÇÃO FILTRO DE BUSCA
        const selectSituacaoDebito = document.form1.selectSituacaoDebito.value;
        const selectFiltroBusca = document.form1.selectFiltroBusca.value;
        const cgm = document.form1.z01_numcgm.value;
        const matric = document.form1.j01_matric.value;
        const inscr = document.form1.q02_inscr.value;
        
        if (selectFiltroBusca == '') {
            alert('Selecione um Filtro de Busca!');
            return false;
        } else if (selectFiltroBusca == 'cgm') {
            if (cgm == '') {
                alert('Informe um intervalo de CGM!');
                return false;
            }
        } else if (selectFiltroBusca == 'matric') {
            if (matric == '') {
                alert('Informe um intervalo de Matrícula!');
                return false;
            } 
        } else if (selectFiltroBusca == 'inscr') {
            if (inscr == '') {
                alert('Informe um intervalo de Inscrição!');
                return false;
            } 
        } 

        // VALIDAÇÃO INTERVALO DE EXERCICIO
        const exercicio_inicial = document.form1.exercicio_inicial.value;
        const exercicio_final = document.form1.exercicio_final.value;

        if (exercicio_inicial.length==4 && exercicio_final.length==4) {
            if (exercicio_inicial != 0 || exercicio_inicial != 0) {
                var current_year=new Date().getFullYear();
                if ((exercicio_inicial < 1980) || (exercicio_inicial > current_year) || (exercicio_final > current_year)) {
                    alert('Intervalo de Exercício deve estar entre 1980 e o ano atual (<?=db_getsession("DB_anousu")?>)');
                    return false;
                }
                if ((exercicio_final - exercicio_inicial > 5) || (selectSituacaoDebito == '')) {
                    if (confirm('Os filtros selecionados podem gerar uma busca demorada, deseja continuar?')) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
        else {
            alert('Insira uma exercício válido!');
            return false;
        }

        if (exercicio_inicial > exercicio_final) {
            alert('Exercício inicial deve ser menor que o final!');
            return false;
        }

        return true;
    }
</script>
