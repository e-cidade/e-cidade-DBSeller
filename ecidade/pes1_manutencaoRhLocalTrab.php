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

require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_libpessoal.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_rhlocaltrab_classe.php"));
require_once(modification("classes/db_rhlocaltrabcustoplano_classe.php"));

db_postmemory($HTTP_POST_VARS);

$clrhlocaltrab                    = new cl_rhlocaltrab();
$clrhlocaltrabagentesnocivos      = new cl_rhlocaltrabagentesnocivos();
$clrhlocaltrabequipamentoprotecao = new cl_rhlocaltrabequipamentoprotecao();
$clrhlocaltrabregistroambiental   = new cl_rhlocaltrabregistroambiental();
$cldb_estrut                      = new cl_db_estrut();
$cldb_uf                          = new cl_db_uf();

$clrhlocaltrab->rotulo->label();
$clrhlocaltrabagentesnocivos->rotulo->label();
$clrhlocaltrabequipamentoprotecao->rotulo->label();
$clrhlocaltrabregistroambiental->rotulo->label();

$clrotulo = new rotulocampo;
$clrotulo->label("rh86_criteriorateio");
$clrotulo->label("cc08_descricao");

$aParamKeys  = array(
    "cc09_anousu" => db_getsession("DB_anousu"),
    "cc09_instit" => db_getsession("DB_instit"),
);
$aParametrosCustos   = db_stdClass::getParametro("parcustos",$aParamKeys);
$iTipoControleCustos = 0;

if (count($aParametrosCustos) > 0) {
    $iTipoControleCustos = $aParametrosCustos[0]->cc09_tipocontrole;
}

/*
 * Variavel de controle cadastrada na funcao do item de menu
 * Caso não seja econtrada assume o comportamento de cadastro
 */
if (!isset($db_opcao)) {
    $db_opcao = 1;
}

