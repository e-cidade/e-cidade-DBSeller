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

//MODULO: educação
use App\Domain\Configuracao\Helpers\StorageHelper;

$oDaoAluno->rotulo->label();
$oClRotulo = new rotulocampo;
$iEscola   = db_getsession("DB_coddepto");

if ($db_opcao != 1 && $chavepesquisa != "") {
    $sql     = "SELECT ed56_i_escola as cod_escola FROM alunocurso WHERE ed56_i_aluno = $chavepesquisa";
    $query   = db_query($sql);
    $linhas4 = pg_num_rows($query);

    if ($linhas4 == 0) {
        $db_botao = true;
    } elseif ($iEscola != pg_fetch_result($query, 0, 0)) {
        $db_botao = false;
    } else {
        $db_botao = true;
    }
}

if ($ed47_i_nacion == 3) {
    $db_opcao1 = 3;
} else {
    $db_opcao1 = 1;
}

?>
<form id="frmDocumentacaoAluno" name="form1" method="post" action="">
<div class="form-container">
  <table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr valign="top">
      <td align="center">
        <?php db_ancora(@$Led47_i_codigo, "", 3);?>
        <?php db_input('ed47_i_codigo', 20, $Ied47_i_codigo, true, 'text', 3, "")?>
        <?php db_input('ed47_v_nome', 40, $Ied47_v_nome, true, 'text', 3, '')?>
        <?=@$Led47_c_codigoinep?>
        <?php db_input('ed47_c_codigoinep', 12, $Ied47_c_codigoinep, true, 'text', 3, '')?>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset><legend><b>Certidão</b></legend>
          <table border="0" id="tblMatricula" name="tblMatricula" cellspacing="0" cellpadding="0" width="100%">
            <tr>
              <td>
                <?=@$Led47_certidaomatricula?>
              </td>
              <td>
                <input type="text" id="matri_cartorio" name="matri_cartorio" tabindex="1"
                       onchange="buscaCartorioMatricula();" onkeyup="mudaFocoCampoMatricula(this, 6, event);"
                       onkeypress="return validaSomenteNumero(event);"
                       size="6" maxlength="6" title="Cartório" />
                <input type="text" id="matri_tipoacervo" name="matri_tipoacervo" tabindex="2"
                       onchange="validaTpAcervoMatricula();" onkeyup="mudaFocoCampoMatricula(this, 2, event);"
                       onkeypress="return validaSomenteNumero(event);"
                       size="2" maxlength="2" title="Tipo do Acervo" />
                <input type="text" id="matri_numservico" name="matri_numservico" tabindex="3"
                       onchange="validaNumServicoMatricula();" onkeyup="mudaFocoCampoMatricula(this, 2, event);"
                       onkeypress="return validaSomenteNumero(event);"
                       size="2" maxlength="2" title="Número Serviço" />
                <input type="text" id="matri_anoregistro" name="matri_anoregistro" tabindex="4"
                       onchange="validaAnoRegistroMatricula();" onkeyup="mudaFocoCampoMatricula(this, 4, event);"
                       onkeypress="return validaSomenteNumero(event);"
                       size="4" maxlength="4" title="Ano do Registro" />
                <input type="text" id="matri_tipolivro" name="matri_tipolivro" tabindex="5"
                       onchange="validaTpLivroMatricula();" onkeyup="mudaFocoCampoMatricula(this, 1, event);"
                       onkeypress="return validaSomenteNumero(event);"
                       size="1" maxlength="1" title="Tipo do Livro" />
                <input type="text" id="matri_numlivro" name="matri_numlivro" tabindex="6"
                       onchange="validaNumLivroMatricula();" onkeyup="mudaFocoCampoMatricula(this, 5, event);"
                       onkeypress="return validaSomenteNumero(event);"
                       size="5" maxlength="5" title="Número do Livro" />
                <input type="text" id="matri_numfolha" name="matri_numfolha" tabindex="7"
                       onchange="validaFolhaMatricula();" onkeyup="mudaFocoCampoMatricula(this, 3, event);"
                       onkeypress="return validaSomenteNumero(event);"
                       size="3" maxlength="3" title="Número da Folha" />
                <input type="text" id="matri_termo" name="matri_termo" tabindex="8"
                       onchange="validaTermoMatricula();" onkeyup="mudaFocoCampoMatricula(this, 7, event);"
                       onkeypress="return validaSomenteNumero(event);"
                       size="7" maxlength="7" title="Termo" />
                <input type="text" id="matri_codverificador" name="matri_codverificador" tabindex="9"
                       onchange="validaCodVerifMatricula();"
                       onblur="mudaFocoCampoMatricula(this, 2, event);"
                       onkeyup="mudaFocoCampoMatricula(this, 2, event);"
                       onkeypress="return validaSomenteNumero(event);"
                       size="2" maxlength="2" title="Código Verificador" />

                <input type="hidden" name="ed47_certidaomatricula" id="ed47_certidaomatricula" value="" />

                <input type="button" onclick="limparDadosMatricula();" value="Limpar Matrícula"
                       id="btnMatricula" name="btnMatricula" title="Limpar Matrícula" tabindex="10" />

              </td>
            </tr>
            <tr>
              <td width="15%">
                <?=@$Led47_c_certidaotipo?>
              </td>
              <td>
                <?php
                  $x = array('' => '', 'N' => 'NASCIMENTO', 'C' => 'CASAMENTO', 'E' => 'ESPECIAL');
                  db_select('ed47_c_certidaotipo', $x, true, $db_opcao1, "");

                  echo @$Led47_c_certidaonum;
                  db_input('ed47_c_certidaonum', 8, $Ied47_c_certidaonum, true, 'text', $db_opcao1, "");
                ?>
              </td>
            </tr>
            <tr>
              <td>
                <?=@$Led47_c_certidaofolha?>
              </td>
              <td>
                <?php
                  db_input('ed47_c_certidaofolha', 4, $Ied47_c_certidaofolha, true, 'text', $db_opcao1, "");
                  echo @$Led47_c_certidaolivro;

                  db_input('ed47_c_certidaolivro', 8, $Ied47_c_certidaolivro, true, 'text', $db_opcao1, "");
                  echo @$Led47_c_certidaodata;
                  db_inputdata(
                      'ed47_c_certidaodata',
                      @$ed47_c_certidaodata_dia,
                      @$ed47_c_certidaodata_mes,
                      @$ed47_c_certidaodata_ano,
                      true,
                      'text',
                      $db_opcao1,
                      ""
                  );
                    ?>
              </td>
            </tr>
            <tr>
              <td>
                <?=@$Led47_i_censoufcert?>
              </td>
              <td>
                <?php
                  $sSqlUf = $oDaoCensoUf->sql_query_file(
                      "",
                      "ed260_i_codigo,ed260_c_nome",
                      "ed260_c_nome",
                      "ed260_i_codigo <> 0"
                  );
                  $rsUf = $oDaoCensoUf->sql_record($sSqlUf);
                    ?>
                <select name="ed47_i_censoufcert"
                        id="ed47_i_censoufcert"
                        onChange="pesquisaMunicipiosUF(this.value);"
                        style="width:200px;height:18px;font-size:10px;">
                  <option value=""></option>
                  <?php
                    for ($i = 0; $i < $oDaoCensoUf->numrows; $i++) {
                        $dadosUF = db_utils::fieldsMemory($rsUf, $i);
                        echo "<option value=\"{$dadosUF->ed260_i_codigo}\" ";
                        echo (($dadosUF->ed260_i_codigo == $ed47_i_censoufcert)?"selected":"").">";
                        echo $dadosUF->ed260_c_nome;
                        echo "</option>";
                    } ?>
                </select>

                <?php
                echo $Led47_i_censomuniccert
                ?>
                <select name="ed47_i_censomuniccert" id="ed47_i_censomuniccert" onchange="pesquisaCartorios(this.value)"
                        style="width:200px;height:18px;font-size:10px;" >
                <?php
                if (isset($ed47_i_censomuniccert) && !empty($ed47_i_censomuniccert)) {
                    echo "<option value=\"{$ed261_i_codigo}\" selected> {$ed261_c_nome} </option>";
                }
                ?>
                </select>
              </td>
            </tr>
            <tr>
              <td>
                <?=@$Led47_i_censocartorio?>
              </td>
              <td>
                  <input type="text" id="nome_cartorio" name="nome_cartorio" placeholder="Digite o nome do cartório"
                         style="display:none;width:450px;height:30px;font-size:10px;" />
                <select name="ed47_i_censocartorio" id="ed47_i_censocartorio"
                        style="display:block;width:468px;height:18px;font-size:10px;" >
                    <?php
                        if (isset($ed47_i_censocartorio) && !empty($ed47_i_censocartorio)) {
                            echo "<option value=\"{$ed47_i_censocartorio}\" selected> {$ed291_c_nome} </option>";
                        }
                    ?>
                </select>
              </td>
            </tr>
            <tr>
              <td>
                <br>
              </td>
            </tr>
            <tr>
              <td>
                <b>Anexo da Certidão:</b>
              </td>
              <td>
                <div style="display: flex; align-items: center; width:100%;">
                <iframe name="frame_imagemCertidao"
                        id="frame_imagemCertidao"
                        src="edu4_alunodocumentocertidao.php"
                        width="56"
                        height="40"
                        frameborder="1"
                        scrolling="no"></iframe>
                <?php
                  $rs = db_query($oDaoAluno->sql_query_file(
                      null,
                      "ed47_i_certidado_estorage  as idCertidao",
                      null,
                      "ed47_i_codigo = $chavepesquisa"
                  ));
                  $dados = db_utils::fieldsMemory($rs, 0);
                  $arquivo = "";
                  $arquivocertidao = "";
                  if ((isset($chavepesquisa) || isset($alterar)) && isset($dados->idcertidao)) {
                      try {
                          $arquivo = (!empty($dados->idcertidao))?StorageHelper::downloadArquivo($dados->idcertidao):"";
                          $arquivocertidao = basename($arquivo);
                      } catch (Exception $oErro) {
                          echo "Erro ao consultar arquivo no e-storage: ".$oErro->getMessage();
                      }
                  }
                    ?>
               <script>
               frame_imagemCertidao.location.href="edu4_alunodocumentocertidao.php?imagem_gerada=<?=$arquivocertidao?>";
               </script>
                <?php
                if ($db_botao == true) {
                    ?>
                  <div style="display: flex;margin-top: -5px;flex-direction: column;width:100%;">
                    <iframe name="frame_certidao"
                            id="frame_certidao"
                            src="edu1_framealunodocumentocertidao.php"
                            width="100%"
                            height="31"
                            frameborder="0"
                            scrolling="no"
                            style="margin-bottom: 0px;margin-top:2px;"></iframe>
                    <input type="button"
                           value="Excluir Imagem"
                           onclick="location.href='edu1_aluno002.php?excluircertidao&chavepesquisa=<?=$chavepesquisa?>'"
                           style="font-size: 9px;padding: 0px;margin-left: 3px;width:82px;">
                  </div>
                <?php } ?>
                  <input name="oid_arquivoCertidao"
                         type="hidden"
                         id="oid_arquivoCertidao"
                         value="<?=(isset($arquivo)?$arquivo:'')?>"
                         size="30">
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <br>
              </td>
            </tr>
          </table>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset><legend><b>Identidade</b></legend>
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
              <td width="15%">
                <?=@$Led47_v_ident?>
              </td>
              <td>
                <?php
                  db_input(
                      'ed47_v_ident',
                      15,
                      $Ied47_v_ident,
                      true,
                      'text',
                      $db_opcao1,
                      "onChange='verificaDuplicidadeDocumentosAluno(this.name, this.value)';
                            onBlur='verificaDuplicidadeDocumentosAluno(this.name, this.value)'"
                  );
                  echo @$Led47_v_identcompl;

                  db_input('ed47_v_identcompl', 4, @$Ied47_v_identcompl, true, 'text', $db_opcao1);
                  echo @$Led47_i_censoufident;

                  $sQueryUf  = $oDaoCensoUf->sql_query_file("", "ed260_i_codigo,ed260_c_nome", "ed260_c_nome");
                  $result_uf = $oDaoCensoUf->sql_record($sQueryUf);
                  db_selectrecord("ed47_i_censoufident", $result_uf, "", $db_opcao1, "", "", "", "  ", "", 1);
                    ?>
              </td>
            </tr>
            <tr>
              <td>
                <?=@$Led47_i_censoorgemissrg?>
              </td>
              <td>
                <?php
                  $sSqlCensoOrgEmissRg = $oDaoCensoOrgEmissRg->sql_query_file(
                      "",
                      "ed132_i_codigo, ed132_c_descr",
                      "ed132_c_descr"
                  );
                  $result_org = $oDaoCensoOrgEmissRg->sql_record($sSqlCensoOrgEmissRg);
                  db_selectrecord("ed47_i_censoorgemissrg", $result_org, "", $db_opcao1, "", "", "", " ", "", 1);

                  echo @$Led47_d_identdtexp;
                  db_inputdata(
                      'ed47_d_identdtexp',
                      @$ed47_d_identdtexp_dia,
                      @$ed47_d_identdtexp_mes,
                      @$ed47_d_identdtexp_ano,
                      true,
                      'text',
                      $db_opcao1
                  );
                    ?>
              </td>
            </tr>
          </table>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset><legend><b>CNH</b></legend>
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
              <td width="15%">
                <?=@$Led47_v_cnh?>
              </td>
              <td>
                <?php
                  db_input(
                      'ed47_v_cnh',
                      15,
                      $Ied47_v_cnh,
                      true,
                      'text',
                      $db_opcao1,
                      "onChange='verificaDuplicidadeDocumentosAluno(this.name, this.value)';
                            onBlur='verificaDuplicidadeDocumentosAluno(this.name, this.value)'"
                  );
                  echo @$Led47_v_categoria;

                  $y = array("" => "", "A" => "A", "B" => "B", "C" => "C", "D" => "D", "E" => "E");
                  db_select('ed47_v_categoria', $y, true, $db_opcao1);

                  echo @$Led47_d_dtemissao;
                  db_inputdata(
                      'ed47_d_dtemissao',
                      @$ed47_d_dtemissao_dia,
                      @$ed47_d_dtemissao_mes,
                      @$ed47_d_dtemissao_ano,
                      true,
                      'text',
                      $db_opcao1
                  );
                    ?>
              </td>
            </tr>
            <tr>
              <td>
                <?=@$Led47_d_dthabilitacao?>
              </td>
              <td>
                <?php
                  db_inputdata(
                      'ed47_d_dthabilitacao',
                      @$ed47_d_dthabilitacao_dia,
                      @$ed47_d_dthabilitacao_mes,
                      @$ed47_d_dthabilitacao_ano,
                      true,
                      'text',
                      $db_opcao1
                  );
                  echo @$Led47_d_dtvencimento;

                  db_inputdata(
                      'ed47_d_dtvencimento',
                      @$ed47_d_dtvencimento_dia,
                      @$ed47_d_dtvencimento_mes,
                      @$ed47_d_dtvencimento_ano,
                      true,
                      'text',
                      $db_opcao1
                  );
                    ?>
              </td>
            </tr>
          </table>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset><legend><b>Outros</b></legend>
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
              <td width="15%">
                <?=@$Led47_v_cpf?>
              </td>
              <td>
                <?php
                  db_input(
                      'ed47_v_cpf',
                      11,
                      3,
                      true,
                      'text',
                      1,
                      "onChange='js_verificacpf(this); verificaDuplicidadeDocumentosAluno(this.name, this.value)';
                       onBlur='verificaDuplicidadeDocumentosAluno(this.name, this.value)'",
                      "",
                      "",
                      "",
                      15
                  );
                  $desabpassaporte = $ed47_i_nacion != 3 ? "readOnly style='background:#DEB887'" : "";
                  echo @$Led47_c_passaporte;
                  db_input(
                      'ed47_c_passaporte',
                      20,
                      $Ied47_c_passaporte,
                      true,
                      'text',
                      $db_opcao,
                      " {$desabpassaporte}
                        onChange='verificaDuplicidadeDocumentosAluno(this.name, this.value)';
                        onBlur='verificaDuplicidadeDocumentosAluno(this.name, this.value)' "
                  );
                  echo $Led47_cartaosus;
                  db_input(
                      'ed47_cartaosus',
                      20,
                      $Ied47_cartaosus,
                      true,
                      'text',
                      $db_opcao,
                      "onChange='verificaDuplicidadeDocumentosAluno(this.name, this.value)';
                            onBlur='verificaDuplicidadeDocumentosAluno(this.name, this.value)'"
                  );
                    ?>
              </td>
            </tr>
            <tr>
              <td><br></td>
            </tr>
            <tr>
              <td>
                <b>Anexo do CPF:</b>
              </td>
              <td>
                <div style="display: flex; align-items: center; width:100%;">

                <iframe name="frame_imagemCPF"
                        id="frame_imagemCPF"
                        src="edu4_alunodocumentocpf.php"
                        width="56"
                        height="40"
                        frameborder="1"
                        scrolling="no"></iframe>
                <?php
                  $rs = db_query($oDaoAluno->sql_query_file(
                      null,
                      "ed47_i_cpf_estorage  as idCpf",
                      null,
                      "ed47_i_codigo = $chavepesquisa"
                  ));
                  $dados = db_utils::fieldsMemory($rs, 0);
                  $arquivo = "";
                  $arquivocpf = "";
                  if ((isset($chavepesquisa) || isset($alterar)) && isset($dados->idcpf)) {
                      try {
                          $arquivo = (!empty($dados->idcpf)) ? StorageHelper::downloadArquivo($dados->idcpf): "" ;
                          $arquivocpf = basename($arquivo);
                      } catch (Exception $oErro) {
                          echo "Erro ao consultar arquivo no e-storage: ".$oErro->getMessage();
                      }
                  }
                    ?>
                  <script>
                  frame_imagemCPF.location.href="edu4_alunodocumentocpf.php?imagem_gerada=<?php echo $arquivocpf?>";
                  </script>
                <?php
                if ($db_botao == true) {
                    ?>
                  <div style="display: flex;margin-top: -5px;flex-direction: column;width:100%;">
                    <iframe name="frame_cpf"
                            id="frame_cpf"
                            src="edu1_framealunodocumentocpf.php"
                            width="100%"
                            height="31"
                            frameborder="0"
                            scrolling="no"
                            style="margin-bottom: 0px;margin-top:2px;"></iframe>
                    <input type="button" value="Excluir Imagem"
                           onclick="location.href='edu1_aluno002.php?excluircpf&chavepesquisa=<?=$chavepesquisa?>'"
                           style="font-size: 9px;padding: 0px;margin-left: 3px;width:82px;">
                  </div>
                <?php } ?>
                  <input name="oid_arquivoCPF"
                         type="hidden"
                         id="oid_arquivoCPF"
                         value="<?=(isset($arquivo)?$arquivo:'')?>"
                         size="30">
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <br>
              </td>
            </tr>
            <tr>
                <td><?php echo $Led47_rnm; ?></td>
                <td>
                    <?php
                    db_input('ed47_rnm', 20, $Ied47_rnm, true, 'text', $db_opcao);
                    echo $Led47_visto;
                    db_input('ed47_visto', 20, $Ied47_visto, true, 'text', $db_opcao);
                    ?>
                </td>
            </tr>
          </table>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td align="center">
        <table align="center">
          <tr>
            <td nowrap title="<?=@$Ted47_t_obs?>">
              <?=@$Led47_t_obs?><br>
              <?php db_textarea('ed47_t_obs', 4, 60, $Ied47_t_obs, true, 'text', $db_opcao, "") ?>
            </td>
            <td width="10%"></td>
            <td>
              <?=@$Led47_v_contato?><br>
              <?php db_textarea('ed47_v_contato', 4, 60, $Ied47_v_contato, true, 'text', $db_opcao, "") ?>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</div>
