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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_protprocesso_classe.php"));
include(modification("classes/db_procvar_classe.php"));
include(modification("classes/db_proctipovar_classe.php"));
include(modification("classes/db_db_syscampo_classe.php"));
include(modification("dbforms/db_funcoes.php"));

require_once('model/protocolo/ProcessoProtocoloNumeracao.model.php');

$clprotprocesso = new cl_protprocesso;
$rotulo = new rotulocampo();
$rotulo->label("p58_codproc");

?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>

<?php
  if (ProcessoProtocoloNumeracao::getTipoConfiguracao() == ProcessoProtocoloNumeracao::TIPOORGAO) { ?>
    function js_processo(valor, nome) {
      var parametros = {
        exec: 'buscarVolumes',
        codigoProcesso: valor,
        desarquivamento: true
      };

      new AjaxRequest(
        'pro4_protprocessovolume.RPC.php',
        parametros,
        function(retorno, erro) {
          if (retorno.erro) {
            alert(retorno.message.urlDecode());
            return;
          }

          if (confirm("Confirma desarquivamento do Processo " + valor + " - " + nome + " ?")) {
            if (retorno.volumes.length > 0) {
              var mensagem = "Deseja desarquivar os Volumes desse Processo:\n\n";
              var volumes = '';

              retorno.volumes.forEach(function(volume) {
                mensagem += `${volume.p58_numero}\n`;
              });

              if (confirm(mensagem)) {
                location.href=`pro4_desarquivamentovolumes.php?processopai=${valor}`;
              } else {
                location.href='pro4_canarquiv002.php?p58_codproc=' + valor;
              }
            } else {
              location.href='pro4_canarquiv002.php?p58_codproc=' + valor;
            }
          }
        }
      ).execute();
    }
<?php
  } else { ?>
    function js_processo(valor,nome){
      if (confirm("Confirma desarquivamento do Processo "+valor+" - "+nome+" ?")){
        location.href='pro4_canarquiv002.php?p58_codproc='+valor;
      }
    }
<?php
  } ?>

</script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr>
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td  align="left" valign="top" bgcolor="#CCCCCC">
    <center>
    <table>
    <form method="post" name="form2" action="">
    <tr>
       <td title="<?=$Tp58_codproc?>">
           <?=$Lp58_codproc;?>
       </td>
       <td>
          <input type='text' value="" name="p58_codproc">
       </td>
       <td> <input type="button" value="Pesquisar" onclick="document.form2.submit()">
    </tr>
    </form>
    </table>
     <?php
      $where  = " p67_coddepto = ".db_getsession("DB_coddepto");
      $where .= " and p69_codarquiv is null";
//      $where .= " and not exists (select 1
//    	                            from arqandam
//    	                           where p69_codarquiv = procarquiv.p67_codarquiv
//    	                             and p69_arquivado is false)";
    	if(isset($p58_codproc) && $p58_codproc != '' ){
    	  $where .= " and p68_codproc={$p58_codproc}";
    	}
      $sql = "SELECT distinct on(p67_codproc)
                     p67_codproc,
                     cast(p58_numero||'/'||p58_ano::varchar as varchar) as p58_numero,
                     p67_dtarq,
                     (case when p58_requer isnull then z01_nome else p58_requer end) as dl_Requerente,
                     p67_historico
               from  procarquiv
                     inner join protprocesso on p67_codproc = p58_codproc
                     inner join cgm on p58_numcgm = z01_numcgm
                     inner join arqproc on p67_codproc = p68_codproc
                     left join arqandam on p69_codarquiv = p67_codarquiv and p69_arquivado is false
               where {$where} and not exists (select 1 from processosapensados where p30_procapensado = p58_codproc)
               order by p67_codproc ";
    $rs = db_query($sql);
    // die($sql);
   // db_lovrot($query, $numlinhas, $arquivo = "", $filtro = "%", $aonde = "_self", $campos_layer = "", $NomeForm = "NoMe", $variaveis_repassa = array (), $automatico = true, $totalizacao = array()) {
    db_lovrot($sql,20,"()","","js_processo|p67_codproc|dl_requerente",'',"NoMe",array(),false);

  ?>
 </center>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
