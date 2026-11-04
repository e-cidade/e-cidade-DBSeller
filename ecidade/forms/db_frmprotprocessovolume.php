<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2016  DBSeller Servicos de Informatica
 *                    www.dbseller.com.br
 *                 e-cidade@dbseller.com.br
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

use Mpdf\Tag\Strong;
require_once(modification("libs/db_conecta.php"));
include(modification("classes/db_cgm_classe.php"));


//MODULO: protocolo
$clcgm = new cl_cgm;
$clrotulo = new rotulocampo;
$clprotpro = new cl_protprocesso;

$clprotprocesso->rotulo->label();
$clrotulo->label("p51_descr");
$clrotulo->label("z01_nome");
$clrotulo->label("descrdepto");

$sInstit = db_getsession("DB_instit");
$sDbIdUsuario = db_getsession("DB_id_usuario");

$result_param = $clprotparam->sql_record($clprotparam->sql_query_file(null,"*",null,"p90_instit=".$sInstit));
if ($clprotparam->numrows > 0){
  db_fieldsmemory($result_param,0);
} else {
  $p90_emiterecib='f';
  $p90_alteracgmprot=='f';
}

$sqlDepartamento = "
  SELECT
    descrdepto,
    o40_descr,
    o40_orgao
  FROM
    db_depart
  LEFT JOIN db_departorg
    ON db_departorg.db01_coddepto = db_depart.coddepto
  LEFT JOIN orcorgao
    ON db_departorg.db01_orgao = orcorgao.o40_orgao
  WHERE
    coddepto = " . db_getsession("DB_coddepto") ."
    AND o40_anousu = " . db_getsession("DB_anousu") . "
    AND db01_anousu = " . db_getsession("DB_anousu")
;

$postgresObjectDepartamento = db_query($sqlDepartamento);

if (pg_num_rows($postgresObjectDepartamento) > 0) {
  $resultado = pg_fetch_assoc($postgresObjectDepartamento);
  $departamento = $resultado['descrdepto'];
  $orgao = $resultado['o40_orgao'] . ' - ' . $resultado['o40_descr'];
  $idOrgao = $resultado['o40_orgao'];
}

if ($p58_codigo) {
  $cltipoproc = new cl_tipoproc();
  $sqlTipoDocumento = $cltipoproc->sql_query($p58_codigo, 'p91_sequencial, p91_descricao');

  db_fieldsmemory($cltipoproc->sql_record($sqlTipoDocumento), 0);
}

?>