<input id="alterar" name="alterar" type="submit" value="Alterar" <?=($db_botao == false ? "disabled" : "")?>
       onclick="return validaFormulario();">
</form>
<script>
const sUrlRpcEscola = "edu4_escola.RPC.php";
const data_hj = <?=date("Y").date("m").date("d")?>;

inicializaFormulario();
function inicializaFormulario()
{
  buscaMatricula();
  validaMatricula();
  preencheDadosCertidao();
}

function buscaMatricula()
{
  if ($('ed47_i_codigo').value != "") {
    js_divCarregando("Aguarde, Carregando informações da matricula da certidão...","msgBox");

    var oParam        = new Object();
        oParam.exec   = 'getMatriculaAluno';
        oParam.iAluno = $('ed47_i_codigo').value;
    var oAjax = new Ajax.Request(sUrlRpcEscola,
                                 {
                                   method    : 'post',
                                   asynchronous: false,
                                   parameters: 'json='+Object.toJSON(oParam),
                                   onComplete: retornoMatricula
                                 }
                                );

  }
}

function retornoMatricula(oResponse)
{
  js_removeObj("msgBox");
  var oRetorno = JSON.parse(oResponse.responseText);
  if (oRetorno.iStatus == 1) {
    $('ed47_certidaomatricula').value = oRetorno.ed47_certidaomatricula;
    insereDadosMatricula(oRetorno.ed47_certidaomatricula);
  }
}

