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

parse_str($_SERVER['QUERY_STRING']);
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_sql.php"));

?>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>
  function js_atualizaValorComDesconto(novoValor, id, vlrmin, possuiDesconto, percentualDesconto){
    //if (possuiDesconto || Number(percentualDesconto) !== 1) {
      document.getElementById('valorparcelamentocomdesconto').innerHTML = Number(novoValor).toFixed(2);
    //}

    parent.js_valparc(id, vlrmin, possuiDesconto, percentualDesconto);
  }
</script>
<form name='form1' action=''>
<table border="1">
<tr align='center'>
<td colspan="3">
<input type='hidden' name='vt' value='<?=$valor?>'>
<strong>Valor do parcelamento sem desconto: </strong><font id="vt"></font>
<?php
echo "<script>document.getElementById('vt').innerHTML = \"$valor\"</script>";
?>
<br>
<input type='hidden' name='vtcomdesconto' value='<?=$valorcomdesconto?>'>
<input type='hidden' name='temdesconto' value='<?=$temdesconto?>'>
<font style="display: none;" id="vtcomdesconto"><?=$valorcomdesconto?></font>
<strong>Valor do parcelamento com desconto: </strong><font id="valorparcelamentocomdesconto"><?=$valorcomdesconto?></font>
</td>
</tr>

<?php

echo "<tr bgcolor='#6699cc'> <pre>";
$linha = -1;
$tipo1 = explode("-",$tiposparc);

$ultmaxparc = null;
$ultparc    = null;

