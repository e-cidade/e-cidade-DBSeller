<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (c) 2014  DBSeller Servicos de Informatica
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

db_postmemory($_SERVER);
db_postmemory($_POST);

$clprocandam    = new cl_procandam;
$clproctransfer = new cl_proctransfer;
$clproctransand = new cl_proctransand;
$clprotprocesso = new cl_protprocesso;
$clarqandam     = new cl_arqandam;
$clprocessosapensados = new cl_processosapensados;
$aProcessosApensados = array();

$db_opcao = 1;
$db_botao = true;

if((isset($p58_codproc))){

  db_inicio_transacao();

  $sqlerro = false;
  $sql  = "select distinct p67_codarquiv, p68_codproc, p67_coddepto, p61_dtandam, p61_hora";
  $sql .= "  from arqproc";
  $sql .= " inner join procarquiv on p68_codarquiv = p67_codarquiv";
  $sql .= " inner join arqandam ";
  $sql .= "    on p69_codarquiv = p67_codarquiv ";
  $sql .= " inner join procandam  ";
  $sql .= "    on p69_codandam = p61_codandam";
  $sql .= " where p68_codproc = {$p58_codproc}";
  $sql .= " order by p68_codproc";
  $rs   = db_query($sql);
  $erro = 0;

  $date = new DateTime;
  $date->setTimestamp(db_getsession('DB_datausu'));
  $dataTransferencia = $date->format('Y-m-d');
  $lErro               = false;
  if(pg_num_rows($rs) > 0) {

    db_fieldsmemory($rs,0);

    $sSqlProcessosApensados = $clprocessosapensados->sql_query_file(null, "*", null, "p30_procprincipal = {$p58_codproc}");
    $rsProcessosApensados = $clprocessosapensados->sql_record($sSqlProcessosApensados);
    for ($iProcesso = 0; $iProcesso < $clprocessosapensados->numrows; $iProcesso++) {
        $aProcessosApensados[] = db_utils::fieldsmemory($rsProcessosApensados, $iProcesso)->p30_procapensado;
    }

    if ( $p61_dtandam > $dataTransferencia ||
      ( $p61_dtandam == $dataTransferencia
      && $p61_hora > db_hora() ) )
    {

      $lErro               = true;
      $sqlerro             = true;
    }

    if( $lErro == false) {
    //inclui a transferencia
    $clproctransfer->p62_coddepto    = $p67_coddepto;
    $clproctransfer->p62_dttran      = $dataTransferencia;
    $clproctransfer->p62_coddeptorec = $p67_coddepto;
    $clproctransfer->p62_id_usorec   = db_getsession("DB_id_usuario");
    $clproctransfer->p62_id_usuario  = db_getsession("DB_id_usuario");
    $clproctransfer->p62_hora        = db_hora();
    $clproctransfer->incluir(null);

    $cod = $clproctransfer->p62_codtran;
    $rsi =  db_query("insert into proctransferproc values($cod,$p58_codproc)");

    if($clproctransfer->erro_status == "1" or !$rsi ) {
      $erro += 0;
    } else {

      $clproctransfer->erro(true,false);
      $sqlerro = true;
    }

    //inclui o andamento
    $clprocandam->p61_despacho   =  "Processo Desarquivado";
    $clprocandam->p61_codproc    = $p68_codproc;
    $clprocandam->p61_dtandam    = $dataTransferencia;
    $clprocandam->p61_hora       = db_hora();
    $clprocandam->p61_id_usuario = db_getsession("DB_id_usuario");
    $clprocandam->p61_coddepto   = $p67_coddepto;
    $clprocandam->p61_publico    =  "t";
    $clprocandam->incluir("");

    if($clprocandam->erro_status == "1"){
      $erro += 0;
    } else {

      $clprocandam->erro(true,false);
      $sqlerro = true;
      return false;
    }

    //inclui  o andamento e o cod. do arquivamento e diz se é arquivamento ou desarquivamento na tabela arqandam
		$clarqandam->p69_codarquiv = $p67_codarquiv;
		$clarqandam->p69_codandam  = $clprocandam->p61_codandam;
		$clarqandam->p69_arquivado = 'false';
		$clarqandam->incluir();

		if($clarqandam->erro_status == "1") {
      $erro += 0;
    } else {

      $clarqandam->erro(true,false);
      $sqlerro = true;
      return false;
    }

    //inclui  a transferencia. e o andamento do processo na tabela proctransand;
    $clproctransand->p64_codtran  = $clproctransfer->p62_codtran;
    $clproctransand->p64_codandam = $clprocandam->p61_codandam;
    $clproctransand->incluir();

    if($clproctransand->erro_status == "1") {
      $erro += 0;
    } else {

      $clproctransand->erro(true,false);
      $sqlerro = true;
      return false;
    }

    //atualiza codandam da tabela protprocesso;
    $clprotprocesso->p58_codproc  = $p58_codproc;
    $clprotprocesso->p58_codandam = $clprocandam->p61_codandam;
    $clprotprocesso->p58_despacho = " ";
    $clprotprocesso->alterar($p58_codproc);

    if($clprotprocesso->erro_status == "1") {
      $erro += 0;
    } else {

      $clprotprocesso->erro(true,false);
      $sqlerro = true;
      return false;
    }

    if($sqlerro == false) {
      db_query("delete from arqproc where p68_codproc = $p58_codproc");
    }
    }

      foreach($aProcessosApensados as $iProcessoApensado) {

          $sql = "select distinct p67_codarquiv,p68_codproc,p67_coddepto
                from arqproc
               inner join procarquiv on p68_codarquiv = p67_codarquiv
               where p68_codproc = $iProcessoApensado
               order by p68_codproc";

          $rssql     = db_query($sql);
          if (pg_num_rows($rssql) == 0) {
              continue;
          }

          $oProcesso = db_utils::fieldsmemory($rssql, 0);

          $rsi =  pg_exec("insert into proctransferproc values($cod, $iProcessoApensado)");
          if(!$rsi) {
              $sqlerro = true;
              break;
          }

          $clprocandam->p61_codproc = $iProcessoApensado;
          $clprocandam->incluir(null);
          if($clprocandam->erro_status == 0) {
              $sqlerro = true;
              $clprocandam->erro(true, false);
              break;
          }

          $clarqandam->p69_codarquiv = $oProcesso->p67_codarquiv;
          $clarqandam->p69_codandam  = $clprocandam->p61_codandam;
          $clarqandam->p69_arquivado = 'false';
          $clarqandam->incluir();
          if ($clarqandam->erro_status == "1"){
              $erro += 0;
          }else{
              $clarqandam->erro(true,false);
              $sqlerro = true;
              break;
          }

          $clproctransand->p64_codtran  = $clproctransfer->p62_codtran;
          $clproctransand->p64_codandam = $clprocandam->p61_codandam;
          $clproctransand->incluir();
          if ($clproctransand->erro_status == "1"){
              $erro += 0;
          }else{
              $clproctransand->erro(true,false);
              $sqlerro = true;
              break;
          }

          $clprotprocesso->p58_codproc  = $iProcessoApensado;
          $clprotprocesso->p58_codandam = $clprocandam->p61_codandam;
          $clprotprocesso->p58_despacho = " ";
          $clprotprocesso->alterar($p58_codproc);
          if ($clprotprocesso->erro_status == "1"){
              $erro += 0;
          }else{
              $clprotprocesso->erro(true,false);
              $sqlerro=true;
              break;
          }
          if ($sqlerro==false){
              pg_exec("delete from arqproc where p68_codproc = $iProcessoApensado");
          }
      }

    if($erro == 0 && $sqlerro==false && $lErro ==false) {
      echo "<script>alert('Processo Desarquivado com Sucesso!! ');</script>";
    } else {

      $sMensagem = "Desarquivamento não Efetuado";

      if($lErro == true) {

        $sMensagem = "A data para o arquivamento é maior que a data da última movimentação. Verifique a data da sessão!";
      }

      echo "<script>alert('".$sMensagem."');</script>";
    }

    db_fim_transacao($sqlerro);
  }
}
echo "<script>location.href='pro4_canarquiv001.php'</script>";
