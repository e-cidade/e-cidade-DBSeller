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

//MODULO: itbI
?>
<script type="text/javascript">
  function verificaCampos() {
    let btnProcessar = document.getElementById('processar');
    let arquivo = document.getElementById('fileImportItbi').value;
    let descricao = document.getElementById('descrArquivoImport').value;
    let processarButton = document.getElementById('processar');
    if (btnProcessar.value == 'Processar') {
      if (arquivo.length > 0 && descricao.length > 0) {
        processarButton.disabled = false;
      } else {
        processarButton.disabled = true;
      }

    } else if (btnProcessar.value == 'Alterar') {
      if (descricao.length > 0) {
        processarButton.disabled = false;
      } else {
        processarButton.disabled = true;
      }
    }

  }

 async function processarDados() {
    
    let btnProcessar = document.getElementById('processar');
    const sApiUrl = "<?= ECIDADE_REQUEST_PATH ?>v4/api/";

    if (btnProcessar.value == 'Processar') {
      js_divCarregando('Aguarde!', 'msgbox');
      let verificaArquivo = document.getElementById('fileImportItbi').value;
      let descricao = document.getElementById('descrArquivoImport').value;
      let obs = document.getElementById('obsArquivoImport').value;
      let user = <?= db_getsession("DB_id_usuario") ?>;
      let itbi = <?= $itbi ?>;
      if (verificaArquivo.length > 0 && descricao.length > 0) {
        let file = document.getElementById('fileImportItbi').files[0];
        const data = new FormData();
        data.append('itbi', itbi);
        data.append('file', file);
        data.append('desc', descricao);
        data.append('obs', obs);
        data.append('user', user);

       await HttpClient.post(`${sApiUrl}tributario/itbi/arquivoitbi/processar-arquivo`, {
          body: data,
          reportProgress: false
        }).then(response => {
          if (response.file && response.file[0] == 'arqInvalido') {
            alert('Erro ao processar o arquivo. \n\n Erro: Arquivo Invalido.');
            js_removeObj('msgbox');
          }

          if (response.data) {
            if(response.data == 'semPerm'){
              js_removeObj('msgbox');
              alert('Erro ao processar o arquivo. \n\n Erro: Guia já está fechada.');
              return false;
            }

            if (response.data.saveInfo.sucesso && response.data.upload.sucesso) {
              js_removeObj('msgbox');
              alert('Arquivo processado com sucesso!');
              getArquivosItbi(itbi);
              formImportacaoItbi.reset();
              verificaCampos();

            }else if(reponse.data == 'semPerm'){
              js_removeObj('msgbox');
              alert('Erro ao processar o arquivo. \n\n Erro: Guia já está fechada.');
            }else{
              js_removeObj('msgbox');
              alert('Erro ao processar o arquivo. \n\n Erro: ' + response.data.error);
            }
          }
          js_removeObj('msgbox');
          
        }).catch(error => {
          js_removeObj('msgbox');
          alert('Erro ao processar o arquivo. \n\n Erro: ' + error);
        });
        js_removeObj('msgbox');
        return false;
      }

    } else if (btnProcessar.value == 'Alterar') {
      js_divCarregando('Aguarde!', 'msgbox');
      let descricao = document.getElementById('descrArquivoImport').value;
      let obs = document.getElementById('obsArquivoImport').value;
      let dadosAlteracao = document.getElementById('dadosAlteracao').value;
      let sequencial = dadosAlteracao.split('|')[0];
      if (descricao.length > 0) {
        let itbi = <?= $itbi ?>;
        const data = new FormData();
        data.append('sequencial', sequencial);
        data.append('desc', descricao);
        data.append('obs', obs);
        data.append('itbi', itbi);
        HttpClient.post(`${sApiUrl}tributario/itbi/arquivoitbi/update-dados-arquivo`, {
          body: data,
          reportProgress: false
        }).then(response => {
          if (response.data.sucesso == true) {
            js_removeObj('msgbox');
            alert('Dados alterados com sucesso!');
            getArquivosItbi(itbi);
            formImportacaoItbi.reset();
            verificaCampos();
          } else if (response.data == 'semPerm') {
            js_removeObj('msgbox');
            alert('Erro ao alterar o arquivo. \n\n Erro: Guia já está fechada.');
          }else {
            js_removeObj('msgbox');
            alert('Erro ao alterar o arquivo. \n\n Erro: ' + response.data.erro);
          }
        });
        js_removeObj('msgbox');
        return false;
      }
    }

  }


  function cancelarAlteracao() {
    let btnProcessar = document.getElementById('processar');
    btnProcessar.value = 'Processar';
    let inptArquivo = document.getElementById('fileImportItbi');
    inptArquivo.disabled = false;
    let btnCancelar = document.getElementById('cancelarAlteracao');
    formImportacaoItbi.reset();
    if (btnCancelar) {
      btnCancelar.remove();
    }
  }

  function getArquivosItbi(itbi) {
    if (itbi) {
      const sApiUrl = "<?= ECIDADE_REQUEST_PATH ?>v4/api/";
      const data = new FormData();
      data.append('itbi', itbi);

      HttpClient.post(`${sApiUrl}tributario/itbi/arquivoitbi/get-arquivos`, {
        body: data,
        reportProgress: false
      }).then(response => {
        limpaTable();
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
            },
            {
              label: 'Alterar'
            },
            {
              label: 'Excluir'
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

            if (item.permite_excluir) {
              tableHtml += `<td><button class="btn-alterar" data-item='${JSON.stringify(item)}' data-itbi='${itbi}' onclick="alterarArquivo(this)">Alterar</button></td>`;
              tableHtml += `<td><button class="btn-excluir" onclick="excluirArquivo(${item.it39_sequencial} + '|' + '${item.it39_idstorage}', ${itbi})">Excluir</button></td>`;
            } else {
              tableHtml += `<td><button class="btn-alterar" disabled>Alterar</button></td>`;
              tableHtml += `<td><button class="btn-excluir" disabled>Excluir</button></td>`;
            }

            tableHtml += '</tr>';
          });
          tableHtml += '</tbody>';

          tableHtml += '</table>';

          document.getElementById('painelarquivos').innerHTML = tableHtml;
        }
      }).catch(error => {
        console.error('Erro ao buscar os arquivos:', error);
        document.getElementById('painelarquivos').innerHTML = 'Erro ao carregar arquivos.';
      });
    }
  }

  function alterarArquivo(button) {
    const item = JSON.parse(button.getAttribute('data-item'));
    const itbi = button.getAttribute('data-itbi');
    let inptArquivo = document.getElementById('fileImportItbi');
    let inptDescricao = document.getElementById('descrArquivoImport');
    let inptObservacao = document.getElementById('obsArquivoImport');
    let btnProcessar = document.getElementById('processar');
    let dadosAlteracao = document.getElementById('dadosAlteracao');

    if (item && itbi) {
      inptArquivo.disabled = true;
      inptDescricao.value = item.it39_descricao;
      inptObservacao.value = item.it39_observacao;
      dadosAlteracao.value = item.it39_sequencial + '|' + item.it39_idstorage;
      btnProcessar.value = 'Alterar';
      let btnCancelar = document.getElementById('cancelarAlteracao');
      if (!btnCancelar) {
        let divButtons = document.getElementById('buttons');
        btnCancelar = document.createElement('input');
        btnCancelar.type = 'button';
        btnCancelar.className = 'btn-cancelar';
        btnCancelar.id = 'cancelarAlteracao';
        btnCancelar.onclick = cancelarAlteracao;
        btnCancelar.value = 'Cancelar';
        divButtons.appendChild(btnCancelar);
      }
      verificaCampos();
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

  function excluirArquivo(dados, itbi) {
    let msg = 'Deseja realmente excluir o arquivo?';
    if (confirm(msg) !== true) {
      return;
    }
    if (dados) {
      const sApiUrl = "<?= ECIDADE_REQUEST_PATH ?>v4/api/";
      const data = new FormData();
      let idUser = <?= db_getsession("DB_id_usuario") ?>;
      let arquivo = dados.split('|');
      let sequencial = arquivo[0];
      let idArq = arquivo[1];
      data.append('sequencial', sequencial);
      data.append('idUser', idUser);
      data.append('idArq', idArq);
      data.append('itbi', itbi);
      HttpClient.post(`${sApiUrl}tributario/itbi/arquivoitbi/delete-arquivo`, {
        body: data,
        reportProgress: false
      }).then(response => {
        if (response.data.status == true) {
          getArquivosItbi(itbi);
          alert('Arquivo excluido com sucesso!');
        } else if(response.data == 'semPerm'){
          alert('Erro ao excluir o arquivo. \n\n Erro: Guia já está fechada.');
        }else {
          alert('Erro ao excluir o arquivo.');
          getArquivosItbi(itbi);
        }
      });
    }
  }

  function limpaTable() {
    document.getElementById('painelarquivos').innerHTML = '';
  }

  <?php
  if ($itbi !== 0) {
    echo "getArquivosItbi($itbi);";
  }
  ?>
</script>

<center>
  <fieldset>
    <legend>Anexos</legend>
    <form action="" method="post" name="formItbi" id="formImportacaoItbi" enctype="multipart/form-data">
      <fieldset style="width: 500px">
        <legend>Upload</legend>
        <table class="form-container">
          <tr>
            <td><label for="guiaItbi">Guia ITBI:</label></td>
            <td><input style="width: 100px;" type="text" name="ItbiGuia" id="guiaItbi" disabled value="<?= $itbi ?>" /></td>
          </tr>
          <tr>
            <td><label for="fileImportItbi">Arquivo:</label></td>
            <td><input style="height: 25px; width: 422px;" type="file" name="fileItbi" id="fileImportItbi" onchange="verificaCampos();" /></td>
          </tr>
          <tr>
            <td><label for="descrArquivoImport">Descrição*:</label></td>
            <td><input style="width: 100%;" type="text" name="descrArquivo" id="descrArquivoImport" onchange="verificaCampos();" /></td>
          </tr>
          <tr>
            <td><label for="obsArquivoImport">Observação:</label></td>
            <td><textarea name="obsArquivo" id="obsArquivoImport"></textarea></td>
          </tr>
          <input type="text" name="dadosalteracao" id="dadosAlteracao" value="" hidden>
        </table>
      </fieldset>
      <div id="buttons">
        <input type="button" id="processar" value="Processar" disabled onclick="processarDados();" />
        <input type="button" id="limpar" value="Limpar" onclick="formImportacaoItbi.reset(); verificaCampos();" />
      </div>
    </form>
    <fieldset style="margin-top:25px; max-width: 1000px">
      <legend>Gerenciar Uploads</legend>
      <div id="painelarquivos">

      </div>
    </fieldset>
  </fieldset>
</center>