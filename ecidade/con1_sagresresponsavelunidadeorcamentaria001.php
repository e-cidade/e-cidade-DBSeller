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
// referencia aco4_abaassinaturacontratos001_natal.php

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_utils.php');
require_once modification('dbforms/db_funcoes.php');
db_app::load("prototype.js");
db_postmemory($HTTP_POST_VARS);

$c140_datainicio_dia = ""; $c140_datafim_dia = "";
$c140_datainicio_mes = ""; $c140_datafim_mes = "";
$c140_datainicio_ano = ""; $c140_datafim_ano = "";
    
$clSagresResponsavelOrdenador   = new cl_sagresresponsavelunidadeorcamentaria;
$clSagresResponsavelOrdenador->rotulo->label();

?>
<html>
  <head>
  <title>DBSeller Informática Ltda - Página Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script src="scripts/classes/http/http.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" type="text/css" href="assets/fontawesome/css/all.min.css">
  <?php
  db_app::load("scripts.js, strings.js, prototype.js, datagrid.widget.js");
  db_app::load("widgets/messageboard.widget.js, widgets/windowAux.widget.js");
  db_app::load("estilos.css, grid.style.css");
  ?>
  <style type="text/css">
      .linhagrid {
          text-align: center;
      }
      .d-flex {
        display: flex;
      }
      .justify-content-center {
        justify-content: center;
      }
      .align-items-center {
          align-items: center;
      }
      .mt-1 {
          margin-top: 1rem;
      }
      .mx-1 {
          margin: 0 1rem;
      }
  </style>
  </head>
  <body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
    <br><br>
    <center>
        <div>
            <form name="form1" action="" id="formResponsavel">
                <table>
                    <tr>
                        <td>
                            <fieldset>
                                <legend><b>Filtros de busca:</b></legend>
                        
                                <table border="0">
                                    <tr>
                                        <td class="bold">
                                            <label for="c140_orgao">
                                                <?php db_ancora('Órgão:', 'buscarOrgao(true)', 1); ?>
                                            </label>
                                        </td>
                                        <td>
                                            <?php                      
                                            db_input('c140_orgao', 10, 1, true, 'text', 1, 'onChange="buscarOrgao(false)"');
                                            ?>
                                        </td>
                                        <td>
                                            <?php                      
                                            db_input('orgao_descricao', 40, 0, true, 'text', 3);
                                            ?>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td class="bold">
                                            <label for="c140_unidade>">
                                            <?php db_ancora('Unidade:', 'buscarUnidade(true)', 1); ?>
                                            </label>
                                        </td>
                                        <td>
                                            <?php
                                            db_input('c140_unidade', 10, 1, true, 'text', 1, 'onChange="buscarUnidade(false)"');
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            db_input('unidade_descricao', 40, 0, true, 'text', 3);
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="bold">
                                            <label for="c140_cgm">
                                                <?php db_ancora('CGM:', 'buscarCGM(true)', 1); ?>
                                            </label>
                                        </td>
                                        <td>
                                            <?php
                                            db_input('c140_cgm', 10, 1, true, 'text', 1, 'onChange="buscarCGM(false)"');
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            db_input('cgm_descricao', 40, 0, true, 'text', 3);
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td nowrap title="Responsável Principal">
                                            <b>Responsável Principal:</b>
                                        </td>
                                        <td>
                                            <?php
                                            $aPrincipal = array("f" => "NÃO", "t" => "SIM");
                                            db_select("c140_principal", $aPrincipal, true, 1);
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
                                                                    db_inputdata('c140_datainicio', $c140_datainicio_dia, $c140_datainicio_mes, $c140_datainicio_ano, true, 'text',1);
                                                                    ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex justify-content-center align-items-center">
                                                                    <b>Fim: </b>
                                                                    <?php
                                                                    db_inputdata('c140_datafim', $c140_datafim_dia, $c140_datafim_mes, $c140_datafim_ano, true, 'text',1);
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
                                        <td nowrap title="Substituto">
                                            <b>Substituto:</b>
                                        </td>
                                        <td>
                                            <?php
                                            $aSub = array("f" => "NÃO", "t" => "SIM");
                                            db_select("c140_substituto", $aSub, true, 1, 'onChange="toogleSub()"');
                                            ?>
                                        </td>
                                    </tr>

                                    <tr class="ctSub" hidden>
                                        <td class="bold">
                                            <label for="c140_cgmsubstituto">
                                                <?php db_ancora('CGM Substituto:', 'buscarCGMSub(true)', 1); ?>
                                            </label>
                                        </td>
                                        <td>
                                            <?php
                                            db_input('c140_cgmsubstituto', 10, 1, true, 'text', 1, 'onChange="buscarCGMSub(false)"');
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
                                                                    db_inputdata('c140_datainiciosub', $c140_datainicio_dia, $c140_datainicio_mes, $c140_datainicio_ano, true, 'text',1);
                                                                    ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex justify-content-center align-items-center">
                                                                    <b>Fim: </b>
                                                                    <?php
                                                                    db_inputdata('c140_datafimsub', $c140_datafim_dia, $c140_datafim_mes, $c140_datafim_ano, true, 'text',1);
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
                                        <td nowrap title="Quantidade">
                                            <b>Tipo Ato Jurídico:</b>
                                        </td>
                                        <td>
                                            <?php
                                            $aTipoAto = array("1" => "Lei","2" => "Decreto","3" => "Portaria","4" => "Outros");
                                            db_select("c140_tipoatojuridico", $aTipoAto, true, 1);
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
                                <legend><b>Responsáveis</b></legend>
                                <div id='ctnGridResponsavel'>
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
        const formulario = document.getElementById('formResponsavel');

        const tipoato = {
            1: "LEI",
            2: "DECRETO",
            3: "PORTARIA",
            4: "OUTROS"
        };

        function buscarOrgao(lMostrar) {

            var sQuerySring = 'orcdotacao="true"&funcao_js=parent.retornoOrgao|0|2';
            var sArquivo    = 'func_orcorgao.php';
            var sTituloTela = 'Pesquisar Órgão';

            if (!lMostrar) {
              js_divCarregando("Pesquisando ..." , 'msgBox');
              sQuerySring = 'pesquisa_chave=' + $F('c140_orgao') + '&orcdotacao="true"&funcao_js=parent.retornoOrgaoChave';
            }

            js_OpenJanelaIframe('', 'db_iframe_orcorgao', sArquivo +'?' +sQuerySring, sTituloTela, lMostrar);
        }

        function retornoOrgaoChave(sDescricao, lErro) {
            js_removeObj('msgBox');
            $('c140_unidade').value    = '';
            $('unidade_descricao').value = '';

            if (lErro) {
                $('c140_orgao').value = "";  
            }
            $('orgao_descricao').value = sDescricao;
        }

        function retornoOrgao(iCodigo, sDescricao, lErro) {
            js_removeObj('msgBox');
            $('c140_unidade').value    = '';
            $('unidade_descricao').value = '';

            $('c140_orgao').value = iCodigo;
            $('orgao_descricao').value = sDescricao;
            
            db_iframe_orcorgao.hide();
        }

        function buscarUnidade(lMostrar) {
            const iOrgao = $F('c140_orgao');
            if (iOrgao == '') {
                alert("Para selecionar uma unidade, você deve primeiro informar o Órgão.");
                return false;
            }

            var sQuerySring = 'orgao=' + iOrgao + '&funcao_js=parent.retornoUnidade|2|4|8';
            var sArquivo    = 'func_orcunidade.php';
            var sTituloTela = 'Pesquisar Unidade';

            if (!lMostrar) {
                js_divCarregando("Pesquisando ..." , 'msgBox');
                sQuerySring = 'pesquisa_chave=' + $F('c140_unidade') + '&orgao=' + iOrgao + '&funcao_js=parent.retornoUnidadeChave';
            }

            js_OpenJanelaIframe('', 'db_iframe_orcunidade', sArquivo +'?' +sQuerySring, sTituloTela, lMostrar);
            getRows();
        }   

        function retornoUnidade(iCodigo, sDescricao) {
            js_removeObj('msgBox');
            $('c140_unidade').value    = iCodigo;
            $('unidade_descricao').value = sDescricao;
            db_iframe_orcunidade.hide();
        }

        function retornoUnidadeChave(sDescricao, lErro, iCodigo) {
            js_removeObj('msgBox');
            if (lErro) {
                $('c140_unidade').value = "";
            }
            $('unidade_descricao').value = sDescricao;
        }

        function buscarCGM(lMostrar) {
            $('cgm_descricao').value = '';
            if ( lMostrar ) {
                $('c140_cgm').value = '';
                js_OpenJanelaIframe("",'func_nome','func_cgm.php?condition="somenteAtivos"&funcao_js=parent.js_preencheCGM|z01_numcgm|z01_nome','Pesquisa',true);
            } else {
                if($F('c140_cgm')== 0) { $('c140_cgm').value = ''; return;}
                js_divCarregando("Pesquisando ..." , 'msgBox');
                js_OpenJanelaIframe("",'func_nome','func_cgm.php?condition="somenteAtivos"&pesquisa_chave='+document.form1.c140_cgm.value+'&funcao_js=parent.js_preencheCGM1','Pesquisa',false);
            }
        }

        function js_preencheCGM( iCodigo, sDescricao ) {
            document.form1.c140_cgm.value           = iCodigo;
            document.form1.cgm_descricao.value = sDescricao;
            func_nome.hide();
        }

        function js_preencheCGM1( lErro, sDescricao ) {
            js_removeObj('msgBox');
            if ( !lErro ) {
                document.form1.cgm_descricao.value = sDescricao;
            } else {
                document.form1.c140_cgm.value           = "";
                document.form1.cgm_descricao.value = sDescricao;
            }
        }

        function buscarCGMSub(lMostrar) {
            $('cgm_sub_descricao').value = '';
            if ( lMostrar ) {
                $('c140_cgmsubstituto').value = '';
                js_OpenJanelaIframe("",'func_nome','func_cgm.php?condition="somenteAtivos"&funcao_js=parent.js_preencheCGMSub|z01_numcgm|z01_nome','Pesquisa',true);
            } else {
                js_OpenJanelaIframe("",'func_nome','func_cgm.php?condition="somenteAtivos"&pesquisa_chave='+document.form1.c140_cgmsubstituto.value+'&funcao_js=parent.js_preencheCGMSub1','Pesquisa',false);
            }
        }

        function js_preencheCGMSub( iCodigo, sDescricao ) {
            document.form1.c140_cgmsubstituto.value = iCodigo;
            document.form1.cgm_sub_descricao.value  = sDescricao;
            func_nome.hide();
        }

        function js_preencheCGMSub1( lErro, sDescricao ) {
            if ( !lErro ) {
                document.form1.cgm_sub_descricao.value = sDescricao;
            } else {
                document.form1.c140_cgmsubstituto.value = "";
                document.form1.cgm_sub_descricao.value  = sDescricao;
            }
        }

        function js_incluirCadastro()
        {
            if($F('c140_orgao').trim() == '' || $F('c140_unidade').trim() == ''){
                alert('O campo Órgão/Unidade é necessário para continuar.');
                return;
            }

            if($F('c140_cgm').trim() == ''){
                alert('O campo CGM é necessário para continuar.');
                return;
            }

            if($F('c140_cgmsubstituto').trim() == '' && $F('c140_substituto') == 't'){
                alert('O campo CGM do Substituto é necessário para continuar.');
                return;
            }

            if($F('c140_cgmsubstituto').trim() == $F('c140_cgm').trim()){
                alert('Os campos CGM e CGM Substituto não podem ser iguais.');
                return;
            }

            if($F('c140_datainicio').trim() == ''){
                alert('O campo Data Início é necessário para continuar.');
                return;
            }

            if($F('c140_datainiciosub').trim() == '' && $F('c140_substituto') == 't'){
                alert('O campo Data Inicio Substituto é necessário para continuar.');
                return;
            }

            if($F('c140_datafimsub').trim() == '' && $F('c140_substituto') == 't'){
                alert('O campo Data Fim Substituto é necessário para continuar.');
                return;
            }

            const parametros = new FormData(formulario);
            parametros.append('exec', 'salvarResponsavel');
            
            HttpClient.post(sUrlRPC, {body: parametros}).then((response) => {

                $('c140_cgm').value             = '';
                $('cgm_descricao').value        = '';
                $('c140_cgmsubstituto').value   = '';
                $('cgm_sub_descricao').value    = '';
                $('c140_principal').value       = 'f';
                $('c140_substituto').value      = 'f';
                $('c140_datainicio').value      = '';
                $('c140_datafim').value         = '';
                $('c140_tipoatojuridico').value = '1';
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
            oGridResponsavel.clearAll(true);
            res.each(function (oRow, id) {
                aLinha    = new Array();
                aLinha[0] = oRow.cgm;
                aLinha[1] = oRow.nome;
                aLinha[2] = oRow.orgao.padStart(2, '0')+' / '+oRow.unidade.padStart(2, '0');
                aLinha[3] = oRow.principal == 't' ? 'SIM' : 'NÃO';
                aLinha[4] = oRow.substituto == 't' ? 'SIM' : 'NÃO';
                aLinha[5] = oRow.cgmsub == 0 ? '-' : oRow.cgmsub;
                aLinha[6] = oRow.cgmsub == 0 ? '-' : oRow.nomesub;
                aLinha[7] = oRow.periodo; 
                aLinha[8] = oRow.periodosub == '' ? '-' : oRow.periodosub; 
                aLinha[9] = tipoato[oRow.tipoato];
                aLinha[10] = oRow.ativo == 'f' ? '-' : "<input type='button' value='E' onclick='inativarResponsavel("+oRow.sequencial+")'>";
                aLinha[11] = `<span class="status-${oRow.ativo}">${oRow.tipoato}</span>`;
                
                oGridResponsavel.addRow(aLinha);
            });
            oGridResponsavel.renderRows();

            colorGrid();
        }

        function colorGrid() {
            document.querySelectorAll('.status-f').forEach((e)=>{
                e.closest('tr').setAttribute('style','background: #ee00001f')
            })
        }

        function toogleSub() {
            var ct = document.querySelectorAll('.ctSub');
            if ($F('c140_substituto') == 't') {
                ct[0].removeAttribute('hidden');
                ct[1].removeAttribute('hidden');
            } else {
                ct[0].setAttribute('hidden','hidden');
                ct[1].setAttribute('hidden','hidden');
                $('c140_cgmsubstituto').value   = '';
                $('cgm_sub_descricao').value    = '';
                $('c140_datainiciosub').value    = '';
                $('c140_datafimsub').value    = '';
            }
        }

        function js_main() {
            oGridResponsavel              = new DBGrid('oGridResponsavel');
            oGridResponsavel.nameInstance = 'oGridResponsavel';
            oGridResponsavel.setCellAlign(new Array('center', 'left', 'center', 'center', 'center', 'center', 'left', 'center', 'center', 'center', 'center', 'center'));
            oGridResponsavel.setCellWidth(
                new Array('40px', '180px', '40px', '40px', '50px', '50px', '180px', '80px', '80px', '50px', '30px', '0')
            );
            oGridResponsavel.setHeader(
                new Array('CGM', 'Nome', 'Org/Uni', 'Principal' , 'Substituto', 'CGM-Sub', 'Nome-Sub', 'Período', 'Período-Sub', 'Tipo Ato', 'Ação', 'id_tipo')
            );
            oGridResponsavel.aHeaders[11].lDisplayed = false;
            oGridResponsavel.show($('ctnGridResponsavel'));

            $('incluirCadastro').onclick=js_incluirCadastro;

            getRows();
        }

        function getRows() {
            js_divCarregando("Pesquisando ..." , 'msgBox');

            const parametros = new FormData(formulario);
            parametros.append('exec', 'getResponsavel');
            parametros.append('orgao', $F('c140_orgao'));
            parametros.append('unidade', $F('c140_unidade'));
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


        function inativarResponsavel(id) {
            js_divCarregando("Pesquisando ..." , 'msgBox');

            const parametros = new FormData(formulario);
            parametros.append('exec', 'inativarResponsavel');
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
