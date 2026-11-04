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
//MODULO: fiscal

$clfandam->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("y41_descr");
$clrotulo->label("y70_codvist");
$clrotulo->label("y30_codnoti");
$clrotulo->label("y50_codauto");
$clrotulo->label("nome");
$clrotulo->label("p58_numero");
$clrotulo->label("y60_proces");
$clrotulo->label("p58_requer");
$clrotulo->label("pa01_codigo");
$clrotulo->label("nl01_codlanc");
$clrotulo->label("y41_permcalc");
$clrotulo->label("y39_id_usuario");
$clrotulo->label("y39_data");

if (!isset($getIntimacao)){
   $getIntimacao = '';
}

if (!isset($sequencial)){
  $sequencial = '';
}

?>
<style>
  input#y39_data {
    display: none;
  }
</style>
<form id="form1" name="form1" method="post" action="">
  <center>
    <table border="0">
      <tr>
        <td nowrap title="<?= @$Ty39_codandam ?>">
          <?= @$Ly39_codandam ?>
        </td>
        <td>
          <?php 
          db_input('y39_codandam', 20, $Iy39_codandam, true, 'text', 3, "");
          db_input('y70_codvist', 20, $Iy70_codvist, true, 'hidden', 3, "");
          db_input('y30_codnoti', 20, $Iy30_codnoti, true, 'hidden', 3, "");
          db_input('y50_codauto', 20, $Iy50_codauto, true, 'hidden', 3, "");
          db_input('nl01_codlanc', 20, $Inl01_codlanc, true, 'hidden', 3, "");

          if (isset($y50_codauto) and $y50_codauto != '') {
            $tipoPeca = 3;
            $codigo   = $y50_codauto;
          } elseif ((isset($y30_codnoti) and $y30_codnoti != '') and (isset($intimacao) and $intimacao == 1)) {
            $tipoPeca = 4;
            $codigo   = $y30_codnoti;
          } elseif (isset($y70_codvist) and $y70_codvist != '') {
            $tipoPeca = 5;
            $codigo   = $y70_codvist;
          } elseif (isset($y30_codnoti) and $y30_codnoti != '') {
            $tipoPeca = 2;
            $codigo   = $y30_codnoti;
          } elseif (isset($nl01_codlanc) and $nl01_codlanc != '') {
            $tipoPeca = 6;
            $codigo   = $nl01_codlanc;
          }

          db_input('tipoPeca', 20, $tipoPeca, true, 'hidden', 3, '');
          ?>

          <?php 
          if ($db_opcao == 1) {
            $y39_data_dia = date("d", db_getsession("DB_datausu"));
            $y39_data_mes = date("m", db_getsession("DB_datausu"));
            $y39_data_ano = date("Y", db_getsession("DB_datausu"));
            $y39_data = $y39_data_ano . '-' . $y39_data_mes . '-' . $y39_data_dia;;
          }

          db_inputdata('y39_data', @$y39_data_dia, @$y39_data_mes, @$y39_data_ano, true, 'text', 3, "")
          ?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?= @$Ty39_codtipo ?>">
          <?php 
          db_ancora(@$Ly39_codtipo, "js_pesquisay39_codtipo(true);", $db_opcao);
          ?>
        </td>
        <td>
          <?php 
          db_input('y39_codtipo', 10, $Iy39_codtipo, true, 'text', $db_opcao, " onchange='js_pesquisay39_codtipo(false);'")
          ?>
          <?php 
          db_input('y41_descr', 54, $Iy41_descr, true, 'text', 3, '')
          ?>
          <?php 
          db_input('y41_permcalc', 10, $Iy41_permcalc, true, 'hidden', 3, '')
          ?>

        </td>
      </tr>
      <tr>
        <td nowrap title="<?= @$Ty39_obs ?>">
          <?= @$Ly39_obs ?>
        </td>
        <td>
          <?php 
          db_textarea('y39_obs', 7, 66, $Iy39_obs, true, 'text', $db_opcao, "")
          ?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?= @$Ty39_id_usuario ?>">
        </td>
        <td>
          <?php 
          db_input('y39_id_usuario', 5, $Iy39_id_usuario, true, 'hidden', $db_opcao, "");
          echo "<script>document.form1.y39_id_usuario.value = '" . db_getsession("DB_id_usuario") . "'</script>";
          ?>
          <?php 
          db_input('nome', 20, $Inome, true, 'hidden', 3, '')
          ?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?= @$Ty60_proces ?>">
          <?php 
          db_ancora("<b>Processo:</b>", ' js_mostracodproc(true); ', 4);
          ?>
        </td>
        <td>
          <?php 
          db_input('p58_numero', 14, $Ip58_numero, true, 'text', $bloqueia, 'onchange="js_mostracodproc(false);"', "", "", "", 14);
          db_input('y60_proces', 10, $Iy60_proces, true, 'hidden', 4, '');

          db_input('p58_requer', 50, $Ip58_requer, true, 'text', 3, '');
          db_input('pa01_codigo', 5, $Ipa01_codigo, true, 'hidden', 3, '');
          ?>

        </td>
      </tr>
      <tr>
        <td nowrap title="<?= @$Ty39_hora ?>">
          <?= @$Ly39_hora ?>
        </td>
        <td>
          <?php
          if (!isset($y39_hora) or $y39_hora == '' or $y39_hora == null) {
            $y39_hora = date('H:i');
          }
          db_input('y39_hora', 5, $Iy39_hora, true, 'text', $db_opcao, 'OnKeyUp="Mascara_Hora(this.value)"');
          ?>
        </td>
      </tr>
      <?php db_input('sequencial', 20, $sequencial, true, 'hidden', 3, ''); ?>

      <?php 
      if (isset($nl01_codlanc) && !empty($nl01_codlanc)) {
        $oSql = "select nl27_autodvenc as pa01_autodvenc,nl27_autodprazo as pa01_autodprazo from fiscalizacao.fis_parnotificacaolancamento where nl27_instit = " . db_getsession('DB_instit');
      } else {
        $oSql = "select * from fiscalizacao.fis_fiscalparametros";
      }
      $rsDiasParam = db_query($oSql);
      if (pg_num_rows($rsDiasParam) > 0)
         db_fieldsmemory($rsDiasParam, 0);
         db_input('pa01_autodvenc', 20, $pa01_autodvenc, true, 'hidden', 3, '');
         db_input('pa01_autodprazo', 20, $pa01_autodprazo, true, 'hidden', 3, '');
      ?>
      <tr id="tr_data_ciencia" style="display: none;">
        <td><b>Data de ciência:</b></td>
        <td align="left">
          <?php
            db_inputdata('data_ciencia', @$data_ciencia_dia, @$data_ciencia_mes, @$data_ciencia_ano, true, 'text', $db_opcao, "onchange='somadata2();'", "", "", "parent.somadata2();");

          ?>
        </td>
      </tr>

      <tr id="div_data" style="display:none;">
        <td><strong>Data de vencimento:</strong> </td>

        <td align="left">
          <?php 
            db_inputdata('y50_dtvenc', @$y50_dtvenc_dia, @$y50_dtvenc_mes, @$y50_dtvenc_ano, true, 'text', 3, "")
          ?>
        </td>
        <td>
          <?php 
            db_inputdata('y50_dtvenc_tmp', @$y50_dtvenc_tmp_dia, @$y50_dtvenc_tmp_mes, @$y50_dtvenc_tmp_ano, true, 'hidden', 3, "")
          ?>
        </td>
      </tr>
      <tr id="prazorec" style="display:none;">
        <td><strong>Data do prazo de recurso:</strong> </td>

        <td align="left">
          <?php 
           db_inputdata('y50_prazorec', @$y50_prazorec_dia, @$y50_prazorec_mes, @$y50_prazorec_ano, true, 'text', 3, "")
          ?>
        </td>
      </tr>
    </table>
  </center>
  </br>


  <input name="db_opcao" type="submit" onclick="return js_confirm();" id="db_opcao" value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" <?= ($db_botao == false ? "disabled" : "") ?>>

  <?php
  if ($db_opcao != 1 && $db_opcao != 3 && $db_opcao != 33) {
  ?>
    <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
  <?php
  }
  ?>
