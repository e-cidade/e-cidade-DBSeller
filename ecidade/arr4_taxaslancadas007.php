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
require_once(modification("libs/db_app.utils.php"));

$clrotulo = new rotulocampo;
$clrotulo->label("ar47_codcam");
$clrotulo->label("ar47_obrigatorio");
$clrotulo->label("ar47_tipocampo");
$clrotulo->label("ar47_valordefault");
?>
<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="container">
        <form name="form1" method="post">
            <fieldset>
                <legend>Atributos</legend>
                <table class="form-container">
                    <tr>
                        <td nowrap title="<?=@$Tar47_codcam?>">
                            <?php db_ancora(@$Lar47_codcam,"js_pesquisaCampo(true);",$db_opcao);?>
                        </td>
                        <td>
                            <?php
                            db_input('ar47_codcam',5,$Iar47_codcam,true,'text',$db_opcao," onchange='js_pesquisaCampo(false);'")
                            ?>
                            <?php
                            db_input('nomecam',40,$Inomecam,true,'text',3,'')
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?=@$Tar47_obrigatorio?>">
                            <?= $Lar47_obrigatorio ?>
                        </td>
                        <td>
                            <select id="ar47_obrigatorio" name="ar47_obrigatorio">
                                <option value="f">Não</option>
                                <option value="t">Sim</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?=@$Tar47_tipocampo?>">
                            <?= $Lar47_tipocampo ?>
                        </td>
                        <td>
                            <select id="ar47_tipocampo" name="ar47_tipocampo" onchange="js_formataCampoDefault()">
                                <option value="1">Texto</option>
                                <option value="2">Hidden</option>
                                <option value="3">Data</option>
                                <option value="4">Verdadeiro / Falso</option>
                                <option value="5">Número sem casas decimais</option>
                                <option value="6">Número com casas decimais</option>
                                <option value="7">Combo</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="trValorDefault">
                        <td nowrap title="<?=@$Tar47_valordefault?>">
                            <?= $Lar47_valordefault ?>
                        </td>
                        <td>
                            <?php
                            db_textarea('ar47_valordefault', 1, 1, $Iar47_valordefault, true, 'text', 1, '')
                            ?>
                            <p style="margin: 0px;" id="mensagem"></p>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input name="salvar" id="salvar" type="button" onclick="js_salvar();" value="Salvar">
            <input name="fechar" id="fechar" type="button" onclick="parent.db_iframe_campos_dinamicos.hide();" value="Fechar">
        </form>
    </div>
    <center>
        <div id="ctnGridCampos" style="width: 650px;"></div>
    </center>
    <?php db_menu(); ?>
</body>

