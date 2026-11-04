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

$proximoNumeroMaxLength = '';
if ($oProtParamGlobal->p06_tipo == ProcessoProtocoloNumeracao::TIPOORGAO) {
  $proximoNumeroMaxLength = '5';
}


?>
<form action="" name="form1" >
  <fieldset style="width: 280px;">
    <legend class='label'>Numero do Sequencial</legend>
    <table >
      <tr title="<?=@$Tp07_ano?>">
        <td class='label' nowrap >
          <?=@$Lp07_ano?>
          </td>
        <td class='info'>
          <?php
            db_input('p07_ano',15,$Ip07_ano,true,'text',$db_opcao,"");
            db_input('p07_sequencial',15,$Ip07_sequencial,true,'hidden',3,"");
            db_input('p07_instit',15,$Ip07_instit,true,'hidden',3,"");
          ?>
        </td>
      </tr>
      <tr title="<?=@$Tp07_proximonumero?>">
        <td class='label' nowrap>
          <?=@$Lp07_proximonumero?>
        </td>
        <td class='info'>
          <?php
            db_input('p07_proximonumero',15,$Ip07_proximonumero,true,'text',$db_opcao,'', '', '', '', $proximoNumeroMaxLength);
          ?>
        </td>
      </tr>
      <?php
        if ($oProtParamGlobal->p06_tipo == ProcessoProtocoloNumeracao::TIPOORGAO) {
      ?>
          <tr>
            <td nowrap title="<?php echo @$Tp07_prottipodocumentoprocesso ?>">
              <?php
                db_ancora(@$Lp07_prottipodocumentoprocesso, "js_pesquisa_tipo_documento_processo(true);", $db_opcao);
              ?>
            </td>
            <td nowrap>
              <?php
                db_input('p07_prottipodocumentoprocesso', 10, $p07_prottipodocumentoprocesso, false, 'text', 3 ," onchange='js_pesquisa_tipo_documento_processo(false);'");
                db_input('p91_descricao', 40, $Ip91_descr, true, 'text', 3, '');
              ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="<?php echo @$Tp07_orgao ?>">
              <?php echo @$Lp07_orgao ?>
            </td>
            <td>
              <?php
                $clorcunidade = new cl_orcunidade();
                $sWhere  = " o40_anousu     = ".db_getSession("DB_anousu");
                $sWhere .= " and o41_instit = " . db_getsession('DB_instit');

                $result = $clorcunidade->sql_record(
                  $clorcunidade->sql_query(null,null,null,"distinct o40_orgao,o40_descr","o40_descr",$sWhere)
                );

                if (@pg_numrows($result) == 0) {
                  echo "<strong>Sistema não localizou nenhum orgão com unidades vinculadas na instituição selecionada!</strong>";
                } else {
                  db_selectrecord("p07_orgao", @$result, true, $db_opcao);
                }
              ?>
            </td>
          </tr>
      <?php
        }
      ?>
    </table>
  </fieldset>
  <div align="center">
    <input id="botao" type="submit" name='opcao' value="<?=$sBotao?>" />
  </div>
  <br />
  <div align="center" style="display:table;">
    <?php
      $sAnoUso = db_getsession("DB_anousu");
      $sWhere = "p07_ano = " . $sAnoUso;
      $iOpcoes = 4;
      if ($iTipoParamGlobal == 2 ){

        $sInstit = db_getsession("DB_instit");
        $sWhere .= " and p07_instit = " . $sInstit;
        $iOpcoes = 2;

      }
      $cliframe_alterar_excluir = new cl_iframe_alterar_excluir;

      $chavepri = array("p07_sequencial"=>@$p07_sequencial);
      $cliframe_alterar_excluir->chavepri = $chavepri;
      $cliframe_alterar_excluir->campos = "p07_sequencial, p07_instit, p07_ano, p07_proximonumero";

      if ($oProtParamGlobal->p06_tipo == ProcessoProtocoloNumeracao::TIPOORGAO) {
        $cliframe_alterar_excluir->campos .= ", p91_descricao, o40_orgao, o40_descr";
        $sWhere .= ' AND p07_orgao != 0 AND p07_prottipodocumentoprocesso != 0 AND orcorgao.o40_anousu = '. db_getsession("DB_anousu");
      } else {
        $sWhere .= ' AND p07_orgao = 0 AND p07_prottipodocumentoprocesso = 0';
      }

      $cliframe_alterar_excluir->sql           = $clprotprocessonumeracao->sql_query_file(
        null,$cliframe_alterar_excluir->campos,"p07_sequencial"," {$sWhere}"
      );
      $cliframe_alterar_excluir->legenda       = "Numerações Cadastradas";
      $cliframe_alterar_excluir->msg_vazio     ="<font size='1'>Nenhum andamento Cadastrado!</font>";
      $cliframe_alterar_excluir->textocabec    ="darkblue";
      $cliframe_alterar_excluir->iframe_width  = $widthForm;
      $cliframe_alterar_excluir->textocorpo    ="black";
      $cliframe_alterar_excluir->fundocabec    ="#aacccc";
      $cliframe_alterar_excluir->fundocorpo    ="#ccddcc";
      $cliframe_alterar_excluir->iframe_height ="170";
      $cliframe_alterar_excluir->opcoes        = $iOpcoes;
      $cliframe_alterar_excluir->iframe_alterar_excluir(1);

    ?>

  </div>
</form>

<script type="text/javascript">

function js_pesquisa_tipo_documento_processo(mostra){
  var url = "func_prottipodocumentoprocesso.php";
  var parametros = "?funcao_js=parent.js_mostra_tipo_documento_processo";

  parametros += !mostra ? `&pesquisa_chave=${document.form1.p07_prottipodocumentoprocesso.value}` : '|0|1';

  js_OpenJanelaIframe("", "iframe_tipodocumento", url + parametros, "Pesquisa Tipo de Documento", mostra);
}

function js_mostra_tipo_documento_processo(chave1, chave2) {
  $('p07_prottipodocumentoprocesso').value = chave1;
  $('p91_descricao').value = chave2;
  iframe_tipodocumento.hide();
}

/*
function comparaValor(proximoNumero){
  var numeroAntigo = <?=@$p07_proximonumero?>;

  if (numeroAntigo > proximoNumero.value) {
    alert("Próximo número não pode ser menor do que o número do ultimo protocolo cadastrado: "+numeroAntigo);
    proximoNumero.value = numeroAntigo;
  }

}
*/
</script>
