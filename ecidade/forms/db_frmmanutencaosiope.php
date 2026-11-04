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

/**
 * MODULO: pessoal
 */

require_once(modification("libs/db_stdlib.php"));

$clrhpessoal->rotulo->label();
$clrhpessoalmov->rotulo->label();

$oRotulo = new rotulocampo;
$oRotulo->label("rh01_numcgm");
$oRotulo->label("z01_nome");
$oRotulo->label("rh20_cargo");
$oRotulo->label("rh01_regist");

$rh01_instit = db_getsession("DB_instit");
$sNameBotaoProcessar = "incluir";

?>

<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
  <body class="body-default">
    <div class="container">
      <form name="form1" method="post" action="">
        <fieldset>
          <legend align="left"><b>DADOS PESSOAIS</b></legend>
          <hr></hr>
          <table>    
            <tr>
                 <td nowrap title="Ano / Mês exercício">
                     <b>Exercício:</b>
                 </td>
                 <td nowrap>
                     <?php
                       db_input('rh02_anousu', 4, $Irh02_anousu, true);
                     ?>
                     &nbsp;<b>/</b>&nbsp;
                     <?php
                       db_input('rh02_mesusu', 2, $Irh02_mesusu, true);
                     ?>
                 </td>
              </tr>
            <tr>
            <td nowrap title="<?php echo $Trh01_regist; ?>">
                <label class="bold" for="rh01_regist" id="lbl_rh01_regist">
                  <?php echo $Srh01_regist; ?>:
                </label>
              </td>
              <td>
                <?php
                  db_input('rh01_regist', 10, $Irh01_regist, true, 'text', 3,'');
                ?>
              </td>
            <tr>
              <td nowrap title="Nome do funcionário">
                <label class="bold" for="z01_nome" id="lbl_z01_nome">Nome do funcionário:</label>
              </td>
              <td>
                <?php 
                  db_input('z01_nome', 100, $Iz01_nome, true, 'text', 3, ''); 
                ?>
              </td>
            </tr>
            <tr></tr>

            <?php

                if (isset($rh01_regist) && $rh01_regist > 0){

                    $sCampos = "si03_id, si03_descricao, si06_situacao, si06_segmento";
                    $sSqlSiopeManutencao = $clsiopeservidormanutencao->sql_query_dados_manutencao($rh01_regist, $sCampos);
                    $rsSiopeManutencao = $clsiopeservidormanutencao->sql_record($sSqlSiopeManutencao);
                    $db_opcao = 1;

                    if ( $clsiopeservidormanutencao->numrows > 0 ) {
                      $oDados            = db_utils::fieldsMemory($rsSiopeManutencao, 0);
                      $txtCodigoSiope    = $oDados->si03_id;
                      $txtDescricaoSiope = $oDados->si03_descricao;
                      $iSituacaoSiope    = $oDados->si06_situacao;
                      $iSegmentoSiope    = $oDados->si06_segmento;
                      $sNameBotaoProcessar = "alterar";
                      $db_opcao = 2;
                    }

                }

            ?>

            <tr>
             <td nowrap  align="left">
                <?php  db_ancora("Categoria SIOPE:", "js_pesquisacategoriasiope(true);", $db_opcao);  ?>
              </td>
              <td nowrap>
                <?php
                  db_input('txtCodigoSiope', 10, $Irh20_cargo, true, 'text', $db_opcao, "onchange='js_pesquisacategoriasiope(false);'");
                  db_input('txtDescricaoSiope', 45, null, true, 'text', 3, '');
                ?>
              </td>
            </tr>
            <tr></tr>
            <tr>
              <td nowrap  align="left">
                <strong>Situa&ccedil;&atilde;o SIOPE</strong>
              </td>
              <td nowrap>
                <?php

                  if (isset($rh01_regist) && $rh01_regist > 0){
                      $sCampos = "si01_id, si01_descricao";
                      $sSqlSiopeSituacao = $clsiopesituacao->sql_query_file(null, $sCampos, "si01_id");
                      $rsSiopeSituacao = $clsiopesituacao->sql_record($sSqlSiopeSituacao);
                      $aSituacao = array("0" => "Selecione");
    
                      if ($clsiopesituacao->numrows > 0){
                        while ($aRetornoSituacao = pg_fetch_object($rsSiopeSituacao)) {
                            $aSituacao[$aRetornoSituacao->si01_id] = $aRetornoSituacao->si01_descricao;
                        }  
                      }

                      db_select("iSituacaoSiope", $aSituacao, true, $db_opcao , "", "",  "");
                  }
                ?>
              </td>
            </tr>
            <tr></tr>
            <tr>
             <td nowrap  align="left">
                <strong>Segmento de Atuação SIOPE</strong>
             </td>
             <td nowrap>
               <?php
 
                 if (isset($rh01_regist) && $rh01_regist > 0){
                     $sCampos = "si07_segmento, si07_descricao";
                     $sSqlSiopeSegmento = $clsiopesegmentoatuacao->sql_query_file(null, $sCampos, "si07_segmento");
                     $rsSiopeSegmento = $clsiopesegmentoatuacao->sql_record($sSqlSiopeSegmento);
                     $aSegmento = array("0" => "Selecione");
                     
                     if ($clsiopesegmentoatuacao->numrows > 0){
                       while ($aRetornoSegmento = pg_fetch_object($rsSiopeSegmento)) {
                        $aSegmento[$aRetornoSegmento->si07_segmento] = $aRetornoSegmento->si07_descricao;
                       }
                     }

                     db_select("iSegmentoSiope", $aSegmento, true, $db_opcao , "", "",  "");
                 }
               ?>
             </td>
            </tr>              
            <tr></tr>
            <tr>
              <td colspan="2">
                <fieldset>
                  <legend><b>Qualificação dos profissionais de educação</b></legend>
                    <table cellspacing="0">
                      <?php
                        if (isset($rh01_regist) && $rh01_regist > 0){

                           $sSqlQualificacaoServidor = $clsiopeservidorqualificacao->sql_query_file($rh01_regist, null, "si08_qualificacao");
                           $rsQualificacaoServidor = $clsiopeservidorqualificacao->sql_record($sSqlQualificacaoServidor);
                           $aQualificacaoServidor = array();

                           if ($clsiopeservidorqualificacao->numrows > 0){
                              while ($aRetornoQualificacaoServidor = pg_fetch_object($rsQualificacaoServidor)) {
                                $aQualificacaoServidor[] = $aRetornoQualificacaoServidor->si08_qualificacao;
                              }
                           }

                           $sCampos = "si04_id, si04_descricao, si05_id, si05_descricao";
                           $sSqlQualificacao = $clsiopequalificacao->sql_query(null, $sCampos, "si05_id, si04_id");
                           $rsQualificacao = $clsiopequalificacao->sql_record($sSqlQualificacao);
                           if($clsiopequalificacao->numrows > 0){
                             $condicao = 1;
                             $iQualifGrupo = 0;
                             while ($aRetornoQualificacao = pg_fetch_object($rsQualificacao)) {
                               $condicao++;
                               $cor = $condicao%2 ? "#F7F7F7" : "#C0C0C0";
                               $bMarcado = '';
                               if (in_array($aRetornoQualificacao->si04_id, $aQualificacaoServidor)){
                                 $bMarcado = 'checked';
                               }
                                
                               if ($iQualifGrupo <> $aRetornoQualificacao->si05_id){
                                   $iQualifGrupo = $aRetornoQualificacao->si05_id;
                                   echo "
                                          <td colspan='3' class='text-center'>
                                            <h3> {$aRetornoQualificacao->si05_descricao}</h3>
                                          </td>
                                        ";
                                   $condicao++;
                                   $cor = $condicao%2 ? "#F7F7F7" : "#C0C0C0";
                               }
                                
                               echo "
                                     <tr bgcolor='{$cor}'>
                                       <td>
                                         <input type='checkbox' id='{$aRetornoQualificacao->si04_id}' name='qualificacao[]' value='{$aRetornoQualificacao->si04_id}' {$bMarcado}>
                                       </td>
                                       <td width='10'>
                                         <p>-</p>
                                       </td>
                                       <td width='800' style='padding-right: 10px'>
                                         <label for='{$aRetornoQualificacao->si04_id}'>
                                            <p align='justify'>{$aRetornoQualificacao->si04_descricao}</p>
                                         </label>
                                       </td>
                                     </tr>
                                    ";
                             }
                           }
                        }
                      ?>
                    </table>
                </fieldset>
              </td>
            </tr>

          </table>
        </fieldset>
        <input name="<?php echo $sNameBotaoProcessar; ?>" type="submit" id="db_opcao" 
               value="<?php echo ucfirst($sNameBotaoProcessar); ?>" <?php echo (!$db_botao ? "disabled" : ""); ?> >
        <input type="hidden" id='opcao' value="<?=$db_opcao?>">
        <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
      </form>
    </div>
    <?php db_menu(); ?>
  </body>
  <script>

    function js_pesquisacategoriasiope(mostra){
        if (mostra == true){
           js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_categoriasiope',
                               'func_siopecategoria.php?funcao_js=parent.js_mostraCategoriaSiope1|si03_id|si03_descricao',
                               'Pesquisa',true);
        }else{
           js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_categoriasiope',
                               'func_siopecategoria.php?pesquisa_chave='+$('txtCodigoSiope').value+'&funcao_js=parent.js_mostraCategoriaSiope',
                               'Pesquisa',false);
        }
    }
  
    function js_mostraCategoriaSiope(chave,erro){
        if(erro==true){
            $('txtCodigoSiope').value = '';
          $('txtDescricaoSiope').value = erro;
        }
        $('txtDescricaoSiope').value = chave;
    }
   
    function js_mostraCategoriaSiope1(chave1, chave2){
      $('txtCodigoSiope').value = chave1;
      $('txtDescricaoSiope').value = chave2;
      db_iframe_categoriasiope.hide();
    }

    function js_pesquisa() {
      js_OpenJanelaIframe('CurrentWindow.corpo',
                          'db_iframe_rhpessoal',
      'func_rhpessoal.php?<?=($db_opcao==2||$db_opcao==22 ? "testarescisao=ra&" : "")?>funcao_js=parent.js_preenchepesquisa|rh01_regist&instit=<?=db_getsession("DB_instit")?>',
                          'Pesquisa', true,0);
    }

    function js_preenchepesquisa(chave, iInstit){
      db_iframe_rhpessoal.hide();
      <?php
      echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
      ?>
    }


    <?php echo (isset($sPosScripts) ? $sPosScripts : ""); ?>
  </script>
</html>