</html>
<script>
    function js_pesquisaCampo(mostra)
    {
        if(mostra==true){
            this.js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_db_syscampo','func_db_syscampo.php?funcao_js=parent.db_iframe_campos_dinamicos.jan.js_mostradb_syscampo1|codcam|nomecam','Pesquisa',true);
        }else{
            if (document.form1.ar47_codcam.value != '') {
                this.js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_db_syscampo','func_db_syscampo.php?pesquisa_chave='+document.form1.ar47_codcam.value+'&funcao_js=parent.db_iframe_campos_dinamicos.jan.js_mostradb_syscampo','Pesquisa',false);
            } else {
                document.form1.nomecam.value = '';
            }
        }
    }

    function js_mostradb_syscampo(chave,erro)
    {
        document.form1.nomecam.value = chave;

        if (erro==true) {
            document.form1.ar47_codcam.focus();
            document.form1.ar47_codcam.value = '';
        }
    }

    function js_mostradb_syscampo1(chave1,chave2)
    {
        document.form1.ar47_codcam.value = chave1;
        document.form1.nomecam.value = chave2;
        parent.db_iframe_db_syscampo.hide();
    }

    function js_salvar()
    {
        if (js_verificaCampoObrigatorio()) {
            return false;
        }

        const obj = document.form1;

        const oCampos = new Object();
        oCampos.ar47_codcam = obj.ar47_codcam.value;
        oCampos.nomecam = obj.nomecam.value.trim();
        oCampos.ar47_obrigatorio = obj.ar47_obrigatorio.value;
        oCampos.ar47_tipocampo = obj.ar47_tipocampo.value;
        oCampos.ar47_valordefault = obj.ar47_valordefault.value;

        parent.setCampos(oCampos);

        js_listaCampos();

        obj.ar47_codcam.value = "";
        obj.nomecam.value = "";
        obj.ar47_obrigatorio.value = "f";
        obj.ar47_tipocampo.value = "1";
        obj.ar47_valordefault.value = "";
    }

    var oGridCampos = new DBGrid('gridCampos');
    var aHeaders   = ["Código", "Nome", "Obrigatório", "Tipo Campo", "Default", "Ação"];
    var aCellWidth = ["10%", "25%", "15%", "25%", "10%", "15%"];
    var aCellAlign = ["center", "left", "center", "center", "center", "center"];

    oGridCampos.nameInstance = 'oGridCampos';
    oGridCampos.setCellWidth(aCellWidth);
    oGridCampos.setCellAlign(aCellAlign);
    oGridCampos.setHeader(aHeaders);
    oGridCampos.setHeight(100);
    oGridCampos.show($('ctnGridCampos'));

    js_listaCampos();

    function js_listaCampos(linhasRemover = [])
    {
        oGridCampos.clearAll(true);

        const oCampos = parent.getCampos();

        oCampos.forEach(function (oCampo){
            var aLinha = [];
            aLinha.push(oCampo.ar47_codcam);
            aLinha.push(oCampo.nomecam);
            aLinha.push((oCampo.ar47_obrigatorio == "t" ? "Sim" : "Não"));
            var tipoCampo = "";

            if (oCampo.ar47_tipocampo == 1) {
                tipoCampo = "Text"
            } else {
                if (oCampo.ar47_tipocampo == 2) {
                    tipoCampo = "Hidden"
                } else {
                    if (oCampo.ar47_tipocampo == 3) {
                        tipoCampo = "Data"
                    } else {
                        if (oCampo.ar47_tipocampo == 4) {
                            tipoCampo = "Verdadeiro / Falso"
                        } else {
                            if (oCampo.ar47_tipocampo == 5) {
                                tipoCampo = "Número sem casas decimais"
                            } else {
                                if (oCampo.ar47_tipocampo == 6) {
                                    tipoCampo = "Número sem casas decimais"
                                } else {
                                    if (oCampo.ar47_tipocampo == 7) {
                                        tipoCampo = "Combo"
                                    }
                                }
                            }
                        }
                    }
                }
            }


            aLinha.push(tipoCampo);
            aLinha.push(oCampo.ar47_valordefault);

            var oBtnAlterar = document.createElement('input');
            oBtnAlterar.setAttribute("value", "A");
            oBtnAlterar.setAttribute("type", "button");
            oBtnAlterar.setAttribute("id", "btnAlterar_" + oCampo.ar47_codcam);
            oBtnAlterar.setAttribute("onclick", "js_alterarCampo("+oCampo.ar47_codcam+")");

            var oBtnExcluir = document.createElement('input');
            oBtnExcluir.setAttribute("value", "R");
            oBtnExcluir.setAttribute("type", "button");
            oBtnExcluir.setAttribute("id", "btnRemover_" + oCampo.ar47_codcam);
            oBtnExcluir.setAttribute("onclick", "js_removerCampo("+oCampo.ar47_codcam+")");

            aLinha.push(oBtnAlterar.outerHTML+" "+oBtnExcluir.outerHTML);

            oGridCampos.addRow(aLinha);
        });

        if (linhasRemover.length > 0) {
            oGridCampos.removeRow(linhasRemover);
        }

        oGridCampos.renderRows();
    }

    function js_removerCampo(ar47_codcam)
    {
        parent.js_removerCampo(ar47_codcam);

        js_listaCampos();
    }

    function js_alterarCampo(ar47_codcam)
    {
        const oCampos = parent.js_alterarCampo(ar47_codcam);
        const obj = document.form1;

        obj.ar47_codcam.value = oCampos.ar47_codcam;
        obj.nomecam.value = oCampos.nomecam;
        obj.ar47_obrigatorio.value = oCampos.ar47_obrigatorio;
        obj.ar47_tipocampo.value = oCampos.ar47_tipocampo;
        obj.ar47_valordefault.value = oCampos.ar47_valordefault;

        js_formataCampoDefault();

        js_listaCampos([oCampos.key]);
    }

    function js_formataCampoDefault()
    {
        const campo = document.getElementById("ar47_tipocampo").value;

        const trValorDefault = document.getElementById("trValorDefault");
        trValorDefault.show();

        const ar47_valordefault = document.getElementById("ar47_valordefault");
        ar47_valordefault.removeAttribute("isMandatory");

        const mensagem = document.getElementById("mensagem");
        var mensagemTexto = "";

        if (campo == 3)
        {
            mensagemTexto = "<table>";
                mensagemTexto += "<tr>";
                    mensagemTexto += "<td>Opções:</td>";
                    mensagemTexto += "<td><strong>now</strong> para o dia corrente;</td>";
                mensagemTexto += "</tr>";
                mensagemTexto += "<tr>";
                    mensagemTexto += "<td></td>";
                    mensagemTexto += "<td><strong>now#10</strong> para o dia corrente mais a quantidade de dias;</td>";
                mensagemTexto += "</tr>";
                mensagemTexto += "<tr>";
                    mensagemTexto += "<td></td>";
                    mensagemTexto += "<td><strong>now|10</strong> para o dia corrente menos a quantidade de dias;</td>";
                mensagemTexto += "</tr>";
            mensagemTexto += "</table>";
        }
        else if (campo == 4)
        {
            trValorDefault.hide();
            ar47_valordefault.value = "";
        }
        else if (campo == 7)
        {
            mensagemTexto = "<table>";
                mensagemTexto += "<tr>";
                    mensagemTexto += "<td>Formato:</td>";
                    mensagemTexto += "<td><strong>#</strong> para dividir label e valor;</td>";
                mensagemTexto += "</tr>";
                mensagemTexto += "<tr>";
                    mensagemTexto += "<td></td>";
                    mensagemTexto += "<td><strong>|</strong> para dividir options;</td>";
                mensagemTexto += "</tr>";
                mensagemTexto += "<tr>";
                    mensagemTexto += "<td>Ex.:</td>";
                    mensagemTexto += "<td><strong>teste1#1|teste2#2</strong></td>";
                mensagemTexto += "</tr>";
            mensagemTexto += "</table>";

            ar47_valordefault.setAttribute("isMandatory", "true");
        }

        mensagem.innerHTML = mensagemTexto;
    }
</script>
