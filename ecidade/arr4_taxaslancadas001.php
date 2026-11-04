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

use App\Domain\Tributario\Arrecadacao\Repositories\GrupoTaxasRepository;
use App\Domain\Tributario\Arrecadacao\Services\GrupoTaxasService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

db_postmemory($_GET);

$clrotulo = new rotulocampo;
$clrotulo->label("ar44_sequencial");
$clrotulo->label("ar44_descricao");
$clrotulo->label("ar44_valorinflator");
$clrotulo->label("ar44_inflator");
$clrotulo->label("ar44_diasvencimento");
$clrotulo->label("ar44_tipo");
$clrotulo->label("ar44_receitaxaexpediente");
$clrotulo->label("ar44_valortaxaexpediente");
$clrotulo->label("ar44_datavigencia");
$clrotulo->label("ar44_departamento");
$clrotulo->label("ar44_procedencia");
$clrotulo->label("ar44_receita");
$clrotulo->label("ar44_emissaoweb");
$clrotulo->label("ar44_recursoadm");
$clrotulo->label("ar44_permitesubtaxas");
$clrotulo->label("ar44_origem");

$oDaoNumpref = new cl_numpref;
$iInstit = db_getsession("DB_instit");
$iAnoUsu = db_getsession("DB_anousu");
$sSqlPermissaoUsoGrupoTaxas = $oDaoNumpref->sql_query_file($iAnoUsu,$iInstit,'k03_usagrupotaxa');
$rsPermissaoUsoGrupoTaxas = db_query($sSqlPermissaoUsoGrupoTaxas);
$permissaoUsoGrupoTaxas = db_utils::getCollectionByRecord($rsPermissaoUsoGrupoTaxas);
$permissaoUsoGrupoTaxas = $permissaoUsoGrupoTaxas[0]->k03_usagrupotaxa == 't';
?>
<html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
        <link href="estilos.css" rel="stylesheet" type="text/css">
        <style>
            #ar44_datavigencia {
                height: 18px !important;
                border-radius: 2px !important;
                border: 1px solid #999 !important;
            }

            select {
                width: 49px !important;
            }

            #ctnGridTaxas {
                width: 800px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <form name="form1" method="post">
                <input type="hidden" name="ar44_sequencial" id="ar44_sequencial">
                <fieldset>
                    <legend>Taxa</legend>
                    <table class="form-container">
                        <tr>
                            <td title="<?= @$Tar44_descricao ?>" style="width: 70px;">
                                <?= @$Lar44_descricao ?>
                            </td>
                            <td>
                                <?php
                                db_input("ar44_descricao", 50, @$Iar44_descricao, "ar44_descricao", "text", 1);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tar44_valorinflator ?>" style="width: 70px;">
                                <?= @$Lar44_valorinflator ?>
                            </td>
                            <td>
                                <?php
                                db_input("ar44_valorinflator", 5, @$Iar44_valorinflator, "ar44_valorinflator", "text", 1);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tar44_inflator ?>">
                                <?php
                                db_ancora(@$Lar44_inflator, "js_pesquisaInflator(true);", 4);
                                ?>
                            </td>
                            <td>
                                <?php
                                db_input("ar44_inflator", 5, @$Iar44_inflator, true, "text", 4, "onchange='js_pesquisaInflator(false);'");
                                db_input("i01_descr", 40, false, true, "text");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tar44_diasvencimento ?>">
                                <?= @$Lar44_diasvencimento ?>
                            </td>
                            <td>
                                <?php
                                db_input("ar44_diasvencimento", 5, @$Iar44_diasvencimento, true, "text", 1);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tar44_tipo ?>">
                                <?= @$Lar44_tipo ?>
                            </td>
                            <td>
                                <select name="ar44_tipo" id="ar44_tipo" style="width: 70px !important;">
                                    <option value="0">Fixa</option>
                                    <option value="1">Variável</option>
                                    <option value="2">Percentual</option>
                                    <option value="3">Fixo Sobre Faixa</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tar44_receitaxaexpediente ?>">
                                <?php
                                db_ancora(@$Lar44_receitaxaexpediente, "js_pesquisaReceita(true);", 4);
                                ?>
                            </td>
                            <td>
                                <?php
                                db_input("ar44_receitaxaexpediente", 5, @$Iar44_receitaxaexpediente, true, "text", 4, "onchange='js_pesquisaReceita(false);'");
                                db_input("k02_descr", 40, false, true, "text");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tar44_valortaxaexpediente ?>" style="width: 70px;">
                                <?= @$Lar44_valortaxaexpediente ?>
                            </td>
                            <td>
                                <?php
                                db_input("ar44_valortaxaexpediente", 5, @$Iar44_valortaxaexpediente, "ar44_valortaxaexpediente", "text", 1);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tar44_datavigencia ?>" style="width: 70px;">
                                <?= @$Lar44_datavigencia ?>
                            </td>
                            <td>
                                <?php
                                db_inputdata("ar44_datavigencia", "", "", "", true, 'text', 1)
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                db_ancora("<strong>Departamentos</strong>", "js_pesquisaDepartamento(true);", 4);
                                db_input("departamentos", 5, false, "departamentos", "hidden", 1);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="Seleciona se deve ou gerar um débito">
                                <strong>Gera Débito:</strong>
                            </td>
                            <td>
                                <select name="geraDebito" id="geraDebito" onchange="js_geraDebito(this.value);">
                                    <option value="true">Sim</option>
                                    <option value="false">Não</option>
                                </select>
                            </td>
                        </tr>
                        <tr id="trProcedenciaDebito">
                            <td title="<?= @$Tar44_procedencia ?>">
                                <?php
                                db_ancora(@$Lar44_procedencia, "js_pesquisaProcedencia(true);", 4);
                                ?>
                            </td>
                            <td>
                                <?php
                                db_input("ar44_procedencia", 5, @$Iar44_procedencia, true, "text", 4, "onchange='js_pesquisaProcedencia(false);'");
                                db_input("dv09_descr", 40, false, true, "text");
                                ?>
                            </td>
                        </tr>
                        <tr id="trReceitaDebito">
                            <td title="<?= @$Tar44_receita ?>">
                                <?php
                                db_ancora(@$Lar44_receita, "js_pesquisaReceita(true, 1);", 4);
                                ?>
                            </td>
                            <td>
                                <?php
                                db_input("ar44_receita", 5, @$Iar44_receita, true, "text", 4, "onchange='js_pesquisaReceita(false, 1);'");
                                db_input("k02_descr1", 40, false, true, "text");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tar44_emissaoweb ?>">
                                <?= @$Lar44_emissaoweb ?>
                            </td>
                            <td>
                                <select name="ar44_emissaoweb" id="ar44_emissaoweb">
                                    <option value="f">Não</option>
                                    <option value="t">Sim</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tar44_recursoadm ?>">
                                <?= @$Lar44_recursoadm ?>
                            </td>
                            <td>
                                <select name="ar44_recursoadm" id="ar44_recursoadm">
                                    <option value="f">Não</option>
                                    <option value="t">Sim</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tar44_origem ?>">
                                <?= @$Lar44_origem ?>
                            </td>
                            <td>
                                <select name="ar44_origem" id="ar44_origem" style="width: 100px !important;">
                                    <option value="T">Todas</option>
                                    <option value="C">CGM</option>
                                    <option value="M">Matricula</option>
                                    <option value="I">Inscrição</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                db_ancora("<strong>Campos Dinâmicos</strong>", "js_cadastraCamposDinamicos();", 4);
                                ?>
                            </td>
                        </tr>
                        <tr id="trPermiteSubtaxas">
                            <td title="<?= $Tar44_permitesubtaxas ?>">
                                <?= $Lar44_permitesubtaxas ?>
                            </td>
                            <td>
                                <select <?= $permissaoUsoGrupoTaxas ? 'disabled' : '' ?> name="ar44_permitesubtaxas" id="ar44_permitesubtaxas" onchange="js_mostraIframeSubtaxas(this.value);">
                                    <option value="f">Não</option>
                                    <option value="t">Sim</option>
                                </select>
                            </td>
                        </tr>
                    </table>

                    <?php
                    if (!$permissaoUsoGrupoTaxas) {
                    ?>

                    <div class="container" id="trIframeSubtaxas" >
                        <?php
                            $cliframe_custasliberadas  = new cl_iframe_seleciona;
                            $oDaoTaxasLancadas         = new cl_taxaslancadas();
                            $sSqlPesquisaTaxasLancadas = $oDaoTaxasLancadas->sql_query();
                            
                            if (isset($_GET['ar44_sequencial']) && !empty(isset($_GET['ar44_sequencial']))) {
                                $sequencialTaxa = $_GET['ar44_sequencial'];
                                $sSqlPesquisaTaxasLancadas = $sSqlPesquisaTaxasLancadas . " where ar44_sequencial <> $sequencialTaxa ";
                            }

                            $cliframe_custasliberadas->sql           = $sSqlPesquisaTaxasLancadas;
                            $cliframe_custasliberadas->campos        = "ar44_sequencial,ar44_descricao,ar44_datavigencia";
                            $cliframe_custasliberadas->legenda       = "Subtaxas";
                            $cliframe_custasliberadas->iframe_height = "200";
                            $cliframe_custasliberadas->iframe_width  = "400";
                            $cliframe_custasliberadas->iframe_nome   = "listataxas";
                            $cliframe_custasliberadas->chaves        = 'ar44_sequencial';
                            $cliframe_custasliberadas->iframe_seleciona($db_opcao);
				        ?>
                    </div>

                    <?php
                    } else {
                    ?>

                    <div class="container" id="trIframeGrupoTaxastaxas" >
                        <?php
                            $cliframe_grupotaxas  = new cl_iframe_seleciona;
                            $grupoTaxasRepository = new GrupoTaxasRepository();
                            $oDaoGrupoTaxas       = new GrupoTaxasService($grupoTaxasRepository);
                            $sSqlPesquisaGrupoTaxas = $oDaoGrupoTaxas->getSqlQueryGrupoTaxas();

                            if (isset($_GET['ar55_sequencial']) && !empty(isset($_GET['ar55_sequencial']))) {
                                $sequencialGrupoTaxas = $_GET['ar55_sequencial'];
                                $sSqlPesquisaGrupoTaxas = $sSqlPesquisaGrupoTaxas . " where ar55_sequencial <> $sequencialGrupoTaxas ";
                            }
                            

                            if (isset($_GET['ar44_sequencial']) && !empty(isset($_GET['ar44_sequencial']))) {
                                $sequencialTaxa = $_GET['ar44_sequencial'];
                                $cliframe_grupotaxas->sql_marca = $grupoTaxasRepository->sqlBuscaTaxasVinculadas($sequencialTaxa);
                            }

                            $cliframe_grupotaxas->sql           = $sSqlPesquisaGrupoTaxas;
                            $cliframe_grupotaxas->campos        = "ar55_sequencial,ar55_descricao,ar55_datalimite";
                            $cliframe_grupotaxas->legenda       = "Grupo de Taxas";
                            $cliframe_grupotaxas->iframe_height = "200";
                            $cliframe_grupotaxas->iframe_width  = "400";
                            $cliframe_grupotaxas->iframe_nome   = "listagrupotaxas";
                            $cliframe_grupotaxas->chaves        = 'ar55_sequencial';
                            $cliframe_grupotaxas->dbscript      = "onClick='parent.js_desabilitaDataLimite()'";
                            $cliframe_grupotaxas->iframe_seleciona($db_opcao);
				        ?>
                    </div>

                    <?php
                    }
                    ?>
                </fieldset>
                <input name="salvar" id="salvar" type="button" onclick="js_salvar();" value="Salvar">
                <input name="cancelar" id="cancelar" type="button" onclick="js_limpa();" value="Cancelar" style="display: none;">
                <?php if ($db_opcao == 2) : ?>
                    <input name="pesquisar" id="pesquisar" type="button" onclick="js_pesquisaTaxas();" value="Pesquisar">
                <?php endif; ?>
            </form>
        </div>
        <div class="container">
            <div id="ctnGridTaxas"></div>
        </div>
        <?php db_menu(); ?>
    </body>