function insereDadosMatricula(iMatricula)
{
  $('matri_cartorio').value       = iMatricula.substr(0, 6);
  $('matri_tipoacervo').value     = iMatricula.substr(6, 2);
  $('matri_numservico').value     = iMatricula.substr(8, 2);
  $('matri_anoregistro').value    = iMatricula.substr(10, 4);
  $('matri_tipolivro').value      = iMatricula.substr(14, 1);
  $('matri_numlivro').value       = iMatricula.substr(15, 5);
  $('matri_numfolha').value       = iMatricula.substr(20, 3);
  $('matri_termo').value          = iMatricula.substr(23, 7);
  $('matri_codverificador').value = iMatricula.substr(30, 2);
}

function validaMatricula()
{
  if ($F('matri_cartorio') != "") {
    $('ed47_c_certidaonum').setAttribute('readOnly','readonly');
    $('ed47_c_certidaolivro').setAttribute('readOnly','readonly');
    $('ed47_c_certidaofolha').setAttribute('readOnly','readonly');
    $('ed47_c_certidaotipo').disabled = true;
    $('ed47_i_censoufcert').disabled = true;
    $('ed47_i_censomuniccert').disabled = true;
    $('ed47_i_censocartorio').disabled = true;
  } else {
    $('ed47_c_certidaonum').removeAttribute('readOnly');
    $('ed47_c_certidaolivro').removeAttribute('readOnly');
    $('ed47_c_certidaofolha').removeAttribute('readOnly');
    $('ed47_c_certidaotipo').disabled = false;
    $('ed47_i_censoufcert').disabled = false;
    $('ed47_i_censomuniccert').disabled = false;
    $('ed47_i_censocartorio').disabled = false;
  }
}

