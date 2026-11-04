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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("libs/db_app.utils.php");

  $cliptuconstrhabite = new cl_iptuconstrhabite();

  db_app::load("scripts.js");
  db_app::load("prototype.js");
  db_app::load("datagrid.widget.js");
  db_app::load("strings.js");
  db_app::load("arrays.js");
  db_app::load("grid.style.css");
  db_app::load("estilos.css");
  db_app::load("classes/dbViewAvaliacoes.classe.js");
  db_app::load("widgets/windowAux.widget.js");
  db_app::load("widgets/dbmessageBoard.widget.js");
  db_app::load("dbcomboBox.widget.js");
  db_app::load("widgets/Collection.widget.js");
  db_app::load("widgets/DatagridCollection.widget.js");
  db_app::load("classes/http/http.js");

  $oRotulo = new rotulocampo;
  $oRotulo->label('j83_pontos');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
function js_averba(codigo){
	js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_averba','cad3_averbacao002.php?codigo='+codigo,'Pesquisa',true);
}

/*
 * Funções JS criadas para exibir e reemitir certidões de existencias
 *
 */

/*
 * para nao definir no escopo global do JS um RPC fixo
 */
function js_getRpcCertidao(){

  var sUrlRPC = "cad4_certidaoexistenciaconstrucao.RPC.php";
  return sUrlRPC;
}

function js_gridConstrucoes() {

 oGridConstrucoes              = new DBGrid('Construcoes');
 oGridConstrucoes.nameInstance = 'oGridConstrucoes';
    oGridConstrucoes.setCellWidth([
        '100px',
                                           '100px' ,
                                           '120px',
                                           '150px',
                                           '150px',
        '100px']);
    oGridConstrucoes.setCellAlign([
        'center',
                                           'left'  ,
                                           'center'  ,
                                           'left',
                                           'center',
        'center']);
    oGridConstrucoes.setHeader([
        'Sequencial',
                                        'Construção',
                                        'Data Emissão',
                                        'Usuário',
                                        'Arquivo',
        '']);
 oGridConstrucoes.setHeight(150);
 oGridConstrucoes.show($('ctnGridConstrucoes'));
 oGridConstrucoes.clearAll(true);

}

function js_getCertidoes(iMatricula) {

  oGridConstrucoes.clearAll(true);

  var sUrlRPC            = js_getRpcCertidao();
  var msgDiv             = "Carregando Lista de Certidões \n Aguarde ...";
    var oParametros = {};

  oParametros.exec       = 'getCertidao';
  oParametros.iMatricula = iMatricula;

  js_divCarregando(msgDiv,'msgBox');

   var oAjaxLista  = new Ajax.Request(sUrlRPC,
                                             {method: "post",
                                              parameters:'json='+Object.toJSON(oParametros),
                                              onComplete: js_certidoes
                                             });
}

function js_certidoes(oAjax) {

    js_removeObj('msgBox');

    var oRetorno = JSON.parse(oAjax.responseText);

    if (oRetorno.iStatus == 1) {

      oRetorno.aDados.each(
          function (oDado, iInd) {

              var aRow = [];
                  aRow[0] = oDado.j133_sequencial  ;
                  aRow[1] = oDado.j133_iptuconstr  ;
                  aRow[2] = oDado.j133_data        ;
                  aRow[3] = oDado.login.urlDecode();
                  aRow[4] = oDado.j133_arquivo     ;
                  aRow[5] = "<input type='button' value='Reemitir' onclick='js_reemiteCertidaoExistencia("+aRow[0]+")' /> ";

                  oGridConstrucoes.addRow(aRow);
             });
      oGridConstrucoes.renderRows();

    } else {

      alert(oRetorno.sMessage.urlDecode());
      return false;
    }
}

function js_reemiteCertidaoExistencia(iCertidao){

  oJanela = window.open("cad4_certidaoexistencia003.php?iCodigoCertidao=" + iCertidao);
}

