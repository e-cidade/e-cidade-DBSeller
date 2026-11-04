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
require_once modification('libs/db_utils.php');
require_once modification('dbforms/db_funcoes.php');
db_app::load("prototype.js");
db_postmemory($HTTP_POST_VARS);

$datainicio_dia = ""; $datafim_dia = "";
$datainicio_mes = ""; $datafim_mes = "";
$datainicio_ano = ""; $datafim_ano = "";

$datainiciosub_dia = ""; $datafimsub_dia = "";
$datainiciosub_mes = ""; $datafimsub_mes = "";
$datainiciosub_ano = ""; $datafimsub_ano = "";

$clSagresOrdenadorDespesa   = new cl_sagresordenadordespesa;
$clSagresOrdenadorDespesa->rotulo->label();

?>
<html>
  <head>
  <title>DBSeller Informática Ltda - Página Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script src="scripts/scripts.js" type="text/javascript"></script>
  <script src="scripts/classes/http/http.js"></script>
  <script src="scripts/scripts.js" rel="script" type="text/javascript"></script>
  <script src="scripts/prototype.js" rel="script" type="text/javascript"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" type="text/css" href="assets/fontawesome/css/all.min.css">
  <?php
  db_app::load("strings.js, datagrid.widget.js");
  db_app::load("widgets/messageboard.widget.js, widgets/windowAux.widget.js");
  db_app::load("grid.style.css");
  ?>
  <style>
      .d-flex { display: flex; }
      .justify-content-center { justify-content: center; }
      .align-items-center { align-items: center; }
      .mt-1 { margin-top: 1rem; }
      .mx-1 { margin: 0 1rem; }
  </style>
  </head>
  <body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
    <br><br>
    <center>
        <div>
            <form name="form1" action="" id="formOrdenador">
                <table>
                    <tr>
                        <td>
                            <fieldset>
                                <legend><b>Filtros de busca:</b></legend>

                                <table border="0">
                                    <tr>
                                        <td class="bold">
                                            <label for="orgao_numero">
                                                <b>Instituição:</b>
                                            </label>
                                        </td>
                                        <td>
                                            <?php
                                            $c139_instit = db_getsession("DB_instit");
                                            db_input('c139_instit', 10, 1, true, 'text', 3);
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="bold">
                                            <label for="CGM">
                                                <?php db_ancora('CGM:', 'buscarCGM(true)', 1); ?>
                                            </label>
                                        </td>
                                        <td>
                                            <?php
                                            db_input('c139_cgm', 10, 1, true, 'text', 1, 'onChange="buscarCGM(false)"');
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            db_input('cgm_descricao', 40, 0, true, 'text', 3);
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td nowrap title="Ordenador Principal">
                                            <b>Ordenador Principal:</b>
                                        </td>
                                        <td>
                                            <?php
                                            $aPrincipal = array("t" => "SIM", "f" => "NÃO");
                                            db_select("c139_principal", $aPrincipal, true, 1);
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="3">
                                            <fieldset>
                                                <legend>
                                                    <b>Período de Atividade:</b>
                                                </legend>
                                                <center>
                                                    <table cellpadding="0" border="0" width="100%" class="table-vigencia">
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex justify-content-center align-items-center">
                                                                    <b>Início: </b>
                                                                    <?php
                                                                    $iCampo = 1;
                                                                    db_inputdata('c139_datainicio', $datainicio_dia, $datainicio_mes, $datainicio_ano, true, 'text',1);
                                                                    ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex justify-content-center align-items-center">
                                                                    <b>Fim: </b>
                                                                    <?php
                                                                    db_inputdata('c139_datafim', $datafim_dia, $datafim_mes, $datafim_ano, true, 'text',1);
                                                                    ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </fieldset>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="bold">
                                            <label for="Título">
                                                <b>Título:</b>
                                            </label>
                                        </td>
                                        <td colspan="2">
                                            <?php
                                            db_input('c139_titulo', 54, 0, true, 'text', 1);
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td nowrap title="Substituto">
                                            <b>Substituto:</b>
                                        </td>
                                        <td>
                                            <?php
                                            $aSub = array("f" => "NÃO", "t" => "SIM");
                                            db_select("c139_substituto", $aSub, true, 1, 'onChange="toogleSub()"');
                                            ?>
                                        </td>
                                    </tr>

                                    <tr class="ctSub" hidden>
                                        <td class="bold">
                                            <label for="CGM Substituto">
                                                <?php db_ancora('CGM Substituto:', 'buscarCGMSub(true)', 1); ?>
                                            </label>
                                        </td>
                                        <td>
                                            <?php
                                            db_input('c139_cgmsubstituto', 10, 1, true, 'text', 1, 'onChange="buscarCGMSub(false)"');
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            db_input('cgm_sub_descricao', 40, 0, true, 'text', 3);
                                            ?>
                                        </td>
                                    </tr>

                                    <tr class="ctSub" hidden>
                                        <td colspan="3">
                                            <fieldset>
                                                <legend>
                                                    <b>Período de Atividade do Substituto:</b>
                                                </legend>
                                                <center>
                                                    <table cellpadding="0" border="0" width="100%" class="table-vigencia">
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex justify-content-center align-items-center">
                                                                    <b>Início: </b>
                                                                    <?php
                                                                    $iCampo = 1;
                                                                    db_inputdata('c139_datainiciosub', $datainicio_dia, $datainicio_mes, $datainicio_ano, true, 'text',1);
                                                                    ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex justify-content-center align-items-center">
                                                                    <b>Fim: </b>
                                                                    <?php
                                                                    db_inputdata('c139_datafimsub', $datafim_dia, $datafim_mes, $datafim_ano, true, 'text',1);
                                                                    ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </fieldset>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td nowrap title="Tipo Ato Jurídico">
                                            <b>Tipo Ato Jurídico:</b>
                                        </td>
                                        <td>
                                            <?php
                                            $aTipoAto = array("1" => "Lei","2" => "Decreto","3" => "Portaria","4" => "Outros");
                                            db_select("c139_tipoatojuridico", $aTipoAto, true, 1);
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="3">
                                            <div class="d-flex justify-content-center mt-1">
                                                <input class="mx-1" type="button" id="incluirCadastro" value="Incluir">
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td>
                            <input id="radio-all-rows" type="radio" name="status" onclick="getRows()" value="" checked> Todos
                            <input type="radio" name="status" onclick="getRows()" value="t"> Ativos
                            <input type="radio" name="status" onclick="getRows()" value="f"> Inativos
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <fieldset style="width: 1500px;">
                                <legend><b>Ordenadores</b></legend>
                                <div id='ctnGridOrdenador'>
                                </div>
                            </fieldset>
                        </td>
                    </tr>

                </table>
            </form>
        </div>
    </center>

    <script>

        const sUrlRPC   = "con4_sagres.RPC.php";
        const formulario = document.getElementById('formOrdenador');

        const tipoato = {
            1: "LEI",
            2: "DECRETO",
            3: "PORTARIA",
            4: "OUTROS"
        };

        function buscarCGM(lMostrar) {
            $('cgm_descricao').value = '';
            if ( lMostrar ) {
                $('c139_cgm').value = '';
                js_OpenJanelaIframe("",'func_nome','func_cgm.php?condition="somenteAtivos"&funcao_js=parent.js_preencheCGM|z01_numcgm|z01_nome','Pesquisa',true);
            } else {
                js_divCarregando("Pesquisando ..." , 'msgBox');
                js_OpenJanelaIframe("",'func_nome','func_cgm.php?condition="somenteAtivos"&pesquisa_chave='+document.form1.c139_cgm.value+'&funcao_js=parent.js_preencheCGM1','Pesquisa',false);
            }
        }

        function js_preencheCGM( iCodigo, sDescricao ) {
            document.form1.c139_cgm.value           = iCodigo;
            document.form1.cgm_descricao.value = sDescricao;
            func_nome.hide();
        }

        function js_preencheCGM1( lErro, sDescricao ) {
            js_removeObj('msgBox');
            if ( !lErro ) {
                document.form1.cgm_descricao.value = sDescricao;
            } else {
                document.form1.c139_cgm.value           = "";
                document.form1.cgm_descricao.value = sDescricao;
            }
        }

        function buscarCGMSub(lMostrar) {
            $('cgm_sub_descricao').value = '';
            if ( lMostrar ) {
                $('c139_cgmsubstituto').value = '';
                js_OpenJanelaIframe("",'func_nome','func_cgm.php?condition="somenteAtivos"&funcao_js=parent.js_preencheCGMSub|z01_numcgm|z01_nome','Pesquisa',true);
            } else {
                js_divCarregando("Pesquisando ..." , 'msgBox');
                js_OpenJanelaIframe("",'func_nome','func_cgm.php?condition="somenteAtivos"&pesquisa_chave='+document.form1.c139_cgmsubstituto.value+'&funcao_js=parent.js_preencheCGMSub1','Pesquisa',false);
            }
        }

        function js_preencheCGMSub( iCodigo, sDescricao ) {
            document.form1.c139_cgmsubstituto.value = iCodigo;
            document.form1.cgm_sub_descricao.value  = sDescricao;
            func_nome.hide();
        }

        function js_preencheCGMSub1( lErro, sDescricao ) {
            js_removeObj('msgBox');
            if ( !lErro ) {
                document.form1.cgm_sub_descricao.value  = sDescricao;
            } else {
                document.form1.c139_cgmsubstituto.value = "";
                document.form1.cgm_sub_descricao.value  = sDescricao;
            }
        }

        let ordenadores = [];

        function js_incluirCadastro()
        {
            if($F('c139_instit').trim() == ''){
                alert('O campo Instituição é necessário para continuar.');
                return;
            }

            if($F('c139_cgmsubstituto').trim() == '' && $F('c139_substituto') == 't'){
                alert('O campo CGM do Substituto é necessário para continuar.');
                return;
            }

            if($F('c139_cgmsubstituto').trim() == $F('c139_cgm').trim()){
                alert('Os campos CGM e CGM Substituto não podem ser iguais.');
                return;
            }

            if($F('c139_datainicio').trim() == ''){
                alert('O campo Data Início é necessário para continuar.');
                return;
            }

            if($F('c139_datafim').trim() == '' && $F('c139_substituto') == 't'){
                alert('O campo Data Fim é necessário para continuar.');
                return;
            }

            const parametros = new FormData(formulario);
            parametros.append('exec', 'salvarOrdenador');

            HttpClient.post(sUrlRPC, {body: parametros}).then((response) => {
                
                $('c139_cgm').value             = '';
                $('cgm_descricao').value        = '';
                $('c139_cgmsubstituto').value   = '';
                $('cgm_sub_descricao').value    = '';
                $('c139_principal').value       = 'f';
                $('c139_substituto').value      = 'f';
                $('c139_datainicio').value      = '';
                $('c139_datafim').value         = '';
                $('c139_tipoatojuridico').value = '1';
                if($('c139_titulo') != null) {
                    $('c139_titulo').value      = '';
                }
                toogleSub();

                if (response.erro) {
                    alert(response.message);
                    return;
                }
                radiobtn = document.getElementById("radio-all-rows");
                radiobtn.checked = true;
                getRows();
            });
        }

        function renderGrid(res) {
            oGridOrdenador.clearAll(true);
            res.each(function (oRow, id) {
                aLinha    = new Array();
                aLinha[0] = oRow.cgm;
                aLinha[1] = oRow.nome;
                aLinha[2] = oRow.titulo == '' ? '-' : oRow.titulo;
                aLinha[3] = oRow.principal == 't' ? 'SIM' : 'NÃO';
                aLinha[4] = oRow.substituto == 't' ? 'SIM' : 'NÃO';
                aLinha[5] = oRow.cgmsub == 0 ? '-' : oRow.cgmsub;
                aLinha[6] = oRow.cgmsub == 0 ? '-' : oRow.nomesub;
                aLinha[7] = oRow.periodo == '' ? '-' : oRow.periodo; 
                aLinha[8] = oRow.periodosub == '' ? '-' : oRow.periodosub;
                aLinha[9] = tipoato[oRow.tipoato];
                aLinha[10] = oRow.ativo == 'f' ? '-' : "<input type='button' value='E' onclick='inativarOrdenador("+oRow.sequencial+")'>";
                aLinha[11] = oRow.tipoato;
                aLinha[12] = `<span class="status-${oRow.ativo}">${oRow.ativo}</span>`;

                oGridOrdenador.addRow(aLinha);
            });
            oGridOrdenador.renderRows();

            colorGrid();
        }

        function colorGrid() {
            document.querySelectorAll('.status-f').forEach((e)=>{
                e.closest('tr').setAttribute('style','background: #ee00001f')
            })
        }

        function toogleSub() {
            var ct = document.querySelectorAll('.ctSub');
            if ($F('c139_substituto') == 't') {
                ct[0].removeAttribute('hidden');
                ct[1].removeAttribute('hidden');
            } else {
                ct[0].setAttribute('hidden','hidden');
                ct[1].setAttribute('hidden','hidden');
            }
        }

        function js_main() {
            oGridOrdenador              = new DBGrid('oGridOrdenador');
            oGridOrdenador.nameInstance = 'oGridOrdenador';
            oGridOrdenador.setCellAlign(new Array('center', 'left', 'left', 'center', 'center', 'center', 'left', 'center', 'center', 'center', 'center', 'center', 'center'));
            oGridOrdenador.setCellWidth(
                new Array('50px', '180px', '100px', '55px', '55px', '55px', '180px', '120px', '120px', '80px', '50px', '0', '0')
            );
            oGridOrdenador.setHeader(
                new Array('CGM', 'Nome', 'Titulo', 'Principal' , 'Substituto', 'CGM-Sub', 'Nome-Sub', 'Período', 'Período Sub', 'Tipo Ato', 'Ação',  'id_tipo', 'status')
            );
            oGridOrdenador.aHeaders[11].lDisplayed = false;
            oGridOrdenador.aHeaders[12].lDisplayed = false;
            oGridOrdenador.show($('ctnGridOrdenador'));

            $('incluirCadastro').onclick=js_incluirCadastro;

            getRows();
        }

        function getRows() {
            js_divCarregando("Pesquisando ..." , 'msgBox');

            const parametros = new FormData(formulario);
            parametros.append('exec', 'getOrdenador');
            parametros.append('instit', $F('c139_instit'));
            parametros.append('ativo', document.querySelector('input[name="status"]:checked').value);


            HttpClient.post(sUrlRPC, {body: parametros}).then((response) => {
                js_removeObj('msgBox');
                if(response.erro) {
                    alert(response.message);
                    return;
                }
                renderGrid(response.rows);
            });
        };

        function inativarOrdenador(id) {
          js_divCarregando("Pesquisando ..." , 'msgBox');

            const parametros = new FormData(formulario);
            parametros.append('exec', 'inativarOrdenador');
            parametros.append('id', id);

            HttpClient.post(sUrlRPC, {body: parametros}).then((response) => {
                js_removeObj('msgBox');
                if(response.erro) {
                    alert(response.message);
                    return;
                }
                getRows();
            });
        };

        js_main();
    </script>
    <?php
    db_menu();
    ?>
  </body>
</html>