function preencheDadosCertidao()
{
  if( $F('matri_cartorio') != '' ) {
    buscaCartorioMatricula();
  }

  if( $F('matri_tipoacervo') != '' ) {
    validaTpAcervoMatricula();
  }

  if( $F('matri_numservico') != '' ) {
    validaNumServicoMatricula();
  }

  if( $F('matri_anoregistro') != '' ) {
    validaAnoRegistroMatricula();
  }

  if( $F('matri_tipolivro') != '' ) {
    validaTpLivroMatricula();
  }

  if( $F('matri_numlivro') != '' ) {
    validaNumLivroMatricula();
  }

  if( $F('matri_numfolha') != '' ) {
    validaFolhaMatricula();
  }

  if( $F('matri_termo') != '' ) {
    validaTermoMatricula();
  }

  if( $F('matri_codverificador') != '' ) {
    validaCodVerifMatricula();
  }
}

function buscaCartorioMatricula()
{
  if( $F('matri_cartorio') == '' ) {
    return;
  }

  js_divCarregando("Aguarde, Carregando dados do cartorio da matricula...","msgBoxCartorio");
  $('matri_cartorio').value = strPad($F('matri_cartorio'), 6, "0", "L");
  $('matri_cartorio').style.backgroundColor = '#FFFFFF';

  var oParam = new Object();
      oParam.exec = 'getCartorioMatricula';
      oParam.iCartorio = $('matri_cartorio').value;
  var oAjax = new Ajax.Request(sUrlRpcEscola,
                               {
                                 method    : 'post',
                                 asynchronous: true,
                                 parameters: 'json='+Object.toJSON(oParam),
                                 onComplete: retornoBuscaCartorio
                               });

}

function retornoBuscaCartorio(oRetorno)
{
  js_removeObj("msgBoxCartorio");
  oRetorno = JSON.parse(oRetorno.responseText);

  if (oRetorno.iStatus != 1) {
      if (confirm('Não foi encontrada nenhum cartório com o código digitado. ' +
          'Deseja prosseguir para cadastrar um novo cartório com esse código?')) {
          cadastroManualCartorio();
      } else {
          $('ed47_i_censocartorio').style.display = "block";
          $('nome_cartorio').style.display= "none";
          $('matri_cartorio').value = "";
          $('matri_cartorio').focus();
          alert(oRetorno.sMessage.urlDecode());
      }
  } else {
    $('ed47_i_censocartorio').style.display = "block";
    $('nome_cartorio').style.display= "none";

    if (oRetorno.ed260_i_codigo.trim() != "") {
      for (var iCont = 0; iCont < $('ed47_i_censoufcert').length; iCont++) {
        if ($('ed47_i_censoufcert').options[iCont].value == oRetorno.ed260_i_codigo) {
          $('ed47_i_censoufcert').selectedIndex = iCont;
          pesquisaMunicipiosUF($F('ed47_i_censoufcert'));
          break;
        }
      }
    }

    if (oRetorno.ed291_i_censomunic.trim() != "") {
      for (var iCont = 0; iCont < $('ed47_i_censomuniccert').length; iCont++) {
        if ($('ed47_i_censomuniccert').options[iCont].value == oRetorno.ed291_i_censomunic) {
          $('ed47_i_censomuniccert').selectedIndex = iCont;
          pesquisaCartorios($F('ed47_i_censomuniccert'));
          break;
        }
      }
    }

    if (oRetorno.ed291_i_codigo.trim() != "") {
      for (var iCont = 0; iCont < $('ed47_i_censocartorio').length; iCont++) {
        if ($('ed47_i_censocartorio').options[iCont].value == oRetorno.ed291_i_codigo) {
          $('ed47_i_censocartorio').selectedIndex = iCont;
          break;
        }
      }
    }

    $('ed47_i_censoufcert').disabled = true;
    $('ed47_i_censomuniccert').disabled = true;
    $('ed47_i_censocartorio').disabled = true;
  }
}

function cadastroManualCartorio()
{
    var placeholder = "Não foi encontrada nenhum cartório, informe o nome para cadastrar";

    $('ed47_i_censocartorio').style.display = "none";
    $('nome_cartorio').style.display= "block";
    $('nome_cartorio').setAttribute("placeholder", placeholder);
}

function validaTpAcervoMatricula()
{
  if (   $('matri_tipoacervo').value == ""
      || $('matri_tipoacervo').value.length < 2) {
    alert('Tipo de acervo não é válido, verifique!');
    $('matri_tipoacervo').style.backgroundColor = '#99A9AE';
    $('matri_tipoacervo').value                 = "";
    $('matri_tipoacervo').focus();
  } else {
    $('matri_tipoacervo').style.backgroundColor = '#FFFFFF';
  }
}

function validaNumServicoMatricula()
{
  if (   $('matri_numservico').value != ""
      || $('matri_numservico').value.length == 2) {
    if ($('matri_numservico').value != "55") {
      alert('Número de serviço não é válido, verifique!');
      $('matri_numservico').style.backgroundColor = '#99A9AE';
      $('matri_numservico').value                 = "";
      $('matri_numservico').focus();
    } else {
      $('matri_numservico').style.backgroundColor = '#FFFFFF';
    }
  } else {
    alert('Número inválido!');
    $('matri_numservico').style.backgroundColor = '#99A9AE';
    $('matri_numservico').focus();
  }
}

function validaAnoRegistroMatricula()
{
  var iAno       = $('matri_anoregistro').value;
  var oData      = new Date();
  var iAnoAtual  = oData.getFullYear();

  if (iAno == "" || iAno.length < 4) {
    alert('Ano de registro não é válido, verifique!');
    $('matri_anoregistro').style.backgroundColor = '#99A9AE';
    $('matri_anoregistro').value                 = "";
    $('matri_anoregistro').focus();
  } else {
    $('matri_anoregistro').style.backgroundColor = '#FFFFFF';
  }

  if ( 1900 > iAno || iAno > iAnoAtual) {
    alert('Número inválido!!');
    $('matri_anoregistro').style.backgroundColor = '#99A9AE';
    $('matri_anoregistro').value                 = "";
    $('matri_anoregistro').focus();
  } else {
    $('matri_anoregistro').style.backgroundColor = '#FFFFFF';
  }
}

function validaTpLivroMatricula()
{
  var iTipoLivro = $('matri_tipolivro').value;
  $('matri_tipolivro').style.backgroundColor = '#FFFFFF';
  if (iTipoLivro == 1) {
      $('ed47_c_certidaotipo').options.length = 0;
      $('ed47_c_certidaotipo').add(new Option('NASCIMENTO', 'N'));
  } else if (iTipoLivro == 2) {
      $('ed47_c_certidaotipo').options.length = 0;
      $('ed47_c_certidaotipo').add(new Option('CASAMENTO', 'C'));
  } else if (iTipoLivro == 7) {
      $('ed47_c_certidaotipo').options.length = 0;
      $('ed47_c_certidaotipo').add(new Option('ESPECIAL', 'E'));
  } else {
    alert('Tipo do livro não é válido, verifique!');
    $('matri_tipolivro').style.backgroundColor = '#99A9AE';
    $('matri_tipolivro').value                 = "";
    $('matri_tipolivro').focus();
  }
}

