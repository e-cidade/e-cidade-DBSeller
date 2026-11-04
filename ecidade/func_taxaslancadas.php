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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

use ECidade\Tributario\Arrecadacao\Model\TaxasLancadasDepart;
use ECidade\Tributario\Arrecadacao\Repository\TaxasLancadasDepartRepository;

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$clrotulo = new rotulocampo;
$clrotulo->label("ar44_sequencial");
$clrotulo->label("ar44_descricao");

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <table height="100%" border="0" align="center" cellspacing="0" bgcolor="#CCCCCC">
        <tr height="63" align="center" valign="top">
            <td height="63" align="center" valign="top">
                <table width="35%" border="0" align="center" cellspacing="0">
                    <form name="form2" method="post" action="">
                        <tr>
                            <td title="<?= @$Tar44_sequencial ?>" style="width: 70px;">
                                <?= @$Lar44_sequencial ?>
                            </td>
                            <td>
                                <?php
                                db_input("ar44_sequencial", 5, @$Iar44_sequencial, "ar44_sequencial", "text", 4, "", "chave_ar44_sequencial");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tar44_descricao ?>" style="width: 70px;">
                                <?= @$Lar44_descricao ?>
                            </td>
                            <td>
                                <?php
                                db_input("ar44_descricao", 40, @$Iar44_descricao, "ar44_descricao", "text", 4, "", "chave_ar44_descricao");
                                ?>
                            </td>
                        </tr>
                        <tr> 
                            <td colspan="2" align="center"> 
                                <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
                                <input name="limpar" type="reset" id="limpar" value="Limpar" >
                                <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_taxaslancadas.hide();">
                            </td>
                        </tr>
                    </form>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center" valign="top">
                <?php
                    $cl_taxaslancadas = new \cl_taxaslancadas();

                    $sWhere = "";

                    if (isset($departamentos) AND !empty($departamentos)) {
                        $taxasLancadasDepart = new TaxasLancadasDepart();
                        $taxasLancadasDepartRepository = TaxasLancadasDepartRepository::getInstance();
        
                        $taxasLancadasDepart->sWhere = "(ar45_sequencial IS NULL OR ar45_departamento IN ({$departamentos}))";
        
                        $oDepartamentos = $taxasLancadasDepartRepository->getDepartamentos($taxasLancadasDepart);

                        $sTaxas = implode(",", array_map(function ($oDepartamento) {
                            return $oDepartamento->ar45_taxaslancadas;
                        }, $oDepartamentos));
        
                        $sWhere = " ar44_sequencial IN ({$sTaxas})";    

                        if ($receita == true) {
                            $sWhere .= " AND ar44_receita is not null ";
                        }
                    }

                    if (!isset($pesquisa_chave)) {
                        $sql = $cl_taxaslancadas->sql_query_file("", "", "ar44_sequencial", $sWhere);

                        $repassa = array();

                        if (isset($chave_ar44_descricao)) {
                            $repassa = array(
                                "chave_ar44_sequencial" => $chave_ar44_sequencial,
                                "chave_ar44_descricao" => $chave_ar44_descricao
                            );
                        }

                        db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
                    } else {
                        if (!empty($pesquisa_chave)) {
                            if (!empty($sWhere)) {
                                $sWhere .= " AND ";
                            }

                            $sWhere .= " ar44_sequencial = {$pesquisa_chave}";

                                
                            if ($receita == true) {
                                $sWhere .= " AND ar44_receita is not null ";
                            }

                            $lConsulta = true;

                            if (isset($aTaxas)) {
                                if (!in_array($pesquisa_chave, $aTaxas)) {
                                    $lConsulta = false;    
                                }
                            }

                            if ($lConsulta) {
                                $result = $cl_taxaslancadas->sql_record($cl_taxaslancadas->sql_query(null, "*", null, $sWhere));
                            }

                            if ($cl_taxaslancadas->numrows != 0) {
                                db_fieldsmemory($result, 0);

                                echo "<script>" . $funcao_js . "('$ar44_descricao',false, '$ar44_tipo');</script>";
                            } else {
                                echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
                            }
                        } else {
                            echo "<script>" . $funcao_js . "('',false, '');</script>";
                        }
                    }
                ?>
            </td>
        </tr>
    </table>
</body>
</html>