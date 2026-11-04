<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009  DBselller Servicos de Informatica             
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

require_once(modification('libs/db_stdlib.php'));
require_once(modification('libs/db_conecta.php'));
require_once(modification('libs/db_sessoes.php'));
require_once(modification('libs/db_usuariosonline.php'));
require_once(modification('libs/JSON.php'));
require_once(modification('libs/db_utils.php'));

$oJson       = new services_json();
$sName       = html_entity_decode(crossUrlDecode($_POST['string']));
$iTipo       = isset($_GET['tipo']) ? $_GET['tipo'] : -1;
$sSql        = '';
$iLinhas     = 0;
$aResultados = array();

switch($iTipo) {

  case 1: // EXAME
    
    //$sWhere[] = "la08_i_ativo = 1 ";
    $sWhere[] = "la08_c_descr ilike '$sName%' ";
    $sWhere = implode(" and ", $sWhere);


    $campos = "la08_i_codigo as cod,
               la08_c_descr as label,
               la08_c_sigla as sigla";

    $cllab_exame = new cl_lab_exame;
    $sSql = $cllab_exame->sql_query("", $campos, "la08_i_codigo", $sWhere);
    $rs         = $cllab_exame->sql_record($sSql);
    $iLinhas    = $cllab_exame->numrows;

  break;


}

if ($iLinhas > 0) {
  $aResultados = db_utils::getCollectionByRecord($rs, false, false, true);
}

echo $oJson->encode($aResultados);

function crossUrlDecode($sSource) {

 // Troco os caracteres especiais por pelo coringa
 $aOrig   = array('á', 'é', 'í', 'ó', 'ú', 'â', 'ê', 'ô', 'ã', 'õ', 'à', 'è', 'ì', 'ò', 'ù', 'ç', 
                  'Á', 'É', 'Í', 'Ó', 'Ú', 'Â', 'Ê', 'Ô', 'Ã', 'Õ', 'À', 'È', 'Ì', 'Ò', 'Ù', 'Ç'
                 );

 return str_replace($aOrig, '_', mb_convert_encoding($sSource, "ISO-8859-1", "UTF-8"));

}

?>