function validaNumLivroMatricula()
{
  $('matri_numlivro').value = strPad($('matri_numlivro').value, 5, "0", "L");
  var numLivro              = $('matri_numlivro').value;

  if (   numLivro == ""
      || numLivro.length < 5) {

    alert('Número do livro não é válido, verifique!');
    $('matri_numlivro').style.backgroundColor = '#99A9AE';
    $('matri_numlivro').value                 = "";
    $('matri_numlivro').focus();
  } else {

    $('ed47_c_certidaolivro').value           = numLivro;
    $('ed47_c_certidaolivro').setAttribute('readOnly','readonly');
    $('matri_numlivro').style.backgroundColor = '#FFFFFF';
  }
}

function validaFolhaMatricula()
{
  $('matri_numfolha').value = strPad($('matri_numfolha').value, 3, "0", "L");
  var iNumFolha             = $('matri_numfolha').value;

  if (   iNumFolha == ""
      || iNumFolha.length < 3) {

    alert('Número da folha não é válido, verifique!');
    $('matri_numfolha').style.backgroundColor = '#99A9AE';
    $('matri_numfolha').value                 = "";
    $('matri_numfolha').focus();
  } else {

    $('ed47_c_certidaofolha').setAttribute('readOnly','readonly');
    $('ed47_c_certidaofolha').value           = iNumFolha;
    $('matri_numfolha').style.backgroundColor = '#FFFFFF';
  }
}

function validaTermoMatricula()
{
  $('matri_termo').value = strPad($('matri_termo').value, 7, "0", "L");
  var iTermo             = $('matri_termo').value;

  if (   iTermo == ""
      || iTermo.length < 7) {

    alert('Número do termo não é válido, verifique!');
    $('matri_termo').style.backgroundColor = '#99A9AE';
    $('matri_termo').value                 = "";
    $('matri_termo').focus();
  } else {

    $('ed47_c_certidaonum').setAttribute('readOnly','readonly');
    $('ed47_c_certidaonum').value          = iTermo;
    $('matri_termo').style.backgroundColor = '#FFFFFF';
  }
}

function validaCodVerifMatricula()
{
  var iCodVerif = $('matri_codverificador').value;

  if (   iCodVerif == ""
      || iCodVerif.length < 2) {

    alert('Código verificador não é válido, verifique!');
    $('matri_codverificador').style.backgroundColor = '#99A9AE';
    $('matri_codverificador').value                 = "";
    $('matri_codverificador').focus();
  } else {
    $('matri_codverificador').style.backgroundColor = '#FFFFFF';
  }
}

function verificaEnvioMatricula()
{
  var iCartorio    = $F('matri_cartorio');
  var iTpAcervo    = $F('matri_tipoacervo');
  var iNumServico  = $F('matri_numservico');
  var iAnoRegistro = $F('matri_anoregistro');
  var sTipoLivro   = $F('matri_tipolivro');
  var iNumLivro    = $F('matri_numlivro');
  var iNumFolha    = $F('matri_numfolha');
  var iTermo       = $F('matri_termo');
  var iCodVerif    = $F('matri_codverificador');

  if (iCartorio.trim() == "") {

    if ((iTpAcervo.trim() || iNumServico.trim() || iAnoRegistro.trim() || sTipoLivro.trim()
         || iNumLivro.trim() || iNumFolha.trim() || iTermo.trim() || iCodVerif.trim()) != "") {
      return false;
    }
  }

  if (iTpAcervo.trim() == "") {

    if ((iCartorio.trim() || iNumServico.trim() || iAnoRegistro.trim() || sTipoLivro.trim()
         || iNumLivro.trim() || iNumFolha.trim() || iTermo.trim() || iCodVerif.trim()) != "") {
      return false;
    }
  }

  if (iNumServico.trim() == "") {

    if ((iCartorio.trim() || iTpAcervo.trim() || iAnoRegistro.trim() || sTipoLivro.trim()
         || iNumLivro.trim() || iNumFolha.trim() || iTermo.trim() || iCodVerif.trim()) != "") {
      return false;
    }
  }

  if (iAnoRegistro.trim() == "") {

    if ((iCartorio.trim() || iTpAcervo.trim() || iNumServico.trim() || sTipoLivro.trim()
         || iNumLivro.trim() || iNumFolha.trim() || iTermo.trim() || iCodVerif.trim()) != "") {
      return false;
    }
  }

  if (sTipoLivro.trim() == "") {

    if ((iCartorio.trim() || iTpAcervo.trim() || iNumServico.trim() || iAnoRegistro.trim()
         || iNumLivro.trim() || iNumFolha.trim() || iTermo.trim() || iCodVerif.trim()) != "") {
      return false;
    }
  }

  if (iNumLivro.trim() == "") {

    if ((iCartorio.trim() || iTpAcervo.trim() || iNumServico.trim() || iAnoRegistro.trim()
         || sTipoLivro.trim() || iNumFolha.trim() || iTermo.trim() || iCodVerif.trim()) != "") {
      return false;
    }
  }

  if (iNumFolha.trim() == "") {

    if ((iCartorio.trim() || iTpAcervo.trim() || iNumServico.trim() || iAnoRegistro.trim()
         || sTipoLivro.trim() || iNumLivro.trim() || iTermo.trim() || iCodVerif.trim()) != "") {
      return false;
    }
  }

  if (iTermo.trim() == "") {

    if ((iCartorio.trim() || iTpAcervo.trim() || iNumServico.trim() || iAnoRegistro.trim()
         || sTipoLivro.trim() || iNumLivro.trim() || iNumFolha.trim() || iCodVerif.trim()) != "") {
      return false;
    }
  }

  if (iCodVerif.trim() == "") {

    if ((iCartorio.trim() || iTpAcervo.trim() || iNumServico.trim() || iAnoRegistro.trim()
         || sTipoLivro.trim() || iNumLivro.trim() || iNumFolha.trim() || iTermo.trim()) != "") {
      return false;
    }
  }

  return true;
}


