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

$lLiberado = true;
$sSqlValidaDataConciliacao  = "select max(c99_data) ";
$sSqlValidaDataConciliacao .= "  from condataconf ";
$sSqlValidaDataConciliacao .= " where c99_instit = ".db_getsession("DB_instit");
$sSqlValidaDataConciliacao .= " having max(c99_data) >= '{$data}'";
$rsValidaDataConciliacao   = db_query($sSqlValidaDataConciliacao);
if (pg_num_rows($rsValidaDataConciliacao) > 0) {
	//  $lLiberado = false;
	//  nao testa mais a data da contabilidade ( nao faz sentido )
  $lLiberado = true;
}

$lUltimaData = false;
$sSqlValidaUltimaData  = "select max(k68_data) ";
$sSqlValidaUltimaData .= "  from concilia ";
$sSqlValidaUltimaData .= " where k68_contabancaria = {$conta}";
$sSqlValidaUltimaData .= " having max(k68_data) = '{$data}'";
$rsValidaUltimaData   = db_query($sSqlValidaUltimaData);
if (pg_num_rows($rsValidaUltimaData) > 0) {
  $lUltimaData = true;
}

/**
 * Valida se data atual é menor que da ultima conciliação aberta/reaberta
 */
$lDataMenorUltimaConciliacao = false;
$sSqlValidaUltimaConciliacao  = "select max(k68_data) ";
$sSqlValidaUltimaConciliacao .= "  from concilia ";
$sSqlValidaUltimaConciliacao .= " where k68_contabancaria = {$conta} ";
$sSqlValidaUltimaConciliacao .= "   and k68_conciliastatus in(1,3)";
$sSqlValidaUltimaConciliacao .= " having max(k68_data) > '{$data}' ";
$rsValidaUltimaConciliacao   = db_query($sSqlValidaUltimaConciliacao);
if (pg_num_rows($rsValidaUltimaConciliacao) > 0) {
  $lDataMenorUltimaConciliacao = true;
}

/* variaveis de configuração do formulario  */
$borda = 0;
$iInstit = db_getsession("DB_instit");

$sqlDadosConta  = " select distinct ";
$sqlDadosConta .= "        db83_sequencial as reduzido,";
$sqlDadosConta .= "        db83_descricao  as descricao,";
$sqlDadosConta .= "        db90_codban as banco,";
$sqlDadosConta .= "        db83_conta||'-'||db83_dvconta as contabancaria,";
$sqlDadosConta .= "        db89_codagencia||'-'||db89_digito as agencia,";
$sqlDadosConta .= "        db83_contaunica as conta_unica";
$sqlDadosConta .= "   from contabancaria ";
$sqlDadosConta .= "        inner join bancoagencia on bancoagencia.db89_sequencial = contabancaria.db83_bancoagencia ";
$sqlDadosConta .= "        inner join db_bancos    on db_bancos.db90_codban        = bancoagencia.db89_db_bancos ";
$sqlDadosConta .= "  where contabancaria.db83_sequencial = {$conta} ";
$rsDadosConta   = db_query($sqlDadosConta);
if($rsDadosConta && pg_num_rows($rsDadosConta) > 0){
  db_fieldsmemory($rsDadosConta,0);
}

/* variaveis dos saldos  */
$saldo_tesouraria = 0;
$saldo_extrato = 0;
$quais_reduzidos = "(";

$sSqlReduz  = " select c61_reduz, ";
$sSqlReduz .= "             c61_instit , ";
$sSqlReduz .= "            (select case ";
$sSqlReduz .= "                            when k97_situacao = 'D' ";
$sSqlReduz .= "                                 then k97_saldofinal * -1 ";
$sSqlReduz .= "                            else k97_saldofinal ";
$sSqlReduz .= "                         end as k97_saldofinal ";
$sSqlReduz .= "               from extratosaldo ";
$sSqlReduz .= "             where k97_contabancaria = {$conta} ";
$sSqlReduz .= "                  and k97_dtsaldofinal = '$data') as saldo_extrato";
$sSqlReduz .= "   from contabancaria ";
$sSqlReduz .= "        inner join conplanocontabancaria on conplanocontabancaria.c56_contabancaria = contabancaria.db83_sequencial ";
$sSqlReduz .= "        inner join conplanoreduz         on conplanoreduz.c61_codcon = conplanocontabancaria.c56_codcon ";
$sSqlReduz .= "                                        and conplanoreduz.c61_anousu = conplanocontabancaria.c56_anousu ";
$sSqlReduz .= "                                        and conplanoreduz.c61_anousu = ".db_getsession('DB_anousu');
//se for conta unica a instituicao nao deve ser considerada
if ($conta_unica == "f") {
    $sSqlReduz .= " and conplanoreduz.c61_instit = ".db_getsession('DB_instit');
}
$sSqlReduz .= "  where contabancaria.db83_sequencial = {$conta} ";
$rsReduz    = db_query($sSqlReduz);

$sReduzidos = "Não encontrado";
$aReduzidos = array();

if( $rsReduz && pg_num_rows($rsReduz) > 0 ) {

  for ($i = 0; $i <  pg_num_rows($rsReduz); $i++) {

    db_fieldsmemory($rsReduz,$i);

    $quais_reduzidos .= ($quais_reduzidos =='('?'':',').$c61_reduz;

    $aReduzidos[] = $c61_reduz;

    $sqlSaldoContaCaixa = "select substr(fc_saltessaldo(".$c61_reduz.",'".$data."','".$data."',null,{$c61_instit}),41,13)::float as saldocontacaixa";
    $rsSaldoContaCaixa  = db_query($sqlSaldoContaCaixa);
    if ( pg_num_rows($rsSaldoContaCaixa) > 0) {
      db_fieldsmemory($rsSaldoContaCaixa,0);
      $saldo_tesouraria += $saldocontacaixa;
    }
  }
}
$quais_reduzidos .= ')';

$sql = " select round(sum(k86_valor),2) as saldo_pendencias from (
           select case when k86_tipo = 'D' then k86_valor else k86_valor * -1 end as k86_valor
        from conciliapendextrato
             inner join concilia on k88_concilia = k68_sequencial
             inner join extratolinha on k86_sequencial = k88_extratolinha
             inner join extrato on k85_sequencial = k86_extrato
	where k86_contabancaria = $conta and k68_data = '$data' and k86_bancohistmov = 1
       ) as x
       ";

$rsSaldoContaCaixa  = db_query($sql);

