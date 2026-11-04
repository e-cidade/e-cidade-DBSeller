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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
?>

<html>

<head>
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<style>
  td {
    padding: 3px;
  }
</style>

<body bgcolor=#CCCCCC>

    <table  width="100%" class="form-container" border="1" cellpadding="0" cellspacing="0" id="tableLista">
    <thead>
      <tr style="background-color: LightGray; font-weight: bold">
        <td class="text-center field-size4">Código</td>
        <td class="text-center field-size4">Exercício</td>
        <td class="text-center field-size8">Cálculo</td>
        <td class="text-center field-size8">Percentual</td>
        <td class="text-center field-size8">Observação</td>
        <td class="text-center field-size8">Usuario</td>
        <td class="text-center field-size4">Inclusao</td>
      </tr>
    </thead>
    <tbody>
      <?php foreach (getIsencoes() as $oIsencao) { ?>
        <tr style="background-color: #FFFFFF">
          <td class="text-center"><?= $oIsencao->sequencialisencao ?></td>
          <td class="text-center"><?= "$oIsencao->anoinicial - $oIsencao->anofinal" ?></td>
          <td class="text-center"><?= $oIsencao->descricaocalculo ?></td>
          <td class="text-center"><?= $oIsencao->percentual ?>%</td>
          <td class="text-center"><?= $oIsencao->v10_observacao ?></td>
          <td class="text-center"><?= $oIsencao->usuario ?></td>
          <td class="text-center"><?= date("d/m/Y", strtotime($oIsencao->datalancamento)) ?></td>
        </tr>
      <?php } ?>
    </tbody>
    </table>

</body>

</html>

<?php
function getIsencoes()
{
  $oDaoIsencao =  new cl_isencao;

  /**
   * Busca isencoes pelo numero da inscricao
   */
  $sSqlBuscaIsencoesPorInscricao = $oDaoIsencao->sql_buscaIsencoesPorInscricao($_GET['inscricao']);
  $rsBuscaIsencoesPorInscricao = db_query($sSqlBuscaIsencoesPorInscricao);
  $inscricoesEncontradasPorInscricao = db_utils::getCollectionByRecord($rsBuscaIsencoesPorInscricao);

  /**
   * Busca isencoes pelo numero do cgm
   */
  $sSqlBuscaIsencoesPorCgm = $oDaoIsencao->sql_buscaIsencoesPorCgm($_GET['cgm']);
  $rsBuscaIsencoesPorCgm = db_query($sSqlBuscaIsencoesPorCgm);
  $inscricoesEncontradasPorCgm = db_utils::getCollectionByRecord($rsBuscaIsencoesPorCgm);

  return $inscricoesEncontradasPorInscricao + $inscricoesEncontradasPorCgm;
}
?>