<fieldset>
  <legend><b>Dados Processo</b></legend>

  <table border="0">
    <tr>
      <td nowrap title="Usuário">
        <b>Usuário:</b>
      </td>
      <td>
      <?php
        $sql = "select nome from db_usuarios where id_usuario = ".$sDbIdUsuario;
        echo pg_result(db_query($sql),0,"nome");
      ?>
      </td>
    </tr>
    <tr>
      <td nowrap title="Departamento">
        <b>Departamento:</b>
      </td>
      <td>
          <?php
            echo $departamento;
          ?>
      </td>
    </tr>
    <tr>
      <td nowrap title="Órgão">
        <b>Órgão:</b>
      </td>
      <td>
          <?php
            echo $orgao;
          ?>
          <input type="hidden" id="p58_orgao" name="p58_orgao" value="<?php echo $Ip58_orgao ?>" />
      </td>
    </tr>
    <tr>
      <td nowrap title="<?=@$Tp58_processopai?>">
        <?php
            if ($_GET['alteracao'] == '1' || !empty($p58_codproc)) {
              echo '<strong>'. @$Lp58_processopai .'</strong>';
            } else {
              db_ancora(@$Lp58_processopai, "js_pesquisa_processopai(true)", $db_opcao);
            }
        ?>
      </td>
      <td>
          <?php
            db_input(
              'numeracaoprocessopai',
              20,
              $numeracaoprocessopai,
              true,
              'text',
              3,
              ""
            );
          ?>
          <input type="hidden" id="p58_processopai" name="p58_processopai" value="<?php echo $Ip58_processopai ?>" />
      </td>
    </tr>
    <tr>
      <td nowrap title="<?=@$Tp58_codproc?>">
        <?=@$Lp58_codproc; ?>
      </td>
      <td>
      <?php
        db_input('p58_codproc',20,$Ip58_codproc,true,'text',3,"");
      ?>
    </td>
  </tr>
  <?php
    if ($p58_codproc) {
      $numeracao = "{$p58_numero}/{$p58_ano}";
  ?>
      <tr>
          <td nowrap title="<?=@$Tp58_numero?>">
            <?=@$Lp58_numero; ?>
          </td>
          <td>
          <?php
            db_input('p58_numero', 20, $numeracao, true, 'text',3,"");
          ?>
        </td>
      </tr>
  <?php
    }?>
    <tr>
      <td nowrap title="<?=@$Tp58_dtproc;?>">
      <?=@$Lp58_dtproc;?>
      </td>
      <td>
    <?php
        db_inputdata('p58_dtproc',@$p58_dtproc_dia,@$p58_dtproc_mes,@$p58_dtproc_ano,false,'text',3,"","p58_dtproc");
    ?>
    </td>
    </tr>
    <tr>
      <td nowrap title="<?=@$Tp58_hora;?>">
      <?=@$Lp58_hora;?>
      </td>
      <td>
    <?php
    if($db_opcao == 1){
      $p58_hora = db_hora();
      db_input('p58_hora',10,@$Ip58_hora,true,'text','3','');
    }else
        db_input('p58_hora',10,$Ip58_hora,true,'text',3);
    ?>
    </td>
    </tr>
    <tr>
      <td nowrap title="<?php echo @$Tp58_prottipodocumentoprocesso ?>">
        <strong>Tipo de Documento</strong>
      </td>
      <td nowrap>
        <?php
          db_input('p91_sequencial', 10, $p91_sequencial, true, 'text', 3);
          db_input('p91_descricao', 40, $Ip91_descr, true, 'text', 3, '');
        ?>
      </td>
    </tr>
  <?php
    $op_tip = 1;

    if($db_opcao==2){
      $op_tip = 2;

      if(isset($p58_codproc) && trim($p58_codproc)!=""){
        $sql_tipo = " select p61_codproc as processo1,
                            p63_codproc as processo2,
                            p67_codproc as processo3
                        from protprocesso
                            left join procandam        on procandam.p61_codproc        = protprocesso.p58_codproc
                            left join proctransferproc on proctransferproc.p63_codproc = protprocesso.p58_codproc
                            left join procarquiv       on procarquiv.p67_codproc       = protprocesso.p58_codproc
                      where protprocesso.p58_codproc=$p58_codproc
                        and procandam.p61_codproc is null
                        and proctransferproc.p63_codproc is null
                        and procarquiv.p67_codproc is null";
        $result_tipo = $clprotpro->sql_record($sql_tipo);

        if($clprotpro->numrows==0){
          $op_tip = 3;
        }
      }
    }else if($db_opcao==3){
      $op_tip = 3;
    }
  ?>
    <tr>
      <td nowrap title="<?=@$Tp58_codigo?>">
        <?php
          echo '<span style="display:inline-block;">'. @$Lp58_codigo . '</span>';
        ?>
      </td>
      <td>
        <?php
          db_input('p58_codigo', 10, $Ip58_codigo, true, 'text');
        ?>
        <?php
          db_input('p51_descr', 40, $Ip51_descr, true, 'text', 3, '');
          if($db_opcao == 1){
            $p58_hora = db_hora();
            db_input('p58_hora',60,@$Ip58_hora,true,'hidden','','');
          }
        ?>
      </td>
    </tr>
    <tr>
      <td nowrap title="<?=@$Tp58_numcgm?>">
        <?php
        db_ancora(@$Lp58_numcgm,"js_pesquisap58_numcgm(true);",$db_opcao);
        ?>
      </td>
      <td nowrap>
        <?php
          $msg_debito="";
          if (isset($p58_numcgm)&&$p58_numcgm != ""){
            $result_param = $clprotparam->sql_record($clprotparam->sql_query_file());

            if ($clprotparam->numrows>0){
              db_fieldsmemory($result_param,0);

              if (@$p90_debiaber=='t'){
                $data_atual = date('Y-m-d', db_getsession("DB_datausu"));
                $sWhere = "arrenumcgm.k00_numcgm = {$p58_numcgm} and k00_dtvenc < '{$data_atual}' ";
                $result_debito = $clarrenumcgm->sql_record($clarrenumcgm->sql_query_deb(null,null,"z01_nome",null,$sWhere));

                if ($clarrenumcgm->numrows>0){
                  db_fieldsmemory($result_debito,0);
                  $msg_debito="Contribuinte com debito(s) em aberto!!";
                }
              }
          }
          }
          db_input('p58_numcgm',10,$Ip58_numcgm,true,'text',3," onchange='js_pesquisap58_numcgm(false);'");
          db_input('z01_nome',40,$Iz01_nome,true,'text',3,'');
        ?>
        <input name="Alterar CGM" type="button" id="alterarcgm" value="Alterar CGM"
              onclick="js_AlteraCGM(document.form1.p58_numcgm.value);" <?=($db_botao == false ? "disabled" : "")?>>

    </td>
  </tr>
  <tr>
    <td>
    </td>
    <td >
      <font color='red'>
        <b>
          <?php echo @$msg_debito?>
        </b>
      </font>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tp58_requer?>">
      <?php echo @$Lp58_requer?>
    </td>
    <td>
      <?php
        db_input('p58_requer',54,$Ip58_requer,true,'text',$db_opcao,"");
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=$Tp58_obs?>" colspan='2'>
      <fieldset class="separator">
        <legend>Assunto:</legend>
        <?php db_textarea('p58_obs',10, 80, $Ip58_obs,true,'text',$db_opcao,""); ?>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td colspan=2>
      <fieldset>
        <table>
          <tr>
            <td>
              <b>CAMPOS COMPLEMENTARES</b>
            </td>
          </tr>
          <tr>
            <td id="campos_complementares"></td>
          </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="3" id="tipo_processo_documentos" valign='top'></td>
  </tr>
