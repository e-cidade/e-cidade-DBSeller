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
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_corrente_classe.php"));
require_once(modification("classes/db_concilia_classe.php"));

$clcorrente = new cl_corrente;
$clconcilia = new cl_concilia;
$objJSON    = new Services_JSON();

db_postmemory($_POST);


include("cai4_abreconciliacao_processa.php");

$iAnousu = db_getsession("DB_anousu");


$sqlMenorDataCaixa  = " select min(menordatacaixa) as menordatacaixa ";
$sqlMenorDataCaixa .= "   from (  select min(k68_data) as menordatacaixa from concilia ";
$sqlMenorDataCaixa .= "	            union ";
$sqlMenorDataCaixa .= "	  	 	  select min(k89_data) as menordatacaixa from conciliapendcorrente ) as x ";
$rsMenorDataCaixa   = $clconcilia->sql_record($sqlMenorDataCaixa);
if($clconcilia->numrows > 0){
  db_fieldsmemory($rsMenorDataCaixa,0);
}else{
  $menordatacaixa = '2006-01-01';
}

$sqlAutentica  = " select caixa,                                  ";
$sqlAutentica .= "        autent,                                 ";
$sqlAutentica .= "        arquivo,                                ";
$sqlAutentica .= "        data,                                   ";
$sqlAutentica .= "        sum(valor_debito) as valor_debito,      ";
$sqlAutentica .= "        sum(valor_credito) as valor_credito,    ";
$sqlAutentica .= "        cheque,                                 ";
$sqlAutentica .= "        credor,                                 ";
$sqlAutentica .= "        min(detalhe) as detalhe,                ";
$sqlAutentica .= "        classe,                                 ";
$sqlAutentica .= "        itemconciliacao,                        ";
$sqlAutentica .= "        erro,                                   ";
$sqlAutentica .= "        justificativa                           ";
$sqlAutentica .= "   from ( select distinct ";
$sqlAutentica .= "                 caixa as caixa, ";
$sqlAutentica .= "                 autent as autent, ";
$sqlAutentica .= "                 arquivo as arquivo, ";
$sqlAutentica .= "                 data as data, ";
$sqlAutentica .= "                 valor_debito as valor_debito, ";
$sqlAutentica .= "                 valor_credito as valor_credito, ";
$sqlAutentica .= "                 receita as receita, ";
$sqlAutentica .= "                 cheque as cheque, ";
$sqlAutentica .= "                 credor as credor, ";
$sqlAutentica .= "                 detalhe as detalhe, ";
$sqlAutentica .= "                 case ";
$sqlAutentica .= "                   when x.classe = 'conciliado' then 'conciliado' ";
$sqlAutentica .= "                   when ( k86_data is not null and k86_documento is not null ) then 'preselecionado' ";
$sqlAutentica .= "                   else x.classe ";
$sqlAutentica .= "                 end as classe, ";
$sqlAutentica .= "                 itemconciliacao, ";
$sqlAutentica .= "                 justificativa, ";
$sqlAutentica .= "                 erro as erro ";
$sqlAutentica .= "            from ( ";




