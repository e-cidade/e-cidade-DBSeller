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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_veicdevolucao_classe.php"));
require_once(modification("classes/db_veiculos_classe.php"));
require_once(modification("classes/db_veictipoabast_classe.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));

db_app::import("veiculos.*");
parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);

$clveicdevolucao = new cl_veicdevolucao;
$clveiculos      = new cl_veiculos;
$clveictipoabast = new cl_veictipoabast;
$clveicabast = new cl_veicabast;
$db_opcao = 22;
$db_botao = false;
$sqlerro = false;

if (isset($alterar)) {
  db_inicio_transacao();

  try {
    $dataDevolucaoComHora = DateTime::createFromFormat('d/m/Y H:i', "{$ve61_datadevol} {$ve61_horadevol}");
    $dataSaidaComHora = DateTime::createFromFormat('d/m/Y H:i', "{$ve60_datasaida} {$ve60_horasaida}");
    $medidaDevolucao = str_replace(".", "", $ve61_medidadevol);
    $medidaSaida = str_replace(".", "", $ve60_medidasaida);

    if ($dataDevolucaoComHora < $dataSaidaComHora) {
      throw new Exception('Data e Hora de Devolução devem ser maiores ou iguais a Data e Hora de Retirada!');
    }

    if ($medidaDevolucao < $medidaSaida) {
      throw new Exception('Medida na Devolução deve ser maior que a Medida na Retirada!');
    }

    /*
     * Verificamos se existem abastecimentos registrados para esse veículo,
     * caso existam, verifica se a devolução é maior ou igual que a data e a hora do último abastecimento.
     */
    $campos = "ve70_dtabast, ve70_hora";
    $where = "ve70_veiculos = {$ve60_veiculo} and ve73_veicretirada = {$ve61_veicretirada}";
    $sqlAbastecimentosRealizados = @$clveicabast->sql_query_info(null, $campos, 've70_dtabast DESC, ve70_hora DESC', $where);
    $recordsetAbastecimentosRealizados = db_query($sqlAbastecimentosRealizados);
    $abastecimentosRealizados = db_utils::getCollectionByRecord($recordsetAbastecimentosRealizados);

    if (count($abastecimentosRealizados) > 0) {
      $ultimoAbastecimento = $abastecimentosRealizados[0];
      $ultimoAbastecimento = DateTime::createFromFormat('Y-m-d H:i', "{$ultimoAbastecimento->ve70_dtabast} {$ultimoAbastecimento->ve70_hora}");

      if ($dataDevolucaoComHora  < $ultimoAbastecimento) {
        throw new Exception('Data e Hora da Devolução não podem ser menores que Data e Hora do Abastecimento.');
      }
    }
    $clveicdevolucao->alterar($ve61_codigo);
  } catch (Exception $e) {
    $clveicdevolucao->erro_status = "0";
    $sqlerro = true;
    $clveicdevolucao->erro_msg = $e->getMessage();
    $db_opcao = 2;

    db_fim_transacao(true);
  }

  db_fim_transacao();
} else if (isset($chavepesquisa)) {
  $db_opcao = 2;
  $result = $clveicdevolucao->sql_record($clveicdevolucao->sql_query($chavepesquisa));
  db_fieldsmemory($result, 0);
  $db_botao = true;

  $result = $clveiculos->sql_record($clveiculos->sql_query($ve60_veiculo, "ve01_veictipoabast"));
  db_fieldsmemory($result, 0);

  $result_veictipoabast = $clveictipoabast->sql_record($clveictipoabast->sql_query($ve01_veictipoabast, "ve07_sigla"));
  if ($clveictipoabast->numrows > 0) {
    db_fieldsmemory($result_veictipoabast, 0);
  }
}
?>

<html>

<script>
  js_tabulacaoforms("form1", "ve61_veicretirada", true, 1, "ve61_veicretirada", true);
</script>

<?php
require_once(modification("forms/db_frmveicdevolucao.php"));
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>

<?php
if (isset($alterar)) {
  if ($sqlerro == true) {
    db_msgbox($clveicdevolucao->erro_msg);
    $db_botao = true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if ($clveicdevolucao->erro_campo != "") {
      echo "<script> document.form1." . $clveicdevolucao->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1." . $clveicdevolucao->erro_campo . ".focus();</script>";
    }
  } else {
    $clveicdevolucao->erro(true, true);
  }
}

if ($db_opcao == 22) {
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>


</html>
