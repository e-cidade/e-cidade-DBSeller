<?php
    /**
     *     E-cidade Software Publico para Gestao Municipal
     *  Copyright (c) 2018  DBSeller Servicos de Informatica
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

    //MODULO: projetos

    $clobras->rotulo->label();
    $clobraspropri->rotulo->label();
    $clobraslote->rotulo->label();
    $clobraslotei->rotulo->label();
    $clobrasender->rotulo->label();
    $clobrasresp->rotulo->label();

    $clrotulo = new rotulocampo;

    $clrotulo->label("ob02_descr");
    $clrotulo->label("ob01_nomeobra");
    $clrotulo->label("z01_nome");
    $clrotulo->label("j34_setor");
    $clrotulo->label("j34_quadra");
    $clrotulo->label("j34_lote");
    $clrotulo->label("j14_nome");
    $clrotulo->label("j13_descr");
    $clrotulo->label("ob15_crea");
    $clrotulo->label("j01_matric");
    $clrotulo->label("p58_requer");
    $clrotulo->label("ob01_responsavelprojeto");
    $clrotulo->label("ob01_arquitetoobra");
    $clrotulo->label("ob01_numeroartprojeto");
    $clrotulo->label("ob01_numerorrtprojeto");
    $clrotulo->label("ob01_numeroarttecnico");
    $clrotulo->label("ob01_numerorrttecnico");

    if (isset($profissao_tecnicos) && $profissao_tecnicos == "") {
        db_msgbox('Responsável pelo Projeto sem profissão cadastrada.');
    } else if (isset($profissao_resp) && $profissao_resp == "") {
        db_msgbox('Responsável técnico sem profissão cadastrada.');
    }

?>

<head>
  <link rel="stylesheet" type="text/css" href="estilos/grid.style.css" />
  <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
</head>

<form class="container" name="form1" id="form1" style="margin-top:10px;">
    <fieldset>
        <legend>Obra</legend>
        <table class="form-container">
            <tr>
                <td nowrap title="<?=@$Tob01_codobra?>">
                    <?=@$Lob01_codobra?>
                </td>
                <td>
                    <?php
                        db_input('ob01_codobra',10,$Iob01_codobra,true,'text',3,"");
                        db_input('ob01_regular',10,$Iob01_regular,true,'hidden',3,"");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?=@$Tob01_nomeobra?>">
                    <?=@$Lob01_nomeobra?>
                </td>
                <td>
                    <?php
                        db_input('ob01_nomeobra', 54,$Iob01_nomeobra,true,'text',$db_opcao,"")
                    ?>
                </td>
            </tr>
            <tr>
                <td title="<?=@$Tob01_dtobra?>">
                    <?=@$Lob01_dtobra?>
                </td>
                <td>
                    <?php
                        db_inputdata('ob01_dtobra', @$ob01_dtobra_dia, @$ob01_dtobra_mes, @$ob01_dtobra_ano, true, 'text', $db_opcao)
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?=@$Tob01_tiporesp?>">
                    <?=@$Lob01_tiporesp?>
                </td>
                <td id="tipo_responsavel">
                    <?php
                        $result = $clobrastiporesp->sql_record($clobrastiporesp->sql_query_file());
                        db_selectrecord("ob01_tiporesp",$result,true,$db_opcao,"","ob01_tiporesp","",""," js_exibir_ocultar(this.value); ");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?=@$Tob03_numcgm?>">
                    <?php
                        db_ancora(@$Lob03_numcgm,"js_pesquisaob03_numcgm(true);",$db_opcao);
                    ?>
                </td>
                <td>
                    <?php
                        db_input('ob03_numcgm',10,$Iob03_numcgm,true,'text',3," onchange='js_pesquisaob03_numcgm(false);'")
                    ?>
                    <?php
                        db_input('z01_nome',40,$Iz01_nome,true,'text',3,'')
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <div id="divLancadorCGM"></div>
                </td>
            </tr>
            <tr>
            <?php
                if(@$ob01_regular == 't'){
                    ?>
                    <tr>
                        <td title="<?=$Tj01_matric?>">
                            <?php
                                db_ancora($Lj01_matric, 'js_pesquisaMatricula(true)', $db_opcao)
                            ?>
                        </td>
                        <td>
                            <?php
                                db_input('j01_matric', 10, $Ij01_matric, true, 'text', $db_opcao, "onchange='js_pesquisaMatricula(false)'");

                                db_input('z01_nome_matricula'  , 40, $Ij01_matric, true, 'text', 3);
                            ?>
                        </td>
                    </tr>
                    <?php
                }else{
                    db_input('ob05_idbql',6,$Iob05_idbql,true,'hidden',3," onchange='js_pesquisaob05_idbql(false);'");
                    ?>
                    <tr>
                        <td>
                            Localização:
                        </td>
                        <td colspan="2" nowrap title="<?=@$Tob06_setor?>" >
                            <?=@$Lob06_setor?>
                            <?php
                                db_input('ob06_setor',9,$Iob06_setor,true,'text',$db_opcao,"")
                            ?>
                            <?=@$Lob06_quadra?>
                            <?php
                                db_input('ob06_quadra',9,$Iob06_quadra,true,'text',$db_opcao,"")
                            ?>
                            <?=@$Lob06_lote?>
                            <?php
                                db_input('ob06_lote',9,$Iob06_lote,true,'text',$db_opcao,"")
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            ?>
            <tr>
                <td nowrap title="<?=@$Tob10_numcgm?>">
                    <?php
                        db_ancora("Responsável Execução da Obra:","js_pesquisaob10_numcgm(true);",$db_opcao);
                    ?>
                </td>
                <td>
                    <?php
                        db_input('ob10_numcgm',10,$Iob10_numcgm,true,'text',3," onchange='js_pesquisaob10_numcgm(false);'");
                    ?>
                    <?php
                        db_input('z01_nome',40,$Iz01_nome,true,'text',3,'','z01_nomeresp');
                    ?>
                </td>
            </tr>
            <?php
                db_input('profissaoProjeto', '','',true,'hidden');
                db_input('profissaoTecnico', '','',true,'hidden');
            ?>
            <tr>
                <td nowrap title="Responsável pelo Projeto: ">
                    <?php
                        db_ancora("Responsável pelo Projeto:","js_pesquisaob01_tecnico(true);",$db_opcao);
                    ?>
                </td>
                <td>
                    <?php
                        db_input('ob15_crea'      , 10, $Iob15_crea,true, 'text'  , 3, '');
                        db_input('z01_nometec'    , 40, $Iz01_nome ,true, 'text'  , 3, '');
                        db_input('ob15_sequencial', 40, ""         ,true, 'hidden', 3, '');
                        db_input('ob20_sequencial', 40, ""         ,true, 'hidden', 3, '');
                    ?>
                </td>
            </tr>
            <tr id="idNumArtProjeto" title="<?=@$Tob01_numeroartprojeto?>" style='display:none'>
                <td nowrap>
                    <?= @$Lob01_numeroartprojeto ?>
                </td>
                <td>
                    <?php
                        db_input('ob01_numeroartprojeto', 10, @$Iob01_numeroartprojeto, true, 'text', $db_opcao, "", "", "#FFF");
                    ?>
                </td>
            </tr>
            <tr id="idNumRrtProjeto" title="<?=@$Tob01_numerorrtprojeto?>" style='display:none'>
                <td nowrap>
                    <?= @$Lob01_numerorrtprojeto ?>
                </td>
                <td>
                    <?php
                        db_input('ob01_numerorrtprojeto', 10, @$Iob01_numerorrtprojeto, true, 'text', $db_opcao, "", "", "#FFF");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="Responsável Técnico">
                    <?php
                        db_ancora("Responsável Técnico: ", "pesquisaArquitetoObra();", $db_opcao);
                    ?>
                </td>
                <td>
                    <?php
                        db_input('crea_arquiteto', 10, '', true, 'text', 3);
                        db_input('ob01_arquitetoobra', 10, $Iob01_arquitetoobra, true, 'hidden', 3);
                        db_input('nome_arquiteto', 40, '', true, 'text', 3);
                    ?>
                </td>
            </tr>
            <tr id="idNumArtTecnico" title="<?=@$Tob01_numeroarttecnico?>" style='display:none'>
                <td nowrap>
                    <?= @$Lob01_numeroarttecnico ?>
                </td>
                <td>
                    <?php
                        db_input('ob01_numeroarttecnico', 10, @$Iob01_numeroarttecnico, true, 'text', $db_opcao, "", "", "#FFF");
                    ?>
                </td>
            </tr>
            <tr id="idNumRrtTecnico" title="<?=@$Tob01_numerorrttecnico?>" style='display:none'>
                <td nowrap>
                    <?= @$Lob01_numerorrttecnico ?>
                </td>
                <td>
                    <?php
                        db_input('ob01_numerorrttecnico', 10, @$Iob01_numerorrttecnico, true, 'text', $db_opcao, "", "", "#FFF");
                    ?>
                </td>
            </tr>
            <tr>
                <td title="Processo existente no sistema">
                    Processo do Sistema
                </td>
                <td>
                    <?php
                        db_select('ob01_processosistema', array('S'=>'SIM', 'N'=>'NÃO'), true, 1, "onchange='js_trocaProcesso(this.value)'")
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div id="processoSistemaInterno">
                        <fieldset class="separator">
                            <legend>Dados do Processo</legend>
                            <table class="form-container">
                                <tr>
                                    <td title="<?=@$Tob01_processo?>">
                                        <?php
                                            db_ancora($Lob01_processo, 'js_pesquisaProcesso(true)', 1)
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            if (isset($ob01_processo)  and ($ob01_processosistema == "S")){
                                                $ob01_processo_1       = $ob01_processo;
                                                $ob01_nometitularproc_1 = $ob01_nometitularproc;
                                            }
                                            db_input('ob01_processo_1', 10, $Iob01_processo, true, 'text', $db_opcao, "onchange='js_pesquisaProcesso(false)'");
                                            db_input('ob01_nometitularproc_1', 40, $Iob01_nometitularproc, true, 'text', 3);
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
                    <div id="processoSistemaExterno" style="display: none;   ">
                        <fieldset class="separator">
                            <legend>Dados do Processo</legend>
                            <table class="form-container">
                                <tr>
                                    <td title="<?=$Tob01_processo?>">
                                        <?=$Lob01_processo?>
                                    </td>
                                    <td>
                                        <?php
                                            if (isset($ob01_processo) and ($ob01_processosistema == "N")){

                                                $ob01_processo_2        = $ob01_processo;
                                                $ob01_nometitularproc_2 = $ob01_nometitularproc;

                                            }

                                            db_input('ob01_processo_2', 10, $Iob01_processo, true, 'text', 1)
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="<?=$Tob01_nometitularproc?>">
                                        <?=$Lob01_nometitularproc?>
                                    </td>
                                    <td>
                                        <?php
                                            db_input('ob01_nometitularproc_2', 40, $Iob01_nometitularproc, true, 'text', 1)
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="<?=$Tob01_dtprocesso?>">
                                        <?=$Lob01_dtprocesso?>
                                    </td>
                                    <td>
                                        <?php
                                            db_inputdata('ob01_dtprocesso', @$ob01_dtprocesso_dia, @$ob01_dtprocesso_mes, @$ob01_dtprocesso_ano, true, 'text', $db_opcao)
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
                <td>
            </tr>
            <tr>
                <td title=<?=$Tob01_obs?> colspan="2">
                    <fieldset class="separator">
                        <legend><?=$Lob01_obs ?></legend>
                        <?php
                            db_textarea('ob01_obs', 3, 50, $Iob01_obs, true, 'text', 1)
                        ?>
                    </fieldset>
                </td>
            </tr>
        </table>
    </fieldset>
    <input name='db_opcao' type='button' id='db_opcao' value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?>>
    <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<style>

    .header-container, div.body-container {
        overflow-y: visible;
    }

    div.body-container {
        overflow-x: hidden;
    }
    .grid-resize {
        display: none;
    }
</style>
<script>

    function js_validaResponsavelProjeto() {
        var idNumArtProjeto = document.getElementById('idNumArtProjeto');
        var idNumRrtProjeto = document.getElementById('idNumRrtProjeto');
        var profissaoProjeto = document.form1.profissaoProjeto.value;
        if (profissaoProjeto == 'Arquiteto') {
            idNumArtProjeto.style.display='none';
            idNumRrtProjeto.style.display='block';
        } else if (profissaoProjeto == 'Engenheiro') {
            idNumRrtProjeto.style.display='none';
            idNumArtProjeto.style.display='block';
        } else {
            idNumArtProjeto.style.display='none';
            idNumRrtProjeto.style.display='none';
        }
    }
    function js_validaResponsavelTecnico() {
        var idNumArtTecnico = document.getElementById('idNumArtTecnico');
        var idNumRrtTecnico = document.getElementById('idNumRrtTecnico');
        var profissaoTecnico = document.form1.profissaoTecnico.value;
        if (profissaoTecnico == 'Arquiteto') {
            idNumArtTecnico.style.display='none';
            idNumRrtTecnico.style.display='block';
        } else if (profissaoTecnico == 'Engenheiro') {
            idNumRrtTecnico.style.display='none';
            idNumArtTecnico.style.display='block';
        } else {
            idNumArtTecnico.style.display='none';
            idNumRrtTecnico.style.display='none';
        }
    }

    function js_pesquisaProcesso(lMostra) {

        if (lMostra) {
            js_OpenJanelaIframe('','db_iframe_matric', 'func_protprocesso.php?funcao_js=parent.js_mostraProcesso|p58_codproc|z01_nome','Pesquisa',true);
        } else {
            js_OpenJanelaIframe('','db_iframe_matric', 'func_protprocesso.php?pesquisa_chave='+document.form1.ob01_processo.value+'&funcao_js=parent.js_mostraProcessoHidden','Pesquisa',false);
        }

    }

    function js_mostraProcesso(iCodProcesso, sRequerente) {

        document.form1.ob01_processo.value        = iCodProcesso;
        document.form1.ob01_nometitularproc.value = sRequerente;
        db_iframe_matric.hide();

    }

    function js_mostraProcessoHidden(iCodProcesso, sNome, lErro) {

        if(lErro == true) {
            document.form1.ob01_processo.value = "";
            document.form1.ob01_nometitularproc.value   = sNome;
        } else {
            document.form1.ob01_nometitularproc.value   = sNome;
        }

    }

    js_trocaProcesso(document.form1.ob01_processosistema.value);

    function js_trocaProcesso(lProcessoSistema) {

        if(lProcessoSistema == 'S') {

            document.getElementById('processoSistemaInterno').style.display = '';
            document.getElementById('processoSistemaExterno').style.display = 'none';

            if (document.getElementById('ob01_processo_2') == null) {

                document.getElementById('ob01_processo').setAttribute('name', 'ob01_processo_2');
                document.getElementById('ob01_processo').setAttribute('id'  , 'ob01_processo_2');

                document.getElementById('ob01_nometitularproc').setAttribute('name', 'ob01_nometitularproc_2');
                document.getElementById('ob01_nometitularproc').setAttribute('id'  , 'ob01_nometitularproc_2');

            }

            document.getElementById('ob01_processo_1').setAttribute('name', 'ob01_processo');
            document.getElementById('ob01_processo_1').setAttribute('id'  , 'ob01_processo');

            document.getElementById('ob01_nometitularproc_1').setAttribute('name', 'ob01_nometitularproc');
            document.getElementById('ob01_nometitularproc_1').setAttribute('id'  , 'ob01_nometitularproc');

        } else {

            document.getElementById('processoSistemaInterno').style.display = 'none';
            document.getElementById('processoSistemaExterno').style.display = '';

            if (document.getElementById('ob01_processo_1') == null) {

                document.getElementById('ob01_processo').setAttribute('name', 'ob01_processo_1');
                document.getElementById('ob01_processo').setAttribute('id'  , 'ob01_processo_1');

                document.getElementById('ob01_nometitularproc').setAttribute('name', 'ob01_nometitularproc_1');
                document.getElementById('ob01_nometitularproc').setAttribute('id'  , 'ob01_nometitularproc_1');

            }

            document.getElementById('ob01_processo_2').setAttribute('name', 'ob01_processo');
            document.getElementById('ob01_processo_2').setAttribute('id'  , 'ob01_processo');

            document.getElementById('ob01_nometitularproc_2').setAttribute('name', 'ob01_nometitularproc');
            document.getElementById('ob01_nometitularproc_2').setAttribute('id'  , 'ob01_nometitularproc');

        }

    }

    function js_pesquisaob01_tecnico(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_obrastec','func_obrastec.php?chave_tecobra=true&funcao_js=parent.js_mostraTec|z01_nome|ob15_crea|ob15_sequencial|ob15_profissao','Pesquisa',true);
        }
    }
    function js_mostraTec(nomeTec,creaTec,seqTec,profissaoTec){
        db_iframe_obrastec.hide();
        if (empty(profissaoTec)) {
            alert('Responsável com dados inconsistentes (3)');
        } else {
            document.form1.z01_nometec.value     = nomeTec;
            document.form1.ob15_crea.value       = creaTec;
            document.form1.ob15_sequencial.value = seqTec;
            document.form1.profissaoProjeto.value = profissaoTec;
            js_validaResponsavelProjeto();
        }
    }



    function js_mudaresp(){
        if(document.form1.ob01_tiporesp.value == 1){
            document.form1.ob10_numcgm.disabled = true
        }else{
            document.form1.ob10_numcgm.disabled = false
        }
    }

    js_exibir_ocultar(document.form1.ob01_tiporesp.value);

    function js_exibir_ocultar(selectTipoResp) {
        if(selectTipoResp == 56) {
            document.getElementById('divLancadorCGM').style.display = '';
        } else {
            document.getElementById('divLancadorCGM').style.display = 'none';
        }
    };

    var oDBLancadorCGM = new DBLancador('oDBLancadorCGM');
    oDBLancadorCGM.setNomeInstancia('oDBLancadorCGM');
    oDBLancadorCGM.setGridHeight(85);
    oDBLancadorCGM.setTextoFieldset('Outros proprietários');
    oDBLancadorCGM.setLabelAncora('Outros proprietários: ');
    oDBLancadorCGM.setParametrosPesquisa('func_nome.php', ['z01_numcgm', 'z01_nome']);
    oDBLancadorCGM.show($('divLancadorCGM'));
    var oFieldsetLancador = oDBLancadorCGM.getFieldset();

    <?php if ($db_opcao == 2) { ?>
        oFieldsetLancador.style.width = "86%";
    <?php } else { ?>
        oFieldsetLancador.style.width = "95%";
    <?php } ?>
    var sUrl = 'pro1_obras.RPC.php';

    <?php if ($db_opcao == 2 || $db_opcao == 3) { ?>
        js_getParametros();
    <?php } ?>

    /**
     * funcao que ira popular a tela com os parametros configurados
     */
    function js_getParametros() {

        var oParam          = new Object();
            oParam.codigo_obra   = ob01_codobra.value;
            oParam.acao     = "buscaRegistros";

        new Ajax.Request( sUrl, {
                            method: 'post',
                            parameters: 'json='+js_objectToJson(oParam),
                            onComplete: js_retornoGetParametros
        });
    }

    /**
    * retorno da funcao getDadosParalisacao
    * ira preenher os campos
    */
    function js_retornoGetParametros(oAjax) {

        var oRetorno = JSON.parse(oAjax.responseText);
        var sMensagem = oRetorno.sMessage.urlDecode();

        if (!oRetorno.lErro) {

            oRetorno.aUsuarios.each( function( oValor, iIndice ){

                var sNomeUsuario   = oValor.sNomeUsuario  .urlDecode();
                var iCodigoUsuario = oValor.iCodigoUsuario.urlDecode();
                    oDBLancadorCGM.adicionarRegistro ( iCodigoUsuario, sNomeUsuario );
            });
        }
    }

    function js_pesquisaob10_numcgm(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_cgm','func_nome.php?testanome=true&funcao_js=parent.js_mostracgmp1|0|1','Pesquisa',true);
        }else{
            if(document.form1.ob10_numcgm.value != ''){
                js_OpenJanelaIframe('','db_iframe_cgm','func_nome.php?pesquisa_chave='+document.form1.ob10_numcgm.value+'&funcao_js=parent.js_mostracgmp','Pesquisa',false);
            }else{
                document.form1.z01_nome.value = '';
            }
        }
    }
    function js_mostracgmp(erro,chave,erro){
        document.form1.z01_nomeresp.value = chave;
        if(erro==true){
            document.form1.ob10_numcgm.focus();
            document.form1.ob10_numcgm.value = '';
        }
    }
    function js_mostracgmp1(chave1,chave2){
        document.form1.ob10_numcgm.value = chave1;
        document.form1.z01_nomeresp.value = chave2;
        db_iframe_cgm.hide();
    }
    function js_pesquisaob07_lograd(mostra){
        if(mostra==true){
            <?php
            if(@$ob01_regular == "t"){
            ?>
            js_OpenJanelaIframe('','db_iframe_ruas','func_ruasobras.php?pesquisa_chave='+document.form1.ob05_idbql.value+'&funcao_js=parent.js_mostraruas2|j36_codigo|j14_nome|j13_codi|j13_descr','Pesquisa',true);
            <?php
            }else{
            ?>
            js_OpenJanelaIframe('','db_iframe_ruas','func_ruas.php?funcao_js=parent.js_mostraruas1|j14_codigo|j14_nome','Pesquisa',true);
            <?php
            }
            ?>
        }else{
            if(document.form1.ob07_lograd.value != ''){
                js_OpenJanelaIframe('','db_iframe_ruas','func_ruas.php?pesquisa_chave='+document.form1.ob07_lograd.value+'&funcao_js=parent.js_mostraruas','Pesquisa',false);
            }else{
                document.form1.j14_nome.value = '';
            }
        }
    }
    function js_mostraruas(chave,erro){
        document.form1.j14_nome.value = chave;
        if(erro==true){
            document.form1.ob07_lograd.focus();
            document.form1.ob07_lograd.value = '';
        }
    }
    function js_mostraruas1(chave1,chave2){
        document.form1.ob07_lograd.value = chave1;
        document.form1.j14_nome.value = chave2;
        db_iframe_ruas.hide();
    }
    function js_mostraruas2(chave1,chave2,cod,bai){
        document.form1.ob07_lograd.value = chave1;
        document.form1.j14_nome.value = chave2;
        document.form1.ob07_bairro.value = cod;
        document.form1.j13_descr.value = bai;
        db_iframe_ruas.hide();
    }
    function js_pesquisaob07_bairro(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_bairro','func_bairro.php?funcao_js=parent.js_mostrabairro1|j13_codi|j13_descr','Pesquisa',true);
        }else{
            if(document.form1.ob07_bairro.value != ''){
                js_OpenJanelaIframe('','db_iframe_bairro','func_bairro.php?pesquisa_chave='+document.form1.ob07_bairro.value+'&funcao_js=parent.js_mostrabairro','Pesquisa',false);
            }else{
                document.form1.j13_descr.value = '';
            }
        }
    }
    function js_mostrabairro(chave,erro){
        document.form1.j13_descr.value = chave;
        if(erro==true){
            document.form1.ob07_bairro.focus();
            document.form1.ob07_bairro.value = '';
        }
    }
    function js_mostrabairro1(chave1,chave2){
        document.form1.ob07_bairro.value = chave1;
        document.form1.j13_descr.value = chave2;
        db_iframe_bairro.hide();
    }
    function js_pesquisaob05_idbql(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_lote','func_lote.php?funcao_js=parent.js_mostralote1|j34_idbql|j34_setor|j34_quadra|j34_lote|','Pesquisa',true);
        }else{
            if(document.form1.ob05_idbql.value != ''){
                js_OpenJanelaIframe('','db_iframe_lote','func_lote.php?pesquisa_chave='+document.form1.ob05_idbql.value+'&funcao_js=parent.js_mostralote','Pesquisa',false);
            }else{
                document.form1.j34_setor.value = '';
            }
        }
    }
    function js_mostralote(chave,erro){
        document.form1.j34_setor.value = chave;
        if(erro==true){
            document.form1.ob05_idbql.focus();
            document.form1.ob05_idbql.value = '';
        }
    }
    function js_mostralote1(chave1,chave2,q,l){
        document.form1.ob05_idbql.value = chave1;
        document.form1.j34_setor.value = chave2;
        document.form1.j34_quadra.value = q;
        document.form1.j34_lote.value = l;
        db_iframe_lote.hide();
    }
    function js_pesquisaob03_numcgm(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_cgm','func_nome.php?testanome=true&funcao_js=parent.js_mostracgm1|0|1','Pesquisa',true);
        }else{
            if(document.form1.ob03_numcgm.value != ''){
                js_OpenJanelaIframe('','db_iframe_cgm','func_nome.php?pesquisa_chave='+document.form1.ob03_numcgm.value+'&funcao_js=parent.js_mostracgm','Pesquisa',false);
            }else{
                document.form1.z01_nome.value = '';
            }
        }
    }
    function js_pesquisaob04_numcgm(mostra1){
        if(mostra1==true){
            js_OpenJanelaIframe('','db_iframe_cgm','func_nome.php?testanome=true&funcao_js=parent.js_mostracgm3|0|1','Pesquisa',true);
        }else{
            if(document.form1.ob04_numcgm.value != ''){
                js_OpenJanelaIframe('','db_iframe_cgm','func_nome.php?pesquisa_chave='+document.form1.ob04_numcgm.value+'&funcao_js=parent.js_mostracgm2','Pesquisa',false);
            }else{
                document.form1.z02_nome.value = '';
            }
        }
    }
    function js_mostracgm(erro,chave,erro){
        document.form1.z01_nome.value = chave;
        if(erro==true){
            document.form1.ob03_numcgm.focus();
            document.form1.ob03_numcgm.value = '';
        }
    }
    function js_mostracgm1(chave1,chave2){
        document.form1.ob03_numcgm.value = chave1;
        document.form1.z01_nome.value = chave2;
        db_iframe_cgm.hide();
    }
    function js_mostracgm2(erro,chave,erro){
        document.form1.z02_nome.value = chave;
        if(erro==true){
            document.form1.ob04_numcgm.focus();
            document.form1.ob04_numcgm.value = '';
        }
    }
    function js_mostracgm3(chave1,chave2){
        document.form1.ob04_numcgm.value = chave1;
        document.form1.z02_nome.value = chave2;
        db_iframe_cgm.hide();
    }
    function js_pesquisaob01_tiporesp(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_obrastiporesp','func_obrastiporesp.php?funcao_js=parent.js_mostraobrastiporesp1|ob02_cod|ob02_descr','Pesquisa',true);
        }else{
            if(document.form1.ob01_tiporesp.value != ''){
                js_OpenJanelaIframe('','db_iframe_obrastiporesp','func_obrastiporesp.php?pesquisa_chave='+document.form1.ob01_tiporesp.value+'&funcao_js=parent.js_mostraobrastiporesp','Pesquisa',false);
            }else{
                document.form1.ob02_descr.value = '';
            }
        }
    }
    function js_mostraobrastiporesp(chave,erro){
        document.form1.ob02_descr.value = chave;
        if(erro==true){
            document.form1.ob01_tiporesp.focus();
            document.form1.ob01_tiporesp.value = '';
        }
    }
    function js_mostraobrastiporesp1(chave1,chave2){
        document.form1.ob01_tiporesp.value = chave1;
        document.form1.ob02_descr.value = chave2;
        db_iframe_obrastiporesp.hide();
    }
    function js_pesquisa(){
        js_OpenJanelaIframe('','db_iframe_obras','func_obras.php?funcao_js=parent.js_preenchepesquisa|ob01_codobra','Pesquisa',true);
    }
    function js_preenchepesquisa(chave){
        db_iframe_obras.hide();
        <?php
        if($db_opcao == 2 || $db_opcao == 22){
            echo " location.href = 'pro1_obras002.php?abas=1&chavepesquisa='+chave;";
        }elseif($db_opcao == 33 || $db_opcao == 3){
            echo " location.href = 'pro1_obras003.php?abas=1&chavepesquisa='+chave;";
        }
        ?>
    }

    function js_pesquisaMatricula(lMostra){


        if (lMostra == true){

            js_OpenJanelaIframe('','db_iframe_matric', 'func_iptubasenaobaixa.php?funcao_js=parent.js_mostraMatricula|j01_matric|z01_nome','Pesquisa',true);

        } else {

            js_OpenJanelaIframe('','db_iframe_matric', 'func_iptubasenaobaixa.php?pesquisa_chave='+document.form1.j01_matric.value+'&funcao_js=parent.js_mostraMatriculaHidden','Pesquisa',false);

        }
    }

    function js_mostraMatricula(iMatricula, sNome) {

        document.form1.j01_matric.value = iMatricula;
        document.form1.z01_nome_matricula.value   = sNome;

        db_iframe_matric.hide();

    }

    function js_mostraMatriculaHidden(sNome, lErro) {

        if(lErro == true) {
            document.form1.j01_matric.value = "";
            document.form1.z01_nome_matricula.value   = sNome;
        } else {
            document.form1.z01_nome_matricula.value   = sNome;
        }

    }

    function pesquisaResponsavelProjeto() {
        js_OpenJanelaIframe(
            '',
            'db_iframe_responsavelprojeto',
            'func_obrastec.php?chave_tecobra=true&funcao_js=parent.mostraResponsavelProjeto|z01_nome|ob15_crea|ob15_sequencial',
            'Pesquisa Responsável pelo Projeto',
            true
        );
    }

    function mostraResponsavelProjeto(nomeTec, creaTec, seqTec) {

        document.form1.crea_responsavel.value = creaTec;
        document.form1.ob01_responsavelprojeto.value = seqTec;
        document.form1.nome_responsavel.value = nomeTec;

        db_iframe_responsavelprojeto.hide();
    }

    function pesquisaArquitetoObra(){
        js_OpenJanelaIframe(
            '',
            'db_iframe_arquitetoobra',
            'func_obrastec.php?chave_tecobra=true&funcao_js=parent.mostraArquitetoObra|z01_nome|ob15_crea|ob15_sequencial|ob15_profissao',
            'Pesquisa Arquiteto da Obra',
            true
        );
    }

    function mostraArquitetoObra(nomeTec, creaTec, seqTec, profissaoTec) {
        db_iframe_arquitetoobra.hide();
        if (empty(profissaoTec)) {
            alert('Responsável com dados inconsistentes (4)');
        } else {
            document.form1.crea_arquiteto.value = creaTec;
            document.form1.ob01_arquitetoobra.value = seqTec;
            document.form1.nome_arquiteto.value = nomeTec;
            document.form1.profissaoTecnico.value = profissaoTec;
            js_validaResponsavelTecnico();
        }
    }

    <?php
    if($db_opcao == 2){
    ?>
    js_OpenJanelaIframe('','db_iframe_cgm','func_nome.php?pesquisa_chave=<?=@$ob03_numcgm?>&funcao_js=parent.js_mostracgm','Pesquisa',false);
    js_OpenJanelaIframe('','db_iframe_cgm','func_nome.php?pesquisa_chave=<?=@$ob10_numcgm?>&funcao_js=parent.js_mostracgmp','Pesquisa',false);
    <?php
    }
    ?>
    if(document.form1.ob01_tiporesp.value == 1){
        document.form1.ob10_numcgm.disabled = true
    }