// -------------------------------------------------------------------------- pendentes
  $sqlAutentica .= "                   select distinct ";
  $sqlAutentica .= "                          ricaixa           as caixa, ";
  $sqlAutentica .= "                          riautent          as autent, ";
  $sqlAutentica .= "                          ( select e75_codgera
                                                  from conlancamcorrente
                                            inner join corlanc on c86_id = k12_id
                                                              and c86_data = k12_data
                                                              and c86_autent = k12_autent
                                            inner join conlancamslip on c84_slip = k12_codigo
                                                                    and c84_conlancam = c86_conlancam
                                            inner join empageslip on e89_codigo = k12_codigo
                                            inner join empagedadosretmov on e76_codmov = e89_codmov
                                                                        and e76_processado = true
                                            inner join empagedadosret on e75_codret = e76_codret
                                            inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret
                                                                                  and e02_empagedadosretmov = e76_codmov
                                                                                  and e02_errobanco in (2, 269)
                                                 where corlanc.k12_data = ridata
                                                   and corlanc.k12_id = ricaixa
                                                   and corlanc.k12_autent = riautent
                                    union

                                       select e75_codgera
                                         from conlancamcorgrupocorrente
                                   inner join corgrupocorrente on k105_sequencial = c23_corgrupocorrente
                                   inner join corempagemov  on k12_id = k105_id
                                                           and k12_data = k105_data
                                                           and k12_autent = k105_autent
                                   inner join empagedadosretmov on e76_codmov = k12_codmov
                                                               and e76_processado = true
                                   inner join empagedadosret on e75_codret = e76_codret
                                   inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret
                                                                         and e02_empagedadosretmov = e76_codmov
                                                                         and e02_errobanco in ( 2, 269)
                                        where corempagemov.k12_data = ridata
                                          and corempagemov.k12_id = ricaixa
                                          and corempagemov.k12_autent = riautent
                                          ) as arquivo, ";

  $sqlAutentica .= "                          ridata            as data, ";
  $sqlAutentica .= "                          rnvalordebito     as valor_debito, ";
  $sqlAutentica .= "                          rivalorcredito    as valor_credito, ";
  $sqlAutentica .= "                          rireceita         as receita, ";
  $sqlAutentica .= "                          richeque          as cheque, ";
  $sqlAutentica .= "                          rtcredor          as credor, ";
  $sqlAutentica .= "                          rtdetalhe         as detalhe, ";
  $sqlAutentica .= "                          k89_justificativa as justificativa, ";
  $sqlAutentica .= "                          'pendente'        as classe,";
  $sqlAutentica .= "                          0                 as itemconciliacao, ";
  $sqlAutentica .= "                          rberro            as erro ";
  $sqlAutentica .= "                     from conciliapendcorrente ";
  $sqlAutentica .= "                          inner join concilia on k68_sequencial = k89_concilia ";
  $sqlAutentica .= "                          inner join ( select * from fc_extratocaixa(" . db_getsession('DB_instit') . ",$conta,'" . $menordatacaixa . "','" . $data . "',false ) ) as x ";
  $sqlAutentica .= "                                                          on ricaixa  = k89_id ";
  $sqlAutentica .= "                                                         and riautent = k89_autent ";
  $sqlAutentica .= "                                                         and ridata   = k89_data ";
  $sqlAutentica .= "                           left join conciliacor          on ricaixa  = k84_id ";
  $sqlAutentica .= "                                                         and riautent = k84_autent ";
  $sqlAutentica .= "                                                         and ridata   = k84_data ";
  $sqlAutentica .= "                           left join conciliaitem         on k83_sequencial = k84_conciliaitem ";
  $sqlAutentica .= "                                                         and k83_concilia = (select k68_sequencial ";
  $sqlAutentica .= "                                                                               from concilia  ";
  $sqlAutentica .= "                                                                              where k68_contabancaria = {$conta} ";
  $sqlAutentica .= "                                                                                and k68_data = '" . $data . "' ) ";
  $sqlAutentica .= "                     where ( k83_sequencial is null ) ";
  $sqlAutentica .= "                       and k68_sequencial = " . $concilia;

 //- --------------------------------------  FINAL DOS PENDENTES


  $sqlAutentica .= "                     union all ";


//------------------------------------------- conciliados



/**
 * aqui , devido o sistema buscar registros muito antigo para conciliados, exemplo a partir de 2007 resolvemos buscar
 * somente um dia de conciliados , quando a query abaixo retornar registro para a conta.
 * essa regra é a mesma da implantação, inclui consilia, basicamente
 */