</form>
<script>
  document.form1.y50_dtvenc.value = '';
  document.form1.y50_prazorec.value = '';

  var PosMouseY, PosMoudeX;

  function js_comparaDatasy50_dtvenc(dia, mes, ano) {



    var today = new Date();
    var choose = document.form1.y50_dtvenc_ano.value + '-' + document.form1.y50_dtvenc_mes.value + '-' + document.form1.y50_dtvenc_dia.value;

    var diaatual = new Date(today);
    var escolha = new Date(choose);


    if (escolha.setHours(0, 0, 0, 0) >= diaatual.setHours(0, 0, 0, 0)) {
      alert('Data Inválida!')

    } else {

      var objData = document.getElementById('y50_dtvenc');

      objData.value = dia + '/' + mes + '/' + ano;
      somadata("<?= $pa01_autodprazo ?>", "<?= $pa01_autodvenc ?>");
    }
  }

  function js_confirm(form1) {

    if (contemDataCiencia == 't') {
      if (document.getElementById('data_ciencia').value == '') {
        alert('Preencha a data de ciência!');
        document.getElementById('data_ciencia').focus();
        return false;
      }
    }

    if (document.form1.y39_hora.value == "") {
      alert('Informe a Hora!');
      return false;
    }

    if (document.form1.y39_hora.value.length < 5) {
      alert('Hora Inválida!');
      return false;
      // Pega a Data de Hoje e Data da Ciência
    }
    var sdata_ciencia = document.getElementById('data_ciencia').value;
    var sdatahoje = new Date();

    adata_ciencia = sdata_ciencia.split('/');

    // Cria dois objetos do tipo Date() para comparação
    var ddata_ciencia = new Date(adata_ciencia[2], adata_ciencia[1] - 1, adata_ciencia[0]);
    var ddata_hoje = new Date(sdatahoje.getFullYear(), sdatahoje.getMonth(), sdatahoje.getDate());

    if (ddata_ciencia > ddata_hoje) {
      alert("A data de ciência não pode ser maior que a data de hoje.");
      document.getElementById('data_ciencia').focus();
      return false;
    }

    if (document.form1.y50_dtvenc.value == "" && document.form1.y41_permcalc.value == 't') {
      alert("Preencha a data de vencimento!");
      document.form1.y50_dtvenc.focus();
      return false;
    } else {


      var tipoPeca = document.form1.tipoPeca.value;

      if (tipoPeca == '3') {
        mensagem = 'o auto';
      } else if (tipoPeca == '2') {
        mensagem = 'a notificação';
      } else if (tipoPeca == '4') {
        mensagem = 'a intimação';
      } else if (tipoPeca == '5') {
        mensagem = 'a vistoria';
      }

      var envia = confirm('Deseja implantar ' + mensagem + '?');

      if (envia == true) {
        var enviaComfirma = confirm('Confirma a implantação d' + mensagem + '?');

        if (enviaComfirma == true) {

          return true;
        } else {

          return false;
          alert('Operação cancelada.');
        }
      } else {
        return false;
      }

    }
  }

  function somadatafor(dias) {

    datahoje = document.form1.data_ciencia.value;
    dia = datahoje.substr(0, 2);
    mes = datahoje.substr(3, 2);
    ano = datahoje.substr(6, 4);

    for (i = 0; i < dias; i++) {
      if (mes == 01 || mes == 03 || mes == 05 || mes == 07 || mes == 08 || mes == 10 || mes == 12) {
        if (mes == 12 && dia == 31) {
          mes = 01;
          ano++;
          dia = 00;
        }
        if (dia == 31 && mes != 12) {
          mes++;
          dia = 00;
        }
      }

      if (mes == 04 || mes == 06 || mes == 09 || mes == 11) {
        if (dia == 30) {
          dia = 00;
          mes++;
        }
      }

      if (mes == 02) {
        if (ano % 4 == 0 && ano % 100 != 0) {
          if (dia == 29) {
            dia = 00;
            mes++;
          }
        } else {
          if (dia == 28) {
            dia = 00;
            mes++;
          }
        }
      }

      dia++;

    }
    dia = dia.toString();
    mes = mes.toString();
    if (dia.length == 1) {
      dia = "0" + dia;
    }
    if (mes.length == 1) {
      mes = "0" + mes;
    }

    nova_data = dia + "/" + mes + "/" + ano;
    return nova_data;
  }

  function somadata(dias, dias2) {

    document.form1.y50_prazorec.value = somadatafor(dias);
    document.form1.y50_prazorec_dia.value = dia;
    document.form1.y50_prazorec_mes.value = mes;
    document.form1.y50_prazorec_ano.value = ano;

    document.form1.y50_dtvenc_tmp.value = somadatafor(dias2);
    document.form1.y50_dtvenc_tmp_dia.value = dia;
    document.form1.y50_dtvenc_tmp_mes.value = mes;
    document.form1.y50_dtvenc_tmp_ano.value = ano;

  }

  function somadata2() {
    document.form1.y50_dtvenc.value = somadatafor("<?= $pa01_autodvenc ?>");
    document.form1.y50_dtvenc_dia.value = dia;
    document.form1.y50_dtvenc_mes.value = mes;
    document.form1.y50_dtvenc_ano.value = ano;

    document.form1.y50_prazorec.value = somadatafor("<?= $pa01_autodprazo ?>");
    document.form1.y50_prazorec_dia.value = dia;
    document.form1.y50_prazorec_mes.value = mes;
    document.form1.y50_prazorec_ano.value = ano;
    js_diaultil();
  }


  function js_consultaformula() {
    var oConsulta = document.form1.y41_permcalc.value;

    if (oConsulta == 't') {
      document.getElementById('div_data').removeAttribute('style');
      document.getElementById('y50_dtvenc').disabled = false;
      document.getElementById('y50_dtvenc_dia').disabled = false;
      document.getElementById('y50_dtvenc_mes').disabled = false;
      document.getElementById('y50_dtvenc_ano').disabled = false;

      /*		    document.getElementById('y50_prazorec').disabled = false;
			    document.getElementById('y50_prazorec_dia').disabled = false;
			    document.getElementById('y50_prazorec_mes').disabled = false;
			    document.getElementById('y50_prazorec_ano').disabled = false;
*/
    } else {

      document.getElementById('div_data').style.display = "none";

      document.getElementById('y50_dtvenc').disabled = true;
      document.getElementById('y50_dtvenc_dia').disabled = true;
      document.getElementById('y50_dtvenc_mes').disabled = true;
      document.getElementById('y50_dtvenc_ano').disabled = true;
      /*
      			    document.getElementById('y50_prazorec').disabled = true;
      			    document.getElementById('y50_prazorec_dia').disabled = true;
      			    document.getElementById('y50_prazorec_mes').disabled = true;
      			    document.getElementById('y50_prazorec_ano').disabled = true;
      */
    }
  }


  function js_pesquisay39_codtipo(mostra) {

    if (mostra == true) {
      js_OpenJanelaIframe('', 'db_iframe_tipoandam', 'func_fis_tipoandamentos.php?funcao_js=parent.js_mostratipoandam1|y41_codtipo|dl_data_ciencia_andamento|dl_tem_data_recurso|y41_permcalc|y41_descr<?= $getIntimacao ?>&tipoPeca=' + document.form1.tipoPeca.value + '&codigo=<?= $codigo ?>', 'Pesquisa', true);
    } else {
      js_OpenJanelaIframe('', 'db_iframe_tipoandam', 'func_fis_tipoandamentos.php?pesquisa_chave=' + document.form1.y39_codtipo.value + '&funcao_js=parent.js_mostratipoandam<?= $getIntimacao ?>&tipoPeca=' + document.form1.tipoPeca.value + '&codigo=<?= $codigo ?>', 'Pesquisa', false);
    }
  }

  function js_mostratipoandam(chave, erro) {
    document.form1.y41_descr.value = chave;
    if (erro == true) {
      document.form1.y39_codtipo.focus();
      document.form1.y39_codtipo.value = '';
      document.form1.y41_descr.value = '';
    }
    js_consultaformula();
  }
  contemDataCiencia = "";

  function js_mostratipoandam1(chave1, chave2, chave3, chave4, chave5) {
    document.form1.y39_codtipo.value = chave1;
    document.form1.y41_permcalc.value = chave4;
    document.form1.y41_descr.value = chave5;

    contemDataCiencia = chave2;

    // Verifica se tem data de ciência.
    if (chave2 == 't') {
      document.getElementById('tr_data_ciencia').removeAttribute('style');
      document.form1.y39_hora.value = '';
    }
    // Verifica se tem data de recurso.
    if (chave3 == 't') {
      document.getElementById('prazorec').removeAttribute('style');
    } else {
      document.getElementById('prazorec').setAttribute('style', 'display:none');
    }
    js_consultaformula();
    db_iframe_tipoandam.hide();
  }

  function js_pesquisay39_id_usuario(mostra) {
    if (mostra == true) {
      js_OpenJanelaIframe('', 'db_iframe_db_usuarios', 'func_db_usuarios.php?funcao_js=parent.js_mostradb_usuarios1|id_usuario|nome', 'Pesquisa', true);
    } else {
      js_OpenJanelaIframe('', 'db_iframe_db_usuarios', 'func_db_usuarios.php?pesquisa_chave=' + document.form1.y39_id_usuario.value + '&funcao_js=parent.js_mostradb_usuarios', 'Pesquisa', false);
    }
  }

  function js_mostradb_usuarios(chave, erro) {
    document.form1.nome.value = chave;
    if (erro == true) {
      document.form1.y39_id_usuario.focus();
      document.form1.y39_id_usuario.value = '';
    }
  }

  function js_mostradb_usuarios1(chave1, chave2) {
    document.form1.y39_id_usuario.value = chave1;
    document.form1.nome.value = chave2;
    db_iframe_db_usuarios.hide();
  }

  function js_pesquisa() {
    js_OpenJanelaIframe('', 'db_iframe_fandam', 'func_<?= (!isset($pesqandam) && !isset($auto) ? "fis_vistoriaandam.php" : (!isset($auto) ? "fis_fiscalandam.php" : "fis_autoandam.php")) ?>?funcao_js=parent.js_preenchepesquisa|y39_codandam<?= $getIntimacao ?>', 'Pesquisa', true);
  }

  function js_preenchepesquisa(chave) {
    db_iframe_fandam.hide();
    <?php 
    if ($db_opcao == 2 || $db_opcao == 22 && !isset($pesqandam) && !isset($auto)) {
      echo " location.href = 'fis3_fis_fandam002.php?abas=1$getIntimacao&chavepesquisa='+chave;";
    } elseif ($db_opcao == 33 || $db_opcao == 3 && !isset($pesqandam)) {
      echo " location.href = 'fis3_fis_fandam003.php?abas=1$getIntimacao&chavepesquisa='+chave;";
    }
    if ($db_opcao == 2 || $db_opcao == 22 && isset($pesqandam) && !isset($auto)) {
      echo " location.href = 'fis3_fis_fandamnoti002.php?abas=1$getIntimacao&chavepesquisa='+chave;";
    } elseif ($db_opcao == 33 || $db_opcao == 3 && isset($pesqandam) && !isset($auto)) {
      echo " location.href = 'fis3_fis_fandamnoti003.php?abas=1$getIntimacao&chavepesquisa='+chave;";
    }
    if ($db_opcao == 2 || $db_opcao == 22 && isset($auto)) {
      echo " location.href = 'fis3_fis_fandamauto002.php?abas=1$getIntimacao&chavepesquisa='+chave;";
    } elseif ($db_opcao == 33 || $db_opcao == 3 && isset($auto)) {
      echo " location.href = 'fis3_fis_fandamauto003.php?abas=1$getIntimacao&chavepesquisa='+chave;";
    }
    ?>
  }

  function js_mostracodproc(mostra) {
    <?php 
    if (isset($ProcFiscal) && $ProcFiscal != "") {

    ?>
      url = '&ProcFiscal=' + <?php  echo $ProcFiscal ?>;
    <?php 
    } else {
    ?>
      url = '';
    <?php 
    } ?>

    if (mostra == true) {
      js_OpenJanelaIframe('', 'db_iframe_proc', 'func_fis_processoadministrativo_andamentos.php?funcao_js=parent.js_mostraproc1|p58_codproc|p58_numero|z01_nome' + url, 'Pesquisa', true, '15');
    } else {
      js_OpenJanelaIframe('', 'db_iframe_proc', 'func_fis_processoadministrativo_andamentos.php?pesquisa_chave=' + document.form1.p58_numero.value + '&funcao_js=parent.js_mostraproc&chave_p58_numero=1' + url, 'Pesquisa', false);
    }
  }

  function js_mostraproc(chave, obs, erro) {
    if (erro == true) {
      document.form1.p58_numero.focus();
      document.form1.y60_proces.value = '';
      document.form1.p58_numero.value = '';
      document.form1.p58_requer.value = '';
    } else {
      document.form1.p58_requer.value = obs;
      document.form1.y60_proces.value = chave;

    }
  }

  function js_mostraproc1(chave1, n, z, chave2) {
    document.form1.p58_requer.value = z;
    document.form1.y60_proces.value = chave1;
    document.form1.p58_numero.value = n;
    db_iframe_proc.hide();
  }

  function Mascara_Hora(Hora) {
    var hora01 = '';
    hora01 = hora01 + Hora;
    if (hora01.length == 2) {
      hora01 = hora01 + ':';
      document.form1.y39_hora.value = hora01;
    }
    if (hora01.length == 5) {
      Verifica_Hora();
    }
  }

  function Verifica_Hora() {
    hrs = (document.form1.y39_hora.value.substring(0, 2));
    min = (document.form1.y39_hora.value.substring(3, 5));

    estado = "";
    if ((hrs < 00) || (hrs > 23) || (min < 00) || (min > 59)) {
      estado = "errada";
    }

    if (document.form1.y39_hora.value == "") {
      estado = "errada";
    }

    if (estado == "errada") {
      alert("Hora inválida!");
      document.form1.y39_hora.focus();
      document.form1.y39_hora.value = "";
    }
  }
