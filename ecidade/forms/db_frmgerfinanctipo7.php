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


// Débito tipo 7 - DIVERSOS

use ECidade\Tributario\Arrecadacao\Repository\DiversosTaxaRepository;

$iInstituicao = db_getsession('DB_instit');

$sSqlDiversos  = " select diversos.dv05_coddiver ,                                                  ";
$sSqlDiversos .= "        diversos.dv05_dtinsc   ,                                                  ";
$sSqlDiversos .= "        diversos.dv05_privenc  ,                                                  ";
$sSqlDiversos .= "        diversos.dv05_vlrhis   ,                                                  ";
$sSqlDiversos .= "        diversos.dv05_procdiver,                                                  ";
$sSqlDiversos .= "        diversos.dv05_numpre   ,                                                  ";
$sSqlDiversos .= "        diversos.dv05_obs      ,                                                  ";
$sSqlDiversos .= "        (select descrdepto from db_depart where db_depart.coddepto = diversos.dv05_depto) as nome_departamento, ";
$sSqlDiversos .= "        (select nome from db_usuarios where db_usuarios.id_usuario = diversos.dv05_login) as nome_usuario,      ";
$sSqlDiversos .= "        cgm.z01_nome           ,                                                  ";
$sSqlDiversos .= "        arreinscr.k00_inscr    ,                                                  ";
$sSqlDiversos .= "        arrematric.k00_matric  ,                                                  ";
$sSqlDiversos .= "        procdiver.dv09_descr                                                      ";
$sSqlDiversos .= " from diversos                                                                    ";
$sSqlDiversos .= " inner join arreinstit      on arreinstit.k00_numpre    = diversos.dv05_numpre    ";
$sSqlDiversos .= "                           and arreinstit.k00_instit    = {$iInstituicao}         ";
$sSqlDiversos .= " left outer join arrematric on arrematric.k00_numpre    = diversos.dv05_numpre    ";
$sSqlDiversos .= " left outer join arreinscr  on arreinscr.k00_numpre     = diversos.dv05_numpre    ";
$sSqlDiversos .= " inner join procdiver       on procdiver.dv09_procdiver = diversos.dv05_procdiver ";
$sSqlDiversos .= " inner join cgm             on diversos.dv05_numcgm     = cgm.z01_numcgm          ";
$sSqlDiversos .= " where diversos.dv05_numpre = {$numpre}                                           ";

$rsDiversos = db_query($sSqlDiversos);

if (pg_numrows($rsDiversos) == 0) {
  
  echo "Código de Arrecadação não cadastrado no diversos.";
  exit;
  
} else {
  
  //db_fieldsmemory($rsDiversos,0,'1');
  $oDiversos = db_utils::fieldsMemory($rsDiversos, 0);
  
  $dv05_coddiver   = $oDiversos->dv05_coddiver ;
  $dv05_dtinsc     = db_formatar($oDiversos->dv05_dtinsc, 'd');
  $dv05_privenc    = db_formatar($oDiversos->dv05_privenc, 'd');
  $dv05_vlrhis     = db_formatar($oDiversos->dv05_vlrhis, 'f');
  $dv05_procdiver  = $oDiversos->dv05_procdiver;
  $dv05_numpre     = $oDiversos->dv05_numpre   ;
  $dv05_obs        = $oDiversos->dv05_obs      ;
  $dv09_descr      = $oDiversos->dv09_descr    ;
  $z01_nome        = $oDiversos->z01_nome      ;
  $k00_matric      = $oDiversos->k00_matric    ;
  $k00_inscr       = $oDiversos->k00_inscr     ;
  $nome_departamento = $oDiversos->nome_departamento;
  $nome_usuario     = $oDiversos->nome_usuario    ;
  
}

$daoDiversos = new cl_diversos();
$where = "dv05_numpre = $numpre";

if ($numpar > 0) {
    $where .= " and q05_numpar = $numpar";
}

$sqlIssvar = $daoDiversos->sql_query_issvar(null, "distinct q05_mes, q05_ano", "q05_ano, q05_mes", $where);
$rsIssvar = db_query($sqlIssvar);

if (empty($rsIssvar)) {
    db_redireciona('db_erros.php?db_erro=Erro ao buscar a competência do auto de infração de origem deste bébito.');
    exit;
}