</table>

<?php
  db_input('docs', 50, $Ip58_codproc, true, 'hidden', 3, "");
  db_input('ndocs', 50, $Ip58_codproc, true, 'hidden', 3, "");
  db_input('alterou', 10, '', true, 'hidden', 3);
  db_input('p90_emiterecib', 10, $p90_emiterecib, true, 'hidden', 3);
  db_input('p90_alteracgmprot', 10, $p90_alteracgmprot, true, 'hidden', 3);
  //alterado pro Robson
  db_input('btnincluir', 10, "", true, 'hidden', 3);
?>

<?php
  $sValue  = "Incluir";
  $disabled = 'disabled';

  if (!empty($_GET['alteracao']) || !empty($p58_codproc)) {
    $sValue = "Alterar";
    $disabled = '';
  }
?>

<input name="salvar" type="button" id="salvar" value="<?php echo $sValue?>" />

<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisadpto();"
      <?php echo $disabled ?> >

<input type="button" id="btnAnexarDocumento" value="Anexar Documento" />

</fieldset>
<script>
var arquivoRPC = 'pro4_protprocessovolume.RPC.php';
var flagAlteracao = '<?php echo $_GET['alteracao'] ?>';

<?php
  if (isset($p58_ano)) {
      echo "document.form1.p58_numero.value = '" . $p58_numero . "/" . $p58_ano . "'";
  }
?>

function js_valor(){
    var cods  = '';
    var ncods = '';
    var iTam  = document.form1.length;

    for(i = 0; i < iTam ; i++){
        if(document.form1.elements[i].type == "checkbox"){
            if(document.form1.elements[i].checked == true){
                cods += document.form1.elements[i].value + "#";
            } else {
                ncods += document.form1.elements[i].value + '#';
            }
        }
    }

    $('docs').value  = cods;
    $('ndocs').value = ncods;
}

function js_testa(opcao) {
  var passa = true;
  $('btnincluir').value = opcao;

  if (opcao == 1) {
    if ($('p90_alteracgmprot').value == 't') {
      if ($('alterou').value != '1') {
	       passa = false;
      }
    }
  }

  if (!passa) {
    alert('Atualize o cgm do contribuinte!');
    js_AlteraCGM($('p58_numcgm').value);

    return false;
  }

  if ( !js_validaObservacao() ) {
    return false;
  }

  return passa;
}

