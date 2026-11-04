<?php

/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
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
 *  Voce deve ter recebido uma copia dnulla Licenca Publica Geral GNU
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
require_once modification('libs/db_usuariosonline.php');
require_once modification('dbforms/db_funcoes.php');
parse_str($HTTP_SERVER_VARS['QUERY_STRING'], $queryString);
foreach ($queryString as $key => $value) {
    ${$key} = $value;
}
$clrotulo = new rotulocampo;
$clrotulo->label('rh01_regist');
$clrotulo->label('z01_nome');
$clrotulo->label('h12_codigo');
$clrotulo->label('h12_descr');
?>
<html>

<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/bootstrap-table@1.20.2/dist/bootstrap-table.min.css" rel="stylesheet">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
  <script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="">
  <div class="container">
    <form name="form1" method="post">
      <table align="center" border="0" cellspacing="4" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>
            <label class='bold m-2' onclick="js_pesquisarh01_regist(true);">
              <a href="#">Matricula : </a>
            </label>
          </td>
          <td nowrap>
            <?php
            db_input('rh01_regist', 6, $Irh01_regist, true, 'text', 1, "onchange='js_pesquisarh01_regist(false);'")
            ?>
            <?php
            db_input('z01_nome', 40, $Iz01_nome, true, 'text', 3, '')
            ?>
          </td>
        </tr>
        <tr>
          <td>
            <label class='bold m-2'>Assentamento :</label>
          </td>
          <td>
            <select name="h12_codigo" id="h12_codigo">
            </select>
            <select name="h12_codigodescr" id="h12_codigodescr">
            </select>
            <script>
              function js_ProcCod_h12_codigo(proc, res) {
                var sel1 = document.forms[0].elements[proc];
                var sel2 = document.forms[0].elements[res];
                for (var i = 0; i < sel1.options.length; i++) {
                  if (sel1.options[sel1.selectedIndex].value == sel2.options[i].value)
                    sel2.options[i].selected = true;
                }
              }
              js_ProcCod_h12_codigo('h12_codigo', 'h12_codigodescr');
            </script>
          </td>
        </tr>
        <tr>
          <td>
            <label class='bold m-2'>Data Limite :</label>
          </td>
          <td>
            <?php
            $dataprocessamento = date("Y-m-d");
            $dataprocessamento_dia = date("d");
            $dataprocessamento_mes = date("m");
            $dataprocessamento_ano = date("Y");
            db_inputdata(
                'dataprocessamento',
                '00',
                '00',
                '0000',
                true,
                'text',
                2
            );
            ?>
          </td>
        </tr>
        <tr>
          <td align="center" colspan="2">
            <button onClick="js_limpar()" type="button"  class="m-2">Limpar</button>
            <button id="btprocessar" disabled="" type="button" onClick="js_pesquisa()" class="m-2">Processar</button>
            <button id="btstop" type="button" onClick="js_stop()" style="display: none; class="m-2">Parar Processamento</button>

          </td>
        </tr>
      </table>
    </form>
    <br>
    <br>
    <div class="progress">
      <div id="progress" class="progress-bar" role="progressbar" style="width: 0%; display: none;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div>
      <H3  style="display: none;" id="perc"></H3>
    </div>
    <div id="divtable">
      <table id="table"></table>
    </div>
  </div>
  <?php
      db_menu(
          db_getsession("DB_id_usuario"),
          db_getsession("DB_modulo"),
          db_getsession("DB_anousu"),
          db_getsession("DB_instit")
      );
        ?>
</body>