$competencias = db_utils::makeCollectionFromRecord($rsIssvar, function($value) {
    return $value->q05_mes . "/" .$value->q05_ano;
});

?>

<style>
  #tabelaTaxas{
    border: 1px solid black !important;
    margin: 3px;
  }

  .tabelaTaxasHead {
    border: 1px solid black !important;
    background-color: #E1E1E1 !important;
    width: fit-content;
    min-width: 100px;
    font-weight: bold;
    padding: 2px;
    margin: 0;
  }

  .tabelaTaxasCorpo {
    border: 1px solid black !important;
    padding: 2px;
    margin: 0;
    font-weight: normal;
  }
</style>

<fieldset>
  <legend>Módulo Diversos</legend>
  
  <table class="linhaZebrada">  
    <tr> 
      <td>C&oacute;digo Diverso:</td>
      <td><?php echo $dv05_coddiver; ?></td>
    </tr>
    <tr> 
      <td>Data Inclus&atilde;o:</td>
      <td><?php echo $dv05_dtinsc; ?></td>
    </tr>
    <tr> 
      <td>Vencimento:</td>
      <td><?php echo $dv05_privenc; ?></td>
    </tr>
    <tr> 
      <td>Valor Lan&ccedil;ado:</td>
      <td><?php echo $dv05_vlrhis; ?></td>
    </tr>
    <tr> 
      <td>Proced&ecirc;ncia:</td>
      <td><?php echo $dv05_procdiver.'-'.$dv09_descr; ?></td>
    </tr>
    <tr> 
      <td>Contribu&iacute;nte:</td>
      <td><?php echo $z01_nome; ?></td>
    </tr>
    <tr> 
      <td>C&oacute;digo Arrecada&ccedil;&atilde;o:</td>
      <td><?php echo $dv05_numpre; ?></td>
    </tr>
    <tr> 
      <td>Hist&oacute;rico:</td>
      <td><?php echo $dv05_obs; ?></td>
    </tr>
    
    <tr> 
      <td>Matr&iacute;cula Im&oacute;vel:</td>
      <td>
        <?php
          if ($k00_matric != "") {
          	echo $k00_matric;
          }
        ?>
      </td>
    </tr>
    <tr> 
      <td>Inscri&ccedil;&atilde;o Alvar&aacute;:</td>
      <td> 
        <?php
          if ($k00_inscr != "") {
          	echo $k00_inscr;
          }
        ?>
      </td>
    </tr>
      <tr>
          <td>Compet&ecirc;ncia:</td>
          <td>
              <?php
              if (!empty($competencias)) {
                  echo implode(", ", $competencias);
              }
              ?>
      </td>
    </tr>

    <tr> 
      <td>Departamento:</td>
      <td><?php echo $nome_departamento; ?></td>
    </tr>
    <tr> 
      <td>Usuário:</td>
      <td><?php echo $nome_usuario; ?></td>
    </tr>

    <tr>
      <td>Taxas:</td>
      <td>
        <table id="tabelaTaxas" cellspacing="0" cellpadding="0">
          <?php
            $diversosTaxaRepository = new DiversosTaxaRepository();
            $taxasVinculadas = $diversosTaxaRepository->getTaxasByCodigoDiversos($dv05_coddiver);

            if (count($taxasVinculadas) > 0) {
          ?>
            <tbody>
            <tr>
              <td class="DBLovrotTdCabecalho tabelaTaxasHead" >Sequencial</td>
              <td class="DBLovrotTdCabecalho tabelaTaxasHead" >Descrição</td>
              <td class="DBLovrotTdCabecalho tabelaTaxasHead" >Valor</td>
            </tr>
            <?php
              foreach($taxasVinculadas as $taxaVinculada) {
            ?>
              <tr>
                <td class="DBLovrotRegistrosRetornados tabelaTaxasCorpo" ><?= $taxaVinculada->ar44_sequencial ?></td>
                <td class="DBLovrotRegistrosRetornados tabelaTaxasCorpo" ><?= $taxaVinculada->ar44_descricao ?></td>
                <td class="DBLovrotRegistrosRetornados tabelaTaxasCorpo" >R$<?= number_format($taxaVinculada->dv15_valor, 2, ',', '') ?></td>
              </tr>
            <?php
              }
            ?>
            </tbody>
          <?php
            }
          ?>
        </table>
      </td>
    </tr>
  
  </table>

</fieldset>