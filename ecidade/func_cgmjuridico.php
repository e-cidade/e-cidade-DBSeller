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
require_once(modification("classes/db_cgmjuridico_classe.php"));
db_postmemory($_POST);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$dao = new cl_cgm;
$dao->rotulo->label("z01_numcgm");
$dao->rotulo->label("z01_nome");
$dao->rotulo->label("z01_cgccpf");

$where = array('length(z01_cgccpf) = 14');
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div class="container">
    <form name="form2" method="post" action="">
        <fieldset>
            <legend>Informe os filtros</legend>

            <table class="form-container">
                <tr title="<?= $Tz01_numcgm ?>">
                    <td>
                        <label for="chave_z01_numcgm"><?= $Lz01_numcgm ?></label>
                    </td>
                    <td>
                        <?php
                        db_input("z01_numcgm", 10, $Iz01_numcgm, true, "text", 4, "", "chave_z01_numcgm");
                        ?>
                    </td>
                </tr>
                <tr title="<?= $Tz01_nome ?>">
                    <td>
                        <label for="chave_z01_nome"><?= $Lz01_nome ?></label>
                    </td>
                    <td>
                        <?php
                        db_input("z01_nome", 50, $Iz01_nome, true, "text", 4, "", "chave_z01_nome");
                        ?>
                    </td>
                </tr>
                <tr title="Informe o CNPJ da empresa">
                    <td>
                        <label for="chave_z01_cgccpf">
                            CNPJ:
                        </label>
                    </td>
                    <td>
                        <?php db_input('z01_cgccpf', 20, $Iz01_cgccpf, true, 'text', 1,
                            "onkeyup='js_ValidaCampos(this,1,\"CNPJ\",\"\",\"\",event);'", 'chave_z01_cgccpf'); ?>
                    </td>
                </tr>

            </table>
        </fieldset>
        <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
        <input name="limpar" type="reset" id="limpar" value="Limpar">
        <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_cgmjuridico.hide();">
    </form>
</div>

<?php
$campos = "z01_numcgm, z01_nome, z01_cgccpf";
if (!isset($pesquisa_chave)) {

    if (isset($chave_z01_numcgm) && (trim($chave_z01_numcgm) != "")) {
        $where[] = "z01_numcgm = {$chave_z01_numcgm}";
    } elseif (!empty($chave_z01_nome)) {
        $where[] = "z01_nome ilike '{$chave_z01_nome}%'";
    } elseif (!empty($chave_z01_cgccpf)) {
        $where[] = "z01_cgccpf = '{$chave_z01_cgccpf}'";
    }

    $sql = '';
    if (!empty($chave_z01_numcgm) || !empty($chave_z01_nome) || !empty($chave_z01_cgccpf)) {
        $sql = $dao->sql_query_file(null, $campos, "z01_nome", implode(" and ", $where));
    }

    $repassa = array();
    if (isset($chave_z01_numcgm)) {
        $repassa = array(
            "chave_z01_numcgm" => $chave_z01_numcgm,
            "chave_z01_nome" => $chave_z01_nome,
            "chave_z01_cgccpf" => $chave_z01_cgccpf,
        );
    }

    echo '<div class="container" style="min-width: 750px;">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';
    db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
    echo '  </fieldset>';
    echo '</div>';

} else {
    if ($pesquisa_chave != null && $pesquisa_chave != "") {
        $where[] = "z01_numcgm = {$pesquisa_chave}";
        $sql = $dao->sql_query_file(null, $campos, "z01_nome", implode(" and ", $where));
        $result = $dao->sql_record($sql);
        if ($dao->numrows != 0) {
            db_fieldsmemory($result, 0);
            echo "<script>" . $funcao_js . "('$z01_nome',false, $z01_cgccpf);</script>";
        } else {
            echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
        }
    } else {
        echo "<script>" . $funcao_js . "('',false);</script>";
    }
}
?>

</body>
</html>
<?php
if (!isset($pesquisa_chave)) {
    ?>
    <script>
    </script>
    <?php
}
?>
<script>
    js_tabulacaoforms("form2", "chave_z01_numcgm", true, 1, "chave_z01_nome", true);
</script>
<script type="text/javascript">
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