/*
 Comentado trecho de código pois utilizando a data da conciliacao registros de datas anterios conciliados
 nao estavam sendo mostrados.

 Exemplo:
 Data do registro: 07/12/2021
 Data da conciliacao: 01/01/2022

 A data passada para buscar as movimentacoes do extrato fica 01/01/2022 até a data da da conciliacao atual.
 Nao retornando o movimento do dia 07/12/2021.
$sql = "
          select k68_conciliastatus,
                 k95_descr,
                 k68_contabancaria,
                 max(k68_data) as k68_data,
                 c56_reduz
          from concilia
          join conplanocontabancaria  on k68_contabancaria = c56_contabancaria
          join conciliastatus on (k68_conciliastatus) = (k95_sequencial)
          where c56_anousu = {$iAnousu}
            and k68_contabancaria = {$conta}
            and k68_conciliastatus = 2
          group by k68_conciliastatus,
               k95_descr,
               k68_contabancaria,
               c56_reduz
          order by k68_data
";
$rs = db_query($sql);
$dataConciliados = $menordatacaixa;
if (pg_numrows($rs) > 0) {
    $dataConciliados = db_utils::fieldsMemory($rs, 0)->k68_data;
}
*/
$dataConciliados = $menordatacaixa;

$sqlAutentica .= "                    select distinct ";
$sqlAutentica .= "                           ricaixa   as caixa, ";
$sqlAutentica .= "                           riautent  as autent, ";
$sqlAutentica .= "                           (                    ";
$sqlAutentica .= "                             select e75_codgera ";
$sqlAutentica .= "                               from conlancamcorrente ";
$sqlAutentica .= "                         inner join corlanc on c86_id = k12_id ";
$sqlAutentica .= "                                           and c86_data = k12_data ";
$sqlAutentica .= "                                           and c86_autent = k12_autent ";
$sqlAutentica .= "                         inner join conlancamslip on c84_slip = k12_codigo ";
$sqlAutentica .= "                                                 and c84_conlancam = c86_conlancam ";
$sqlAutentica .= "                         inner join empageslip on e89_codigo = k12_codigo ";
$sqlAutentica .= "                         inner join empagedadosretmov on e76_codmov = e89_codmov ";
$sqlAutentica .= "                                                     and e76_processado = true ";
$sqlAutentica .= "                         inner join empagedadosret on e75_codret = e76_codret ";
$sqlAutentica .= "                         inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret ";
$sqlAutentica .= "                                                               and e02_empagedadosretmov = e76_codmov ";
$sqlAutentica .= "                                                               and e02_errobanco in ( 2, 269 ) ";
$sqlAutentica .= "                              where corlanc.k12_data = ridata ";
$sqlAutentica .= "                                and corlanc.k12_id = ricaixa ";
$sqlAutentica .= "                                and corlanc.k12_autent = riautent ";
$sqlAutentica .= "                  union ";
$sqlAutentica .= "                             select e75_codgera ";
$sqlAutentica .= "                               from conlancamcorgrupocorrente ";
$sqlAutentica .= "                         inner join corgrupocorrente on k105_sequencial = c23_corgrupocorrente ";
$sqlAutentica .= "                         inner join corempagemov  on k12_id = k105_id ";
$sqlAutentica .= "                                                 and k12_data = k105_data ";
$sqlAutentica .= "                                                 and k12_autent = k105_autent ";
$sqlAutentica .= "                         inner join empagedadosretmov on e76_codmov = k12_codmov ";
$sqlAutentica .= "                                                     and e76_processado = true ";
$sqlAutentica .= "                         inner join empagedadosret on e75_codret = e76_codret ";
$sqlAutentica .= "                         inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret ";
$sqlAutentica .= "                                                               and e02_empagedadosretmov = e76_codmov ";
$sqlAutentica .= "                                                               and e02_errobanco in (2, 269) ";
$sqlAutentica .= "                              where corempagemov.k12_data = ridata ";
$sqlAutentica .= "                                and corempagemov.k12_id = ricaixa ";
$sqlAutentica .= "                                and corempagemov.k12_autent = riautent ";

$sqlAutentica .= "                           ) as arquivo, ";

