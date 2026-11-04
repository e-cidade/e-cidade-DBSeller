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

require_once modification('dbforms/db_classesgenericas.php');

$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
//MODULO: pessoal
$clrhdepend->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");

if (!isset($Ip58_numero)) {
    $Ip58_numero = null;
}

?>
<form name="form1" method="post" action="">
    <center>
        <table border="0">
            <tr>
                <td nowrap title="<?=@$Trh31_regist?>">
                <fieldset>
                        <legend>
                            <b>Dependentes</b>
                        </legend>
                        <table align="center" >
                            <tr>
                                <td nowrap title="<?=@$Trh16_regist?>">
                                    <?php db_ancora(@$Lrh31_regist, "", 3);?>
                                </td>
                                <td>
                                    <?php
                                    db_input('rh31_regist', 6, $Irh31_regist, true, 'text', 3, "");
                                    db_input('rh31_codigo', 6, $Irh31_codigo, true, 'hidden', 3);
                                    db_input('z01_nome', 40, $Iz01_nome, true, 'text', 3, '');
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?=@$Trh31_nome?>">
                                    <?php db_ancora(@$Lrh31_nome, "js_pesquisarh01_numcgm(true);", $db_opcao);?>
                                </td>
                                <td>
                                    <?php
                                    db_input(
                                        'rh31_nome',
                                        70,
                                        empty($Irh31_nome) ? null : $Irh31_nome,
                                        true,
                                        'text',
                                        $db_opcao,
                                        '',
                                        '',
                                        '',
                                        '',
                                        70
                                    );
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?=@$Trh31_dtnasc?>">
                                    <?=@$Lrh31_dtnasc?>
                                </td>
                                <td>
                                    <?php
                                    db_inputdata(
                                        'rh31_dtnasc',
                                        @$rh31_dtnasc_dia,
                                        @$rh31_dtnasc_mes,
                                        @$rh31_dtnasc_ano,
                                        true,
                                        'text',
                                        $db_opcao,
                                        ""
                                    );
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td><b>CPF</b></td>
                                <td>
                                    <?php
                                    db_input(
                                        'dp01_cpf',
                                        15,
                                        @$Iz01_cpf,
                                        true,
                                        'text',
                                        $db_opcao,
                                        "onBlur='js_verificaCGCCPF(this);'",
                                        '',
                                        '',
                                        'text-align:left;',
                                        11
                                    );
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Sexo:</b>
                                </td>
                                <td>
                                    <?php
                                    $sex = ["M" => "Masculino", "F" => "Feminino"];
                                    db_select('dp01_sexo', $sex, true, $db_opcao, 'style="width:125px;"');
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?=@$Trh31_gparen?>">
                                    <?=@$Lrh31_gparen?>
                                </td>
                                <td id="tipo_parentesco">
                                    <?php
                                    $arr_gparen = [
                                        'C'=>'Cônjuge',
                                        'F'=>'Filho',
                                        'P'=>'Pai',
                                        'M'=>'Mãe',
                                        'A'=>'Avó',
                                        'O'=>'Outros'
                                    ];
                                    db_select("rh31_gparen", $arr_gparen, true, $db_opcao);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= @$Trh31_tipoparentesco ?>">
                                    <?= @$Lrh31_tipoparentesco ?>
                                </td>
                                <td>
                                    <?php
                                    $tipo_dependente = [
                                        '01'=>'01 Cônjuge',
                                        '02'=>'02 Companheiro(a) com o(a) qual tenha filho ou viva há mais de 5 (cinco) anos ou possua declaração de união estável',
                                        '03'=>'03 Filho(a) ou enteado(a)',
                                        '06'=>'06 Irmão(ã), neto(a) ou bisneto(a) sem arrimo dos pais, do(a) qual detenha a guarda judicial',
                                        '09'=>'09 Pais, avós e bisavós',
                                        '10'=>'10 Menor pobre do qual detenha a guarda judicial',
                                        '11'=>'11 A pessoa absolutamente incapaz, da qual seja tutor ou curador',
                                        '12'=>'12 Ex-cônjuge',
                                        '99'=>'99 Agregado/Outros'
                                    ];

                                    db_select("rh31_tipoparentesco", $tipo_dependente, true, $db_opcao, "style='width : 300px;' onchange=exibeDescricaoParentesco();");
                                    ?>
                                </td>
                            </tr>
                            <tr id="exibeDescricaoParentesco">
                                <td nowrap title="<?= @$Trh31_descricaoparentesco ?>">
                                    <?= @$Lrh31_descricaoparentesco ?>
                                </td>
                                <td>
                                    <?php
                                    db_textarea(
                                        'rh31_descricaoparentesco',
                                        3,
                                        78,
                                        $Irh31_descricaoparentesco,
                                        true,
                                        'text',
                                        $db_opcao,
                                        ""
                                    );
                                   ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?=@$Trh31_depend?>">
                                    <?=@$Lrh31_depend?>
                                </td>
                                <td>
                                    <?php
                                    if (!isset($rh31_depend)) {
                                        $rh31_depend = "N";
                                    }

                                    $arr_depend = [
                                        'C'=>'Cálculo',
                                        'S'=>'Sempre dependente',
                                        'N'=>'Não dependente'
                                    ];
                                    db_select("rh31_depend", $arr_depend, true, $db_opcao);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?=@$Trh31_irf?>">
                                    <?=@$Lrh31_irf?>
                                </td>
                                <td>
                                    <?php
                                    $arr_irf = [
                                        '0' => 'Não Dependente',
                                        '1' => 'Cônjuge,Companheiro(a)',
                                        '2' => 'Filho(a)/Enteado(a), até 21 anos de idade',
                                        '3' => 'Filho(a) ou enteado(a),  24 anos de idade cursando ensino superior',
                                        '4' => 'Irmão(ã), neto(a) ou bisneto(a),  até 21 anos',
                                        '5' => 'Irmão(ã), neto(a) ou bisneto(a), de 21 a 24 anos c/ensino superior',
                                        '6' => 'Pais, avós e bisavós',
                                        '7' => 'Menor pobre até 21 anos, com a guarda judicial',
                                        '8' => 'Pessoa absolutamente incapaz'
                                    ];
                                    db_select("rh31_irf", $arr_irf, true, $db_opcao);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?=@$Trh31_especi?>">
                                    <?=@$Lrh31_especi?>
                                </td>
                                <td>
                                    <?php
                                    if (!isset($rh31_especi)) {
                                        $rh31_especi = "N";
                                    }
                                    $arr_especi = [
                                        'N' => 'Não dependente',
                                        'C' => 'Cálculo',
                                        'S' => 'Sempre dependente'
                                    ];
                                    db_select("rh31_especi", $arr_especi, true, $db_opcao);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="Dependente para fins previdenciários">
                                    <strong>Dependente para fins previdenciários:</strong>
                                </td>
                                <td>
                                    <?php
                                    db_select(
                                        'rh31_fins_previdenciarios', 
                                        [
                                            'f' => 'Não',
                                            't' => 'Sim'
                                        ],
                                        true,
                                        $db_opcao
                                    );
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="Dependente Complementar somente para fins de IR">
                                    <strong>Dependente Complementar somente para fins de IR:</strong>
                                </td>
                                <td>
                                    <?php
                                    db_select(
                                        'rh31_irfcomplementar', 
                                        [
                                            'f' => 'Não',
                                            't' => 'Sim'
                                        ],
                                        true,
                                        $db_opcao
                                    );
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
        </table>
    </center>
    <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
    <?php
    if (isset($opcao)) {
        echo "<input name='novo' type='button' id='novo' value='Novo' onclick='document.location.href=\"pes1_rhdepend001.php?rh31_regist=$rh31_regist&vmenu=true\"' >";
    }
    ?>
    <table width="90%">
        <tr>
            <td valign="top"  align="center" width="90%" heigth="100%">
                <?php
                $dbwhere = " rh31_regist = $rh31_regist ";
                if (isset($rh31_codigo) && trim($rh31_codigo) != "") {
                    $dbwhere .= " and rh31_codigo <> $rh31_codigo ";
                }
                $sql = $clrhdepend->sql_query_file(
                    null,
                    "rh31_codigo,
                    rh31_regist,
                    rh31_nome,
                    rh31_dtnasc,
                    rh31_fins_previdenciarios,
                    (select case
                        when dp01_processo is not null then 'SIM'
                        else 'NAO' end as p58_codproc  from rhdependeplug where dp01_rhdepend = rh31_codigo) as p58_numero,
                    (select  dp01_cpf from rhdependeplug where dp01_rhdepend = rh31_codigo) as z01_cgccpf,
                    (select  dp01_sexo from rhdependeplug where dp01_rhdepend = rh31_codigo) as z01_sexo,
                        case rh31_gparen
                            when 'C' then 'Conjuje'
                            when 'F' then 'Filho'
                            when 'P' then 'Pai'
                            when 'M' then 'Mãe'
                            when 'A' then 'Avó'
                        else 'Outros'
                        end
                        as rh31_gparen
                        ,
                        case when rh31_depend='C' then
                            'Cálculo'
                            else case when rh31_depend='S' then
                                'Sempre dependente'
                                    else
                                'Não dependente'
                            end
                        end
                        as rh31_depend,

                        case rh31_irf
                            when '0' then 'Não dependente'
                            when '1' then 'Cônjuge,Companheiro(a)'
                            when '2' then 'Filho(a)/Enteado(a), até 21 anos de idade'
                            when '3' then 'Filho(a) ou enteado(a),  24 anos de idade cursando ensino superior'
                            when '4' then 'Irmão(ã), neto(a) ou bisneto(a),  até 21 anos'
                            when '5' then 'Irmão(ã), neto(a) ou bisneto(a), de 21 a 24 anos c/ensino superior'
                            when '6' then 'Pais, avós e bisavós'
                            when '7' then 'Menor pobre até 21 anos, com a guarda judicia'
                        else 'Pessoa absolutamente incapaz'
                        end as rh31_irf
                        ,
                        case when rh31_especi='C' then
                            'Cálculo'
                            else case when rh31_especi='S' then
                                'Sempre dependente'
                                    else
                                'Não dependente'
                            end
                        end
                        as rh31_especi
                        ",
                    "rh31_nome",
                    $dbwhere
                );
                $asopcoes = 1;

                $chavepri= array("rh31_codigo"=>@$rh31_codigo);
                $cliframe_alterar_excluir->chavepri=$chavepri;
                $cliframe_alterar_excluir->sql = $sql;
                $cliframe_alterar_excluir->campos  ="rh31_nome,rh31_dtnasc,rh31_gparen,rh31_depend,rh31_irf,rh31_especi,rh31_fins_previdenciarios";
                $cliframe_alterar_excluir->legenda="Dependentes Lançados";
                $cliframe_alterar_excluir->iframe_height ="100%";
                $cliframe_alterar_excluir->iframe_width ="100%";
                $cliframe_alterar_excluir->opcoes = $asopcoes;
                $cliframe_alterar_excluir->iframe_alterar_excluir(1);
                ?>
            </td>
        </tr>
    </table>