$('salvar').addEventListener('click', function(){
    if ($('p58_processopai').value == '') {
        alert('Processo Principal não informado.');
        return;
    }

    if ($('p58_numcgm').value == '') {
        alert('Titular não informado.');
        return;
    }

    // alteração -> 2 / inclusão -> 1
    var opcao = flagAlteracao == '1' ? 2 : 1;
    if (!js_testa(opcao)) {
      return;
    }

    var acao = 'incluído';
    if (opcao == 2) {
      acao = 'alterado';
    }

    var parametros = {
        exec: 'salvar',
        p58_codproc: $('p58_codproc').value,
        p58_processopai: $('p58_processopai').value,
        p58_codigo: $('p58_codigo').value,
        p58_numcgm: $('p58_numcgm').value,
        p58_requer: $('p58_requer').value,
        p58_obs: $('p58_obs').value,
        p58_orgao: $('p58_orgao').value,
        p91_sequencial: $('p91_sequencial').value,
        numeracaoprocessopai: $('numeracaoprocessopai').value,
        p58_requer: $('p58_requer').value,
        ndocs: $('ndocs').value,
        docs: $('docs').value
    };

    new AjaxRequest(
        arquivoRPC,
        parametros,
        function(retorno, erro) {
            if (retorno.erro) {
                alert(retorno.message);
                return;
            }

            alert(`Volume ${retorno.p58_numero} ${acao} com sucesso.`);
            window.open(`pro4_capaprocesso.php?codproc=${retorno.p58_codproc}`, '', 'location=0');
            if (retorno.p90_emiterecib == 't') {
                if (confirm('Deseja Emitir Recibo?')) {
                    location.href=`cai4_recibo001.php?p58_codproc=${retorno.p58_codproc}&codtipo=${retorno.p58_codigo}&incproc=true&mostramenu=true&sIframe=iframe_dadosprocesso`;
                } else {
                    location.href=`pro4_protprocessovolume.php?p58_codproc=${retorno.p58_codproc}&alteracao=1`;
                }
            } else {
              location.href=`pro4_protprocessovolume.php?p58_codproc=${retorno.p58_codproc}&alteracao=1`;
            }
        }
    ).execute();
});

function js_pesquisa_processopai() {
    var sUrl = `func_protprocesso_protocolo.php?p58_numero=${$('numeracaoprocessopai').value}&apenas_processopai=0&funcao_js=parent.js_mostra_processopai|0|1`;
    js_OpenJanelaIframe("", "iframe_processopai", sUrl, "Pesquisa Processo Principal ", true);
}

function buscaDocumentosProcesso(codproc, tipoproc) {
  new AjaxRequest(
      arquivoRPC,
      {
          exec: 'buscaDocumentosProcesso',
          p58_codproc: codproc,
          p58_codigo: tipoproc
      },
      function(retorno, erro) {
          if (retorno.erro) {
              alert(retorno.message.urlDecode());
              return;
          }

          if (retorno.html) {
              $('tipo_processo_documentos').innerHTML = retorno.html;
              $('docs').value = retorno.docs;
              $('ndocs').value = retorno.ndocs;
          }
      }
  ).execute();
}

function js_mostra_processopai(codproc, numeracao) {
    $('p58_processopai').value = codproc;
    $('numeracaoprocessopai').value = numeracao;

    if (codproc) {
        new AjaxRequest(
            arquivoRPC,
            {
                exec: 'tipoProcesso',
                p58_processopai: codproc,
                p58_codproc: $('p58_codproc').value
            },
            function(retorno, erro) {
                if (retorno.erro) {
                    $('p58_codigo').value = '';
                    $('p91_sequencial').value = '';
                    $('p51_descr').value = '';
                    $('p91_descricao').value = '';
                    $('p58_orgao').value = '';
                    alert(retorno.message.urlDecode());
                    return;
                }

                $('p58_codigo').value = retorno.p58_codigo;
                $('p91_sequencial').value = retorno.p91_sequencial;
                $('p51_descr').value = retorno.p51_descr;
                $('p91_descricao').value = retorno.p91_descricao;
                $('p58_orgao').value = retorno.p58_orgao;

                buscaDocumentosProcesso($('p58_codproc').value, retorno.p58_codigo);
            }
        ).execute();
    }

    iframe_processopai.hide();
    js_pesquisap58_numcgm(true);
}

function js_AlteraCGM(cgm) {
    var sUrl = "prot1_cadcgm002.php?chavepesquisa="+cgm+"&testanome=true&autoprot=true";
    js_OpenJanelaIframe("", "iframe_tipo", sUrl, "Pesquisa ", true);
}