</script>
<script language="JavaScript" type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript">
  function js_diaultil() {

    var vencim = $('#y50_dtvenc').val();
    var prazor = $('#y50_prazorec').val();
    var ciencia = $('#data_ciencia').val();

    $.ajax({
      type: 'post',
      url: 'fis1_fis_diautil.php',
      data: {
        vencim: vencim,
        prazor: prazor,
        ciencia: ciencia
      },
      dataType: 'json',
      success: function(d) {
        if (d.erro != 0) {
          alert(d.erro);
        } else {
          $('#y50_dtvenc').val(d.sRetornoV_dia + "/" + d.sRetornoV_mes + "/" + d.sRetornoV_ano);
          $('#y50_dtvenc_ano').val(d.sRetornoV_ano);
          $('#y50_dtvenc_mes').val(d.sRetornoV_mes);
          $('#y50_dtvenc_dia').val(d.sRetornoV_dia);
          $('#y50_prazorec').val(d.sRetornoP_dia + "/" + d.sRetornoP_mes + "/" + d.sRetornoP_ano);
          $('#y50_prazorec_ano').val(d.sRetornoP_ano);
          $('#y50_prazorec_mes').val(d.sRetornoP_mes);
          $('#y50_prazorec_dia').val(d.sRetornoP_dia);
        }
      }
    });
  }
</script>