if ( pg_num_rows($rsSaldoContaCaixa) > 0) {
   db_fieldsmemory($rsSaldoContaCaixa,0);
}

$saldo_conciliacao = $saldo_extrato + ( $saldo_pendencias ) ;


$saldo_pendencias = 0;

$sql = "select round(sum(k86_valor),2) as saldo_pendencias
          from (

               select case
                         when k86_tipo = 'C' then k86_valor * -1
                         else k86_valor end as k86_valor
	             from conciliapendextrato
                 inner join concilia on k88_concilia = k68_sequencial
                 inner join extratolinha on k86_sequencial = k88_extratolinha
                 inner join extrato on k85_sequencial = k86_extrato
	             where k86_contabancaria = $conta
                   and k68_data = '$data'
                   and k86_bancohistmov = 2

	union all

        select round(sum(case
                          when tipo = 'cheque' or tipo = 'credito' then valor
                          else valor * -1 end),2) as valor
            from (
                   select
                      max( case
                             when richeque is not null and richeque <> 0 and rivalorcredito <> 0 then 'cheque'
                             when rnvalordebito is not null and rnvalordebito <> 0 or richeque is not null and richeque <> 0 and rnvalordebito <> 0 then 'debito'
	                         when rivalorcredito is not null and rivalorcredito <> 0 then 'credito'
                            end) as tipo,
                        ricaixa,
                        riautent,
                        ridata,
                        (select e60_codemp||'/'||e60_anousu from empempenho where e60_numemp = riempenho ) as riempenho,
                        riordem,
                        riplanilha,
                        rislip,
                        richeque as cheque,
                        max(case when rnvalordebito is not null and rnvalordebito <> 0 then 'D' else 'C' end) as tipomov,
                        sum(case when rnvalordebito is not null and rnvalordebito <> 0 then rnvalordebito else rivalorcredito end) as valor,
                        k89_justificativa
                   from conciliapendcorrente
                        inner join concilia on k89_concilia = k68_sequencial
                        inner join fc_extratocaixa(".db_getsession('DB_instit').", $conta, null, null, false ) on ricaixa = k89_id
                                                                                                              and riautent = k89_autent
                                                                                                              and ridata = k89_data
                   where k68_contabancaria = $conta
                     and k68_data = '$data'
                     and not exists (select 1
                                       from corgrupocorrente
                                       where k105_autent = k89_autent
                                         and k105_id = k89_id
					 and k105_data = k89_data
                                         and ( ( ( k105_corgrupotipo in (2,3,5,6) and extract(year from k105_data) <= 2012 ) )                                               or ( k105_corgrupotipo in (2,3) ) )

)
                group by ricaixa,
                         riautent,
                         ridata,
                         riempenho,
                         riordem,
                         riplanilha,
                         rislip,
                         richeque,
                         k89_justificativa

              ) as x

) as x";

$rsSaldoContaCaixa  = db_query($sql);

if ( pg_num_rows($rsSaldoContaCaixa) > 0) {
   db_fieldsmemory($rsSaldoContaCaixa,0);
}
$saldo_conciliacao -=  ( $saldo_pendencias ) ;


if (count($aReduzidos) > 0) {

    $sReduzidos = implode(", ", $aReduzidos);
}

?>
<div id='disable' style='background-color: #000; -moz-opacity: 0.6;opacity: 0.6; filter:alpha(opacity=80); position: absolute; width: 0%;height: 0%; top: 17; left: 0; z-index: 9'>
</div>
<form name="form1" enctype="multipart/form-data" method="post" action="">

<!-- div de mensagem -->
<div id='loading' > </div>

<table width='100%' border=<?php echo $borda?>>
  <tr>
    <td colspan=3 bgcolor='#000099'>
       <center><b><font color='#FFFFFF'> Conciliação Bancária em <?php echo db_formatar($data,'d')?></font></b></center>
    </td>
  </tr>
  <tr>
    <td align='left' width='33%'>
      <!-- dados do extrato bancario -->
      <fieldset>
      <Legend align="left"><b> Dados do Banco : </b></Legend>
        <table border=<?php echo $borda?> width='100%'>
          </tr>
          <tr>
          <tr>
            <td nowrap> <b>Banco: </b> <?php echo @$banco?> <b> Agencia : </b> <?php echo @$agencia?> </td>
	    <td nowrap <b> Conta : </b>   <?php echo @$contabancaria?>     </b> </td>
          </tr>
          <tr >
            <td nowrap colspan=2 align=right>
              <input name="esconderext" type="button" id="esconderext" value="Esconder"          onClick="js_hideFrame(document.getElementById('mostrarext'),this,'extrato',true);">
	      <input name="mostrarext"  type="button" id="mostrarext"  value="Mostrar" disabled  onClick="js_hideFrame(document.getElementById('esconderext'),this,'extrato',false);">
	      <input name="digitasaldoextratomanual"
                     type="button"
                     id="digitasaldoextratomanual"
                     value="Edição Saldo e Pendências"
                     onClick='js_digitasaldoextratomanual();'
                     size='10' >

            </td>
          </tr>
        </table>
      </fieldset>
    </td>

    <td align='left' width='33%'>
      <fieldset>
      <Legend align="left"><b> Saldos : </b></Legend>
	 <table>
	   <tr>
         <td nowrap width='25%' > <b>Extrato: </b> </td>
	     <td nowrap width='25%' align='right'>
           <input name="vlr_saldoextrato" size='15'  id='id_saldo_extrato' style='border: 0; text-align: right' onchange="js_calcula();" type="text" align="right"   value="<?php echo db_formatar(@$saldo_extrato,'f')?>" ></td>
         <td nowrap width='25%' > <b>Tesouraria: </b> </td>
	     <td nowrap width='25%' align='right'>
	       <input name="vlr_saldo_tesouraria"  size='15' id='id_saldo_tesouraria'  style='border: 0; text-align: right'  align="right"  type="text" value="<?php echo db_formatar(@$saldo_tesouraria,'f')?>" >
        </td>
       </tr>
	   <tr>
         <td  nowrap  align='right'> <b>Conciliação: </b> </td>
	     <td  nowrap align='right'  >
           <input name="vlr_conciliacao" id='id_saldo_conciliacao'  size='15'  style='border: 0; text-align: right'  align="right"  type="text" readonly  value="<?php echo db_formatar(@$saldo_conciliacao,'f')?>" > </td>
         <td  nowrap  align='right'> <b>Diferença: </b> </td>
	     <td  nowrap align='right'  >
            <input name="vlr_diferenca" id='id_saldo_diferenca'  size='15' style='border: 0; text-align: right'  align="right"  type="text" readonly  value="<?php echo db_formatar(@$saldo_tesouraria - @$saldo_conciliacao,'f')?>" >
            <input name="saldo_diferenca" id='saldo_diferenca'  type="hidden" value="<?php echo round(@$saldo_tesouraria - @$saldo_conciliacao,2)?>" >
         </td>
       </tr>
     </table>
      </fieldset>
    </td>

    <td align='left' width='33%'>
    <!-- dados da autenticacao no sistema -->
      <fieldset>
      <Legend align="left"><b> Dados da Tesouraria : </b></Legend>

        <table border=<?php echo $borda?> width='100%' >
          <tr>
	        <td nowrap width='25%' title="<?php echo @$descricao?>"> <b><?php echo substr(@$descricao,0,40)?></b> </td>
            <td nowrap width='25%' title='Reduzidos: <?php echo $quais_reduzidos?>'><b>Seq. Conta:</b> <?php echo @$reduzido?> </td>
          </tr>
        </table>

        <table border=<?php echo $borda?> width='100%' >
          <tr>
	        <td nowrap style="vertical-align: top;" width='3%' title='Reduzidos: <?php echo $quais_reduzidos?>' > <b>Reduzidos:</b> </td>
            <td width='45%' title='Reduzidos: <?php echo $quais_reduzidos?>'><?php echo $sReduzidos?> </td>
          </tr>
        </table>

        <table border=<?php echo $borda?> width='100%' >
          <tr>
             <td nowrap width='25%' title="<?php echo @$descricao?>"> <b><?php echo substr(@$descricao,40,40)?></b> </td>
            <td nowrap align='right' width='25%'>
	          <input name="novaconta" type="button" id="novaconta" value="Sair da Conta"        onClick="location.href = 'cai4_alteraconciliacao_manual.php'">
              <input name="esconderaut" type="button" id="esconderaut" value="Esconder"          onClick="js_hideFrame(document.getElementById('mostraraut'),this,'autenticacoes',true);">
              <input name="mostraraut"  type="button" id="mostraraut"  value="Mostrar"  disabled onClick="js_hideFrame(document.getElementById('esconderaut'),this,'autenticacoes',false);">
            </td>
          </tr>
        </table>
      </fieldset>
    </td>
  </tr>