</script>
<script>

    <?php if($db_opcao != 3 || $db_opcao == 33){?>
    $("ob01_tiporesp").setAttribute("rel","ignore-css");
    $("ob01_tiporesp").addClassName("field-size2");
    $("ob01_tiporespdescr").setAttribute("rel","ignore-css");
    $("ob01_tiporespdescr").addClassName("field-size7");
    <?php }else{?>
    $("ob02_descr").addClassName("field-size7");
    <?php }?>

    function js_validaCampos() {
        const ob01_numeroartprojeto = document.getElementById("ob01_numeroartprojeto");
        const ob01_numerorrtprojeto = document.getElementById("ob01_numerorrtprojeto");
        const ob01_numeroarttecnico = document.getElementById("ob01_numeroarttecnico");
        const ob01_numerorrttecnico = document.getElementById("ob01_numerorrttecnico");
        var profissaoProjeto = document.form1.profissaoProjeto.value;

        // projeto
        const z01_nometec = document.form1.z01_nometec.value;
        const ob15_crea = document.form1.ob15_crea.value;
        const ob15_sequencial = document.form1.ob15_sequencial.value;
        // tecnico
        const crea_arquiteto = document.form1.crea_arquiteto.value;
        const ob01_arquitetoobra = document.form1.ob01_arquitetoobra.value;
        const nome_arquiteto = document.form1.nome_arquiteto.value;

        // projeto
        if (z01_nometec.trim() != "") {
            if (profissaoProjeto == 'Arquiteto' && empty(ob01_numerorrtprojeto.value)) {
                alert("Informe o Nmero RRT do responsvel pelo projeto.");
                return false;
            } else if (profissaoProjeto == 'Engenheiro' && empty(ob01_numeroartprojeto.value)) {
                alert("Informe o Nmero ART do responsvel pelo projeto.");
                return false;
            }
        }
        // tecnico
        if (crea_arquiteto.trim() != "") {
            if (profissaoTecnico == 'Arquiteto' && empty(ob01_numerorrttecnico.value)) {
                alert("Informe o Nmero RRT do responsvel tcnico.");
                return false;
            } else if (profissaoTecnico == 'Engenheiro' && empty(ob01_numeroarttecnico.value)) {
                alert("Informe o Nmero ART do responsvel tcnico.");
                return false;
            }
        }

        return true;
    }

    <?php if($db_opcao == 1 || $db_opcao == 2) {?>
    $('db_opcao').addEventListener('click', function() {
        if (js_validaCampos() == true) {
            js_salvar();
        }
    });
    <?php } else { ?>
    $('db_opcao').addEventListener('click', function() {
            js_excluir();
    });
    <?php } ?>

    function js_salvar(){

        var aUsuarios = getUsuariosLancados();

        if (aUsuarios.length > 10) {
            alert("É possível incluir no máximo 10 outros proprietários");
        }

        var oParam                       = new Object();
        <?php if($db_opcao == 1) {?>
            oParam.acao                  = "salvarRegistros";
        <?php } else { ?>
            oParam.acao                  = "alterarRegistros";
        <?php } ?>
            oParam.ob01_codobra          = ob01_codobra.value;
            oParam.ob03_numcgm           = ob03_numcgm.value;
            oParam.ob01_regular          = ob01_regular.value;
            if (ob01_regular.value == 't') {
                oParam.j01_matric = j01_matric.value;
            } else {
                oParam.ob06_setor  = ob06_setor.value;
                oParam.ob06_quadra = ob06_quadra.value;
                oParam.ob06_lote   = ob06_lote.value;
            }
            oParam.ob15_sequencial       = ob15_sequencial.value;
            oParam.ob20_sequencial       = ob20_sequencial.value;
            oParam.ob01_processosistema  = ob01_processosistema.value;
            oParam.ob10_numcgm           = ob10_numcgm.value;
            oParam.aUsuarios             = aUsuarios;
            oParam.ob01_nomeobra         = encodeURIComponent(tagString(ob01_nomeobra.value));
            oParam.ob01_tiporesp         = ob01_tiporesp.value;
            oParam.z01_nome              = encodeURIComponent(tagString(z01_nome.value));
            oParam.ob01_dtobra           = ob01_dtobra.value;
            oParam.ob01_processo         = ob01_processo.value;
            oParam.ob01_obs              = encodeURIComponent(tagString(ob01_obs.value));
            oParam.ob01_arquitetoobra    = ob01_arquitetoobra.value;
            oParam.ob01_numeroartprojeto = ob01_numeroartprojeto.value;
            oParam.ob01_numerorrtprojeto = ob01_numerorrtprojeto.value;
            oParam.ob01_numeroarttecnico = ob01_numeroarttecnico.value;
            oParam.ob01_numerorrttecnico = ob01_numerorrttecnico.value;

            js_divCarregando("Salvando" ,'msgBox');

            new Ajax.Request( sUrl, {
                                method     : 'post',
                                parameters : 'json=' + js_objectToJson(oParam),
                                onComplete : js_retornoSalvar
            });
    }

    function js_retornoSalvar(oAjax) {

        js_removeObj('msgBox');
        var oRetorno  = JSON.parse(oAjax.responseText);
        if (oRetorno.status == 1) {
            alert('Itens salvos com sucesso!');

            parent.iframe_obras.location.href='pro1_obras002.php?chavepesquisa='+oRetorno.codobra+'&abas=1';
            parent.iframe_constr.location.href='pro1_obrasconstr001.php?ob08_codobra='+oRetorno.codobra+'&abas=1';
            parent.mo_camada('constr');
            parent.document.formaba.constr.disabled=false;
            parent.document.formaba.areas.disabled=false;
        } else if (oRetorno.status == 2) {
            alert('Itens alterados com sucesso.');

        } else {
            alert(oRetorno.sMessage.urlDecode());
        }
    }

    function js_excluir() {

        var aUsuarios = getUsuariosLancados();

        var oParam                       = new Object();
            oParam.acao                  = "excluirRegistros";
            oParam.ob01_codobra          = ob01_codobra.value;

            js_divCarregando("Salvando" ,'msgBox');

            new Ajax.Request( sUrl, {
                                method     : 'post',
                                parameters : 'json=' + js_objectToJson(oParam),
                                onComplete : js_retornoExcluir
            });
    }

    function js_retornoExcluir(oAjax) {
        js_removeObj('msgBox');
        var oRetorno  = JSON.parse(oAjax.responseText);

        if (oRetorno.status == 3) {
            alert('Itens excluídos com sucesso.');
            $('ob01_codobra').value = '';
            $('ob01_nomeobra').value = '';
            $('ob01_dtobra').value = '';
            $('ob01_tiporesp').value = '';
            $('ob02_descr').value = '';
            $('ob03_numcgm').value = '';
            $('z01_nome').value = '';
            oDBLancadorCGM.clearAll(true);
            $('j01_matric').value = '';
            $('z01_nome_matricula').value = '';
            $('ob10_numcgm').value = '';
            $('z01_nomeresp').value = '';
            $('ob15_crea').value = '';
            $('z01_nometec').value = '';
            $('crea_arquiteto').value = '';
            $('nome_arquiteto').value = '';
            $('ob01_processo').value = '';
            $('ob01_nometitularproc').value = '';
            $('ob01_obs').value = '';
            js_pesquisa();
        } else {
            alert(oRetorno.message.urlDecode());
        }
    }

    /**
    * funcao retorna um array de usuarios lancados para enviar ao rpc salvar
    */
    function getUsuariosLancados(){

        var aUsuarios = [];
        oDBLancadorCGM.getRegistros().each( function( oDados, iIndice){

        aUsuarios.push( oDados.sCodigo );
        });

        return aUsuarios;
    }


</script>