$sqlAutentica .= "                           ridata         as data, ";
$sqlAutentica .= "                           rnvalordebito  as valor_debito, ";
$sqlAutentica .= "                           rivalorcredito as valor_credito, ";
$sqlAutentica .= "                           rireceita      as receita, ";
$sqlAutentica .= "                           richeque       as cheque, ";
$sqlAutentica .= "                           rtcredor       as credor, ";
$sqlAutentica .= "                           rtdetalhe      as detalhe, ";
$sqlAutentica .= "                           ''             as justificativa, ";
$sqlAutentica .= "                           'conciliado'   as classe, ";
$sqlAutentica .= "                           k83_sequencial as itemconciliacao, ";
$sqlAutentica .= "                           rberro         as erro ";
$sqlAutentica .= "                      from conciliacor ";
$sqlAutentica .= "                           inner join conciliaitem on k83_sequencial = k84_conciliaitem ";
$sqlAutentica .= "                           inner join concilia     on k83_concilia   = k68_sequencial ";
$sqlAutentica .= "                           inner join fc_extratocaixa(".db_getsession('DB_instit').",$conta,'".$dataConciliados."','".$data."',false ) ";
$sqlAutentica .= "                                                   on k84_id     = ricaixa ";
$sqlAutentica .= "                                                  and k84_autent = riautent ";
$sqlAutentica .= "                                                  and k84_data   = ridata ";
$sqlAutentica .= "                     where k68_sequencial = {$concilia} ";

$sqlAutentica .= "                     union all ";

// -------------------------- final dos CONCILIADOS




// ---------------------------------------------   registros normais



$sqlAutentica .= "                    select distinct ";
$sqlAutentica .= "                           ricaixa        as caixa, ";
$sqlAutentica .= "                           riautent       as autent, ";
$sqlAutentica .= "                           ( select e75_codgera
                                                 from conlancamcorrente
                                           inner join corlanc on c86_id = k12_id
                                                                   and c86_data = k12_data
                                                                   and c86_autent = k12_autent
                                            inner join conlancamslip on c84_slip = k12_codigo
                                                                    and c84_conlancam = c86_conlancam
                                            inner join empageslip on e89_codigo = k12_codigo
                                            inner join empagedadosretmov on e76_codmov = e89_codmov
                                                                        and e76_processado = true
                                            inner join empagedadosret on e75_codret = e76_codret
                                            inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret
                                                                                  and e02_empagedadosretmov = e76_codmov
                                                                                  and e02_errobanco in ( 2, 269)
                                                  where corlanc.k12_data = ridata
                                                    and corlanc.k12_id = ricaixa
                                                    and corlanc.k12_autent = riautent
                                  union
                                    select e75_codgera
                                      from conlancamcorgrupocorrente
                                inner join corgrupocorrente on k105_sequencial = c23_corgrupocorrente
                                inner join corempagemov  on k12_id = k105_id
                                                        and k12_data = k105_data
                                                        and k12_autent = k105_autent
                                inner join empagedadosretmov on e76_codmov = k12_codmov
                                                            and e76_processado = true
                                inner join empagedadosret on e75_codret = e76_codret
                                inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret
                                                                      and e02_empagedadosretmov = e76_codmov
                                                                      and e02_errobanco in ( 2, 269)
                                     where corempagemov.k12_data = ridata
                                       and corempagemov.k12_id = ricaixa
                                       and corempagemov.k12_autent = riautent
                                       ) as arquivo, ";