for ($contatipo1 = 0; $contatipo1 < sizeof($tipo1); $contatipo1++) {
  $tipo2 = explode("=", $tipo1[$contatipo1]);

  $tipoparc = $tipo2[0];
  $maxparc  = $tipo2[1];
  $descmul  = $tipo2[2];
  $descjur  = $tipo2[3];
  $forma		= $tipo2[5];
  $descvlr  = $tipo2[6];
  $vlrmin		= $tipo2[7];
  $tipovlr  = $tipo2[8];
  $minparc  = $tipo2[9];
  $cadtipoparc = $tipo2[10];

  if ($ultparc == null) {
    $ultparc  = $tipo2[9];
  }

  if ($forma == 2) {
    $ultparc = 3;
  }

	$registros=explode("=", $valoresportipo);

	$valorTotal			       = 0;
  $valorTotalParcelado   = 0;
  $valorMinimoPorParcela = 0;

	for ($x=0; $x < sizeof($registros); $x++) {

		if ($registros[$x] == "") {
			continue;
		}

		$valores = explode("-", $registros[$x]);
	  $valdesconto	= 0;

		$k03_tipo					= $valores[0];
		$k00_cadtipoparc	= $valores[1];
		$k00_vlrhis				= $valores[2];
		$k00_vlrcor				= $valores[3];
		$k00_juros 				= $valores[4];
		$k00_multa 				= $valores[5];
		$k00_desconto			= $valores[6];
		$k00_total   			= $valores[7];

    $cltipoparc        = new cl_tipoparc();
    $campos            = "tipoparc,descr,dtini,dtfim,maxparc,minparc,vlrmin,vlrmax,dtvlr,vlrmindeb,vlrmaxdeb,inflat,descvlr,descmul,descjur,k42_minentrada,cadtipoparc,k40_descr";
    $sqlCaseTipoPessoa = ",case when tipopessoa = 0 then 'Todos' when tipopessoa = 1 then 'Física' when tipopessoa = 2 then 'Jurídica' ELSE '' END as tipopessoa";
    $sqlTipoParc       = $cltipoparc->sql_query(null,$campos.$sqlCaseTipoPessoa,"tipoparc","cadtipoparc.k40_codigo=$k00_cadtipoparc and tipoparc.minparc > 1 and tipoparc = $tipoparc");
    $rsTipoParc        = db_query($sqlTipoParc);

    $regraParcelamento = db_utils::getCollectionByRecord($rsTipoParc)[0];

    $dataAtual        = new DateTime('now');
    $dataInicialRegra = DateTime::createFromFormat('Y-m-j H:i:s', $regraParcelamento->dtini . ' 00:00:01');
    $dataFinalRegra   = DateTime::createFromFormat('Y-m-j H:i:s', $regraParcelamento->dtfim . ' 23:59:59');
    $regraValida      = $dataInicialRegra <= $dataAtual && $dataAtual <= $dataFinalRegra;

    if(!$regraValida){
      continue;
    }

    $valorHistorico    = $k00_vlrhis;
    $valorCorrigido    = $k00_vlrcor;
    $juros             = $k00_juros;
    $multa             = $k00_multa;
    $desconto          = $k00_desconto;
    $valorTotal       += $k00_total;

    $percentualDescontoMulta = $regraParcelamento->descmul;
    $percentualDescontoJuros = $regraParcelamento->descjur;
    $multaTotal              = $multa * ((100 - $percentualDescontoMulta) / 100);
    $jurosTotal              = $juros * ((100 - $percentualDescontoJuros) / 100);

    $valorTotalParcelado          += $valorCorrigido + $multaTotal + $jurosTotal;
    $valorMinimoPorParcela        += $regraParcelamento->vlrmin;
    $aplicaRegraAntesDoLancamento = $regraParcelamento->k40_aplicacao == '2';

    if ($k00_cadtipoparc > 0 && !$aplicaRegraAntesDoLancamento) {

      $valdescontocorrecao = 0;
      if ($tipovlr == 1) {
        $valdescontocorrecao = ($k00_vlrcor - $k00_vlrhis) * $descvlr / 100;
      } else if ($tipovlr == 2) {
        $valdescontocorrecao = ($k00_vlrcor) * $descvlr / 100;
      }

			$valdesconto	+= $valdescontocorrecao + ($k00_juros * $descjur / 100) + ($k00_multa * $descmul / 100);

			$valorTotal			+= $k00_vlrcor + ($k00_juros + $k00_multa) - $valdesconto;

		} else {
			$valorTotal			+= $k00_vlrcor + $k00_juros + $k00_multa;
		}
  }
  
  if ((float)$valorTotal > (float)$valor) {
    $valorTotal = (float)$valor;
  }

  if ($ultmaxparc == null) {
    $ultmaxparc = $ultparc;
  }

  // adiciona desconto de valor k42_minentrada, pois como é obrigado a dar essa entrada deve se descontar
  // do resto da divida e entao gerar as parcelas respeitando as regras de parcelamento

  $entradaminima = 0;

  if ($tipo2[4] > 0) {
    $entradaminima = $valorTotal * $tipo2[4] / 100;
  }

  $valorTotal = $valorTotal - $entradaminima;


  if ( ( round($valorTotal/$ultparc,2) >= $vlrmin ) and ( $ultparc >= $ultmaxparc ) ) {

 $sSql = "select k40_controlavencimento
          from cadtipoparc
          inner join tipoparc on k40_codigo = cadtipoparc
          where k40_codigo = $k00_cadtipoparc";

  $result = db_query($sSql) or die($sSql);

  db_fieldsmemory($result, 0);

  if($k40_controlavencimento == 't'){
    $mesesano = 12;
    $mesatual = date('m');
    $maxparc = $mesesano - ($mesatual - 1);
  }

    for ($parcela = $ultparc; $parcela <= $maxparc; $parcela++) {

      $i = $parcela;

      if ($i < $minparc) {
        continue;
      }
      if (round($valorTotal/$i,2) < $vlrmin) {

        break;
      }

      if($i%2 == 0){
        $cor='#6699cc';
      }else{
        $cor='#99ccaa';
      }
      if($linha == 2){
        echo "</tr>";
        echo "<tr bgcolor='$cor'>";
      }

      $entradaminima = calculaPercentual($regraParcelamento->k42_minentrada,$valorTotalParcelado);
      $percentualDesconto = 1;

      if ((float)$valorTotalParcelado < (float)$valor) {
        $percentualDesconto = (((float)$valorTotalParcelado * 100) / (float)$valor) / 100;
      }

      $valorTotalParceladoSemEntrada = $valorTotalParcelado - $entradaminima;
      $valorMedioParcelas = $valorTotalParceladoSemEntrada / ($i - 1);

      if (($valorMedioParcelas < $regraParcelamento->vlrmin || $valorTotalParcelado/$i < $regraParcelamento->vlrmin)){
        continue;
      }

      echo "<td nowrap align='left' valign='top'>
              <input type='radio' name='val' id='val$i' onClick=\"parent.document.form1.parc.value='".($i - 1)."';js_atualizaValorComDesconto($valorTotalParcelado, $i, $vlrmin,$temdesconto,$percentualDesconto);\" value=''>$i X R$<font id='$i'>
                ".str_pad(number_format(($valorTotalParcelado/$i),2,",","."),strlen(round($valorTotalParcelado/$i)),",",STR_PAD_LEFT)."
              </font>
            </td>";
      if ($linha == 2) {
        $linha = 0;
      } else{
        $linha +=1;
      }
    }

    $ultparc = $parcela;
    $ultmaxparc = $maxparc;

  }

}

function calculaPercentual($percentage, $total) {
  return (int)$percentage ? (number_format($percentage, 2) / 100) * round((float)$total, 2) : 0;
}

?>
</table>
</form>