</html>
<script>
  var processando = true;
  const url = '<?= ECIDADE_REQUEST_PATH ?>'+'v4/api/recursos-humanos/rh/concessaodireitos';
  const routers = {
    'processar': url + '/processar',
    'search': url + '/assentamentos',
    'getprocessamento': url + '/getprocessamento',
    'stopsprocessamento': url + '/stopsprocessamento'
  };
  var datalimite = []
  const dado = new FormData;
  HttpClient.post(routers.search, {
      body: dado,
      reportProgress : false
    })
    .then((res) => {
      if (res.hasOwnProperty('data')) {
        let assentamentos = res.data;
        resultado = res.data[0].rh500_datalimite.split("-");
        dataoriginal = resultado[2] + '/' + resultado[1] + '/' + resultado[0]
        jQuery("#dataprocessamento").val(dataoriginal).change();
      
        for (let index = 0; index < assentamentos.length; index++) {
          datalimite.push([
            assentamentos[index].rh500_sequencial,
            assentamentos[index].rh500_datalimite
          ]);
          var x = document.createElement("OPTION");
          x.setAttribute("value", assentamentos[index].rh500_sequencial);
          var t = document.createTextNode(assentamentos[index].h12_codigo);
          x.appendChild(t);

          var x1 = document.createElement("OPTION");
          x1.setAttribute("value", assentamentos[index].rh500_sequencial);
          var t1 = document.createTextNode(assentamentos[index].h12_descr);
          x1.appendChild(t1);
          jQuery('#h12_codigo').append(x)
          jQuery('#h12_codigodescr').append(x1)
        }
      } else {
        alert(JSON.stringify(res));
      }
    });

    function fdatalimite(value){
      for (let index = 0; index < datalimite.length; index++) {
        let element = datalimite[index];
        if (element[0] == value) {
          resultado = element[1].split("-");
          dataoriginal = resultado[2] + '/' + resultado[1] + '/' + resultado[0]
          jQuery('#dataprocessamento').val(dataoriginal)
        }
      }
    }
  //Unifica Select Assentamentos
  jQuery('#h12_codigo').change(function() {
    if (jQuery('#h12_codigodescr').children("option:selected").val() !=
      jQuery('#h12_codigo').children("option:selected").val()) {
      let value = jQuery('#h12_codigo').children("option:selected").val();
      jQuery("#h12_codigodescr").val(value).change();
      fdatalimite(value)
    }
  });



  jQuery('#h12_codigodescr').change(function() {
    if (jQuery('#h12_codigodescr').children("option:selected").val() !=
      jQuery('#h12_codigo').children("option:selected").val()) {
      let value = jQuery('#h12_codigodescr').children("option:selected").val();
      jQuery("#h12_codigo").val(value).change();
      fdatalimite(value)
    }
  });

  function js_pesquisarh01_regist(mostra) {
    if (mostra == true) {
      js_OpenJanelaIframe('CurrentWindow.corpo',
        'db_iframe_rhpessoal',
        'func_rhpessoal.php?filtro_lotacao=true&funcao_js=parent.js_mostrapessoal1|' +
        'rh01_regist|z01_nome&instit=<?= (db_getsession("DB_instit")) ?>',
        'Pesquisa',
        true);
    } else {
      if (document.form1.rh01_regist.value != '') {
        js_OpenJanelaIframe('CurrentWindow.corpo',
          'db_iframe_rhpessoal',
          'func_rhpessoal.php?filtro_lotacao=true&pesquisa_chave=' +
          document.form1.rh01_regist.value +
          '&funcao_js=parent.js_mostrapessoal&instit=<?= (db_getsession("DB_instit")) ?>',
          'Pesquisa',
          false);
      } else {
        document.form1.z01_nome.value = '';
      }
    }
  }

  function js_mostrapessoal(chave, erro) {
    document.form1.z01_nome.value = chave;
    if (erro == true) {
      document.form1.rh01_regist.focus();
      document.form1.rh01_regist.value = '';
    }
  }

  function js_mostrapessoal1(chave1, chave2) {
    document.form1.rh01_regist.value = chave1;
    document.form1.z01_nome.value = chave2;
    db_iframe_rhpessoal.hide();
  }

  var $table = jQuery('#table')
  const columns = [{
      align: 'center',
      title: 'Sequencial',
      field: 'rh504_sequencial',
      sortable: true
    },
    {
      field: 'rh504_data',
      title: 'Data Original',
      sortable: true,
      align: 'center',
      formatter: FormatterData
    },
    {
      field: 'rh506_datanova',
      title: 'Nova data',
      sortable: true,
      align: 'center',
      formatter: FormatterNovaData
    },
    {
      field: 'rh501_perc',
      align: 'center',
      title: 'Percentual %',
      sortable: true
    },
    {
      field: 'h31_amparolegal',
      align: 'center',
      title: 'Status',
      sortable: true,
      formatter: FormatterStatus
    }
  ];

  function FormatterStatus(value) {
    if (!value) {
      value = 'Sem Portaria';
    }
    return [
      '<p>' + value + '</p>'
    ].join('')
  }

  function FormatterData(value) {

    resultado = value.split("-");
    dataoriginal = resultado[2] + '/' + resultado[1] + '/' + resultado[0]
    return [
      '<a href="#" onclick="js_data(this);" >' + dataoriginal + '</a>'
    ].join('')
  }

  function FormatterNovaData(value) {
    if (value) {
      resultado = value.split("-");
      dataoriginal = resultado[2] + '/' + resultado[1] + '/' + resultado[0]
      return [
        '<a href="#" onclick="js_datanova(this);" >' + dataoriginal + '</a>'
      ].join('')
    } else {
      return [
        '<p> ---- </p>'
      ].join('')
    }

}