function validaFormulario()
{
  var nacion        = <?=$ed47_i_nacion?>;
  var datanasc      = "<?=$ed47_d_nasc?>";
  var iCartorio     = $F('matri_cartorio').trim();
  var iTpAcervo     = $F('matri_tipoacervo').trim();
  var iNumServico   = $F('matri_numservico').trim();
  var iAnoRegistro  = $F('matri_anoregistro').trim();
  var sTipoLivro    = $F('matri_tipolivro').trim();
  var iNumLivro     = $F('matri_numlivro').trim();
  var iNumFolha     = $F('matri_numfolha').trim();
  var iTermo        = $F('matri_termo').trim();
  var iCodVerif     = $F('matri_codverificador').trim();
  var identnum      = $F('ed47_v_ident').trim();
  var identcomp     = $F('ed47_v_identcompl').trim();
  var identorg      = $F('ed47_i_censoorgemissrg').trim();
  var identuf       = $F('ed47_i_censoufident').trim();
  var identdata     = $F('ed47_d_identdtexp').trim();
  var certtip       = $F('ed47_c_certidaotipo').trim();
  var certnum       = $F('ed47_c_certidaonum').trim();
  var certfol       = $F('ed47_c_certidaofolha').trim();
  var certliv       = $F('ed47_c_certidaolivro').trim();
  var certcar       = $F('ed47_i_censocartorio').trim();
  var certdat       = $F('ed47_c_certidaodata').trim();
  var certuf        = $F('ed47_i_censoufcert').trim();
  var certmun       = $F('ed47_i_censomuniccert').trim();

  if (nacion != 3) {
     if (verificaEnvioMatricula() == false) {
       alert("Campo matrícula inválido, verifique o número da matrícula antes de processeguir!");
       return false;
     } else if (verificaEnvioMatricula()) {
       $('ed47_certidaomatricula').value = iCartorio + iTpAcervo + iNumServico + iAnoRegistro + sTipoLivro +
                                           iNumLivro + iNumFolha + iTermo + iCodVerif;

       if ($F('ed47_certidaomatricula') != ""
           && !validaDigitoVerificadorCertidaoMatricula($F('ed47_certidaomatricula'))) {
         return false;
       }
     }

    if (   identnum == ""
        && (identcomp != ""
            || identorg != ""
            || identuf != ""
            || identdata != "")) {
      sMsgIdent  = " Campo N° Identidade deve ser informado quando\num dos campos abaixo estiverem ";
      sMsgIdent += " informados:\n\nComplemento\nUF Identidade\nÓrgao Emissor\nData Expedição Identidade";
      alert(sMsgIdent);
      return false;
    }

    if (   identorg == ""
        && (identnum != ""
            || identuf != "")) {
      sMsgOrg  = " Campo Órgão Emissor deve ser informado quando\num dos campos abaixo";
      sMsgOrg += " estiverem informados:\n\nN° Identidade\nUF Identidade";
      alert(sMsgOrg);
      return false;
    }

    if (   identuf == ""
        && (identnum != ""
            || identorg != "")) {

      sMsgUf  = " Campo UF Identidade deve ser informado quando\num dos campos abaixo";
      sMsgUf += " estiverem informados:\n\nN° Identidade\nÓrgão Emissor";
      alert(sMsgUf);
      return false;
    }

    if (   identcomp != ""
        && identnum == ""
        && identorg == ""
        && identuf == " ") {
      sMsgComp  = " Campo Complemento só pode ser informado quando\num dos campos abaixo estiverem";
      sMsgComp += " informados:\n\nN° Identidade\nÓrgão Emissor\nUF Identidade";
      alert(sMsgComp);
      return false;
    }

    if (   identdata != ""
        && identnum == ""
        && identorg == ""
        && identuf == "") {
      sMsgData  = " Campo Data Expedição Identidade só pode ser informado quando\num dos campos abaixo estiverem";
      sMsgData += " informados:\n\nN° Identidade\nÓrgão Emissor\nUF Identidade";
      alert(sMsgData);
      return false;
    }

    if (identdata != "") {
      diaident = identdata.substr(0,2);
      mesident = identdata.substr(3,2);
      anoident = identdata.substr(6,4);
      dianasc  = datanasc.substr(8,2);
      mesnasc  = datanasc.substr(5,2);
      anonasc  = datanasc.substr(0,4);

      if (anoident < 1900) {
        alert("Ano da Data de Expedição deve ser maior que 1899!");
        return false;
      }

      data_ident = anoident+""+mesident+""+diaident;
      data_nasc  = anonasc+""+mesnasc+""+dianasc;

      if (parseInt(data_ident) >= parseInt(data_hj)) {
        alert("Campo Data de Expedição deve ser menor que a data corrente!");
        return false;
      }

      if (parseInt(data_ident) < parseInt(data_nasc)) {
        sMsgNasc  = " Campo Data de Expedição não deve ser menor que a data de";
        sMsgNasc += " nascimento do aluno ("+dianasc+"/"+mesnasc+"/"+anonasc+")!";
        alert(sMsgNasc);
        return false;
      }
    }

    if (   certtip == ""
        && (certnum != ""
            || certfol != ""
            || certliv != ""
            || certdat != ""
            || certuf != ""
            || certcar != ""
            || certmun != "" )) {
      sMsgCert  = " Campo Tipo de Certidão deve ser informado quando\num dos campos abaixo estiverem";
      sMsgCert += " informados:\n\nNúmero do Termo\nFolha\nLivro\nData da Emissão\nUF Cartório\nCartório\nMunicípio";
      alert(sMsgCert);
      return false;
    }

    if (   (certcar == "" && $F("nome_cartorio") == "")
        && (certnum != ""
            || certfol != ""
            || certliv != ""
            || certdat != ""
            || certuf != ""
            || certmun != "" )) {
      sMsgCartorio  = " Campo Cartório deve ser informado quando\num dos campos abaixo estiverem";
      sMsgCartorio += " informados:\n\nNúmero do Termo\nFolha\nLivro\nData da Emissão\nUF Cartório\nMunicípio";
      alert(sMsgCartorio);
      return false;
    }

    if (   certnum == ""
        && (certtip != ""
            || certuf != ""
            || certcar != ""
            || certmun != "" )) {
      sMsgNum  = " Campo Número do Termo deve ser informado quando\num dos campos abaixo estiverem ";
      sMsgNum += " informados:\n\nTipo de Certidão\nUF Cartório\nCartório\nMunicípio";
      alert(sMsgNum);
      return false;
    }

    if (   (certcar == "" && $F("nome_cartorio") == "")
        && (certtip != ""
            || certuf != ""
            || certnum != ""
            || certmun != "" )) {
      sMsgCar  = " Campo Cartório deve ser informado quando\num dos campos abaixo";
      sMsgCar += " estiverem informados:\n\nTipo de Certidão\nUF Cartório\nNúmero do Termo\nMunicípio";
      alert(sMsgCar);
      return false;
    }

    if (   certuf == ""
        && (certtip != ""
            || certcar != ""
            || certnum != ""
            || certmun != "" )) {
      sMsgCertUf  = " Campo UF Cartório deve ser informado quando\num dos campos abaixo estiverem";
      sMsgCertUf += " informados:\n\nTipo de Certidão\nCartório\nNúmero do Termo\nMunicípio";
      alert(sMsgCertUf);
      return false;
    }

    if (   certfol != ""
        && certtip == ""
        && certnum == ""
        && certuf == ""
        && certcar == "") {
      sMsgFol  = " Campo Folha só pode ser informado quando\num dos campos abaixo";
      sMsgFol += " estiverem informados:\n\nTipo de Certidão\nNúmero do Termo\nUF Cartório\nCartório";
      alert(sMsgFol);
      return false;
    }

    if (   certliv != ""
        && certtip == ""
        && certnum == ""
        && certuf == ""
        && certcar == "") {
      sMsgLiv  = " Campo Livro só pode ser informado quando\num dos campos abaixo estiverem";
      sMsgLiv += " informados:\n\nTipo de Certidão\nNúmero do Termo\nUF Cartório\nCartório";
      alert(sMsgLiv);
      return false;
    }

    if (   certdat != ""
        && certtip == ""
        && certnum == ""
        && certuf == ""
        && certcar == "") {
      sMsgFim  = " Campo Data de Emissão só pode ser informado quando\num dos campos abaixo ";
      sMsgFim += " estiverem informados:\n\nTipo de Certidão\nNúmero do Termo\nUF Cartório\nCartório";
      alert(sMsgFim);
      return false;
    }

    if (certdat != "") {
      diacert   = certdat.substr(0,2);
      mescert   = certdat.substr(3,2);
      anocert   = certdat.substr(6,4);
      dianasc   = datanasc.substr(8,2);
      mesnasc   = datanasc.substr(5,2);
      anonasc   = datanasc.substr(0,4);
      data_cert = anocert+""+mescert+""+diacert;
      data_nasc = anonasc+""+mesnasc+""+dianasc;

      if (parseInt(data_cert) >= parseInt(data_hj)) {
        alert("Campo Data de Emissão deve ser menor que a data corrente!");
        return false;
      }

      if (certtip == "N") {
        if (parseInt(data_cert) < parseInt(data_nasc)) {
          sMsgTip  = " Campo Data de Emissão não pode ser menor que a data de";
          sMsgTip += " nascimento do aluno ("+dianasc+"/"+mesnasc+"/"+anonasc+")!";
          alert(sMsgTip);
          return false;
        }
      } else if(certtip == "C") {

        if (parseInt(data_cert) < parseInt(data_nasc)) {

          sMsgCertTip  = " Campo Data de Emissão não pode ser menor que a data de nascimento";
          sMsgCertTip += " do aluno ("+dianasc+"/"+mesnasc+"/"+anonasc+")!";
          alert(sMsgCertTip);
          return false;
        }
      }
    }

    if ( $F('ed47_c_passaporte') != "") {
       sMsgPass  = " Campo N° Passaporte só pode ser informado quando nacionalidade do";
       sMsgPass += " aluno for Estrangeira (Aba Dados Pessoais).";
       alert(sMsgPass);
       return false;
    }
  }

  if (   nacion == 3 ) {
     if ( certtip != ""
          || certnum != ""
          || certfol != ""
          || certliv != ""
          || certcar != ""
          || certdat != ""
          || certuf != ""
          || certmun != "") {
       sMsgNacion  = " Aluno com nacionalidade Estrangeira (Aba Dados Pessoais).\nCampos ";
       sMsgNacion += " referente a Certidão NÃO devem ser informados!";
       alert(sMsgNacion);
       return false;
     }

     if ( identnum != ""
          || identcomp != ""
          || identorg != " "
          || identuf != " "
          || identdata != "") {
          sMsg  = " Aluno com nacionalidade Estrangeira (Aba Dados Pessoais).\nCampos referente";
          sMsg += " a Identidade NÃO devem ser informados!";
          alert(sMsg);
          return false;
     }

     if ($F('ed47_c_passaporte') =="") {
        sMsgPass  = " Campo N° Passaporte não informado ";
        alert(sMsgPass);
        return false;
     }
  }

  return true;
}

