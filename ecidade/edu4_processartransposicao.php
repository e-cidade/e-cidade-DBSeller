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

use App\Domain\Educacao\Escola\Models\Transposicao;

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="ISO-8859-1">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
          name="viewport">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="./extension/package/Desktop/assets/vendors/select2/css/select2.min.css" />
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
</head>
<body class="body-default">
<div class="container">
    <fieldset>
        <legend>Processamento de Transposição</legend>
        <?php
        $transposicao = Transposicao::getTransposicaoAtual();
        $processada = $transposicao->ed353_processada ? 'Sim' : 'Não';
        ?>
        <input type="hidden" id="codigo" value="<?php echo $transposicao->ed353_codigo?>">
        <table class="form-container" style="border-collapse: separate;">
            <tr>
                <td><label for="ano">Ano: </label></td>
                <td>
                    <input type="text" id="ano" class="field-size2" value="<?php echo $transposicao->ed353_ano?>" disabled>
                </td>
            </tr>
            <tr>
                <td>Inscrições: </td>
                <td>
                    <span class="<?php echo $transposicao->color?>" style="padding: 2px 4px" id="status">
                        <?php echo $transposicao->status?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>Processada: </td>
                <td><?php echo $processada?></td>
            </tr>
        </table>
    </fieldset>
</div>
<div class="container">
    <button type="button" class="btn btn-light" id="btnEncerramento">
        <i class="far fa-clock"></i>
        <?php echo $transposicao->labelBotao?>
    </button>
    <button type="button" class="btn btn-light" id="btnRelatorioInscritos">
        <i class="far fa-file-alt"></i>
        Inscritos CSV
    </button>
    <br><br>
    <button type="button" class="btn btn-light"  id="btnProcessarDesignacao">
        <?php echo $transposicao->ed353_processada ? '<i class="fas fa-redo"></i> Reprocessar' : '<i class="fa fa-cogs"></i> Processar designação'?>
    </button>
</div>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    var urlApi;
    window.addEventListener('load', () => {
        PHPSession.loadData().then(() => {
            urlApi = PHPSession.requestApi;
        });
    });

    const inputCodigo = document.getElementById('codigo');
    const inputAno = document.getElementById('ano');
    const ctnDownload = document.getElementById('ctnDownload');
    const spanStatus = document.getElementById('status');
    const btnEncerramento = document.getElementById('btnEncerramento');
    const btnRelatorioInscritos = document.getElementById('btnRelatorioInscritos');
    const btnProcessarDesignacao = document.getElementById('btnProcessarDesignacao');

    const transposicaoProcessada = '<?php echo $transposicao->ed353_processada?>';

    btnEncerramento.addEventListener('click', () => {
        const formData = new FormData();
        formData.append('codigo', inputCodigo.value);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/educacao/escola/transposicao/toggle`, { body: formData })
            .then((response) => {
                alert(response.message);
                if (response.error) {
                    return;
                }

                let transposicao = response.data;
                spanStatus.innerText = transposicao.status;
                spanStatus.setAttribute('class', transposicao.color);

                btnEncerramento.innerHTML = `<i class="far fa-clock"></i> ${transposicao.labelBotao}`;
            });
    });

    btnRelatorioInscritos.addEventListener('click', () => {
        const formData = new FormData();
        formData.append('anoDestino', inputAno.value);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/educacao/escola/transposicao/relatorio-inscritos`, { body: formData })
            .then((response) => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                window.open(response.data.url);
            });
    });

    btnProcessarDesignacao.addEventListener('click', () => {
        if (transposicaoProcessada && !confirm("Antes de reprocessar, atualize o cadastro de vagas e as opções dos candidatos não designados")) {
            return;
        }

        const formData = new FormData();
        formData.append('anoTransposicao', inputAno.value);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/educacao/escola/transposicao/processar`, { body: formData })
            .then((response) => {
                alert(response.message);
            });
    });
</script>
</body>
</html>