</table>

<!-- tabela com os iframes extrato e autenticacao -->
<table width='100%' border=<?php echo $borda?>>
  <tr width='100%'>
    <td>
    <div id='extrato'>
      <table width='100%' border=<?php echo $borda?>>
        <tr>
          <td bgcolor='#000099'>
             <center><b><font color='#FFFFFF'> Dados do extrato bancário</font></b></center>
          </td>
        </tr>
        <tr>
          <td>
             <iframe name="iframeExtrato" id="iframeExtrato" src="cai4_listaextrato_manual.php?data=<?php echo $data?>&conta=<?php echo $conta?>" width='100%' height='300'></iframe>
          </td>
        </tr>
      </table>
    </div>
    </td>

    <td>
    <div id='autenticacoes'>
      <table width='100%' border=<?php echo $borda?>>
        <tr>
          <td bgcolor='#000099'>
            <center><b><font color='#FFFFFF'>Dados da autenticação no sistema</font></b></center>
          </td>
        </tr>
        <tr>
          <td>
            <iframe name="iframeAutent" id="iframeAutent" src="cai4_listaautenticacoes_manual.php?data=<?php echo $data?>&conta=<?php echo $conta?>" width='100%' height='300' > </iframe>
          </td>
        </tr>
      </table>
    </div>
    </td>
  </tr>
</table>
<! -- totalizadores -->
<table width=90% border=<?php echo $borda?>>
  <tr>
    <td align='right' width='50%'>
      <b>Valor total selecionado do extrato: </b>
      <input name="totalextrato" type="text" id="totalextrato" style="text-align:right" value="" readonly>
    </td>
    <td align='right' nowrap >
      <b>Valor total de autenticações selecionado : </b>
      <input name="totalautent" type="text" id="totalautent" style="text-align:right" value="" readonly>
    </td>
  </tr>

  <tr>
    <td colspan=2>
      <table width=100% border=<?php echo $borda?>>
        <tr>
          <td align='right' width='100%'>
            <fieldset>
              <Legend align="left"><b> Opções de visualização : </b></Legend>
              <table border=<?php echo $borda?> width='90%' >
                <tr>
                  <td align='center'> <b>Datas:</b>
                  <script>
                  function js_troca_data(data,codigo){
                    document.location.href = 'cai4_concbanc001_manual.php?conta=<?php echo $reduzido?>&data='+data.substr(6,4)+'-'+data.substr(3,2)+'-'+data.substr(0,2)+'&concilia='+codigo;
                  }
                  </script>
                    <select name='data_conciliar' onChange="js_troca_data(this.options[this.selectedIndex].text,this.value)">
                  <?php
                  $sqlDadosContaDatas = " select k68_sequencial,
                                                  k68_data
                                             from concilia
                                            where k68_contabancaria = {$conta}
                                            order by k68_data desc";
                  $rsDadosContaDatas   = db_query($sqlDadosContaDatas);
                  if($rsDadosContaDatas && pg_num_rows($rsDadosContaDatas) > 0){
                    global $k68_data;
                    for ($i=0;$i<pg_num_rows($rsDadosContaDatas);$i++){
			    db_fieldsmemory($rsDadosContaDatas,$i);
			    // nao vai mais mostrar as datas anteriores.
                       if ( $k68_sequencial==$concilia ) {
			       echo "<option value='$k68_sequencial' ".($k68_sequencial==$concilia?' selected ':'').">".db_formatar($k68_data,'d')."</option>";
		       }
                    }
                  }
                  ?>
                  </select>
		  </td>

            <td align='center'> <b>Arquivo:</b>
            <?php
