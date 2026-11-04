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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_app.utils.php"));


$clrotulo = new rotulocampo();
$clrotulo->label('v13_certid');
$clrotulo->label('v15_observacao');
$clrotulo->label('k60_codigo');
$clrotulo->label('k60_descr');

?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/bootstrap-table@1.20.2/dist/bootstrap-table.min.css" rel="stylesheet">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
  <script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">

</head>

<body bgcolor=#CCCCCC>
<form class="container" name="form1" id="form1">
  <fieldset>
    <legend>Cancela CDA Geral</legend>    
    <table class="form-container">
      <tr>
        <td title="<?=$Tv13_certid?>">        
          <?
            db_ancora("Certidão:","js_pesquisa_certid_ini(true);",1); 
          ?>        
        </td>
        <td>
          <strong>
          <?
            db_input("v13_certidini",6,$Iv13_certid,true,"text",4,"onchange='js_pesquisa_certid_ini(false);'");

              db_ancora("até", "js_pesquisa_certid_fim(true);", 1);

              db_input("v13_certidfim", 6, $Iv13_certid, true, "text", 4, "onchange='js_pesquisa_certid_fim(false);'");
              ?>
            </strong>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?php echo $Tk60_codigo ?>">
            <?php
            db_ancora($Lk60_codigo, "js_pesquisalista(true);", 4);
            ?>
          </td>

          <td>
            <div>
              <?php
              db_input("k60_codigo", 4, $Ik60_codigo, true, "text", 4, "onchange='js_pesquisalista(false);'");
              db_input("k60_descr", 40, $Ik60_descr, true, "text", 3, "");
              ?>
              <button id="btpesquisar" type="button" onClick="js_pesquisa()" disabled="disabled">Pesquisar</button>
            </div>
          </td>
        </tr>
        <tr>
          <div id="divtable">
            <table id="table"></table>
          </div>
        </tr>
        <tr>
          <td colspan="2">
            <fieldset class="separator">
              <legend>Observação</legend>
              <?php
              db_textarea('v15_observacao', 10, 100, $Iv15_observacao, true, 'text', 1, '', '', '', 500);
              ?>
            </fieldset>
          </td>
        </tr>
      </table>
    </fieldset>
    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12" align="center">
      <input type="button" id="btprocessar" value="Processar" onclick="js_processar()">
      <input type="button" id="btprocessaranulacao" value="Processar Anulação por Lista" onclick="js_processaranulacao()" style="display: none;">
    </div>
    <div id="progress" style="display: none; justify-content: center;">
      <?php db_criatermometro('termometro', 'Concluido...', 'blue', 1); ?>
    </div>

    <?php
    db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
    ?>
  </form>
  <script>
    var processando = true;
    const url = '<?= ECIDADE_REQUEST_PATH ?>';
    const routers = {
      'pesquisar': url + '/v4/api/tributario/juridico/anulacaocda/pesquisar',
      'processar': url + 'v4/api/tributario/juridico/anulacaocda/processar',
      'getprocessamento': url + 'v4/api/tributario/juridico/anulacaocda/getprocessamento'
    };

    async function js_pesquisa() {

      k60_codigo = jQuery('#k60_codigo').val();
      if (k60_codigo == '') {
        alert('Informe a Lista');
        return
      }
      $table.bootstrapTable('destroy');
      const data = {
        k60_codigo: k60_codigo
      };
      const dado = new FormData;
      for (index in data) {
        dado.append(index, data[index]);
      }

      HttpClient.post(routers.pesquisar, {
          body: dado,
          reportProgress: true
        })
        .then((res) => {
          if (res.hasOwnProperty('data')) {
            if (res.error) {
              alert(res.message);
              return
            }
            jQuery('#btprocessar').hide();
            jQuery('#btprocessaranulacao').show();
            $table.bootstrapTable({
              cache: false,
              columns,
              striped: true,
              pagination: true,
              pageSize: 10,
              pageList: [10, 25, 50, 100],
              data: res.data
            });
          }
        }).catch((err) => {
          console.error(err.response.data);
        })
    }

    var $table = jQuery('#table')
    const columns = [{
        align: 'center',
        title: 'Inicial',
        field: 'v51_inicial',
        sortable: true
      },
      {
        field: 'v13_certid',
        title: 'Certidão',
        sortable: true,
        align: 'center',
      },
      {
        field: 'v13_dtemis',
        title: 'Data Emissão',
        sortable: true,
        align: 'center',
        formatter: FormatterData
      },
      {
        field: 'k00_descr',
        align: 'center',
        title: 'Descrição',
        sortable: true
      }
    ];

    function FormatterData(value) {
      resultado = value.split("-");
      dataoriginal = resultado[2] + '/' + resultado[1] + '/' + resultado[0]
      return [dataoriginal].join('')
    }

    function js_processaranulacao() {
      k60_codigo = jQuery('#k60_codigo').val();
      if (k60_codigo == '') {
        alert('Informe a Lista');
        return
      }
      if (!confirm("Deseja  realemente anular as CDA pela lista?")) {
        return false;
      }
      const data = {
        k60_codigo: k60_codigo,
        v15_observacao: jQuery('#v15_observacao').val()
      };
      const dado = new FormData;
      for (index in data) {
        dado.append(index, data[index]);
      }

      HttpClient.post(routers.processar, {
          body: dado,
          reportProgress: true
        })
        .then((res) => {
          if (res.hasOwnProperty('data')) {
            if (res.error) {
              alert(res.message);
              return
            }
            alert('Iniciando Processamento de Anulação!');
            $table.bootstrapTable('destroy');
            jQuery('#k60_codigo').val('');
            jQuery('#btprocessar').show();
            jQuery('#btprocessaranulacao').hide();

            setTimeout(function() {
              processando = true;
              intervalo();
            }, 5000);

          }
        }).catch((err) => {
          console.error(err.response.data);
        })
    }

    function js_processar() {

      iCertidaoInicial = document.form1.v13_certidini.value;

      iCertidaoFinal = document.form1.v13_certidfim.value;

      sObservacao = document.form1.v15_observacao.value;

      if (iCertidaoInicial == '') {
        alert('Certidão de inicio não informada.');
        return false;
      }

      if (iCertidaoFinal == '') {
        alert('Certidão de inicio não informada.');
        return false;
      }

      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_anulacda', 'div4_cancelcdageral002.php?certidaoinicial=' + iCertidaoInicial + '&certidaofinal=' + iCertidaoFinal + '&observacao=' + sObservacao, 'Processando Anulações', true);
    }

    function js_fecharJanela() {

      db_iframe_anulacda.hide();

      window.location = 'div4_cancelcdageral001.php';

    }

    function js_pesquisa_certid_ini(mostra) {
      var certid = document.form1.v13_certidini.value;
      if (mostra == true) {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_certid', 'func_certid.php?funcao_js=parent.js_mostracertid_ini1|0', 'Pesquisa', true);
      } else {
        if (document.form1.v13_certidini.value != '') {
          js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_certid', 'func_certid.php?pesquisa_chave=' + document.form1.v13_certidini.value + '&funcao_js=parent.js_mostracertid_ini', 'Pesquisa', false);
          document.form1.v13_certidfim.value = document.form1.v13_certidini.value;
        } else {
          document.form1.v13_certidini.value = '';
        }
      }
    }

    function js_mostracertid_ini(chave, erro) {
      resetlist();
      resertcodigo();
      if (erro == true) {
        document.form1.v13_certidini.value = '';
        document.form1.v13_certidini.focus();
      }
    }

    function js_mostracertid_ini1(chave1) {
      resetlist();
      resertcodigo();
      document.form1.v13_certidini.value = chave1;
      document.form1.v13_certidfim.value = chave1;
      db_iframe_certid.hide();
    }

    function js_pesquisa_certid_fim(mostra) {
      if (mostra == true) {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_certid', 'func_certid.php?funcao_js=parent.js_mostracertid_fim1|v13_certid', 'Pesquisa', true);
      } else {
        if (document.form1.v13_certidfim.value != '') {
          js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_certid', 'func_certid.php?pesquisa_chave=' + document.form1.v13_certidfim.value + '&funcao_js=parent.js_mostracertidfim', 'Pesquisa', false);
        } else {
          document.form1.v13_certidfim.value = '';
        }
      }
    }

    function js_mostracertidfim(chave, erro) {
      resetlist();
      resertcodigo();
      if (erro == true) {
        document.form1.v13_certidfim.value = '';
        document.form1.v13_certidfim.focus();
      }
    }

    function js_mostracertid_fim1(chave1) {
      resetlist();
      resertcodigo();
      document.form1.v13_certidfim.value = chave1;
      db_iframe_certid.hide();
    }

    function js_testacampo(func) {
      if (document.form1.v13_certidini.value == "") {
        db_msgbox('Informe uma Certidão!!');
        document.form1.v13_certidini.focus();
        return false;
      } else {
        return true;
      }

    }

    function js_pesquisalista(mostra) {
      if (mostra == true) {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_lista', 'func_lista.php?funcao_js=parent.js_mostralista1|k60_codigo|k60_descr', 'Pesquisa', true);
      } else {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_lista', 'func_lista.php?pesquisa_chave=' + document.form1.k60_codigo.value + '&funcao_js=parent.js_mostralista', 'Pesquisa', 'false');
      }
    }

    function js_mostralista(chave, erro) {
      document.form1.k60_descr.value = chave;
      if (erro == true) {
        document.form1.k60_descr.focus();
        document.form1.k60_descr.value = '';
      } else {
        resetlist();
        jQuery('#btpesquisar').prop('disabled', false);
      }
      db_iframe_lista.hide();
    }

    function js_mostralista1(chave1, chave2) {
      document.form1.k60_codigo.value = chave1;
      document.form1.k60_descr.value = chave2;
      resetlist();
      jQuery('#btpesquisar').prop('disabled', false);
      db_iframe_lista.hide();
    }

    function intervalo() {
      var intervalo = setInterval(() => {
        if (processando) {
          const dado = new FormData;
          HttpClient.get(routers.getprocessamento, {
              body: dado,
              reportProgress: false
            })
            .then((res) => {
              const data = res.data
              if (data.processamento) {
                jQuery('#progress').show();
                processando = true;
                var value = data.quantidade;
                if (value == 0) {
                  value = 1;
                }
                js_termo_termometro(value);

                jQuery('#btprocessar').show();
                jQuery('#btprocessaranulacao').hide();

              } else {
                processando = false;
                jQuery('#progress').hide();
                clearInterval(intervalo);
              }
            })
        }
      }, 5000);
    }

    intervalo();


    function resetlist() {
      $table.bootstrapTable('destroy');
      jQuery('#btprocessar').show();
      jQuery('#btprocessaranulacao').hide();
    }

    function resertcodigo() {
      jQuery('#k60_codigo').val('');
      jQuery('#k60_descr').val('');
    }
  </script>

</body>

</html>
<script>
  $("v13_certidini").addClassName("field-size2");
  $("v13_certidfim").addClassName("field-size2");
</script>