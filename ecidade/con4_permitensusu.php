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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_utils.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('dbforms/db_funcoes.php');
require_once modification('classes/db_db_permherda_classe.php');
require_once modification('classes/db_db_permissao_classe.php');

try {
    $parametros = JSON::requestParameters();
    $instituicaoSessao = InstituicaoRepository::getInstituicaoSessao();
    $codigoInstituicaoSessao = $instituicaoSessao->getCodigo();
    $codigoUsuarioSessao = UsuarioSistemaRepository::getUsuarioSessao()->getCodigo();
    $oDaoPermHerda = new cl_db_permherda();
    $oDaoPermissao = new cl_db_permissao();
    $anoSistema = db_getsession('DB_anousu');
    $rsPerfisInstituicaoSelecionados = '';

    $wherePerfisInstituicao = array(
        "usuext = 2",
        "u.id_usuario <> {$parametros->usuario}",
        "i.id_instit = {$codigoInstituicaoSessao}",
        "usuarioativo <> 0"
    );

    $wherePerfisInstituicao = implode(' AND ', $wherePerfisInstituicao);

    $sqlPerfisInstituicao = "
        SELECT u.id_usuario, u.id_usuario || ' - ' || nome AS nome
        FROM db_usuarios u
                 INNER JOIN db_userinst i ON i.id_usuario = u.id_usuario
        WHERE {$wherePerfisInstituicao}
        ORDER BY nome
    ";

    $rsPerfisInstituicao = db_query($sqlPerfisInstituicao);
    $perfisInstituicao = array();

    while ($rsPerfil = pg_fetch_object($rsPerfisInstituicao)) {
        $perfisInstituicao[] = $rsPerfil;
    }

    if (isset($parametros->salvar)) {
        db_inicio_transacao();

        $codigosPerfisInstituicao = implode(', ', array_map(function ($perfilInstituicao) {
            return $perfilInstituicao->id_usuario;
        }, $perfisInstituicao));

        $mensagem = 'Alteração realizada com sucesso!';
        $sCamposPermHerda = "db_permherda.id_usuario AS usuario, db_permherda.id_perfil AS perfil";

        $sWherePermHerda = array(
            "db_permherda.id_usuario = {$parametros->usuario}"
        );

        if ($codigosPerfisInstituicao) {
            $sWherePermHerda[] = "db_permherda.id_perfil IN ({$codigosPerfisInstituicao})";
        }

        $sSqlPermHerda = $oDaoPermHerda->sql_query_db_userinst(
            null,
            null,
            $sCamposPermHerda,
            null,
            implode(' AND ', $sWherePermHerda)
        );

        $rsPermHerda = db_query($sSqlPermHerda);

        if (!$rsPermHerda) {
            throw new Exception('Não foi possível buscar as permissões do usuário.');
        }

        if (pg_num_rows($rsPermHerda) > 0) {
            while ($oDadosPermHerda = pg_fetch_object($rsPermHerda)) {
                $sWherePermHerda = array(
                    "id_usuario = {$oDadosPermHerda->usuario}",
                    "id_perfil = {$oDadosPermHerda->perfil}"
                );

                $oDaoPermHerda->excluir(null, null, implode(' AND ', $sWherePermHerda));

                if ($oDaoPermHerda->erro_status === '0') {
                    throw new Exception($oDaoPermHerda->erro_msg);
                }
            }
        }

        if (isset($parametros->perfis)) {
            foreach ($parametros->perfis as $perfilSelecionado) {
                $oDaoPermHerda->id_usuario = $parametros->usuario;
                $oDaoPermHerda->id_perfil = $perfilSelecionado;
                $oDaoPermHerda->incluir($parametros->usuario, $perfilSelecionado);

                if ($oDaoPermHerda->erro_status === '0') {
                    throw new Exception($oDaoPermHerda->erro_msg);
                }
            }
        }

        db_fim_transacao();
        db_msgbox($mensagem);
        DBMenu::limpaCache($parametros->usuario);
    }

    if (count($perfisInstituicao) > 0) {
        $sqlPerfisInstituicaoSelecionados = "
            SELECT d.id_perfil
            FROM db_permherda d
            WHERE d.id_usuario = {$parametros->usuario}
        ";

        $rsPerfisInstituicaoSelecionados = db_query($sqlPerfisInstituicaoSelecionados);

        if (!$rsPerfisInstituicaoSelecionados || pg_num_rows($rsPerfisInstituicaoSelecionados) === 0) {
            $rsPerfisInstituicaoSelecionados = '';
        }
    }
} catch (Exception $exception) {
    db_msgbox($exception->getMessage());
    db_fim_transacao(true);
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DBSeller Informática Ltda - Página Inicial</title>
    <link href="estilos.css" rel="stylesheet">
    <style rel="stylesheet">
        #perfis {
            min-width: 300px;
        }

        .container {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }

        @media (min-width: 576px) {
            .container {
                max-width: 540px;
            }
        }

        @media (min-width: 768px) {
            .container {
                max-width: 720px;
            }
        }

        @media (min-width: 992px) {
            .container {
                max-width: 960px;
            }
        }

        @media (min-width: 1200px) {
            .container {
                max-width: 1140px;
            }
        }
    </style>
</head>
<body class="container">
<form method="post">
    <table style="margin: auto">
        <tbody>
        <?php if (count($perfisInstituicao) > 0) { ?>
            <tr>
                <td align="left">
                    <label>
                        <strong>Perfis:</strong>
                    </label>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    db_selectmultiple(
                        'perfis',
                        $rsPerfisInstituicao,
                        18,
                        2,
                        '',
                        '',
                        '',
                        $rsPerfisInstituicaoSelecionados
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td style="text-align: center">
                    <input name="salvar" type="submit" id="salvar" value="Salvar">
                </td>
            </tr>
        <?php } else { ?>
            <tr>
                <td>
                    <strong>Não há nenhum perfil cadastrado para a
                        instituição <?php echo $instituicaoSessao->getDescricao(); ?>.</strong>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</form>
<script src="scripts/scripts.js" rel="script"></script>
</body>
</html>