function pesquisaMunicipiosUF(uf)
{
  if (uf == "") {
      $('ed47_i_censomuniccert')[0].selected   = true;
      $('ed47_i_censomuniccert').disabled      = true;
      $('ed47_i_censocartorio').innerHTML      = "";
      $('ed47_i_censocartorio').disabled       = true;
      return false;
  }

  js_divCarregando("Aguarde, carregando registro(s)","msgBox");
  var oParam = new Object();
      oParam.exec = 'pesquisaMunicipios';
      oParam.uf = uf;
  var oAjax = new Ajax.Request(sUrlRpcEscola,
                               {
                                 method    : 'post',
                                 asynchronous: false,
                                 parameters: 'json='+Object.toJSON(oParam),
                                 onComplete: retornoMunicipiosUF
                               }
                              );

}

function retornoMunicipiosUF(oAjax)
{
  js_removeObj("msgBox");
  var oRetorno = JSON.parse(oAjax.responseText);
  sHtml = '';
  if (oRetorno.aResult.length == 0) {
    sHtml += '<option value="">Selecione o Estado</option>';
    $('ed47_i_censomuniccert').innerHTML = sHtml;
  } else {
    municipios = oRetorno.aResult;
    sHtml += '<option value=""></option>';
    for (var i = 0;i < municipios.length; i++) {
      sHtml += '<option value="'+municipios[i].ed261_i_codigo+'">'+municipios[i].ed261_c_nome.urlDecode()+'</option>';
    }

    $('ed47_i_censomuniccert').innerHTML = sHtml;
    $('ed47_i_censomuniccert')[0].selected = true;
    pesquisaCartorios($F('ed47_i_censomuniccert'));
  }

  $('ed47_i_censomuniccert').disabled  = false;
}

function pesquisaCartorios(municipio)
{
  $('ed47_i_censocartorio').innerHTML = "";
  $('ed47_i_censocartorio').disabled  = true;

  js_divCarregando("Aguarde, carregando registro(s)","msgBox");
  var oParam = new Object();
      oParam.exec = 'pesquisaCartorios';
      oParam.uf = $F('ed47_i_censoufcert');
      oParam.municipio = municipio
  var oAjax = new Ajax.Request(sUrlRpcEscola,
                               {
                                 method    : 'post',
                                 asynchronous: false,
                                 parameters: 'json='+Object.toJSON(oParam),
                                 onComplete: retornoCartorios
                               }
                              );
}

function retornoCartorios(oAjax)
{
  js_removeObj("msgBox");
  var oRetorno = JSON.parse(oAjax.responseText);
  sHtml = '';

  if (oRetorno.aResult.length==0) {
    sHtml += '<option value="">Não há cartório</option>';
  } else {

    cartorios = oRetorno.aResult;
    sHtml += '<option value=""></option>';
    for (var i = 0;i < cartorios.length; i++) {
      with (cartorios[i]) {
        if ( cartorios[i] != "" ) {
            sHtml += '<option value="'+ed291_i_codigo+'">'+ed291_c_nome.urlDecode()+'</option>';
        }
      }
    }
  }

  $('ed47_i_censocartorio').innerHTML = sHtml;
  $('ed47_i_censocartorio').disabled  = false;
}

function limparDadosMatricula()
{
  if (confirm('Deseja limpar o número da matrícula?')) {

    $('matri_cartorio').value         = "";
    $('matri_tipoacervo').value       = "";
    $('matri_numservico').value       = "";
    $('matri_anoregistro').value      = "";
    $('matri_tipolivro').value        = "";
    $('matri_numlivro').value         = "";
    $('matri_numfolha').value         = "";
    $('matri_termo').value            = "";
    $('matri_codverificador').value   = "";
    $('ed47_certidaomatricula').value = "";

    validaMatricula();

    $('ed47_c_certidaonum').value = "";
    $('ed47_c_certidaofolha').value = "";
    $('ed47_c_certidaolivro').value = "";
    $('ed47_c_certidaodata').value = "";
    $('ed47_c_certidaodata_dia').value = "";
    $('ed47_c_certidaodata_mes').value = "";
    $('ed47_c_certidaodata_ano').value = "";
    $('ed47_i_censoufcert').disabled = false;

    $('ed47_i_censoufcert')[0].selected = true;
    $('ed47_i_censomuniccert').innerHTML = "<option value='' selected></option>";
    $('ed47_i_censomuniccert').disabled = false;
    $('ed47_i_censocartorio').innerHTML = "<option value='' selected></option>";
    $('ed47_i_censocartorio').disabled = false;

    var check1 = "";
    var check2 = "";

    if ($('ed47_c_certidaotipo').value == "N") {
      check1 = "selected";
    } else if ($('ed47_c_certidaotipo').value == "C") {
      check2 = "selected";
    }

    $('ed47_c_certidaotipo').options.length = 0;
    $('ed47_c_certidaotipo').add(new Option('', ''));
    $('ed47_c_certidaotipo').add(new Option('NASCIMENTO', 'N'));
    $('ed47_c_certidaotipo').add(new Option('CASAMENTO', 'C'));
    $('ed47_c_certidaotipo').add(new Option('ESPECIAL', 'E'));
    $('alterar').disabled = false;
  }
}

