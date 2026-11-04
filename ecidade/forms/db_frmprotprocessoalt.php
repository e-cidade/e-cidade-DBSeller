<?php
/**
 * E-cidade Software Publico para Gestão Municipal
 *   Copyright (C) 2009 DBSeller Serviços de Informática Ltda
 *                          www.dbseller.com.br
 *                          e-cidade@dbseller.com.br
 *   Este programa é software livre; você pode redistribuí-lo e/ou
 *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 *   publicada pela Free Software Foundation; tanto a versão 2 da
 *   Licença como (a seu critério) qualquer versão mais nova.
 *   Este programa e distribuído na expectativa de ser útil, mas SEM
 *   QUALQUER GARANTIA; sem mesmo a garantia implícita de
 *   COMERCIALIZAÇÃO ou de ADEQUAÇÃO A QUALQUER PROPÓSITO EM
 *   PARTICULAR. Consulte a Licença Pública Geral GNU para obter mais
 *   detalhes.
 *   Você deve ter recebido uma cópia da Licença Pública Geral GNU
 *   junto com este programa; se não, escreva para a Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *   02111-1307, USA.
 *   Cópia da licença no diretório licenca/licenca_en.txt
 *                                 licenca/licenca_pt.txt
 */

require_once('model/protocolo/ProcessoProtocoloNumeracao.model.php');

//MODULO: protocolo
$clprotprocesso->rotulo->label();
$clprotpro = new cl_protprocesso;
$clProttipodocumentoprocesso = new cl_prottipodocumentoprocesso;
$clrotulo = new rotulocampo;

$clrotulo->label("p51_descr");
$clrotulo->label("z01_nome");
$clrotulo->label("descrdepto");

if ($p58_prottipodocumentoprocesso) {
  $sqlTipoDocumento = $clProttipodocumentoprocesso->sql_query_file($p58_prottipodocumentoprocesso, 'p91_descricao');
  $postgresObject = db_query($sqlTipoDocumento);

  if (pg_num_rows($postgresObject) > 0) {
    $p91_descricao = pg_fetch_assoc($postgresObject, 0)['p91_descricao'];
  }
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
}

if ($p58_codigo) {
  $cltipoproc = new cl_tipoproc();
  $sqlTipoDocumento = $cltipoproc->sql_query($p58_codigo, 'p91_sequencial, p91_descricao');

  db_fieldsmemory($cltipoproc->sql_record($sqlTipoDocumento), 0);
}