</form>
<script>
    function js_pesquisa() {
        js_OpenJanelaIframe (
            '',
            'db_iframe_rhdepend',
            'func_rhdepend.php?funcao_js=parent.js_preenchepesquisa|rh31_regist',
            'Pesquisa',
            true
        );
    }

    function js_preenchepesquisa(chave) {
        db_iframe_rhdepend.hide();
        <?php
        if($db_opcao!=1){
            echo "  location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
        }
        ?>
    }

    function js_pesquisarh01_numcgm(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe(
                '',
                'func_dependete',
                'func_dependete.php?funcao_js=parent.js_mostracgm1|z01_nome|z01_sexo|z01_nasc|z01_cgccpf',
                'Pesquisa',
                true,
                '0'
            );
        }
    }

    function js_mostracgm(erro, chave1, chave2, chave3, chave4) {
        document.form1.rh01_numcgm.value = chave1;
        document.form1.z01_nome.value = chave2;
        if (chave3 != "") {
            document.form1.rh01_sexo.value = chave3;
        }
        if (chave4 != "") {
            document.form1.rh01_nasc_dia.value = chave4.substr(8, 2);
            document.form1.rh01_nasc_mes.value = chave4.substr(5, 2);
            document.form1.rh01_nasc_ano.value = chave4.substr(0, 4);
        }
        if (erro == true) {
            document.form1.rh01_numcgm.focus();
            document.form1.rh01_numcgm.value = '';
        }
    }

    function js_mostracgm1(chave1, chave2, chave3, chave4) {
        document.form1.rh31_nome.value = chave1;
        document.form1.dp01_sexo.value = chave2;
        if (chave3 != "") {
            document.form1.rh31_dtnasc_dia.value = chave3.substr(8,2);
            document.form1.rh31_dtnasc_mes.value = chave3.substr(5,2);
            document.form1.rh31_dtnasc_ano.value = chave3.substr(0,4);
            document.form1.rh31_dtnasc.value = chave3.substr(8, 2) + "/" + chave3.substr(5, 2) + "/" + chave3.substr(0, 4);
        }
        document.form1.dp01_cpf.value = chave4;
        func_dependete.hide();
    }

    function valida_campos() {
        var proc;
        proc = document.form1.processo.value;
        if (proc == "S") {
            if (document.form1.p58_numero.value == "") {
                alert('Informe o Processo');
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    function exibeDescricaoParentesco() {
        let tipoParentesco = document.getElementById("rh31_tipoparentesco").value;
        let exibeDescricaoParentesco = document.getElementById("exibeDescricaoParentesco");
        exibeDescricaoParentesco.style.display = 'none';
        if (tipoParentesco !== "") {
            if (tipoParentesco == "99") {
                exibeDescricaoParentesco.style.display = 'contents';
            } else {
                document.getElementById("rh31_descricaoparentesco").value = "";
            }
        } else {
            document.getElementById("rh31_descricaoparentesco").value = "";
        }
    }

    (function () {
        exibeDescricaoParentesco();
    })();
</script>
