<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009 DBSeller Servicos de Informatica             
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
require_once modification("libs/db_utils.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("libs/db_app.utils.php");
require_once modification("dbforms/db_classesgenericas.php");

db_postmemory($_POST);
$oPost = db_utils::postmemory($_POST);

$z01_nome = $oPost->z01_nomematri;
$j39_idcons = $oPost->iConstrucao;
?>

<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("scripts.js, strings.js, prototype.js");
    db_app::load("estilos.css, grid.style.css");

    // $parametro recebe cod_matricula
    $sql = " select iptubaixa.*,                                                                                             ";
    $sql .= "        iptubaixaproc.*,                                                                                         ";
    $sql .= "        proprietario.* ,                                                                                         ";
    $sql .= "        c.z01_nome as promitente,                                                                                ";
    $sql .= "        c.z01_ender as ender_promitente,                                                                         ";
    $sql .= "        c.z01_numero as numero_promitente,                                                                       ";
    $sql .= "        c.z01_compl as compl_promitente,                                                                         ";
    $sql .= "        c.z01_munic as munic_promitente,                                                                         ";
    $sql .= "        c.z01_uf as uf_promitente,                                                                               ";
    $sql .= "        c.z01_telef as telef_promitente,                                                                         ";
    $sql .= "        j.z01_nome as imobiliaria,                                                                               ";
    $sql .= "        j.z01_ender as ender_imobiliaria,                                                                        ";
    $sql .= "        loteloc.*,                                                                                               ";
    $sql .= "        setorloc.*,                                                                                              ";
    $sql .= "        loteloteam.j34_loteam,                                                                                   ";
    $sql .= "        loteam.j34_descr,                                                                                        ";
    $sql .= "        j01_idbql,                                                                                               ";
    $sql .= "        j01_idbql,                                                                                               ";
    $sql .= "        bairro.j13_descr as bairro,                                                                              ";
    $sql .= "        iptuconstr.j39_obs as obsiptuconstr,                                                                     ";
    $sql .= "        round(((                                                                                                 ";
    $sql .= "              round(( select rnfracao                                                                            ";
    $sql .= "                       from fc_iptu_fracionalote({$k00_matric}," . db_getsession("DB_datausu") . ",true,false) ";
    $sql .= "                   ),10)                                                                                         ";
    $sql .= "            * lote.j34_area)/100),2) as areafracionada                                                           ";
    $sql .= "   from proprietario                                                                                     ";
    $sql .= "        left join iptubaixa     on j02_matric                 = j01_matric                               ";
    $sql .= "        left join iptubaixaproc on j02_matric                 = j03_matric                               ";
    $sql .= "        left outer join cgm c   on j41_numcgm                 = c.z01_numcgm                             ";
    $sql .= "        left outer join cgm j   on j44_numcgm                 = j.z01_numcgm                             ";
    $sql .= "        left join  loteloc      on loteloc.j06_idbql          = j01_idbql                                ";
    $sql .= "        left join  setorloc     on setorloc.j05_codigo        = loteloc.j06_setorloc                     ";

    $sql .= "       inner join  lote               on lote.j34_idbql                 = j01_idbql                      ";
    $sql .= "        left join  loteloteam         on loteloteam.j34_idbql           = lote.j34_idbql                 ";
    $sql .= "        left join  loteam             on loteam.j34_loteam              = loteloteam.j34_loteam          ";
    $sql .= "        left join  bairro             on bairro.j13_codi                = lote.j34_bairro                ";
    $sql .= "        left join  iptuconstr         on iptuconstr.j39_matric          = j01_matric                     ";

    $sql .= "   where j01_matric = {$k00_matric} limit 1   ";
    // echo $sql;
    $matriculaSelecionada = db_query($sql) or die($sql);
    $fieldmatriculaSelecionada = db_utils::fieldsMemory($matriculaSelecionada, 0);

    $areafrac = $fieldmatriculaSelecionada->areafracionada;

    $sqlareatotal = " select sum(j34_area) as areatotal 
  from (select distinct j34_idbql, j34_area
  from lote 
  inner join iptubase on j01_idbql = j34_idbql 
  where j34_setor  = '" . $fieldmatriculaSelecionada->j34_setor . "'
  and j34_quadra = '" . $fieldmatriculaSelecionada->j34_quadra . "' 
  and j34_lote   = '" . $fieldmatriculaSelecionada->j34_lote . "'
  and j01_baixa is null) as x";

    $resultareatotal = db_query($sqlareatotal);
    $linhasareatotal = pg_num_rows($resultareatotal);
    if ($linhasareatotal > 0) {
        $temareatotal = "";
        $areatotal = pg_result($resultareatotal, 0);
    } else {
        $temareatotal = "disabled";
        $areatotal = "Não Informado";
    }

    $sqlareaconst = "
  select sum(j39_area) as areaconst
  from iptuconstr 
  inner join iptubase on j01_matric = j39_matric 
  where j39_matric = " . $fieldmatriculaSelecionada->j01_matric . "
  and j39_dtdemo is null 
  and j01_baixa is null";

    $resultareaconst = db_query($sqlareaconst);
    $linhasareaconst = pg_num_rows($resultareaconst);
    if ($linhasareaconst > 0) {
        $temareaconst = "";
        $areaconst = pg_result($resultareaconst, 0);
    } else {
        $temareaconst = "disabled";
        $areaconst = "Não Informado";
    }

    $sqlareaconsttotal = "
  select j34_totcon 
  from lote 
  where j34_setor = '" . $fieldmatriculaSelecionada->j34_setor . "' 
  and j34_quadra  = '" . $fieldmatriculaSelecionada->j34_quadra . "' 
  and j34_lote    = '" . $fieldmatriculaSelecionada->j34_lote . "'  limit 1";

    $resultareaconsttotal = db_query($sqlareaconsttotal);
    $linhasareaconsttotal = pg_num_rows($resultareaconsttotal);
    if ($linhasareaconsttotal > 0) {
        $temareaconsttotal = "";
        $areaconsttotal = pg_result($resultareaconsttotal, 0);
    } else {
        $temareaconsttotal = "disabled";
        $areaconsttotal = "Não Informado";
    }

    $sSqlHabite = "select case when obrashabite.ob09_codhab is null then j131_cadhab else cast(ob09_habite as varchar) end as j131_cadhab, to_char(j131_dthabite,'DD/MM/YYYY') as j131_dthabitebr, j131_dthabite from iptuconstrhabite inner join iptuconstr on iptuconstr.j39_matric = iptuconstrhabite.j131_matric and iptuconstr.j39_idcons = iptuconstrhabite.j131_idcons inner join ruas on ruas.j14_codigo = iptuconstr.j39_codigo inner join iptubase on iptubase.j01_matric = iptuconstr.j39_matric inner join db_usuarios on db_usuarios.id_usuario = iptuconstrhabite.j131_usuario left join protprocesso on trim(protprocesso.p58_codproc::text) = trim(iptuconstrhabite.j131_codprot::text) left join obrashabite on trim(obrashabite.ob09_codhab::text) = trim(iptuconstrhabite.j131_cadhab::text) where j131_matric = $k00_matric and j131_idcons = $j39_idcons order by j131_dthabite desc limit 1";

    $rsHabite = db_query($sSqlHabite);
    if (pg_num_rows($rsHabite) > 0) {
        $temhabite = "";
        $numhabite = pg_result($rsHabite, 0, 0);
        $dthabite = pg_result($rsHabite, 0, 1);
    } else {
        $temhabite = "disabled";
        $numhabite = "Não Informado";
        $dthabite = "Não Informado";
    }

    ?>

  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default">
<div class="container">

  <form name="form1" method="post" action="cad4_certidaoexistenciaconstrucao002.php">

    <fieldset>

      <legend>Emissão de Certidão de Construção</legend>
      <table class="form-container">

        <tr>
          <td>
            <strong>Matrícula:</strong>
          </td>
          <td>
              <?php
              db_input('k00_matric', 10, '', true, 'text', 3);
              db_input('z01_nome', 60, 0, true, 'text', 3);
              ?>
          </td>
        </tr>

        <tr>
          <td>
            <strong>Construção:</strong>
          </td>
          <td>
              <?php
              db_input('j39_idcons', 10, '', true, 'text', 3);
              ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="Processos registrado no sistema?">
            <strong>Processo do Sistema:</strong>
          </td>
          <td nowrap>
              <?php
              $lProcessoSistema = true;
              $aProcessoSistema = array(
                "0" => "SELECIONE...",
                "1" => "NÃO",
                "2" => "SIM"
              );
              ?>
            <select id='lProcessoSistema' name='lProcessoSistema' onchange='js_processoSistema();' style='width: 95px'>
              <option value="2">SELECIONE...</option>
              <option value="0">NÃO</option>
              <option value="1">SIM</option>
            </select>
          </td>
        </tr>

        <tr id="processoSistema" style="display: none;">
          <td nowrap title="<?php echo @$Tp58_codproc ?>">
            <strong>
                <?php
                db_ancora('Processo:', 'js_pesquisaProcesso(true)', 1);
                ?>
            </strong>
          </td>
          <td nowrap>
              <?php
              db_input('v01_processo', 10, false, true, 'text', 1, 'onchange="js_pesquisaProcesso(false)"');
              db_input('p58_requer', 60, false, true, 'text', 3);
              ?>
          </td>
        </tr>

        <tr id="processoExterno1" style="display: none;">
          <td nowrap title="Número do processo externo">
            <strong>Processo:</strong>
          </td>
          <td nowrap>
              <?php
              db_input('v01_processoExterno', 10, "", true, 'text', 1, null, null, null,
                "background-color: rgb(230, 228, 241);");
              ?>
          </td>
        </tr>

        <tr id="processoExterno2" style="display: none;">
          <td nowrap title="Número do processo externo">
            <strong>
              Titular do Processo:
            </strong>
          </td>
          <td nowrap>
              <?php
              db_input('v01_titular', 74, 'false', true, 'text', 1);
              ?>
          </td>
        </tr>

        <tr id="processoExterno3" style="display: none;">
          <td nowrap title="Número do processo externo">
            <strong>
              Data do Processo:
            </strong>
          </td>
          <td nowrap>
              <?php
              db_inputdata('v01_dtprocesso', @$v01_dtprocesso_dia, @$v01_dtprocesso_mes, @$v01_dtprocesso_ano, true,
                'text', 1);
              ?>
          </td>
        </tr>

        <tr>
          <td colspan="2">
            <fieldset>
              <legend>
                <strong>Observação</strong>
              </legend>
                <?php db_textarea('sObservacao', 5, 90, null, true, null, 1, null, null, null, 800) ?>

            </fieldset>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <fieldset>
              <legend><strong>Dados da Construção</strong></legend>
              <table align="left" width="90%" cellpadding="1" border="0">
                <tr>
                  <td>
                    <input type="checkbox" id="areadolote" value="<?php echo $areafrac ?>">
                    <label for="areadolote">Área do Lote: <?php echo $areafrac . " m2"; ?></label>
                  </td>
                  <td>
                    <input type="checkbox" id="arearealdolote"
                           value="<?php echo $areatotal ?>" <?php echo $temareatotal ?>>
                    <label for="arearealdolote">Área Real do Lote: <?php echo $areatotal . " m2"; ?></label>
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type="checkbox" id="areaconstruida"
                           value="<?php echo $areaconst ?>" <?php echo $temareaconst ?>>
                    <label for="areaconstruida">Área Construída: <?php echo $areaconst . " m2"; ?></label>
                  </td>
                  <td>
                    <input type="checkbox" id="arearealconstruida"
                           value="<?php echo $areaconsttotal ?>" <?php echo $temareaconsttotal ?>>
                    <label for="arearealconstruida">Área Real Construída: <?php echo $areaconsttotal . " m2"; ?></label>
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type="checkbox" id="numerodohabite"
                           value="<?php echo $numhabite ?>" <?php echo $temhabite ?>>
                    <label for="numerodohabite">Número do Habite-se: <?php echo $numhabite; ?></label>
                  </td>
                  <td>
                    <input type="checkbox" id="datadohabite" value="<?php echo $dthabite ?>" <?php echo $temhabite ?>>
                    <label for="datadohabite">Data do Habite-se: <?php echo $dthabite; ?></label>
                  </td>
              </table>
            </fieldset>
          </td>
        </tr>
      </table>
    </fieldset>
    <input name="gerar" id="gerar" type="button" value="Processar" onclick="js_gerarCertidao();">
    <input name="voltar" id="voltar" type="button" value="Voltar" onclick="js_volta();">
  </form>
</div>
<?php
db_menu();
?>
</body>
</html>
<script>

  /*
   * funcao responsavel pelo envio dos dados para gerar certidao
   */

  var sUrlRPC = "cad4_certidaoexistenciaconstrucao.RPC.php";
  let processo = null

  function js_gerarCertidao() {

    var iMatricula = $F('k00_matric');
    var lProcessoSistema = $F('lProcessoSistema');
    var iConstrucao = $F('j39_idcons');
    var sObservacao = $F('sObservacao').replace(/\n/g, "<quebralinha>");
    var iProcesso = '';
    var sTitular = '';
    var dtDataProcesso = '';
    var iAreaLote = $F('areadolote');
    var iAreaRealLote = $F('arearealdolote');
    var iAreaConstruida = $F('areaconstruida');
    var iAreaRealConst = $F('arearealconstruida');
    var iNumHabite = $F('numerodohabite');
    var iDatHabite = $F('datadohabite');
    var msgDiv = "Gerando Certidão \n Aguarde ...";
    var oParametros = new Object();

    if(lProcessoSistema == '1') {
      iProcesso = processo
    } else if(lProcessoSistema == '0') {

      iProcesso = $F('v01_processoExterno');
      sTitular = $F('v01_titular');
      dtDataProcesso = $F('v01_dtprocesso');
    }

    if((lProcessoSistema == '1' || lProcessoSistema == '0') && iProcesso == '') {

      alert("Selecione algum processo.\nCaso não queira processo vinculado, altere a opção 'processo do sistema'. ");
      return false;
    }

    js_divCarregando(msgDiv, 'msgBox');

    oParametros.exec = 'geraCertidao';
    oParametros.iMatricula = iMatricula;
    oParametros.lProcessoSistema = lProcessoSistema;
    oParametros.iConstrucao = iConstrucao;
    oParametros.sObservacao = sObservacao;
    oParametros.iProcesso = iProcesso;
    oParametros.sTitular = sTitular;
    oParametros.dtDataProcesso = dtDataProcesso;

    if(areadolote.checked) {
      oParametros.iAreaLote = iAreaLote;
    }
    if(arearealdolote.checked) {
      oParametros.iAreaRealLote = iAreaRealLote;
    }
    if(areaconstruida.checked) {
      oParametros.iAreaConstruida = iAreaConstruida;
    }
    if(arearealconstruida.checked) {
      oParametros.iAreaRealConst = iAreaRealConst;
    }
    if(numerodohabite.checked) {
      oParametros.iNumHabite = iNumHabite;
    }
    if(datadohabite.checked) {
      oParametros.iDatHabite = iDatHabite;
    }

    var oAjaxLista = new Ajax.Request(sUrlRPC,
      {
        method: "post",
        parameters: 'json=' + Object.toJSON(oParametros),
        onComplete: js_certidao
      });
  }

  function js_certidao(oAjax) {

    js_removeObj('msgBox');

    var oRetorno = JSON.parse(oAjax.responseText);

    if(oRetorno.iStatus == 1) {

      jan = window.open("cad4_certidaoexistencia003.php?iCodigoCertidao=" + oRetorno.iCodigoCertidao, '', 'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 ');
      jan.moveTo(0, 0)
    } else {

      alert(oRetorno.sMessage.urlDecode());
      return false;
    }
  }

  /*
   * FUNCOES DE PESQUISA
   */

  function js_pesquisaProcesso(lMostra) {

    if(lMostra) {
      js_OpenJanelaIframe('', 'db_iframe_matric', 'func_protprocesso_protocolo.php?funcao_js=parent.js_mostraProcesso|p58_numero|z01_nome|dl_codigo_do_processo', 'Pesquisa', true);
    } else {
      js_OpenJanelaIframe('', 'db_iframe_matric', 'func_protprocesso_protocolo.php?pesquisa_chave=' + document.form1.v01_processo.value + '&funcao_js=parent.js_mostraProcessoHidden', 'Pesquisa', false);
    }
  }

  function js_mostraProcesso(iCodProcesso, sRequerente, codigo_processo) {

    document.form1.v01_processo.value = iCodProcesso;
    document.form1.p58_requer.value = sRequerente;

    processo = codigo_processo;

    db_iframe_matric.hide();

  }

  function js_mostraProcessoHidden(iCodProcesso, sNome, lErro, p58_codproc) {

    if(lErro == true) {
      document.form1.v01_processo.value = "";
      document.form1.p58_requer.value = "";
    } else {
      document.form1.p58_requer.value = sNome;
      processo = p58_codproc
    }
  }

  /*
    funcao que trata se o processo é externo ou interno
  */

  function js_processoSistema() {


    var lProcessoSistema = $F('lProcessoSistema');

    if(lProcessoSistema == 1) {

      document.getElementById('processoExterno1').style.display = 'none';
      document.getElementById('processoExterno2').style.display = 'none';
      document.getElementById('processoExterno3').style.display = 'none';
      document.getElementById('processoSistema').style.display = '';
      $('v01_processo').value = "";
      $('p58_requer').value = "";
      $('v01_dtprocesso').value = "";

    } else if(lProcessoSistema == 0) {

      document.getElementById('processoExterno1').style.display = '';
      document.getElementById('processoExterno2').style.display = '';
      document.getElementById('processoExterno3').style.display = '';
      document.getElementById('processoSistema').style.display = 'none';

      $('v01_processo').value = "";
      $('v01_processoExterno').value = "";
      $('v01_titular').value = "";
      $('v01_dtprocesso').value = "";

    } else if(lProcessoSistema == 2) {

      document.getElementById('processoExterno1').style.display = 'none';
      document.getElementById('processoExterno2').style.display = 'none';
      document.getElementById('processoExterno3').style.display = 'none';
      document.getElementById('processoSistema').style.display = 'none';

      $('v01_processo').value = "";
      $('v01_processoExterno').value = "";
      $('v01_titular').value = "";
      $('v01_dtprocesso').value = "";

    }
  }


  function js_volta() {
    location.href = 'cad4_certidaoexistenciaconstrucao001.php ';
  }

</script>