/*
            $sCampos = "distinct e75_codgera, 'codret: ' || e75_codret || '-' || e75_arquivoret";

            $sWhere =  "  c56_contabancaria = $conta
                      and e76_dataefet between '$data'::date - '1 day'::interval
                      and '$data'::date
                      and e80_instit = $iInstit";
            $sSql = $clempagedadosret->sql_query_retorna_arquivos(null, $sCampos, "e75_codgera",$sWhere);
 */

            $sSql = $clempagedadosret->sql_query_retorna_arquivosFiltrados($conta, $data, $concilia)  ;
	    $rsArquivos = $clempagedadosret->sql_record($sSql);

            $db_passapar = "true";
            if($clempagedadosret->numrows == 0){
              $db_passapar = "false";
	    }
            db_selectrecord("e75_codgera",$rsArquivos,true,1,"","","","0-Nenhum");
            ?>

              <input name="verobn"
                     type="button"
                     id="verobn"
                     value="Marcar"
                     onClick='js_verobn();'
              >

   	    </td>
                  <td align='center'> <input name="chkConciliado"     type="checkbox" id="chkconciliado"     value="" checked onClick="js_escondeLinha(this,'conciliado');"     > <b> Conciliados      </b> </td>
                  <td align='center'> <input name="chkPendente"       type="checkbox" id="chkpendente"       value="" checked onClick="js_escondeLinha(this,'pendente');"       > <b> Pendentes        </b> </td>
                  <td align='center'> <input name="chkPreselecionado" type="checkbox" id="chkpreselecionado" value="" checked onClick="js_escondeLinha(this,'preselecionado');" > <b> Pre-selecionados </b> </td>
                  <td align='center'> <input name="chkNormal"         type="checkbox" id="chknormal"         value="" checked onClick="js_escondeLinha(this,'normal');"         > <b> Correntes        </b> </td>
                </tr>
              </table>
            </fieldset>
          </td>
        </tr>
      </table>
    </td>
  </tr>

</table>

<!-- botoes de acao -->
 <table width=90% border=<?php echo $borda?>>
  <tr>
    <td align='right' width='100%'>
      <fieldset>
        <Legend align="left"><b> Ações : </b></Legend>
        <table border=<?php echo $borda?> width='90%' >
          <tr>
            <td align='center'>
              <input name="processarconciliacao"
                     type="button"
                     id="procconc"
                     value="Conciliar Registros"
                     onClick='js_selecao();'
                     style='width:200px' >
            </td>
            <td align='center'>
               <input name="salvarconciliacao"
                      type="button"
                      id="salvar"
                      value="Salvar Conciliação"
                      disabled
                      <?php echo ( !$lDataMenorUltimaConciliacao && $lLiberado ) ? "" : "class=bloqueado "; ?>
                      onClick='js_salvarConciliacao();'
                      style='width:200px' >
            </td>
            <td align='center'>
              <input name="desprocessaritens"
                     type="button"
                     id="desproc"
                     value="Ativar modo desprocessar Itens"
                     <?php echo ($lLiberado==true)?"":"disabled"?>
                     onClick='js_ativarModo();'
                     style='width:150px' >
            </td>
            <td align='center'>
              <input name="confimadesprocessamento"
                     type="button"
                     id="confdesproc"
                     value="Confirma desprocessamento"
                     disabled
                     onClick='js_confirmaDesproc();'
                     style='width:150px' >
	    </td>
            <td align='center'>
              <input name="gerarrelatorio"
                     type="button"
                     id="relatorio"
                     value="Gerar Relatório"
                     onClick='js_relatorio();'
                     style='width:130px' >
            </td>
            <td align='center'>
              <input name="proximo"
                     type="button"
                     id="proximo"
                     value="Proximo Dia"
                     disabled="disabled"
                     onClick='js_getProximaData("dia");'
		     style='width:100px' >
              <input name="proximomes"
                     type="button"
                     id="proximomes"
                     value="Próximo Mês"
                     disabled="disabled"
                     onClick='js_getProximaData("mes");'
                     style='width:100px' >
            </td>
          </tr>
        </table>
      </fieldset>
    </td>
  </tr>
</table>

<!-- codigo da conciliacao -->
<input name="concilia"          type="hidden" id="concilia" value="<?php echo @$concilia?>" >
<input name="conta"             type="hidden" id="conta"    value="<?php echo @$conta?>" >
<input name="data"              type="hidden" id="data"     value="<?php echo @$data?>" >
<input name="data_ant"          type="hidden" id="data_ant" value="<?php echo @$data?>" >
<input name="modooperacao"      type="hidden" id="modo"     value="desativado" >

<input name="load_extrato"      type="hidden" id="load_extrato"      value="false" >
<input name="load_autenticacao" type="hidden" id="load_autenticacao" value="false" >
<input name="salvo"             type="hidden" id="salvo"             value="false" >

</form>
<script>

var lLiberado   = <?php echo ($lLiberado==true)?"true":"false"?>;
var lUltimaData = <?php echo ($lUltimaData==true)?"true":"false"?>;
var lDataMenorUltimaConciliacao = <?php echo ($lDataMenorUltimaConciliacao ? 'true' : 'false'); ?>

/**
 * Habilita/desabilita botoes:
 * - Salvar Conciliação
 * - Proximo
 * - Ativar modo desprocessar itens
 *
 * @access public
 * @return boolean
 */

 var lBloqueProximo = true;
function js_habilitaSalvar() {

  if ( !lLiberado ) {
    return false;
  }

  var lDesabilitaModoDesprocessar  = true;
  var lDesabilitaSalvar            = true;
  var lDesabilitarProximo          = true;
  var lDesabilitarProximomes          = true;

  /**
   * Habilita botao proximo
   */
  if ( !lUltimaData ) {
    lDesabilitarProximo = false;
    lDesabilitarProximomes = false;
  }

  /**
   * Iframes nao carregados ainda ou conciliao bloqueadada
   */
  if ( $F("load_extrato") == "true" && $F("load_autenticacao") == "true" ) {

    lDesabilitaModoDesprocessar = false;

    /**
     * Valida:
     * - Data nao é menor que a ultima conciliao aberta
     * - "Botão Salvar Conciliação" não clicaco
     */
    if ( !lDataMenorUltimaConciliacao || $('salvo').value == 'false' ) {
      lDesabilitaSalvar = false;
    }
  }

  //alert("bloque : " + lBloqueProximo + "\nproximo ; " + lDesabilitarProximo + "\nsalvar: " + lDesabilitaSalvar);

  $('desproc').disabled = lDesabilitaModoDesprocessar;
  $('salvar').disabled  = lDesabilitaSalvar;
  $("proximo").disabled = lDesabilitarProximo;
  $("proximomes").disabled = lDesabilitarProximomes;
  if (!lBloqueProximo) {

    $("proximo").disabled = false;
    $("proximomes").disabled = false;
    //lBloqueProximo = true;
  }
  return true;
}

