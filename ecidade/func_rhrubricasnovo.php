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

use ECidade\RecursosHumanos\Pessoal\Service\RubricasUsuarioService;

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('dbforms/db_funcoes.php');
require_once modification('classes/db_rhrubricas_classe.php');

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

$clrhrubricas = new cl_rhrubricas();
$clrhrubricas->rotulo->label('rh27_rubric');
$clrhrubricas->rotulo->label('rh27_descr');

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body>
<form name="form2" method="post" class="container">
    <fieldset>
        <legend>Dados para Pesquisa</legend>
        <table class="form-container">
            <tr>
                <td>
                    <label for="chave_rh27_rubric"><?php echo $Lrh27_rubric; ?></label>
                </td>
                <td>
                    <?php db_input('rh27_rubric', 4, $Irh27_rubric, true, 'text', 4, '', 'chave_rh27_rubric'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="chave_rh27_descr"><?php echo $Lrh27_descr; ?></label>
                </td>
                <td>
                    <?php db_input('rh27_descr', 30, $Irh27_descr, true, 'text', 4, '', 'chave_rh27_descr'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="opcao">Seleção por:</label>
                </td>
                <td>
                    <?php

                    if (!isset($opcao)) {
                        $opcao = 't';
                    }

                    if (!isset($opcao_bloq)) {
                        $opcao_bloq = 1;
                    }

                    $arr_opcao = array('i' => 'Todos', 't' => 'Ativos', 'f' => 'Inativos');
                    db_select('opcao', $arr_opcao, true, $opcao_bloq);

                    ?>
                </td>
            </tr>
        </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar">
    <input name="Fechar" type="button" id="fechar" value="Fechar" onclick="parent.db_iframe_rhrubricas.hide()">
</form>
<?php

$dao = new cl_rhrubricas();
$service = new RubricasUsuarioService();
$instituicao = InstituicaoRepository::getInstituicaoSessao();
$usuario = UsuarioSistemaRepository::getPorCodigo(db_getsession('DB_id_usuario'));
$where = array();
$ordem = array();

if ($service->possuiConfiguracao($usuario, $instituicao)) {
    $dao = new cl_rubricasusuario();
    $where = array(
        "rh219_usuario = {$usuario->getCodigo()}",
        "rh219_instituicao = {$instituicao->getCodigo()}"
    );
}

$naoFiltraUsuario = filter_input(INPUT_GET, 'naoFiltraUsuario', FILTER_VALIDATE_BOOLEAN);

if ($naoFiltraUsuario !== null && $naoFiltraUsuario === true) {
    $dao = new cl_rhrubricas();
    $where = array();
}

$where[] = "rh27_instit = {$instituicao->getCodigo()}";

if (isset($opcao) && trim($opcao) != 'i') {
    $where[] = "rh27_ativo = '{$opcao}'";
}

if (!isset($pesquisa_chave)) {
    if (isset($campos) === false) {
        if (file_exists('funcoes/db_func_rhrubricas.php') == true) {
            require_once modification('funcoes/db_func_rhrubricas.php');
        } else {
            $campos = 'rhrubricas.*';
        }
    }

    if (isset($chave_rh27_rubric) && trim($chave_rh27_rubric) !== '') {
        $where[] = "rh27_rubric = '{$chave_rh27_rubric}'";
        $ordem[] = 'rh27_rubric';
    } else {
        if (isset($chave_rh27_descr) && trim($chave_rh27_descr) !== '') {
            $where[] = "rh27_descr ILIKE '%{$chave_rh27_descr}%'";
            $ordem[] = 'rh27_descr';
        }
    }

    $sql = $dao->sqlRubricas($campos, $where, $ordem); ?>

    <div class="container">
        <fieldset>
            <legend>Resultado da Pesquisa</legend>
            <?php db_lovrot($sql, 15, '()', '', $funcao_js); ?>
        </fieldset>
    </div>
<?php } else {
    if ($pesquisa_chave != null && $pesquisa_chave != "") {
        $campos = "*, CASE WHEN trim(rh27_form) = '' THEN 'f' ELSE 't' END AS formula";
        $where[] = "rh27_rubric = '{$pesquisa_chave}'";

        $sql = $dao->sqlRubricas($campos, $where, $ordem);
        $result = $dao->sql_record($sql);

        if (pg_num_rows($result) > 0) {
            db_fieldsmemory($result, 0);

            if (isset($ret)) {
                echo "<script>{$funcao_js}('{$rh27_descr}', '{$rh27_limdat}', '{$formula}', '{$rh27_obs}', '{$rh27_pd}', false);</script>";
            } else {
                echo "<script>{$funcao_js}('{$rh27_descr}', '{$rh27_limdat}', '{$formula}', '{$rh27_obs}', false);</script>";
            }
        } else {
            if (isset($ret)) {
                echo "<script>{$funcao_js}('Chave({$pesquisa_chave}) não encontrado(a)', true, true, true, true, true);</script>";
            } else {
                echo "<script>{$funcao_js}('Chave({$pesquisa_chave}) não encontrado(a)', true, true, true, true);</script>";
            }
        }
    } else {
        if (isset($ret)) {
            echo "<script>{$funcao_js}('', true, true, true, true, false);</script>";
        } else {
            echo "<script>{$funcao_js}('', true, true, true, false);</script>";
        }
    }
}
?>
</body>
</html>
