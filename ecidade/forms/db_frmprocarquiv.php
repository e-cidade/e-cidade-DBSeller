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

require_once('model/protocolo/ProcessoProtocoloNumeracao.model.php');


//MODULO: protocolo
$clprocarquiv->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("p58_requer");

if (!isset($lMesmoUsuario)) {
  $lMesmoUsuario = true;
}

$db_opcaoHistorico = $db_opcao;
if (!$lMesmoUsuario) {
  $db_opcaoHistorico = 3;
}
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td>
      <fieldset>
        <legend><b>Dados do Arquivamento</legend>
        <table>
					<tr>
        		<td><b>Processo:</b></td>
        		<td>
        			<?php
        				db_input('p58_codproc', 25, false, true, 'hidden', 3);
        				db_input('p58_numero', 25, false, true, 'text', 3);
        			?>
        		</td>
        	</tr>
					<tr>
        		<td><b>Requerente:</b></td>
        		<td>
        			<?php
        				db_input('p58_requer', 50, false, true, 'text', 3);
        			?>
        		</td>
        	</tr>
          <tr>
            <td nowrap title="Usuário">
              <b>Usuário:</b>
            </td>
            <td>
             <?php
             $iUsuario = db_getsession("DB_id_usuario");
             if ($db_opcao == 2 && isset($p67_id_usuario)) {
               $iUsuario = $p67_id_usuario;
             }

             $rsUsuario = db_query("select nome from db_usuarios where id_usuario = {$iUsuario} ");
             if ($rsUsuario != false && pg_num_rows($rsUsuario) > 0) {
               echo pg_result($rsUsuario, 0, "nome");
             }
             ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="Departamento">
              <b>Departamento:</b>
            </td>
            <td>
             <?php
             $iDepartamento = db_getsession("DB_coddepto");
             if ($db_opcao == 2 && isset($te)) {
               $iDepartamento = $p67_coddepto;
             }

             $rsDepartamento = db_query("select descrdepto from db_depart where coddepto = {$iDepartamento} ");
             if ($rsDepartamento != false && pg_num_rows($rsDepartamento) > 0) {
               echo pg_result($rsDepartamento, 0, "descrdepto");
             }
             ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="<?=@$Tp67_codarquiv?>">
               <?=@$Lp67_codarquiv?>
            </td>
            <td>
              <?php
              db_input('p67_codarquiv',10,$Ip67_codarquiv,true,'text',3,"")
              ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="<?=@$Tp67_dtarq?>">
               <?=@$Lp67_dtarq?>
            </td>
            <td>
              <?php
              if (empty($y30_data_dia)) {

                $p67_dtarq_dia = date("d",db_getsession("DB_datausu"));
                $p67_dtarq_mes = date("m",db_getsession("DB_datausu"));
                $p67_dtarq_ano = date("Y",db_getsession("DB_datausu"));
              }
              db_inputdata('p67_dtarq', @$p67_dtarq_dia, @$p67_dtarq_mes, @$p67_dtarq_ano, true, 'text', $db_opcaoHistorico, "");
              ?>
            </td>
          </tr>
          <?php
            if (ProcessoProtocoloNumeracao::getTipoConfiguracao() == ProcessoProtocoloNumeracao::TIPOORGAO && $db_opcao != 2) { ?>
              <tr>
                <td>
                  <b>Arquivar Volumes:</b>
                </td>
                <td>
                  <input type="checkbox" id="arquivar_volumes" onchange="if (this.checked) { buscarVolumes(); }" />
                </td>
              </tr>
          <?php
              } ?>
            <tr>
              <td nowrap title="<?=@$Tp67_historico?>" colspan="2">
                <fieldset>
                  <legend><b><?=@$Lp67_historico?>:</b></legend>
                  <?php
                    db_textarea('p67_historico', 6, 65, $Ip67_historico, true, 'text', $db_opcaoHistorico, "");
                  ?>
                </fieldset>
              </td>
            </tr>
        </table>
      </fieldset>
    </td>
  </tr>
  <?php
    if (ProcessoProtocoloNumeracao::getTipoConfiguracao() == ProcessoProtocoloNumeracao::TIPOORGAO && $db_opcao != 2) { ?>
      <tr id="tr_volumes">
        <td>
          <fieldset>
          <legend><b>Volumes</legend>
            <table style="width:100%;">
              <tr>
                <td>
                  <div id="grid_volumes"></div>
                </td>
              </tr>
            </table>
          </fieldset>
        </td>
      </tr>
  <?php
    }?>
</table>
<br/>
<div id="divButtons">
<input type="hidden" id="grupo" name="grupo" value="<?=$grupo?>">
<input name="db_opcao" type="submit" id="db_opcao"
       value="<?=($db_opcao==1?"Incluir":($db_opcao==2?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="btnPesquisaProcessoOuvidoria" type="button" id="btnPesquisaProcessoOuvidoria"
       value="Pesquisar" onclick="js_pesquisaProcessoOuvidoria(true);" >
<input name="pesquisar" type="button" id="pesquisar"
       value="Pesquisar Arquivamento" onclick="js_pesquisa();" >