function js_limpar() {
    $table.bootstrapTable('destroy');
    jQuery('#rh01_regist').val('')
    jQuery('#z01_nome').val('')
}

  function js_pesquisa() {
    if(jQuery('#dataprocessamento').val() == '00/00/0000'){
      alert('Data Invalida');
      return
    }
    jQuery('#btprocessar').prop('disabled', true);
    $table.bootstrapTable('destroy');
    const data = {
      matricula: jQuery('#rh01_regist').val(),
      rh500_sequencial: jQuery('#h12_codigo').val(),
      dataprocessamento: jQuery('#dataprocessamento').val(),
      DB_instit: <?php echo db_getsession("DB_instit"); ?>,
      DB_datausu: <?php echo db_getsession("DB_datausu"); ?>
    };
    const dado = new FormData;
    for (index in data) {
      dado.append(index, data[index]);
    }
    HttpClient.post(routers.processar, {
        body: dado,
        reportProgress : true
      })
      .then((res) => {
        if (res.hasOwnProperty('data')) {
          if (res.error) {
            jQuery('#btprocessar').prop('disabled', false);
            alert(res.message);
            return
          }
          if (jQuery('#rh01_regist').val() != '') {
            $table.bootstrapTable({
              columns,
              pageSize: 5,
              data: res.data,
              pagination: true,
            });
            jQuery('#btprocessar').prop('disabled', false);
          } else {
            jQuery('#progress').show();
            jQuery('#perc').show();
            jQuery('#perc').text('Iniciando...');
            setTimeout(function(){
                processando = true;
                intervalo();
            },3000);
          }
          alert(res.message);
        }
        jQuery('#rh01_regist').removeAttr('disabled');
      });
  }

  function js_stop() {
    const dado = new FormData;
    HttpClient.post(routers.stopsprocessamento, {
        body: dado,
        reportProgress : true
      })
      .then((res) => {
        jQuery('#progress').hide();
        jQuery('#perc').hide();
        jQuery('#btstop').hide();
        jQuery('#btprocessar').prop('disabled', false);
        processando = false;
      });
  }

  function js_data(e) {
    id = e.closest("a").closest("td").closest("tr").children[0].innerText;
    js_OpenJanelaIframe('CurrentWindow.corpo',
      'func_data',
      'rec3_consvantagemdata001.php?rh507_concessaocalculo=' + id+'&acao="data"', 'Data',
      true,
      '20');
  }

  function js_datanova(e) {
    id = e.closest("a").closest("td").closest("tr").children[0].innerText;
    js_OpenJanelaIframe('CurrentWindow.corpo',
      'func_data',
      'rec3_consvantagemdata001.php?rh507_concessaocalculo='+ id+'&acao="novadata"', 'Data',
      true,
      '20');
  }

  function intervalo (){
    var intervalo = setInterval(() => {
      if(processando){
        const dado = new FormData;
        HttpClient.post(routers.getprocessamento, {
          body: dado,
          reportProgress : false
        })
        .then((res) => {
          const data = res.data
          if (data.length != 0) {
            var pr = document.getElementById("progress"); 
            processando = true;
            var value = 100 - (data[0].rh510_quantidade / data[0].rh510_total * 100).toFixed(2);

            if ((data[0].rh510_quantidade / data[0].rh510_total * 100).toFixed(2) >= 100.00) {
                this.value = 100
            }

            jQuery("#perc").text( data[0].rh510_total - data[0].rh510_quantidade +' / '+ data[0].rh510_total);
            pr.style.width = value+'%';
            jQuery('#progress').show();
            jQuery('#perc').show();
            jQuery('#btprocessar').prop('disabled', true);
            jQuery('#btstop').show();
          } else {
            jQuery('#btstop').hide();
            jQuery('#btprocessar').prop('disabled', false);
            processando = false;
            jQuery('#progress').hide();
            jQuery('#perc').hide();
            clearInterval(intervalo);
          }
        })
      }
    }, 3000);
  }

  intervalo();

</script>