</html>
<script>
    var campoReceita = 0;
    var aCampos = [];
    let gruposTaxa = [];

    js_getGrupos();

    function js_getGrupos() {
        const requestBody = {
            page: 1,
            porPagina: 100
        };
        const rota = 'v4/api/tributario/arrecadacao/grupotaxas/buscar';
        CurrentWindow.axios.post(rota, requestBody).then(response => {
            gruposTaxa = response.data.data.data;
        })
    }

    async function js_desabilitaDataLimite() {
        const dataLimite = document.getElementById('ar44_datavigencia');
        const botaoSelecionaData = document.getElementById('dtjs_ar44_datavigencia');
        const grupoTaxasHabilitado = <?= $permissaoUsoGrupoTaxas ? 'true' : 'false' ?>;
        let possuiGrupoComDataLimite = false;
        
        for (const sequencialGrupo of js_getGruposTaxasSelecionados()) {
            const dadosGrupo = gruposTaxa.find(item => Number(item.ar55_sequencial) === Number(sequencialGrupo));
            const grupoSelecionadoPossuiDataLimite = dadosGrupo.ar55_datalimite != null 
                                                        && dadosGrupo.ar55_datalimite.trim() != '';

            if (grupoSelecionadoPossuiDataLimite) {
                possuiGrupoComDataLimite = true;
                break;
            }
        }

        if (possuiGrupoComDataLimite) {
            dataLimite.value = '';
            dataLimite.setAttribute('disabled', 'true') 
            dataLimite.setAttribute('readonly', 'true') 
            botaoSelecionaData.setAttribute('disabled', 'true') 
            botaoSelecionaData.setAttribute('readonly', 'true') 
        } else {
            dataLimite.removeAttribute('disabled', 'true') 
            dataLimite.removeAttribute('readonly', 'true') 
            botaoSelecionaData.removeAttribute('disabled', 'true') 
            botaoSelecionaData.removeAttribute('readonly', 'true') 
        }
    }

    <?php if ($db_opcao == 2 && !isset($alterar)) {?>
        js_pesquisaTaxas();
    <?php } else if (isset($_GET['ar44_sequencial'])) {?>
        js_buscarTaxa(<?= $_GET['ar44_sequencial'] ?>);
        db_iframe_taxaslancadas.hide();
    <?php }; ?>

    function js_pesquisaTaxas()
    {
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_taxaslancadas','func_taxaslancadas.php?funcao_js=parent.js_mostraTaxaLancada|ar44_sequencial','Pesquisa',true);
    }

    function js_mostraTaxaLancada(ar44_sequencial)
    {
        <?php
            if (!$_GET['ar44_sequencial']) {
                echo "location.href = '" . basename($_SERVER["PHP_SELF"]) . '?' . $_SERVER["QUERY_STRING"] ."&alterar=true&ar44_sequencial=' + ar44_sequencial;";
            } else {
                echo "
                    js_buscarTaxa(ar44_sequencial);
                    db_iframe_taxaslancadas.hide();
                ";
            }
        ?>
    }

    function js_pesquisaInflator(mostra)
    {
        if(mostra==true){
            js_OpenJanelaIframe("CurrentWindow.corpo","db_iframe_inflan","func_inflan.php?funcao_js=parent.js_mostraInflator1|i01_codigo|i01_descr","Pesquisa",true,"20");
        }else{
            if(document.form1.ar44_inflator.value != ""){ 
                js_OpenJanelaIframe("CurrentWindow.corpo","db_iframe_inflan","func_inflan.php?pesquisa_chave="+document.form1.ar44_inflator.value+"&funcao_js=parent.js_mostraInflator","Pesquisa",false,"20");
            }else{
                document.form1.ar44_inflator.value = ""; 
            }
        }
    }

    function js_mostraInflator(chave,erro)
    {
        document.form1.i01_descr.value = chave;

        if(erro==true){ 
            document.form1.ar44_inflator.focus(); 
            document.form1.ar44_inflator.value = ""; 
        }
    }

    function js_mostraInflator1(chave1,chave2)
    {
        document.form1.ar44_inflator.value = chave1;
        document.form1.i01_descr.value = chave2;
        db_iframe_inflan.hide();
    }

    function js_pesquisaReceita(mostra, campo = 0)
    {
        campoReceita = campo;

        if (campoReceita == 1 && !js_verificaProcedencia()) {
            return js_verificaProcedencia();
        }

        if(mostra==true){
            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_receita','func_tabrec_todas.php?funcao_js=parent.js_mostraReceita1|k02_codigo|k02_descr&dtLimite=true','Pesquisa',true);
        }else{
            var receita = document.form1.ar44_receitaxaexpediente.value;

            if (campoReceita == 1) {
                receita = document.form1.ar44_receita.value;
            }

            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_receita','func_tabrec.php?pesquisa_chave='+receita+'&funcao_js=parent.js_mostraReceita&dtLimite=true','Pesquisa',false);
        }
    }

    function js_mostraReceita(chave,erro)
    {
        if (campoReceita == 0) {
            document.form1.k02_descr.value = chave;

            if(erro==true){
                document.form1.ar44_receitaxaexpediente.focus();
                document.form1.ar44_receitaxaexpediente.value = '';
            }
        } else {
            document.form1.k02_descr1.value = chave;

            if(erro==true){
                document.form1.ar44_receita.focus();
                document.form1.ar44_receita.value = '';
            }
        }
    }

    function js_mostraReceita1(chave1,chave2)
    {
        if (campoReceita == 0) {
            document.form1.ar44_receitaxaexpediente.value = chave1;
            document.form1.k02_descr.value = chave2;
        } else {
            document.form1.ar44_receita.value = chave1;
            document.form1.k02_descr1.value = chave2;
        }

        db_iframe_receita.hide();
    }

    function js_pesquisaDepartamento(mostra)
    {
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_departamento','arr4_taxaslancadas002.php?departamentos='+document.form1.departamentos.value,'Pesquisa',true);
    }

    function js_ocultaDepartamento ()
    {
        db_iframe_departamento.hide();
    }

    function js_pesquisaProcedencia(mostra)
    {
        if (!js_verificaReceita()) {
            return false;
        }

        if(mostra==true){
            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe','func_procdiver.php?funcao_js=parent.js_mostraProcedencia|dv09_procdiver|dv09_descr&excecao=dv09_tipo&valorExcecao=13,43,45,47,49','Pesquisa',true);
        }else{
            if (document.form1.ar44_procedencia.value != "") {
                js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe','func_procdiver.php?pesquisa_chave='+document.form1.ar44_procedencia.value+'&funcao_js=parent.js_mostraProcedencia1&excecao=dv09_tipo&valorExcecao=13,43,45,47,49','Pesquisa',false);
            } else {
                document.form1.ar44_procedencia.value = '';            
            }
        }
    }

    function js_mostraProcedencia(chave1,chave2)
    {
        document.form1.ar44_procedencia.value = chave1;
        document.form1.dv09_descr.value = chave2;
        db_iframe.hide();
    }

    function js_mostraProcedencia1(chave,erro)
    {
        document.form1.dv09_descr.value = chave;

        if(erro==true){ 
            document.form1.ar44_procedencia.focus(); 
            document.form1.ar44_procedencia.value = ''; 
            document.form1.dv09_descr.value = ''; 
        }
    }

    function js_geraDebito(libera)
    {
        if (libera == "true") {
            document.getElementById("trReceitaDebito").hide();
            document.getElementById("trProcedenciaDebito").show();
            document.getElementById("trPermiteSubtaxas").show();

            document.getElementById("ar44_procedencia").setAttribute("isMandatory", "true");
            document.getElementById("ar44_receita").removeAttribute("isMandatory");

            document.form1.ar44_receita.value = "";
        } else {
            document.getElementById("trProcedenciaDebito").hide();
            document.getElementById("trPermiteSubtaxas").hide();
            document.getElementById("trReceitaDebito").show();

            document.getElementById("ar44_receita").setAttribute("isMandatory", "true");
            document.getElementById("ar44_procedencia").removeAttribute("isMandatory");

            document.form1.ar44_procedencia.value = "";
        }
    }

    function js_mostraIframeSubtaxas(libera)
    {
        if (libera == 'true' || libera == 't') {
            document.getElementById("trIframeSubtaxas").show();
        } else {
            document.getElementById("trIframeSubtaxas").hide();
        }
    }

    js_geraDebito('true');
    js_mostraIframeSubtaxas('false');
    const mensagem = "Preencha somente a procedência ou a receita.";

    function js_verificaProcedencia()
    {
        if (document.form1.ar44_procedencia.value != "") {
            alert(mensagem);

            document.form1.ar44_receita.value = "";
            document.form1.k02_descr1.value = "";

            return false;
        }

        return true;
    }

    function js_verificaReceita()
    {
        if (document.form1.ar44_receita.value != "") {
            alert(mensagem);

            document.form1.ar44_procedencia.value = "";
            document.form1.dv09_descr.value = "";

            return false;
        }

        return true;
    }

    function js_salvar()
    {
        if (js_verificaCampoObrigatorio()) {
            return false;
        }

        obj = document.form1;
        
        <?php if ($db_opcao == 2) : ?>
            if (obj.ar44_sequencial.value == "") {
                alert("Selecione uma taxa.");
                return false;
            }
        <?php endif; ?>

		var oParam = new Object();
		oParam.executa = "salvar";
        oParam.ar44_sequencial = obj.ar44_sequencial.value;
        oParam.ar44_descricao = obj.ar44_descricao.value;
        oParam.ar44_valorinflator = obj.ar44_valorinflator.value;
        oParam.ar44_inflator = obj.ar44_inflator.value;
        oParam.ar44_diasvencimento = obj.ar44_diasvencimento.value;
        oParam.ar44_tipo = obj.ar44_tipo.value;
        oParam.ar44_receitaxaexpediente = obj.ar44_receitaxaexpediente.value;
        oParam.ar44_valortaxaexpediente = obj.ar44_valortaxaexpediente.value;
        oParam.ar44_datavigencia = obj.ar44_datavigencia.value;
        oParam.departamentos = obj.departamentos.value;
        oParam.ar44_procedencia = obj.ar44_procedencia.value;
        oParam.ar44_receita = obj.ar44_receita.value;
        oParam.ar44_emissaoweb = obj.ar44_emissaoweb.value;
        oParam.ar44_recursoadm = obj.ar44_recursoadm.value;
        oParam.ar44_origem = obj.ar44_origem.value;
        oParam.ar44_permitesubtaxas = obj.ar44_permitesubtaxas.value;
        oParam.subTaxasSelecionadas = js_getSubTaxasSelecionadas();
        oParam.gruposTaxasSelecionados = js_getGruposTaxasSelecionados();
        oParam.camposDonamicos = JSON.stringify(aCampos);

		new AjaxRequest("arr4_taxaslancadas.RPC.php", oParam, js_getSalvar).execute();
	}

    function js_getSubTaxasSelecionadas() {
        const checkboxes = document.getElementById('ativ').contentWindow.document.body.children.form1.querySelectorAll('input');
        const sequencialSubtaxasSelecionadas = [];

        checkboxes.forEach(checkbox => {
            if (checkbox.checked && checkbox.value != '') {
                sequencialSubtaxasSelecionadas.push(checkbox.value);
            }
        });

        return sequencialSubtaxasSelecionadas;
    }

    function js_getGruposTaxasSelecionados() {
        const checkboxes = document.getElementById('ativ').contentWindow.document.body.children.form1.querySelectorAll('input');
        const sequencialSubtaxasSelecionadas = [];

        checkboxes.forEach(checkbox => {
            if (checkbox.checked && checkbox.value != '') {
                sequencialSubtaxasSelecionadas.push(checkbox.value);
            }
        });

        return sequencialSubtaxasSelecionadas;
    }

    function js_getSalvar(oRetorno)
    {
		alert(oRetorno.mensagem);

		if (oRetorno.erro) {
			return;
		}

		js_limpa();
	}

    function js_limpa()
    {
		location.href = "arr4_taxaslancadas001.php?db_opcao=<?= $db_opcao ?>";
    }

    function js_buscarTaxa(sequencial)
    {
        var oParam = new Object();
		oParam.executa = "buscar";
		oParam.ar44_sequencial = sequencial;

		new AjaxRequest("arr4_taxaslancadas.RPC.php", oParam, js_getBuscarTaxas).execute();
    }

    function js_getBuscarTaxas(oRetorno)
    {
        if (oRetorno.erro) {
            alert(oRetorno.mensagem);
			return;
        }

        const obj = document.form1;

        obj.i01_descr.value = "";
        obj.k02_descr.value = "";
        obj.dv09_descr.value = "";
        obj.k02_descr1.value = "";

        obj.ar44_sequencial.value = oRetorno.oTaxa.ar44_sequencial;
        obj.ar44_descricao.value = oRetorno.oTaxa.ar44_descricao;
        obj.ar44_valorinflator.value = oRetorno.oTaxa.ar44_valorinflator;
        obj.ar44_inflator.value = oRetorno.oTaxa.ar44_inflator;
        obj.ar44_diasvencimento.value = oRetorno.oTaxa.ar44_diasvencimento;
        obj.ar44_tipo.value = oRetorno.oTaxa.ar44_tipo;
        obj.ar44_receitaxaexpediente.value = oRetorno.oTaxa.ar44_receitaxaexpediente;
        obj.ar44_valortaxaexpediente.value = oRetorno.oTaxa.ar44_valortaxaexpediente;
        obj.ar44_datavigencia.value = oRetorno.oTaxa.ar44_datavigencia;
        obj.departamentos.value = oRetorno.oTaxa.departamentos;
        obj.geraDebito.value = oRetorno.oTaxa.geraDebito;
        obj.ar44_procedencia.value = oRetorno.oTaxa.ar44_procedencia;
        obj.ar44_receita.value = oRetorno.oTaxa.ar44_receita;
        obj.ar44_emissaoweb.value = oRetorno.oTaxa.ar44_emissaoweb;
        obj.ar44_recursoadm.value = oRetorno.oTaxa.ar44_recursoadm;
        obj.ar44_origem.value = oRetorno.oTaxa.ar44_origem;

        <?php
        if (!$permissaoUsoGrupoTaxas) {
        ?>
            obj.ar44_permitesubtaxas.value = oRetorno.oTaxa.ar44_permitesubtaxas;
        <?php
        }
        ?>
        aCampos = oRetorno.oTaxa.camposDinamicos;

        document.getElementById("cancelar").show();

        js_carregaDescricoes();
        js_mostraIframeSubtaxas(obj.ar44_permitesubtaxas.value);
        js_setSubTaxasSelecionadas(oRetorno.oTaxa.subTaxas);
        js_setGruposSelecionados(oRetorno.oTaxa.grupos);
    }

    function js_setSubTaxasSelecionadas(subTaxas) {
        const subTaxasSelecionadas = subTaxas.map(subtaxa => subtaxa.ar44_sequencial);
        const checkboxes = document.getElementById('ativ').contentWindow.document.body.children.form1.querySelectorAll('input');

        checkboxes.forEach(checkbox => {
            if (subTaxasSelecionadas.find(sequencialSubTaxa => sequencialSubTaxa.toString() === checkbox.value.toString())) {
                checkbox.checked = true;
            }
        });
    }

    function js_setGruposSelecionados(grupos) {
        const checkboxes = document.getElementById('ativ').contentWindow.document.body.children.form1.querySelectorAll('input');

        checkboxes.forEach(checkbox => {
            if (grupos.find(sequencialGrupo => sequencialGrupo.toString() === checkbox.value.toString())) {
                checkbox.checked = true;
            }
        });
    }

    function js_carregaDescricoes()
    {
        const obj = document.form1;

        js_pesquisaInflator(false);
        
        if (obj.ar44_receitaxaexpediente.value != "") {
            js_pesquisaReceita(false);   
        }

        if (obj.ar44_procedencia.value != "") {
            js_pesquisaProcedencia(false);
        } else {
            if (obj.ar44_receita.value != "") {
                setTimeout(() => {
                    js_pesquisaReceita(false, 1);
                }, 500);
            }
        }

        js_geraDebito(obj.geraDebito.value);
    }

    function js_cadastraCamposDinamicos()
    {
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_campos_dinamicos','arr4_taxaslancadas007.php','Campos Dinâmicos',true);
    }

    function getCampos()
    {
        return aCampos;
    }

    function setCampos(oCampo2)
    {
        const campoExiste = aCampos.find(function (oCampo) {
            if (oCampo.ar47_codcam == oCampo2.ar47_codcam) {
                return true;
            }
        });

        if (campoExiste != undefined) {
            aCampos.forEach(function (oCampo, key) {
                if (oCampo.ar47_codcam == oCampo2.ar47_codcam) {
                    aCampos[key].ar47_codcam = oCampo2.ar47_codcam;
                    aCampos[key].nomecam = oCampo2.nomecam;
                    aCampos[key].ar47_obrigatorio = oCampo2.ar47_obrigatorio;
                    aCampos[key].ar47_tipocampo = oCampo2.ar47_tipocampo;
                    aCampos[key].ar47_valordefault = oCampo2.ar47_valordefault;
                }
            });
        } else {
            aCampos.push(oCampo2);
        }
    }

    function js_removerCampo(ar47_codcam)
    {
        for (var i = 0; i < aCampos.length; i++) {
            if (aCampos[i].ar47_codcam == ar47_codcam) {
                aCampos.splice(i, 1);
                break;
            }
        }
    }

    function js_alterarCampo(ar47_codcam)
    {
        var oCampos = "";

        for (var i = 0; i < aCampos.length; i++) {
            if (aCampos[i].ar47_codcam == ar47_codcam) {
                aCampos[i].key = i;
                oCampos = aCampos[i];
                break;
            }
        }

        return oCampos;
    }

    <?php
        if ($_GET['ar44_sequencial']) {
            echo "js_mostraTaxaLancada(". $_GET['ar44_sequencial'] .");";
        }
    ?>
</script>