?>
  <script>
    function js_testa() {
      document.form1.btnalterar.value = '2';
    }

    function js_novo() {
      location.href = 'pro4_aba1protprocesso001.php';
    }
  </script>
  <fieldset>
    <legend><b>Dados Processo</b></legend>
    <center>
      <table border="0">
        <tr>
          <td nowrap title="Usuário">
            <b>Usuário:</b>
          </td>
          <td>
              <?php
              $sql = "select nome from db_usuarios where id_usuario = " . db_getsession("DB_id_usuario");
              echo pg_result(db_query($sql), 0, "nome");
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
          </td>
        </tr>
        <tr>
          <td nowrap title="<?= @$Tp58_codproc ?>">
              <?= @$Lp58_codproc; ?>
          </td>
          <td>
              <?php
              db_input('p58_codproc', 30, $Ip58_codproc, true, 'text', 3, "");
              ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?= @$Tp58_numero ?>">
              <?= @$Lp58_numero; ?>
          </td>
          <td>
              <?php
              db_input('p58_numero', 30, $Ip58_numero, true, 'text', 3, "");
              ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?= @$Tp58_dtproc; ?>">
              <?= @$Lp58_dtproc; ?>
          </td>
          <td>
              <?php
              if ($db_opcao == 3) {
                  db_inputdata('p58_dtproc', @$p58_dtproc_dia, @$p58_dtproc_mes, @$p58_dtproc_ano, false, 'text', 2, "",
                    "p58_dtproc");
              } else {
                  db_inputdata('p58_dtproc', @$p58_dtproc_dia, @$p58_dtproc_mes, @$p58_dtproc_ano, false, 'text',
                    $db_opcao, "", "p58_dtproc");
              }
              ?>
          </td>
        </tr>
        <?php
          if (ProcessoProtocoloNumeracao::getTipoConfiguracao() == ProcessoProtocoloNumeracao::TIPOORGAO) { ?>
            <tr>
              <td nowrap title="<?php echo @$Tp58_prottipodocumentoprocesso ?>">
                <?php
                  db_ancora('Tipo de Documento:', "js_pesquisa_tipo_documento_processo(true);", $db_opcao);
                ?>
              </td>
              <td nowrap>
                <?php
                  db_input('p91_sequencial', 10, $Ip91_sequencial, false, 'text', 3 ," onchange='js_pesquisa_tipo_documento_processo(false);'");
                  db_input('p91_descricao', 40, $Ip91_descricao, true, 'text', 3, '');
                ?>
              </td>
            </tr>
        <?php
          } ?>
          <?php
            $op_tip = 1;
            $pesq_p58_codigo1 = "js_pesquisap58_codigo(true)";
            $pesq_p58_codigo2 = "js_pesquisap58_codigo(false)";
            if ($db_opcao == 2) {
                $op_tip = 2;
                if (isset($p58_codproc) && trim($p58_codproc) != "") {
                    $sql_tipo = " select p61_codproc as processo1,
                            p63_codproc as processo2,
                            p67_codproc as processo3
                        from protprocesso
                            left join procandam        on procandam.p61_codproc        = protprocesso.p58_codproc
                            left join proctransferproc on proctransferproc.p63_codproc = protprocesso.p58_codproc
                            left join procarquiv       on procarquiv.p67_codproc       = protprocesso.p58_codproc
                      where protprocesso.p58_codproc = {$p58_codproc}
                        and procandam.p61_codproc is null
                        and proctransferproc.p63_codproc is null
                        and procarquiv.p67_codproc is null ";

                    $result_tipo = $clprotpro->sql_record($sql_tipo);
                    if ($clprotpro->numrows == 0) {
                        $op_tip = 3;
                    }
                }
            } else {
                if ($db_opcao == 3) {
                    $op_tip = 3;
                }
            }
          ?>
        <tr>
          <td nowrap title="<?= @$Tp58_codigo ?>">
              <?php
                if (ProcessoProtocoloNumeracao::getTipoConfiguracao() == ProcessoProtocoloNumeracao::TIPOORGAO) {
                  //= db_ancora(@$Lp58_codigo, 'js_pesquisap58_codigo(true)', $op_tip);
                  $op_tip_tipoprocesso = 1;
                  if (!$p58_codigo) {
                    $op_tip_tipoprocesso = 3;
                  }

                  $styleAncoraTipoProcesso = 'display:none';
                  $styleSpanTipoProcesso = 'display:inline-block';

                  if (!empty($p58_codigo)) {
                    $styleAncoraTipoProcesso = 'display:inline-block';
                    $styleSpanTipoProcesso = 'display:none';
                  }

                  echo db_ancora(@$Lp58_codigo, "js_pesquisap58_codigo(true)", $op_tip, $styleAncoraTipoProcesso, 'ancora_tipoprocesso');

                  echo '<span id="span_tipoprocesso" style="'. $styleSpanTipoProcesso .'">'. @$Lp58_codigo . '</span>';
                } else {
                  echo db_ancora(@$Lp58_codigo, "js_pesquisap58_codigo(true)", $op_tip, '', 'ancora_tipoprocesso');
                }
              ?>
          </td>
          <td>
              <?php
              db_input('p58_codigo', 5, $Ip58_codigo, true, 'text', $op_tip_tipoprocesso, " 'js_pesquisap58_codigo(false)'")
              ?>
              <?php
              db_input('p51_descr', 40, $Ip51_descr, true, 'text', 3, '');
              if ($db_opcao == 1) {
                  $p58_hora = db_hora();
                  db_input('p58_hora', 60, @$Ip58_hora, true, 'hidden', '', '');
              }
              ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?= @$Tp58_hora; ?>">
              <?= @$Lp58_hora; ?>
          </td>
          <td>
              <?php
              if ($db_opcao == 1) {
                  $p58_hora = db_hora();
                  db_input('p58_hora', 7, @$Ip58_hora, true, 'text', '3', '');
              } else {
                  db_input('p58_hora', 7, $Ip58_hora, true, 'text', 3);
              }
              ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?= @$Tp58_numcgm ?>">
              <?php
              db_ancora(@$Lp58_numcgm, "js_pesquisap58_numcgm(true);", $db_opcao);
              ?>
          </td>
          <td>
              <?php
              db_input('p58_numcgm', 5, $Ip58_numcgm, true, 'text', 3, " onchange='js_pesquisap58_numcgm(false);'");

              db_input('z01_nome', 40, $Iz01_nome, true, 'text', 3, '');
              ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?= @$Tp58_requer ?>">
              <?= @$Lp58_requer ?>
          </td>
          <td>
              <?php
              db_input('p58_requer', 50, $Ip58_requer, true, 'text', $db_opcao, "")
              ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?= $Tp58_obs ?>" colspan='2'>
            <fieldset class="separator">
              <legend><?= $Lp58_obs ?></legend>

                <?php
                db_textarea('p58_obs', 5, 80, $Ip58_obs, true, 'text', $db_opcao, "")
                ?>
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
                  <td>
                      <?php
                      if ($db_opcao != 22) {
                          $funcaojava = null;
                          $clprocvar = new cl_procvar;
                          $cldb_syscampo = new cl_db_syscampo;

                          $result_andpadrao = $clandpadrao->sql_record($clandpadrao->sql_query($p58_codigo));
                          if ($clandpadrao->numrows == 0) {
                              echo "<script> \n
         alert('Tipo de processo nao configurado corretamente! Contate suporte!');
         location.href = 'pro4_aba1protprocesso001.php'; \n
  </script>";
                          }


                          $result = $clprocvar->sql_record($clprocvar->sql_query($p58_codigo));
                          if ($clprocvar->numrows > 0) {
                              for ($i = 0; $i < $clprocvar->numrows; $i++) {
                                  db_fieldsmemory($result, $i);
                                  $rscampo = $cldb_syscampo->sql_record($cldb_syscampo->sql_query($p54_codcam));
                                  db_fieldsmemory($rscampo, 0);
                                  $clrotulo->label("$nomecam");
                                  if ($db_opcao == 2) {
                                      $sql1 = "select p55_conteudo from proctipovar where p55_codproc = $p58_codproc and p55_codcam = $p54_codcam";
                                      $rsq = db_query($sql1);
                                      if (pg_num_rows($rsq) > 0) {
                                          $$nomecam = pg_result($rsq, 0, "p55_conteudo");
                                      }
                                  }
                                  $jl = "L" . $nomecam;
                                  echo "<tr>";
                                  echo "<td>" . $$jl . "</td>";
                                  $xc = $conteudo;
                                  $ji = "I$nomecam";
                                  if (substr($xc, 0, 4) != "date") {
                                      if ((substr($xc, 0, 3) == "cha") || (substr($xc, 0, 3) == "var") || (substr($xc,
                                            0, 3) == "flo")) {
                                          echo "<td>";
                                          db_input("$nomecam", $tamanho, $$ji, true, 'text', $db_opcao, $funcaojava);
                                          echo "</td></tr>";
                                      } else {
                                          if (substr($xc, 0, 3) == "boo") {
                                              $x = array("f" => "NAO", "t" => "SIM");
                                              echo "<td>";
                                              db_select("$nomecam", $x, true, $db_opcao, $funcaojava);
                                              echo "</td></tr>";
                                          } else {
                                              if (substr($xc, 0, 3) == "tex") {
                                                  echo "<td>";
                                                  db_textarea("$nomecam", 0, 0, $$ji, true, 'text', $db_opcao,
                                                    $funcaojava);
                                                  echo "</td></tr>";
                                              } else {
                                                  echo "<td>";
                                                  db_input("$nomecam", $tamanho, $$ji, true, 'text', $db_opcao,
                                                    $funcaojava);
                                                  echo "</td></tr>";
                                              }
                                          }
                                      }

                                  } else {
                                      $dia = substr($$nomecam, 0, 2);
                                      $mes = substr($$nomecam, 3, 2);
                                      $ano = substr($$nomecam, 6, 4);
                                      echo "<td>";
                                      db_inputdata("$nomecam", @$dia, @$mes, @$ano, true, 'text', $db_opcao,
                                        $funcaojava);
                                      echo "</td></tr>";
                                  }
                              }
                          }
                          $chamacgm = true;
                      }
                      ?>
              </table>
          </td>
        </tr>
        <tr>
          <td colspan="3" valign='top'>
              <?php
              $cldoc = new cl_procdoctipo;
              $res = $cldoc->sql_record($cldoc->sql_query(@$p58_codigo, "", "p56_coddoc,p56_descr"));

              if ($cldoc->numrows > 0) {
                  echo "<fieldset>";
                  if ($db_opcao == 1) {
                      if (@$p58_codigo != "") {
                          if ($cldoc->numrows > 0) {
                              echo "<b>DOCUMENTOS</b><br>";
                              $ndocs = "";
                              for ($x = 0; $x < $cldoc->numrows; $x++) {
                                  db_fieldsmemory($res, $x);
                                  echo "<input type='checkbox' name='doc$x' onClick='js_valor()' value='$p56_coddoc'><b>$p56_descr</b><br>";
                                  $ndocs .= $p56_coddoc . "#";
                              }
                          }
                      }
                  } else {
                      if ($db_opcao == 2) {

                          if (isset($btnalterar) && $btnalterar == 2) {
                              if (@$p58_codproc != "") {
                                  $sqldoc = "select coalesce(p81_doc, false) as p81_doc, ";
                                  $sqldoc .= "       p56_coddoc, ";
                                  $sqldoc .= "       p56_descr ";
                                  $sqldoc .= "  from procdoctipo ";
                                  $sqldoc .= "       inner join procdoc          on p56_coddoc  = p57_coddoc ";
                                  $sqldoc .= "       left  join procprocessodoc  on p81_coddoc  = p57_coddoc ";
                                  $sqldoc .= "                                  and p81_codproc = $p58_codproc ";
                                  $sqldoc .= " where p57_codigo = $p58_codigo ";

                                  $res = $cldoc->sql_record($sqldoc);

                                  if ($cldoc->numrows > 0) {
                                      echo "<b>DOCUMENTOS</b><br>";
                                      $docs = "";
                                      $ndocs = "";
                                      for ($x = 0; $x < $cldoc->numrows; $x++) {
                                          db_fieldsmemory($res, $x);
                                          echo "<input type='checkbox' name='doc$x' " . ($p81_doc == 't' ? 'checked' : '') . " onClick='js_valor()'
                             value='$p56_coddoc'><b>$p56_descr</b><br>";
                                          if ($p81_doc == 't') {
                                              $docs .= $p56_coddoc . "#";
                                          } else {
                                              $ndocs .= $p56_coddoc . "#";
                                          }
                                      }
                                  }
                              }

                          } else {

                              if (@$p58_codigo != "") {
                                  $res = $cldoc->sql_record($cldoc->sql_query($p58_codigo, "", "p56_coddoc,p56_descr"));
                                  if ($cldoc->numrows > 0) {
                                      echo "<b>DOCUMENTOS</b><br>";
                                      for ($x = 0; $x < $cldoc->numrows; $x++) {
                                          db_fieldsmemory($res, $x);
                                          echo "<input type='checkbox' name='doc$x' onClick='js_valor()' value='$p56_coddoc'>
                        <b>$p56_descr</b><br>";
                                      }
                                  }
                              }
                          }
                      }
                  }
                  echo "</fieldset>";
              }
              db_input('docs', 50, $Ip58_codproc, true, 'hidden', 3, "");
              db_input('ndocs', 50, $Ip58_codproc, true, 'hidden', 3, "");
              db_input('btnalterar', 10, "", true, 'hidden', 3);
              ?>
          </td>
        </tr>
      </table>
    </center>
    <input name="db_opcao" type="submit" id="db_opcao"
           value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 ? "Alterar" : "Alterar")) ?>"
           onclick="js_testa()" <?= ($db_botao == false ? "disabled" : "") ?>>

    <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();"
      <?= ($db_opcao == 1 ? "disabled" : "") ?>>

    <input name="novo" type="button" id="novo" value="Novo" onclick="js_novo();"
      <?= ($db_opcao == 1 ? "disabled" : "") ?>>

    <input type="button" id="btnAnexarDocumento" value="Anexar Documento"/>
  </fieldset>
  <script>

    $("btnAnexarDocumento").observe("click", function() {

      if($F("p58_codproc").trim() == "") {
        alert("Número do processo não informado.");
        return false;
      }
      js_OpenJanelaIframe("", "iframe_processo_documento", "prot4_processodocumento001.php?iCodigoProcesso=" + $F("p58_codproc"), "Anexar Documento", true);
    });

    function js_valor() {
      var cods = '';
      var ncods = '';

      for(i = 0; i < document.form1.length; i++) {
        if(document.form1.elements[i].type == "checkbox") {
          if(document.form1.elements[i].checked == true) {
            cods += document.form1.elements[i].value + "#";
          } else {
            ncods += document.form1.elements[i].value + '#';
          }
        }
      }
      document.form1.docs.value = cods;
      document.form1.ndocs.value = ncods;
    }

    function js_pesquisap58_codigo(mostra) {
      var parametros = '';
      if ($('p91_sequencial')) {
        var parametros = `&p91_sequencial=${$('p91_sequencial').value}`;
      }

      if(mostra == true) {
        js_OpenJanelaIframe(
          '',
          'db_iframe',
          'func_tipoproc.php?grupo=1&funcao_js=parent.js_mostratipoproc1|0|1' + parametros,
          'Pesquisa Tipo',
          mostra
        );
      } else {
        var p58_codigo = document.form1.p58_codigo.value;

        js_OpenJanelaIframe(
          '',
          'db_iframe',
          'func_tipoproc.php?grupo=1&pesquisa_chave=' + p58_codigo + '&funcao_js=parent.js_mostratipoproc' + parametros,
          'Pesquisa Tipo',
          mostra
        );
      }

      parent.document.formaba.dadosprocesso.disabled = false;
      parent.document.formaba.processosapensados.disabled = true;
    }

    function js_pesquisa_tipo_documento_processo(mostra){
      var url = "func_prottipodocumentoprocesso.php";
      var parametros = "?funcao_js=parent.js_mostra_tipo_documento_processo";

      parametros += !mostra ? `&pesquisa_chave=${$('p91_sequencial').value}` : '|0|1';

      js_OpenJanelaIframe("", "iframe_tipodocumento", url + parametros, "Pesquisa Tipo de Documento", mostra);
    }

    function js_mostra_tipo_documento_processo(chave1, chave2) {
      $('p91_sequencial').value = chave1;
      $('p91_descricao').value = chave2;

      if (chave1) {
        $('p58_codigo').readOnly = false;
        $('p58_codigo').style.backgroundColor = "";

        $('span_tipoprocesso').style.display = 'none';
        $('ancora_tipoprocesso').style.display = 'inline-block';

      } else {
        $('p58_codigo').readOnly = true;
        $('p58_codigo').style.backgroundColor = "rgb(222, 184, 135)";

        $('span_tipoprocesso').style.display = 'inline-block';
        $('ancora_tipoprocesso').style.display = 'none';
      }

      $('p58_codigo').value = '';
      $('p51_descr').value = '';

      iframe_tipodocumento.hide();
      js_pesquisap58_codigo(true);

    }

    function js_mostratipoproc(chave, erro) {
      document.form1.p51_descr.value = chave;
      if(erro == true) {
        document.form1.p58_codigo.focus();
        document.form1.p58_codigo.value = '';
      }
      parent.document.formaba.dadosprocesso.disabled = false;
      parent.document.formaba.processosapensados.disabled = false;
    }

    function js_mostratipoproc1(chave1, chave2) {
      document.form1.p58_codigo.value = chave1;
      document.form1.p51_descr.value = chave2;
      document.form1.btnalterar.value = '';
      document.form1.submit();
      db_iframe.hide();
      parent.document.formaba.dadosprocesso.disabled = false;
      parent.document.formaba.processosapensados.disabled = false;
    }

    function js_pesquisap58_numcgm(mostra) {
      if(mostra == true) {
        js_OpenJanelaIframe(
          '',
          'db_iframe',
          'func_nome.php?funcao_js=parent.js_mostracgm1|0|1&testanome=true&incproc=true',
          'Pesquisa CGM',
          mostra
        );
      } else {
        var p58_numcgm = document.form1.p58_numcgm.value;

        js_OpenJanelaIframe(
          '',
          'db_iframe',
          'func_nome.php?pesquisa_chave=' + p58_numcgm + '&funcao_js=parent.js_mostracgm',
          'Pesquisa CGM',
          mostra
        );
      }

      parent.document.formaba.dadosprocesso.disabled = false;
      parent.document.formaba.processosapensados.disabled = true;
    }

    function js_mostracgm(erro, chave) {
      document.form1.z01_nome.value = chave;
      document.form1.p58_requer.value = chave2;
      if(erro == true) {
        document.form1.p58_numcgm.focus();
        document.form1.p58_numcgm.value = '';
      }
      parent.document.formaba.dadosprocesso.disabled = false;
      parent.document.formaba.processosapensados.disabled = false;
    }

    function js_mostracgm1(chave1, chave2) {
      document.form1.p58_numcgm.value = chave1;
      document.form1.z01_nome.value = chave2;
      document.form1.p58_requer.value = chave2;
      db_iframe.hide();
      parent.document.formaba.dadosprocesso.disabled = false;
      parent.document.formaba.processosapensados.disabled = false;
    }

    function js_pesquisap58_coddepto(mostra) {
      if(mostra == true) {
        js_OpenJanelaIframe(
          '',
          'db_iframe',
          'func_db_depart.php?funcao_js=parent.js_mostradb_depart1|0|z01_nome',
          'Pesquisa Departamento',
          mostra
        );
      } else {
        var p58_coddepto = document.form1.p58_coddepto.value;

        js_OpenJanelaIframe(
          '',
          'db_iframe',
          'func_db_depart.php?pesquisa_chave=' + p58_coddepto + '&funcao_js=parent.js_mostradb_depart',
          'Pesquisa Departamento',
          mostra
        );
      }

      parent.document.formaba.dadosprocesso.disabled = false;
      parent.document.formaba.processosapensados.disabled = true;
    }

    function js_mostradb_depart(chave, erro) {
      document.form1.descrdepto.value = chave;
      if(erro == true) {
        document.form1.p58_coddepto.focus();
        document.form1.p58_coddepto.value = '';
      }
      parent.document.formaba.dadosprocesso.disabled = false;
      parent.document.formaba.processosapensados.disabled = false;
    }

    function js_mostradb_depart1(chave1, chave2) {
      document.form1.p58_coddepto.value = chave1;
      document.form1.descrdepto.value = chave2;
      db_iframe.hide();
      parent.document.formaba.dadosprocesso.disabled = false;
      parent.document.formaba.processosapensados.disabled = false;
    }

    function js_pesquisa() {

      const url = "func_protprocessodeptoatual.php?processopai=0&grupo=1&funcao_js=parent.js_preenchepesquisa|0|1";
      js_OpenJanelaIframe("", "db_iframeprocesso", url, "Pesquisa ", true);
      parent.document.formaba.dadosprocesso.disabled = false;
      parent.document.formaba.processosapensados.disabled = true;
    }

    function js_preenchepesquisa(chave1, chave2) {
      if (typeof db_iframeprocesso !== 'undefined') {
        db_iframeprocesso.hide();
      }

      location.href = 'pro4_aba1protprocesso002.php?chavepesquisa=' + chave1 + '&p58_numcgm=' + chave2;
      js_processosapensados(chave1);
      parent.document.formaba.dadosprocesso.disabled = false;
      parent.document.formaba.processosapensados.disabled = false;
    }

    function js_processosapensados(chave) {
      var sUrl = "pro4_aba2protprocesso001.php?p58_codproc=" + chave;
      parent.iframe_processosapensados.location.href = sUrl;
    }

    <?php
    if (isset($p58_ano)) {
        echo "document.form1.p58_numero.value = '" . $p58_numero . "/" . $p58_ano . "'";
    }
    ?>

    function js_validaObservacao() {

      var sMensagem = 'Aviso:\n Você informou no campo observação mais de 500 caracteres, pode ser que na capa de processo não conste todas informações.\n';
      sMensagem += 'Deseja salvar assim mesmo?';
      if($F('p58_obs').length > 500 && !confirm(sMensagem)) {
        return false;
      }

      var quebraLinhas = $F('p58_obs').match(/(\r\n|\n|\r)/gm);
      if (quebraLinhas.length > 4) {
          alert('Você informou no campo observação mais de 4 quebras de linhas.');
          return false;
      }
      return true;
    }

  </script>
<?php

if ($db_opcao == 22) {
    echo "<script>
        onload = js_pesquisa();
        </script>";
    $chamacgm = false;
}
