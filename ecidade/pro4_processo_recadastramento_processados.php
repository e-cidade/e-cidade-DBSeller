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
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

$matricula = filter_input(INPUT_GET, "matricula", FILTER_VALIDATE_INT);
if (!$matricula) {
    db_redireciona('db_erros.php?fechar=false&db_erro=Matrícula inválida');
}

$rs = db_query("
    select
        ouvidoria.processoouvidoria.ov09_protprocesso,
        ouvidoria.ouvidoriaatendimento.ov01_sequencial,
        protocolo.protprocesso.p58_dtproc,
        ouvidoria.ouvidoriaatendimento.ov01_numero,
        ouvidoria.ouvidoriaatendimento.ov01_anousu,
        ouvidoria.ouvidoriaatendimento.ov01_dataatend,
        recursoshumanos.processamentorecadastramento.h260_data,
        processamentorecadastramento.h260_regist
    from
        recursoshumanos.processamentorecadastramento
        inner join ouvidoria.processoouvidoria
        on ouvidoria.processoouvidoria.ov09_protprocesso  = recursoshumanos.processamentorecadastramento.h260_codproc
        inner join ouvidoria.ouvidoriaatendimento
            on ouvidoria.ouvidoriaatendimento.ov01_sequencial  = ouvidoria.processoouvidoria.ov09_ouvidoriaatendimento
        inner join protocolo.protprocesso on protocolo.protprocesso.p58_codproc  = ouvidoria.processoouvidoria.ov09_protprocesso
    where
       recursoshumanos.processamentorecadastramento.h260_status = true
     and recursoshumanos.processamentorecadastramento.h260_regist = {$matricula}
    order by ouvidoria.ouvidoriaatendimento.ov01_numero desc ,ouvidoria.ouvidoriaatendimento.ov01_anousu desc;
");

$processamentos = pg_fetch_all($rs);
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link href="skins/default/estilos/widgets/DBModal.css" rel="stylesheet" type="text/css">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <style>
        body {
            background: #CCCCCC;
        }

        table, th, td {
            border: 1px solid #ccc;
            border-spacing: 0;
        }

        th {
            color: white;
            background: #4a789b;
        }

        th, td {
            padding: 10px
        }

        .container-table {
            width: 80%;
            justify-content: center;
            display: flex;
            flex-direction: column;
            margin: auto;
        }

        .btn-ver-formulario {
            background: #4a789c;
            padding: 5px;
            border-radius: 5px;
            color: white;
        }
    </style>
</head>
<body>
<div class="container-table">
    <h1>Recadastramento</h1>
    <table>
        <thead>
        <tr>
            <th>Atendimento</th>
            <th>Data Atendimento</th>
            <th>Data Aprovação</th>
            <th>Data Processamento</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (empty($processamentos)) {
            echo "<tr>
         <td colspan='5'>
         Não possui recadastramento processado
       </td>
</tr>";
        } else {
            foreach ($processamentos as $processamento) {
                $atendimento = $processamento["ov01_numero"] . "/" . $processamento["ov01_anousu"];
                ?>
                <tr>
                    <td><?php echo $atendimento; ?></td>
                    <td>
                        <?php echo empty($processamento["ov01_dataatend"])
                            ? "-"
                            : (new DateTime($processamento["ov01_dataatend"]))->format("d/m/Y")
                        ?>
                    </td>
                    <td>
                        <?php echo empty($processamento["p58_dtproc"])
                            ? "-"
                            : (new DateTime($processamento["p58_dtproc"]))->format("d/m/Y")
                        ?>
                    </td>
                    <td>
                        <?php
                        echo empty($processamento["h260_data"])
                            ? "-"
                            : (new DateTime($processamento["h260_data"]))->format("d/m/Y H:i:s")
                        ?>
                    </td>
                    <td>
                        <a
                            class="btn-ver-formulario"
                            data-atendimento="<?php echo $atendimento ?>"
                            data-processo="<?php echo $processamento["ov09_protprocesso"] ?>"
                        >Ver Formulário</a>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
</div>

<script>

    const btnVerFormualrio = document.querySelectorAll(".btn-ver-formulario");
    if (btnVerFormualrio.length) {
        btnVerFormualrio.forEach(el => {
            el.addEventListener("click", openFormulario)
        })
    }

    function openFormulario(e) {
        const {atendimento, processo} = e.target.dataset;
        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            "processo_recadastramento_externo",
            `pro4_processo_recadastramento_externo.php?atendimento=${atendimento}&p58_codproc=${processo}`,
            "formulário recadastramento"
        )

    }
</script>
</body>
</html>