function js_desprocessarItens(item,origem) {
  var modoatual = $('modo').value;
  if(modoatual == 'desativado'){
    return false;
  }

  var objIframeExtrato = iframeExtrato.document;
  var objIframeAutent  = iframeAutent.document;
  var codigoconcilia   = $('concilia').value;
  var objTableExtrato  = objIframeExtrato.getElementById('tabelaExtrato');
  var objTableAutent   = objIframeAutent.getElementById('tabelaAutent');

  if (objTableExtrato != null && objTableExtrato.rows != undefined) {
    for (i=0;i < objTableExtrato.rows.length-1; i++ ){
      eval ('var objLinhaExtrato = '+objIframeExtrato.getElementById("objJSON"+i).value);
      if(objTableExtrato.rows[(i+1)].className == 'conciliado'){
        var classeatual  = 'desprocessado';
        var desabilitado = true;
      }else{
        var classeatual  = 'conciliado';
        var desabilitado = true;
      }
      marcado = objIframeExtrato.getElementById('marcado'+i);
      if(objLinhaExtrato.itemconciliacao == item){
        objTableExtrato.rows[(i+1)].className = classeatual;
        marcado.disabled = desabilitado;
      }
    }
  }

  if (objTableAutent != null && objTableAutent.rows != undefined ) {
    for (i=0;i < objTableAutent.rows.length-1; i++ ){
      eval ('var objLinhaAutent = '+objIframeAutent.getElementById("objJSON"+i).value);
      if(objTableAutent.rows[(i+1)].className == 'conciliado'){
        var classeatual  = 'desprocessado';
        var desabilitado = true;
      }else{
        var classeatual  = 'conciliado';
        var desabilitado = true;
      }
      marcado = objIframeAutent.getElementById('marcado'+i);
      if(objLinhaAutent.itemconciliacao == item){
        objTableAutent.rows[(i+1)].className = classeatual;
        marcado.disabled = desabilitado;
      }
    }
  }
}

function js_confirmaDesproc(){
  var confirmacao = confirm(' Essa operacao implicara na geração de pendencias para os itens selecionados \n Confima operação ?');
  if(!confirmacao){
    return false;
  }

  var codigoconcilia   = $('concilia').value;
  var objIframeExtrato = iframeExtrato.document;
  var objIframeAutent  = iframeAutent.document;
  var objTableExtrato  = objIframeExtrato.getElementById('tabelaExtrato');
  var objTableAutent   = objIframeAutent.getElementById('tabelaAutent');
  var strJSONe         = '';
  var strJSONa         = '';
  var arrayObjExtrato  = new Array();
  var arrayObjAutent   = new Array();

  if (objTableExtrato != null && objTableExtrato.rows != undefined) {
    var ii = 0;
    for (i=0;i < objTableExtrato.rows.length-1; i++ ){
      eval ('var objLinhaExtrato = '+objIframeExtrato.getElementById("objJSON"+i).value);
      if(objTableExtrato.rows[(i+1)].className == 'desprocessado'){
        eval ('arrayObjExtrato['+ii+'] = '+objIframeExtrato.getElementById("objJSON"+i).value);
        ii++;
      }
    }
    strJSONe = JSON.stringify(arrayObjExtrato);
  }

  if (objTableAutent != null && objTableAutent.rows != undefined ) {
    var ii = 0;
    for (i=0;i < objTableAutent.rows.length-1; i++ ){
      eval ('var objLinhaAutent = '+objIframeAutent.getElementById("objJSON"+i).value);
      if(objTableAutent.rows[(i+1)].className == 'desprocessado'){
        eval ('arrayObjAutent['+ii+'] = '+objIframeAutent.getElementById("objJSON"+i).value);
        ii++;
      }
    }
    strJSONa = JSON.stringify(arrayObjAutent);
  }
  /*
  // esse dois alertas deve retornar dois arrays de obj json, algo tipo "[object object],[object object],[object object]" de acordo com a qtd marcada
  eval('var objteste = '+strJSONa);
  alert(objteste);

  eval('var objteste = '+strJSONe);
  alert(objteste);
*/

  strJSONa = encodeURIComponent(strJSONa);
  strJSONe = encodeURIComponent(strJSONe);

  js_processaConciliacao(strJSONe,strJSONa,'desprocessaritem',codigoconcilia);

  $('modo').value = 'desativado';
  $('confdesproc').disabled = true;
  $('desproc').value        = 'Ativar modo desprocessar itens';
  $('procconc').disabled    = false;
  $('salvar').disabled      = false;
  $('salvar').removeClassName("bloqueado");
  $('chkpendente').disabled = false;
  $('chkpreselecionado').disabled = false;
  $('chknormal').disabled = false;
  $('chkconciliado').disabled = false;

  /**
   * Data sendo menor ou nao a data da ultima conciliacao ao confirmar desprocessamento dos itens,
   * botao salvar e checkbox devem ficar ativos
   */
  lDataMenorUltimaConciliacao = false;
}

function js_ativarModo(){
  var modoatual = $('modo').value;

  if (modoatual == 'desativado') {
    alert('Modo desprocessar itens ativado ! \n Dois cliques sobre o item que deseja desprocessar ');
    $('confdesproc').disabled = false;
    $('desproc').value     = 'Ativar modo conciliação';
    //$('procconc').disabled = true;
    $('salvar').disabled   = true;
    $('salvar').addClassName("bloqueado");
    modoatual = 'ativado';
    $('chkpendente').checked  = false;
    $('chkpendente').disabled = true;
    js_escondeLinha($('chkpendente'),'pendente');
    $('chkpreselecionado').checked  = false;
    $('chkpreselecionado').disabled = true;
    js_escondeLinha($('chkpreselecionado'),'preselecionado');
    $('chknormal').checked  = false;
    $('chknormal').disabled = true;
    js_escondeLinha($('chknormal'),'normal');
    $('chkconciliado').disabled = true;

  }else{
    alert('Modo conciliar itens ativado ! ');
    $('confdesproc').disabled = true;
    $('desproc').value        = 'Ativar modo desprocessar itens';
   // $('procconc').disabled    = false;
    $('salvar').disabled      = false;
    $('salvar').removeClassName("bloqueado");
    modoatual = 'desativado';
    $('chkpendente').checked  = true;
    $('chkpendente').disabled = false;
    js_escondeLinha($('chkpendente'),'pendente');
    $('chkpreselecionado').checked  = true;
    $('chkpreselecionado').disabled = false;
    js_escondeLinha($('chkpreselecionado'),'preselecionado');
    $('chknormal').checked  = true;
    $('chknormal').disabled = false;
    js_escondeLinha($('chknormal'),'normal');
    $('chkconciliado').disabled = false;

    iframeExtrato.location.reload();
    iframeAutent.location.reload();

  }

  $('modo').value = modoatual;

}