function js_reemissao(oAjax) {

  js_removeObj('msgBox');

  var oRetorno = JSON.parse(oAjax.responseText);

  if (oRetorno.iStatus == 1) {

    alert('reemitir');

  } else {

    alert(oRetorno.sMessage.urlDecode());
    return false;
  }
}
</script>
<style type="text/css">
.db_area {
  font-family : courier;
}
.cores:nth-child(even) {
    background: #FFF;
}
.cores:nth-child(odd) {
    background: #efefef;
}
table.form-container tr td {
    font-weight: normal !important;
}
</style>
</head>
<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php
  db_postmemory($_GET,0);
  $btnmatric = @$parametro;

  /**
   * if acrecentado para verificar certidoes de  existencia de construções
   */

  if ($solicitacao == 'certidaoConstrucao'){

      echo "  <center>                                                                                            ";
      echo "    <fieldset style='margin-top:20px; width:600px;'>                                                  ";
      echo "    <legend><strong>Certidões Cadastradas</strong></legend>                                           ";
      echo "      <table width='70%' border='0' align='center' cellpadding='0' cellspacing='2'>                   ";
      echo "        <tr align='left'>                                                                             ";
      echo "          <td colspan='6' >                                                                           ";
      echo "            <div id='ctnGridConstrucoes' style='margin-top: 10px;'> </div>                            ";
      echo "          </td>                                                                                       ";
      echo "        </tr>                                                                                         ";
      echo "      </table>                                                                                        ";
      echo "    </fieldset>                                                                                       ";
      echo "  <center>                                                                                            ";

      echo "<script>                ";
      echo " js_gridConstrucoes();  ";
      echo " js_getCertidoes($parametro);     ";
      echo "</script>               ";

   }else if ($solicitacao == 'dadosbaixa'){

    	$sqlDadosBaixa  = " select *, ";
      $sqlDadosBaixa .= "   (select (p58_numero || '/' || p58_ano) as processo ";
      $sqlDadosBaixa .= "     from protprocesso where p58_codproc = j03_codproc) as numerodoprocesso ";
      $sqlDadosBaixa .= " from iptubaixa ";
    	$sqlDadosBaixa .= "   inner join db_usuarios   on id_usuario = j02_usuario ";
    	$sqlDadosBaixa .= "   left  join iptubaixaproc on j02_matric = j03_matric  ";
    	$sqlDadosBaixa .= " where j02_matric = $parametro ";
    	$rsDadosBaixa   = db_query($sqlDadosBaixa);
    	db_fieldsmemory($rsDadosBaixa, 0);
    	echo " <table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'> ";
    	echo "   <tr>  ";
    	echo "     <td colspan='8' align='center'><u>Dados da Baixa</u></td> ";
    	echo "   </tr> ";
    	echo "   <tr>  ";
    	echo "     <td colspan='8'>&nbsp;</td> ";
    	echo "   </tr> ";

    	echo "   <tr align='left'>  ";
    	echo "     <td nowrap> Usuario : </td> ";
    	echo "     <td align='left' width='70%' wrap> $nome </td> ";
    	echo "   </tr> ";
    	echo "   <tr align='left'>  ";
    	echo "     <td nowrap> Data da Baixa : </td> ";
    	echo "     <td align='left' wrap> ".db_formatar($j02_dtbaixa,'d')." </td> ";
    	echo "   </tr> ";
    	echo "   <tr align='left'>  ";
    	echo "     <td nowrap> Motivo : </td> ";
    	echo "     <td align='left' wrap> $j02_motivo </td> ";
    	echo "   </tr> ";
    	echo "   <tr align='left'>  ";
    	echo "     <td nowrap> Código do processo : </td> ";
    	echo "     <td align='left' wrap> $numerodoprocesso </td> ";
    	echo "   </tr> ";

    	echo " </table> ";

 } else if ($solicitacao == "anexos"){
	include_once modification("cad4_iptuarquivo002.php");
 }else if ($solicitacao == "CaracteristicasDoImovel") {

?>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
  <tr>
    <td colspan="8" align="center"><u>Caracter&iacute;sticas do im&oacute;vel</u></td>
  </tr>
  <tr>
    <td colspan="8">&nbsp;</td>
  </tr>
  <tr align="center" bgcolor="#CCCCCC">
    <td width="20" nowrap>Caracter.</td>
    <td align="left" nowrap>Descri&ccedil;&atilde;o</td>
    <td width="20" align="center" nowrap>Grupo</td>
    <td align="left" nowrap>Descri&ccedil;&atilde;o</td>
    <td align="center" nowrap>Pontos</td>
  </tr>
  <?php
  // recebe o j01_idbql por parametro.
  $sql = "select *
            from carlote, caracter, cargrup
	         where j35_idbql = $parametro1
             and j35_caract = j31_codigo
             and j31_grupo = j32_grupo
		  order by j31_grupo";

  $result = db_query($sql);
	for ($contador=0;$contador < pg_num_rows($result);$contador ++ ){
	  db_fieldsmemory($result,$contador);
  ?>
  <tr align="center">
    <td width="20" nowrap bgcolor="#666666"> <font color="#FFFFFF">
      <?=$j35_caract;?>
      </font> &nbsp;</td>
    <td width="40" align="left" nowrap bgcolor="#CCCCCC">
      <?=substr($j31_descr, 0, 40);?>
      &nbsp;</td>
    <td width="20" align="center" nowrap bgcolor="#666666"> <font color="#FFFFFF">
      <?=$j31_grupo;?>
    </td>
    <td width="215" align="left" nowrap bgcolor="#CCCCCC">
      <?=substr($j32_descr, 0, 30);?>
    </td>
    <td width="40" align="center" nowrap bgcolor="#CCCCCC">
      <?=$j31_pontos;?>
    </td>
  </tr>
  <?php
  }
?>
</table>
<?php
  } else if ($solicitacao == "Isencoes") {

  $sql = "select distinct iptuisen.*, tipoisen.*, login, nome
            from iptuisen
                 inner join db_usuarios on db_usuarios.id_usuario = iptuisen.j46_idusu
                 inner join tipoisen    on tipoisen.j45_tipo      = iptuisen.j46_tipo
                 inner join isenexe     on iptuisen.j46_codigo    = isenexe.j47_codigo
		       where j46_matric = $parametro
		    order by j46_dtini desc";
  $result = db_query($sql);
?>
<table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
  <tr align="center">
    <td colspan="2" nowrap><u>Isen&ccedil;&otilde;es</u></td>
  </tr>
  <tr>
    <td colspan="2" nowrap>&nbsp;</td>
  </tr>
  <?php
  if( pg_num_rows($result) != 0 ) {

    for ($contador=0;$contador < pg_num_rows($result);$contador ++ ){

	  db_fieldsmemory($result,$contador);

	  $result_lim = db_query("select j47_anousu
			                        from isenexe
		                         where j47_codigo = $j46_codigo
                          order by j47_anousu ");

	  $numrows = pg_num_rows($result_lim);

	  if ($numrows > 0) {
  ?>

  <tr>
    <td nowrap bgcolor="#CCCCCC">&nbsp;Código</td>
    <td width="80%" nowrap bgcolor="#FFFFFF">
      &nbsp;
      <?=$j46_codigo;?>
      &nbsp;
    </td>
  </tr>

  <tr>
    <td nowrap bgcolor="#CCCCCC">&nbsp;Inclusão</td>
    <td   width="80%" nowrap bgcolor="#FFFFFF">&nbsp;
      <?=db_formatar($j46_dtinc, 'd');?> - pelo usuário: <?=$login;?> (<?=$nome;?>)
      &nbsp;
    </td>
  </tr>

  <tr>
    <td nowrap bgcolor="#CCCCCC">&nbsp;Validade</td>
    <td   width="80%" nowrap bgcolor="#FFFFFF">&nbsp;
    <?php

      $sValidade = "A partir de " . db_formatar($j46_dtini,'d') . ".";

      if( !empty($j46_dtfim) ){
        $sValidade = "De " . db_formatar($j46_dtini,'d') . " a " . db_formatar($j46_dtfim,'d') . ".";
      }

      echo $sValidade;
    ?>
    </td>
  </tr>
  <tr>
    <td width="20%" nowrap bgcolor="#CCCCCC">&nbsp;Tipo</td>
    <td nowrap bgcolor="#FFFFFF"> &nbsp;
      <?=substr($j45_descr, 0, 30);?>
      &nbsp; </td>
  </tr>
  <tr>
    <td nowrap bgcolor="#CCCCCC">&nbsp;Motivo</td>
    <td nowrap bgcolor="#FFFFFF"> &nbsp;
    <?php
		  echo "<div style='margin-left:3px; margin-top:-10px; overflow-y:auto; white-space:normal; width:500px; height:70px;'>". $j46_hist ."</div>";
		?>
      &nbsp;
		</td>
  </tr>
  <tr>
    <td nowrap bgcolor="#CCCCCC">&nbsp;</td>
    <td nowrap bgcolor="">
     <center>
     	<input name='imprimir<?=$contador;?>' type='button' value='Imprimir Certidão de Isenção' onclick="js_emiteCertidao('<?=$j46_codigo;?>','<?=$parametro;?>')">
		 </center>
     <input name='btnmatric<?=$contador;?>' type='hidden' value='' onclick="">
    </td>
  </tr>
  <tr>
    <td colspan="2" nowrap>&nbsp;</td>
  </tr>
  <?php
    }
	}
  } else {
?>
  <tr>
    <td  colspan="2" align="center" nowrap><strong>Sem Isenções</strong></td>
  </tr>
  <?php
  }
?>
</table>
<?php

  } else if ($solicitacao == "Construcoes") {

    $sql = "select distinct
                  j39_matric as matric,
                  j39_idcons as idcons,
                  j39_codigo as cod,j14_nome,j88_sigla,
                  j39_area   as area,
                  j39_ano    as ano,
                  j39_pavim,
                  j39_numero as numero,
                  j39_compl  as compl,
	                j48_caract,
	                j31_descr,
	                j31_grupo,
	                j32_descr,
	                j31_pontos,
		              case
		                when j39_idprinc
		                  then 'SIM'
		                else 'NAO'
		              end as j39_idprinc,
		              case
		                when j39_dtdemo is null
		                  then
		                    case
		                      when j60_matric is null
		                        then 'NAO'
		                      else 'PARCIALMENTE'
		                    end
		                else 'SIM'
		              end as demolida,
		              j39_dtdemo,
		              j39_areap,
		              case
		                when j39_idaument > 0
		                  then 'AMPLIAÇÃO DA CONSTRUÇÃO '||j39_idaument
		                else 'NOVA'
		              end as j39_idaument,
		              j39_dtlan,
		              j39_habite,
		              j39_obs,
                  j83_pontos
             from iptuconstr
		              left  join carconstr      on j39_matric        = j48_matric
		                                       and j39_idcons        = j48_idcons
		              left  join caracter       on j31_codigo        = j48_caract
		              left  join cargrup        on cargrup.j32_grupo = caracter.j31_grupo
		              inner join ruas           on ruas.j14_codigo   = iptuconstr.j39_codigo
                              inner join ruastipo       on ruas.j14_tipo     = ruastipo.j88_codigo
		              left  join iptuconstrdemo on j60_matric        = j39_matric
		                                       and j60_idcons        = j39_idcons
                  left  join iptuconstrpontos on j83_matric      = j39_matric
		                                         and j83_idcons      = j39_idcons
	          where j39_matric = $parametro
	            and j39_dtdemo is null
		     order by j39_idcons";

	  $tituloJanela = "Construções Levantadas";
    $result       = db_query($sql);
    $aResult      = db_utils::getCollectionByRecord($result);
    $id_numero    = 0;
    ?>
    <table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
      <tr align="center">
        <td colspan="4">
          <u> <?php echo $tituloJanela; ?> </u>
        </td>
      </tr>
      <tr align="center">
        <td colspan="4">&nbsp; </td>
      </tr>
    </table>
    <?php

    if( pg_num_rows($result) != 0 ) {
      $sqlUtilpontos = "select j18_utilpontos from cfiptu where j18_anousu = ".db_getsession("DB_anousu");
      $resultUtilpontos = db_query($sqlUtilpontos);
      $utilizaPontosConstrucao = pg_fetch_result($resultUtilpontos,0);

      //Verifica se utiliza os pontos da construção ou não.
      $pontos = [];
      if($utilizaPontosConstrucao){
        foreach ($aResult as $row) {
          if (isset($pontos[$row->idcons])) {
            continue;
          }
          $pontos[$row->idcons] = $row->j83_pontos;
        }
      } else {
        foreach ($aResult as $row) {
          if (isset($pontos[$row->idcons])) {
            $pontos[$row->idcons] += $row->j31_pontos;
            continue;
          }
          $pontos[$row->idcons] = $row->j31_pontos;
        }
      }

      for ($contador=0;$contador < pg_num_rows($result);$contador ++ ){

        db_fieldsmemory($result,$contador);
          if( $id_numero != $idcons ){
            $impcar = 0;
            $id_numero = $idcons;
        ?>
            <!-- Fechando table e fieldset das caracteristicas ao começar uma nova construção -->
            </table>
            </fieldset>

            <td colspan="4" align="center" nowrap> <font color="#000000">&nbsp; </font></td>

            <table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
              <tr align="center">
                <td align="left" nowrap bgcolor="#CCCCCC">Constru&ccedil;&atilde;o
                </td>
                <td width="30%" align="left" nowrap bgcolor="#FFFFFF">
                  <?php echo $idcons; ?>
                  &nbsp;
                </td>
                <td width="30%" align="left" nowrap bgcolor="#CCCCCC">Lançamento:
                </td>
                <td align="left" nowrap bgcolor="#FFFFFF">
                  <?php echo db_formatar($j39_dtlan, 'd'); ?>
                  &nbsp;
                </td>
              </tr>
              <tr>
                <td align="left" nowrap bgcolor="#CCCCCC" >Ano Constru&ccedil;&atilde;o:
                </td>
                <td width="40%" align="left" nowrap bgcolor="#FFFFFF" >
                  <?php echo $ano; ?>
                  &nbsp;
                </td>
                <td width="30%" align="left" nowrap bgcolor="#CCCCCC">Principal:
                </td>
                <td align="left" nowrap bgcolor="#FFFFFF">
                  <?php echo $j39_idprinc; ?>
                  &nbsp;
                </td>
              </tr>
              <tr align="center">
                <td   width="10%" align="left" nowrap bgcolor="#CCCCCC">&Aacute;rea:</td>
                <td align="left" nowrap bgcolor="#FFFFFF">
                  <?php echo db_formatar($area, 'p'); ?>
                  &nbsp; </td>
                <td width="17%" align="left" nowrap bgcolor="#CCCCCC">&Aacute;rea privada:</td>
                <td align="left" nowrap bgcolor="#FFFFFF">
                  <?php echo db_formatar($j39_areap, 'p'); ?>
                  &nbsp; </td>
              </tr>
              <tr align="center">
                <td   width="17%" align="left" nowrap bgcolor="#CCCCCC">Demolida:</td>
                <td align="left" nowrap bgcolor="#FFFFFF">
                  <?php echo $demolida; ?>
                  &nbsp; </td>
                <td width="17%" align="left" nowrap bgcolor="#CCCCCC">Data demolicao:</td>
                <td align="left" nowrap bgcolor="#FFFFFF">
                  <?php echo $j39_dtdemo; ?>
                  &nbsp; </td>
              </tr>
              <tr align="center">
                <td   width="17%" align="left" nowrap bgcolor="#CCCCCC">Origem:</td>
                <td align="left" nowrap bgcolor="#FFFFFF">
                  <?php echo $j39_idaument; ?>
                  &nbsp; </td>
                </td>
                <td width="30%" align="left" nowrap bgcolor="#CCCCCC">Habite-se:
                </td>
                <td align="left" nowrap bgcolor="#FFFFFF">
                  <?php echo db_formatar($j39_habite, 'd'); ?>
                  &nbsp;
                </td>
              </tr>
            </table>
            <table  width="95%" align="center">
              <tr>
                <td width="17%" align="left"  bgcolor="#CCCCCC" >Frente: </td>
                <td colspan="3" align="left"  bgcolor="#FFFFFF" >
                  <?=trim($j88_sigla . " " . $j14_nome);?><?=($numero > 0 ? "," : "");?>
                  <?=$numero;?><?=($compl != "" ? "/" : "");?>
                  <?=$compl;?>
                  &nbsp;
                </td>
              </tr>
              <tr>
                <td align="left"  bgcolor="#CCCCCC" >Pavimento: </td>
                <td align="left"  bgcolor="#FFFFFF" >
                  <?=$j39_pavim;?>
                  &nbsp;
                </td>
                <td width="30%" align="left" nowrap bgcolor="#CCCCCC"><?="$Sj83_pontos:"?>
                </td>
                <td width="13%" align="left" nowrap bgcolor="#FFFFFF"><?=db_formatar($pontos[$idcons], 'p');?>&nbsp;</td>
              </tr>
              <tr>
                <td align="left"  bgcolor="#CCCCCC" >Observações: </td>
                <td colspan="3" align="left" bgcolor="#FFFFFF" >
                  <?=$j39_obs;?>
                  &nbsp;
                </td>
              </tr>
            </table>

            <?php
            $sqldemo = "select iptuconstrdemo.*, db_usuarios.login from iptuconstrdemo inner join db_usuarios on j60_usuario = id_usuario where j60_matric = $parametro and j60_idcons = $idcons";
            $resultdemo = db_query($sqldemo);
            if (pg_num_rows($resultdemo) > 0) {
            ?>
              <br>
              <table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
                <tr style="font-weight:bold">
                  <td width="100%" nowrap bgcolor="#CCCCCC">Demolições parciais desta construção:</td>
                </tr>
              </table>
              <table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
              <tr style="font-weight:bold">
                <td width="20%" nowrap bgcolor="#CCCCCC">Sequencia</td>
                <td width="20%" nowrap bgcolor="#CCCCCC">Processo</td>
                <td width="15%" nowrap bgcolor="#CCCCCC">Area demolida</td>
                <td width="20%" nowrap bgcolor="#CCCCCC">Data da demolição</td>
                <td width="20%" nowrap bgcolor="#CCCCCC">Data do lançamento</td>
                <td width="15%" nowrap bgcolor="#CCCCCC">Hora</td>
                <td width="20%" nowrap bgcolor="#CCCCCC">Usuario</td>
              </tr>
            <?php
              for ($contadordemo=0;$contadordemo < pg_num_rows($resultdemo);$contadordemo++) {
                db_fieldsmemory($resultdemo,$contadordemo);
            ?>
                <tr>
                  <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000"> &nbsp;
                    <?=$j60_seq;?>
                    &nbsp;</font></td>
                  <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000"> &nbsp;
                    <?=substr($j60_codproc, 0, 20);?>
                    &nbsp; </font></td>
                  <td width="15%" nowrap bgcolor="#CCCCCC"><font color="#000000">
                    <?=$j60_area;?>
                    </font></td>
                  <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000">
                    <?=$j60_datademo;?>
                    </font></td>
                  <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000">
                    <?=$j60_data;?>
                    </font></td>
                  <td width="15%" nowrap bgcolor="#CCCCCC"><font color="#000000">
                    <?=$j60_hora;?>
                    </font></td>
                  <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000">
                    <?=$login;?>
                    </font></td>
                </tr>
            <?php
              }
            }
            ?>

            <?php
            $sCampos = "j131_sequencial,
                        login,
                        j131_data,
                        j131_hora,
                        ob09_data,
                        j131_obs,
                        case
                          when obrashabite.ob09_codhab is null
                            then j131_cadhab
                          else cast(ob09_habite as varchar)
                        end as j131_cadhab,
                        case
                          when protprocesso.p58_codproc is null
                            then j131_codprot
                          else cast(p58_codproc as varchar)
                        end as j131_codprot,
                        j131_dthabite";
            $rsHabite = $cliptuconstrhabite->sql_record($cliptuconstrhabite->sql_query_dados(null, $sCampos, null, "j131_matric = {$parametro} and j131_idcons = {$idcons}"));
            if ($cliptuconstrhabite->numrows > 0) {
            ?>
              <fieldset>
                <legend> <strong>Habite-se desta construção:</strong> </legend>
                <table width="100%">
                  <tr style="font-weight:bold">
                    <td width="20%" nowrap bgcolor="#CCCCCC">Sequencial</td>
                    <td width="20%" nowrap bgcolor="#CCCCCC">Protocolo</td>
                    <td width="15%" nowrap bgcolor="#CCCCCC">Habite-se</td>
                    <td width="20%" nowrap bgcolor="#CCCCCC">Data do Habite-se</td>
                    <td width="20%" nowrap bgcolor="#CCCCCC">Data do Lançamento</td>
                    <td width="15%" nowrap bgcolor="#CCCCCC">Hora</td>
                    <td width="20%" nowrap bgcolor="#CCCCCC">Usuario</td>
                  </tr>
            <?php
                  for ($iIndHabite=0;$iIndHabite < pg_num_rows($rsHabite);$iIndHabite++){
                    $oDadosHabite = db_utils::fieldsmemory($rsHabite,$iIndHabite, true);
            ?>
                    <tr>
                      <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000"> &nbsp;
                        <?=$oDadosHabite->j131_sequencial;?>
                        &nbsp;</font></td>
                      <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000"> &nbsp;
                        <?=substr($oDadosHabite->j131_codprot, 0, 20);?>
                        &nbsp; </font></td>
                      <td width="15%" nowrap bgcolor="#CCCCCC"><font color="#000000">
                        <?=$oDadosHabite->j131_cadhab;?>
                        </font></td>
                      <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000">
                        <?=$oDadosHabite->j131_dthabite;?>
                        </font></td>
                      <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000">
                        <?=$oDadosHabite->j131_data;?>
                        </font></td>
                      <td width="15%" nowrap bgcolor="#CCCCCC"><font color="#000000">
                        <?=$oDadosHabite->j131_hora;?>
                        </font></td>
                      <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000">
                        <?=$oDadosHabite->login;?>
                        </font></td>
                    </tr>
                    <tr>
                     <td colspan="7">
                       <fieldset>
                         <legend><strong>Observação</strong></legend>
                         <?=$oDadosHabite->j131_obs;?>
                       </fieldset>
                     </td>
                    </tr>
                    <tr>
                     <td colspan="7">&nbsp;</td>
                    </tr>
                <?php }?>
                  </table>
                  </fieldset>
               <?php }?>
              <br>
              <fieldset>
                <legend><strong>Caracteristicas da Construção: <?php echo $idcons; ?></strong></legend>
                <table>
                 <tr style="font-weight:bold">
                  <td align="center" nowrap bgcolor="#CCCCCC">C&oacute;digo</td>
                  <td width="50%" nowrap bgcolor="#CCCCCC">Descrição</td>
                  <td width="17%" nowrap bgcolor="#CCCCCC">Grupo</td>
                  <td width="25%" nowrap bgcolor="#CCCCCC">Pontos</td>
                 </tr>
            <?php
          }
            ?>
            <tr>
              <td width="8%" align="center" nowrap bgcolor="#666666">
                <font color="#FFFFFF"> &nbsp; <?=$j48_caract;?> &nbsp;</font>
              </td>
              <td nowrap bgcolor="#CCCCCC">
                <font color="#000000"> &nbsp; <?=substr($j31_descr, 0, 20);?> &nbsp; </font>
              </td>
              <td nowrap bgcolor="#CCCCCC">
                <font color="#000000"> <?=$j32_descr;?> </font>
              </td>
              <td nowrap bgcolor="#CCCCCC">
                <font color="#000000"> <?=$j31_pontos;?> </font>
              </td>
            </tr>
        <?php
      }
         ?>

<?php
  } else {
?>
<table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
 <tr>
    <td class="tabfonte" align="center"><strong>Sem Lan&ccedil;amento de Constru&ccedil;&otilde;es
      Levantadas </strong></td>
 </tr>
</table>
<?php
  }

  /////////////////////////////////////////////////////////////////////////////////////////////////////
  } else if ($solicitacao == "Construcoesdemolidas") {

   $sql = "select distinct
                  j39_matric as matric,
                  j39_idcons as idcons,
                  j39_codigo as cod,
                  j14_nome,
                  j39_area as area,
                  j39_ano as ano,
                  j39_pavim,
                  j39_numero as numero,
                  j39_compl as compl,
	                j48_caract,
	                j31_descr,
	                j31_grupo,
	                j32_descr,
	                j31_pontos,
		              case
		                when j39_idprinc
		                  then 'SIM'
		                else 'NAO'
		              end as j39_idprinc,
		              case
		                when j39_dtdemo is null
		                  then
		                    case
		                      when j60_matric is null
		                        then 'NAO'
		                      else 'PARCIALMENTE'
		                    end
		                else 'SIM'
		              end as demolida,
		              j39_dtdemo,
		              j39_areap,
		              case
		                when j39_idaument > 0
		                  then 'AMPLIAÇÃO DA CONSTRUÇÃO '||j39_idaument
		                else 'NOVA'
		              end as j39_idaument,
		              j39_dtlan,
		              j39_habite,
		              j39_obs,
                              j83_pontos
             from iptuconstr
		              left  join carconstr      on j39_matric        = j48_matric
		                                       and j39_idcons        = j48_idcons
		              left  join caracter       on j31_codigo        = j48_caract
		              left  join cargrup        on cargrup.j32_grupo = caracter.j31_grupo
		              inner join ruas           on ruas.j14_codigo   = iptuconstr.j39_codigo
		              left  join iptuconstrdemo on j60_matric        = j39_matric
		                                       and j60_idcons        = j39_idcons
                              left  join iptuconstrpontos on j83_matric      = j39_matric
		                                         and j83_idcons      = j39_idcons
	          where j39_matric = $parametro
	            and j39_dtdemo is not null
		   order by j39_idcons  ";
	$tituloJanela = "Construções Levantadas";
  $result = db_query($sql);
  $id_numero = 0;
?>
<table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
  <tr align="center">
    <td colspan="4"> <u>
      <?=$tituloJanela;?>
      </u> </td>
  </tr>
  <tr align="center">
    <td colspan="4">&nbsp; </td>
  </tr>
  <?php
  if( pg_num_rows($result) != 0 ) {
	 for ($contador=0;$contador < pg_num_rows($result);$contador ++ ){
	   db_fieldsmemory($result,$contador);
  	   if( $id_numero != $idcons ){
		  $impcar = 0;
		  $id_numero = $idcons;
?>
</table>

<table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
  <tr align="center">

    <td align="left" nowrap bgcolor="#CCCCCC">Constru&ccedil;&atilde;o
    </td>
    <td width="30%" align="left" nowrap bgcolor="#FFFFFF">
      <?=$idcons;?>
      &nbsp;
    </td>
    <td width="30%" align="left" nowrap bgcolor="#CCCCCC">Lançamento:
    </td>
    <td align="left" nowrap bgcolor="#FFFFFF">
      <?=db_formatar($j39_dtlan, 'd');?>
      &nbsp;
    </td>

  </tr>

    <td align="left" nowrap bgcolor="#CCCCCC" >Ano Constru&ccedil;&atilde;o:
    </td>
    <td width="40%" align="left" nowrap bgcolor="#FFFFFF" >
      <?=$ano;?>
      &nbsp;
    </td>
    <td width="30%" align="left" nowrap bgcolor="#CCCCCC">Principal:
    </td>
    <td align="left" nowrap bgcolor="#FFFFFF">
      <?=$j39_idprinc;?>
      &nbsp;
    </td>

  <tr align="center">
    <td   width="10%" align="left" nowrap bgcolor="#CCCCCC">&Aacute;rea:</td>
    <td align="left" nowrap bgcolor="#FFFFFF">
      <?=db_formatar($area, 'p');?>
      &nbsp; </td>
    <td width="17%" align="left" nowrap bgcolor="#CCCCCC">&Aacute;rea privada:</td>
    <td align="left" nowrap bgcolor="#FFFFFF">
      <?=db_formatar($j39_areap, 'p');?>
      &nbsp; </td>
  </tr>

  </tr>
  <tr align="center">
    <td   width="17%" align="left" nowrap bgcolor="#CCCCCC">Demolida:</td>
    <td align="left" nowrap bgcolor="#FFFFFF">
      <?=$demolida;?>
      &nbsp; </td>
    <td width="17%" align="left" nowrap bgcolor="#CCCCCC">Data demolicao:</td>
    <td align="left" nowrap bgcolor="#FFFFFF">
      <?=$j39_dtdemo;?>
      &nbsp; </td>
  </tr>

  </tr>
  <tr align="center">
    <td   width="17%" align="left" nowrap bgcolor="#CCCCCC">Origem:</td>
    <td align="left" nowrap bgcolor="#FFFFFF">
      <?=$j39_idaument;?>
      &nbsp; </td>
    </td>
    <td width="30%" align="left" nowrap bgcolor="#CCCCCC">Habite-se:
    </td>
    <td align="left" nowrap bgcolor="#FFFFFF">
      <?=db_formatar($j39_habite, 'd');?>
      &nbsp;
    </td>
  </tr>

</table>

<table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">

  <tr>
    <td   width="17%" align="left" nowrap bgcolor="#CCCCCC" >Frente: </td>
    <td colspan="3" align="left" nowrap bgcolor="#FFFFFF" >
      <?=$j14_nome;?><?=($numero > 0 ? "," : "");?>
      <?=$numero;?><?=($compl != "" ? "/" : "");?>
      <?=$compl;?>
      &nbsp; </td>
  </tr>
  <tr>
    <td   width="17%" align="left" nowrap bgcolor="#CCCCCC" >Pavimento: </td>
    <td align="left" nowrap bgcolor="#FFFFFF" >
      <?=$j39_pavim;?>
      &nbsp; </td>
      </td>
      <td width="30%" align="left" nowrap bgcolor="#CCCCCC"><?="$Sj83_pontos:"?></td>
      <td width="13%" align="left" nowrap bgcolor="#FFFFFF"><?=db_formatar($j83_pontos, 'p');?>&nbsp;</td>
  </tr>

  <tr>
    <td align="left"  bgcolor="#CCCCCC" >Observações: </td>
    <td colspan="3" align="left" bgcolor="#FFFFFF" >
      <?=$j39_obs;?>
      &nbsp; </td>
  </tr>

</table>

<?php

$sqldemo    = "select iptuconstrdemo.*, db_usuarios.login from iptuconstrdemo inner join db_usuarios on j60_usuario = id_usuario where j60_matric = $parametro and j60_idcons = $idcons";
$resultdemo = db_query($sqldemo);
if (pg_num_rows($resultdemo) > 0) {
?>

 <br>
  <table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
  <tr style="font-weight:bold">
  <td width="100%" nowrap bgcolor="#CCCCCC">Demolições parciais desta construção:</td>
  </tr>
  </table>

  <table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
  <tr style="font-weight:bold">
    <td width="20%" nowrap bgcolor="#CCCCCC">Sequencia</td>
    <td width="20%" nowrap bgcolor="#CCCCCC">Processo</td>
    <td width="15%" nowrap bgcolor="#CCCCCC">Area demolida</td>
    <td width="20%" nowrap bgcolor="#CCCCCC">Data da demolição</td>
    <td width="20%" nowrap bgcolor="#CCCCCC">Data do lançamento</td>
    <td width="15%" nowrap bgcolor="#CCCCCC">Hora</td>
    <td width="20%" nowrap bgcolor="#CCCCCC">Usuario</td>
  </tr>

<?php
  for ($contadordemo=0;$contadordemo < pg_num_rows($resultdemo);$contadordemo++){
    db_fieldsmemory($resultdemo,$contadordemo);
?>

  <tr>
    <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000"> &nbsp;
      <?=$j60_seq;?>
      &nbsp;</font></td>
    <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000"> &nbsp;
      <?=substr($j60_codproc, 0, 20);?>
      &nbsp; </font></td>
    <td width="15%" nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$j60_area;?>
      </font></td>
    <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$j60_datademo;?>
      </font></td>
    <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$j60_data;?>
      </font></td>
    <td width="15%" nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$j60_hora;?>
      </font></td>
    <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$login;?>
      </font></td>
  </tr>

<?php
  }
}

$sCampos = "j131_sequencial,
            login,
            j131_data,
            j131_hora,
            ob09_data,
            case
              when obrashabite.ob09_codhab is null
                then j131_cadhab
              else cast(ob09_habite as varchar)
            end as j131_cadhab,
            case
              when protprocesso.p58_codproc is null
                then j131_codprot
              else cast(p58_codproc as varchar)
            end as j131_codprot";
$rsHabite = $cliptuconstrhabite->sql_record($cliptuconstrhabite->sql_query_dados(null, $sCampos, null, "j131_matric = {$parametro} and j131_idcons = {$idcons}"));
if ( $cliptuconstrhabite->numrows > 0) {
?>
 <br>
  <table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
  <tr style="font-weight:bold">
  <td colspan=7 width="100%" nowrap bgcolor="#CCCCCC">Habite-se desta construção:</td>
    <tr style="font-weight:bold">
    <td width="20%" nowrap bgcolor="#CCCCCC">Sequencial</td>
    <td width="20%" nowrap bgcolor="#CCCCCC">Protocolo</td>
    <td width="15%" nowrap bgcolor="#CCCCCC">Habite-se</td>
    <td width="20%" nowrap bgcolor="#CCCCCC">Data do Habite-se</td>
    <td width="20%" nowrap bgcolor="#CCCCCC">Data do Lançamento</td>
    <td width="15%" nowrap bgcolor="#CCCCCC">Hora</td>
    <td width="20%" nowrap bgcolor="#CCCCCC">Usuario</td>
  </tr>

<?php
  for ($iIndHabite=0;$iIndHabite < $cliptuconstrhabite->numrows;$iIndHabite++){
   $oDadosHabite = db_utils::fieldsmemory($rsHabite,$iIndHabite);
?>
  <tr>
    <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000"> &nbsp;
      <?=$oDadosHabite->j131_sequencial;?>
      &nbsp;</font></td>
    <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000"> &nbsp;
      <?=substr($oDadosHabite->j131_codprot, 0, 20);?>
      &nbsp; </font></td>
    <td width="15%" nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$oDadosHabite->j131_cadhab;?>
      </font></td>
    <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$oDadosHabite->ob09_data;?>
      </font></td>
    <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$oDadosHabite->j131_data;?>
      </font></td>
    <td width="15%" nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$oDadosHabite->j131_hora;?>
      </font></td>
    <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$oDadosHabite->login;?>
      </font></td>
  </tr>

<?php
  }
  echo "</table>";
}
?>
<br>
<table width="95%" border="0" align="center"  cellpadding="0" cellspacing="1">
  <tr style="font-weight:bold">
    <td align="center" nowrap bgcolor="#CCCCCC">C&oacute;digo</td>
    <td width="50%" nowrap bgcolor="#CCCCCC">&nbsp;&nbsp;Caracter&iacute;sticas</td>
    <td width="17%" nowrap bgcolor="#CCCCCC">Grupo</td>
    <td width="25%" nowrap bgcolor="#CCCCCC">Pontos</td>
  </tr>

  <?php
	  }
  ?>
  <tr>
    <td   width="8%" align="center" nowrap bgcolor="#666666"><font color="#FFFFFF">
      &nbsp;
      <?=$j48_caract;?>
      &nbsp;</font></td>
    <td nowrap bgcolor="#CCCCCC"><font color="#000000"> &nbsp;
      <?=substr($j31_descr, 0, 20);?>
      &nbsp; </font></td>
    <td nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$j32_descr;?>
      </font></td>
    <td nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$j31_pontos;?>
      </font></td>
  </tr>

  <?php
  }
?>
  <td colspan="4" align="center" nowrap> <font color="#000000">&nbsp; </font></td>
</table>

<?php
  } else {
?>
<table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
 <tr>
    <td class="tabfonte" align="center"><strong>Sem Lan&ccedil;amento de Constru&ccedil;&otilde;es
      Levantadas </strong></td>
 </tr>
</table>
<?php
  }

  /////////////////////////////////////////////////////////////////////////////////////////////////////
  } else if ($solicitacao == "ConstrucoesEscrituradas") {
    $sql = "select j52_matric as matric,
                   j52_idcons as idcons,
                   j52_codigo as cod,j14_nome,
                   j52_area as area,
                   j52_ano as ano,
                   j52_numero as numero,
                   j52_compl as compl,
                   j53_caract,
                   j31_descr,
                   j31_grupo,
                   j31_pontos,
                   j83_pontos
              from constrescr
                   left join iptuconstrpontos
                          on j52_matric = j83_matric
                         and j52_idcons = j83_idcons,
                   constrcar, caracter, ruas
	           where j52_matric = $parametro
	             and j52_matric = j53_matric
	             and j52_idcons = j53_idcons
	             and j53_caract = j31_codigo
	             and j52_codigo = j14_codigo
		         order by j52_idcons";
	  $tituloJanela = "Construções Escrituradas";
    $result = db_query($sql);
    $id_numero = 0;
?>
<table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
  <tr align="center">
    <td colspan="4">
      <u><?=$tituloJanela;?></u>
    </td>
  </tr>
  <tr align="center">
    <td colspan="4">&nbsp; </td>
  </tr>
<?php
  if( pg_num_rows($result) != 0 ) {
	 for ($contador=0;$contador < pg_num_rows($result);$contador ++ ){
	   db_fieldsmemory($result,$contador);
  	   if( $id_numero != $idcons ){
		  $confere = 0;
		  $impcar = 0;
		  $id_numero = $idcons;
?>
  <tr align="center">
    <td align="left" nowrap bgcolor="#CCCCCC">Constru&ccedil;&atilde;o</td>
    <td   width="23%" align="left" nowrap bgcolor="#FFFFFF">
      <?=$idcons;?>
      &nbsp; </td>
    <td align="left" nowrap bgcolor="#CCCCCC" >Ano Constru&ccedil;&atilde;o:</td>
    <td   width="37%" align="left" nowrap bgcolor="#FFFFFF" >
      <?=$ano;?>
      &nbsp; </td>
  </tr>
  <tr align="center">
    <td   width="17%" align="left" nowrap bgcolor="#CCCCCC">&Aacute;rea:</td>
    <td align="left" nowrap bgcolor="#FFFFFF">
      <?=$area;?>
      &nbsp; </td>
    <td   width="23%" align="left" nowrap bgcolor="#CCCCCC" >Frente: </td>
    <td align="left" nowrap bgcolor="#FFFFFF" >
      <?=$j14_nome;?>
      <?=$numero;?>
      <?=$compl;?>
      &nbsp; </td>
  </tr>
  <tr align="center">
    <td   width="17%" align="left" nowrap bgcolor="#CCCCCC"><?="$Sj83_pontos:"?></td>
    <td align="left" nowrap bgcolor="#FFFFFF"><?=$j83_pontos;?>&nbsp; </td>
  </tr>
</table>

<?php
$sCampos = "j131_sequencial,
            login,
            j131_data,
            j131_hora,
            ob09_data,
            case
              when obrashabite.ob09_codhab is null
                then j131_cadhab
              else cast(ob09_habite as varchar)
            end as j131_cadhab,
            case
              when protprocesso.p58_codproc is null
                then j131_codprot
              else cast(p58_codproc as varchar)
            end as j131_codprot";
$rsHabite = $cliptuconstrhabite->sql_record($cliptuconstrhabite->sql_query_dados(null, $sCampos, null, "j131_matric = {$parametro} and j131_idcons = {$idcons}"));
if (@pg_num_rows($rsHabite) > 0) {
?>
<br>
  <table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
  <tr style="font-weight:bold">
  <td colspan=7 width="100%" nowrap bgcolor="#CCCCCC">Habite-se desta construção:</td>
    <tr style="font-weight:bold">
    <td width="20%" nowrap bgcolor="#CCCCCC">Sequencial</td>
    <td width="20%" nowrap bgcolor="#CCCCCC">Protocolo</td>
    <td width="15%" nowrap bgcolor="#CCCCCC">Habite-se</td>
    <td width="20%" nowrap bgcolor="#CCCCCC">Data do Habite-se</td>
    <td width="20%" nowrap bgcolor="#CCCCCC">Data do Lançamento</td>
    <td width="15%" nowrap bgcolor="#CCCCCC">Hora</td>
    <td width="20%" nowrap bgcolor="#CCCCCC">Usuario</td>
  </tr>
<?php
  for ($iIndHabite=0;$iIndHabite < pg_num_rows($rsHabite);$iIndHabite++){
   $oDadosHabite = db_utils::fieldsmemory($rsHabite,$iIndHabite);
?>
  <tr>
    <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000"> &nbsp;
      <?=$oDadosHabite->j131_sequencial;?>
      &nbsp;</font></td>
    <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000"> &nbsp;
      <?=substr($oDadosHabite->j131_codprot, 0, 20);?>
      &nbsp; </font></td>
    <td width="15%" nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$oDadosHabite->j131_cadhab;?>
      </font></td>
    <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$oDadosHabite->ob09_data;?>
      </font></td>
    <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$oDadosHabite->j131_data;?>
      </font></td>
    <td width="15%" nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$oDadosHabite->j131_hora;?>
      </font></td>
    <td width="20%" nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$oDadosHabite->login;?>
      </font></td>
  </tr>
<?php
  }
  echo "</table>";
}
?>
<br>
<table width="95%" border="0" align="center"  cellpadding="0" cellspacing="1">
  <tr>
    <td colspan="4" bgcolor="#CCCCCC"><strong>Caracter&iacute;sticas desta constru&ccedil;&atilde;o:</strong></td>
  </tr>
<table width="95%" border="0" align="center"  cellpadding="0" cellspacing="1">
  <?php
	  }

  if($impcar!=1){
     ?>
  <tr>
    <td align="center" nowrap bgcolor="#CCCCCC">C&oacute;digo</td>
    <td nowrap bgcolor="#CCCCCC">&nbsp;&nbsp;Caracter&iacute;sticas</td>
    <td nowrap bgcolor="#CCCCCC">Grupo</td>
    <td nowrap bgcolor="#CCCCCC">Pontos</td>
  </tr>
  <?php
  }

    if ( $confere == 0 ){
	  $confere = 1;
  ?>
  <tr>
    <td   width="3%" align="center" nowrap bgcolor="#666666"><font color="#FFFFFF">
      &nbsp;
      <?=$j53_caract;?>
      &nbsp;</font></td>
    <td nowrap bgcolor="#CCCCCC"><font color="#000000"> &nbsp;
      <?=substr($j31_descr, 0, 20);?>
      &nbsp; </font></td>
    <td nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$j31_grupo;?>
      </font></td>
    <td nowrap bgcolor="#CCCCCC"><font color="#000000">
      <?=$j31_pontos;?>
      </font></td>
  </tr>
  <?php
        if( $impcar == 0 ){
		  $impcar = 1;
        }
	 } else {
	   $confere = 0;
    }
  }
?>
  <td colspan="6" align="center" nowrap> <font color="#000000">&nbsp; </font></td>
</table>
<?php
  } else {
?>
<table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
 <tr>
    <td class="tabfonte" align="center"><strong>Sem Lan&ccedil;amento de Constru&ccedil;&otilde;es
      Escrituradas </strong></td>
 </tr>
</table>
<?php
  }

  /////////////////////////////////////////////////////////////////////////////////////////////////////
  } else if ($solicitacao == "Testada") {
?>
<table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
  <tr align="center">
    <td colspan="4" nowrap><u><strong>Testada</strong></u></td>
  </tr>
  <tr>
    <td colspan="4" nowrap>&nbsp;</td>
  </tr>
  <?php

     $sql = "select *,
                    case
                        when facevalor.j81_valorterreno is not null
                           then facevalor.j81_valorterreno
                        else
                           face.j37_valor
                    end as valorterreno
              from testada
             left join testadanumero
                on j15_idbql = j36_idbql
               and j15_face =  j36_face
              inner join orientacao   on j64_sequencial = j36_orientacao
              left outer join testpri on j49_idbql    = j36_idbql
                                     and j49_face     = j36_face
              inner join face         on j37_face     = j36_face
              inner join ruas         on j36_codigo   = j14_codigo
	      inner join ruastipo     on j14_tipo     = j88_codigo
	      left  join ruascep      on j14_codigo   = j29_codigo and
                                         j37_segmento = j29_inicio
              left  join facevalor    on j81_face     = j37_face
                                     and j81_anousu   = (select max(j81_anousu) from facevalor)
              where j36_idbql = $parametro order by j49_face
		   ";
      
    $result1 = db_query($sql);
    if( pg_num_rows($result1) != 0 ) {
      for ($contador1=0;$contador1 < pg_num_rows($result1);$contador1 ++ ){
	    db_fieldsmemory($result1,$contador1);
?>
  <tr>
    <td nowrap bgcolor="#CCCCCC">&nbsp;Logradouro:</td>
    <?php if(isset($j49_idbql)):
      ?>
      <td bgcolor="#FFFFFF" align="left" nowrap>&nbsp;&nbsp;<?php echo $j36_codigo . " - " . $j88_sigla . " " . str_pad($j14_nome, 20); ?></td>
      <?php endif; ?>
    <td nowrap bgcolor="#CCCCCC">Tipo: </td>
    <td bgcolor ="FFFFFF" nowrap>
      <?=(($j49_idbql == "") ? "Secundária" : "Principal");?>
    </td>
  </tr>
  <tr>
    <td nowrap bgcolor="#CCCCCC">&nbsp;N&uacute;mero:</td>
    <td bgcolor="#FFFFFF" align="left" nowrap>&nbsp;&nbsp;
      <?=$j15_numero?></td>
    <td nowrap bgcolor="#CCCCCC">Complemento: </td>
    <td bgcolor ="FFFFFF" nowrap>
      <?=$j15_compl?>
    </td>
  </tr>
  <tr>
    <td nowrap bgcolor="#CCCCCC">&nbsp;Código da Face:</td>
    <td bgcolor="#FFFFFF" width="40%" nowrap> &nbsp;
      <?=$j36_face;?>
    </td>
   <td nowrap bgcolor="#CCCCCC">CEP:</td>
    <td bgcolor='FFFFFF' nowrap>
      <?=$j29_cep;?>
    </td>
  <tr><td></td><td bgcolor="FFFFFF"></td>
    <td width="24%" nowrap bgcolor="#CCCCCC">Lado:</td>
    <td bgcolor="FFFFFF" width="17%" nowrap>
      <?=$j37_lado;?>
    </td>
  </tr>
  <tr>
    <td nowrap bgcolor="#CCCCCC">&nbsp;Nº Face:</td>
    <td bgcolor="FFFFFF" width="40%" nowrap> &nbsp;
      <?=$j37_sequencia;?>
    </td>
    <td width="24%" nowrap bgcolor="#CCCCCC">Segmento:&nbsp;</td>
    <td bgcolor="FFFFFF"  width="17%" nowrap>
      <?=$j37_segmento;?>
  </tr>
  <tr>
    <td   width="19%" nowrap bgcolor="#CCCCCC">Testada:&nbsp;</td>
    <td bgcolor="FFFFFF" nowrap>&nbsp;
      <?=db_formatar($j36_testad, 'f');?>
      &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;</td>
    <td nowrap bgcolor="#CCCCCC">Testada Levantada:</td>
    <td bgcolor="FFFFFF" nowrap>
      <?=db_formatar($j36_testle, 'f');?>
    </td>
  </tr>
  <tr>
    <td nowrap bgcolor="#CCCCCC">Valor m2 Terreno</td>
    <td bgcolor="FFFFFF" nowrap> &nbsp;
      <?=db_formatar($valorterreno, 'f');?>
      &nbsp;</td>
    <td nowrap bgcolor="#CCCCCC">Valor M2 Constru&ccedil;&atilde;o:</td>
    <td bgcolor="FFFFFF" nowrap>
      <?=db_formatar($j37_vlcons, 'f', ' ', 15, 'e', 2);?>
      &nbsp;</td>
  </tr>
  <tr>
    <td nowrap bgcolor="#CCCCCC">Zona:</td>
    <td bgcolor="FFFFFF" nowrap> &nbsp;
      <?=substr($j37_outros, 0, 20);?>
      &nbsp;</td>
    <td nowrap bgcolor="#CCCCCC">Orientacação:</td>
    <td nowrap> <?=$j64_descricao;?>
      &nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" nowrap> <table width="99%" border="0" align="center"  cellpadding="0" cellspacing="1">
        <tr>
          <td colspan="5" align="center" nowrap bgcolor="#CCCCCC">Caracter&iacute;sticas:</td>
        </tr>
        <tr>
          <td align="center" nowrap bgcolor="#CCCCCC">Cód.</td>
          <td nowrap bgcolor="#CCCCCC">&nbsp;&nbsp;Característica</td>
          <td nowrap bgcolor="#CCCCCC">Cód.</td>
          <td nowrap bgcolor="#CCCCCC">Grupo</td>
          <td align="center" nowrap bgcolor="#CCCCCC">Pontos</td>
        </tr>
        <?php
        $sql = "select *
		        from carface
				     inner join caracter on j31_codigo = j38_caract
				     inner join cargrup on j32_grupo = j31_grupo
	            where j38_face  = $j37_face
				";
       $result = db_query($sql);
       if( pg_num_rows($result) != 0 ) {
         for ($contador=0;$contador < pg_num_rows($result);$contador ++ ){
	       db_fieldsmemory($result,$contador);
		?>
        <tr>
          <td   width="3%" align="center" nowrap bgcolor="#666666"><font color="#FFFFFF">
            &nbsp;
            <?=$j38_caract;?>
            &nbsp;</font></td>
          <td nowrap bgcolor="#CCCCCC"><font color="#000000"> &nbsp;
            <?=substr($j31_descr, 0, 20);?>
            &nbsp; </font></td>
          <td width="3%" align="center" nowrap bgcolor="#666666"><font color="#FFFFFF">
	    &nbsp;
            <?=$j31_grupo;?>
            &nbsp;</font></td>
          <td nowrap bgcolor="#CCCCCC"><font color="#000000"> &nbsp;
            <?=substr($j32_descr, 0, 20);?>
            </font></td>
          <td align="center" nowrap bgcolor="#CCCCCC"><font color="#000000">
            <?=$j31_pontos;?>
            </font></td>
        </tr>
        <?php
		   }
		  }
        ?>
        <td colspan="6" align="center" nowrap> <font color="#000000">&nbsp; </font></td>
      </table></td>
  </tr>
  <?php
      }
   } else {
  ?>
  <tr>
    <td  colspan="8" align="center"><strong>Sem Registro de Testadas</strong></td>
  </tr>
  <?php
  }
?>
</table>
<?php
  } else if ($solicitacao == "TestadasInternas") {
?>
<table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
  <tr>
    <td colspan="6" align="center" nowrap><u>Testadas Internas</u></td>
  </tr>
  <tr>
    <td colspan="6" nowrap>&nbsp;</td>
  </tr>
  <?php
    $sql = "select tesinter.*,
                   lote.*,
                   orientacao.*,
                   tesinterlote.*,
                   case
                     when interno.j34_lote is null
                       then tesintertipo.j92_descr
                     else interno.j34_lote
                   end as loteinterno
              from tesinter
                   inner join lote            on j39_idbql         = j34_idbql
                   inner join orientacao      on j39_orientacao    = j64_sequencial
									 left  join tesinterlote    on j39_sequencial    = j69_tesinter
                   left  join tesinteroutros  on j84_tesinter      = j39_sequencial
                   left  join tesintertipo    on j92_sequencial    = j84_tesintertipo
                   left  join lote as interno on interno.j34_idbql = j69_idbql
             where j39_idbql = $parametro ";
    $result = db_query($sql);
    if( pg_num_rows($result) != 0 ) {
      for ($contador=0;$contador < pg_num_rows($result);$contador ++ ){
	    db_fieldsmemory($result,$contador); ?>

        <tr>
          <td width="12%" nowrap bgcolor="#CCCCCC">&nbsp;Metragem:&nbsp;</td>
          <td width="24%" nowrap bgcolor="#FFFFFF">
            <?=($j39_testad>0?$j39_testad:$j39_testle)?>
          </td>
          <td nowrap bgcolor="#CCCCCC">
            &nbsp;Confronta&ccedil;&atilde;o:&nbsp;
          </td>
          <td colspan="3" nowrap bgcolor="#FFFFFF">&nbsp;
            <?=($j69_idbql!=""?"$j64_descricao / Lote : $loteinterno ":" $j64_descricao / $loteinterno ")?>
            &nbsp;
          </td>
        </tr> <?php
      }

    } else {
      ?>
       <tr align="center">
         <td  colspan="6"><strong>Sem Registro de Testadas Internas</strong></td>
       </tr>
<?php }?>
</table>
<?php

  } else if ($solicitacao == "EnderecoDeEntrega") {
  $sql = "select *
            from iptuender
		       where j43_matric = $parametro";
  $result = db_query($sql);
?>
<table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
  <tr align="center">
    <td colspan="4" nowrap><u>Endere&ccedil;o de entrega</u></td>
  </tr>
  <tr>
    <td colspan="4" nowrap>&nbsp; </td>
  </tr>
<?php

  if( pg_num_rows($result) != 0 ) {
?>
  <tr align="center" bgcolor="#CCCCCC">
    <td nowrap>Endere&ccedil;o</td>
    <td nowrap>Cx.Postal</td>
    <td nowrap>Bairro</td>
    <td nowrap>Municipio</td>
    <td nowrap>CEP</td>
    <td nowrap>UF</td>
  </tr>
<?php

    for ($contador=0;$contador < pg_num_rows($result);$contador ++ ){
	  db_fieldsmemory($result,$contador);
?>
  <tr align="center">
    <td nowrap bgcolor="#FFFFFF"><?=trim($j43_ender) . "," . $j43_numimo . ($j43_comple != "" ? "/" : "") . $j43_comple;?>&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><?=$j43_cxpost;?>&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><?=$j43_bairro;?>&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><?=$j43_munic;?>&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><?=$j43_cep;?>&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><?=$j43_uf;?>&nbsp;</td>
  </tr>
  <?php

	}
  } else {
?>
  <tr>
    <td  colspan="4" align="center" nowrap  ><strong>Sem endereço de entrega&nbsp;</strong></td>
  </tr>
  <?php

  }
?>
</table>
<?php

   } else if ($solicitacao == "OutrosProprietarios") {
?>
<div class="container">
<fieldset>
        <legend>Outros Propriet&aacute;rios</legend>
<table class="form-container" border="1" style="width: 850px">
  <?php
    $sql = "SELECT z01_numcgm,
                    z01_nome,
                    z01_ender,
                    j163_descricao
            FROM
                propri
                INNER JOIN cgm ON cgm.z01_numcgm = propri.j42_numcgm
                INNER JOIN tipoproprietario ON tipoproprietario.j163_tipoproprietario = propri.j42_tipoproprietario
            WHERE
                j42_matric = {$parametro}
                AND j42_numcgm = z01_numcgm";

    $result = db_query($sql);
      if( pg_num_rows($result) != 0 ) {
 ?>
   <thead>
        <tr style="background-color: #e1e1e1">
            <th class="text-left field-size8">&nbsp;Nome</th>
            <th class="text-left field-size4">&nbsp;Tipo do proprietário</th>
            <th class="text-left">&nbsp;Endereço</th>
            <th class="text-left"></th>
        </tr>
    </thead>
        <tbody>
  <?php

     for ($contador=0;$contador < pg_num_rows($result);$contador ++ ){
	   db_fieldsmemory($result,$contador);

?>
    <tr class="cores" onclick="js_JanelaAutomatica('cgm','<?=$z01_numcgm;?>')" >
        <td class="text-left">&nbsp;<?=$z01_nome;?></td>
        <td class="text-left">&nbsp;<?=$j163_descricao;?></td>
        <td class="text-left">&nbsp;<?=$z01_ender;?></td>
        <td bgcolor="#CCCCCC" title="Clique aqui para outros dados"><a href="" onclick='return false;' >Outros</a></td>
    </tr>
  <?php
    }
  } else {
?>
  <tr>
    <td  colspan="3" align="center"><strong>Sem Outros Proprietários</strong></td>
  </tr>
<?php
  }
?>
          </tbody>
      </table>
  </fieldset>
</div>
<?php
  } else if ($solicitacao == "OutrosPromitentes") {
?>
<div class="container">
    <fieldset>
        <legend>Responsáveis</legend>
<table class="form-container" border="1" style="width: 850px">
  <?php
 // Recebe $cod_matricula como parametro
 $sql = "SELECT z01_numcgm,
                   z01_nome,
                   z01_ender,
                   j41_promitipo,
                   j41_tipopro,
                   j164_descricao
              FROM promitente
             INNER JOIN cgm
                ON z01_numcgm = j41_numcgm
             INNER JOIN tipopromitente
                ON j164_promitipo = j41_promitipo
             WHERE j41_matric = {$parametro}
             ORDER BY j41_tipopro DESC";

 $result = db_query($sql);
   if( pg_num_rows($result) != 0 ) {
 ?>
   <thead>
        <tr style="background-color: #e1e1e1">
            <th class="text-left field-size8">&nbsp;Nome</th>
            <th class="text-left field-size4">&nbsp;Tipo do promitente</th>
            <th class="text-left">&nbsp;Endereço</th>
            <th class="text-left"> Principal</th>
            <th class="text-left"></th>
        </tr>
        </thead>
        <tbody>
        <?php
for ($contador = 0; $contador < pg_num_rows($result); $contador++) {
			db_fieldsmemory($result, $contador);
			?>
            <tr class="cores" onclick="js_JanelaAutomatica('cgm','<?=$z01_numcgm;?>')" >
                <td class="text-left">&nbsp;<?=$z01_nome;?></td>
                <td class="text-left">&nbsp;<?=$j164_descricao;?></td>
                <td class="text-left">&nbsp;<?=$z01_ender;?></td>
                <td class="text-left"> <?=$j41_tipopro == 't' ? 'SIM' : 'NÃO';?></td>
                <td class="text-center" title="Clique aqui para outros dados"><a href="" onclick='return false;' >Outros</a></td>
            </tr>
            <?php
}
} else {
  ?>
      <tr>
          <td  colspan="3" class="text-center"><strong>Sem Responsáveis</strong></td>
      </tr>
      <?php
}
?>
  </tbody>
</table>
  </fieldset>
</div>
<?php
  } else if ($solicitacao == "Averbacao") {
?>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
  <tr align="center">
    <td colspan="4"><u>Averba&ccedil;&atilde;o</u></td>
  </tr>
  <tr align="center">
    <td colspan="4">&nbsp;</td>
  </tr>
<?php

   $sql = "select *
           from averbacao
					      inner join averbatipo on j93_codigo = j75_tipo
                inner join iptubase   on j01_matric = j75_matric
                inner join cgm        on z01_numcgm = j01_numcgm
	       where  j75_matric = $parametro ";
   $result = db_query($sql);
     if( pg_num_rows($result) != 0 ) {
?>
  <tr align="center">
    <td bgcolor="#CCCCCC">Matr&iacute;cula</td>
    <td bgcolor="#CCCCCC">Código</td>
    <td bgcolor="#CCCCCC">Data Averbação</td>
    <td bgcolor="#CCCCCC">Tipo</td>
    <td bgcolor="#CCCCCC">Data Tipo</td>
    <td bgcolor="#CCCCCC">Obs</td>
    <td bgcolor="#CCCCCC">Situação</td>
  </tr>
<?php
       for ($contador=0;$contador < pg_num_rows($result);$contador ++ ){
	     db_fieldsmemory($result,$contador);
?>
  <tr align="center">
    <td bgcolor="#FFFFFF">
      <?=$j75_matric;?>
      &nbsp;</td>
      <td bgcolor="#FFFFFF">
      <a href="#" onclick="js_averba(<?=$j75_codigo;?>);"><?=$j75_codigo;?></a>
      &nbsp;</td>
    <td bgcolor="#FFFFFF">
      <?=db_formatar($j75_data, 'd');?>
      &nbsp;</td>
    <td bgcolor="#FFFFFF">
      <?php

			$tipo = $j93_descr;

      ?>
      <?=$tipo;?>
      &nbsp;</td>
    <td bgcolor="#FFFFFF">
      <?=db_formatar($j75_dttipo, 'd');?>
      &nbsp;</td>
     <td bgcolor="#FFFFFF">
      <?=$j75_obs;?>
      &nbsp;</td>
     <td bgcolor="#FFFFFF">
     <?php

     if ($j75_situacao==1){
      	$situacao = "Não Processado";
      }else if ($j75_situacao==2){
      	$situacao = "Processado";
      }
      ?>
      <?=@$situacao;?>
      &nbsp;</td>
 </tr>
  <?php

      }
    }else{

?>
  <tr>
    <td  colspan="4" align="center"><strong>Sem lançamentos de Averbação</strong></td>
  </tr>
<?php

  }
?>
</table>
<?php

  } else if ($solicitacao == "Calculo") {
    $resano = db_query("select distinct j23_anousu from iptucalc where j23_matric = $parametro order by j23_anousu desc");
if(pg_num_rows ($resano) > 0){
  for($xx=0;$xx<pg_num_rows($resano);$xx++){
    db_fieldsmemory($resano,$xx);
?>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
  <tr align="center">
    <td colspan="7"><u>C&aacute;lculo por matr&iacute;cula</u></td>
  </tr>
  <?php

  $sql = "select distinct iptucalc.j23_anousu,         iptucalc.j23_matric ,
                            iptucalc.j23_testad ,
                            iptucalc.j23_arealo ,
                            iptucalc.j23_areafr ,
                            iptucalc.j23_areaed ,
                            iptucalc.j23_m2terr ,
                            iptucalc.j23_vlrter ,
                            iptucalc.j23_aliq   ,
                            iptucalc.j23_vlrisen
                   ,sum(j22_valor) as j22_valor
          from iptucalc
		       left outer join iptucale on j22_matric = j23_matric and j22_anousu = j23_anousu
		  where j23_matric = $parametro and j23_anousu = $j23_anousu
		  group by iptucalc.j23_anousu,
                            iptucalc.j23_matric ,
                            iptucalc.j23_testad ,
                            iptucalc.j23_arealo ,
                            iptucalc.j23_areafr ,
                            iptucalc.j23_areaed ,
                            iptucalc.j23_m2terr ,
                            iptucalc.j23_vlrter ,
                            iptucalc.j23_aliq   ,
                            iptucalc.j23_vlrisen
order by iptucalc.j23_anousu desc
		 ";
      $result = db_query($sql);
   if( pg_num_rows($result) != 0 ) {

                 $matricula = \db_utils::fieldsmemory($result, 0)->j23_matric;
		 $sql2 = "select k02_codigo,                                                             ";
                 $sql2 .= "       k02_descr,                                                             ";
		 $sql2 .= "       j20_numpre,                                                             ";
		 $sql2 .= "       j17_codhis,                                                            ";
		 $sql2 .= "       j17_descr,                                                             ";
		 $sql2 .= "       j21_valor,                                                             ";
		 $sql2 .= "       0 as j152_areaed,                                                             ";
                 $sql2 .= "       0 as j152_quant,                                                             ";
		 $sql2 .= "       case                                                                   ";
		 $sql2 .= "         when iptucalhconf.j89_codhis is not null then                        ";
		 $sql2 .= "           (select sum(x.j21_valor)                                           ";
		 $sql2 .= "					    from iptucalv x                                                  ";
		 $sql2 .= "						 where x.j21_anousu = iptucalv.j21_anousu                          ";
		 $sql2 .= "						   and x.j21_matric = iptucalv.j21_matric                          ";
		 $sql2 .= "							 and x.j21_receit = iptucalv.j21_receit                          ";
		 $sql2 .= "							 and x.j21_codhis = iptucalhconf.j89_codhis)                     ";
		 $sql2 .= "         else 0                                                               ";
     $sql2 .= "       end as j21_valorisen, 	
                      CASE
                        WHEN j21_gerafinanc THEN 'Sim'
                        ELSE 'Não'
                      end as gerafinanc                                                      ";
		 $sql2 .= "  from iptucalv                                                               ";
                 $sql2 .= "       inner join iptucalh        on iptucalh.j17_codhis        = j21_codhis  ";
                 $sql2 .= "       left join iptunump        on iptunump.j20_anousu        = $j23_anousu ";
		 $sql2 .= "                                 and iptunump.j20_matric        = j21_matric  ";
		 $sql2 .= "       left  join iptucalhconf    on iptucalhconf.j89_codhispai = j21_codhis  ";
	 	 $sql2 .= "       inner join tabrec          on tabrec.k02_codigo          = j21_receit  ";
 		 $sql2 .= "       left  join iptucadtaxaexe  on iptucadtaxaexe.j08_tabrec  = j21_receit  ";
 		 $sql2 .= "                                 and iptucadtaxaexe.j08_anousu  = $j23_anousu ";
 		 $sql2 .= " where j21_matric = {$matricula}";
		 $sql2 .= "   and j21_anousu = $j23_anousu                                               ";
		 $sql2 .= "   and j17_codhis not in (select j89_codhis from iptucalhconf)                ";
                 $sql2 .= " UNION                                                             ";

     $sql2 .= "SELECT k02_codigo,
                      k02_descr,
                      j151_numpre AS j20_numpre,
                      j17_codhis,
                      j17_descr,
                      j152_valor AS j21_valor,
                      j152_areaed,
                      j152_quant,
                      CASE
                          WHEN iptucalhconf.j89_codhis IS NOT NULL THEN
                               (SELECT sum(x.j152_valor)
                                  FROM iptutaxacalv x
                                 WHERE x.j152_iptutaxanump = iptutaxacalv.j152_iptutaxanump
                                   AND x.j152_codhis = iptucalhconf.j89_codhis)
                          ELSE 0
                      END AS j21_valorisen,
                      'Não' as gerafinanc
                 FROM iptutaxanump
                      INNER JOIN iptutaxacalv ON iptutaxacalv.j152_iptutaxanump = iptutaxanump.j151_codigo
                      INNER JOIN iptucalh ON iptucalh.j17_codhis = iptutaxacalv.j152_codhis
                      INNER JOIN iptucadtaxaexe ON iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                      INNER JOIN tabrec ON tabrec.k02_codigo = iptutaxacalv.j152_receit
                      LEFT JOIN iptucalhconf ON iptucalhconf.j89_codhispai = iptutaxacalv.j152_codhis
                WHERE j151_matric = {$matricula}
                  AND iptucadtaxaexe.j08_anousu = {$j23_anousu} 
                  AND j17_codhis NOT IN (SELECT j89_codhis
                                           FROM iptucalhconf)
                  AND j151_numpre IS NOT NULL ";

     $sql2 .= " order by j17_codhis         ";

     $result2 = db_query($sql2);

     for ($contador=0;$contador < pg_num_rows($result);$contador ++ ){
	     db_fieldsmemory($result,$contador);
?>
  <tr align="center">
    <td colspan="7" bgcolor="#333333"><font color="#FFFFFF">Ano:
      <?=$j23_anousu;?>
      </font></td>
  </tr>
  <tr align="left">
    <td colspan="7" nowrap>
		<table width="100%" border="0" cellspacing="2" cellpadding="0" >
        <tr>
          <td width="21%" align="left" nowrap bgcolor="#CCCCCC"><div align="right">&nbsp;Testada
              do C&aacute;lculo</div></td>
          <td width="23%" align="right">
            <?=$j23_testad;?>
          </td>
          <td width="25%" align="left" nowrap bgcolor="#CCCCCC"><div align="right">Area
              do lote para calculo</div></td>
          <td width="31%" align="right">
            <?=db_formatar($j23_arealo, 'f');?>
          </td>
        </tr>
        <tr>
          <td align="left" nowrap bgcolor="#CCCCCC"><div align="right">&nbsp;Fra&ccedil;&atilde;o
              de C&aacute;lculo</div></td>
          <td align="right">
            <?=$j23_areafr;?>
          </td>
          <td align="left" nowrap bgcolor="#CCCCCC"><div align="right">Area edificada</div></td>
          <td align="right">
            <?=number_format($j23_areaed, 2, ',', '.');?>
          </td>
        </tr>
        <tr>
          <td align="left" nowrap bgcolor="#CCCCCC"><div align="right">Valor m2
              Terreno</div></td>
          <td align="right">
            <?=db_formatar($j23_m2terr, 'f');?>
          </td>
          <td align="left" nowrap bgcolor="#CCCCCC"><div align="right">Valor Venal
              Terreno.</div></td>
          <td align="right">
            <?=db_formatar($j23_vlrter, 'f');?>
          </td>
        </tr>
        <tr>
          <td align="left" nowrap bgcolor="#CCCCCC"><div align="right">&nbsp;Aliquota</div></td>
          <td align="right">
            <?=db_formatar($j23_aliq, 'f');?>
          </td>
          <td align="left" nowrap bgcolor="#CCCCCC"><div align="right">Valor Venal
              Edific.</div></td>
          <td align="right">
            <?=db_formatar($j22_valor, 'f');?>
          </td>
        </tr>
        <tr>
          <td align="right" nowrap bgcolor="#CCCCCC"><div align="right">Valor
              da Isenção</div></td>
          <td align="right">
            <?=db_formatar($j23_vlrisen, 'f');?>
          </td>
          <td align="left" nowrap bgcolor="#CCCCCC"><div align="right">Valor Venal:</div></td>
          <td align="right">
            <?=db_formatar(($j23_vlrter + $j22_valor), 'f');?>
          </td>
        </tr>
      </table></td>
  </tr>

  <tr align="left">
    <td colspan="7" nowrap>
		<table width="100%" border="0" cellspacing="2" cellpadding="0" class='tab_cinza'>
        <tr>
          <th width="5%"  nowrap bgcolor="#CCCCCC"> Lan&ccedil;amento</th>
          <th width="9%"  nowrap bgcolor="#CCCCCC"> Receita</th>
          <th width="20%" nowrap bgcolor="#CCCCCC"> Descri&ccedil;&atilde;o</th>
          <th width="5%"  nowrap bgcolor="#CCCCCC"> Histórico</th>
          <th width="25%" nowrap bgcolor="#CCCCCC"> Descri&ccedil;&atilde;o</th>
          <th width="12%" nowrap bgcolor="#CCCCCC"> Valor Calculado</th>
          <th width="12%" nowrap bgcolor="#CCCCCC"> Valor Isen&ccedil;&atilde;o</th>
          <th width="12%" nowrap bgcolor="#CCCCCC"> Saldo a pagar</th>
        </tr>
        <?php

      	$soma     = 0;
    		$somacalc = 0;
    		$somaisen = 0;

        for ($contador2=0;$contador2 < pg_num_rows($result2);$contador2 ++ ){

    	    db_fieldsmemory($result2,$contador2);

          $soma     = $soma     + ($j21_valor-abs($j21_valorisen));
          $somacalc = $somacalc + $j21_valor;
          $somaisen = $somaisen + $j21_valorisen;
    		?>
        <tr>
          <td width="9%" nowrap align="center">
            <?=$j20_numpre;?>
          </td>
          <td width="9%" nowrap align="center">
            <?=$k02_codigo;?>
          </td>
          <td width="21%" nowrap bgcolor="#FFFFFF">
            <?=substr($k02_descr, 0, 20);?>
          </td>
          <td width="5%" nowrap align="center">
            <?=$j17_codhis;?>
          </td>
          <td width="30%" nowrap bgcolor="#FFFFFF">
              <?=substr($j17_descr, 0, 30);?>
          </td>
          <td width="6%"  align="right" nowrap bgcolor="#FFFFFF">
            <?=db_formatar($j21_valor, 'f');?>
          </td>
          <td width="6%"  align="right" nowrap bgcolor="#FFFFFF">
            <?=db_formatar($j21_valorisen, 'f');?>
          </td>
          <td width="16%" align="right" nowrap bgcolor="#FFFFFF">
            <?=db_formatar(($j21_valor - abs($j21_valorisen)), 'f');?>
          </td>
        </tr>
        <?php

    }
    $habilitaGeraFinac = false;
    $oDaocfiptu = new cl_cfiptu;
    $sqlcfiptu = $oDaocfiptu->sql_query_file(db_getsession("DB_anousu"),'j18_taxaseparada, j18_simulacaocalculo');
    $rs = pg_fetch_object(db_query($sqlcfiptu));

    if($rs->j18_taxaseparada == 0 && $rs->j18_simulacaocalculo == 't'){
      $habilitaGeraFinac = true;
    }

?>
  <tr align="left">
    <th align="right" nowrap bgcolor="#CCCCCC">
      <?= $habilitaGeraFinac ? 'Calcular Financeiro' : ''; ?>
		</th>
    <th  align="center" nowrap bgcolor="#CCCCCC">
      <strong><?= $habilitaGeraFinac ? $gerafinanc : ''; ?></strong>
		</th>
    <th colspan="3" align="right" nowrap bgcolor="#CCCCCC">TOTAL :
		</th>
    <td width="12%" align="right" nowrap bgcolor="#CCCCCC">
      <strong><?=db_formatar($somacalc, 'f');?></strong>
		</td>
    <td width="12%" align="right" nowrap bgcolor="#CCCCCC">
      <strong><?=db_formatar($somaisen, 'f');?></strong>
		</td>
    <td width="12%" align="right" nowrap bgcolor="#CCCCCC">
      <strong><?=db_formatar($soma, 'f');?></strong>
		</td>
  </tr>

   </table>
	 </td>
  </tr>
  <tr>
    <td colspan="8" align="right" nowrap bgcolor="#CCCCCC" class="linkHistorico">
      <a href='javascript:void(0);' onclick='js_mostra_histcalculo(<?php echo $j23_anousu; ?>, <?php echo $parametro; ?>);return false;'>Histórico de cálculo do ano <?php echo $j23_anousu; ?></a>
    </td>
  </tr>

  <tr align="left">
    <td colspan="7">&nbsp;</td>
  </tr>
  <?php
     //Verifica se o plugin de calculo proporcional esta ativo.
     $sqlValidaPluginAtivo = "select * from db_plugin where db145_nome = 'calculo-de-iptu-proporcional'";
     $resultValidaPluginAtivo = db_query($sqlValidaPluginAtivo);

     $sqlIptucale = "select * from iptucale
		              inner join iptuconstr on j22_matric = j39_matric and  j22_idcons = j39_idcons
		              where j22_matric = $j23_matric and j22_anousu = $j23_anousu";

	 $resultSqlIptucale = db_query($sqlIptucale);
     if( pg_num_rows($resultSqlIptucale) != 0 ) {
?>
  <tr align="center">
    <td>&nbsp; </td>
    <td colspan="6" align="center" bgcolor="#CCCCCC">C&aacute;lculos para cada
      edifica&ccedil;&atilde;o&nbsp; &nbsp; &nbsp;</td>
  </tr>
  <tr border="1" align="center">
    <td nowrap>&nbsp;</td>
    <td colspan="6" align="center" nowrap bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="0">
        <tr>
          <td width="4%">N&deg;</td>
          <td width="16%" align="center">&Aacute;rea</td>
          <td width="12%" align="center">Exerc&iacute;cio</td>
          <td width="19%" align="center">Valor m&sup2;</td>
          <td width="15%" align="center">Pontua&ccedil;&atilde;o</td>
          <td width="34%" align="right">Valor Venal</td>
        </tr>
        <?php
	    for ($i=0;$i<pg_num_rows($resultSqlIptucale);$i++) {
            db_fieldsmemory($resultSqlIptucale,$i);
            $sqlIptuconstrareahistorico = "";

            $areaExibida = $j22_areaed;

            /*
             * Este if, serve para quando o plugin
             * iptu calculo proporcional estiver instalado
             * levar em consideração a area filtrado por vigencias
             * respectiva salva na tabela de historico que é adicionada pelo mesmo.
             */

            if(pg_num_rows($resultValidaPluginAtivo) != 0)
            {
                $sqlIptuconstrareahistorico =
                    "select sum(area) as area from plugins.iptuconstrareahistorico
                          where plugins.iptuconstrareahistorico.matricula = $j23_matric
                            and plugins.iptuconstrareahistorico.datainicio <= ($j23_anousu||'-'||'01'||'-01')::date
                            and ($j23_anousu||'-'||'01'||'-01')::date <= plugins.iptuconstrareahistorico.data
                            and id_constr = $j39_idcons";

                $resultIptuconstrareahistorico = db_query($sqlIptuconstrareahistorico);
                $area = pg_fetch_object($resultIptuconstrareahistorico)->area;
                $areaExibida = $area ? $area : $areaExibida;
            }


      ?>
        <tr>
          <td>
            <?=$j22_idcons;?>
          </td>
          <td align="center">
            <?=db_formatar($areaExibida, 'f');?>
          </td>
          <td align="center">
            <?=$j23_anousu;?>
          </td>
          <td align="center">
            <?=db_formatar($j22_vm2, 'f');?>
          </td>
          <td align="center">
            <?=$j22_pontos;?>
          </td>
          <td align="right">
            <?=db_formatar($j22_valor, 'f');?>
          </td>
        </tr>
        <?php

	  }
?>
      </table></td>
  </tr>
  <?php

	  }

    $sqlcalclog = " select * from iptucalclog
                    inner join db_usuarios on iptucalclog.j27_usuario = db_usuarios.id_usuario
                    inner join iptucalclogmat on iptucalclogmat.j28_codigo = iptucalclog.j27_codigo
                    inner join iptucadlogcalc on iptucadlogcalc.j62_codigo = iptucalclogmat.j28_tipologcalc
                    where j27_anousu = $j23_anousu and j28_matric = $parametro
                    order by j27_codigo desc
                  ";
    $resultcalclog = db_query($sqlcalclog) or die($sqlcalclog);

    echo "</table> <table> <tr>";

    for ($calclog=0; $calclog < pg_num_rows($resultcalclog); $calclog++) {
      db_fieldsmemory($resultcalclog, $calclog);

      echo "<tr>";
      echo "<td>Código: $j27_codigo</td>";
      echo "<td>Tipo: " . ($j27_parcial == "t"?"PARCIAL":"GERAL") . "</td>";
      echo "</tr>";

      echo "<tr>";
      echo "<td>Data: " . db_formatar($j27_data,'d') . "</td>";
      echo "<td>Hora: $j27_hora </td>";
      echo "</tr>";

      echo "<tr>";
      echo "<td>Resultado: $j28_tipologcalc - $j62_descr </td>";
      echo "<td>Tipo: " . ($j62_erro == "t"?"ERRO":"NORMAL") . " </td>";
      echo "</tr>";

      if ($j27_parcial == "t" && !empty($j27_observacao)) {
        echo "<tr>";
        echo "<td>Observação: $j27_observacao </td>";
        echo "<td>&nbsp;</td>";
        echo "</tr>";
      }

      echo "<tr>";
      echo "<td>Usuário: $login - $nome </td>";
      echo "<td>&nbsp;</td>";
      echo "</tr>";

      echo "<tr>";
      echo "<td>&nbsp;<td>";
      echo "<td>&nbsp;<td>";
      echo "</tr>";

    }

    echo "</table></tr>";

?>
  <tr align="center">
    <td colspan="7">&nbsp;</td>
  </tr>
  <?php

    }
  }

}
  } else {
?>
  <tr>
    <td  colspan="7" align="center"><strong>Sem c&aacute;lculos</strong></td>
  </tr>
  <?php

  }
?>
</table>
<?php

  } else if ($solicitacao == "outros") {
  $sql = "select *
            from matricobs
		       where j26_matric = $parametro";
  $result = db_query($sql);
?>
<table width="95%" border="0" align="center"  cellpadding="0" cellspacing="2">
  <tr align="center">
    <td colspan="4" nowrap><u>Outros dados</u></td>
  </tr>
  <tr>
    <td colspan="4" nowrap>&nbsp; </td>
  </tr>
<?php

  if( pg_num_rows($result) != 0 ) {
    db_fieldsmemory($result,0);
?>
  <tr align="center" bgcolor="#CCCCCC">
    <td nowrap>Observações:</td>
  </tr>
  <tr align="center">
    <td bgcolor="#FFFFFF"><?=$j26_obs;?>&nbsp;</td>
  </tr>
  <?php

  } else {
?>
  <tr>
    <td  colspan="4" align="center" nowrap  ><strong>Sem outros dados&nbsp;</strong></td>
  </tr>
  <?php

  }
?>
</table>
<?php

  }else if($solicitacao == "RegistroImovel"){
 ?>
 <br>
  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
  <tr>
    <td colspan="2" align="center"><u>Dados do Registro de Imóveis</u></td>
  </tr>
  <tr>
    <td colspan="2" align="center"></td>
  </tr>

  <?php
   $sqlreg = " select * from iptubaseregimovel inner join setorregimovel on j69_sequencial = j04_setorregimovel where j04_sequencial = $parametro";
					    $resultreg = db_query($sqlreg);
					    $linhasreg = pg_num_rows($resultreg);
					    if($linhasreg>0){
					      db_fieldsmemory($resultreg,0);
					      ?>
					      <tr>
    <td align="left"  width="90">Matrícula: </td>
    <td align="left"><?=$j04_matricregimo;?></td>
  </tr>
  <tr>
    <td align="left">Setor:</td>
    <td align="left"><?=$j04_setorregimovel;?> - <?=$j69_descr;?></td>
  </tr>
  <tr>
    <td align="left">Quadra:</td>
    <td align="left"><?=$j04_quadraregimo;?></td>
  </tr>
  <tr>
    <td align="left">Lote:</td>
    <td align="left"><?=$j04_loteregimo;?></td>
  </tr>
  <tr>
    <td align="left"></td>
    <td align="left"></td>
  </tr>
  <?php

					    }
  ?>

    </table>
  <?php

}else if ($solicitacao == "Imagens") {

	db_query("begin");
   $result = db_query("select distinct j23_anousu from iptucalc where j23_matric = $parametro");
   if(pg_num_rows($result) > 1 && !isset($j23_anousu)) {
  ?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td align="center"><strong>Escolha o ano para o demonstrativo de cálculo:</strong></td>
	  </tr>
<?php

     for($tt=0;$tt<pg_num_rows($result);$tt++){
       db_fieldsmemory($result,$tt);
?>
	  <tr align="center">
	    <td>
		 <input type='radio' name='j23_anousu' value='<?=$j23_anousu;?>' onClick="location.href='cad3_conscadastro_002_detalhes.php?solicitacao=Imagens&parametro=<?=$parametro;?>&j23_anousu='+this.value"><strong><?=$j23_anousu;?></strong>
	    </td>
	  </tr>
<?php

     }
?>
	</table>

<?php

   }elseif(pg_num_rows($result) == 1 || isset($j23_anousu)){

     if(!isset($j23_anousu)){
       db_fieldsmemory($result,0);
     }
     $result = db_query("select j23_manual from iptucalc where j23_anousu = $j23_anousu and j23_matric = $parametro");
     db_fieldsmemory($result,0);
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><strong>DEMONSTRATIVO DE CÁLCULO - <?=$j23_anousu;?> </strong> </td>
  </tr>
  <tr align="center">
    <td>
	 <textarea class="db_area" rows="13" cols="72"><?=$j23_manual;?></textarea>
    </td>
  </tr>
</table>

<?php

  } else {
?>
<div align="center"> </div>
<table  align="center" width="100%" border="0" cellspacing="2" cellpadding="0">
  <tr>
    <td>
      <strong>Sem imagem cadastrada.</strong>
	</td>
  </tr>
</table>
<?php

  }
  db_query("end");
?>
    </td>
  </tr>
</table>
<?php

  } else if($solicitacao == "ListaITBI") {
 ?>
 <br>
  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
  <tr>
    <td colspan="2" align="center"><u>Lista de ITBI</u></td>
  </tr>
  <tr>
    <td colspan="2" align="center"></td>
  </tr>
  <?php

    $sSqlListaITBI  = " select it06_guia,												  			                           ";
    $sSqlListaITBI .= "		     it01_data,												  			                           ";
    $sSqlListaITBI .= "		     case														  			                             ";
    $sSqlListaITBI .= "			   when it14_guia is not null then 'Sim' else 'Não' 		  			       ";
    $sSqlListaITBI .= "		     end as dl_Liberada										 			                         ";
    $sSqlListaITBI .= "	  from itbimatric	   										      			                       ";
    $sSqlListaITBI .= "		     inner join itbi 		   on itbi.it01_guia 		   = itbimatric.it06_guia";
    $sSqlListaITBI .= "		     left  join itbiavalia on itbiavalia.it14_guia = itbimatric.it06_guia";
    $sSqlListaITBI .= "	 where it06_matric = {$parametro}			   								                   ";

    $funcao_js = "js_consultaDetalhesITBI|it06_guia";

    db_lovrot($sSqlListaITBI,50,"()","",$funcao_js,"");

  } else if ($solicitacao == "historicoProprietarios") { 
  ?>
  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
    <tr align="center">
      <td colspan="4"><u>Hist&oacute;rico de Propriet&aacute;rios</u></td>
    </tr>
    <tr align="center">
      <td colspan="4">&nbsp;</td>
    </tr>
    <?php
      $clhistoricoproprietarios = new cl_historicoproprietarios();
      $sql = $clhistoricoproprietarios->sqlHistoricoProprietariosAverbacao($parametro);
      $result = db_query($sql);

      $clrotulo = new rotulocampo;
      $clrotulo->label("j172_percentual");

      if( pg_num_rows($result) != 0 ) {
    ?>
    <tr align="center">
      <td bgcolor="#CCCCCC"><strong>Matr&iacute;cula</strong></td>
      <td bgcolor="#CCCCCC"><strong>Protocolo</strong></td>
      <td bgcolor="#CCCCCC"><strong>Tipo Lan&ccedil;amento</strong></td>
      <td bgcolor="#CCCCCC"><strong>Data</strong></td>
      <td bgcolor="#CCCCCC"><strong>Tipo Propriet&aacute;rio</strong></td>
      <td bgcolor="#CCCCCC"><strong>Tipo Promitente</strong></td>
      <td bgcolor="#CCCCCC"><strong>Adquirente</strong></td>
      <td bgcolor="#CCCCCC"><?=$Lj172_percentual?></td>
      <td bgcolor="#CCCCCC"><strong>Obs</strong></td>
    </tr>
    <?php
    for ($contador=0;$contador < pg_num_rows($result);$contador ++ ){
      db_fieldsmemory($result,$contador);
    ?>
      <tr align="center">
        <td bgcolor="#FFFFFF">
          <?=$matricula;?>
          &nbsp;</td>
        <td bgcolor="#FFFFFF">
          <?=$protocolo;?>
          &nbsp;</td>
        <td bgcolor="#FFFFFF">
          <?=$tipolancamento;?>
          &nbsp;</td>
        <td bgcolor="#FFFFFF">
          <?=db_formatar($datahistorico, 'd');?>
          &nbsp;</td>
        <td bgcolor="#FFFFFF">
          <?=$tipoproprietario;?>
          &nbsp;</td>
        <td bgcolor="#FFFFFF">
          <?=$tipopromitente;?>
          &nbsp;</td>
        <td bgcolor="#FFFFFF">
          <?=$adquirente;?>
          &nbsp;</td>
        <td bgcolor="#FFFFFF">
          <?=$percentual;?>
          &nbsp;</td>
        <td bgcolor="#FFFFFF">
          <?=$observacao;?>
          &nbsp;</td>
      </tr>
      <?php
      }
    } else {
    ?>
      <tr>
        <td  colspan="4" align="center"><strong>Sem histórico de proprietários</strong></td>
      </tr>
    <?php
    }
  }
  ?>
</body>
</html>
<script type="text/javascript">

  function js_mostra_histcalculo(anousu, matricula) {
    js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_consulta', 'cad3_histcalculoanual.php?matricula='+matricula+'&exercicio='+anousu, 'Histórico Cálculo Anual', true);
  }

  function js_consultaDetalhesITBI(iGuia){

    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_consulta','itb4_consultaitbi001.php?it01_guia='+iGuia,'Pesquisa',true,30);
    db_iframe_consultaitbi.hide();
  }

  function js_emiteCertidao(codigoIsencao, matricula){

    jan = window.open('cad2_certisen002.php?matric='+matricula+'&codigoIsencao='+codigoIsencao,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
    jan.moveTo(0,0);
  }
</script>
