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

//MODULO: cadastro
$cliptubaixa->rotulo->label();
$cliptubaixaproc->rotulo->label();
$clrotulo = new rotulocampo;
$clnumpref = new cl_numpref;
$clrotulo->label("j01_nome");
$clrotulo->label("nome");
$clrotulo->label("j02_dtbaixa");
$clrotulo->label("p58_nome");
$clrotulo->label("p58_numero");
?>
<form name="form1" method="post" action="">

  <fieldset>
    <legend>Baixa</legend>

    <fieldset class="separator">
      <legend>Dados do usuário</legend>
      <table class="form-container">
          <?php
          if (!isset($j02_usuario) || $j02_usuario == "") {
              $sqlUsuario = "select id_usuario as j02_usuario,nome from db_usuarios where id_usuario = " . db_getsession('DB_id_usuario');
              $rsUsuario  = db_query($sqlUsuario);
              db_fieldsmemory($rsUsuario, 0);
          }

          if (!isset($j02_data) || $j02_data == "") {
              $j02_data_dia = date('d', db_getsession('DB_datausu'));
              $j02_data_mes = date('m', db_getsession('DB_datausu'));
              $j02_data_ano = date('Y', db_getsession('DB_datausu'));
          }

          if (!isset($j02_hora) || $j02_hora == "") {
              $j02_hora = db_hora();
          }
          ?>
        <tr>
          <td nowrap title="<?=$Tj02_usuario;?>">
            <label for="j02_usuario">
              <?php
              db_ancora($Lj02_usuario, "js_pesquisaj02_usuario(true);", 3);
              ?>
            </label>
          </td>
          <td nowrap>
              <?php
              db_input('j02_usuario', 10, $Ij02_usuario, true, 'text', 3, " onchange='js_pesquisaj02_usuario(false);'");
              db_input('nome', 34, $Inome, true, 'text', 3, '');
              ?>
          </td>
        </tr>

        <tr>
          <td nowrap title="<?=$Tj02_data;?>">
              <label for="j02_data"><?=$Lj02_data;?></label>
          </td>
          <td nowrap>
              <?php
              db_inputdata('j02_data', @$j02_data_dia, @$j02_data_mes, @$j02_data_ano, true, 'text', 3, "");
              echo $Lj02_hora;
              db_input('j02_hora', 5, $Ij02_hora, true, 'text', 3, "");
              ?>
          </td>
        </tr>
      </table>
    </fieldset>

    <fieldset class="separator">
      <legend>Dados da baixa</legend>
      <table>
        <tr>
          <td nowrap title="<?=$Tj02_matric;?>">
            <label for="j02_matric">
              <?php
              db_ancora($Lj02_matric, "js_pesquisaj02_matric(true);", $db_opcao);
              ?>
            </label>
          </td>
          <td>
              <?php
              db_input('j02_matric', 10, $Ij02_matric, true, 'text', $db_opcao, " onchange='js_pesquisaj02_matric(false);'");
              db_input('j01_nome', 34, null, true, 'text', 3, '');
              ?>
          </td>
        </tr>
        <tr id="linhaProcessoSistemaInterno">
          <td colspan="2">
              <div>
                  <fieldset style='width:fit-content;' >
                      <legend>
                        <strong>Dados do Processo</strong>
                      </legend>
                      <table align="center">
                          <tr>
                              <td title="<?= @$Tp58_numero ?>">
                                  <?php
                                  db_ancora($Lp58_numero, 'js_pesquisaProcesso(true)', 1)
                                  ?>
                              </td>
                              <td>
                                  <?php
                                  db_input('p58_numero', 10, '', true, 'text', $db_opcao, "onchange='js_pesquisaProcesso(false)'", '', '', '', 20);
                                  ?>
                              </td>
                              <td>
                                <?php
                                db_input('z01_nome', 40, isset($Iz01_nome) ? $Iz01_nome : '', true, 'text', 3, "", 'z01_nomeprocesso');
                                ?>
                              </td>
                          </tr>
                          <tr>
                              <td title="Ano">Ano</td>
                              <td>
                                  <input type="number" id="anoSessao" name="anoSessao" value="<?=$anoSessao?>" style="width: 82px;" onchange='js_pesquisaProcesso(false)'>
                              </td>
                          </tr>
                          <input type="hidden" id='numeroProcessoConcatenado' name='numeroProcessoConcatenado' value=''>
                          <input type="hidden" id='p58_codproc' name='j03_codproc' value=''>
                      </table>
                  </fieldset>
              </div>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?=$Tj02_motivo;?>" colspan="2">
            <fieldset>
              <legend>Observações</legend>
              <div>
                  <?php
                  db_textarea('j02_motivo', 5, 45, $Ij02_motivo, true, 'text', $db_opcao, "");
                  ?>
              </div>
            </fieldset>
          </td>
        </tr>
      </table>
    </fieldset>
  </fieldset>
  <input name="<?=($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
         type="submit" id="db_opcao"
         value="<?=($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>"
         <?=($db_botao == false ? "disabled" : "") ?>
         onClick="return js_valida();">
  <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
