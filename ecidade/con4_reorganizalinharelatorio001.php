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

use ECidade\Configuracao\RelatorioLegal\Modelo\Relatorio;
use ECidade\Configuracao\RelatorioLegal\Registry\RelatorioRegistry;
use ECidade\Configuracao\RelatorioLegal\Repositorio\LinhaRepositorio;

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_utils.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('classes/db_db_config_classe.php');
require_once modification('classes/db_orcparamseq_classe.php');
require_once modification('dbforms/db_funcoes.php');

$parametros = JSON::requestParameters();
$relatorio = null;
$oDaoOrcparamseq = new cl_orcparamseq();
$oGET = db_utils::postMemory($_GET);
$db_opcao = 1;

if (isset($parametros->chavepesquisa)) {
    $relatorio = RelatorioRegistry::get($parametros->chavepesquisa);
    $chavepesquisa = $parametros->chavepesquisa;
    $db_opcao = 22;
}

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>DBSeller Informática Ltda</title>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script src="scripts/scripts.js"></script>
    <script src="scripts/classes/http/http.js"></script>
</head>
<body>
<div class="container">
    <form method="post" name="form1" id="formLinhas" onsubmit="return false;" style="width: 600px">
        <input type="hidden" name="relatorio" id="relatorio"
               value="<?php echo isset($chavepesquisa) ? $chavepesquisa : ''; ?>">
        <?php if ($relatorio instanceof Relatorio) {
    ?>
            <fieldset>
                <legend>Relatório</legend>
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td style="width: 15%">
                            <input class="form-control" id="relatorio" name="relatorio" disabled
                                   value="<?php echo $relatorio->getSequencial(); ?>" style="width: 100%">
                        </td>
                        <td style="width: 85%">
                            <input class="form-control" id="descricao" name="descricao" disabled
                                   value="<?php echo $relatorio->getDescricao(); ?>" style="width: 100%">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        <?php
} ?>
        <fieldset>
            <legend>Ordenar as Linhas</legend>
            <table style="width: 100%">
                <tr>
                    <td style="width: 90%">
                        <select name="linhas[]" id="linhas" size="15" multiple style="width: 100%">
                            <?php
                            if (isset($chavepesquisa)) {
                                $linhaRepositorio = new LinhaRepositorio();
                                $linhas = $linhaRepositorio->scopeRelatorio($relatorio)
                                    ->addOrder('o69_ordem')
                                    ->setUseLeftJoin(false)
                                    ->get();

                                foreach ($linhas as $linha) {
                                    $nivel = $linha->getNivelLinha() * 10;
                                    $sStyle = "margin-left: {$nivel}px;";
                                    if ($linha->isTotalizadora()) {
                                        $sStyle .= "font-weight: bold;";
                                    } ?>
                                    <option style="<?php echo $sStyle; ?>" value="<?php echo $linha->getLinha(); ?>">
                                        <?php echo $linha->getDescricao() ?: '-'; ?>
                                    </option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td style="width: 10%">
                        <img style="cursor: pointer" onclick="js_sobe(); return false;"
                             src="skins/img.php?file=Controles/seta_up.png"/>
                        <br/><br/>
                        <img style="cursor: pointer" onclick="js_desce()"
                             src="skins/img.php?file=Controles/seta_down.png"/>
                        <br/><br/>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="db_opcao" type="button" id="db_opcao" value="Salvar">
        <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
    </form>
</div>
<?php
db_menu();
?>
<script>
    const rpc = 'con1_relatorio_legal.RPC.php';
    const formLinhas = document.getElementById('formLinhas');
    const btnReordenar = document.getElementById('db_opcao');

    const selecionar = () => {
        var F = document.getElementById('linhas').options;
        for (var i = 0; i < F.length; i++) {
            F[i].selected = true;
        }
        return true;
    };

    btnReordenar.addEventListener('click', () => {
        selecionar();
        const data = new FormData(formLinhas);
        data.append('acao', 'reordenarLinhas');

        HttpClient.post(rpc, {body: data}).then(response => alert(response.mensagem));
    });

    function js_sobe() {
        var F = document.getElementById('linhas');

        if (F.selectedIndex != -1 && F.selectedIndex > 0) {
            var SI = F.selectedIndex - 1;
            var auxText = F.options[SI].text;
            var auxValue = F.options[SI].value;
            var sStyle = F.options[SI + 1].style;
            var sStyleOther = F.options[SI].style;

            F.options[SI] = new Option(F.options[SI + 1].text, F.options[SI + 1].value);
            F.options[SI].style.fontWeight = sStyle.fontWeight;
            F.options[SI].style.marginLeft = sStyle.marginLeft;
            F.options[SI + 1] = new Option(auxText, auxValue);
            F.options[SI + 1].style.fontWeight = sStyleOther.fontWeight;
            F.options[SI + 1].style.marginLeft = sStyleOther.marginLeft;
            F.options[SI].selected = true;
        }
    }

    function js_desce() {
        var F = document.getElementById('linhas');

        if (F.selectedIndex != -1 && F.selectedIndex < (F.length - 1)) {
            var SI = F.selectedIndex + 1;
            var auxText = F.options[SI].text;
            var auxValue = F.options[SI].value;
            var sStyle = F.options[SI - 1].style;
            var sStyleOther = F.options[SI].style;

            F.options[SI] = new Option(F.options[SI - 1].text, F.options[SI - 1].value);
            F.options[SI].style.fontWeight = sStyle.fontWeight;
            F.options[SI].style.marginLeft = sStyle.marginLeft;
            F.options[SI - 1] = new Option(auxText, auxValue);
            F.options[SI - 1].style.fontWeight = sStyleOther.fontWeight;
            F.options[SI - 1].style.marginLeft = sStyleOther.marginLeft;
            F.options[SI].selected = true;
        }
    }

    function js_pesquisa() {
        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_orcparamrel',
            'func_orcparamrel.php?funcao_js=parent.js_preenchepesquisa|o42_codparrel',
            'Pesquisa',
            true,
            '0',
            '1'
        );
    }

    function js_preenchepesquisa(chave) {
        db_iframe_orcparamrel.hide();
        location.href = `<?php echo basename($_SERVER['PHP_SELF']); ?>?chavepesquisa=${chave}`;
    }

    <?php
    if ($db_opcao != 22) {
        ?>
    document.form1.pesquisar.click();
    <?php
    } ?>
</script>
</body>
</html>
