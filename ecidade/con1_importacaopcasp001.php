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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");

?>
<!DOCTYPE html>

<html>
<head>
    <title>DBSeller Informática Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>

    <style>
        table.table-exemplo {
            border-collapse: collapse;
            background-color: #FFF;
            color: #0a0a0a;
        }

        table.table-exemplo td {
            border: 1px solid black;
            padding: 0 2px;
        }

        .P {
            color: #115e2f;
            background-color: #BBE9AD;
        }

        .D {
            color: #0C3366;
            background-color: #AAE6E0;
        }
    </style>
</head>
<body class="body-default">
<div class="alert alert-primary text-left" role="alert">
    Importe uma planilha do Leiaute do PCASP no formato CSV.<br>
    Padrões da planilha:<br>
    <ul style="margin: 0;">
        <li><strong>Codificação: </strong>Europa Ocidental (ISO-8859-1) ou Latin1</li>
        <li><strong>Delimitador de campo:</strong> ; (ponto e vírgula)</li>
        <li><strong>Delimitador de texto:</strong> " (aspas duplas)</li>
    </ul>
    <div>
        <table class="table-exemplo">
            <tr>
                <td class="P">Classe</td>
                <td class="P">Grupo</td>
                <td class="P">Subgrupo</td>
                <td class="P">Título</td>
                <td class="P">Subtítulo</td>
                <td class="P">Item</td>
                <td class="P">Subitem</td>
                <td class="D">Desdobramento 1</td>
                <td class="D">Desdobramento 2</td>
                <td class="D">Desdobramento 3</td>
                <td>Conta</td>
                <td>Nome</td>
                <td>Função</td>
                <td>Natureza do saldo</td>
                <td>Nível Detalhado</td>
                <td>Indicador do superávit financeiro</td>
                <td>Informação complementar</td>
                <td>Status</td>
            </tr>
            <tr>
                <td class="P">3</td>
                <td class="P">1</td>
                <td class="P">1</td>
                <td class="P">1</td>
                <td class="P">1</td>
                <td class="P">01</td>
                <td class="P">22</td>
                <td class="D">00</td>
                <td class="D">00</td>
                <td class="D">00</td>
                <td>3.1.1.1.1.01.22</td>
                <td>Nome da Conta</td>
                <td>Descrição completa da conta</td>
                <td>C<br>D<br>C/D</td>
                <td>Sintético<br>Analítico</td>
                <td>-<br>F<br>P<br>F/P</td>
                <td>
                    PO <br>
                    PO - FP <br>
                    PO - FP - DC <br>
                    PO - FP - FR - CO <br>
                    PO - FR - CO<br>
                    PO - FR - CO - NR<br>
                    PO - FS - FR - CO - ND <br>
                    PO - FP - DC - FR <br>
                    PO - FS - FR - CO - ND - AI
                </td>
                <td>Ativa<br>Inativa</td>
            </tr>
        </table>
    </div>
</div>

<div class="container">
    <form id="formulario">
        <fieldset>
            <legend>Atualização do PCASP</legend>
            <table class="form-container">
                <tr>
                    <td><label for="plano">Plano de Contas:</label></td>
                    <td>
                        <select id="plano" name="plano">
                            <option value="uniao">União / Federação</option>
                            <option value="UF">Estadual / Regional</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label class="bold" for="exercicio">Exercício:</label></td>
                    <td>
                        <select id="exercicio" name="exercicio" style="width:100px;">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                </tr>
            </table>
            <div id="ctnImportacao"></div>
        </fieldset>
        <button type="button" id="btnImportar" class="btn btn-light" disabled>
            <i class="fas fa-save"></i>
            Importar
        </button>

        <button type="button" id="btnImprimir" class="btn btn-light">
            <i class="fas fa-print"></i>
            Imprimir
        </button>

        <button type="button" id="btnAtualiza" class="btn btn-light" disabled>
            <i class="fas fa-save"></i>
            Atualizar Nomes
        </button>
    </form>
</div>
<script type="text/javascript" src="scripts/widgets/DBFileUpload.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">

    const formulario = document.getElementById('formulario');
    const cboPlano = document.getElementById('plano');
    const cboExercicio = document.getElementById('exercicio');
    const linhaUF = document.getElementById('linhaUF');
    const btnImportar = document.getElementById('btnImportar');
    const btnAtualiza = document.getElementById('btnAtualiza');

    const routs = {
        pcasp: 'financeiro/contabilidade/plano-contas/importar/pcasp',
        emitir: 'financeiro/contabilidade/plano-contas/emitir/pcasp',
        atualiza: 'financeiro/contabilidade/plano-contas/importar/atualizar-pcasp',
    }

    cboPlano.addEventListener('change', (e) => {
        linhaUF.style.display = 'none';
    });

    const erroRetornoArquivo = mensagem => {
        alert(mensagem);
        btnImportar.disabled = true;
        btnAtualiza.disabled = true;
        fileUpload.clear();
    }

    function retornoEnvioArquivo(retorno) {
        if (retorno.error) {
            erroRetornoArquivo(retorno.error);
            return false;
        }

        if (retorno.extension.toLowerCase() != 'csv') {
            erroRetornoArquivo('Arquivo inválido! O arquivo deve ser uma planilha em "csv".');
            return false;
        }

        btnImportar.disabled = false;
        btnAtualiza.disabled = false;
    }

    const fileUpload = new DBFileUpload({callBack: retornoEnvioArquivo, labelButton: 'Arquivo'});
    fileUpload.show(document.getElementById('ctnImportacao'));

    const validar = () => {
        try {
            if (cboExercicio.value === '') {
                throw 'Você deve selecionar o exercício.'
            }
        } catch (e) {
            alert(e);
            return false;
        }
        return true;
    };

    PHPSession.loadData().then(() => {
        let exercicio = Number(PHPSession.getValueSession('DB_anousu'))
        cboExercicio.add(new Option(exercicio, exercicio));
        cboExercicio.add(new Option(exercicio + 1, exercicio + 1));

        document.getElementById('btnImportar').addEventListener('click', () => {

            if (!validar()) {
                return false;
            }

            const formData = new FormData(formulario);
            formData.append('file', JSON.stringify({
                "extension": fileUpload.extension,
                "name": fileUpload.file,
                "path": fileUpload.filePath
            }));
            PHPSession.appendFormData(formData);

            HttpClient.post(`${PHPSession.requestApi}/${routs.pcasp}`, {body: formData}).then(response => {
                alert(response.message);
                if (response.error) {
                    return;
                }
            });
        });

        btnAtualiza.addEventListener('click', () => {
            enviaPlanilha(routs.atualiza);
        })
    });

    const enviaPlanilha = rota => {
        if (!validar()) {
            return false;
        }

        const formData = new FormData(formulario);
        formData.append('file', JSON.stringify({
            "extension": fileUpload.extension,
            "name": fileUpload.file,
            "path": fileUpload.filePath
        }));
        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${rota}`, {body: formData}).then(response => {
            alert(response.message);
            if (response.error) {
                return;
            }
        });
    }

    document.getElementById('btnImprimir').addEventListener('click', () => {
        if (cboExercicio.value === '') {
            alert('Você deve selecionar o exercício.');
            return;
        }

        HttpClient.get(`${PHPSession.requestApi}/${routs.emitir}/${cboPlano.value}/${cboExercicio.value}`)
            .then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                const download = new DBDownload();
                download.addFile(response.data.csv, "Plano de contas - CSV");
                download.show();
            });
    });
</script>
<?php db_menu(); ?>
</body>
</html>