function js_escondeLinha(obj,classe){

  var objIframeExtrato = iframeExtrato.document;
  var objIframeAutent  = iframeAutent.document;
  var objTableExtrato  = objIframeExtrato.getElementById('tabelaExtrato');
  var objTableAutent   = objIframeAutent.getElementById('tabelaAutent');

  if (objTableExtrato != null && objTableExtrato.rows != undefined ) {
    for (i=1;i < objTableExtrato.rows.length; i++ ){
      if(obj.checked){
        if(objTableExtrato.rows[i].className == classe){
          objTableExtrato.rows[i].style.display = '';
        }
      }else{
        if(objTableExtrato.rows[i].className == classe){
          objTableExtrato.rows[i].style.display = 'none';
        }
      }
    }
  }

  if (objTableAutent != null && objTableAutent.rows != undefined ) {
    for (i=1;i < objTableAutent.rows.length ; i++ ){
      if(obj.checked){
        if(objTableAutent.rows[i].className == classe ){
          objTableAutent.rows[i].style.display = '';
        }
      }else {
        if ( objTableAutent.rows[i].className == classe ){
          objTableAutent.rows[i].style.display = 'none';
        }
      }
    }
  }
}

function js_atualizar(){

  iframeExtrato.js_processaRequest("<?php echo $data?>","<?php echo $conta?>");
  iframeAutent.js_processaRequest("<?php echo $data?>","<?php echo $conta?>");

  $('chkconciliado').checked = true;
  $('chkpendente').checked = true;
  $('chkpreselecionado').checked = true;
  $('chknormal').checked = true;
  lBloqueProximo = false;
}

function js_verobn() {

  var objIframeAutent  = iframeAutent.document;
  var objTableAutent   = objIframeAutent.getElementById('tabelaAutent');
  var strJSONa         = '';
  var arrayObjAutent   = new Array();

  if (objTableAutent != null && objTableAutent.rows != undefined ) {

    for (i=0;i < objTableAutent.rows.length-1; i++ ) {
      eval ('objTMP = '+objIframeAutent.getElementById("objJSON"+i).value);
      marcado = objIframeAutent.getElementById('marcado'+i);
      arquivo = objIframeAutent.getElementById('arquivo'+i);
      autent = objIframeAutent.getElementById('autent'+i);
//      console.log('id: ' + i + ' - arquivo: ' + arquivo.innerHTML + ' - autent: ' + autent.innerHTML + ' - ' + marcado.checked + ' - ' + objTMP.classe + ' - size: ' + objTableAutent.rows.length + ' - codgera: ' + document.form1.e75_codgera.value);

//      console.log(arquivo.innerHTML);
//      console.log(document.form1.e75_codgera.value);

      if(objTMP.classe != 'conciliado' && arquivo.innerHTML.trim() == document.form1.e75_codgera.value.trim() ) {
        if(marcado.checked == false ) {
          eval ('arrayObjAutent['+i+'] = '+JSON.stringify(objTMP));
	  marcado.checked = true;
          js_somaAutenticacoesobn(marcado,(i));
	} else {
	  marcado.checked = false;
          js_somaAutenticacoesobn(marcado,(i));
	}
      }
    }
    strJSONa = JSON.stringify(arrayObjAutent);
  }

  strJSONa = encodeURIComponent(strJSONa);

}


    function js_somaAutenticacoesobn(obj,id) {

      var objIframeAutent  = iframeAutent.document;
      var objTableAutent   = objIframeAutent.getElementById('tabelaAutent');

      var linha = objIframeAutent.getElementById('tablinha'+id);
      var mostraMensagem = obj.checked;

      var valordebito  = objIframeAutent.getElementById('valdeb'+id).innerHTML;
      var valorcredito = objIframeAutent.getElementById('valcred'+id).innerHTML;

      var valdeb = valordebito.replace(/\./gi,"");
      valdeb = valdeb.replace(",",".");
      valdeb2 = new Number( valdeb );

      var valcred = valorcredito.replace(/\./gi,"");
      valcred = valcred.replace(",",".");
      valcred2 = new Number( valcred );

//      console.log( document.form1.totalautent.value );

      if (obj.checked) {

        var classeatual  = 'selecionado';

        js_somaobn( valdeb2,  document.form1.totalautent.value,'-' );
        js_somaobn( valcred2, document.form1.totalautent.value,'+' );

      } else {

        eval('var objJSON = '+objIframeAutent.getElementById('objJSON'+id).value);
        var classeatual = objJSON.classe;

        js_somaobn( valdeb2,  document.form1.totalautent.value,'+' );
        js_somaobn( valcred2, document.form1.totalautent.value,'-' );

      }

      objTableAutent.rows[(id+1)].className = classeatual;

////      js_comparaValores(mostraMensagem);

    }


    function js_somaobn(val,obj,operador) {

      if(typeof(obj.value) == 'string' && obj.value == '' ){
        var valantes = new Number('0.00');
      } else {
        var valantes = new Number( obj );
      }

//      console.log('somando ' + valantes + ' com ' + val + ' - operador: ' + operador);

      if ( operador == '+' ) {
        document.form1.totalautent.value = new String((valantes + val).toFixed(2));
      } else {
        document.form1.totalautent.value = new String((valantes - val).toFixed(2));
      }

//      eval ('obj.value = new String( ( valantes '+operador+' val ).toFixed(2) ) ');

    }

