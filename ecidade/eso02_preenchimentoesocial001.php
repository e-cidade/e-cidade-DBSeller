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

use ECidade\RecursosHumanos\ESocial\Model\Configuracao;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_utils.php");
require_once modification("dbforms/db_funcoes.php");

$parametros = JSON::requestParameters();

$configuracao = new Configuracao();
$tituloFormulario = "";

try {
    if (empty($parametros->formularioTipo)) {
        throw new Exception("Nenhum tipo de formulário selecionado para preenchimento.\nContate o suporte.");
    }

    $vinculado = null;
    switch ($parametros->formularioTipo) {

        case Tipo::ALTERACAO_CONTRATUAL:
            $vinculado = true;
            break;

        case Tipo::ALTERACAO_TRABALHADOR_SEM_VINCULO:
            $vinculado = false;
            break;
    }

    $tituloFormulario = Tipo::getTitulos($parametros->formularioTipo);
} catch (Exception $e) {
    throw new Exception( $e->getMessage());
}
?>

<html>
<head>
    <title>DBSeller Informática Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link rel="stylesheet" href="estilos.css">
    <script src="scripts/scripts.js"></script>
    <script src="scripts/widgets/DBLookUp.widget.js"></script>
</head>
<body>
<form class="container" style="width: 560px;" action="eso02_preenchimentoesocial002.php" method="POST">
    <fieldset>
        <input type="hidden" id="formularioTipo" name="formularioTipo" value="<?php echo $parametros->formularioTipo ?>"/>

        <legend>Preenchimento do formulário <?php echo $tituloFormulario ?></legend>

            <div id="questionario">
                <table class="form-container">
                    <tr>
                        <td nowrap title="Matrícula">
                            <a id="lbl_rh01_regist" for="matricula">Matrícula</a>
                        </td>
                        <td>
                            <?php db_input('rh01_regist', 10, "Matrícula", true, "text"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="Servidor">
                            <label id="lbl_z01_nome" for="z01_nome">Servidor:</label>
                        </td>
                        <td><?php db_input('z01_nome', 50, "Servidor", true, "text"); ?></td>
                    </tr>
                </table>
            </div>

            <script>

                var servidorVinculado = '<?php echo $vinculado; ?>';

                const labelMatricula = document.querySelector('#lbl_rh01_regist');
                const inputMatricula = document.querySelector('input[name=rh01_regist]');
                const inputNome = document.querySelector('input[name=z01_nome]');

                const lookup = new DBLookUp(labelMatricula, inputMatricula, inputNome, {
                    'sArquivo': 'func_rhpessoal.php',
                    'oObjetoLookUp': 'func_nome',
                    'aParametrosAdicionais': [
                        'vinculados='+servidorVinculado,
                        'sAtivos=1'
                    ]
                });

                lookup.abrirJanela(true);

            </script>

    </fieldset>
    <input type="submit" value="Pesquisar">
</form>
<?php db_menu(); ?>
</body>