function mudaFocoCampoMatricula(elemento, iTamanho, evento)
{
  var iTecla = 0;
  iTecla = evento.which;

  const cartorio    =  $('matri_cartorio').value;
  const tipoAcervo  =  $('matri_tipoacervo').value;
  const numServico  =  $('matri_numservico').value;
  const anoRegistro =  $('matri_anoregistro').value;
  const tipoLivro   =  $('matri_tipolivro').value;
  const numLivro    =  $('matri_numlivro').value;
  const numFolha    =  $('matri_numfolha').value;
  const termo       =  $('matri_termo').value;
  const dv          =  $('matri_codverificador').value;

  if (iTecla != '16' && iTecla != '9') {

    if (elemento.value.length == iTamanho) {

      if (cartorio != '' && tipoAcervo != '' && numServico != '' && anoRegistro != ''
            && tipoLivro != '' && numLivro != '' && numFolha != '' && termo != '' && dv != '') {

              const numMatricula = cartorio+tipoAcervo+numServico+anoRegistro+tipoLivro+numLivro+numFolha+termo+dv;
              if (validaDigitoVerificadorCertidaoMatricula(numMatricula)) {
                  /*
                   * requisacao para verificar se existe mais de um aluno cadastrado com o memsmo numero de matricula
                   */
                  buscaAlunosNumeroCertidaoMatricula(numMatricula);
              }

      }
      elemento.next().focus();
    }
  }
}

function validaSomenteNumero(sCaractere)
{
  var iTecla = 0;
  iTecla = sCaractere.which;
  if (   (iTecla > 47 && iTecla < 58)
      || iTecla == 8
      || iTecla == 127
      || iTecla == 0
      || iTecla == 9
      || iTecla == 13) {
    return true;
  } else {
    return false;
  }
}

function js_TestaNi(cNI)
{
  var NI;
  NI = js_LimpaCampo(cNI.value,10);

  if (NI.length != 11) {

    alert('O número do CPF informado está incorreto');
    cNI.value = "";
    cNI.select();
    cNI.focus();
    return(false);
  }

  if (NI.substr(9, 2) != js_CalculaDV(NI.substr(0, 9), 11)) {

    alert('O número do CPF informado está incorreto');
    cNI.value = "";
    cNI.select();
    cNI.focus();
    return(false);
  }

  return (true);
}

function js_processacpf(cpf)
{
  // Substitui o formato 000.000.000-00 por 00000000000
  for(var i = 0;i<2;i++){
    cpf = cpf.replace('\.', '');
    cpf = cpf.replace('-', '');
  }

  return cpf;
}

function js_verificacpf(obcgc)
{
  if (obcgc.value == "") {
     return false;
  }

  obcgc.value = js_processacpf(obcgc.value);

  if (   obcgc.value == 00000000000
      || obcgc.value == 00000000191) {

    alert('Valor Informado não é Válido para CPF.');
    obcgc.value = "";
    obcgc.select();
    obcgc.focus();
  }

  if (obcgc.value.length == 11) {
    return js_TestaNi(obcgc);
  }

  if (obcgc.value != "") {

    alert('Valor Informado não é Válido para CPF.');
    obcgc.value = "";
    obcgc.select();
    obcgc.focus();
  }

  return false;
}

function strPad(palavra, casas, carac, dir)
{
  if(palavra == null || palavra == '') {
    palavra = 0;
  }
  var ret = '';
  var nro = casas - (palavra.length);
  for(var i = 0; i < nro; i++) {
    ret += carac;
  }
  if(dir == 'R') {
    ret = palavra + ret;
  } else if(dir == 'L') {
    ret += palavra;
  }
  return ret;
}

function validaDigitoVerificadorCertidaoMatricula(numMatricula)
{
  var validaMatricula = numMatricula.slice(0, numMatricula.length - 2);

  const pesosDvum = [2, 3, 4, 5, 6, 7, 8, 9, 10,
                   0, 1, 2, 3, 4, 5, 6, 7, 8,
                   9, 10, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9
                  ];

  const pesosDvdois = [ 1,  2,  3,  4,  5,  6,  7,  8,  9, 10,
      0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 10,
      0,  1,  2,  3,  4,  5,  6,  7,  8,  9
      ];

  var umDV = 0;
  for(let i = 0; i < validaMatricula.length; i++) {
    var num = parseInt(validaMatricula.charAt(i));
    umDV += num * pesosDvum[i];
  }

  umDV = umDV % 11;
  if (umDV == 10) {
    umDV = 1;
  }

  var primeiroDv = umDV;

  var doisDv = 0;
  validaMatricula += primeiroDv;

  for(let i = 0; i < validaMatricula.length; i++) {

    var num = parseInt(validaMatricula.charAt(i));
    doisDv += num * pesosDvdois[i];
  }

  doisDv = doisDv % 11;
  if (doisDv == 10) {
    doisDv = 1;
  }

  var segundoDv = doisDv;
  validaMatricula += segundoDv;

  if (numMatricula == validaMatricula) {
      return true;
  }else{
      alert("Campo matrícula inválido, verifique o número da matrícula antes de prosseguir!");
      return false;
  }
}


function buscaAlunosNumeroCertidaoMatricula(numeroCertidao)
{
  js_divCarregando("Aguarde, verificando alunos com mesmo numero da matricula da certidão","msgBoxCertidao");
  var oParam = new Object();
      oParam.exec = 'buscaAlunosNumeroCertidaoMatricula';
      oParam.codigoAluno = $F("ed47_i_codigo");
      oParam.numeroCertidao = numeroCertidao;
  var oAjax = new Ajax.Request(sUrlRpcEscola,
                               {
                                 method    : 'post',
                                 asynchronous: false,
                                 parameters: 'json='+Object.toJSON(oParam),
                                 onComplete: retornoAlunosNumeroCertidaoMatricula
                               }
                              );
}

function retornoAlunosNumeroCertidaoMatricula(oAjax)
{
  js_removeObj("msgBoxCertidao");
  var oRetorno = JSON.parse(oAjax.responseText);

  if (oRetorno.iStatus == "0") {
      alert(oRetorno.sMessage);
      return false;
  }
  if (oRetorno.aResult.length > 0) {
      msg = "Encontrado(s) aluno(s) com o mesmo numero da matricula da certidão:\n";
      for (var iCont = 0; iCont < oRetorno.aResult.length; iCont++) {
          msg += oRetorno.aResult[iCont].ed47_i_codigo+" - "+oRetorno.aResult[iCont].ed47_v_nome.urlDecode()+"\n";
      }
      alert(msg);
      $("alterar").disabled = true;
  } else {
      $("alterar").disabled = false;
  }
}

function verificaDuplicidadeDocumentosAluno(documentoValidacao, valor)
{
    if (valor == "") {
        return true;
    }

    if (documentoValidacao == "") {
        alert('Tipo de documento para verificação não informado');
        return false;
    }

    js_divCarregando("Aguarde, verificando documentos "+documentoValidacao,"msgBoxDocumentos");
    var oParam = new Object();
        oParam.exec = 'buscaAlunosDocumento';
        oParam.codigoAluno = $F("ed47_i_codigo");
        oParam.documentoValidacao = documentoValidacao;
        oParam.valor = valor;
    var oAjax = new Ajax.Request(sUrlRpcEscola,
                                 {
                                   method    : 'post',
                                   asynchronous: true,
                                   parameters: 'json='+Object.toJSON(oParam),
                                   onComplete: function(oAjax)
                                               {
                                                  js_removeObj("msgBoxDocumentos");
                                                  var oRetorno = JSON.parse(oAjax.responseText);

                                                  if (oRetorno.iStatus == "0") {
                                                      alert(oRetorno.sMessage);
                                                      return false;
                                                  }
                                                  if (oRetorno.aResult.length > 0) {
                                                      msg  = 'Encontrado(s) aluno(s) com o mesmo número de ';
                                                      msg += oRetorno.label+':\n';
                                                      for (var iCont = 0; iCont < oRetorno.aResult.length; iCont++) {
                                                          msg += oRetorno.aResult[iCont].ed47_i_codigo;
                                                          msg += " - "+oRetorno.aResult[iCont].ed47_v_nome.urlDecode();
                                                          msg += "\n";
                                                      }
                                                      alert(msg);
                                                  }
                                                  return true;
                                               }
                                 });
}
</script>