$sqlAutentica .= "	 		                     ridata         as data, ";
$sqlAutentica .= "  			                 rnvalordebito  as valor_debito, ";
$sqlAutentica .= "  			                 rivalorcredito as valor_credito, ";
$sqlAutentica .= "			                     rireceita      as receita, ";
$sqlAutentica .= "			                     richeque       as cheque, ";
$sqlAutentica .= "			                     rtcredor       as credor, ";
$sqlAutentica .= "			                     rtdetalhe      as detalhe, ";
$sqlAutentica .= "                           ''             as justificativa, ";
$sqlAutentica .= "                           'normal'       as classe, ";
$sqlAutentica .= "                           0              as itemconciliacao, ";
$sqlAutentica .= "			                     rberro         as erro ";
$sqlAutentica .= "                      from fc_extratocaixa(".db_getsession('DB_instit').",$conta,'".$data."','".$data."',false ) ";
$sqlAutentica .= "                           left join conciliacor          on ricaixa    = k84_id       ";
$sqlAutentica .= "                                                         and riautent   = k84_autent   ";
$sqlAutentica .= "                                                         and ridata     = k84_data     ";
$sqlAutentica .= "                           left join conciliaitem         on k83_sequencial = k84_conciliaitem ";
$sqlAutentica .= "                                                         and k83_concilia = (select k68_sequencial ";
$sqlAutentica .= "                                                                               from concilia  ";
$sqlAutentica .= "                                                                              where k68_contabancaria = {$conta} ";
$sqlAutentica .= "                                                                                and k68_data = '".$data."' ) ";
$sqlAutentica .= "                           left join conciliapendcorrente on k89_id     = ricaixa    ";
$sqlAutentica .= "                                                         and k89_autent = riautent   ";
$sqlAutentica .= "			                                                   and k89_data   = ridata     ";
$sqlAutentica .= "                                                         and k89_concilia = (select k68_sequencial ";
$sqlAutentica .= "                                                                               from concilia  ";
$sqlAutentica .= "                                                                              where k68_contabancaria = {$conta} ";
$sqlAutentica .= "                                                                                and k68_data = '".$data."' ) ";
$sqlAutentica .= "                     where ( k89_id is null and k89_autent is null and k89_data is null ) ";
$sqlAutentica .= "                       and ( k83_sequencial is null ) ";
$sqlAutentica .= "                       and not exists (select 1  ";
$sqlAutentica .= "                                         from conciliacor ";
$sqlAutentica .= "                                         inner join conciliaitem  on k83_sequencial    = k84_conciliaitem ";
$sqlAutentica .= "                                         inner join concilia      on k68_sequencial    = k83_concilia ";
$sqlAutentica .= "                                                                and k68_contabancaria = {$conta} ";
$sqlAutentica .= "                                                                and k68_data          = '".$data."' ";
$sqlAutentica .= "                                                              where k84_id     = ricaixa ";
$sqlAutentica .= "                                                                and k84_autent = riautent ";
$sqlAutentica .= "                                                                and k84_data   = ridata ) ";


// ---------------------------------  FINAL DOS NORMAIS



$sqlAutentica .= "                 ) as x ";
$sqlAutentica .= "                 left join extratolinha         on lpad(trim(x.cheque::varchar),20,'0') = lpad(trim(k86_documento),20,'0') ";
$sqlAutentica .= "                                               and k86_contabancaria = $conta ";
$sqlAutentica .= "                                               and (k86_data = x.data or k86_data <= '".$data."') ";
$sqlAutentica .= "                                               and x.cheque <> 0 ";
$sqlAutentica .= "                                               and k86_documento <> '0' ";

$sqlAutentica .= "        ) as x ";
$sqlAutentica .= " where not exists (select 1
                                       from corgrupocorrente
                                      where k105_autent = autent
                                        and k105_id     = caixa
					and k105_data   = data
                                        and (  ( k105_corgrupotipo in (2,3,5,6) and extract(year from k105_data) <= 2012 )

                                        or ( k105_corgrupotipo in (2,3) ) )

                                         )  ";
$sqlAutentica .= "  group by caixa, autent, arquivo, data, cheque, credor, classe, itemconciliacao, erro, justificativa ";
$sqlAutentica .= "  order by data, autent ";

//echo "<br><br>$sqlAutentica <br>"; die();

$rsAutentica   = $clcorrente->sql_record($sqlAutentica);
$intNumrows    = $clcorrente->numrows;

