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



$sSqlBuscaInfo  =  " select ed47_d_nasc as datanascimento,";
$sSqlBuscaInfo .= "         ed47_v_nome as nomealuno,";
$sSqlBuscaInfo .= "         ed47_v_pai as nomepaialuno, ";
$sSqlBuscaInfo .= "         ed47_v_mae as nomemaealuno,";
$sSqlBuscaInfo .= "         ed47_i_codigo as idalunoinep, ";
$sSqlBuscaInfo .= "         ed47_i_censomunicnat as municipionascimento,";
$sSqlBuscaInfo .= "         ed47_i_censoufnat as ufnascimento,";
$sSqlBuscaInfo .= "         ed47_c_codigoinep as codigoalunoinep";
$sSqlBuscaInfo .= "         from aluno";
$sSqlBuscaInfo .= "              inner join censomunic on censomunic.ed261_i_codigo = ed47_i_censomuniccert";
$sSqlBuscaInfo .= "              inner join escola on escola.ed18_i_censomunic = ed261_i_codigo ";
$sSqlBuscaInfo .= "              inner join calendarioescola on calendarioescola.ed38_i_escola = escola.ed18_i_codigo";
$sSqlBuscaInfo .= "              inner join calendario on calendario.ed52_i_codigo = calendarioescola.ed38_i_calendario";
$sSqlBuscaInfo .= "         where ed47_c_codigoinep = '' ";
$sSqlBuscaInfo .= "           and ed18_i_codigo = $iEscola ";
$sSqlBuscaInfo .= "           and ed52_i_ano = $ed52_i_ano";
$rsBuscaInfo    = db_query($sSqlBuscaInfo);
$iLinhas        = pg_num_rows($rsBuscaInfo);

if ($iLinhas == 0) {

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

$sArquivo       = "tmp/arquivoaluno.txt";

/*Liga o arquivo ao layout, 
 *é passado o codigo do layout cadastrado, 
 *e o arquivo que vai fazer referencia ao layout
 */

$oObjetoLayout  = new db_layouttxt(182, $sArquivo,"");

for ($iContador = 0; $iContador < $iLinhas; $iContador++) {
  
  /*Seta o objeto do layout com os nomes dos campos criados, 
   *recebendo com a formatação do str_pad os valores dos campos vindos do banco de dados.
   */
  $oDados = db_utils::fieldsmemory($rsBuscaInfo, $iContador);
  
  $oLayout->idalunoinep         = $oDados->idalunoinep;//str_pad($oDados->idalunoinep,"10"," ", STR_PAD_RIGHT);
  $oLayout->nomealuno           = str_pad($oDados->nomealuno, "15", " ", STR_PAD_LEFT);
  $oLayout->nomepaialuno        = str_pad($oDados->nomepaialuno, "15", " ", STR_PAD_LEFT);
  $oLayout->nomemaealuno        = str_pad($oDados->nomemaealuno, "14", " ", STR_PAD_LEFT);
  $oLayout->codigoalunoinep     = str_pad (" ", 15, " ", STR_PAD_LEFT);
  $oLayout->municipionascimento = $oDados->municipionascimento;//str_pad($oDados->municipionascimento, "15", " ", STR_PAD_LEFT);
  $oLayout->ufnascimento        = $oDados->ufnascimento;//str_pad($oDados->ufnascimento, "15", " ", STR_PAD_LEFT);
  $oLayout->datanascimento      = $oDados->datanascimento;//str_pad($oDados->datanascimento, "15", " ", STR_PAD_LEFT);
  $oObjetoLayout->setByLineOfDBUtils($oLayout, 1);
  /*Escreve no arquivo as informações que estão presentes no objeto oLayout, 
   *passado ainda o codigo que faz referencia ao layout cadastrado.
   */

}

  //Feicha o arquivo criado.
  $oObjetoLayout->fechaArquivo();  

?>
<script>
//feicha a janela que abriu quando o fonte foi chamado.
window.close();
</script>