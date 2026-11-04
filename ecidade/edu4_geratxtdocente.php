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

require_once(modification("fpdf151/pdf.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("classes/db_sau_fecharquivo_classe.php"));
require_once(modification("classes/db_lab_bpamagnetico_classe.php"));
require_once(modification("classes/db_tfd_bpamagnetico_classe.php"));
require_once(modification("dbforms/db_layouttxt.php"));
require_once(modification("classes/db_db_layoutcampos_classe.php"));
require_once(modification("libs/db_utils.php"));

$oDaoRecHumano    = db_utils::getdao("rechumano");
$sCampos          = "z01_nasc as datadenascimento, z01_mae as nomemaedocente, z01_cgccpf as numerocpf, ";
$sCampos         .= " z01_munic as municipionascimento,z01_ufcon as ufnascimento ,z01_nome as nomedocente, ";
$sCampos         .= "ed20_i_codigo as codigodocenteescola, ed20_i_codigoinep as idinep";
$sWhere           = "";
$sWhere          .= " ed18_i_codigo = $iEscola and ed20_i_codigoinep is null "; // and ed52_i_ano = $ed52_i_ano ";
$sSqlRecHumano    = $oDaoRecHumano->sql_query_solicitaseminep("",$sCampos,"",$sWhere);
$rsDocenteTXT     = $oDaoRecHumano->sql_record($sSqlRecHumano);
$iLinhasRecHumano = $oDaoRecHumano->numrows;

if ($iLinhasRecHumano == 0) {

        ?>
          <table width='100%'>
            <tr>
              <td align='center'>
                <font color='#FF0000' face='arial'>
                  <b>Nenhum registro encontrado.<br>
                    <input type='button' value='Fechar' onclick='window.close()'>
                  </b>
                </font>
              </td>
            </tr>
          </table>
        <?php
        exit;
    }




  $sArquivo       = "tmp/arquivodocente.txt";
  $oObjetoLayout  = new db_layouttxt(183, $sArquivo, "");

  for ($iCont = 0; $iCont < $LinhasRecHumano; $iCont++) {
    /*
    $oDado                       = db_utils::fieldsmemory($rsDocenteTXT,$iCont);
    $dData                       = $oDado->datadenascimento;
    $dDataNoArquivo              = db_formatar($oDado->datadenascimento, 'd');
    $oDados->idinep              = str_pad (' ', 15, ' ', STR_PAD_LEFT);
    $oDados->numerocpf           = str_pad ($oDado->numerocpf, 15, ' ', STR_PAD_LEFT);
    $oDados->municipionascimento = str_pad ($oDado->municipionascimento, 10, ' ', STR_PAD_LEFT);
    $oDados->ufnascimento        = str_pad ($oDado->ufnascimento, 10, ' ', STR_PAD_LEFT);
    $oDados->nomemaedocente      = str_pad ($oDado->nomemaedocente, 25, ' ', STR_PAD_LEFT);
    $oDados->datadenascimento    = str_pad ($oDado->datadenascimento, 10,' ', STR_PAD_LEFT);
    $oDados->nomedocente         = str_pad ($oDado->nomedocente, 30, ' ', STR_PAD_LEFT);
    $oDados->codigodocenteescola = str_pad ($oDado->codigodocenteescola, 35, ' ', STR_PAD_RIGHT); 
    */
    $oDados->idinep              = "";
    $oDados->numerocpf           = "";
    $oDados->municipionascimento = "";
    $oDados->ufnascimento        = "";
    $oDados->nomemaedocente      = "Teste do nome do docente";
    $oDados->datadenascimento    = "";
    $oDados->nomedocente         = "";
    $oDados->codigodocenteescola = "";

    $oObjetoLayout->setByLineOfDBUtils($oDados, 1);//codigo do layout

  }

   $oObjetoLayout->fechaArquivo();


  ?>

  <script>
 window.close();
  </script>
  <?php
?>