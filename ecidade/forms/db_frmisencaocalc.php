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

//MODULO: tributario
$clisencao->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("v11_descr");
$clrotulo->label("v18_isencao");
$clrotulo->label("v18_isencao");
$clrotulo->label("v46_percentual");
$clrotulo->label("v46_isencao");
$clrotulo->label("q85_descr");
$clrotulo->label("q81_cadcalc");
$clrotulo->label("nome");

$q81_cadcalc = '';
$v46_percentual = '';
$v46_isencao = '';
?>
<form name="form1" method="post">
  <fieldset>
    <legend>Cálculo selecionado</legend>

    <table border="0" width="790">
      <tr>
        <td nowrap title="<?= $Tq81_cadcalc ?>">
          <?php
          db_ancora($Lq81_cadcalc, "js_pesquisaq81_cadcalc(true);", $db_opcao);
          ?>
        </td>
        <td>
          <?php
          db_input('q81_cadcalc', 10, $Iq81_cadcalc, true, 'text', $db_opcao, " onchange='js_pesquisaq81_cadcalc(false);'");
          db_input('q85_descr', 47, $Iq85_descr, true, 'text', 3, '');
          ?>
        </td>
      </tr>

      <tr>
        <td nowrap title="<?= $Tv46_percentual ?>">
          <?= $Lv46_percentual ?>
        </td>
        <td>
          <?php
          db_input('v46_percentual', 10, 1, true, 'text', $db_opcao);
          ?>%
        </td>
      </tr>

      <tr>
        <td nowrap title="<?= $Tv46_isencao ?>">
          <?= $Lv46_isencao ?>
        </td>
        <td>
          <?php
          db_input('v18_isencao', 10, 1, true, 'text', 3);
          ?>
        </td>
      </tr>
    </table>
  </fieldset>

  <input name="incluir" type="submit" id="db_opcao" value="Incluir" onclick="return adicionaIdCalculo('')" />
  <input name="sequencialcalculo" type="hidden" id="sequencialcalculo" value="" />

  <fieldset>
      <legend>Cálculos selecionados</legend>
      <table class="form-container" border="1">
          <thead>
              <tr style="background-color: #e1e1e1">
                  <th style="width: 10px;" class="text-center">Código</th>
                  <th style="width: 10px;" class="text-left">Descrição</th>
                  <th style="width: 10px;" class="text-left">Percentual</th>
                  <th style="width: 10px;" class="text-left">Ações</th>
              </tr>
          </thead>
          <tbody>
              <?php 
              $clisencaocalc = new cl_isencaocalc;
              $sqlBuscaCalculosVinculados = $clisencaocalc->sql_query(null, '*', 'v46_isencao', "v46_isencao = $v18_isencao");
              $rsBuscaCalculosVinculados = db_query($sqlBuscaCalculosVinculados);

              $numRows = 0;

              if ($rsBuscaCalculosVinculados) {
                $numRows = pg_num_rows($rsBuscaCalculosVinculados);
              }

              for ($i = 0; $i < $numRows; $i++) {
                  db_fieldsmemory($rsBuscaCalculosVinculados, $i);

                  if ($q85_codigo) {
              ?>
                      <tr class="cores">
                          <td class="text-center field-size2"><?= $q85_codigo ?></td>
                          <td class="text-left field-size8"><?= $q85_descr ?></td>
                          <td class="text-left"><?= $v46_percentual ?>%</td>
                          <td class="text-left" style="display: flex; justify-content: center;">
                            <input name="excluir" type="submit" id="botaoExclusao" value="E" onclick="return adicionaIdCalculo('<?= $q85_codigo ?>')" />
                          </td>
                      </tr>
              <?php }
              } ?>
          </tbody>
      </table>
  </fieldset>
</form>

<script>
  function js_pesquisaq81_cadcalc(mostra) {
    if (mostra == true) {
      js_OpenJanelaIframe('', 'db_iframe_cadcalc', 'func_cadcalc.php?funcao_js=parent.js_mostracadcalc1|q85_codigo|q85_descr', 'Pesquisa', true);
    } else {
      js_OpenJanelaIframe('', 'db_iframe_cadcalc', 'func_cadcalc.php?pesquisa_chave=' + document.form1.q81_cadcalc.value + '&funcao_js=parent.js_mostracadcalc', 'Pesquisa', false);
    }
  }

  function js_mostracadcalc(chave, erro) {
    document.form1.q85_descr.value = chave;
    if (erro == true) {
      document.form1.q81_cadcalc.focus();
      document.form1.q81_cadcalc.value = '';
    }
  }

  function js_mostracadcalc1(chave1, chave2) {
    document.form1.q81_cadcalc.value = chave1;
    document.form1.q85_descr.value = chave2;
    db_iframe_cadcalc.hide();
  }

  function adicionaIdCalculo(sequencial) {
    document.form1.sequencialcalculo.value = sequencial;
  }
</script>