function js_selecao(){

  var objIframeExtrato = iframeExtrato.document;
  var objIframeAutent  = iframeAutent.document;
  var codigoconcilia   = $('concilia').value;
  var objTableExtrato  = objIframeExtrato.getElementById('tabelaExtrato');
  var objTableAutent   = objIframeAutent.getElementById('tabelaAutent');
  var strJSONe         = '';
  var strJSONa         = '';
  var arrayObjExtrato  = new Array();
  var arrayObjAutent   = new Array();

  if (objTableExtrato != null && objTableExtrato.rows != undefined ) {
    var ii = 0;
    for (i=0;i < objTableExtrato.rows.length-1; i++ ){
      marcado = objIframeExtrato.getElementById('marcado'+i);
      if(marcado.checked){
        eval ('objTMP = '+objIframeExtrato.getElementById("objJSON"+i).value);
        if(objTMP.classe != 'conciliado'){
          objTMP.historico = '';
          eval ('arrayObjExtrato['+ii+'] = '+JSON.stringify(objTMP));
          marcado.disabled = true;
          ii++;
        }
      }
    }
    strJSONe = JSON.stringify(arrayObjExtrato);
  }

  if (objTableAutent != null && objTableAutent.rows != undefined ) {

    var ii = 0;
    for (i=0;i < objTableAutent.rows.length-1; i++ ){
      marcado = objIframeAutent.getElementById('marcado'+i);
      if(marcado.checked){
        eval ('objTMP = '+objIframeAutent.getElementById("objJSON"+i).value);
        if(objTMP.classe != 'conciliado'){
          objTMP.credor = '';
          objTMP.detalhe = '';

          eval ('arrayObjAutent['+ii+'] = '+JSON.stringify(objTMP));
          marcado.disabled = true;
          ii++;
        }
      }
    }
    strJSONa = JSON.stringify(arrayObjAutent);
  }

  strJSONa = encodeURIComponent(strJSONa);
  strJSONe = encodeURIComponent(strJSONe);

  js_processaConciliacao(strJSONe,strJSONa,'manual',codigoconcilia);

}

/* funcao para salvar conciliacao ( pega os registro que nao estao conciliados e gera pendencia ) */
function js_salvarConciliacao(){

  var diferenca = new Number(document.form1.saldo_diferenca.value);
/*
  if( diferenca != 0 ) {
     alert('A conciliação não está fechada. Verifique a diferença.');
     return false;
  }
*/
  var oButtonSalvar = $('salvar');
  // oButtonSalvar.disabled  = true;
  oButtonSalvar.addClassName("bloqueado");

  $('proximo').disabled = true;
  $('proximomes').disabled = true;

  var confirmacao = confirm('Deseja realmente salvar essa conciliacao? \n Essa operacao vai gerar pendencia \n para todos os registros nao conciliados');

  if (!confirmacao) {

    oButtonSalvar.removeClassName("bloqueado");
    oButtonSalvar.disabled = false;
    return false;
  }

  js_divCarregando("Aguarde...", "msgBoxSalvarConciliacao");

  var objIframeExtrato = iframeExtrato.document;
  var objIframeAutent  = iframeAutent.document;
  var codigoconcilia   = $('concilia').value;
  var objTableExtrato  = objIframeExtrato.getElementById('tabelaExtrato');
  var objTableAutent   = objIframeAutent.getElementById('tabelaAutent');
  var strJSONe         = '';
  var strJSONa         = '';
  var arrayObjExtrato  = new Array();
  var arrayObjAutent   = new Array();

  if (objTableExtrato != null && objTableExtrato.rows != undefined) {

    var ii = 0;
    for (i=0;i < objTableExtrato.rows.length-1; i++ ){

      marcado = objIframeExtrato.getElementById('marcado'+i);

      var objTMP = JSON.parse(objIframeExtrato.getElementById("objJSON"+i).value);
//      eval ('objTMP = '+objIframeExtrato.getElementById("objJSON"+i).value);
      if(objTMP.classe != 'conciliado'){
        objTMP.historico = '';
//        eval ('arrayObjExtrato['+ii+'] = '+JSON.stringify(objTMP));
        arrayObjExtrato[ii] = objTMP;
        ii++;
        marcado.disabled = true;
      }

    }
    strJSONe = JSON.stringify(arrayObjExtrato);
  }

  if (objTableAutent != null && objTableAutent.rows != undefined ) {

    var ii = 0;
    for (i=0;i < objTableAutent.rows.length-1; i++ ){

      marcado = objIframeAutent.getElementById('marcado'+i);

      //eval ('objTMP = '+objIframeAutent.getElementById("objJSON"+i).value);
      var objTMP = JSON.parse(objIframeAutent.getElementById("objJSON"+i).value);
      objTMP.detalhe = tagString(objTMP.detalhe);
      if (objTMP.classe != 'conciliado'){
        objTMP.credor = '';
//        eval ('arrayObjAutent['+ii+'] = '+JSON.stringify(objTMP));
        arrayObjAutent[ii] = objTMP;
        ii++;
        marcado.disabled = true;
      }

    }
    strJSONa = JSON.stringify(arrayObjAutent);

  }

  strJSONa = encodeURIComponent(strJSONa);
  strJSONe = encodeURIComponent(strJSONe);

  js_removeObj("msgBoxSalvarConciliacao");

  js_processaConciliacao(strJSONe,strJSONa,'gerarpendencias',codigoconcilia);

}

