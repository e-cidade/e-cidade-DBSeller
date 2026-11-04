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

//MODULO: empenho
include(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$clempprestaitem->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("e45_tipo");
$clrotulo->label("nome");
$clrotulo->label("e60_codemp");
$clrotulo->label("e45_codmov");
if (isset($tranca)) {
    $db_opcao = 33;
    $db_botao=false;
} elseif (isset($db_opcaoal)) {
    $db_opcao=33;
    $db_botao=false;
} elseif (isset($opcao) && $opcao=="alterar") {
    $db_botao=true;
    $db_opcao = 2;
} elseif (isset($opcao) && $opcao=="excluir") {
    $db_opcao = 3;
    $db_botao=true;
} else {
    $db_opcao = 1;
    $db_botao=true;
    if (isset($novo) || isset($alterar) || isset($excluir) || (isset($incluir) && $sqlerro==false )) {
        $e46_codigo = "";
        $e46_nota = "";
        $e46_valor = "";
        $e46_descr = "";
        $e46_id_usuario = "";
        $e46_cnpj = "";
        $e46_cpf = "";
        $e46_nome = "";
    }
}

$lDiaria = false;
$itemdiaria = null;
if (!empty($e45_tipo)) {
    $oEmpPrestaTipo = new \cl_empprestatip();
    $sql = $oEmpPrestaTipo->sql_query($e45_tipo, "e44_diaria as diaria");
    $rs = db_query($sql);
    if (!$rs) {
        throw new DBException("Erro ao buscar informações do tipo de evento.");
    }
    if (pg_num_rows($rs) == 0) {
        throw new BusinessException("Nenhuma informação do tipo de evento.");
    }
    $evento = \db_utils::fieldsMemory($rs, 0);
    if ($evento->diaria == "t") {
        $lDiaria = true;
    }
    // Busca informações da aba anterior
    $sql = "select e45_obs as observacao, e45_data as data from emppresta where e45_sequencial = {$e45_sequencial}";

    $rs = db_query($sql);
    if (!$rs) {
        throw new DBException("Erro ao buscar informações da prestação de contas.");
    }
    if (pg_num_rows($rs) == 0) {
        throw new BusinessException("Nenhuma informação da prestação de contas.");
    }
    $prestacao = \db_utils::fieldsMemory($rs, 0);
    // Verifica se já existe preenchimento
    if (isset($e46_codigo) && !empty($e46_codigo)) {
        $sql = "select * from empprestaitemdiaria where e446_empprestaitem = {$e46_codigo}";
        $rs = db_query($sql);

        if (!$rs) {
            throw new DBException("Erro ao buscar informações das diárias.");
        }
        if (pg_num_rows($rs) != 0) {
            $itemdiaria =  \db_utils::fieldsMemory($rs, 0);
        }
    }
}
?>
<form name="form1" method="post" action="">
    <center>
        <fieldset style="width: 500px">
            <legend><b>Cadastro de Itens</b></legend>
            <table border="0">
                <tr>
                    <td nowrap title="<?=@$Te60_codemp?>">
                        <?=$Le60_codemp?>
                    </td>
                    <td>
                        <?php
                        db_input('e60_codemp', 10, $Ie60_codemp, true, 'text', 3);
                        db_input('e46_numemp', 10, $Ie46_numemp, true, 'hidden', 1);
                        db_input('e46_codigo', 10, $Ie46_codigo, true, 'hidden', 1);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?php echo $Te45_codmov; ?>">
                        <?php echo $Le45_codmov; ?>
                    </td>
                    <td>
                        <?php db_input('e45_codmov', 10, $Ie45_codmov, true); ?>
                    </td>
                </tr>
                <tr>
                    <?php
                    if ($lDiaria) {
                        $clrotulo->label("e446_sequencial");
                        $clrotulo->label("e446_empprestaitem");
                        $clrotulo->label("e446_regist");
                        $clrotulo->label("e446_quantidade");
                        $clrotulo->label("e446_movimento");

                        $clrotulo->label("e446_motivo");
                        $clrotulo->label("e446_destino");
                        $clrotulo->label("e446_tipodiaria");
                        $clrotulo->label("e446_datainicio");
                        $clrotulo->label("e446_datafim");
                        $e446_motivo = $prestacao->observacao;
                        $e446_datainicio = $prestacao->data;
                        $e446_datafim = $prestacao->data;

                        if (!empty($itemdiaria)) {
                            $e446_sequencial = $itemdiaria->e446_sequencial;
                            $e446_empprestaitem = $itemdiaria->e446_empprestaitem;
                            $e446_quantidade = $itemdiaria->e446_quantidade;
                            $e446_movimento = $itemdiaria->e446_movimento;
                            $e446_motivo = $itemdiaria->e446_motivo;
                            $e446_datainicio = $itemdiaria->e446_datainicio;
                            $e446_datafim = $itemdiaria->e446_datafim;
                            $e446_regist = $itemdiaria->e446_regist;
                            $e446_destino = $itemdiaria->e446_destino;
                            $e446_tipodiaria = $itemdiaria->e446_tipodiaria;
                        }
                        db_input('e446_sequencial', 10, $Ie446_sequencial, true, 'hidden', 1);
                        db_input('e446_empprestaitem', 10, $Ie446_empprestaitem, true, 'hidden', 1);
                        db_input('e446_quantidade', 10, $Ie446_quantidade, true, 'hidden', 1);
                        db_input('e446_movimento', 10, $Ie446_movimento, true, 'hidden', 1);

                        $e446_datainicio_dia = substr($e446_datainicio, 8);
                        $e446_datainicio_mes = substr($e446_datainicio, 5, 2);
                        $e446_datainicio_ano = substr($e446_datainicio, 0, 4);

                        $e446_datafim_dia = substr($e446_datafim, 8);
                        $e446_datafim_mes = substr($e446_datafim, 5, 2);
                        $e446_datafim_ano = substr($e446_datafim, 0, 4);
                        ?>
                        <td nowrap title="<?=@$Te46_nome?>">
                            <?php db_ancora("Matrícula", "js_pesquisarh01_regist(true);", 1);?>
                        </td>
                        <td>
                            <?php
                            $clrotulo->label("e446_regist");
                            $clrotulo->label("z01_nome");
                            db_input('e446_regist', 8, $Ie446_regist, true, 'text', 1, " onchange='js_pesquisarh01_regist(false);'");
                            db_input('e46_nome', 30, $Ie46_nome, true, 'text', 3, '');
                            ?>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td nowrap title="<?=@$Te46_nome?>">
                            <?=@$Le46_nome?>
                        </td>
                        <td>
                            <?php db_input('e46_nome', 40, $Ie46_nome, true, 'text', $db_opcao, "");?>
                        </td>
                        <?php
                    }?>
                </tr>
                <tr>
                    <td nowrap title="<?=@$Te46_nota?>">
                        <?=@$Le46_nota?>
                    </td>
                    <td>
                        <?php db_input('e46_nota', 20, $Ie46_nota, true, 'text', $db_opcao, "");?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?=@$Te46_valor?>">
                        <?=@$Le46_valor?>
                    </td>
                    <td>
                        <?php db_input('e46_valor', 10, $Ie46_valor, true, 'text', $db_opcao, "");?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?=@$Te46_cnpj?>">
                        <?=@$Le46_cnpj?>
                    </td>
                    <td>
                        <?php db_input('e46_cnpj', 14, $Ie46_cnpj, true, 'text', $db_opcao, "");?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?=@$Te46_cpf?>">
                        <?=@$Le46_cpf?>
                    </td>
                    <td>
                        <?php db_input('e46_cpf', 11, $Ie46_cpf, true, 'text', $db_opcao, "");?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?=@$Te46_descr?>" colspan="2">
                        <fieldset>
                            <legend><b><?=@$Le46_descr?></b></legend>
                            <?php db_textarea('e46_descr', 5, 60, $Ie46_descr, true, 'text', $db_opcao, "");?>
                        </fieldset>
                    </td>
                </tr>
                <?php
                if ($lDiaria) {
                    ?>
                    <tr>
                        <td nowrap title="Período">
                            <strong>Período:</strong>
                        </td>
                        <td>
                            <?php
                            echo "<strong>De </strong>";
                            db_inputdata('e446_datainicio', $e446_datainicio_dia, $e446_datainicio_mes, $e446_datainicio_ano, true, 'text', $db_opcao, "");
                            echo "<strong> Até </strong>";
                            db_inputdata('e446_datafim', $e446_datafim_dia, $e446_datafim_mes, $e446_datafim_ano, true, 'text', $db_opcao, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?=@$Te446_motivo?>" colspan="2">
                            <fieldset>
                                <legend><b><?=@$Le446_motivo?></b></legend>
                                <?php db_textarea('e446_motivo', 5, 60, $Ie446_motivo, true, 'text', $db_opcao, "");?>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?=@$Te446_destino?>" colspan="2">
                            <fieldset>
                                <legend><b><?=@$Le446_destino?></b></legend>
                                <?php db_textarea('e446_destino', 5, 60, $Ie446_destino, true, 'text', $db_opcao, "");?>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?=@$Te446_tipodiaria?>"><?=@$Le446_tipodiaria?></td>
                        <td>
                            <?php
                            $arr = array("dentroestado"=>"Dentro do Estado", "foraestado"=>"Fora do Estado","forapais"=>"Fora do País");
                            db_select("e446_tipodiaria", $arr, true, $db_opcao);
                            ?>                        
                        </td>
                    </tr>

                    <?php
                }
                ?>
            </table>
        </fieldset>
        <table>
            <tr>
                <td colspan="2" align="center">
                    <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?>  >
                    <input  <?=($db_botao==false?"disabled":"")?> name="novo" type="button" id="cancelar" value="Novo" onclick="js_cancelar();" <?=($db_opcao==1||isset($db_opcaoal)?"style='visibility:hidden;'":"")?> >
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td valign="top"  align="center">
                    <?php
                    $clemppresta->sql_record($clemppresta->sql_query_emp(null, 'e45_acerta', '', "e45_acerta is null and e45_numemp=$e46_numemp"));

                    if ($clemppresta->numrows==0) {
                        $db_opcao=33;
                    }
                    $chavepri = array("e46_numemp"=>$e46_numemp,"e46_codigo"=>@$e46_codigo);
                    $cliframe_alterar_excluir->chavepri=$chavepri;
                    $cliframe_alterar_excluir->sql     = $clempprestaitem->sql_query_file(null, "*", "", "e46_emppresta=$oGet->e45_sequencial");
                    $cliframe_alterar_excluir->campos  ="e46_codigo,e46_nota,e46_valor,e46_descr,e46_cnpj,e46_cpf,e46_nome";
                    $cliframe_alterar_excluir->legenda="ITENS LANÇADOS";
                    $cliframe_alterar_excluir->iframe_height ="160";
                    $cliframe_alterar_excluir->iframe_width ="700";
                    $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);
                    ?>
                </td>
            </tr>
        </table>
    </center>
</form>
<script>
    function js_cancelar() {
        var opcao = document.createElement("input");
        opcao.setAttribute("type","hidden");
        opcao.setAttribute("name","novo");
        opcao.setAttribute("value","true");
        document.form1.appendChild(opcao);
        document.form1.submit();
    }
  
    // Pega o código da movimentacao do campo na primeira aba
    document.form1.e45_codmov.value = (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_emppresta.document.form1.e45_codmov.value;
</script>
<?php
if ($lDiaria) {
    ?>
    <script type="text/javascript">
        function js_pesquisarh01_regist(mostra) {
            if (mostra==true) {
                js_OpenJanelaIframe('','db_iframe_rhpessoal','func_rhpessoal.php?funcao_js=parent.js_mostrapessoal1|rh01_regist|z01_nome&instit=<?=(db_getsession("DB_instit"))?>','Pesquisa',true);
            } else {
                if (document.form1.rh01_regist.value != '') {
                    js_OpenJanelaIframe('','db_iframe_rhpessoal','func_rhpessoal.php?pesquisa_chave='+document.e446_regist.value+'&funcao_js=parent.js_mostrapessoal&instit=<?=(db_getsession("DB_instit"))?>','Pesquisa',false);
                } else {
                    document.form1.e46_nome.value = '';
                }
            }
        }
        function js_mostrapessoal(chave, erro) {
            document.form1.e46_nome.value = chave; 
            if (erro==true) {
                document.form1.e446_regist.focus(); 
                document.form1.e446_regist.value = ''; 
            } 
        }
        function js_mostrapessoal1(chave1, chave2){
            document.form1.e446_regist.value = chave1;
            document.form1.e46_nome.value   = chave2;
            db_iframe_rhpessoal.hide();
        }
    </script>
    <?php
}
?>