function js_pesquisap58_numcgm(mostra){
    var permissao_cancelar = <?=db_permissaomenu(db_getsession("DB_anousu"), 604, 1306)?>;
    if (permissao_cancelar == false) {
        <?php
            if($p90_alteracgmprot=='t') {
                echo "alert('AVISO:\\nUsuário sem permissão para alterar CGM !\\nCadastro de processo não será efetuado!');";
                echo "document.form1.alterarcgm.disabled = true;";
                echo "return false;";
            }
        ?>
    }

    var url = "func_nome.php";
    var parametros = "?funcao_js=parent.js_mostracgm1|0|1&testanome=true&incproc=true";

    if(mostra === false) {
        parametros = "?pesquisa_chave="+document.form1.p58_numcgm.value+"&funcao_js=parent.js_mostracgm";
    }

    js_OpenJanelaIframe("", "iframe_cgm", url + parametros, "Pesquisa CGM", mostra);
}

function js_mostracgm(erro,chave){
    document.form1.z01_nome.value = chave;
    document.form1.p58_requer.value = chave2;

    if(erro == true){
        document.form1.p58_numcgm.focus();
        document.form1.p58_numcgm.value = '';
    } else {
        document.form1.submit();
    }
}

function js_mostracgm1(chave1,chave2){
    document.form1.p58_numcgm.value = chave1;
    document.form1.z01_nome.value = chave2;
    document.form1.p58_requer.value = chave2;
    iframe_cgm.hide();
}

function js_pesquisap58_coddepto(mostra){
    var url = "func_db_depart.php";
    var parametros = "?funcao_js=parent.js_mostradb_depart1|0|z01_nome";

    if(mostra === false) {
        parametros = "?pesquisa_chave="+document.form1.p58_coddepto.value+"&funcao_js=parent.js_mostradb_depart";
    }

    js_OpenJanelaIframe("", "iframe_departamento", url + parametros, "Pesquisa Departamento", mostra);
}

function js_mostradb_depart(chave,erro){
    document.form1.descrdepto.value = chave;

    if(erro==true){
        document.form1.p58_coddepto.focus();
        document.form1.p58_coddepto.value = '';
    }
}

function js_mostradb_depart1(chave1,chave2){
    document.form1.p58_coddepto.value = chave1;
    document.form1.descrdepto.value = chave2;
    iframe_departamento.hide();
}

function js_pesquisadpto() {
    var sUrl = "func_protprocessodeptoatual.php?apenasvolumes=1&grupo=1&funcao_js=parent.js_preenchepesquisa|0";
    js_OpenJanelaIframe("", "db_iframe", sUrl, "Pesquisa ", true);
}

function js_preenchepesquisa(p58_codproc) {
    db_iframe.hide();
    location.href = '<?php echo basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])?>'+"?p58_codproc=" + p58_codproc;
}

$("btnAnexarDocumento").observe("click", function () {
    if ($F("p58_codproc").trim() == "") {
        alert("Número do processo não informado."); return false;
    }
    js_OpenJanelaIframe("", "iframe_processo_documento", "prot4_processodocumento001.php?volumes=1&iCodigoProcesso="+$F("p58_codproc"), "Anexar Documento", true);
});


function js_validaObservacao() {
    var sMensagem = 'Aviso:\n Você informou no campo observação mais de 500 caracteres, pode ser que na capa de processo não conste todas informações.\n';
    sMensagem    += 'Deseja salvar assim mesmo?';

    if ($F('p58_obs').length > 500 && !confirm(sMensagem) ) {
        return false;
    }

    return true;
}

function js_pesquisa_processo() {
  const url = "func_protprocessodeptoatual.php?apenasvolumes=1&grupo=1&funcao_js=parent.js_preenche_pesquisa_processo|0|1";
  js_OpenJanelaIframe("", "db_iframeprocesso", url, "Pesquisa ", true);
}

function js_preenche_pesquisa_processo(p58_codproc, cgm) {
  db_iframeprocesso.hide();
  location.href = 'pro4_protprocessovolume.php?alteracao=1&p58_codproc=' + p58_codproc + '&p58_numcgm=' + cgm;
}

<?php
  if (!empty($_GET['alteracao']) && empty($p58_codproc)) {
    echo 'js_pesquisa_processo()';
  } else if (!empty($p58_codproc)) {
    echo "buscaDocumentosProcesso({$p58_codproc}, {$p58_codigo})";
  }
?>
</script>
