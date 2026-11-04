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

    db_postmemory($_GET);

    $clrotulo = new rotulocampo;
    $clrotulo->label("j167_sequencial");
    $clrotulo->label("j167_descricao");
    $clrotulo->label("j167_numcgm");
    $clrotulo->label("j167_observacao");
    $clrotulo->label("z01_nome");
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
    <form name="form1" method="post">
        <input type="hidden" name="db_opcao" id="db_opcao" value="<?= $db_opcao ?>">
        <fieldset>
            <legend>Cartório Extrajudicial</legend>
            <table class="form-container">
                <tr>
                    <td title="<?= @$Tj167_sequencial ?>" style="width: 70px;">
                        <strong>Código:</strong>
                    </td>
                    <td>
                        <?php
                            db_input("j167_sequencial", 5, @$Ij167_sequencial, "j167_sequencial", "text", 3);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="<?= @$Tj167_descricao ?>" style="width: 70px;">
                        <?= @$Lj167_descricao ?>
                    </td>
                    <td>
                        <?php
                            db_input("j167_descricao", 50, @$Ij167_descricao, "j167_descricao", "text", $db_opcao);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?=@$Tj167_numcgm?>">
                        <?php
                            db_ancora(@$Lj167_numcgm,"js_pesquisaNumcgm(true);",$db_opcao);
                        ?>
                    </td>
                    <td>
                        <?php
                            db_input('j167_numcgm',5,$Ij167_numcgm,true,'text',$db_opcao," onchange='js_pesquisaNumcgm(false);'")
                        ?>
                        <?php
                            db_input('z01_nome',41,$Iz01_nome,true,'text',3,'')
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 70px;">
                        <strong>Tipo:</strong>
                    </td>
                    <td id="tdTipos"></td>
                </tr>
                <tr>
                    <td nowrap title="<?=@$Tj167_observacao?>" colspan="2">
                        <fieldset class="separator">
                            <legend><label for='j167_observacao'><?=@$Lj167_observacao?></label></legend>
                            <?php
                                db_textarea('j167_observacao',5,50,$Ij167_observacao,true,'text',$db_opcao,"")
                            ?>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="salvar" id="salvar" type="button" onclick="js_salvar();" value="Salvar">
        <?php if ($db_opcao == 2) : ?>
            <input name="pesquisar" id="pesquisar" type="button" onclick="js_pesquisaCartorios();" value="Pesquisar">
        <?php endif; ?>
    </form>
</div>
<?php db_menu(); ?>
</body>
</html>
<script>
if (document.getElementById("db_opcao").value == "2") {
    js_pesquisaCartorios();
}

function js_pesquisaCartorios()
{
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_cartorioextra','func_cartorioextra.php?funcao_js=parent.js_mostraCartorio|j167_sequencial|j167_descricao','Pesquisa',true);
}

function js_mostraCartorio(j167_sequencial, j167_descricao)
{
    document.getElementById("j167_sequencial").value = j167_sequencial;
    document.getElementById("j167_descricao").value = j167_descricao;

    js_buscarTipo(j167_sequencial);

    db_iframe_cartorioextra.hide();
}

function js_buscarTipo(j167_sequencial)
{
    const oParam = new Object();
    oParam.executa = "buscarDadosCartorio";
    oParam.j167_sequencial = j167_sequencial;

    new AjaxRequest("jur01_cartorioextra001.RPC.php", oParam, function (oRetorno){
        if (oRetorno.erro) {
            alert(oRetorno.mensagem);
            return false;
        }

        document.getElementById("j167_numcgm").value = oRetorno.oCartorio.j167_numcgm;
        document.getElementById("j167_observacao").value = oRetorno.oCartorio.j167_observacao;

        oRetorno.aTipos.forEach(function (oTipo){
            console.log(oTipo);
            document.getElementById("tipo_"+oTipo.j168_tiposcartorioextra).checked = true;
        });

        js_pesquisaNumcgm(false);
    }).execute();
}

js_buscarTipos();

function js_buscarTipos()
{
    const oParam = new Object();
    oParam.executa = "listarTipos";

    new AjaxRequest("jur01_cartorioextra001.RPC.php", oParam, function (oRetorno){
        if (oRetorno.erro) {
            alert(oRetorno.mensagem);
            return false;
        }

        const tdTipos = document.getElementById("tdTipos");

        oRetorno.aTipos.forEach(function (oTipos){
            const oInput = document.createElement("input");
            oInput.setAttribute("type", "checkbox");
            oInput.setAttribute("id", `tipo_${oTipos.j169_sequencial}`);
            oInput.setAttribute("value", oTipos.j169_sequencial);
            oInput.setAttribute("style", "margin-left: 0px;");

            const oLabel = document.createElement("label");
            oLabel.setAttribute("for", `tipo_${oTipos.j169_sequencial}`);
            oLabel.setAttribute("style", "position: relative; top: -5px; font-weight: normal; white-space: pre-line;");
            oLabel.innerHTML = oTipos.j169_descricao;

            const br = document.createElement("br");

            tdTipos.appendChild(oInput);
            tdTipos.appendChild(oLabel);
            tdTipos.appendChild(br);
        });
    }).execute();
}

function js_salvar()
{
    if (js_verificaCampoObrigatorio(["j167_sequencial"])) {
        return false;
    }

    var aTipos = document.querySelectorAll('input[type="checkbox"]:checked');
    aTipos = [].map.call(aTipos, function(oTipo) {
        return oTipo.value;
    });

    if (aTipos.length == 0) {
        alert("Selecione um tipo de cartório.");
        return false;
    }

    const oParam = new Object();
    oParam.executa = "salvar";
    oParam.j167_sequencial = document.getElementById("j167_sequencial").value;
    oParam.j167_descricao = document.getElementById("j167_descricao").value;
    oParam.j167_numcgm = document.getElementById("j167_numcgm").value;
    oParam.j167_observacao = document.getElementById("j167_observacao").value;
    oParam.sTipos = aTipos.join();

    new AjaxRequest("jur01_cartorioextra001.RPC.php", oParam, function (oRetorno){
        alert(oRetorno.mensagem);

        if (oRetorno.erro) {
            return false;
        }

        document.form1.reset();

        if (document.getElementById("db_opcao").value == "2") {
            js_pesquisaCartorios();
        }
    }).execute();
}

function js_pesquisaNumcgm(mostra)
{
    if (mostra == true) {
        js_OpenJanelaIframe('CurrentWindow.corpo','func_nome','func_cgm.php?lNovoDetalhe=1&funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome','Pesquisa',true);
    } else {
        if(document.form1.j167_numcgm.value != ''){
            js_OpenJanelaIframe('CurrentWindow.corpo','func_nome','func_cgm.php?lNovoDetalhe=1&pesquisa_chave='+document.form1.j167_numcgm.value+'&funcao_js=parent.js_mostracgm','Pesquisa',false);
        }else{
            document.form1.z01_nome.value = '';
        }
    }
}

function js_mostracgm(chave,erro)
{
    document.form1.z01_nome.value = chave;

    if (erro == true) {
        document.form1.j167_numcgm.focus();
        document.form1.j167_numcgm.value = '';
    }
}

function js_mostracgm1(chave1,chave2)
{
    document.form1.j167_numcgm.value = chave1;
    document.form1.z01_nome.value = chave2;

    func_nome.hide();
}
</script>