function js_processaConciliacao(strJSONExtrato,strJSONAutent,solicitacao,concilia){

  js_divCarregando("Processando, aguarde ...", "msgBoxSalvarConciliacao");

  var pardata   = $('data').value;
  var parconta  = $('conta').value;
  var url       = 'cai4_processaconciliacao_manual.php';
  var parametro = 'strJSONExtrato='+strJSONExtrato.replace(/#/g,'')+'&strJSONAutent='+strJSONAutent.replace(/#/g,'')+'&solicitacao='+solicitacao+'&concilia='+concilia+'&data='+pardata+'&conta='+parconta;
  var objAjax   = new Ajax.Request (url,{
                                          method:'post',
                                          parameters:parametro,
                                          onComplete:( solicitacao == 'gerarpendencias' ? js_retornoConciliacaoSS : js_retornoConciliacao )
                                         });
  $('loading').innerHTML = ' <blink> <b> Aguarde Conciliando registros selecionados ... </b> </blink>';

}

function js_retornoConciliacao(resposta) {

  js_removeObj("msgBoxSalvarConciliacao");

  if(resposta.responseText.substr(0,1) == '1'){

    document.form1.totalextrato.value = '0.00';
    document.form1.totalautent.value  = '0.00';
    $('loading').innerHTML = '';
    js_atualizar();
  }else{
    $('loading').innerHTML = '';
    alert(resposta.responseText);
  }


  $('proximo').disabled = lBloqueProximo;
  $('proximomes').disabled = lBloqueProximo;
 // $('procconc').disabled = true;
  $('salvo').value       = true;
  $('salvar').disabled   = true;
//  $('salvar').addClassName("bloqueado");

  var conta   = document.getElementById('conta').value;
  var data    = document.getElementById('data').value;
  var codigo  = document.getElementById('concilia').value;

  document.location.href = 'cai4_concbanc001_manual.php?conta='+conta+'&data='+data+'&concilia='+codigo;

}

function js_retornoConciliacaoSS(resposta) {

  js_removeObj("msgBoxSalvarConciliacao");

  if(resposta.responseText.substr(0,1) == '1'){

    document.form1.totalextrato.value = '0.00';
    document.form1.totalautent.value  = '0.00';
    $('loading').innerHTML = '';
    js_atualizar();
  }else{
    $('loading').innerHTML = '';
    alert(resposta.responseText);
  }


  $('proximo').disabled = lBloqueProximo;
  $('proximomes').disabled = lBloqueProximo;
 // $('procconc').disabled = true;
  $('salvo').value       = true;
  $('salvar').disabled   = true;
//  $('salvar').addClassName("bloqueado");

}

function js_getProximaData(acao){

  var diferenca = new Number(document.form1.saldo_diferenca.value);

  if( diferenca != 0 ) {
     alert('A conciliação não está fechada. Verifique a diferença.');
     return false;
  }

  $("proximo").disabled = false;
  $("proximomes").disabled = false;
  js_divCarregando("Aguarde, carregando informações...", "msgBoxDatas");

  var url       = 'cai4_carregadatascorrente_manual.php';
  var sData      = document.getElementById('data').value;
  var parametro = 'conta='+document.getElementById('conta').value+'&sData='+sData+'&diferenca='+diferenca+'&acao='+acao;
  var objAjax   = new Ajax.Request (url,{method:'post',parameters:parametro, onComplete:js_setProximadata});

}

function js_setProximadata(resposta){

  js_removeObj("msgBoxDatas");

  var conta   = document.getElementById('conta').value;
  var obj     = document.getElementById('data');
  var str     = resposta.responseText;
  var linhas  = str.split("|");
  var diferenca = new Number(document.form1.saldo_diferenca.value);
  if(linhas[0] != '') {
    colunas = linhas[0].split(";");
    if (  colunas[0] == '1' ){
      alert(colunas[1]);
    }else{
    obj.value = colunas[0];
    if (colunas[1] != "") {
      var confirmacao = confirm('Deseja abrir proxima conciliacao para conta: '+conta+' e data: '+colunas[1]+' ?');
      if (confirmacao){

        js_divCarregando("Processando, Aguarde ...", "msgBoxProximaData");

        var url       = 'cai4_abreconciliacao_manual.php';
        var parametro = 'data='+obj.value+'&conta='+conta;
        var objAjax   = new Ajax.Request (url,{method:'post',parameters:parametro, onComplete:js_passaProxima});
      } else {
        $("proximo").disabled = false;
        document.getElementById('data').value = document.getElementById('data_ant').value;
      }
    }else{
      alert("Não existem mais movimentos a conciliar.");
    }
    }
  }
}

function js_passaProxima(resposta){

  js_removeObj("msgBoxProximaData");

  var conta = document.getElementById('conta').value;
  var data  = document.getElementById('data').value;
  var retorno = resposta.responseText.split('|||');



  if (retorno[0] == '1') {

	  document.location.href = 'cai4_concbanc001_manual.php?conta=<?php echo $aReduzidos[0]?>&data='+data+'&concilia='+retorno[2];
  }else{
     alert(retorno[1]);
     $('salvar').disabled  = false;
     $('salvar').removeClassName("bloqueado");
  }
}

function js_mudaCSSClass(classOrigem,classNova){

  var objIframeExtrato = iframeExtrato.document;
  var objIframeAutent  = iframeAutent.document;
  // muda a classe dos selecionado para conciliados
  var objColectionExtrato = objIframeExtrato.getElementsByClassName(classOrigem);
  for(i=0;i<objColectionExtrato.length;i++  ){
    objColectionExtrato[i].className = classNova;
  }

  var objColectionAutent = objIframeAutent.getElementsByClassName(classOrigem);
  for(i=0;i<objColectionAutent.length;i++ ){
    objColectionAutent[i].className = classNova;
  }
}


function js_comparaValores(mostraMensagem){


  // colocado para nao ver valores na conciliacao manual
  return true;

  var valextrato = document.form1.totalextrato.value;
  var valautent  = document.form1.totalautent.value;
  if (valextrato == '') {
    valextrato = '0';
  }
  if (valautent == '') {
    valautent = '0';
  }
  var valor      = new Number(valautent);

  valextrato     = js_formatar(valextrato,'f', 2);
  valautent      = js_formatar(valautent ,'f', 2);

  if (valextrato == valautent && mostraMensagem == true ){
   // $('procconc').disabled = false;
    //var conf = confirm('Valores fechados \n Conciliar os registros selecionados ?');
   // if (conf) {
      js_selecao();
   // }
  }else{
   // $('procconc').disabled = true;

  }
}

function js_ajaxRetorno(resposta){
  alert(new String(resposta.responseText));
}

function js_hideFrame(btnHabilitar,btnDesabilitar,id,mostrar){
  btnHabilitar.disabled = false;
  btnDesabilitar.disabled = true;
  if (mostrar) {
    document.getElementById(id).style.display = 'none';
  }else{
    document.getElementById(id).style.display = '';
  }
}

function getElementbyClass(rootobj, classname){
  var temparray = new Array()
  var inc = 0
  var rootlength = rootobj.length
  for (i = 0; i < rootlength; i++){
    if (rootobj[i].className == classname)
      temparray[inc++]=rootobj[i]
  }
  return temparray
}

function js_digitasaldoextratomanual() {

  var sUrl = "cai2_digitasaldoextratomanual.php";
  sUrl    += "?concilia="+$F('concilia') + "&conta="+$F('conta') + "&data="+$F('data');

  js_OpenJanelaIframe('', 'db_iframe_digitasaldoextratomanual',
	  sUrl, 'Digitação saldo extrato manual', true);
}

function js_relatorio() {

  var sUrl = "cai2_relconciliacaobancaria001_manual.php";
  sUrl    += "?concilia="+$F('concilia');


  js_OpenJanelaIframe('', 'db_iframe_relatorio',
                      sUrl, 'Relatório Conciliacao Bancária', true);
}

function js_zeracampos(){
  document.form1.totalextrato.value = '0.00';
  document.form1.totalautent.value  = '0.00';
}

function js_escape(str,arr,escapar){
  var strRetorno = '';
  return str;

}

js_zeracampos();

//alert(document.getElementById('saldo_extrato').innerHTML);

</script>
