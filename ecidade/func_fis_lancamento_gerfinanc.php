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

require modification("libs/db_stdlib.php");
require modification("libs/db_conecta.php");
include modification("libs/db_sessoes.php");
include modification("libs/db_usuariosonline.php");
include modification("dbforms/db_funcoes.php");
include modification("classes/db_fis_lancamento_classe.php");
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$cllancamento = new cl_fis_lancamento;
$cllancamento->rotulo->label("nl01_codlanc");
$cllancamento->rotulo->label("y50_nome");
$clrotulo = new rotulocampo;
$clrotulo->label("z01_numcgm");
$clrotulo->label("y80_codsani");
$clrotulo->label("q02_inscr");
$clrotulo->label("j01_matric"); 

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
  <tr>
    <td height="63" align="center" valign="top">
        <table width="35%" border="0" align="center" cellspacing="0">
       <form name="form2" method="post" action="" >
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Tnl01_codlanc?>"><b>Notificação de Lançamento</b></td>
            <td width="96%" align="left" nowrap>
              <?php  db_input("nl01_codlanc",10,0,true,"text",4,"","chave_nl01_codlanc"); ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Tz01_numcgm?>"><?=$Lz01_numcgm?></td>
            <td width="96%" align="left" nowrap>
              <?php  db_input("z01_numcgm",10,$Iz01_numcgm,true,"text",4,"","chave_z01_numcgm"); ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Tj01_matric?>"><?=$Lj01_matric?></td>
            <td width="96%" align="left" nowrap>
              <?php  db_input("j01_matric",10,$Ij01_matric,true,"text",4,"","chave_j01_matric"); ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Tq02_inscr?>"><?=$Lq02_inscr?></td>
            <td width="96%" align="left" nowrap>
              <?php  db_input("q02_inscr",10,$Iq02_inscr,true,"text",4,"","chave_q02_inscr"); ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Ty80_codsani?>"><?=$Ly80_codsani?></td>
            <td width="96%" align="left" nowrap>
              <?php  db_input("y80_codsani",10,$Iy80_codsani,true,"text",4,"","chave_y80_codsani"); ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="Notificação"><b>Notificação</b></td>
            <td width="96%" align="left" nowrap>
              <?php  db_input("y30_codnoti",10,@$y30_codnoti,true,"text",4,"","chave_y30_codnoti"); ?>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
  <tr>
    <td align="center" valign="top">
<?php 

  $dataAtual = date('Y-m-d');
  
  $sGestor  = 'select * from fiscalizacao.fis_cadgestorfiscal where id_usuario = '.db_getsession('DB_id_usuario');
  $rsGestor = pg_query($sGestor);
  $iGestor  = pg_num_rows($rsGestor);

  $where  = ""; 
  $where2 = "where 1=1";
  if( $iGestor > 0 ){
    $where2 .= " AND CASE when y100_sequencial is not null then";
  } else {
   $where2 .= " and";
  } 
  $where2 .= ' (case when fis_grupotipoandamento.sequencial not in (22,11) then ' ;
  $where2 .= ' db_usuarios.id_usuario = '.db_getsession('DB_id_usuario');
  $where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual') ";
  $where2 .= " and fis_processofiscalativo.ativo = 't'";
  $where2 .= " when  nl01_codlanc not in (select nl19_codlanc from fiscalizacao.fis_lancandam) then";
  $where2 .= " db_usuarios.id_usuario =".db_getsession('DB_id_usuario');
  $where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual')";
  $where2 .= " and fis_processofiscalativo.ativo = 't'";
  $where2 .= " else 1=1 end ) ";
  
  if( $iGestor > 0 ){
    $where2 .= " else 1=1 end";
  }

  if(isset($baixado)){
    $where  = " and dl_Notificacao_Lancamento in (select nl18_codlanc from fiscalizacao.fis_lanctipo inner join fiscalizacao.fis_lanctipobaixa on nl23_codlanctipo = nl18_codigo  ";
    $where .= "                                     union all select nl28_codlanc from fiscalizacao.fis_lancmulta inner join lancmultabaixa on nl28_codigo = nl29_codlancmulta ) ";
  }

  if(!isset($pesquisa_chave)){
        if(isset($chave_nl01_codlanc) && (trim($chave_nl01_codlanc)!="") ){
          $sql = $cllancamento->sql_query_busca($chave_nl01_codlanc," nl01_instit = ".db_getsession('DB_instit')." and dl_Notificacao_Lancamento=$chave_nl01_codlanc ".$where." order by dl_Notificacao_Lancamento desc", $where2);
        }elseif(isset($chave_q02_inscr) && (trim($chave_q02_inscr)!="") ){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_identificacao='Inscrição' and dl_codigo=$chave_q02_inscr ".$where." order by dl_Notificacao_Lancamento desc", $where2);
        }elseif(isset($chave_j01_matric) && (trim($chave_j01_matric)!="") ){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_identificacao='Matricula' and dl_codigo=$chave_j01_matric ".$where." order by dl_Notificacao_Lancamento desc", $where2);
        }elseif(isset($chave_z01_numcgm) && (trim($chave_z01_numcgm)!="") ){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_identificacao='Cgm' and dl_codigo=$chave_z01_numcgm ".$where." order by dl_Notificacao_Lancamento desc", $where2);
        }elseif(isset($chave_y80_codsani) && (trim($chave_y80_codsani)!="")){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_identificacao='Sanitario' and dl_codigo=$chave_y80_codsani ".$where." order by dl_Notificacao_Lancamento desc", $where2);
        }elseif(isset($chave_y30_codnoti) && (trim($chave_y30_codnoti)!="")){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_identificacao='Notificacao' and dl_codigo=$chave_y30_codnoti ".$where." order by dl_Notificacao_Lancamento desc", $where2);
        }elseif(isset($chave_y50_numbloco) && (trim($chave_y50_numbloco)!="")){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and x.y50_numbloco = $chave_y50_numbloco ".$where." order by dl_Notificacao_Lancamento desc", $where2);
        }elseif(isset($chave_procfiscal) && (trim($chave_procfiscal)!="")){
               $sql = $cllancamento->sql_query_busca_procfis(null," nl01_instit = ".db_getsession('DB_instit').$where." and dl_Processo_Fiscal = ".$chave_procfiscal." order by dl_Notificacao_Lancamento desc", $where2);
        }elseif(isset($chave_procfiscal) && (trim($chave_procfiscal)=="")){
               $sql = $cllancamento->sql_query_busca_procfis(null," nl01_instit = ".db_getsession('DB_instit').$where." order by dl_Notificacao_Lancamento desc", $where2);
        }else{
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit').$where." order by dl_Notificacao_Lancamento desc" , $where2);
        }
        db_lovrot($sql,12,"()","",$funcao_js);
  
  }else{
        
    if($pesquisa_chave!=null && $pesquisa_chave!=""){
      
      $result = $cllancamento->sql_record($cllancamento->sql_query_busca($pesquisa_chave," nl01_instit = ".db_getsession('DB_instit')." and dl_Notificacao_Lancamento=$pesquisa_chave ".$where, $where2));
      
      if($cllancamento->numrows!=0){
          db_fieldsmemory($result,0);
          if(isset($buscabloco)){
              echo "<script>".$funcao_js."('$dl_numero_bloco',false);</script>";
          }else{
              echo "<script>".$funcao_js."('$z01_nome',false);</script>";
          }
      }else{
         echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
      }
    }else{
        echo "<script>".$funcao_js."('',false);</script>";
    }
  }
      ?>
     </td>
   </tr>
</table>
</body>
</html>

<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