?>
<html lang="pt-br">
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style>
      #frmLocalTrabalho select { width:295px; }
    </style>    
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<br><br>
<div align="center">
<form id="frmLocalTrabalho" name="form1" method="post" action="">
<fieldset style="width: 80%">
<legend>Cadastro de Local de Trabalho</legend>
<table style="width: 100%">
  <tr>
    <td>
      <fieldset id="fieldset_dados_local_trabalho">
        <legend> Dados do Local de Trabalho </legend>
        <table border="0">
          <tr>
            <td nowrap title="<?=@$Trh55_codigo?>">
              <?=@$Lrh55_codigo?>
            </td>
            <td> 
              <?php
              db_input('rh55_codigo',10,$Irh55_codigo,true,'text',3,"")
              ?>
            </td>
          </tr>
          <tr>
            <?php
            $retorno_cfpess = db_sel_cfpess(db_anofolha(),db_mesfolha(),"r11_localtrab");
            if (isset($r11_localtrab) && trim($r11_localtrab) != "") {
              $cldb_estrut->autocompletar = true;
              $cldb_estrut->mascara       = true;
              $cldb_estrut->reload        = false;
              $cldb_estrut->input         = false;
              $cldb_estrut->size          = 10;
              $cldb_estrut->nome          = "rh55_estrut";
              $cldb_estrut->db_opcao      = ($db_opcao!=1?3:1);
              $cldb_estrut->db_mascara("$r11_localtrab");
            } else {
              $erro_msg = 'Estrutural de locais de trabalho não configurados no cfpess para o ano / mês '.db_anofolha().' / '.db_mesfolha().'. Verifique!';
            }
            ?>
          </tr>
          <tr>
            <td nowrap title="<?=@$Trh55_inep?>">
              <?=@$Lrh55_inep?>
            </td>
            <td> 
              <?php
              db_input('rh55_inep', 10, $Irh55_inep, true, 'text', $db_opcao, "")
              ?>
            </td>
          </tr>          
          <tr>
            <td nowrap title="<?=@$Trh55_descr?>">
              <?=@$Lrh55_descr?>
            </td>
            <td> 
              <?php
              db_input('rh55_descr',44,$Irh55_descr,true,'text',$db_opcao,"")
              ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="Criterio Rateio">
               <?php
               db_ancora("Criterio Rateio","adicionaCentroCusto('');",$db_opcao); 
               ?>
            </td>
            <td> 
              <?php
              db_input('rh86_criteriorateio',10,$Irh86_criteriorateio,true,'text',$db_opcao," onchange='adicionaCentroCusto(this.value);'");
              db_input('cc08_descricao',30,$Icc08_descricao,true,'text',3,'')
               ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="Lotação Tributária">
               <?php
               db_ancora("Lotação Tributária","pesquisaLotacaoTributaria(true);",$db_opcao); 
               ?>
            </td>
            <td> 
              <?php
              db_input('rh55_lotacaotributaria',10,$Irh55_lotacaotributaria,true,'text',3,"");
               ?>
            </td>
          </tr>
        </table>           
        
      </fieldset>
    </td>
  </tr>
  
  <!-- INICIO Dados eSocial (Evento S 2240 - Condições Ambientais do Trabalho -->
  <tr>
    <td>
      <fieldset id="fieldset_esocial">
        <legend>Dados eSocial (Evento S 2240 - Condições Ambientais do Trabalho</legend>
        <table style="width: 100%">
        
          <!-- INICIO Dados do Local e Condições Ambientais do Trabalho-Agentes Nocivos -->
          <tr>
            <td>
              <fieldset id="fieldset_esocial_condicoes_ambientais">
                <legend>Dados do Local e Condições Ambientais do Trabalho-Agentes Nocivos</legend>
                <table style="width: 100%">
                  <tr>
                    <td width="520px;">Tipo de local de Trabalho</td>
                    <td>
                      <?php
                        $aTiposLocaisTrabalho = array("0"=>"","1"=>"1 - Urbano","2"=>"2 - Rural");
                        db_select("rh55_tipolocal", $aTiposLocaisTrabalho, true, $db_opcao);
                      ?>
                    </td>
                  </tr>
                  <tr>  
                    <td>Endereço</td>
                    <td>
                      <?php
                        db_input("rh55_endereco",40,$Irh55_endereco,true,'text',$db_opcao);
                      ?>
                    </td>                  
                  </tr>
                  <tr>
                    <td>Tipo de Estabelecimento</td>
                    <td>
                     <?php
                       $aTiposEstabelecimentos = array("0"=>"","1"=>"1 - Estabelecimento do próprio empregador","2"=>"2 - Estabelecimento de Terceiros");
                       db_select("rh55_tipoestabelecimento", $aTiposEstabelecimentos, true, $db_opcao);
                     ?>
                    </td>
                  </tr>
                  <tr>  
                    <td>Código correspondente o tipo de inscrição</td>
                    <td>
                     <?php
                       $aCodigosInscricao = array("0"=>"","1"=>"1 - CNPJ","3"=>"3 - CAEPF","4"=>"4 - CNO");
                       db_select("rh55_tipoinscricao", $aCodigosInscricao, true, $db_opcao);
                     ?>                    
                    </td>
                  </tr>
                  <tr>  
                    <td>Número de Inscrição</td>
                    <td>
                      <?php
                        db_input("rh55_numeroinscricao",40,$Irh55_numeroinscricao,true,'text',$db_opcao);
                      ?>
                    </td>
                  </tr>
                </table>
              </fieldset>              
            </td>
          </tr>
          <!-- Fim Dados do Local e Condições Ambientais do Trabalho-Agentes Nocivos -->
          
          <!-- INICIO Agente(s) nocivo(s) ao(s) qual(is) o trabalhador está exposto, conforme tabela 24 do eSocial -->
          <tr>
            <td>
              <fieldset id="fieldset_esocial_agentes_nocivos">
                <legend>Agente(s) nocivo(s) ao(s) qual(is) o trabalhador está exposto, conforme tabela 24 do eSocial</legend>
                <table style="width: 100%">
                  <tr>
                    <td>
                      <fieldset>
                        <legend>Dados do agente nocivo</legend>
                        <table style="width: 100%" border=0>
                          <tr>
                            <td width="500px;">
                              <?php
                                db_ancora("<b>Agente Nocivo:</b>","pesquisaOpcoes('eventoS2240_tabela24','rh256_agentenocivo')",$db_opcao);
                              ?>
                            </td>
                            <td>
                              <?php
                                db_input("rh256_agentenocivo",10,$Irh256_agentenocivo,true,'hidden',$db_opcao);
                                db_input("rh256_agentenocivo_descricao",40,0,true,'text',3);
                              ?>
                            </td>
                           </tr>
                          <tr>                      
                            <td>Tipo de Avaliação do Agente Nocivo:</td>
                            <td>
                              <?php
                                $aTiposAvaliacoesAgentesNocivos = array("0"=>"","1"=>"1 - Critério quantitativo","2"=>"2 - Critério qualitativo");
                                db_select("rh256_tipoavaliacao", $aTiposAvaliacoesAgentesNocivos, true, $db_opcao);
                              ?>
                            </td>                    
                          </tr>
                          <tr>
                            <td>Intensidade, concentração ou dose da exposição do trabalhador ao agente nocivo, caso seja quantitativo:</td>
                            <td>
                              <?php
                                db_input("rh256_intensidadeconcentracao",40,$Irh256_intensidadeconcentracao,true,'text',$db_opcao);
                              ?>
                            </td> 
                          </tr>
                          <tr>                     
                            <td>Limite de tolerância calculado para agentes específicos:</td>
                            <td>
                              <?php
                                db_input("rh256_tolerancialimite",40,$Irh256_tolerancialimite,true,'text',$db_opcao);
                              ?>
                            </td>                    
                          </tr>
                          <tr> 
                            <td>
                              <?php 
                                db_ancora("<b>Dose ou unidade de medida da intensidade ou concentração do agente:</b>",
                                          "pesquisaOpcoes('eventoS2240_opcoes_unMed',
                                                          'rh256_medida')",
                                                          $db_opcao);
                              ?>
                            </td>
                            <td>
                              <?php
                                db_input("rh256_medida",10,$Irh256_medida,true,'hidden',$db_opcao);
                                db_input("rh256_medida_descricao",40,0,true,'text',3);
                              ?>
                            </td>
                          </tr>                          
                          <tr>                     
                            <td>Técnica utilizada para medição da intensidade ou concentração:</td>
                            <td>
                              <?php
                                db_input("rh256_tecnicamedicao",40,$Irh256_tecnicamedicao,true,'text',$db_opcao);
                              ?>
                            </td>                    
                          </tr>
                          <tr>                     
                            <td>Em caso de agente nocivo incluído por determinação administrativa ou judicial, preencher com o número do processo:</td>
                            <td>
                              <?php
                                db_input("rh256_processo",21,$Irh256_processo,true,'text',$db_opcao);
                              ?>
                            </td>                    
                          </tr>
                          <tr>
                           <td colspan="2" >
                              <!-- INICIO Informações Relativas a Equipamentos de Proteção Coletiva-EPC e Equipamentos de Proteção Individual-EPI -->                        
                              <table style="width: 100%">
                               <tr>
                                 <td>
                                   <fieldset id="fieldset_esocial_equipamentos_protecao">
                                     <legend>Informações Relativas a Equipamentos de Proteção Coletiva-EPC e Equipamentos de Proteção Individual-EPI</legend>
                                     <table style="width: 100%" cellpadding="3">
                                       <tr>
                                         <td width="520px;">
                                           O empregador implementa medidas de proteção coletiva (EPC) para eliminar ou reduzir a exposição dos trabalhadores
                                           ao agente nocivo?
                                         </td>
                                         <td valign="bottom">
                                           <?php
                                             $aOpcoes = array("0"=>"Não se aplica", "1"=>"Não implementa", "2"=>"Implementada");
                                             db_select("rh257_utilizaepc", $aOpcoes, true, $db_opcao);
                                           ?>                    
                                         </td>
                                       </tr>
                                       <tr>
                                         <td>Os EPCs são eficazes na neutralização do risco ao trabalhador?</td>
                                         <td valign="bottom">
                                           <?php
                                             $aOpcoes = array("0"=>"","S"=>"Sim", "N"=>"Não");
                                             db_select("rh257_eficaciaepc", $aOpcoes, true, $db_opcao);
                                           ?>                    
                                         </td>
                                       </tr>
                                       <tr>
                                         <td>Utilização de EPI:</td>
                                         <td valign="bottom">
                                           <?php
                                             $aOpcoes = array("0"=>"Não se aplica", "1"=>"Não utilizado", "2"=>"Utilizado");
                                             db_select("rh257_utilizaepi", $aOpcoes, true, $db_opcao, "onchange='mostraFormCadastroEPI()'");
                                           ?>                    
                                         </td>
                                       </tr>   
                                       <tr>
                                         <td>Os EPIs são eficazes na neutralização do risco ao trabalhador?</td>
                                         <td valign="bottom">
                                           <?php
                                             $aOpcoes = array("0"=>"","S"=>"Sim", "N"=>"Não");
                                             db_select("rh257_eficaciaepi", $aOpcoes, true, $db_opcao);
                                           ?>                    
                                         </td>
                                       </tr>
                                       <tr>
                                         <td>
                                           Foi tentada a implementação de medidas de proteção coletiva, de caráter administrativo ou de organização, optando-se 
                                           pelo EPI por inviabilidade técnica, insuficiência ou interinidade, ou ainda em caráter complementar ou emergencial?
                                         </td>
                                         <td valign="bottom">
                                           <?php
                                             $aOpcoes = array("0"=>"","S"=>"Sim", "N"=>"Não");
                                             db_select("rh257_medidaprotecaoepi", $aOpcoes, true, $db_opcao);
                                           ?>                    
                                         </td>
                                       </tr>                                   
                                       <tr>
                                         <td>
                                           Foram observadas as condições de funcionamento do EPI ao longo do tempo, conforme especificação técnica do fabricante 
                                           nacional ou importador, ajustadas às condições de campo?
                                         </td>
                                         <td valign="bottom">
                                           <?php
                                             $aOpcoes = array("0"=>"","S"=>"Sim", "N"=>"Não");
                                             db_select("rh257_funcionamentoepi", $aOpcoes, true, $db_opcao);
                                           ?>                    
                                         </td>
                                       </tr>
                                       <tr>
                                         <td>
                                           Foi observado o uso ininterrupto do EPI ao longo do tempo, conforme especificação técnica do fabricante nacional 
                                           ou importador, ajustadas às condições de campo?
                                         </td>
                                         <td valign="bottom">
                                           <?php
                                             $aOpcoes = array("0"=>"","S"=>"Sim", "N"=>"Não");
                                             db_select("rh257_usoininterruptoepi", $aOpcoes, true, $db_opcao);
                                           ?>                    
                                         </td>
                                       </tr>
                                       <tr>
                                         <td>
                                           Foi observado o prazo de validade do CA no momento da compra do EPI?
                                         </td>
                                         <td valign="bottom">
                                           <?php
                                             $aOpcoes = array("0"=>"","S"=>"Sim", "N"=>"Não");
                                             db_select("rh257_validadeepi", $aOpcoes, true, $db_opcao);
                                           ?>                    
                                         </td>
                                       </tr>
                                       <tr>
                                         <td>
                                           É observada a periodicidade de troca definida pelo fabricante nacional ou importador e/ou programas ambientais, 
                                           comprovada mediante recibo assinado pelo usuário em época própria?
                                         </td>
                                         <td valign="bottom">
                                           <?php
                                             $aOpcoes = array("0"=>"","S"=>"Sim", "N"=>"Não");
                                             db_select("rh257_periodicidadeepi", $aOpcoes, true, $db_opcao);
                                           ?>                    
                                         </td>
                                       </tr>                                                                        
                                       <tr>
                                         <td>
                                           É observada a higienização conforme orientação do fabricante nacional ou importador?
                                         </td>
                                         <td valign="bottom">
                                           <?php
                                             $aOpcoes = array("0"=>"","S"=>"Sim", "N"=>"Não");
                                             db_select("rh257_higienizacaoepi", $aOpcoes, true, $db_opcao);
                                           ?>                    
                                         </td>
                                       </tr>
                              
                                       <tr>
                                         <td colspan="2" id="tdCadastroEPIs" style="display: none;">
                                           <fieldset id="fieldset_esocial_equipamentos_protecao_epi">
                                             <legend>Cadastros de EPI's</legend>
                                             <table style="width: 100%">
                                               <tr>
                                                 <td>
                                                   <fieldset>
                                                     <legend>Dados do EPI</legend>
                                                     <table style="width: 100%">
                                                      <tr>
                                                        <td width=500px;>Certificado de Aprovação - CA ou documento de avaliação do EPI:</td>
                                                        <td>
                                                          <?php
                                                            db_input("rh259_documentoavaliacao",40,$Irh259_documentoavaliacao,true,'text',$db_opcao);
                                                          ?>                    
                                                        </td>
                                                      </tr>                                                                      
                                                      <tr>
                                                        <td>Descrição do EPI:</td>
                                                        <td>
                                                          <?php
                                                            db_input("rh259_descricao",40,$Irh259_descricao,true,'text',$db_opcao);
                                                          ?>                    
                                                        </td>
                                                      </tr>                        
                                                     </table>
                                                   </fieldset>
                                                 </td>
                                               </tr>      
                                               <tr>
                                                 <td colspan="8" align="center">
                                                   <?php 
                                                     if ($db_opcao != 3 ) { 
                                                   ?>
                                                   <input type='button' id='btnSalvarEPI' value='Incluir registro do EPI' onClick='incluirRegistroEPI()' />
                                                   <!-- <input type='button' id='btnLimparEPI' value='Cancelar' onClick='limparFormularioEPI()' />  --> 
                                                   <?php 
                                                     }
                                                   ?>
                                                 </td>
                                               </tr>
                                               <tr>
                                                 <td>
                                                   <fieldset> 
                                                    <legend>EPI's cadastrados</legend> 
                                                    <div id="ctnDadosEPI"></div>
                                                  </fieldset>
                                                 </td>
                                               </tr>
                                             </table>                
                                           </fieldset>
                                         </td>
                                       </tr> 
                                     </table>
                                   </fieldset>
                                 </td>
                                </tr>
                              </table>
                            </td>
                           </tr>
                          </table>
                       <!-- FIM Informações Relativas a Equipamentos de Proteção Coletiva-EPC e Equipamentos de Proteção Individual-EPI -->                        
                      </fieldset>
                    </td>
                  </tr>      
                  <tr>
                    <td colspan="8" align="center">
                      <?php 
                        if ($db_opcao != 3 ) { 
                      ?>
                      <input type='button' id='btnSalvarAgenteNocivo' value='Incluir dados do agente nocivo' onClick='incluirRegistroAgenteNocivo()' />
                      <!-- <input type='button' id='btnLimparAgenteNocivo' value='Cancelar' onClick='limparFormularioAgenteNocivo()' /> -->
                      <?php 
                        } 
                      ?>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <fieldset> 
                       <legend>Agentes nocivos cadastrados</legend> 
                       <div id="ctnAgentesNocivos"></div>
                     </fieldset>
                    </td>
                  </tr>
                </table>
              </fieldset>
            </td>
          </tr>
          <!-- FIM Agente(s) nocivo(s) ao(s) qual(is) o trabalhador está exposto, conforme tabela 24 do eSocial -->
          
          <!-- INICIO Informações Relativas ao Responsável pelos Registro Ambientais -->
          <tr>
            <td>
              <fieldset id="fieldset_esocial_responsavel_registros_ambientais">
                <legend>Informações Relativas ao Responsável pelos Registros Ambientais</legend>
                <table style="width: 100%">
                  <tr>
                    <td>
                      <fieldset>
                        <legend>Dados do responsável pelos registros ambientais</legend>
                        <table style="width: 100%">
                          <tr>
                            <td width="500px;">CPF do responsável pelos registros ambientais: </td>
                            <td>
                              <?php
                                db_input("rh258_cpfresponsavel",40,$rh258_cpfresponsavel,true,'text',$db_opcao);
                              ?>
                            </td>
                          </tr>
                          <tr>  
                            <td>Órgão de classe vinculado ao responsável: </td>
                            <td>
                              <?php
                                $aOpcoesOrgaoClasse = array("0"=>"");
                                $aOpcoesOrgaoClasse[1] = "1 - Conselho Regional de Medicina - CRM";                
                                $aOpcoesOrgaoClasse[4] = "4 - Conselho Regional de Engenharia e Agronomia - CREA"; 
                                $aOpcoesOrgaoClasse[9] = "9 - Outros";
                                db_select("rh258_identificacaoorgao", $aOpcoesOrgaoClasse, true, $db_opcao);
                              ?>
                            </td>
                          </tr>
                          <tr>  
                            <td>Número de inscrição no órgão de classe:</td>
                            <td>
                              <?php
                                db_input("rh258_numeroinscricaoorgao",40,$Irh258_numeroinscricaoorgao,true,'text',$db_opcao);
                              ?>
                            </td>
                          </tr>
                          <tr>  
                            <td>Descrição (sigla) do órgão de classe vinculado ao responsável: </td>
                            <td>
                              <?php
                                db_input("rh258_descricaoorgao",40,$Irh258_descricaoorgao,true,'text',$db_opcao);
                              ?>
                            </td>
                          </tr>
                          <tr>
                            <td>Sigla da Unidade da Federação - UF do órgão de classe</td>
                            <td>
                              <?php
                                $rsEstados = $cldb_uf->sql_record($cldb_uf->sql_query_file(null,"db12_uf, db12_nome"));
                                db_selectrecord("rh258_uforgao",$rsEstados,true, $db_opcao,"","rh258_uforgao","","...-Selecione");
                              ?>
                            </td>
                          </tr>
                          <tr>  
                            <td>Período de avaliação/responsabilidade:</td>
                            <td>
                              <?php
                                
                                db_inputdata("rh258_periodoinicial",
                                             null,
                                             null,
                                             null, 
                                             true, 
                                             "text", 
                                             $db_opcao);
                                echo " à ";
                                
                                db_inputdata("rh258_periodofinal",
                                             null,
                                             null,
                                             null,
                                             true,
                                             "text",
                                             $db_opcao);
                              ?>
                            </td>                    
                          </tr>
                        </table>
                      </fieldset>
                    </td>
                  </tr>      
                  <tr>
                    <td colspan="8" align="center">
                      <?php 
                        if ($db_opcao != 3 ) { 
                      ?>
                      <input type='button' id='btnSalvarResponsavelRegistroAmbiental' value='Incluir responsável pelo registro ambiental' onClick='incluirRegistroResponsavelRegistroAmbiental()' />
                      <!--  <input type='button' id='btnLimparResponsavelRegistroAmbiental' value='Cancelar' onClick='limparFormularioResponsavelRegistroAmbiental()' /> -->
                      <?php 
                        }
                      ?>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <fieldset> 
                       <legend>Responsáveis cadastrados</legend> 
                       <div id="ctnReponsaveisRegistroAmbiental"></div>
                     </fieldset>
                    </td>
                  </tr>
                </table>                
              </fieldset>
            </td>
          </tr>  
          <!-- FIM Informações Relativas ao Responsável pelos Registro Ambientais -->          
                  
          <!-- INICIO Observações Relativas a Registros Ambientais -->        
          <tr>
            <td>
              <fieldset id="fieldset_esocial_observacao_registros_ambientais">
                <legend>Observações Relativas a Registros Ambientais</legend>
                <table style="width: 100%">
                  <tr>
                    <td>
                      <fieldset>
                        <legend>Observação(ões) complementar(es) referente(s) a registros ambientais:</legend>
                        <?php
                          db_textarea("rh55_observacaoregistrosambientais",5,100,$Irh55_observacaoregistrosambientais,true,"text",$db_opcao);
                        ?>
                      </fieldset>
                    </td>
                  </tr>
                </table>
              </fieldset>
            </td>
          </tr> 
         <!-- FIM Observações Relativas a Registros Ambientais -->
                   
        </table>
      </fieldset>
    </td>
  </tr>
  <!-- FIM Dados eSocial (Evento S 2240 - Condições Ambientais do Trabalho -->
</table>
</fieldset>
  <br>
  <?php
  
    $sDescricaoBotao = "Salvar";
    if ($db_opcao == 3) {
      $sDescricaoBotao = "Excluir";
    }
    echo "<input name=\"btnExecutarProcedimento\" type=\"button\" id=\"btnExecutarProcedimento\" value=\"$sDescricaoBotao\">";
  
    if ($db_opcao != 1) { 
      echo "&nbsp;&nbsp;<input name=\"pesquisar\" type=\"button\" id=\"pesquisar\" value=\"Pesquisar\" onclick=\"pesquisaLocalTrabalho();\" >";
    }
    
  ?>
  
</form>
</div>
<?php
db_menu();
?>
</body>
</html>

<script>

const oFormulario = document.getElementById('frmLocalTrabalho');
const iValidaCentroCusto = <?php echo $iTipoControleCustos; ?>;
const iOpcao = <?php echo $db_opcao; ?>;

if (iOpcao != 1 && $F("rh55_codigo") == "") {
  $('btnExecutarProcedimento').disabled = true;	
}

/*
 * Controle dos fieldsets
 */
var oToogle1 = new DBToogle('fieldset_dados_local_trabalho', true);
var oToogle2 = new DBToogle('fieldset_esocial', true);
var oToogle3 = new DBToogle('fieldset_esocial_condicoes_ambientais', false);
var oToogle4 = new DBToogle('fieldset_esocial_agentes_nocivos', false);
var oToogle5 = new DBToogle('fieldset_esocial_equipamentos_protecao', false);
var oToogle6 = new DBToogle('fieldset_esocial_responsavel_registros_ambientais', false);
var oToogle7 = new DBToogle('fieldset_esocial_observacao_registros_ambientais', false);


/*
 * Contador dos Agentes Nocivos incluidos na grid
 */
iCountAgenteNocivo = 0;

/*
 * Contador dos EPIs incluidos na grid
 */
iCountEPI = 0;

/*
 * Contador dos Responsaveis pelos registros ambientais incluidos na grid
 */
iCountResponsavelRegistroAmbiental = 0;


/*
 * Array com os objetos dos agentes nocivos incluidos
 */
aDadosAgentesNocivos    = new Array();

/*
 * Array com os objetos dos EPIs incluidos
 */
aDadosEPIs    = new Array();

/*
 * Array com os objetos dos responsaveis pelos registros ambientais incluidos
 */
aDadosResponsaveisRegistrosAmbientais    = new Array();

/*
 * Objeto que armazena os dados do agente nocivo carregado no formulario em manutecao/alteracao
 */
oCacheAgenteNocivo = {};

 /*
  * Objeto que armazena os dados do EPI carregado no formulario em manutecao/alteracao
  */
 oCacheEPI = {}; 

/*
 * Objeto que armazena os dados do responsavel pelo registro ambiental carregado no formulario em manutecao/alteracao
 */
oCacheResponsavelRegistroAmbiental = {}; 


mostraFormCadastroEPI();
function mostraFormCadastroEPI() {
   if ($F("rh257_utilizaepi") == 2) {
     $("tdCadastroEPIs").style.display = 'table-cell';
   } else {
   	 $("tdCadastroEPIs").style.display = 'none';
   }
}

function adicionaCentroCusto(iValor) {
 
  var iOrigem  = 3;
  var sUrl     = 'iOrigem=&iNumEmp=&iCodigoDaLinha=0';
  var lMostrar = true;
  if (iValor != "") {
     sUrl += "&iCodigoCriterio="+iValor;
     var lMostrar = false;
  }
  js_OpenJanelaIframe('',
                      'db_iframe_centroCusto',
                      'cus4_escolhercentroCusto.php?'+sUrl,
                      'Centro de Custos',
                      lMostrar,
                      '25',
                      '1',
                      (document.body.scrollWidth-10),
                      (document.body.scrollHeight-100)
                     );
  
   
}

function js_completaCustos(iCodigo, iCriterio, iDescr) {
  
  $('rh86_criteriorateio').value = iCriterio;
  $('cc08_descricao').value  = iDescr;
  db_iframe_centroCusto.hide();

}

/*
 * Funcao para abrir a lookup para pesquisar as opcoes de preenchimento
 */
function pesquisaOpcoes(sArquivoDados,sIdCampo) {

	parametros  = "sArquivoDados="+sArquivoDados;
	parametros += "&sIdCampo="+sIdCampo;
	parametros += "&funcao_js=retornoOpcoes1"; 
    
    js_OpenJanelaIframe('',
                        'db_iframe_opcoes',
                        'func_rhlocaltrabopcoesesocial.php?'+parametros,
                        'Pesquisar Opcoes',
                        true);
    
}

/*
 * Funcao de retorno da opcao escolhida
 */
function retornoOpcoes1(sIdCampo,iCodigo,sDescricao) {

	var sIdCampoDescricao = sIdCampo+"_descricao";
	
	var objCampo = eval(`document.form1.${sIdCampo}`);
	var objCampoDescricao = eval(`document.form1.${sIdCampoDescricao}`);
	
	objCampo.value = iCodigo;
	objCampoDescricao.value = sDescricao;
    
    db_iframe_opcoes.hide();
    
}


/*
 *
 * Inicio funcoes formulario agentes nocivos
 * 
 */
 
/*
 * Inicializacao da grid dos registros de agentes nocivos
 */
function inicializaGridAgentesNocivos() {

	oGridAgentesNocivos = new DBGrid('oGridAgentesNocivos');
	oGridAgentesNocivos.nameInstance = 'oGridAgentesNocivos';
	oGridAgentesNocivos.allowSelectColumns(true);
	
	oGridAgentesNocivos.setCellWidth(
    	    [
        	    '100',
                '300',
                '110',
                '120',
                '120',
                '120',
                '200',
                '250',
                '150',
                '100'
            ]
    );
    
	oGridAgentesNocivos.setCellAlign(
    	    [
               'left',
               'left',
               'left',
               'left',
               'left',
               'left',
               'left',
               'left',
               'left',
               'center'
            ]
    );
    
	oGridAgentesNocivos.setHeader(
    	    [
        	   'Agente Nocivo',
        	   'Descrição do Agente Nocivo',
               'Tipo Avaliação',
               'Intensidade/Concentração/Dose',
               'Limite de tolerância',
               'Unidade de medida',
               'Descrição Unidade de medida',
               'Técnica Utilizada',
               'Número do Processo',
               'Ação'
            ]
    );

	oGridAgentesNocivos.aHeaders[0].lDisplayed = false;
	oGridAgentesNocivos.aHeaders[5].lDisplayed = false;
	if (iOpcao == 3) {
	  oGridAgentesNocivos.aHeaders[8].lDisplayed = false;
	}
	
	oGridAgentesNocivos.setHeight(150);
	
	oGridAgentesNocivos.show($('ctnAgentesNocivos'));
	oGridAgentesNocivos.clearAll(true);
}
inicializaGridAgentesNocivos();

/*
 * Carrega as linhas da grid dos Agentes Nocivos lancados
 * Sempre percorrendo o array aDadosAgentesNocivos
 */
function carregaRegistrosLancadosGridAgentesNocivos() {
  
  oGridAgentesNocivos.clearAll(true);

  aDadosAgentesNocivos.forEach( function(oDadosAgenteNocivo) {

	var aCelulas = new Array();
    aCelulas[0]  = "<span title='"+oDadosAgenteNocivo.rh256_agentenocivo_descricao+"'>"+oDadosAgenteNocivo.rh256_agentenocivo+"</span>";
    aCelulas[1]  = "<span title='"+oDadosAgenteNocivo.rh256_agentenocivo_descricao+"'>"+oDadosAgenteNocivo.rh256_agentenocivo_descricao+"</span>";
    aCelulas[2]  = "<span title='"+oDadosAgenteNocivo.rh256_tipoavaliacao+"'>"+oDadosAgenteNocivo.rh256_tipoavaliacao+"</span>";
    aCelulas[3]  = "<span title='"+oDadosAgenteNocivo.rh256_intensidadeconcentracao+"'>"+oDadosAgenteNocivo.rh256_intensidadeconcentracao+"</span>";
    aCelulas[4]  = "<span title='"+oDadosAgenteNocivo.rh256_tolerancialimite+"'>"+oDadosAgenteNocivo.rh256_tolerancialimite+"</span>";
    aCelulas[5]  = "<span title='"+oDadosAgenteNocivo.rh256_medida+"'>"+oDadosAgenteNocivo.rh256_medida+"</span>";
    aCelulas[6]  = "<span title='"+oDadosAgenteNocivo.rh256_medida_descricao+"'>"+oDadosAgenteNocivo.rh256_medida_descricao+"</span>";
    aCelulas[7]  = "<span title='"+oDadosAgenteNocivo.rh256_tecnicamedicao+"'>"+oDadosAgenteNocivo.rh256_tecnicamedicao+"</span>";
    aCelulas[8]  = "<span title='"+oDadosAgenteNocivo.rh256_processo+"'>"+oDadosAgenteNocivo.rh256_processo+"</span>";
    
    sConteudoAcao  = "<input type='button' value='Alterar' onclick='alterarRegistroAgenteNocivo("+oDadosAgenteNocivo.id+")'>&nbsp;";
    sConteudoAcao += "<input type='button' value='Excluir' onclick='removerRegistroAgenteNocivo("+oDadosAgenteNocivo.id+")'>";
    aCelulas[9] = sConteudoAcao;
    
    oGridAgentesNocivos.addRow( aCelulas );
    
  });
  
  oGridAgentesNocivos.renderRows();
  
  return true;
  
}


function validacaoInclusaoRegistroAgenteNocivo() {
	
	if ($F("rh256_agentenocivo") == "") {
		alert("Informe o Agente Nocivo");
		return false;
	}

	if ($F("rh256_agentenocivo") != "09.01.001" && ($F("rh256_tipoavaliacao") == "0" || $F("rh256_tipoavaliacao") == "")) {
		alert("Informe o tipo de avaliação");
		return false;
	} 

	if ($F("rh256_tipoavaliacao") == 1) {

		if ($F("rh256_intensidadeconcentracao") == "") {
		  alert("Informe a intensidade, concentração ou dose da exposição do trabalhador ao agente nocivo");
		  return false;
		}

		var aAgentesNocivosValidacao = ['01.18.001','02.01.014'];
		if (aAgentesNocivosValidacao.includes($F("rh256_agentenocivo")) && $F("rh256_tolerancialimite") == "") {
		  alert("Informe o limite de tolerância calculado para agentes específicos");
		  return false;
		}		

		if ($F("rh256_medida") == "") {
		  alert("Informe a dose ou unidade de medida da intensidade ou concentração do agente");
		  return false;
		}

		if ($F("rh256_tecnicamedicao") == "") {
		  alert("Informe a técnica utilizada para medição da intensidade ou concentração");
		  return false;	
		}
	}	

	return true;
}

/*
 * Monta o objeto com os dados do agente nocivo e inclui no array aDadosAgentesNocivos
 * Limpa o formulario
 * Recarrega as linhas da grid 
 */
function incluirRegistroAgenteNocivo () {
	 
	if (!validacaoInclusaoRegistroAgenteNocivo()) {
	    return false;
	}

	var iIdAgenteNocivo = iCountAgenteNocivo++;

	if (oCacheAgenteNocivo.hasOwnProperty('id') ) {
	  //alteramos o id do agente nocivo nos EPIS lancados
	  var iIdAgenteNocivoAnterior = oCacheAgenteNocivo.id;
	   
      aNovosEPIs = [];
      aDadosEPIs.forEach( function(oEPI,i) {
      	if (oEPI.agentenocivo_id == iIdAgenteNocivoAnterior) {
      		oEPI.agentenocivo_id = iIdAgenteNocivo;   
      	}
  		aNovosEPIs.push(oEPI);
      });
      aDadosEPIs = aNovosEPIs;	  
	}		

	var oNovoAgenteNocivo = new Object();
        oNovoAgenteNocivo.id                              = iIdAgenteNocivo;
        oNovoAgenteNocivo.rh256_agentenocivo              = $F("rh256_agentenocivo");
        oNovoAgenteNocivo.rh256_agentenocivo_descricao    = $F("rh256_agentenocivo_descricao");
        oNovoAgenteNocivo.rh256_tipoavaliacao             = $F("rh256_tipoavaliacao");
        oNovoAgenteNocivo.rh256_intensidadeconcentracao   = $F("rh256_intensidadeconcentracao");
        oNovoAgenteNocivo.rh256_tolerancialimite          = $F("rh256_tolerancialimite");
        oNovoAgenteNocivo.rh256_medida                    = $F("rh256_medida");
        oNovoAgenteNocivo.rh256_medida_descricao          = $F("rh256_medida_descricao");
        oNovoAgenteNocivo.rh256_tecnicamedicao            = $F("rh256_tecnicamedicao");
        oNovoAgenteNocivo.rh256_processo = $F("rh256_processo");

    var oDadosEquipamentosProtecao = new Object();
        oDadosEquipamentosProtecao.agentenocivo_id          = oNovoAgenteNocivo.id;
        oDadosEquipamentosProtecao.rh257_utilizaepc         = $F("rh257_utilizaepc");
        oDadosEquipamentosProtecao.rh257_eficaciaepc        = $F("rh257_eficaciaepc");
        oDadosEquipamentosProtecao.rh257_utilizaepi         = $F("rh257_utilizaepi");
        oDadosEquipamentosProtecao.rh257_eficaciaepi        = $F("rh257_eficaciaepi");
        oDadosEquipamentosProtecao.rh257_medidaprotecaoepi  = $F("rh257_medidaprotecaoepi");
        oDadosEquipamentosProtecao.rh257_funcionamentoepi   = $F("rh257_funcionamentoepi");
        oDadosEquipamentosProtecao.rh257_usoininterruptoepi = $F("rh257_usoininterruptoepi");
        oDadosEquipamentosProtecao.rh257_validadeepi        = $F("rh257_validadeepi");
        oDadosEquipamentosProtecao.rh257_periodicidadeepi   = $F("rh257_periodicidadeepi");
        oDadosEquipamentosProtecao.rh257_higienizacaoepi    = $F("rh257_higienizacaoepi");
        
        aAgenteNocivoEPI = []
        aDadosEPIs.forEach( function(oEPI,i) {
        	if (oEPI.agentenocivo_id == iIdAgenteNocivo) {   
        	  aAgenteNocivoEPI.push(oEPI);
        	}
        });
        oDadosEquipamentosProtecao.aDadosEPIs = aAgenteNocivoEPI;
        
        oNovoAgenteNocivo.oDadosEquipamentosProtecao = oDadosEquipamentosProtecao;
      
        aDadosAgentesNocivos.push(oNovoAgenteNocivo);
        
        oCacheAgenteNocivo = {};
        
        limparFormularioAgenteNocivo();
        carregaRegistrosLancadosGridAgentesNocivos();
        
    return true;
}

/*
 * Remove o registro do agente nocivo do array aDadosAgentesNocivos
 * Recarreega as linhas da grid
 */
function removerRegistroAgenteNocivo(iId) {

   aNovosDadosAgentesNocivos = [];
   aDadosAgentesNocivos.forEach( function(oDadosAgenteNocivo, i) {
	if (oDadosAgenteNocivo.id != iId) {
		aNovosDadosAgentesNocivos.push(aDadosAgentesNocivos[i]);
   	}
   });
   aDadosAgentesNocivos = aNovosDadosAgentesNocivos; 	 

   carregaRegistrosLancadosGridAgentesNocivos();
   
   return true;
   
}
	
/*
 * Verifica os dados do registro do agente nocivo para alteracao
 */
function alterarRegistroAgenteNocivo(iId) {

	if (oCacheAgenteNocivo.hasOwnProperty('id') ) {
	  aDadosAgentesNocivos.push(oCacheAgenteNocivo);
	  carregaRegistrosLancadosGridAgentesNocivos();
	}	 
	
	aDadosAgentesNocivos.forEach( function(oDadosAgenteNocivo) {
	  if (oDadosAgenteNocivo.id == iId) {
	  	carregaFormularioRegistroAgenteNocivo(oDadosAgenteNocivo);
	  	return true;
	  }
	});

	return true;
}

/*
 * Carrega os dados do registro do agente nocivo para o formulario
 * Removendo a linha da grid 
 */
function carregaFormularioRegistroAgenteNocivo(oAgenteNocivo) {
	
	oCacheAgenteNocivo = oAgenteNocivo;

    $("rh256_agentenocivo").value            = oAgenteNocivo.rh256_agentenocivo;                       
    $("rh256_agentenocivo_descricao").value  = oAgenteNocivo.rh256_agentenocivo_descricao;             
    $("rh256_tipoavaliacao").value           = oAgenteNocivo.rh256_tipoavaliacao;                
    $("rh256_intensidadeconcentracao").value = oAgenteNocivo.rh256_intensidadeconcentracao; 
    $("rh256_tolerancialimite").value        = oAgenteNocivo.rh256_tolerancialimite;             
    $("rh256_medida").value                  = oAgenteNocivo.rh256_medida;        
    $("rh256_medida_descricao").value        = oAgenteNocivo.rh256_medida_descricao;
	$("rh256_tecnicamedicao").value          = oAgenteNocivo.rh256_tecnicamedicao;
	$("rh256_processo").value = oAgenteNocivo.rh256_processo;

	$("rh257_utilizaepc").value         = oAgenteNocivo.oDadosEquipamentosProtecao.rh257_utilizaepc;       
	$("rh257_eficaciaepc").value        = oAgenteNocivo.oDadosEquipamentosProtecao.rh257_eficaciaepc;      
	$("rh257_utilizaepi").value         = oAgenteNocivo.oDadosEquipamentosProtecao.rh257_utilizaepi;       
	$("rh257_eficaciaepi").value        = oAgenteNocivo.oDadosEquipamentosProtecao.rh257_eficaciaepi;      
	$("rh257_medidaprotecaoepi").value  = oAgenteNocivo.oDadosEquipamentosProtecao.rh257_medidaprotecaoepi;
	$("rh257_funcionamentoepi").value   = oAgenteNocivo.oDadosEquipamentosProtecao.rh257_funcionamentoepi; 
	$("rh257_usoininterruptoepi").value = oAgenteNocivo.oDadosEquipamentosProtecao.rh257_usoininterruptoepi;
	$("rh257_validadeepi").value        = oAgenteNocivo.oDadosEquipamentosProtecao.rh257_validadeepi;      
	$("rh257_periodicidadeepi").value   = oAgenteNocivo.oDadosEquipamentosProtecao.rh257_periodicidadeepi; 
	$("rh257_higienizacaoepi").value    = oAgenteNocivo.oDadosEquipamentosProtecao.rh257_higienizacaoepi;   

	carregaRegistrosLancadosGridEPIs();		
	mostraFormCadastroEPI();
	
	removerRegistroAgenteNocivo(oAgenteNocivo.id);
	
	return true;               
}

 /*
  * Limpa os dados do formulario
  * Caso exista registro do agente nocivo em cache, retorna os dados para o array aDadosAgentesNocivos 
  */
function limparFormularioAgenteNocivo() {

	limparFormularioEPI();
	$("rh257_utilizaepi").value = 0;
	oGridEPIs.clearAll(true);
	mostraFormCadastroEPI();
	
	if (oCacheAgenteNocivo.hasOwnProperty('id') ) {
		
	  aDadosAgentesNocivos.push(oCacheAgenteNocivo);
	  carregaRegistrosLancadosGridAgentesNocivos();
	  
	}
	oCacheAgenteNocivo = {};
	
    $("rh256_agentenocivo").value            = "";                      
    $("rh256_agentenocivo_descricao").value  = "";            
    $("rh256_tipoavaliacao").value           = "";               
    $("rh256_intensidadeconcentracao").value = "";
    $("rh256_tolerancialimite").value        = "";            
    $("rh256_medida").value                  = "";     
    $("rh256_medida_descricao").value        = "";
    $("rh256_tecnicamedicao").value          = "";
    $("rh256_processo").value = "";

    $("rh257_utilizaepc").value         = ""; 
    $("rh257_eficaciaepc").value        = "";
    $("rh257_utilizaepi").value         = "";
    $("rh257_eficaciaepi").value        = "";
    $("rh257_medidaprotecaoepi").value  = "";
    $("rh257_funcionamentoepi").value   = "";
    $("rh257_usoininterruptoepi").value = "";
    $("rh257_validadeepi").value        = "";
    $("rh257_periodicidadeepi").value   = "";
    $("rh257_higienizacaoepi").value    = "";                  

    return true;
}
 /*
  *
  * Fim funcoes formulario agentes nocivos
  * 
  */


/*
 *
 * Inicio funcoes formulario responsavel por registros de EPIs
 * 
 */
/*
 * Inicializacao da grid dos registros EPIs
 */
function inicializaGridEPI() {

  oGridEPIs = new DBGrid('oGridEPIs');
  oGridEPIs.nameInstance = 'oGridEPIs';
  oGridEPIs.allowSelectColumns(true);
    
  oGridEPIs.setCellWidth(
            [
                '300',
                '600',
                '100',
            ]
    );
    
  oGridEPIs.setCellAlign(
            [
               'left',
               'left',
               'center'
            ]
    );
    
  oGridEPIs.setHeader(
            [
                'CA ou Documento Avaliação',
                'Descrição',
                'Ação'
                
            ]
    );
    if (iOpcao == 3) {
  	oGridAgentesNocivos.aHeaders[3].lDisplayed = false;
    }
    oGridEPIs.setHeight(150);

    oGridEPIs.show($('ctnDadosEPI'));
    oGridEPIs.clearAll(true);
}
inicializaGridEPI();

/*
 * Carrega as linhas da grid dos EPIs lancados
 * Sempre percorrendo o array aDadosEPIs
 */
function carregaRegistrosLancadosGridEPIs() {
  
  oGridEPIs.clearAll(true);
  
  var iIdAgenteNocivo = iCountAgenteNocivo;
  if (oCacheAgenteNocivo.hasOwnProperty('id') ) {
  	iIdAgenteNocivo = oCacheAgenteNocivo.id;
  }
  
  aDadosEPIs.forEach( function(oEPI) {

	if (oEPI.agentenocivo_id == iIdAgenteNocivo) {   
      var aCelulas = new Array();
      aCelulas[0]  = "<span>"+oEPI.rh259_documentoavaliacao+"</span>";
      aCelulas[1]  = "<span>"+oEPI.rh259_descricao+"</span>";
      sConteudoAcao  = "<input type='button' value='Alterar' onclick='alterarRegistroEPI("+oEPI.id+")'>&nbsp;";
      sConteudoAcao += "<input type='button' value='Excluir' onclick='removerRegistroEPI("+oEPI.id+")'>";
      aCelulas[2] = sConteudoAcao;    
      oGridEPIs.addRow( aCelulas );
	}
    
  });
  
  oGridEPIs.renderRows();
  
  return true;
  
}

function validacaoInclusaoRegistroEPI() {

    if ($F("rh259_documentoavaliacao") == "" && $F("rh259_descricao") == "") {
      alert("Informe o Certificado de Aprovação - CA/Documento de avaliação ou a Descrição do EPI");
      return false;    
    }
    
    return true;    
}

/*
 * Monta o objeto com os dados do EPI e inclui no array aDadosEPIs
 * Limpa o formulario
 * Recarrega as linhas da grid 
 */
function incluirRegistroEPI () {

    if (!validacaoInclusaoRegistroEPI()) {
        return false;
    }
    
    oCacheEPI = {};

    var iIdAgenteNocivo = iCountAgenteNocivo;
    if (oCacheAgenteNocivo.hasOwnProperty('id') ) {
    	iIdAgenteNocivo = oCacheAgenteNocivo.id;
    }

    var oNovoEPI          = new Object();
        oNovoEPI.id                          = iCountEPI++;
        oNovoEPI.agentenocivo_id             = iIdAgenteNocivo;
        oNovoEPI.rh259_documentoavaliacao    = $F("rh259_documentoavaliacao");
        oNovoEPI.rh259_descricao             = $F("rh259_descricao");
     
        aDadosEPIs.push(oNovoEPI);
        limparFormularioEPI();
        carregaRegistrosLancadosGridEPIs();
        
    return true;
}

/*
 * Remove o registro do EPI do array aDadosEPIs
 * Recarreega as linhas da grid
 */
function removerRegistroEPI(iId) {

   aNovosDadosEPIs = [];

   aDadosEPIs.forEach( function(oDadosEPI, i) {
    if (oDadosEPI.id != iId) {
        aNovosDadosEPIs.push(aDadosEPIs[i]);
    }
   });
   aDadosEPIs = aNovosDadosEPIs;     
   
   carregaRegistrosLancadosGridEPIs();
   
   return true;
   
}

/*
 * Verifica os dados do registro do EPI para alteracao
 */
function alterarRegistroEPI(iId) {

    if (oCacheEPI.hasOwnProperty('id') ) {
      aDadosEPIs.push(oCacheEPI);
      carregaRegistrosLancadosGridEPIs();
    }

    aDadosEPIs.forEach( function(oDadosEPI) {
      if (oDadosEPI.id == iId ) {
        carregaFormularioRegistroEPI(oDadosEPI);
        return true;
      }
    });

    return true;
}

/*
 * Carrega os dados do registro do EPI para o formulario
 * Removendo a linha da grid 
 */
function carregaFormularioRegistroEPI(oEPI) {
    
    oCacheEPI = oEPI;
    $("rh259_documentoavaliacao").value = oEPI.rh259_documentoavaliacao;
    $("rh259_descricao").value          = oEPI.rh259_descricao;

    removerRegistroEPI(oEPI.id); 

    return true;               
}

 /*
  * Limpa os dados do formulario
  * Caso exista registro do EPI em cache, retorna os dados para o array aDadosEPIs 
  */
function limparFormularioEPI() {

    if (oCacheEPI.hasOwnProperty('id') ) {
        
      aDadosEPIs.push(oCacheEPI);
      carregaRegistrosLancadosGridEPIs();
      
    }
    oCacheEPI = {};
    
    $("rh259_documentoavaliacao").value = "";                      
    $("rh259_descricao").value = "";            

    return true;
}
 /*
  *
  * Fim funcoes formulario EPI
  * 
  */


/*
 *
 * Inicio funcoes formulario responsavel por registros ambientais
 * 
 */
 /*
  * Inicializacao da grid dos registros dos Responsaveis pelos Registros Ambientais
  */
 function inicializaGridResponsaveisRegistrosAmbientais() {

     oGridResponsaveisRegistrosAmbientais = new DBGrid('oGridResponsaveisRegistrosAmbientais');
     oGridResponsaveisRegistrosAmbientais.nameInstance = 'oGridResponsaveisRegistrosAmbientais';
     oGridResponsaveisRegistrosAmbientais.allowSelectColumns(true);
     
     oGridResponsaveisRegistrosAmbientais.setCellWidth(
             [
                 '130',
                 '150',
                 '160',
                 '180',
                 '110',
                 '140',
                 '140',
                 '100'
             ]
     );
     
     oGridResponsaveisRegistrosAmbientais.setCellAlign(
             [
                'left',
                'left',
                'left',
                'left',
                'left',
                'left',
                'left',
                'center'
             ]
     );
     
     oGridResponsaveisRegistrosAmbientais.setHeader(
             [
                 'CPF',
                 'Classe do órgão',
                 'Número de inscrição',
                 'Descriçao do órgão',
                 'UF',
                 'Período inicial',
                 'Período final',
                 'Ação'
                 
             ]
     );
     if (iOpcao == 3) {
   	  oGridAgentesNocivos.aHeaders[7].lDisplayed = false;
   	}
     oGridResponsaveisRegistrosAmbientais.setHeight(150);

     oGridResponsaveisRegistrosAmbientais.show($('ctnReponsaveisRegistroAmbiental'));
     oGridResponsaveisRegistrosAmbientais.clearAll(true);
 }
 inicializaGridResponsaveisRegistrosAmbientais();


 /*
  * Carrega as linhas da grid dos responsaveis pelos registros ambientais
  * Sempre percorrendo o array aDadosResponsaveisRegistrosAmbientais
  */
 function carregaRegistrosLancadosGridResponsaveisRegistrosAmbientais() {
   
   oGridResponsaveisRegistrosAmbientais.clearAll(true);

   aDadosResponsaveisRegistrosAmbientais.forEach( function(oDadosResponsavel) {

     var aCelulas = new Array();
     aCelulas[0]  = "<span>"+oDadosResponsavel.rh258_cpfresponsavel+"</span>";
     aCelulas[1]  = "<span>"+oDadosResponsavel.rh258_identificacaoorgao+"</span>";
     aCelulas[2]  = "<span>"+oDadosResponsavel.rh258_numeroinscricaoorgao+"</span>";
     aCelulas[3]  = "<span>"+oDadosResponsavel.rh258_descricaoorgao+"</span>";
     aCelulas[4]  = "<span>"+oDadosResponsavel.rh258_uforgao+"</span>";
     aCelulas[5]  = "<span>"+oDadosResponsavel.rh258_periodoinicial+"</span>";    
     aCelulas[6]  = "<span>"+oDadosResponsavel.rh258_periodofinal+"</span>";
     
     sConteudoAcao  = "<input type='button' value='Alterar' onclick='alterarRegistroResponsavelRegistroAmbiental("+oDadosResponsavel.id+")'>&nbsp;";
     sConteudoAcao += "<input type='button' value='Excluir' onclick='removerRegistroResponsavelRegistroAmbiental("+oDadosResponsavel.id+")'>";
     aCelulas[7] = sConteudoAcao;    
     
     oGridResponsaveisRegistrosAmbientais.addRow( aCelulas );
     
   });
   
   oGridResponsaveisRegistrosAmbientais.renderRows();
   
   return true;
   
 }


 function validacaoInclusaoResponsavelRegistroAmbiental() {

	if ($F("rh258_cpfresponsavel") == "") {
		alert("Informe o CPF do responsável pelos registros ambientais");
		return false;
	}      
	if ($F("rh258_identificacaoorgao") == "" || $F("rh258_identificacaoorgao") == "0") {
		alert("Informe o Órgão de classe vinculado ao responsável");
		return false;
	}  
	if ($F("rh258_numeroinscricaoorgao")  == "") {
		alert("Informe o Número de inscrição no órgão de classe");
		return false;
	}
	if ($F("rh258_identificacaoorgao") == 9 && $F("rh258_descricaoorgao") == "") {
		alert("Informe a Descrição (sigla) do órgão de classe vinculado ao responsável");
		return false;
	}
	if ($F("rh258_uforgao") == "" || $F("rh258_uforgao") == "...") {
		alert("Informe a Sigla da Unidade da Federação - UF do órgão de classe");
		return false;
	}             
	if ($F("rh258_periodoinicial") == "") {
		alert("Informe o Período de avaliação/responsabilidade Inicial");
		return false;      
	}        
	
	return true;
 }

 /*
  * Monta o objeto com os dados do responsavel pelo registro ambiental e inclui no array aDadosResponsaveisRegistrosAmbientais
  * Limpa o formulario
  * Recarrega as linhas da grid 
  */
 function incluirRegistroResponsavelRegistroAmbiental() {

    if (!validacaoInclusaoResponsavelRegistroAmbiental()) {
      return false;
    }
    
    oCacheResponsavelRegistroAmbiental = {};
    
    var oNovoResponsavelRegistroAmbiental          = new Object();
        oNovoResponsavelRegistroAmbiental.id                          = iCountResponsavelRegistroAmbiental++;
        oNovoResponsavelRegistroAmbiental.rh258_cpfresponsavel        = $F("rh258_cpfresponsavel");                   
        oNovoResponsavelRegistroAmbiental.rh258_identificacaoorgao    = $F("rh258_identificacaoorgao");         
        oNovoResponsavelRegistroAmbiental.rh258_numeroinscricaoorgao  = $F("rh258_numeroinscricaoorgao");
        oNovoResponsavelRegistroAmbiental.rh258_descricaoorgao        = $F("rh258_descricaoorgao"); 
        oNovoResponsavelRegistroAmbiental.rh258_uforgao               = $F("rh258_uforgao");              
        oNovoResponsavelRegistroAmbiental.rh258_periodoinicial        = $F("rh258_periodoinicial");       
        oNovoResponsavelRegistroAmbiental.rh258_periodofinal          = $F("rh258_periodofinal");             
      
        aDadosResponsaveisRegistrosAmbientais.push(oNovoResponsavelRegistroAmbiental);
    
        limparFormularioResponsavelRegistroAmbiental();
        carregaRegistrosLancadosGridResponsaveisRegistrosAmbientais();
        
    return true;
 }

 /*
  * Remove o registro do agente nocivo do array aDadosResponsaveisRegistrosAmbientais
  * Recarreega as linhas da grid
  */
 function removerRegistroResponsavelRegistroAmbiental(iId) {

	aNovosDadosResponsaveisRegistrosAmbientais = [];
    aDadosResponsaveisRegistrosAmbientais.forEach( function(oDadosResponsavel, i) {
     if (oDadosResponsavel.id != iId) {
    	 aNovosDadosResponsaveisRegistrosAmbientais.push(aDadosResponsaveisRegistrosAmbientais[i]);        
     }
    });   

	aDadosResponsaveisRegistrosAmbientais = aNovosDadosResponsaveisRegistrosAmbientais; 	 
    
    
    carregaRegistrosLancadosGridResponsaveisRegistrosAmbientais();
    
    return true;
    
 }
     
 /*
  * Verifica os dados do registro do responsavel pelo registro ambiental para alteracao
  */
 function alterarRegistroResponsavelRegistroAmbiental(iId) {

     if (oCacheResponsavelRegistroAmbiental.hasOwnProperty('id') ) {
             
       aDadosResponsaveisRegistrosAmbientais.push(oCacheResponsavelRegistroAmbiental);
       carregaRegistrosLancadosGridResponsaveisRegistrosAmbientais();
               
     }    
     
     aDadosResponsaveisRegistrosAmbientais.forEach( function(oDadosResponsavel) {

         if (oDadosResponsavel.id == iId) {

             carregaFormularioRegistroResponsavelRegistroAmbiental(oDadosResponsavel);
             return true;
         }
     });

     return true;
 }

 /*
  * Carrega os dados do registro do responsavel pelo registro ambiental para o formulario
  * Removendo a linha da grid 
  */
 function carregaFormularioRegistroResponsavelRegistroAmbiental(oResponsavelRegistroAmbiental) {
     
     oCacheResponsavelRegistroAmbiental = oResponsavelRegistroAmbiental;
     
     $("rh258_cpfresponsavel").value        = oResponsavelRegistroAmbiental.rh258_cpfresponsavel;             
     $("rh258_identificacaoorgao").value    = oResponsavelRegistroAmbiental.rh258_identificacaoorgao;   
     $("rh258_numeroinscricaoorgao").value  = oResponsavelRegistroAmbiental.rh258_numeroinscricaoorgao;
     $("rh258_descricaoorgao").value        = oResponsavelRegistroAmbiental.rh258_descricaoorgao;
     $("rh258_uforgao").value               = oResponsavelRegistroAmbiental.rh258_uforgao; 
     $("rh258_periodoinicial").value        = oResponsavelRegistroAmbiental.rh258_periodoinicial;     
     $("rh258_periodofinal").value          = oResponsavelRegistroAmbiental.rh258_periodofinal;

     js_ProcCod_rh258_uforgao('rh258_uforgao','rh258_uforgaodescr');

     removerRegistroResponsavelRegistroAmbiental(oResponsavelRegistroAmbiental.id);   
     
     return true;               
 }

  /*
   * Limpa os dados do formulario
   * Caso exista registro do responsavel em cache, retorna os dados para o array aDadosResponsaveisRegistrosAmbientais 
   */
 function limparFormularioResponsavelRegistroAmbiental() {

     if (oCacheResponsavelRegistroAmbiental.hasOwnProperty('id') ) {
         
       aDadosResponsaveisRegistrosAmbientais.push(oCacheResponsavelRegistroAmbiental);
       carregaRegistrosLancadosGridResponsaveisRegistrosAmbientais();
       
     }
     oCacheResponsavelRegistroAmbiental = {};
     
     $("rh258_cpfresponsavel").value = "";
     $("rh258_identificacaoorgao").value = "";
     $("rh258_numeroinscricaoorgao").value = "";
     $("rh258_descricaoorgao").value = "";
     $("rh258_uforgao").value = "...";
     $("rh258_periodoinicial").value = "";    
     $("rh258_periodofinal").value = "";

     js_ProcCod_rh258_uforgao('rh258_uforgao','rh258_uforgaodescr');
     
     return true;
 }  

/*
 *
 * Fim funcoes formulario responsavel por registros ambientais
 * 
 */ 

function validarInformacoesLocalTrabalho() {

   if (iOpcao != 3) {
	  if (iValidaCentroCusto == 1 && $F('rh86_criteriorateio') == '') {
 	    if (!confirm('Centro de Custo não informado.\nDeseja Continuar?')) {
           return false;
        }
      } else if (iValidaCentroCusto == 2 && $F('rh86_criteriorateio') == '') {
 	    if (!confirm('Centro de Custo não informado.')) {
          return false;
        }
      }
   }

   if ($F("rh55_inep") == "") {
	   alert("Informe o código do Inep");
	   return false;
   }

   if ($F("rh55_endereco") == "") {
	   alert("Informe o Endereço em 'Dados do Local e Condições Ambientais do Trabalho-Agentes Nocivos'");
	   return false;
   }   

   if ($F("rh55_numeroinscricao") == "") {
	   alert("Informe o Número de Inscrição em 'Dados do Local e Condições Ambientais do Trabalho-Agentes Nocivos'");
	   return false;
   }
      
   return true;
}

function executarProcedimento() {

	if (!validarInformacoesLocalTrabalho()) {
        return false;
    }

	const formData = new FormData(oFormulario);
	if (iOpcao != 3) {
	  formData.append('acao', 'salvarLocalTrabalho');
    } else {
      formData.append('acao', 'excluirLocalTrabalho');
    }

    formData.append('JsonDadosAgentesNocivos',JSON.stringify(aDadosAgentesNocivos));
    formData.append('JsonDadosResponsaveisRegistrosAmbientais',JSON.stringify(aDadosResponsaveisRegistrosAmbientais));
    
    HttpClient.post('pes4_rhlocaltrab.RPC.php', {body: formData}).then(response => {
        
    	alert(response.mensagem);
        if (response.erro) {
            return false;
        }
        resetFormulario();
    });    
	
}

function atualizaFormulario(oDadosLocalTrabalho) {

	iCountAgenteNocivo = 0;
	iCountEPI = 0;
	iCountResponsavelRegistroAmbiental = 0;

	aDadosAgentesNocivos    = new Array();
	aDadosEPIs    = new Array();
	aDadosResponsaveisRegistrosAmbientais    = new Array();

	oCacheAgenteNocivo = {};
    oCacheEPI = {}; 
    oCacheResponsavelRegistroAmbiental = {}; 
 	
	oGridAgentesNocivos.clearAll(true);
	oGridEPIs.clearAll(true);
	oGridResponsaveisRegistrosAmbientais.clearAll(true);
	
	
	for (var iInd = 0; iInd < oFormulario.length; iInd++) {
		
		for ( sPropriedade in oDadosLocalTrabalho) {
			
		  if (oFormulario.elements[iInd].name == sPropriedade) {
			oFormulario.elements[iInd].value = oDadosLocalTrabalho[sPropriedade];   
		  }
		  
		}
	}
	
	/*
	 * Carregamento dos dados das grids
	 */
	oCacheAgenteNocivo = {};
	oCacheEPI = {};
	oCacheResponsavelRegistroAmbiental = {};

	for (iInd = 0; iInd < oDadosLocalTrabalho.aDadosAgentesNocivos.length; iInd++) {
		
		oAgenteNocivo = oDadosLocalTrabalho.aDadosAgentesNocivos[iInd];
		oAgenteNocivo.id = iCountAgenteNocivo++;
		aDadosAgentesNocivos.push(oAgenteNocivo)
		
		for (iEPI = 0; iEPI < oAgenteNocivo.oDadosEquipamentosProtecao.aDadosEPIs.length; iEPI++ ) {
			
			oEpi = oAgenteNocivo.oDadosEquipamentosProtecao.aDadosEPIs[iEPI];
			oEpi.agentenocivo_id = oAgenteNocivo.id;
			oEpi.id = iCountEPI++;
			aDadosEPIs.push(oEpi);
		}
			
	}

	carregaRegistrosLancadosGridAgentesNocivos();
	
	aDadosResponsaveisRegistrosAmbientais = oDadosLocalTrabalho.aDadosResponsaveisRegistrosAmbientais;
	iCountResponsavelRegistroAmbiental    = oDadosLocalTrabalho.aDadosResponsaveisRegistrosAmbientais.length;
	carregaRegistrosLancadosGridResponsaveisRegistrosAmbientais();	
	
}

/*
 * Funcao para limpeza do formulario
 */
function resetFormulario() {
	oFormulario.reset();
	
	iCountAgenteNocivo = 0;
	iCountEPI = 0;
	iCountResponsavelRegistroAmbiental = 0;

	aDadosAgentesNocivos    = new Array();
	aDadosEPIs    = new Array();
	aDadosResponsaveisRegistrosAmbientais    = new Array();

	oCacheAgenteNocivo = {};
    oCacheEPI = {}; 
    oCacheResponsavelRegistroAmbiental = {}; 
 	
	oGridAgentesNocivos.clearAll(true);
	oGridEPIs.clearAll(true);
	oGridResponsaveisRegistrosAmbientais.clearAll(true);

	if (iOpcao != 1 && $F("rh55_codigo") == "") {
	  $('btnExecutarProcedimento').disabled = true;
	  pesquisaLocalTrabalho();	
    }

}
 
function pesquisaLocalTrabalho() {
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rhlocaltrab','func_rhlocaltrab.php?funcao_js=parent.retornoPesquisaLocalTrabalho|rh55_codigo','Pesquisa',true);
}

function retornoPesquisaLocalTrabalho(chave) {
	
  const formData = new FormData();
  formData.append('acao', 'buscarDadosCadastroLocalTrabalho');
  formData.append('rh55_codigo',chave);
  
  HttpClient.post('pes4_rhlocaltrab.RPC.php', {body: formData}).then(response => {
  	  if (response.erro) {
          return;
      }
  	  atualizaFormulario(response.dados);
  	  $('btnExecutarProcedimento').disabled = false;
  });

  db_iframe_rhlocaltrab.hide();
  
}

$("btnExecutarProcedimento").observe('click', executarProcedimento);

if (iOpcao != 1) {
  pesquisaLocalTrabalho();
}

function pesquisaLotacaoTributaria(lMostrar) {

  var sQueryString = "func_rhlotacaotributaria.php?funcao_js=parent.retornaLotacaoTributaria|dl_Codigo_Lotacao";

  js_OpenJanelaIframe(
    'CurrentWindow.corpo',
    'db_iframe_avaliacaogruporespostalotacaotributaria',
    sQueryString,
    'Pesquisa Lotação Tributária',
    lMostrar
  );
}


function retornaLotacaoTributaria(preenchimento) {
    db_iframe_avaliacaogruporespostalotacaotributaria.hide();
    $('rh55_lotacaotributaria').value = preenchimento;
}


</script>