</div>
</center>
</form>
<script>
<?php

  if (isset($grupo) && $grupo == 1 ) {

    echo "function js_pesquisap67_codproc(mostra){
					  if(mostra==true){
					    db_iframe.jan.location.href = 'func_protprocessoarquiv.php?grupo=1&atend=false&funcao_js=parent.js_mostraprotprocesso1|0|1';
					    db_iframe.mostraMsg();
					    db_iframe.show();
					    db_iframe.focus();
					  }else{
					    db_iframe.jan.location.href = 'func_protprocessoarquiv.php?grupo=1&atend=false&pesquisa_chave='+document.form1.p67_codproc.value+'&funcao_js=parent.js_mostraprotprocesso';
					  }
					}";
  } else {
    echo "function js_pesquisap67_codproc(mostra){
            if(mostra==true){
              db_iframe.jan.location.href = 'func_protprocessoarquivouvidoria.php?funcao_js=parent.js_mostraprotprocesso1|0|1';
              db_iframe.mostraMsg();
              db_iframe.show();
              db_iframe.focus();
            }else{
              db_iframe.jan.location.href = 'func_protprocessoarquivouvidoria.php?pesquisa_chave='+document.form1.p67_codproc.value+'&funcao_js=parent.js_mostraprotprocesso';
            }
          }";
  }

?>

<?php
  if (
    $db_opcao != 2
    && ProcessoProtocoloNumeracao::getTipoConfiguracao() == ProcessoProtocoloNumeracao::TIPOORGAO
  ) { ?>
    const containerVolumes = document.getElementById('grid_volumes');
    const collectionVolumes = new Collection().setId('sequencial');
    const gridVolumes = DatagridCollection.create(collectionVolumes).configure({'order': false, height: 150});

    gridVolumes.addColumn('checkbox', {label: 'Selecione', align: 'center', width: '15%'});
    gridVolumes.addColumn('sequencial', {label: 'Código de Controle', align: 'center', width: '25%'});
    gridVolumes.addColumn('numero', {label: 'Número Processo', align: 'center', width: '45%'});
    gridVolumes.addColumn('volume', {label: 'Volume', align: 'center', width: '15%'});

    const adicionaVolumeCollection = (volume) => {
      collectionVolumes.add({
        checkbox: `<input type="checkbox" id="volume_${volume.p58_codproc}" name="volume[${volume.p58_codproc}]" value="${volume.p58_codproc}" />`,
        sequencial: volume.p58_codproc,
        numero: volume.p58_numero,
        volume: volume.p58_volume
      });
    };

    gridVolumes.show(containerVolumes);

    function buscarVolumes()
    {
      var parametros = {
        exec: 'buscarVolumes',
        codigoProcesso: $('p58_codproc').value,
        orgao: true,
        arquivamento: true
      };

      new AjaxRequest(
        'pro4_protprocessovolume.RPC.php',
        parametros,
        function(retorno, erro) {
          if (retorno.erro) {
            alert(retorno.message);
            return;
          }

          retorno.volumes.map(
            volume => adicionaVolumeCollection(volume)
          );
          gridVolumes.reload();

        }
      ).execute();
    }
<?php
  }?>

const url = new URLSearchParams(window.location.search);
const referrer = url.get('referrer');
if (referrer === "pro3_consultaprocesso002.php") {
    const data = document.getElementById("p67_dtarq");
    const historico = document.getElementById("p67_historico");
    data.setAttribute("readonly", true);
    data.style.backgroundColor = "#DEB887";
    document.getElementById("dtjs_p67_dtarq").hidden = true;
    historico.setAttribute("readonly", true);
    historico.style.backgroundColor = "#DEB887";
    document.getElementById("divButtons").hidden = true;
}

function js_pesquisaProcessoOuvidoria(lMostra) {
  var iGrupo = $('grupo').value;
  var sUrlOpenProcessoOuvidoria = '';
  var sCamposRetorno = '|p58_codproc|p58_requer';

  if(iGrupo == 1) {

    sUrlOpenProcessoOuvidoria = "func_protprocessoarquiv.php?";
    sCamposRetorno += '|p58_numero';

  } else {
    sUrlOpenProcessoOuvidoria = "func_protprocessoarquivouvidoria.php?";
  }

	if (lMostra) {
	  sUrlOpenProcessoOuvidoria = sUrlOpenProcessoOuvidoria+"funcao_js=parent.js_preenchePesquisa" + sCamposRetorno;
	}
  js_OpenJanelaIframe('', 'db_iframe_protprocessoarquivouvidoria', sUrlOpenProcessoOuvidoria, "Pesquisa Processo de Ouvidoria", lMostra);
}

function js_preenchePesquisa(iProcesso, sRequerente, iNumero) {

  if ($('arquivar_volumes')) {
    $('arquivar_volumes').checked = false;
  }

  if (typeof collectionVolumes !== 'undefined') {
    collectionVolumes.clear();
    gridVolumes.reload();
  }

  $('p58_codproc').value = iProcesso;
  $('p58_requer').value  = sRequerente;
  $('p58_numero').value  = iNumero;

  /**
   * quando rotina for acessada pelo grupo != 1, ouvidoria, remove campo p58_numero e exibe o campo p58_codproc
   */
  if (!iNumero) {

    $('p58_codproc').type = 'text';
    $('p58_numero').type = 'hidden';
    $('p58_numero').remove();
  }
  db_iframe_protprocessoarquivouvidoria.hide();
}

function js_pesquisa(){
  db_iframe.jan.location.href = 'func_procarquiv.php?funcao_js=parent.js_preenchepesquisa|p67_codarquiv ';
  db_iframe.mostraMsg();
  db_iframe.show();
  db_iframe.focus();
}
function js_preenchepesquisa(chave){
  db_iframe.hide();
  location.href = '<?=basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])?>'+"?chavepesquisa="+chave;
}
js_pesquisaProcessoOuvidoria(true);
</script>
<?php
$func_iframe = new janela('db_iframe','');
$func_iframe->posX=1;
$func_iframe->posY=20;
$func_iframe->largura=780;
$func_iframe->altura=430;
$func_iframe->titulo='Pesquisa';
$func_iframe->iniciarVisivel = false;
$func_iframe->mostrar();