if ($intNumrows > 0){

  $arrayObj = array();
  for($i = 0; $i < $intNumrows; $i++ ) {

   $varp =  db_utils::fieldsMemory($rsAutentica,$i);

   // adicionado replace pois tem resumo de empenho com |||  tipo balblabl V||| em vz de VIII
   $varp->detalhe = str_replace(["|||"], ["III"], $varp->detalhe);

   $ctPagadora = "-";
   $detalhes = explode("#",  $varp->detalhe);
   $detalhesOp = explode("-", $detalhes[0]);

   /**
    * busca a conta pagadora - reduzido da OP
    */
   if ($detalhesOp[0] == "OP") {

       $codOrd = $detalhesOp[1];
       $sql = "
              SELECT e83_conta
                FROM empagemov
                JOIN empord ON empord.e82_codmov = empagemov.e81_codmov
                JOIN empagepag ON empagepag.e85_codmov = empagemov.e81_codmov
                JOIN empagetipo ON empagetipo.e83_codtipo = empagepag.e85_codtipo
               WHERE e82_codord = {$codOrd}
            ";

       $rsConta = db_query($sql);

       if (pg_num_rows($rsConta) > 0) {
         $ctPagadora = db_utils::fieldsMemory($rsConta, 0)->e83_conta;
       }
   }


   $arrayObj[] = new Autenticacoes($i,
                                   'Concolidado',
                                   $varp->cheque,
				                   str_replace("'","",$varp->detalhe),
                                   $varp->caixa,
                                   $varp->autent,
                                   $varp->arquivo,
                                   db_formatar($varp->data,'d'),
                                   db_formatar($varp->valor_debito,'f'),
                                   db_formatar($varp->valor_credito,'f'),
                                   str_replace("'","",$varp->credor),
                                   $varp->classe,
                                   $varp->itemconciliacao,
                                   $varp->justificativa,
                                   $ctPagadora
                                  );
	}

    //dd($arrayObj);
	// Vai mostrar o codigo em JSON
  $retornoJSON = $objJSON->encode($arrayObj);
	echo '1|||'.$objJSON->encode($arrayObj);//$retornoJSON;
}else{
  echo '2|||'.$objJSON->encode(array());
}

class Autenticacoes {

  // Propriedades
  var $id               = '';
  var $status           = '';
  var $numeroCheque     = '';
  var $detalhe          = '';
  var $caixa            = '';
  var $autent           = '';
  var $arquivo          = '';
  var $data             = '';
  var $valorDebito      = '';
  var $valorCredito     = '';
  var $credor           = '';
  var $classe           = '';
  var $itemconciliacao  = '';
  var $justificativa    = '';
  var $ctPagadora       = '';

  // Construtor   
  public function Autenticacoes ($pid=null,$pstatus=null,$pnumeroCheque=null,$pdetalhe=null,$pcaixa=null,$pautent=null,$parquivo=null,$pdata=null,$pvalorDebito=null,$pvalorCredito=null,$pcredor=null,$pclasse='normal',$pitemconciliacao=null, $sJustificativa = '', $ctPagadora = null){

  	$this->id               = $pid;
    $this->status           = $pstatus;
    $this->numeroCheque     = urlencode($pnumeroCheque);
    $this->detalhe          = utf8_encode(str_replace("\r","",str_replace("\n","",$pdetalhe)));
    $this->caixa            = $pcaixa;
    $this->autent           = $pautent;
    $this->arquivo          = $parquivo;
    $this->data             = $pdata;
    $this->valorDebito      = $pvalorDebito;
    $this->valorCredito     = $pvalorCredito;
    $this->credor           = urlencode($pcredor);
    $this->classe           = $pclasse;
    $this->itemconciliacao  = $pitemconciliacao;
    $this->justificativa    = rawurlencode($sJustificativa);
    $this->ctPagadora       = $ctPagadora;
  }
}
