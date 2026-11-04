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
$oGet            = db_utils::postmemory($_GET);
?>

<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="javascript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
  <script type="text/javascript" src="scripts/classes/http/http.js"></script>
  <style type="text/css">
    .texto {
      background-color: white
    }
  </style>
</head>

<body class="body-default">
  <center>
    <fieldset style="margin-top:25px; max-width: 1000px">
      <legend>Anexos</legend>
      <div id="painelarquivos">

      </div>
    </fieldset>
  </center>
</body>

</html>
<script>
  function getArquivosItbi(itbi) {
    if (itbi) {
      const sApiUrl = "<?= ECIDADE_REQUEST_PATH ?>v4/api/";
      const data = new FormData();
      data.append('itbi', itbi);

      HttpClient.post(`${sApiUrl}tributario/itbi/arquivoitbi/get-arquivos`, {
        body: data,
        reportProgress: false
      }).then(response => {
        if (response.data.length > 0) {
          const colunas = [{
              key: 'it39_sequencial',
              label: 'Cód.'
            },
            {
              key: 'nome',
              label: 'Usuário'
            },
            {
              key: 'it39_arquivo',
              label: 'Arquivo'
            },
            {
              key: 'it39_descricao',
              label: 'Descrição'
            },
            {
              key: 'it39_observacao',
              label: 'Observação'
            },
            {
              key: 'it39_data',
              label: 'Data'
            },
            {
              label: 'Baixar'
            }
          ];

          let tableHtml = '<table border="1" cellpadding="5" cellspacing="0">';

          tableHtml += '<thead><tr>';
          colunas.forEach(coluna => {
            tableHtml += `<th>${coluna.label}</th>`;
          });
          tableHtml += '</tr></thead>';

          tableHtml += '<tbody>';
          response.data.forEach(item => {
            tableHtml += '<tr>';

            colunas.slice(0, 6).forEach(coluna => {
              tableHtml += `<td>${item[coluna.key] || ''}</td>`;
            });

            tableHtml += `<td><button class="btn-baixar" onclick="baixarArquivo(${item.it39_sequencial} + '|' + '${item.it39_idstorage}', ${itbi})">Baixar</button></td>`;

            tableHtml += '</tr>';
          });
          tableHtml += '</tbody>';

          tableHtml += '</table>';

          document.getElementById('painelarquivos').innerHTML = tableHtml;
        }else{
          document.getElementById('painelarquivos').innerHTML = 'Nenhum anexo encontrado.';
        }
      }).catch(error => {
        console.error('Erro ao buscar os arquivos:', error);
        document.getElementById('painelarquivos').innerHTML = 'Erro ao carregar arquivos.';
      });
    }
  }

  function baixarArquivo(dados) {
    if (dados) {
      js_divCarregando('Aguarde!', 'msgbox');
      let idArq = dados.split('|')[1];
      const sApiUrl = "<?= ECIDADE_REQUEST_PATH ?>v4/api/";
      const data = new FormData();
      data.append('idArq', idArq);
      HttpClient.post(`${sApiUrl}tributario/itbi/arquivoitbi/download-arquivo`, {
        body: data,
        reportProgress: false
      }).then(response => {
        js_removeObj('msgbox');
        if (response.data.path !== null) {
          var oDownload = new DBDownload();
          oDownload.addFile(response.data.path, response.data.nomeArquivo);
          oDownload.show();
        }else{
          alert('Erro ao baixar o arquivo.');
        }
      });
    }
  }

  getArquivosItbi(<?= $oGet->guia ?>);
</script>