</form>
<script type="text/javascript">
  const numeroProcesso = document.getElementById('p58_numero');
  const numeroProcessoConcatenado = document.getElementById("numeroProcessoConcatenado");
  const nomeProcesso = document.getElementById('z01_nomeprocesso');
  const codigoProcesso = document.getElementById('p58_codproc');

  function js_pesquisaProcesso(mostra) {
    numeroProcesso.value = numeroProcesso.value.split('/')[0];
    numeroProcessoConcatenado.value = numeroProcesso.value + '/' + anoSessao.value;
    if (mostra == true) {
        js_OpenJanelaIframe('this', 'db_iframe_nomes', 'func_protprocesso_isencao.php?funcao_js=parent.js_mostraProcesso|p58_codproc|z01_nome|p58_numero', 'Pesquisa', true, '0');
    } else {
        if (numeroProcesso.value != '') {
            js_OpenJanelaIframe('this', 'db_iframe_nomes', 'func_protprocesso_isencao.php?pesquisa_chave=' + numeroProcessoConcatenado.value + '&funcao_js=parent.js_mostraProcessoHide&sCampoPesquisa=p58_codproc', 'Pesquisa', false, '0');
        } else {
            nomeProcesso.value = '';
            codigoProcesso.value = '';
        }
    }
  }

  function js_mostraProcessoHide(chave, chave1, erro) {
      codigoProcesso.value = chave;
      nomeProcesso.value = chave1;
      if (erro == true) {
          numeroProcesso.focus();
          numeroProcesso.value = '';
          codigoProcesso.value = '';
      }
  }

  function js_mostraProcesso(chave1, chave2, chave3) {
      codigoProcesso.value = chave1;
      nomeProcesso.value = chave2;
      numeroProcesso.value = chave3.split('/')[0]
      db_iframe_nomes.hide();
  }

  function js_valida() {

    if($F('j02_matric') == '') {

      alert('Campo Matrícula do Imóvel é de preenchimento obrigatório.');
      return false;
    }

    if($F('j02_dtbaixa') == '') {

      alert('Campo Data da Baixa é de preenchimento obrigatório.');
      return false;
    }

    if($F('j02_motivo') == '') {

      alert('Campo Observações é de preenchimento obrigatório.');
      return false;
    }

    return true;
  }

  function js_pesquisaj02_matric(mostra) {

    var sTitulo = 'Pesquisa Matrícula';

    if(mostra == true) {

      js_OpenJanelaIframe(
        'CurrentWindow.corpo',
        'db_iframe_iptubase',
        'func_iptubase.php?funcao_js=parent.js_mostraiptubase1|j01_matric|z01_nome&tipoImovel=<?=$tipoImovel?>',
        sTitulo,
        mostra
      );
    } else {

      if(document.form1.j02_matric.value != '') {

        js_OpenJanelaIframe(
          'CurrentWindow.corpo',
          'db_iframe_iptubase',
          'func_iptubase.php?pesquisa_chave=' + document.form1.j02_matric.value + '&funcao_js=parent.js_mostraiptubase&tipoImovel=<?=$tipoImovel?>',
          sTitulo,
          mostra
        );
      } else {
        document.form1.j01_nome.value = '';
      }
    }
  }

  function js_mostraiptubase(chave, erro) {

    document.form1.j01_nome.value = chave;

    if(erro == true) {

      document.form1.j02_matric.focus();
      document.form1.j02_matric.value = '';
    }
  }

  function js_mostraiptubase1(chave1, chave2) {

    document.form1.j02_matric.value = chave1;
    document.form1.j01_nome.value   = chave2;

    db_iframe_iptubase.hide();
  }

  function js_pesquisaj02_usuario(mostra) {

    var sTitulo = 'Pesquisa Usuário';

    if(mostra == true) {

      js_OpenJanelaIframe(
        'CurrentWindow.corpo',
        'db_iframe_db_usuarios',
        'func_db_usuarios.php?funcao_js=parent.js_mostradb_usuarios1|id_usuario|nome',
        sTitulo,
        mostra
      );
    } else {

      if(document.form1.j02_usuario.value != '') {

        js_OpenJanelaIframe(
          'CurrentWindow.corpo',
          'db_iframe_db_usuarios',
          'func_db_usuarios.php?pesquisa_chave=' + document.form1.j02_usuario.value + '&funcao_js=parent.js_mostradb_usuarios',
          sTitulo,
          mostra
        );
      } else {
        document.form1.nome.value = '';
      }
    }
  }

  function js_mostradb_usuarios(chave, erro) {

    document.form1.nome.value = chave;

    if(erro == true) {

      document.form1.j02_usuario.focus();
      document.form1.j02_usuario.value = '';
    }
  }

  function js_mostradb_usuarios1(chave1, chave2) {

    document.form1.j02_usuario.value = chave1;
    document.form1.nome.value        = chave2;

    db_iframe_db_usuarios.hide();
  }

  function js_pesquisa() {

    js_OpenJanelaIframe(
      'CurrentWindow.corpo',
      'db_iframe_iptubaixa',
      'func_iptubaixa.php?funcao_js=parent.js_preenchepesquisa|j02_matric&tipoImovel=<?=$tipoImovel?>',
      'Pesquisa',
      true
    );
  }

  function js_preenchepesquisa(chave) {

    db_iframe_iptubaixa.hide();
      <?php
      if ($db_opcao != 1) {
          echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?tipoImovel=".$tipoImovel."&chavepesquisa='+chave ";
      }
      ?>
  }

  function js_pesquisaj03_codproc(mostra) {

    var sTitulo = 'Pesquisa Processo';

    if(mostra == true) {

      js_OpenJanelaIframe(
        'CurrentWindow.corpo',
        'db_iframe_protprocesso',
        'func_protprocesso.php?funcao_js=parent.js_mostraprotprocesso1|p58_codproc|p58_requer',
        sTitulo,
        mostra
      );
    } else {

      if(document.form1.j03_codproc.value != '') {

        js_OpenJanelaIframe(
          'CurrentWindow.corpo',
          'db_iframe_protprocesso',
          'func_protprocesso.php?pesquisa_chave=' + document.form1.j03_codproc.value
                             + '&funcao_js=parent.js_mostraprotprocesso'
                             + '&requerente=true',
          sTitulo,
          mostra
        );
      } else {
        document.form1.p58_nome.value = '';
      }
    }
  }

  function js_mostraprotprocesso(chave, erro) {

    document.form1.p58_nome.value = chave;

    if(erro == true) {

      document.form1.j03_codproc.focus();
      document.form1.j03_codproc.value = '';
    }
  }

  function js_mostraprotprocesso1(chave1, chave2) {

    document.form1.j03_codproc.value = chave1;
    document.form1.p58_nome.value    = chave2;

    db_iframe_protprocesso.hide();
  }

  $('nome').addClassName('field-size7');
</script>