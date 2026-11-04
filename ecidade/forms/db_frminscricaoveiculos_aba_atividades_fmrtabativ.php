<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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

require_once(modification("dbforms/db_classesgenericas.php"));

$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$clIssAlvara              = new cl_issalvara;
$cltabativ        = new cl_tabativ;

$cltabativ->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");
$clrotulo->label("q03_descr");
$clrotulo->label("q07_seq");
$clrotulo->label("q11_tipcalc");
$clrotulo->label("q81_descr");
$clrotulo->label("q88_inscr");
$clrotulo->label("q07_horaini");
$clrotulo->label("q07_horafim");

?>

<form id="form_atividade" name="form_atividade" method="post" action="<?=basename($_SERVER['PHP_SELF'])?>" onload='js_testadata();'  >
    <fieldset style="width: 740px">
    <legend>Vincular Atividades</legend>
        <table>
            <tr>
                <td nowrap title="<?=@$Tq07_seq?>">
                    <?=$Lq07_seq?>
                </td>
                <td>
                    <?php
                        db_input('q07_seq',10,$Iq07_seq,true,'text',3);
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?=@$Tq07_inscr?>">
                    <?=$Lq07_inscr?>
                </td>
                <td>
                    <?php
                        db_input('q07_inscr',10,$Iq07_inscr,true,'text',3);
                    ?>
                    <?php
                        // $z01_nome = stripslashes($descricaoInscricaoCGM);
                        db_input('z01_nome',50,$Iz01_nome,true,'text',3);
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?=@$Tq07_ativ?>">
                    <b>
                       <?php
                            db_ancora(@$Lq07_ativ,"js_pesquisaq07_ativ(true);",$db_opcao);
                       ?>
                    </b>
                </td>
                <td>
                    <?php
                        db_input('q07_ativ',10,$Iq07_ativ,true,'text',$db_opcao," onchange='js_pesquisaq07_ativ(false);'")
                    ?>
                    <?php
                        db_input('q03_descr',50,$Iq03_descr,true,'text',3,'')
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="Atividade principal">
                    <b>Atividade principal:</b>
                </td>
                <td>
                    <?php
                        if (isset($princ) && $princ=="t" && $db_opcao==2) {
                            $db_opcao_02=3;
                            $npods=false;
                        } else {
                            $db_opcao_02=1;
                        }
                        $xq = array("f"=>"NÃO","t"=>"SIM");
                        db_select('princ',$xq,true,1);
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?=@$Tq07_quant?>">
                    <?=@$Lq07_quant?>
                </td>
                <td>
                    <?php
                        if(empty($q07_quant)){
                          $q07_quant=1;
                        }
                        db_input('q07_quant',10,$Iq07_quant,true,'text',$db_opcao,"");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?=@$Tq07_perman?>">
                    <?=@$Lq07_perman?>
                </td>
                <td>
                    <?php
                        $xe = array("t"=>"PERMANENTE","f"=>"PROVISÓRIO");
                        db_select('q07_perman',$xe,true,$db_opcao,"onchange='js_testadata(this.value);'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?=@$Tq07_datain?>">
                    <?=@$Lq07_datain?>
                </td>
                <td>
                    <?php
                        if(empty($q07_datain_dia)){

                          $q07_datain_dia = date("d",db_getsession("DB_datausu"));
                          $q07_datain_mes = date("m",db_getsession("DB_datausu"));
                          $q07_datain_ano = date("Y",db_getsession("DB_datausu"));
                        }
                        db_inputdata('q07_datain',@$q07_datain_dia,@$q07_datain_mes,@$q07_datain_ano,true,'text',$db_opcao);
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?=@$Tq07_datafi?>">
                    <?=@$Lq07_datafi?>
                </td>
                <td>
                    <?php
                        db_inputdata('q07_datafi',@$q07_datafi_dia,@$q07_datafi_mes,@$q07_datafi_ano,true,'text',$db_opcao,"");
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                        db_input('q11_tipcalc',10,$Iq07_inscr,true,'hidden',$db_opcao,'onchange="js_tipcalc(false);"');
                    ?>
                    <?php
                        db_input('q81_descr',50,$Iz01_nome,true,'hidden',3,"","","#E6E4F1");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?=@$Tq07_horaini?>">
                   <?=@$Lq07_horaini?>
                </td>
                <td colspan="2">
                    <?php
                        db_input('q07_horaini',5,$Iq07_horaini,true,'text',$db_opcao,"onchange='js_verifica_hora(this.value,this.name)';");
                    ?>
                </td>
            </tr>

            <tr>
                <td nowrap title="<?=@$Tq07_horafim?>">
                    <?=@$Lq07_horafim?>
                </td>
                <td colspan="2">
                    <?php
                        db_input('q07_horafim',5,$Iq07_horafim,true,'text',$db_opcao,"onchange='js_verifica_hora(this.value,this.name)';");
                    ?>
                </td>
            </tr>

            <tr>
                <td><strong>Atividade Interna</strong></td>
                <td colspan="2">
                    <select id="q07_val_ativ_int" name="q07_val_ativ_int">
                        <option value="Sim">Sim</option>
                        <option value="Não">Não</option>
                    </select>
                </td>
            </tr>
        </table>
    </fieldset>
    <input name="salvar" type="button" id="db_opcao" value="Salvar" onclick="js_dtfim();" >
    <input name="novo" type="button" id="cancelar" value="Limpar" onclick="js_limpar();">
</form>
<script>
    function js_limpar() {
        $('q07_seq').value = '';
        $('q07_ativ').value = '';
        $('q03_descr').value = '';
        $('princ').value = 'f';
        $('q07_quant').value = 1;
        $('q07_perman').value = 't';
        $('q07_datain').value = new Date().getDateBR();
        $('q07_datafi').value = '';
        $('q07_horaini').value = '';
        $('q07_horafim').value = '';
        $('q07_val_ativ_int').value = 'Sim';
    }

    function js_verifica_hora(valor, campo) {
        var
            erro= 0,
            ms  = "",
            hs  = "",
            tam = "",
            pos = "",
            tam = valor.length,
            pos = valor.indexOf(":");

        if (pos!=-1) {
            if(pos==0 || pos>2){
                erro++;
            }else{
                if(pos==1){
                    hs = "0"+valor.substr(0,1);
                    ms = valor.substr(pos+1,2);
                }else if(pos==2){
                    hs = valor.substr(0,2);
                    ms = valor.substr(pos+1,2);
                }
                if(ms==""){
                    ms = "00";
                }
            }
        } else {
            if (tam>=4) {
                hs = valor.substr(0,2);
                ms = valor.substr(2,2);
            }else if(tam==3){
                hs = "0"+valor.substr(0,1);
                ms = valor.substr(1,2);
            }else if(tam==2){
                hs = valor;
                ms = "00";
            }else if(tam==1){
                hs = "0"+valor;
                ms = "00";
            }
        }
        if (ms!="" && hs!="") {
            if (hs>24 || hs<0 || ms>60 || ms<0) {
                erro++;
            } else {
                if(ms==60){
                    ms = "59";
                }
                if(hs==24){
                    hs = "00";
                }
                hora = hs;
                minu = ms;
            }
        }
        if (document.form_atividade.q07_horafim.value != "" && erro == 0) {
            var botao   = document.getElementById("db_opcao");
            var val_ini = document.form_atividade.q07_horaini.value;
            var pos_ini = val_ini.indexOf(":");
            var hs_ini  = "";

            if (pos_ini == 1){
                hs_ini = "0" + val_ini.substr(0,1);
            } else if (pos_ini == 2){
                hs_ini = val_ini.substr(0,2);
            }

            debugger;
            if (valor != "") {
                document.form_atividade[campo].value = `${hora}:${minu}`;
            }

            var val_fin = document.form_atividade.q07_horafim.value;
            var pos_fin = val_fin.indexOf(":");
            var ms_fin  = "";

            if (pos_fin == 1){
                hs_fin = "0" + val_fin.substr(0,1);
            } else if (pos_fin == 2){
                hs_fin = val_fin.substr(0,2);
            }
        }
        if(erro>0){
            if (erro < 99){
                alert("Informe uma hora válida.");
            }
        }
        if(valor!=""){
            document.form_atividade[campo].focus();
            document.form_atividade[campo].value = `${hora}:${minu}`;
        }

    }


    function js_dtfim(){
        if(document.form_atividade.q07_ativ.value == ''){
            alert('Codigo da atividade não preenchido!');
            return false;
        }
        if(document.form_atividade.q07_datafi_dia.value == "" && document.form_atividade.q07_perman.value == "f" ){
            alert('Informe a data final para atividade provisória');
            document.form_atividade.q07_datafi.focus();
            return false;
        }
        salvarAtividade();
    }

    function js_tipcalc(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_ativid','func_tipcalcalt.php?funcao_js=parent.js_mostratip1|0|1','Pesquisa',true,0);
        }else{
            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_ativid','func_tipcalcalt.php?pesquisa_chave='+document.form_atividade.q11_tipcalc.value+'&funcao_js=parent.js_mostratip','Pesquisa',false,0);
        }
    }

    function js_mostratip(chave,erro){
        document.form_atividade.q81_descr.value = chave;
        if(erro==true){
            document.form_atividade.q11_tipcalc.focus();
            document.form_atividade.q11_tipcalc.value = '';
        }
    }

    function js_mostratip1(chave1,chave2) {
        document.form_atividade.q11_tipcalc.value = chave1;
        document.form_atividade.q81_descr.value = chave2;
        db_iframe_ativid.hide();
    }

    function js_pesquisaq07_ativ(mostra) {
        if ($('cnpjcpf').value.length == 14) {
            tipo='cnpj';
        }else{
            tipo='cpf';
        }
        if(mostra==true){
            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_ativid','func_atividade.php?tipo_pesquisa='+tipo+'&funcao_js=parent.js_mostraativid1|q03_ativ|q03_descr|q03_horaini|q03_horafim','Pesquisa',true,0);
        }else{
            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_ativid','func_atividade.php?tipo_pesquisa='+tipo+'&pesquisa_chave='+document.form_atividade.q07_ativ.value+'&funcao_js=parent.js_mostraativid','Pesquisa',false,0);
        }
    }

    function js_mostraativid(chave,chave1,chave2,erro) {
        document.form_atividade.q03_descr.value = chave;
        document.form_atividade.q07_horaini.value = chave1;
        document.form_atividade.q07_horafim.value = chave2;
        if(erro==true){
            document.form_atividade.q07_ativ.focus();
            document.form_atividade.q07_ativ.value = '';
            document.form_atividade.q07_horaini.value = '';
            document.form_atividade.q07_horafim.value='';
        }
    }

    function js_mostraativid1(chave1,chave2,chave3,chave4) {
        document.form_atividade.q07_ativ.value = chave1;
        document.form_atividade.q03_descr.value = chave2;

        document.form_atividade.q07_horaini.value = chave3;
        document.form_atividade.q07_horafim.value = chave4;

        db_iframe_ativid.hide();
    }

    //-----------------------------
    function js_testadata(valor) {

        if (valor=='t') {
            document.form_atividade.q07_datafi_dia.value="";
            document.form_atividade.q07_datafi_ano.value="";
            document.form_atividade.q07_datafi_mes.value="";

            document.form_atividade.q07_datafi_ano.style.backgroundColor = '#DEB887';
            document.form_atividade.q07_datafi_dia.style.backgroundColor = '#DEB887';
            document.form_atividade.q07_datafi_mes.style.backgroundColor = '#DEB887';

            document.form_atividade.q07_datafi.value="";
            document.form_atividade.q07_datafi.disabled=true;
            document.form_atividade.q07_datafi.style.backgroundColor = '#DEB887';

        } else {

            document.form_atividade.q07_datafi_dia.disabled=false;
            document.form_atividade.q07_datafi_ano.disabled=false;
            document.form_atividade.q07_datafi_mes.disabled=false;
            document.form_atividade.q07_datafi_ano.style.backgroundColor = '';
            document.form_atividade.q07_datafi_dia.style.backgroundColor = '';
            document.form_atividade.q07_datafi_mes.style.backgroundColor = '';

            document.form_atividade.q07_datafi.disabled=false;
            document.form_atividade.q07_datafi.style.backgroundColor = '';

        }

    }
</script>