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

require_once modification('fpdf151/pdf.php');
require_once modification('libs/db_utils.php');

try {
    $instituicaoSessao = InstituicaoRepository::getInstituicaoSessao();
    $anoSessao = db_getsession('DB_anousu');

    parse_str($_SERVER['QUERY_STRING'], $queryString);

    foreach ($queryString as $key => $value) {
        ${$key} = $value;
    }

    $head1 = 'EXTRATO BANCÁRIO ' . ($imprime_analitico == 'a' ? 'ANALÍTICO' : 'SINTÉTICO');
    $head3 = 'PERÍODO : ' . db_formatar(@$datai, 'd') . ' A ' . db_formatar(@$dataf, 'd');

    if ($somente_contas_bancarias == 's') {
        $head4 = 'SOMENTE CONTAS BANCÁRIAS';
    }

    if ($agrupapor == 2) {
        $head5 = 'AGRUPAMENTO: PELA CONTA DE RECEITA';
    }
    if ($agrupapor == 3) {
        $head5 = 'AGRUPAMENTO: PELOS CÓDIGOS DE EMPENHO E RECEITA';
    }
    if ($receitaspor == 1) {
        $head6 = 'BAIXA BANCÁRIA: NÃO AGRUPADO PELA CLASSIFICAÇÃO';
    }
    if ($receitaspor == 2) {
        $head6 = 'BAIXA BANCÁRIA: AGRUPADO PELA CLASSIFICAÇÃO';
    }

    /// CONTAS MOVIMENTO
    $sql = "select   k13_reduz,
                     k13_descr,
		     k13_dtimplantacao,
                     c60_estrut,
                     c60_codsis,
	                   c63_conta,
	                   substr(fc_saltessaldo,2,13)::float8 as anterior,
	                   substr(fc_saltessaldo,15,13)::float8 as debitado ,
	                   substr(fc_saltessaldo,28,13)::float8 as creditado,
	                   substr(fc_saltessaldo,41,13)::float8 as atual
            	from (
 	                  select k13_reduz,
 	                         k13_descr,
				 k13_dtimplantacao,
	                         c60_estrut,
		                       c60_codsis,
		                       c63_conta,
	                         fc_saltessaldo(k13_reduz,'" . $datai . "','" . $dataf . "',null, {$instituicaoSessao->getCodigo()})
	                  from   saltes
	                         inner join conplanoexe   on k13_reduz = c62_reduz
		                                              and c62_anousu = {$anoSessao}
		                     inner join conplanoreduz on c61_anousu=c62_anousu and c61_reduz = c62_reduz and c61_instit = {$instituicaoSessao->getCodigo()}
	                         inner join conplano      on c60_codcon = c61_codcon and c60_anousu=c61_anousu
	                         left  join conplanoconta on c60_codcon = c63_codcon
	                                                 and c63_anousu = c60_anousu
	                                                 and c63_reduz  = c61_reduz ";

    if ($conta != '') {
        $sql .= "where c61_reduz in {$conta} ";
    }

    if ($conta != '' && $somente_contas_bancarias == 's') {
        $sql .= ' and c60_codsis = 6 ';
    } else {
        if ($somente_contas_bancarias == 's') {
            $sql .= 'where c60_codsis = 6 ';
        }
    }
    $sql .= '  ) as x ';
    $sql .= ' order by substr(k13_descr,1,3),k13_reduz ';

    //echo $sql; die();
    $resultcontasmovimento = db_query($sql);

    if (pg_num_rows($resultcontasmovimento) == 0) {
        throw new Exception('Não existem dados neste periodo.');
    }

    $saldo_dia_credito = 0;
    $saldo_dia_debito = 0;

    $aContas = array();

    $numrows = pg_num_rows($resultcontasmovimento);
    for ($linha = 0; $linha < $numrows; $linha++) {
        db_fieldsmemory($resultcontasmovimento, $linha);

        if ($somente_contas_com_movimento == 's' && ($debitado == 0 && $creditado == 0)) {
            continue;
        }

        if (!array_key_exists($k13_reduz, $aContas)) {
            $aContas[$k13_reduz] = new stdClass();
        }

        $aContas[$k13_reduz]->k13_reduz = $k13_reduz;
        $aContas[$k13_reduz]->k13_descr = $k13_descr;
        $aContas[$k13_reduz]->k13_dtimplantacao = $k13_dtimplantacao;

        // para contas bancárias, saldo positivo = debito, negativos indica debito
        if ($anterior > 0) {
            $aContas[$k13_reduz]->debito = $anterior;
            $aContas[$k13_reduz]->credito = 0;
        } else {
            $aContas[$k13_reduz]->credito = $anterior;
            $aContas[$k13_reduz]->debito = 0;
        }
        // ****************** ANALITICO e sintetico****************


        // *********************  EMPENHO ***************************



        $sqlempenho = "
  /* empenhos- despesa orçamentaria */
  /*   EMPENHO */

  select
        corrente.k12_id as caixa,
        corrente.k12_data as data,
		    0 as valor_debito,
		    corrente.k12_valor as valor_credito,
	      'Pgto. Emp. '||e60_codemp||'/'||e60_anousu::text||' OP: '||coremp.k12_codord::text as tipo_movimentacao,
        e60_codemp||'/'||e60_anousu::text as codigo,
        'empenho'::text as tipo,
        0 as receita,
		    null::text as receita_descr,
		    corhist.k12_histcor::text as historico,
		    coremp.k12_cheque::text as cheque,
		    null::text as contrapartida,
		    coremp.k12_codord as ordem,
		    z01_nome::text as credor,
		    z01_numcgm::text as numcgm,
		    k12_codautent,
		    k105_corgrupotipo,
		    '' as codret,
			  '' as dtretorno,
			  '' as arqret,
			 	'' as dtarquivo
 from corrente
      inner join coremp on coremp.k12_id = corrente.k12_id and coremp.k12_data   = corrente.k12_data
                                                           and coremp.k12_autent = corrente.k12_autent
      inner join empempenho on e60_numemp = coremp.k12_empen
      inner join cgm on z01_numcgm = e60_numcgm
		    /*
		      se habilitar o left abaixo e o empenho tiver mais de um cheque os registros ficam duplicados
		      left join empord on e82_codord = coremp.k12_codord
		     left join empageconfche on e91_codcheque = e82_codmov
		   */
	    left join corhist on  corhist.k12_id     = corrente.k12_id    and corhist.k12_data   = corrente.k12_data  and
					                                                              corhist.k12_autent = corrente.k12_autent
      left join corautent	on corautent.k12_id     = corrente.k12_id   and corautent.k12_data   = corrente.k12_data
							                                                        and corautent.k12_autent = corrente.k12_autent
      left join corgrupocorrente on corrente.k12_data = k105_data and corrente.k12_id = k105_id and corrente.k12_autent = k105_autent
 where corrente.k12_conta = {$k13_reduz}  and corrente.k12_data between '{$datai}'
                                        and '{$dataf}'
                                        and corrente.k12_instit = {$instituicaoSessao->getCodigo()}


";

        $sqlanalitico = "
  /* RECIBO */

 select
       caixa,
		   data,
		   valor_debito,
		   valor_credito,
		   tipo_movimentacao,
		   codigo,
		   tipo,
		   receita,
       receita_descr,
		   historico,
		   cheque,
		   contrapartida,
       ordem,
		   credor,
		   ''::text as numcgm,
		   k12_codautent,
		   0 as k105_corgrupotipo,
		   '' as codret,
			 '' as dtretorno,
			 '' as arqret,
			 '' as dtarquivo
	     from (
      	     select
	                 caixa,
		               data,
		               sum(valor_debito) as valor_debito,
		               valor_credito,
		               tipo_movimentacao::text,
		               codigo::text,
		               tipo::text,
		               receita,
                   receita_descr::text,
		               historico::text,
		               cheque::text,
		               null::text as contrapartida,
		               ordem,
		               credor::text,
		               k12_codautent
	          from (
                  select
	                      corrente.k12_id as caixa,
                        corrente.k12_data as data,
		                    cornump.k12_valor as valor_debito,
		                    0 as valor_credito,
	                      ('Recibo '||k12_numpre||'-'||k12_numpar)::text
	                       as tipo_movimentacao,
			                  k12_numpre||'-'||k12_numpar::text as codigo,
                        'recibo'::text as tipo,
		                    cornump.k12_receit as receita,
			                  tabrec.k02_drecei::text as receita_descr,
			                  (coalesce(corhist.k12_histcor,'.'))::text as historico,
			                  null::text as cheque,
			                  null::text as contrapartida,
			                  e20_pagordem as ordem,
		                   (select z01_nome::text from arrepaga inner join cgm on z01_numcgm = k00_numcgm where k00_numpre=cornump.k12_numpre limit 1 ) as credor,			  k12_codautent
                   from corrente
                       inner join cornump on cornump.k12_id = corrente.k12_id and cornump.k12_data = corrente.k12_data
                                                                                    and cornump.k12_autent = corrente.k12_autent
                       left join corgrupocorrente on corrente.k12_id    = k105_id
                                         and corrente.k12_autent = k105_autent and corrente.k12_data = k105_data
                       left join retencaocorgrupocorrente     on e47_corgrupocorrente  = k105_sequencial
                       left join retencaoreceitas             on e47_retencaoreceita   = e23_sequencial
                       left join retencaopagordem             on e23_retencaopagordem  = e20_sequencial
                       inner join tabrec on tabrec.k02_codigo   = cornump.k12_receit
		                   left join corhist on  corhist.k12_id     = corrente.k12_id and corhist.k12_data     = corrente.k12_data
                                                                                  and corhist.k12_autent   = corrente.k12_autent
				               left join corautent	on corautent.k12_id = corrente.k12_id and corautent.k12_data   = corrente.k12_data
							                                                                    and corautent.k12_autent = corrente.k12_autent
                       left  join corcla on corcla.k12_id = corrente.k12_id and corcla.k12_data   = corrente.k12_data
                                                                            and corcla.k12_autent = corrente.k12_autent
                       left join corplacaixa on corrente.k12_id  = k82_id and corrente.k12_data  = k82_data
                                                                          and corrente.k12_autent= k82_autent
	                 where corrente.k12_conta = {$k13_reduz}
                     and (corrente.k12_data between '{$datai}' and '{$dataf}')
		                 and corrente.k12_instit = {$instituicaoSessao->getCodigo()}
                     and k12_codcla is null
                     and k82_seqpla is null

              ) as x
		group by
		       caixa,
			   data,
			   valor_credito,
			   tipo_movimentacao,
			   codigo,
			   tipo,
			   receita,
               receita_descr,
			   historico,
			   cheque,
			   contrapartida,
			   ordem,
			   credor,
               k12_codautent
             ) as xx
/* PLANILHA */
union all
	     select
             caixa,
             data,
      		   valor_debito,
		         valor_credito,
		         tipo_movimentacao,
		         codigo,
		         tipo,
		         receita,
		         receita_descr,
		         historico,
		         cheque,
             contrapartida,
		         ordem,
		         credor,
		         ''::text as numcgm,
		         k12_codautent,
			 0 as k105_corgrupotipo,
		         '' as codret,
						 '' as dtretorno,
						 '' as arqret,
						 '' as dtarquivo
	     from (
	           select
	                 caixa,
		               data,
		               sum(valor_debito) as valor_debito,
		               valor_credito,
		               tipo_movimentacao::text,
		               codigo::text,
		               tipo::text,
		               receita,
		               receita_descr::text,
		               historico::text,
		               cheque::text,
		               null::text as contrapartida,
		               ordem,
		               credor::text	,
                    " . ($imprime_analitico == 'a' ? 'k12_codautent' : 'NULL::text AS k12_codautent') . "
	           from (
                  select
	                       corrente.k12_id as caixa,
                         corrente.k12_data as data,
                         case when k12_valor > 0 then k12_valor else 0 end as valor_debito,
                         case when k12_valor < 0 then k12_valor else 0 end as valor_credito,
	                       ('planilha :'||k81_codpla)::text as tipo_movimentacao,
			                   k81_codpla::text as codigo,
           	             'planilha'::text as tipo,
		   	                 k81_receita as receita,
                         tabrec.k02_drecei as receita_descr,
		                     (coalesce(placaixarec.k81_obs,'.'))::text as historico,
		                     null::text as cheque,
			                   null::text as contrapartida,
		                     0 as ordem,
			                   null::text as credor ,
	                       k12_codautent
                  from corrente
			                 	inner join corplacaixa on k12_id = k82_id  and k12_data   = k82_data
                                                                   and k12_autent = k82_autent
                				inner join placaixarec on k81_seqpla = k82_seqpla
			                  inner join tabrec on tabrec.k02_codigo = k81_receita
		                     /*
		                      left  join arrenumcgm on k00_numpre = cornump.k12_numpre
                          left join cgm on k00_numcgm = z01_numcgm
                        */
	                     left join corhist on corhist.k12_id = corrente.k12_id     and corhist.k12_data  = corrente.k12_data
                                                                                 and  corhist.k12_autent = corrente.k12_autent
			                 inner join corautent on corautent.k12_id = corrente.k12_id and corautent.k12_data   = corrente.k12_data
                                                                                 and corautent.k12_autent = corrente.k12_autent
           			where corrente.k12_conta = {$k13_reduz}  and (corrente.k12_data between '{$datai}' and '{$dataf}')
		                                                   and corrente.k12_instit = {$instituicaoSessao->getCodigo()}
              ) as x
		group by
		       caixa,
			     data,
			     valor_credito,
			     tipo_movimentacao,
			     codigo,
			     tipo,
			     receita,
			     receita_descr,
			     historico,
           cheque,
	         contrapartida,
			     ordem,
			     credor,
			     k12_codautent
             ) as xx
/*  BAIXA DE BANCO */
union all
      select
             caixa,
		         data,
		         valor_debito,
		         valor_credito,
		         tipo_movimentacao,
		         codigo,
		         tipo,
		         receita,
             receita_descr,
		         historico,
		         cheque,
		         contrapartida,
             ordem,
		         credor,
		         ''::text as numcgm,
		         k12_codautent,
			 0 as k105_corgrupotipo,
		         codret::text,
			       dtretorno::text,
			       arqret::text,
			       dtarquivo::text
     from (
	         select
	                caixa,
      		        data,
		              sum(valor_debito) as valor_debito,
		              valor_credito,
		              tipo_movimentacao::text,
		              codigo::text,
		              tipo::text,
		              receita,
                  receita_descr::text,
		              historico::text,
		              cheque::text,
		              null::text as contrapartida,
		              ordem,
		              credor::text,
		              k12_codautent,
		              codret,
			            dtretorno,
			            arqret,
			            dtarquivo
	          from (
                  select
	                      corrente.k12_id as caixa,
                        corrente.k12_data as data,
		                    cornump.k12_valor as valor_debito,
		                    0 as valor_credito,
	                      ('Baixa da banco ')::text as tipo_movimentacao,
		                     discla.codret as codigo,
                        'Baixa'::text as tipo,
		                    cornump.k12_receit as receita,
		                    tabrec.k02_drecei::text as receita_descr,
		                    (coalesce(corhist.k12_histcor,'.'))::text as historico,
		                    null::text as cheque,
		                    null::text as contrapartida,
			                  0 as ordem,
			                  disarq.codret as codret,
			                  disarq.dtretorno as dtretorno,
			                  disarq.arqret as arqret,
			                  disarq.dtarquivo as dtarquivo,
		                    (select z01_nome::text from recibopaga inner join cgm on z01_numcgm = k00_numcgm where k00_numpre=cornump.k12_numpre limit 1 ) as credor,k12_codautent
                 from corrente
                      inner join cornump on cornump.k12_id = corrente.k12_id and cornump.k12_data   = corrente.k12_data
                                                                             and cornump.k12_autent = corrente.k12_autent
                      inner join tabrec on tabrec.k02_codigo = cornump.k12_receit

	                 	   /*
                         left  join arrenumcgm on k00_numpre = cornump.k12_numpre
                         left join cgm on k00_numcgm = z01_numcgm
                      */

	                   left join corhist   on corhist.k12_id   = corrente.k12_id  and	corhist.k12_data     = corrente.k12_data
                                                                                and corhist.k12_autent   = corrente.k12_autent
                  	 left join corautent on corautent.k12_id = corrente.k12_id
                                        and corautent.k12_data   = corrente.k12_data
                                        and corautent.k12_autent = corrente.k12_autent

		                 inner join corcla    on corcla.k12_id    = corrente.k12_id  and corcla.k12_data      = corrente.k12_data
                                                                                and corcla.k12_autent    = corrente.k12_autent
                     inner join discla on discla.codcla = corcla.k12_codcla and discla.instit = {$instituicaoSessao->getCodigo()}
           					 inner join disarq on disarq.codret = discla.codret and disarq.instit = discla.instit
                     left join corplacaixa on corplacaixa.k82_id     = corrente.k12_id
                                          and corplacaixa.k82_data   = corrente.k12_data
                                          and corplacaixa.k82_autent = corrente.k12_autent
			          where corrente.k12_conta = {$k13_reduz}
                  and (corrente.k12_data between '{$datai}' and '{$dataf}')
                  and corrente.k12_instit = {$instituicaoSessao->getCodigo()}

                  and corplacaixa.k82_id is null
                  and corplacaixa.k82_data is null
                  and corplacaixa.k82_autent is null
              ) as x
		group by
		           caixa,
		      	   data,
			         valor_credito,
			         tipo_movimentacao,
			         codigo,
			         tipo,
			         receita,
               receita_descr,
			         historico,
			         cheque,
			         contrapartida,
			         ordem,
			         credor,
               k12_codautent,
               codret,
			         dtretorno,
			         arqret,
			         dtarquivo
             ) as xx
";
        //  SINTETICO
        $sqlsintetico = "
   union all
  select caixa,
       data,
       valor_debito,
       valor_credito,
       null::text as tipo_movimentacao,
       codigo,
       tipo,
       0 as receita,
       null::text as receita_descr,
       historico,
       cheque,
       contrapartida,
       ordem,
       credor,
       ''::text as numcgm,
       k12_codautent,
       0 as k105_corgrupotipo,
       '' as codret,
			 '' as dtretorno,
			 '' as arqret,
			 '' as dtarquivo
from (
select caixa,
       data,
       sum(valor_debito) as valor_debito,
       sum(valor_credito) as valor_credito,
       codigo,
       tipo,
       historico,
       cheque,
       contrapartida,
       ordem,
       credor,
       k12_codautent
  from ({$sqlanalitico}) as agrupado
	group by
		caixa,
		data,
	    codigo,
		tipo,
		historico,
		cheque,
		contrapartida,
		ordem,
		credor,
		k12_codautent
	) as autent_recibo
";
        /* SLIP DEBITO */
        $sqlslip = "

 	     union all
	     /* transferencias a debito - entradas*/
	     select
	           corrente.k12_id as caixa,
	           corlanc.k12_data as data,
		   corrente.k12_valor as valor_debito,
		   0 as valor_credito,
		   'Slip '||k12_codigo::text as tipo_movimentacao,
		   k12_codigo::text as codigo,
		   'slip'::text as tipo,
		   0 as receita,
           null::text as receita_descr,
		   slip.k17_texto::text as historico,
		   e91_cheque::text as cheque,
           k17_debito||' - '||c60_descr as contrapartida,
		   0 as ordem,
		   z01_nome::text as credor,
		   z01_numcgm::text as numcgm,
       k12_codautent,
       0 as k105_corgrupotipo,
       '' as codret,
			 '' as dtretorno,
			 '' as arqret,
			 '' as dtarquivo
	     from corlanc
	           inner join corrente on corrente.k12_id  = corlanc.k12_id    and
		                          corrente.k12_data  = corlanc.k12_data  and
					 									 corrente.k12_autent = corlanc.k12_autent

           inner join slip on slip.k17_codigo = corlanc.k12_codigo
		   inner join conplanoreduz on c61_reduz  = slip.k17_credito
                                       and c61_anousu = {$anoSessao}
               inner join conplano      on c60_codcon = c61_codcon
                                       and c60_anousu = c61_anousu

		   left join slipnum on slipnum.k17_codigo = slip.k17_codigo
		   left join cgm on slipnum.k17_numcgm = z01_numcgm


		   left join corconf on corconf.k12_id = corlanc.k12_id 				and
		                        corconf.k12_data = corlanc.k12_data 		and
														corconf.k12_autent = corlanc.k12_autent and
														corconf.k12_ativo is true
                   left join empageconfche on empageconfche.e91_codcheque = corconf.k12_codmov and
                   													  corconf.k12_ativo is true
                   													  and empageconfche.e91_ativo is true
                   left join corhist on   corhist.k12_id     = corrente.k12_id    and
		                          corhist.k12_data   = corrente.k12_data  and
					  corhist.k12_autent = corrente.k12_autent
					left join corautent	on corautent.k12_id     = corrente.k12_id
									and corautent.k12_data   = corrente.k12_data
									and corautent.k12_autent = corrente.k12_autent
			     where corlanc.k12_conta = {$k13_reduz}  and
	           corlanc.k12_data between '{$datai}' and '{$dataf}'
	     union all
/* SLIP CREDITO */
	     select
	           corrente.k12_id as caixa,
	           corlanc.k12_data as data,
		   0                  as valor_debito,
		   corrente.k12_valor as valor_credito,
		   'Slip '||k12_codigo::text as tipo_movimentacao,
		   k12_codigo::text as codigo,
		   'slip'::text as tipo,
		   0 as receita,
		   null::text as receita_descr,
		   slip.k17_texto::text as historico,
		   e91_cheque::text as cheque,
		   k17_debito||' - '||c60_descr as contrapartida,
		   0 as ordem,
		   z01_nome::text as credor,
		   z01_numcgm::text as numcgm,
       k12_codautent,
       0 as k105_corgrupotipo,
       '' as codret,
			 '' as dtretorno,
			 '' as arqret,
			 '' as dtarquivo
	     from corrente
	           inner join corlanc on corrente.k12_id     = corlanc.k12_id
                                 and corrente.k12_data   = corlanc.k12_data
                                 and corrente.k12_autent = corlanc.k12_autent
		       inner join slip on        slip.k17_codigo = corlanc.k12_codigo
               inner join conplanoreduz    on c61_reduz  = slip.k17_debito
                                          and c61_anousu = {$anoSessao}
               inner join conplano         on c60_codcon = c61_codcon
                                          and c60_anousu = c61_anousu
		       left join slipnum on slipnum.k17_codigo = slip.k17_codigo
		       left join cgm on slipnum.k17_numcgm = z01_numcgm
		       left join corconf on corconf.k12_id = corlanc.k12_id
                                and corconf.k12_data = corlanc.k12_data
                                and	corconf.k12_autent = corlanc.k12_autent
                                and corconf.k12_ativo is true
               left join empageconfche on empageconfche.e91_codcheque = corconf.k12_codmov
               												and	corconf.k12_ativo is true
               												and empageconfche.e91_ativo is true
	           left join corhist on corhist.k12_id     = corrente.k12_id
                                and corhist.k12_data   = corrente.k12_data
                                and corhist.k12_autent = corrente.k12_autent
              left join corautent	on corautent.k12_id     = corrente.k12_id
									and corautent.k12_data   = corrente.k12_data
									and corautent.k12_autent = corrente.k12_autent
	     where corrente.k12_conta = {$k13_reduz}  and
	           corrente.k12_data between '{$datai}' and '{$dataf}'

";


/*  RETORNO TEF  */

  $sqlTef = <<<SQL


        SELECT corrente.k12_id AS caixa,
               corlanc.k12_data AS DATA,
               corrente.k12_valor AS valor_debito,
               0 AS valor_credito,
               '' ||  k12_codigo::text AS tipo_movimentacao,
               k12_codigo::text AS codigo,
               '' :: text AS tipo,
               0 AS receita,
               NULL::text AS receita_descr,
               k12_histcor::text AS historico,
               ''::text AS cheque,
               corrente.k12_conta ||' - '||c60_descr AS contrapartida,
               0 AS ordem,
               'Retorno TEF'::text AS credor,
               ''::text AS numcgm,
               k12_codautent,
               0 AS k105_corgrupotipo,
               '' AS codret,
               '' AS dtretorno,
               '' AS arqret,
               '' AS dtarquivo

          FROM corrente
    INNER JOIN corlanc ON corrente.k12_id = corlanc.k12_id
                      AND corrente.k12_data = corlanc.k12_data
                      AND corrente.k12_autent = corlanc.k12_autent
    INNER JOIN conplanoreduz ON c61_reduz = corrente.k12_conta
                            AND c61_anousu = {$anoSessao}
    INNER JOIN conplano ON c60_codcon = c61_codcon
                       AND c60_anousu = c61_anousu

     LEFT JOIN corhist ON corhist.k12_id = corrente.k12_id
                      AND corhist.k12_data = corrente.k12_data
                      AND corhist.k12_autent = corrente.k12_autent
     LEFT JOIN corautent ON corautent.k12_id = corrente.k12_id
                        AND corautent.k12_data = corrente.k12_data
                        AND corautent.k12_autent = corrente.k12_autent

    inner join conlancamcorrente on corrente.k12_id = c86_id
                                and corrente.k12_data = c86_data
                                and corrente.k12_autent = c86_autent
    inner join conlancamdoc on   c86_conlancam =  c71_codlan
                           and c71_coddoc = 169

 where corrente.k12_conta = {$k13_reduz}  and
 corrente.k12_data between '{$datai}' and '{$dataf}'

 ORDER BY DATA,
          caixa,
          k12_codautent,
          codigo


SQL;



//echo $sqlTef; die();

        $sqltotal = "{$sqlempenho} UNION ALL {$sqlanalitico}{$sqlslip} UNION ALL {$sqlTef}";


        //echo $sqltotal; die();


        $resmovimentacao = db_query($sqltotal);
        $quebra_data = '';
        $saldo_dia_final = $anterior;

        $aContas[$k13_reduz]->data = array();
        $iInd = -1;
        $saldo_dia_debito = 0;
        $saldo_dia_credito = 0;

        if (pg_num_rows($resmovimentacao) > 0) {
            for ($i = 0; $i < pg_num_rows($resmovimentacao); $i++) {
                db_fieldsmemory($resmovimentacao, $i);

                if (isset($considerar_retencoes) && $considerar_retencoes == 'n') {
                    if ($ordem > 0 && ($k105_corgrupotipo == 0 || $k105_corgrupotipo == 2)) {
                        continue;
                    }
                }

                // controla quebra de saldo por dia
                if ($quebra_data != $data && $quebra_data != '' && $totalizador_diario == 's') {
                    $lPrimeiroDaConta = false;
                    $aContas[$k13_reduz]->data[$iInd]->saldo_dia_debito = $saldo_dia_debito;
                    $aContas[$k13_reduz]->data[$iInd]->saldo_dia_credito = $saldo_dia_credito;
                    // calcula saldo a debito ou credito
                    if ($saldo_dia_debito < 0) {
                        $saldo_dia_final -= abs($saldo_dia_debito);
                        $aContas[$k13_reduz]->data[$iInd]->saldo_dia_final = $saldo_dia_final;
                    } else {
                        $saldo_dia_final += $saldo_dia_debito;
                        $aContas[$k13_reduz]->data[$iInd]->saldo_dia_final = $saldo_dia_final;
                    }

                    if ($saldo_dia_credito < 0) {
                        $saldo_dia_final += abs($saldo_dia_credito);
                        $aContas[$k13_reduz]->data[$iInd]->saldo_dia_final = $saldo_dia_final;
                    } else {
                        $saldo_dia_final -= $saldo_dia_credito;
                        $aContas[$k13_reduz]->data[$iInd]->saldo_dia_final = $saldo_dia_final;
                    }
                    $saldo_dia_debito = 0;
                    $saldo_dia_credito = 0;
                }

                if ($quebra_data != $data) {
                    $iInd++;

                    if (!array_key_exists($iInd, $aContas[$k13_reduz]->data)) {
                        $aContas[$k13_reduz]->data[$iInd] = new stdClass();
                    }

                    $aContas[$k13_reduz]->data[$iInd]->data = $data;
                    $aContas[$k13_reduz]->data[$iInd]->movimentacoes = array();
                }

                $oMovimentacao = new stdClass();
                $oMovimentacao->caixa = $caixa;
                $oMovimentacao->valor_debito = $valor_debito;
                $oMovimentacao->valor_credito = $valor_credito;
                $oMovimentacao->receita = $receita;
                $oMovimentacao->k12_codautent = $k12_codautent;
                $oMovimentacao->codigo = $codigo;
                $oMovimentacao->credor = $credor;
                $oMovimentacao->codigocredor = $numcgm;
                $oMovimentacao->codret = $codret;
                $oMovimentacao->dtretorno = $dtretorno != '' ? db_formatar($dtretorno, 'd') : '';
                $oMovimentacao->arqret = $arqret;
                $oMovimentacao->dtarquivo = $dtarquivo != '' ? db_formatar($dtarquivo, 'd') : '';
                $oMovimentacao->tipo = $tipo;
                $oMovimentacao->planilha = $tipo == 'planilha' ? $codigo : '';
                $oMovimentacao->k12_codcla = $tipo == 'baixa' ? $codigo : '';
                $oMovimentacao->ordem = $ordem;
                $oMovimentacao->empenho = $tipo == 'empenho' ? $codigo : '';
                $oMovimentacao->cheque = $cheque;
                $oMovimentacao->slip = $tipo == 'slip' ? $codigo : '';

                // DEBITO E CREDITO
                if ($valor_debito == 0 && $valor_credito != 0) {
                    $oMovimentacao->valor_debito = '';
                    //Modificação feita para acertar a forma quando é mostrada os valores relativos as planilha de dedução
                    if ($tipo == 'planilha') {
                        $valor_credito = $valor_credito * -1;
                        $oMovimentacao->valor_credito = $valor_credito;
                    } else {
                        $oMovimentacao->valor_credito = $valor_credito;
                    }
                } elseif ($valor_credito == 0 && $valor_debito != 0) {
                    $oMovimentacao->valor_debito = $valor_debito;
                    $oMovimentacao->valor_credito = $valor_credito;
                } else {
                    $oMovimentacao->valor_debito = $valor_debito;
                    $oMovimentacao->valor_credito = $valor_credito;
                }

                if ($receita > 0) {
                    // selecina reduzido da receita no plano de contas

                    $sql = "select c61_reduz
		    						from taborc
		      					inner join orcreceita on o70_codrec=taborc.k02_codrec and o70_anousu=k02_anousu and o70_instit= {$instituicaoSessao->getCodigo()}
		      					inner join conplanoreduz on c61_codcon=o70_codfon and c61_instit=o70_instit and c61_anousu=o70_anousu
		    					where  k02_codigo = {$receita}
		       					 and k02_anousu = {$anoSessao}
		    		union
	            	select c61_reduz
		    						from tabplan
		        				inner join conplanoreduz on c61_reduz=k02_reduz and c61_instit= {$instituicaoSessao->getCodigo()} and c61_anousu=k02_anousu
		    					where k02_codigo = {$receita}
		      					and k02_anousu = {$anoSessao}
	               	";

                    $res_rec = db_query($sql);
                    $c61_reduz = '';
                    if (pg_num_rows($res_rec) > 0) {
                        db_fieldsmemory($res_rec, 0);
                    }
                }

                $oMovimentacao->contrapartida = '';

                if ($tipo == 'recibo' || $tipo == 'planilha' || $tipo == 'Baixa') {
                    if ($receita > 0) {
                        $oMovimentacao->contrapartida = "{$receita} ";
                        if ($c61_reduz != '') {
                            $oMovimentacao->contrapartida .= "({$c61_reduz}) - ";
                        }
                        $oMovimentacao->contrapartida .= $receita_descr;
                    }
                }
                if ($tipo == 'slip') {
                    $oMovimentacao->contrapartida = $contrapartida;
                }

                $oMovimentacao->credor = $credor;
                $oMovimentacao->historico = $historico;
                // soma acumuladores diarios

                $saldo_dia_debito += $valor_debito;
                $saldo_dia_credito += $valor_credito;

                $quebra_data = $data;

                $aContas[$k13_reduz]->data[$iInd]->movimentacoes[] = $oMovimentacao;
            }
        }

        if ($totalizador_diario == 's') {
            // calcula saldo a debito ou credito
            $aContas[$k13_reduz]->data[$iInd]->saldo_dia_debito = $saldo_dia_debito;
            $aContas[$k13_reduz]->data[$iInd]->saldo_dia_credito = $saldo_dia_credito;
            // calcula saldo a debito ou credito
            if ($saldo_dia_debito < 0) {
                $saldo_dia_final -= abs($saldo_dia_debito);
                $aContas[$k13_reduz]->data[$iInd]->saldo_dia_final = $saldo_dia_final;
            } else {
                $saldo_dia_final += $saldo_dia_debito;
                $aContas[$k13_reduz]->data[$iInd]->saldo_dia_final = $saldo_dia_final;
            }
            if ($saldo_dia_credito < 0) {
                $saldo_dia_final += abs($saldo_dia_credito);
                $aContas[$k13_reduz]->data[$iInd]->saldo_dia_final = $saldo_dia_final;
            } else {
                $saldo_dia_final -= $saldo_dia_credito;
                $aContas[$k13_reduz]->data[$iInd]->saldo_dia_final = $saldo_dia_final;
            }
        }
        $aContas[$k13_reduz]->debitado = $debitado;
        $aContas[$k13_reduz]->creditado = $creditado;
        $aContas[$k13_reduz]->atual = $atual;
    }

    if ($agrupapor != 1 || $receitaspor == 2) {
        $aMovimentacao = array();
        $aContasNovas = array();
        foreach ($aContas as $key2 => $oConta) {
            $aContasNovas[$key2] = $oConta;
            foreach ($oConta->data as $key1 => $oData) {
                foreach ($oData->movimentacoes as $oMovimento) {
                    if ($receitaspor == 2 && $oMovimento->tipo == 'Baixa') {
                        $controle = false;
                        foreach ($aMovimentacao as $key => $oValor) {
                            if ($oValor->tipo == $oMovimento->tipo &&
                                $oValor->codigo == $oMovimento->codigo &&
                                $controle == false) {
                                $controle = true;
                                $chave = $key;
                            }
                        }
                        if ($controle) {
                            $aMovimentacao[$chave]->valor_debito += $oMovimento->valor_debito;
                            $aMovimentacao[$chave]->valor_credito += $oMovimento->valor_credito;
                            $aMovimentacao[$chave]->caixa = '';
                            $aMovimentacao[$chave]->planilha = '';
                            $aMovimentacao[$chave]->empenho = '';
                            $aMovimentacao[$chave]->ordem = '';
                            $aMovimentacao[$chave]->cheque = '';
                            $aMovimentacao[$chave]->slip = '';
                            $aMovimentacao[$chave]->contrapartida = 'Baixa Bancária ref Arquivo ';
                            $aMovimentacao[$chave]->contrapartida .= "{$oMovimento->arqret}, do dia ";
                            $aMovimentacao[$chave]->contrapartida .= "{$oMovimento->dtarquivo}, retorno ";
                            $aMovimentacao[$chave]->contrapartida .= "{$oMovimento->codret} de ";
                            $aMovimentacao[$chave]->contrapartida .= $oMovimento->dtretorno;
                            $aMovimentacao[$chave]->credor = '';
                            $aMovimentacao[$chave]->historico = '';
                            $aMovimentacao[$chave]->agrupado = 'Baixa';
                        } else {
                            $oMovimento->contrapartida = 'Baixa Bancária ref Arquivo ';
                            $oMovimento->contrapartida .= "{$oMovimento->arqret}, do dia ";
                            $oMovimento->contrapartida .= "{$oMovimento->dtarquivo}, retorno ";
                            $oMovimento->contrapartida .= "{$oMovimento->codret} de ";
                            $oMovimento->contrapartida .= $oMovimento->dtretorno;

                            $aMovimentacao[] = $oMovimento;
                        }
                    } else {
                        if ($agrupapor == 2 && $oMovimento->receita != '0' && $oMovimento->tipo != 'Baixa') {
                            $controle = false;

                            if ($oMovimento->tipo == 'slip') {
                                $aMovimentacao[] = $oMovimento;
                            } else {
                                foreach ($aMovimentacao as $key => $oValor) {
                                    if ($oValor->receita == $oMovimento->receita && $controle == false) {
                                        $controle = true;
                                        $chave = $key;
                                    }
                                }
                                if ($controle) {
                                    $aMovimentacao[$chave]->valor_debito += $oMovimento->valor_debito ?: 0;
                                    $aMovimentacao[$chave]->valor_credito += $oMovimento->valor_credito;
                                    $aMovimentacao[$chave]->caixa = '';
                                    $aMovimentacao[$chave]->k123_codautent = '';
                                    $aMovimentacao[$chave]->tipo = '';
                                    $aMovimentacao[$chave]->planilha = '';
                                    $aMovimentacao[$chave]->empenho = '';
                                    $aMovimentacao[$chave]->ordem = '';
                                    $aMovimentacao[$chave]->cheque = '';
                                    $aMovimentacao[$chave]->slip = '';
                                    $aMovimentacao[$chave]->contrapartida = $oMovimento->contrapartida;
                                    $aMovimentacao[$chave]->credor = '';
                                    $aMovimentacao[$chave]->historico = '';
                                    $aMovimentacao[$chave]->agrupado = 'receita';
                                } else {
                                    $aMovimentacao[] = $oMovimento;
                                }
                            }
                        } else {
                            if ($agrupapor == 3 && $oMovimento->tipo == 'empenho') {
                                $controle = false;
                                foreach ($aMovimentacao as $key => $oValor) {
                                    if ($oValor->receita == $oMovimento->receita &&
                                        $oValor->codigo == $oMovimento->codigo &&
                                        $oValor->tipo == $oMovimento->tipo &&
                                        $controle == false) {
                                        $controle = true;
                                        $chave = $key;
                                    }
                                }
                                if ($controle) {
                                    $aMovimentacao[$chave]->valor_debito += $oMovimento->valor_debito;
                                    $aMovimentacao[$chave]->valor_credito += $oMovimento->valor_credito;
                                    $aMovimentacao[$chave]->caixa = '';
                                    $aMovimentacao[$chave]->k123_codautent = '';
                                    $aMovimentacao[$chave]->planilha = '';
                                    $aMovimentacao[$chave]->ordem = '';
                                    $aMovimentacao[$chave]->cheque = '';
                                    $aMovimentacao[$chave]->slip = '';
                                    $aMovimentacao[$chave]->contrapartida = $oMovimento->credor;
                                    $aMovimentacao[$chave]->credor = '';
                                    $aMovimentacao[$chave]->historico = '';
                                    $aMovimentacao[$chave]->agrupado = 'empenho';
                                } else {
                                    $oMovimento->contrapartida = $oMovimento->credor;

                                    $aMovimentacao[] = $oMovimento;
                                }
                            } else {
                                if ($agrupapor == 2 && $pagempenhos == 2) {
                                    $controle = false;
                                    if ($oMovimento->tipo != 'empenho') {
                                        $aMovimentacao[] = $oMovimento;
                                    } else {
                                        foreach ($aMovimentacao as $key => $oValor) {
                                            if ($oValor->ordem == $oMovimento->ordem &&
                                                $controle == false &&
                                                $oValor->tipo == 'empenho') {
                                                $controle = true;
                                                $chave = $key;
                                            }
                                        }
                                        if ($controle) {
                                            $aMovimentacao[$chave]->valor_debito += $oMovimento->valor_debito;
                                            $aMovimentacao[$chave]->valor_credito += $oMovimento->valor_credito;

                                            if ($oMovimento->tipo == 'empenho' && $oMovimento->empenho != '') {
                                                $oMovimento->contrapartida = "{$oMovimento->codigocredor} - {$oMovimento->credor}";
                                                $aMovimentacao[$chave]->contrapartida = $oMovimento->contrapartida;
                                            }
                                        } else {
                                            if ($oMovimento->tipo == 'empenho' && $oMovimento->empenho != '') {
                                                $oMovimento->contrapartida = "{$oMovimento->codigocredor} - {$oMovimento->credor}";
                                            }
                                            if ($oMovimento->tipo == 'empenho' || $oMovimento->tipo == 'slip') {
                                                $oMovimento->codigo = '';
                                            }
                                            $aMovimentacao[] = $oMovimento;
                                        }
                                    }
                                } else {
                                    if ($pagempenhos == 2 && $imprime_analitico == 's') {
                                        if ($oMovimento->tipo != 'empenho') {
                                            $aMovimentacao[] = $oMovimento;
                                        } else {
                                            $controle = false;
                                            foreach ($aMovimentacao as $key => $oValor) {
                                                if ($oValor->ordem == $oMovimento->ordem &&
                                                    $oValor->tipo == $oMovimento->tipo &&
                                                    $controle == false &&
                                                    $oValor->tipo == 'empenho') {
                                                    $controle = true;
                                                    $chave = $key;
                                                }
                                            }
                                            if ($controle) {
                                                $aMovimentacao[$chave]->valor_debito += $oMovimento->valor_debito;
                                                $aMovimentacao[$chave]->valor_credito += $oMovimento->valor_credito;
                                                $aMovimentacao[$chave]->caixa = '';
                                                $aMovimentacao[$chave]->k123_codautent = '';
                                                $aMovimentacao[$chave]->planilha = '';
                                                $aMovimentacao[$chave]->cheque = '';
                                                $aMovimentacao[$chave]->slip = '';
                                                $aMovimentacao[$chave]->contrapartida = $oMovimento->credor;
                                                $aMovimentacao[$chave]->credor = '';
                                                $aMovimentacao[$chave]->historico = '';
                                                $aMovimentacao[$chave]->agrupado = 'empenho';
                                            } else {
                                                if ($oMovimento->tipo == 'empenho' && $oMovimento->empenho != '') {
                                                    $oMovimento->contrapartida = "{$oMovimento->codigocredor} - {$oMovimento->credor}";
                                                }
                                                if ($oMovimento->tipo == 'empenho' || $oMovimento->tipo == 'slip') {
                                                    $oMovimento->codigo = '';

                                                    $aMovimentacao[] = $oMovimento;
                                                }
                                            }
                                        }
                                    } else {
                                        if ($oMovimento->tipo == 'empenho' && $oMovimento->empenho != '') {
                                            $oMovimento->contrapartida = $oMovimento->codigocredor . " - " . $oMovimento->credor;
                                        }
                                        if ($oMovimento->tipo == 'empenho' || $oMovimento->tipo == 'slip') {
                                            $oMovimento->codigo = '';
                                        }
                                        $aMovimentacao[] = $oMovimento;
                                    }
                                }
                            }
                        }
                    }
                }
                $aContasNovas[$oConta->k13_reduz]->data[$key1]->movimentacoes = $aMovimentacao;
                $aMovimentacao = array();
            }
        }

        $aContas = $aContasNovas;
    } else {
        if ($agrupapor == 1 && $pagempenhos == 2) {
            $aMovimentacao = array();
            $aContasNovas = array();

            foreach ($aContas as $key2 => $oConta) {
                $aContasNovas[$key2] = $oConta;
                foreach ($oConta->data as $key1 => $oData) {
                    foreach ($oData->movimentacoes as $oMovimento) {
                        $controle = false;

                        if ($oMovimento->tipo != 'empenho') {
                            $aMovimentacao[] = $oMovimento;
                        } else {
                            foreach ($aMovimentacao as $key => $oValor) {
                                if ($oValor->ordem == $oMovimento->ordem &&
                                    $controle == false &&
                                    $oMovimento->tipo == 'empenho' &&
                                    $oValor->tipo == 'empenho') {
                                    $controle = true;
                                    $chave = $key;
                                }
                            }
                            if ($controle) {
                                $aMovimentacao[$chave]->valor_debito += $oMovimento->valor_debito;
                                $aMovimentacao[$chave]->valor_credito += $oMovimento->valor_credito;
                                if ($oMovimento->tipo == 'empenho' && $oMovimento->empenho != '') {
                                    $oMovimento->contrapartida = $oMovimento->codigocredor . " - " . $oMovimento->credor;
                                    $aMovimentacao[$chave]->contrapartida = $oMovimento->contrapartida;
                                }
                            } else {
                                if ($oMovimento->tipo == 'empenho' && $oMovimento->empenho != '') {
                                    $oMovimento->contrapartida = "{$oMovimento->codigocredor} - {$oMovimento->credor}";
                                }
                                $aMovimentacao[] = $oMovimento;
                            }
                        }
                    }
                    $aContasNovas[$oConta->k13_reduz]->data[$key1]->movimentacoes = $aMovimentacao;
                    $aMovimentacao = array();
                }
            }
            $aContas = $aContasNovas;
        }
    }



    if ($imprime_pdf == 'p') {
        $pdf = new PDF();
        $pdf->Open();
        $pdf->AliasNbPages();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->setfillcolor(235);
        $pdf->AutoPageBreak = false;
        $pdf->AddPage('L');

        $quebra_data = '';
        $lQuebra_Historico = false;
        foreach ($aContas as $oConta) {



            $lImprimeSaldo = true;
            if ($pdf->GetY() > $pdf->h - 25) {
                $pdf->AddPage('L');
            }

            imprimeConta(
                $pdf,
                $oConta->k13_reduz,
                $oConta->k13_descr,
                $oConta->k13_dtimplantacao,
                $oConta->debito,
                $oConta->credito,
                $lImprimeSaldo
            );




            $lImprimeSaldo = false;
            imprimeCabecalho($pdf);

            foreach ($oConta->data as $oData) {

                //dd($oData);

                if (property_exists($oData, 'movimentacoes') && !empty($oData->movimentacoes)) {


                    foreach ($oData->movimentacoes as $oMovimento) {
                        if ($totalizador_diario == 's' && $quebra_data != '' && $quebra_data != $oData->data) {
                            imprimeTotalMovDia($pdf, $saldo_dia_debito, $saldo_dia_credito, $saldo_dia_final);
                            $saldo_dia_debito = 0;
                            $saldo_dia_credito = 0;
                            $saldo_dia_final = 0;
                        }

                        if ($pdf->GetY() > $pdf->h - 25) {
                            $pdf->AddPage('L');

                            imprimeConta(
                                $pdf,
                                $oConta->k13_reduz,
                                $oConta->k13_descr,
                                $oConta->k13_dtimplantacao,
                                $oConta->debito,
                                $oConta->credito,
                                $lImprimeSaldo
                            );
                            imprimeCabecalho($pdf);
                        }

                        if ($lQuebra_Historico) {
                            imprimeConta(
                                $pdf,
                                $oConta->k13_reduz,
                                $oConta->k13_descr,
                                $oConta->k13_dtimplantacao,
                                $oConta->debito,
                                $oConta->credito,
                                $lImprimeSaldo
                            );
                            imprimeCabecalho($pdf);
                            $lQuebra_Historico = false;
                        }

                        $pdf->Cell(20, 5, db_formatar($oData->data, 'd'), "T", 0, "C", 0);
                        $pdf->Cell(85, 5, $oMovimento->contrapartida, "T", 0, "L", 0);
                        $pdf->Cell(25, 5, $oMovimento->planilha, "T", 0, "C", 0);
                        $pdf->Cell(25, 5, $oMovimento->empenho, "T", 0, "C", 0);
                        $pdf->Cell(25, 5, $oMovimento->ordem == "0" ? "" : $oMovimento->ordem, "T", 0, "C", 0);
                        $pdf->Cell(25, 5, $oMovimento->cheque == "0" ? "" : $oMovimento->cheque, "T", 0, "C", 0);
                        $pdf->Cell(25, 5, $oMovimento->slip, "T", 0, "C", 0);
                        $pdf->Cell(
                            25,
                            5,
                            $oMovimento->valor_debito == 0 ? "" : db_formatar($oMovimento->valor_debito, "f"),
                            "T",
                            0,
                            "R",
                            0
                        );
                        $pdf->Cell(
                            25,
                            5,
                            $oMovimento->valor_credito == 0 ? "" : db_formatar($oMovimento->valor_credito, "f"),
                            "T",
                            0,
                            "R",
                            0
                        );
                        $pdf->Ln();

                        //Demais detalhes quando analitica

                      //  dd($aContas);

                        if ($imprime_analitico == 'a') {
                            if (!isset($oMovimento->agrupado)) {
                                if ($pdf->GetY() > $pdf->h - 25) {
                                    $pdf->AddPage("L");

                                    imprimeConta(
                                        $pdf,
                                        $oConta->k13_reduz,
                                        $oConta->k13_descr,
                                        $oConta->k13_dtimplantacao,
                                        $oConta->debito,
                                        $oConta->credito,
                                        $lImprimeSaldo
                                    );
                                    imprimeCabecalho($pdf);
                                }


                                //dd($aContas);





                                $pdf->Cell(20, 5, "", 0, 0, "C", 0);
                                $pdf->Cell(30, 5, "Autenticação mecânica:", "", 0, "L", 0);
                                $pdf->Cell(150, 5, trim($oMovimento->k12_codautent), "", 0, "L", 0);
                                $pdf->Ln();
                                if ($pdf->GetY() > $pdf->h - 25) {
                                    $pdf->AddPage("L");

                                    imprimeConta(
                                        $pdf,
                                        $oConta->k13_reduz,
                                        $oConta->k13_descr,
                                        $oConta->k13_dtimplantacao,
                                        $oConta->debito,
                                        $oConta->credito,
                                        $lImprimeSaldo
                                    );
                                    imprimeCabecalho($pdf);
                                }
                                $pdf->Cell(20, 5, "", 0, 0, "C", 0);
                                $pdf->Cell(65, 5, "Classificação de baixa bancária:", "", 0, "L", 0);
                                $pdf->Cell(150, 5, $oMovimento->codigo, "", 0, "L", 0);
                                $pdf->Ln();
                                if ($pdf->GetY() > $pdf->h - 25) {
                                    $pdf->AddPage("L");

                                    imprimeConta(
                                        $pdf,
                                        $oConta->k13_reduz,
                                        $oConta->k13_descr,
                                        $oConta->k13_dtimplantacao,
                                        $oConta->debito,
                                        $oConta->credito,
                                        $lImprimeSaldo
                                    );
                                    imprimeCabecalho($pdf);
                                }
                                $pdf->Cell(20, 5, "", 0, 0, "C", 0);
                                $pdf->Cell(25, 5, "Nome/Razão Social:", "", 0, "L", 0);
                                $pdf->Cell(150, 5, $oMovimento->credor, "", 0, "L", 0);
                                $pdf->Ln();

                                $lQuebra_Historico = false;
                                if ($oMovimento->historico != '' && $imprime_historico == 's') {
                                    $lh = 0;
                                    while ($oMovimento->historico != '') {
                                        $lh = $lh + 1;
                                        if ($pdf->gety() > $pdf->h - 25) {
                                            $pdf->addPage("L");
                                            $lQuebra_Historico = true;
                                        }
                                        $pdf->Cell(20, 5, "", 0, 0, "C", 0);
                                        if ($lh == 1) {
                                            $pdf->Cell(25, 5, "Histórico:", "", 0, "L", 0);
                                        } else {
                                            $pdf->Cell(25, 5, "", "", 0, "L", 0);
                                        }
                                        $oMovimento->historico = $pdf->Row_multicell(array(
                                            '',
                                            '',
                                            '',
                                            $oMovimento->historico,
                                            '',
                                            ''
                                        ), 5, false, 5, 0, true, true, 3, ($pdf->h - 25), 180);
                                    }
                                }
                            }
                        }
                        $quebra_data = $oData->data;
                    }
                }
               // dd($aContas);

                if ($totalizador_diario == 's') {
                    $saldo_dia_credito = $oData->saldo_dia_credito;
                    $saldo_dia_debito = $oData->saldo_dia_debito;
                    $saldo_dia_final = $oData->saldo_dia_final;
                }
            }

            $quebra_data = '';

            imprimeTotalMovDia($pdf, $saldo_dia_debito, $saldo_dia_credito, $saldo_dia_final);
            $saldo_dia_credito = 0;
            $saldo_dia_debito = 0;
            $saldo_dia_final = 0;
            imprimeTotalMovConta($pdf, $oConta->debitado, $oConta->creditado, $oConta->atual);
            $pdf->Ln(5);
        }

        if ($pdf->GetY() > $pdf->h - 25) {
            $pdf->AddPage("L");
        }

        $pdf->Output();
        exit();
    } else {
        //aqui vai gerar o txt
        //Aqui ponteiro para o arquivo
        $fp = fopen('tmp/ExtratoBancario.csv', 'w');

        //Inicio do processametno do conteudo do txt

        $quebra_data = '';
        $lQuebra_Historico = false;
        foreach ($aContas as $oConta) {
            $lImprimeSaldo = true;

            imprimeContaTxt(
                $fp,
                $oConta->k13_reduz,
                $oConta->k13_descr,
                $oConta->debito,
                $oConta->credito,
                $lImprimeSaldo
            );
            $lImprimeSaldo = false;
            imprimeCabecalhoTxt($fp);

            foreach ($oConta->data as $oData) {
                foreach ($oData->movimentacoes as $oMovimento) {
                    if ($totalizador_diario == 's' && $quebra_data != '' && $quebra_data != $oData->data) {
                        imprimeTotalMovDiaTxt($fp, $saldo_dia_debito, $saldo_dia_credito, $saldo_dia_final);
                        $saldo_dia_debito = 0;
                        $saldo_dia_credito = 0;
                        $saldo_dia_final = 0;
                    }

                    if ($lQuebra_Historico) {
                        imprimeContaTxt(
                            $fp,
                            $oConta->k13_reduz,
                            $oConta->k13_descr,
                            $oConta->debito,
                            $oConta->credito,
                            $lImprimeSaldo
                        );
                        imprimeCabecalhoTxt($fp);
                        $lQuebra_Historico = false;
                    }
                    $aLinhaDados = array();
                    $aLinhaDados[0] = db_formatar($oData->data, 'd');
                    $aLinhaDados[1] = $oMovimento->contrapartida;
                    $aLinhaDados[2] = $oMovimento->planilha;
                    $aLinhaDados[3] = $oMovimento->empenho;
                    $aLinhaDados[4] = $oMovimento->ordem == "0" ? "" : $oMovimento->ordem;
                    $aLinhaDados[5] = $oMovimento->cheque == "0" ? "" : $oMovimento->cheque;
                    $aLinhaDados[6] = $oMovimento->slip;
                    $aLinhaDados[7] = $oMovimento->valor_debito == 0 ? "" : db_formatar($oMovimento->valor_debito, "f");
                    $aLinhaDados[8] = $oMovimento->valor_credito == 0 ? "" : db_formatar(
                        $oMovimento->valor_credito,
                        "f"
                    );

                    fputcsv($fp, $aLinhaDados, ',', '"');

                    //Demais detalhes quando analitica
                    if ($imprime_analitico == 'a') {
                        if (!isset($oMovimento->agrupado)) {
                            $aLinhaDados = array();
                            $aLinhaDados[0] = '';
                            $aLinhaDados[1] = "Autenticação mecânica:";
                            $aLinhaDados[2] = '';
                            $aLinhaDados[3] = '';
                            $aLinhaDados[4] = trim($oMovimento->k12_codautent);
                            $aLinhaDados[5] = '';
                            $aLinhaDados[6] = '';
                            $aLinhaDados[7] = '';
                            $aLinhaDados[8] = '';
                            fputcsv($fp, $aLinhaDados, ',', '"');

                            $aLinhaDados = array();
                            $aLinhaDados[0] = '';
                            $aLinhaDados[1] = "Classificação de baixa bancária:";
                            $aLinhaDados[2] = '';
                            $aLinhaDados[3] = '';
                            $aLinhaDados[4] = $oMovimento->codigo;
                            $aLinhaDados[5] = '';
                            $aLinhaDados[6] = '';
                            $aLinhaDados[7] = '';
                            $aLinhaDados[8] = '';
                            fputcsv($fp, $aLinhaDados, ',', '"');

                            $aLinhaDados = array();
                            $aLinhaDados[0] = '';
                            $aLinhaDados[1] = "Nome/Razão Social:";
                            $aLinhaDados[2] = '';
                            $aLinhaDados[3] = '';
                            $aLinhaDados[4] = $oMovimento->credor;
                            $aLinhaDados[5] = '';
                            $aLinhaDados[6] = '';
                            $aLinhaDados[7] = '';
                            $aLinhaDados[8] = '';
                            fputcsv($fp, $aLinhaDados, ',', '"');

                            $lQuebra_Historico = false;

                            if ($oMovimento->historico != '' && $imprime_historico == 's') {
                                $aLinhaDados = array();
                                $aLinhaDados[0] = '';
                                $aLinhaDados[1] = "Histórico:";
                                $aLinhaDados[2] = '';
                                $aLinhaDados[3] = $oMovimento->historico;
                                $aLinhaDados[4] = '';
                                $aLinhaDados[5] = '';
                                $aLinhaDados[6] = '';
                                $aLinhaDados[7] = '';
                                $aLinhaDados[8] = '';
                                fputcsv($fp, $aLinhaDados, ',', '"');
                            }
                        }
                    }

                    $quebra_data = $oData->data;
                }

                if ($totalizador_diario == 's') {
                    $saldo_dia_credito = $oData->saldo_dia_credito;
                    $saldo_dia_debito = $oData->saldo_dia_debito;
                    $saldo_dia_final = $oData->saldo_dia_final;
                }
            }
            $quebra_data = '';
            imprimeTotalMovDiaTxt($fp, $saldo_dia_debito, $saldo_dia_credito, $saldo_dia_final);
            $saldo_dia_credito = 0;
            $saldo_dia_debito = 0;
            $saldo_dia_final = 0;
            imprimeTotalMovContaTxt($fp, $oConta->debitado, $oConta->creditado, $oConta->atual);
        }

        fclose($fp); ?>
        <script>document.location.href = 'tmp/ExtratoBancario.csv';</script>"
        <?php

        exit;
    }
} catch (Exception $exception) {
    $mensagem = urlencode($exception->getMessage());
    db_redireciona("db_erros.php?fechar=true&db_erro={$mensagem}.");
}

/**
 * @param PDF $pdf
 * @param $codigo
 * @param $descricao
 * @param $dtimplantacao
 * @param $debito
 * @param $credito
 * @param $lImprimeSaldo
 */
function imprimeConta(PDF $pdf, $codigo, $descricao, $dtimplantacao, $debito, $credito, $lImprimeSaldo)
{

    //echo "IMPRIMIR <br>";
    $pdf->SetFont('Arial', 'b', 8);
    $pdf->Cell(12, 5, "CONTA:", 0, 0, "L", 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(90, 5, $codigo . " - " . $descricao, 0, 0, "L", 0);
    $pdf->SetFont('Arial', 'b', 8);

    $pdf->Cell(72, 5, "DATA IMPLANTAÇÃO DA CONTA NA TESOURARIA: ", 0, 0, "L", 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(10, 5, db_formatar($dtimplantacao, 'd'), 0, 0, "L", 0);
    $pdf->SetFont('Arial', 'b', 8);

    if ($lImprimeSaldo) {
        $pdf->Cell(40, 5, "SALDO ANTERIOR:", 0, 0, "R", 0);
        $pdf->Cell(25, 5, $debito == 0 ? "" : db_formatar($debito, 'f'), 0, 0, "R", 0);
        $pdf->Cell(25, 5, $credito == 0 ? "" : db_formatar($credito, 'f'), 0, 0, "R", 0);
    }
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 7);
}

/**
 * @param PDF $pdf
 */
function imprimeCabecalho(PDF $pdf)
{
    $pdf->SetFont('Arial', 'b', 8);
    $pdf->Cell(20, 5, "DATA", "T", 0, "C", 1);
    $pdf->Cell(85, 5, "CONTRAPARTIDA", "TL", 0, "C", 1);
    $pdf->Cell(25, 5, "PLANILHA", "TL", 0, "C", 1);
    $pdf->Cell(25, 5, "EMPENHO", "TL", 0, "C", 1);
    $pdf->Cell(25, 5, "ORDEM", "TL", 0, "C", 1);
    $pdf->Cell(25, 5, "CHEQUE", "TL", 0, "C", 1);
    $pdf->Cell(25, 5, "SLIP", "TL", 0, "C", 1);
    $pdf->Cell(25, 5, "DÉBITO", "TL", 0, "C", 1);
    $pdf->Cell(25, 5, "CRÉDITO", "TL", 0, "C", 1);
    $pdf->Ln();
    $pdf->Cell(20, 5, "", "TB", 0, "R", 1);
    $pdf->Cell(210, 5, "INFORMAÇÕES COMPLEMENTARES", "TLB", 0, "C", 1);
    $pdf->Cell(25, 5, "", "TLB", 0, "R", 1);
    $pdf->Cell(25, 5, "", "TB", 0, "R", 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 7);
}

/**
 * @param PDF $pdf
 * @param $saldoDiaDebito
 * @param $saldoDiaCredito
 * @param $saldoDiaFinal
 */
function imprimeTotalMovDia(PDF $pdf, $saldoDiaDebito, $saldoDiaCredito, $saldoDiaFinal)
{
    $pdf->SetFont('Arial', 'b', 8);
    $pdf->Cell(20, 5, "", "TB", 0, "R", 1);
    $pdf->Cell(210, 5, "TOTAIS DA MOVIMENTAÇÃO NO DIA:", "TB", 0, "R", 1);
    $pdf->Cell(25, 5, $saldoDiaDebito == 0 ? "" : db_formatar($saldoDiaDebito, 'f'), "TLB", 0, "R", 1);
    $pdf->Cell(25, 5, $saldoDiaCredito == 0 ? "" : db_formatar($saldoDiaCredito, 'f'), "TLB", 0, "R", 1);
    $pdf->Ln();
    $pdf->Cell(20, 5, "", "TB", 0, "R", 1);
    $pdf->Cell(210, 5, "SALDO NO DIA:", "TB", 0, "R", 1);
    $pdf->Cell(50, 5, $saldoDiaFinal == 0 ? "" : db_formatar($saldoDiaFinal, 'f'), "TLB", 0, "R", 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 7);
}

/**
 * @param PDF $pdf
 * @param $saldoDebitado
 * @param $saldoCreditado
 * @param $saldoAtual
 */
function imprimeTotalMovConta(PDF $pdf, $saldoDebitado, $saldoCreditado, $saldoAtual)
{
    $pdf->SetFont('Arial', 'b', 8);
    $pdf->Cell(20, 5, "", "TB", 0, "R", 1);
    $pdf->Cell(210, 5, "TOTAIS DA MOVIMENTAÇÃO 1:", "TB", 0, "R", 1);
    $pdf->Cell(25, 5, $saldoDebitado == 0 ? "" : db_formatar($saldoDebitado, 'f'), "TLB", 0, "R", 1);
    $pdf->Cell(25, 5, $saldoCreditado == 0 ? "" : db_formatar($saldoCreditado, 'f'), "TB", 0, "R", 1);
    $pdf->Ln();
    $pdf->Cell(20, 5, "", "TB", 0, "R", 1);
    $pdf->Cell(210, 5, "SALDO FINAL:", "TB", 0, "R", 1);
    $pdf->Cell(50, 5, $saldoAtual == 0 ? "0,00" : db_formatar($saldoAtual, 'f'), "TLB", 0, "R", 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 7);
}

/**
 * @param $handle
 * @param $codigo
 * @param $descricao
 * @param $debito
 * @param $credito
 * @param $lImprimeSaldo
 */
function imprimeContaTxt($handle, $codigo, $descricao, $debito, $credito, $lImprimeSaldo)
{
    $fields = array();
    $fields[0] = '';
    $fields[1] = '';
    $fields[2] = '';
    $fields[3] = '';
    $fields[4] = '';
    $fields[5] = '';
    $fields[6] = '';
    $fields[7] = '';
    $fields[8] = '';

    fputcsv($handle, $fields, ',', '"');

    $fields = array();
    $fields[0] = 'CONTA';
    $fields[1] = $codigo . " - " . $descricao;
    $fields[2] = '';
    $fields[3] = '';
    $fields[4] = '';
    $fields[5] = '';
    $fields[6] = '';
    $fields[7] = '';
    $fields[8] = '';
    if ($lImprimeSaldo) {
        $fields[6] = 'SALDO ANTERIOR:';
        $fields[7] = $debito == 0 ? "" : db_formatar($debito, 'f');
        $fields[8] = $credito == 0 ? "" : db_formatar($credito, 'f');
    }
    fputcsv($handle, $fields, ',', '"');
}

/**
 * @param $handle
 */
function imprimeCabecalhoTxt($handle)
{
    $fields = array();
    $fields[0] = "DATA";
    $fields[1] = "CONTRAPARTIDA";
    $fields[2] = "PLANILHA";
    $fields[3] = "EMPENHO";
    $fields[4] = "ORDEM";
    $fields[5] = "CHEQUE";
    $fields[6] = "SLIP";
    $fields[7] = "DÉBITO";
    $fields[8] = "CRÉDITO";

    fputcsv($handle, $fields, ',', '"');

    $fields = array();
    $fields[0] = '';
    $fields[1] = "INFORMAÇÕES COMPLEMENTARES";
    $fields[2] = '';
    $fields[3] = '';
    $fields[4] = '';
    $fields[5] = '';
    $fields[6] = '';
    $fields[7] = '';
    $fields[8] = '';
    fputcsv($handle, $fields, ',', '"');
}

/**
 * @param $handle
 * @param $saldoDiaDebito
 * @param $saldoDiaCredito
 * @param $saldoDiaFinal
 */
function imprimeTotalMovDiaTxt($handle, $saldoDiaDebito, $saldoDiaCredito, $saldoDiaFinal)
{
    $fields = array();
    $fields[0] = '';
    $fields[1] = '';
    $fields[2] = '';
    $fields[3] = '';
    $fields[4] = '';
    $fields[5] = '';
    $fields[6] = "TOTAIS DA MOVIMENTAÇÃO NO DIA:";
    $fields[7] = $saldoDiaDebito == 0 ? "" : db_formatar($saldoDiaDebito, 'f');
    $fields[8] = $saldoDiaCredito == 0 ? "" : db_formatar($saldoDiaCredito, 'f');
    fputcsv($handle, $fields, ',', '"');

    $fields = array();
    $fields[0] = '';
    $fields[1] = '';
    $fields[2] = '';
    $fields[3] = '';
    $fields[4] = '';
    $fields[5] = '';
    $fields[6] = "SALDO NO DIA:";
    $fields[7] = '';
    $fields[8] = $saldoDiaFinal == 0 ? "" : db_formatar($saldoDiaFinal, 'f');
    fputcsv($handle, $fields, ',', '"');
}

/**
 * @param $handle
 * @param $saldoDebitado
 * @param $saldoCreditado
 * @param $saldoAtual
 */
function imprimeTotalMovContaTxt($handle, $saldoDebitado, $saldoCreditado, $saldoAtual)
{
    $fields = array();
    $fields[0] = '';
    $fields[1] = '';
    $fields[2] = '';
    $fields[3] = '';
    $fields[4] = '';
    $fields[5] = '';
    $fields[6] = "TOTAIS DA MOVIMENTAÇÃO 2:";
    $fields[7] = $saldoDebitado == 0 ? "" : db_formatar($saldoDebitado, 'f');
    $fields[8] = $saldoCreditado == 0 ? "" : db_formatar($saldoCreditado, 'f');
    fputcsv($handle, $fields, ',', '"');

    $fields = array();
    $fields[0] = '';
    $fields[1] = '';
    $fields[2] = '';
    $fields[3] = '';
    $fields[4] = '';
    $fields[5] = '';
    $fields[6] = "SALDO FINAL:";
    $fields[7] = '';
    $fields[8] = $saldoAtual == 0 ? "" : db_formatar($saldoAtual, 'f');
    fputcsv($handle, $fields, ',', '"');
}
