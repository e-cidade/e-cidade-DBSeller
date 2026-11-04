<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25034AtualizaPlSaldoitensempenho extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function fc_saldoitens(integer, boolean,integer ,integer);
drop function fc_saldoitensempenho(integer, integer);
drop function fc_saldoitensempenho(integer);
drop function fc_saldoitensempenho();
drop function fc_saldoitensempenho_bi(integer, integer);
drop function fc_saldoitensordem(integer, integer);
drop function fc_saldoitensordem(integer);
drop     type tp_saldoitensempenho;

create type tp_saldoitensempenho as ( riSeqItem             integer, -- sequencial do item no empenho
                                      riNumEmp              integer, -- numero sequencial do emepnho
                                      riCodItem             integer, -- Codigo do item no empenho  e62_sequencial
                                      riCodEmp              integer, -- Codigo do empenho
                                      riAnoEmp              integer, -- Ano do Empenho
                                      ricoditemordem        integer, -- codigo sequencial do item na ordem de compra
                                      riCodOrdem            integer, -- Codigo da ordem de compra
                                      riCodmater            integer, -- Codigo do material
                                      rsDescr               varchar, -- Descricao do material
                                      rsDescrEmp            varchar, -- Descricao do material
                                      rnQuantIni            numeric, -- Quantidade inicial do item (e62_quant)
                                      rnSaldoItem           numeric, -- Saldo atual do item no empenho
                                      rnValorIni            numeric, -- Saldo iniciao do Valor do itens
                                      rnSaldoValor          numeric, -- Saldo atual do valor do item
                                      rnValorUni            numeric, -- Valor unitário do item
                                      rnSaldoEstoque        numeric, -- saldo do estoque do item
                                      rnValorEstoque        numeric, -- saldo atual do valor em estoque
                                      rnSaldoOrdem          numeric, -- Saldo dos itens da Ordem
                                      rnValorOrdem          numeric,  -- saldo do valor da ordem
                                      rnValorLiq            numeric,  -- valor Liquidado
                                      rnValorAnul           numeric,  -- valor anulado
                                      rnSaldoEntradaEmpenho numeric,  -- valor da diferenca entre o empenho e a entrada no estoque
                                      rlControlaQuantidade  boolean -- Booleano para saber se controla o serviço por quantidade (default: false)

                                     );

create function fc_saldoitensempenho(integer)
returns setof tp_saldoitensempenho as
$$
declare
  iNumEmp Alias for $1;
  rtp_saldoitensempenho record;
begin
  for rtp_saldoitensempenho in select * from fc_saldoitens(iNumEmp,false,null,1) loop
      return next rtp_saldoitensempenho;
  end loop;
  return;
end;
$$ language 'plpgsql';

create function fc_saldoitensempenho(integer, integer)
returns setof tp_saldoitensempenho as
$$
declare
  iNumEmp  Alias for $1;
  iCodItem Alias for $2;
  rtp_saldoitensempenho record;
begin
  for rtp_saldoitensempenho in select * from fc_saldoitens(iNumEmp,true,iCodItem,1) loop
      return next rtp_saldoitensempenho;
  end loop;
  return;
end;
$$ language 'plpgsql';

create function fc_saldoitensempenho()
returns setof tp_saldoitensempenho as
$$
declare
  rtp_saldoitensempenho record;
begin
  for rtp_saldoitensempenho in select * from fc_saldoitens(null,true,null,1) loop
      return next rtp_saldoitensempenho;
  end loop;
  return;
end;
$$ language 'plpgsql';

/*
 ** Função para trazer saldo dos itens dos empenhos
 ** de TODAS instituicoes em um Periodo de Anos, para
 ** ser utilizado na carga do BI
 **
 ** @param iAnoIni ano inicial do empenho
 ** @param iAnoIni ano final do empenho
 ** @return setOf do tipo saldoitensempnho.
 **
 ** @Author Fabrizio Mello
 ** since 02/09/2011
 */

create function fc_saldoitensempenho_bi(integer, integer)
returns setof tp_saldoitensempenho as
$$
declare
  iAnoIni alias for $1;
  iAnoFim alias for $2;

  iAno integer;
  rInstit record;
  rtp_saldoitensempenho record;
begin

  for rInstit in
    select codigo
      from db_config
     where exists (select 1 from empempenho where e60_instit = codigo)
     order by codigo
  loop
    perform fc_putsession('DB_instit', rInstit.codigo::text);

    raise info 'Processando Instituicao %', rInstit.codigo;

    for iAno in iAnoIni..iAnoFim
    loop
      perform fc_putsession('DB_anousu', iAno::text);
      raise info '>> Exercicio %', iAno;

      for rtp_saldoitensempenho in select * from fc_saldoitensempenho()
      loop
        return next rtp_saldoitensempenho;
      end loop;

    end loop;

  end loop;

  return;
end;
$$ language 'plpgsql';

/*
 ** Função para trazer saldo dos itens da Ordem de Compra
 ** @param iCodOrdem codigo da ordem de ompra
 ** @return setOf do tipo saldoitensempnho.

 **@Author Iuri Guntchniggg
 **since 18/02/2008
 */
create function fc_saldoItensOrdem(integer)
returns setof tp_saldoitensempenho as
$$
declare
  iCodOrdem Alias for $1;
  rtp_saldoitensempenho record;
begin
  for rtp_saldoitensempenho in select * from fc_saldoitens(iCodOrdem,false,null,2) loop
      return next rtp_saldoitensempenho;
  end loop;
  return;
end;
$$ language 'plpgsql';

create function fc_saldoItensOrdem(integer, integer)
returns setof tp_saldoitensempenho as
$$
declare
  iCodOrdem Alias for $1;
  iCodItem  alias for $2;
  rtp_saldoitensempenho record;
begin
  for rtp_saldoitensempenho in select * from fc_saldoitens(iCodOrdem,false,iCodItem,2) loop
      return next rtp_saldoitensempenho;
  end loop;
  return;
end;
$$ language 'plpgsql';
/*
 ** Função para trazer saldo dos itens do empenho ou da Ordem de compra
 ** @param iCodigo   codigo do empenho ou da ordem de compra a ser pesquisado
 ** @param bRaise    true para debug;
 ** @param iTipo     itipo do saldo 1 - Empenho 2 ordem de compra;
 ** @return setOf do tipo saldoitensempnho.

 **@Author Iuri Guntchniggg
 **since 18/02/2008
 */

create function fc_saldoitens(integer,boolean, integer, integer)
returns setof tp_saldoitensempenho as
$$
declare

   iCodigo             alias for $1;
   lRaiseFunc          alias for $2;
   iTipo               alias for $4;
   iCodItem            alias for $3;
   nQuantIni           numeric  default 0; -- saldo Inicial do Item
   nSaldoItem          numeric  default 0; -- saldo atual do Item;
   nValorIni           numeric  default 0; -- valor inicial do item (valor total dos itens (qtd*valor unitario)
   nSaldoValor         numeric  default 0; -- Saldo do valor do item
   nSaldoEstoque       numeric  default 0;
   nValorEstoque       numeric  default 0;
   nSaldoOrdem         numeric  default 0;
   nValorOrdem         numeric  default 0;
   lControlaQuantidade boolean;
   bRaise              boolean;
   sSQL                varchar;
   sSQLOrdem           varchar;
   sSQLanul            varchar;
   sSqlDesconto        varchar;
   sErro               varchar; -- mensagem de erro
   iInstit             integer;
   iAnoUsu             integer;
   sWhere              varchar default null;
   sJoin               varchar default null;
   iItemAnt            integer default null; -- codigo anterior do item de empenho (usando para controlar contagem dos itens;)

   rtp_saldoitensempenho tp_saldoitensempenho%ROWTYPE;
   rSaldoItens   record;
   rSaldoOrdem   record;
   rSaldoestoque record;
   rNotas        record;
   rAnulado      record;
   rDescontoItem      record;
begin

  iAnousu   = fc_getsession('DB_anousu');
  iInstit   = fc_getsession('DB_instit');
  bRaise  := ( case when fc_getsession('DB_debugon') is null then false else true end);
  if iAnousu is null then
    raise exception 'variavel de sessao DB_anousu nao inicializada';
  end if ;

  if iInstit is null then
    raise exception  'variavel de sessao DB_instit nao inicializada';
  end if ;

  sWhere = ' e60_instit = '||iInstit;
  if iTipo  = 1 then

     if iCodigo is not null then
       sWhere := sWhere || ' and e62_numemp   = '||iCodigo;
     else
       sWhere := sWhere || ' and e60_anousu   = '||iAnousu;
     end if;

     sJoin  := '';
  else
     if iCodigo is not null then
       sWhere := sWhere || ' and m52_codordem = '||iCodigo;
     end if;
     sJoin  := ' left join matordemitem on m52_numemp = e62_numemp
                                       and m52_sequen = e62_sequen';
  end if;
  if iCodItem is not null then
    sWhere := sWhere ||' and e62_sequencial = '||iCodItem;
  end if;
  sSQL = 'select pc01_descrmater,
                 pc01_servico,
                 e62_numemp,
                 e62_item,
                 e60_anousu,
                 e60_codemp,
                 e62_sequencial,
                 e62_sequen,
                 e62_quant,
                 e62_vltot,
                 e62_vlrun,
                 e62_descr,
                 e62_servicoquantidade
            From empempitem
                 inner join empempenho   on e62_numemp   = e60_numemp
                 inner join pcmater      on e62_item     = pc01_codmater
                 '||sJoin||'
           where '||sWhere||'
           order by e62_sequen';

       if bRaise then
        raise notice 'Inicializando variaveis - sql %', sSQL;
       end if;

       rtp_saldoitensempenho.riNumEmp              := 0;
       rtp_saldoitensempenho.riAnoEmp              := 0;
       rtp_saldoitensempenho.riCodOrdem            := 0;
       rtp_saldoitensempenho.riCodmater            := 0;
       rtp_saldoitensempenho.riCodItem             := 0;
       rtp_saldoitensempenho.riCodEmp              := 0;
       rtp_saldoitensempenho.rsDescr               := 0;
       rtp_saldoitensempenho.rsDescrEmp            := '';
       rtp_saldoitensempenho.riSeqItem             := 0;
       rtp_saldoitensempenho.rnQuantIni            := 0;
       rtp_saldoitensempenho.rnSaldoItem           := 0;
       rtp_saldoitensempenho.rnValorIni            := 0;
       rtp_saldoitensempenho.rnSaldoValor          := 0;
       rtp_saldoitensempenho.rnValorUni            := 0;
       rtp_saldoitensempenho.rnSaldoEstoque        := 0;
       rtp_saldoitensempenho.rnValorEstoque        := 0;
       rtp_saldoitensempenho.rnSaldoOrdem          := 0;
       rtp_saldoitensempenho.rnValorOrdem          := 0;
       rtp_saldoitensempenho.ricoditemordem        := 0;
       rtp_saldoitensempenho.rnSaldoEntradaEmpenho := 0;
       rtp_saldoitensempenho.rlControlaQuantidade  := false;

       if bRaise then
         raise notice 'Iniciando soma dos saldos';
       end if;

       for rSaldoItens in execute sSQL loop

         nSaldoValor   := 0;
         nSaldoItem    := 0;
         nSaldoEstoque := 0;
         nValorEstoque := 0;
         nSaldoOrdem   := 0;
         nValorOrdem   := 0;
         --selecionamos os totais da ordem.
         sSQLOrdem := 'select m52_codordem,
                              m52_codlanc,
                              coalesce(sum(m52_quant - (select coalesce(sum(m36_qtd),0)
                                                      from matordemitemanu
                                                     where m36_matordemitem = m52_codlanc)),0) as quantidadeordens,
                              coalesce(sum(m52_valor - (select coalesce(sum(m36_vrlanu),0)
                                                      from matordemitemanu
                                                     where m36_matordemitem = m52_codlanc)),0) as valorordens
                         from matordemitem
                               -- inner join matordem on matordem.m51_codordem = m52_codordem
                                                -- and m51_tipo                = 1
                               inner join empempitem           on m52_numemp        = e62_numemp
                                                              and m52_sequen        = e62_sequen
                               inner join empempenho           on e62_numemp        = e60_numemp
                     where e62_sequencial = '||rSaldoItens.e62_sequencial||'
                       and '||sWhere||'
                     group by m52_codordem,m52_codlanc';


         /*
          * IF Criado porque na anulação de empenho não estava levando em conta o saldo das ordens de compra automáticas para
          * calcular o saldo
          *
          * Criei a validação para mudar a variavel quando o menu acessado seja o de anulação de empenho.          *
          */
         if fc_getsession('DB_itemmenu_acessado') = 3494::text then

           sSQLOrdem := 'select m52_codordem,
                                m52_codlanc,
                                coalesce(sum(m52_quant - (select coalesce(sum(m36_qtd),0)
                                                        from matordemitemanu
                                                       where m36_matordemitem = m52_codlanc)),0) as quantidadeordens,
                                coalesce(sum(m52_valor - (select coalesce(sum(m36_vrlanu),0)
                                                        from matordemitemanu
                                                       where m36_matordemitem = m52_codlanc)),0) as valorordens
                           from matordemitem
                                 inner join matordem on matordem.m51_codordem = m52_codordem
                                                  and m51_tipo                = 1
                                 inner join empempitem           on m52_numemp        = e62_numemp
                                                                and m52_sequen        = e62_sequen
                                 inner join empempenho           on e62_numemp        = e60_numemp
                       where e62_sequencial = '||rSaldoItens.e62_sequencial||'
                         and '||sWhere||'
                       group by m52_codordem,m52_codlanc';
         end if;

         perform fc_debug('sWhere '||sWhere, true);

         for rSaldoOrdem in execute sSQLOrdem loop

           nSaldoItem                           = nSaldoItem    + coalesce((rSaldoOrdem.quantidadeordens), 0);
           nSaldoValor                          = nSaldoValor   + (rSaldoOrdem.valorordens);
           nSaldoOrdem                          = nSaldoOrdem   + rSaldoOrdem.quantidadeordens;
           nValorOrdem                          = nValorOrdem   + rSaldoOrdem.valorordens;
           rtp_saldoitensempenho.riCodOrdem     = rSaldoOrdem.m52_codordem;
           rtp_saldoitensempenho.ricoditemordem = rSaldoOrdem.m52_codlanc;

           /*
            * Calcula e aplica a anulação relativa ao desconto para cada ordem de compra de cada item.
            */
           sSqlDesconto := 'select coalesce(sum(e37_vlranu),0) as valor_anulado
                            from pagordemdescontoempanulado
                                 inner join pagordemdesconto on pagordemdesconto.e34_sequencial = pagordemdescontoempanulado.e06_pagordemdesconto
                                 inner join pagordemnota     on pagordemdesconto.e34_codord     = pagordemnota.e71_codord
                                 inner join empnotaord       on empnotaord.m72_codnota          = pagordemnota.e71_codnota
                                 inner join empanulado       on empanulado.e94_codanu           = pagordemdescontoempanulado.e06_empanulado
                                 inner join empanuladoitem   on empanuladoitem.e37_empanulado   = empanulado.e94_codanu
                                 inner join empempitem       on empempitem.e62_sequencial       = empanuladoitem.e37_empempitem
                                 inner join empempenho       on empempenho.e60_numemp           = empempitem.e62_numemp
                            where empempitem.e62_sequencial = ' || rSaldoItens.e62_sequencial ||
                                  ' and m72_codordem = ' || rSaldoOrdem.m52_codordem;

           for rDescontoItem in execute sSqlDesconto loop
             nSaldoValor := nSaldoValor - rDescontoItem.valor_anulado;
           end loop;

         end loop;


         --selecionamos o totais do estoque
         sSQLOrdem := 'select (coalesce(sum(m75_quant), 0) / m75_quantmult) as m71_quant,
                              coalesce(sum(m71_valor),0)  as m71_valor
                         from matordemitem
                              inner join empempitem           on m52_numemp            = e62_numemp
                                                             and m52_sequen            = e62_sequen
                              inner join empempenho           on e62_numemp            = e60_numemp
                              inner join matestoqueitemoc     on m73_codmatordemitem   = m52_codlanc
                                                             and m73_cancelado  is false
                              inner join matestoqueitem       on m73_codmatestoqueitem = m71_codlanc
                              inner  join matestoqueitemunid   on m75_codmatestoqueitem = m71_codlanc
                        where e62_sequencial = '||rSaldoItens.e62_sequencial||'
                          and '||sWhere||'
                        group by m52_codordem,m52_codlanc,m75_quantmult';


          for rSaldoestoque in execute sSQLOrdem loop

             nSaldoEstoque                        := nSaldoEstoque + rSaldoEstoque.m71_quant;
             nValorEstoque                        := nValorEstoque + rSaldoEstoque.m71_valor;

          end loop;

          nSaldoOrdem := (nSaldoOrdem - nSaldoEstoque);
          nValorOrdem := (nValorOrdem - nValorEstoque);
           -- selecionamos os totais liquidados do item - encontramos na empnotaitem
          select coalesce(sum(e72_vlrliq) - sum(e72_vlranu),0) as e37_vlrliq
            into rNotas
            from empempitem
                  left join empnotaitem    on e62_sequencial = e72_empempitem
            where e62_sequencial  = rSaldoItens.e62_sequencial;

          -- selecionamos os valores anulados do item  - encontramos na empanuladoitem
          select coalesce(sum(e37_vlranu),0) as e37_vlranu,
                 coalesce(sum(e37_qtd),0)    as e37_qtd
             into rAnulado
             from empempitem
                   left join empanuladoitem  on e62_sequencial = e37_empempitem
             where e62_sequencial  = rSaldoItens.e62_sequencial;

         perform fc_debug('nSaldoItem: '||nSaldoItem, true);
         perform fc_debug('nSaldoItem: '||rSaldoItens.e62_sequencial, true);
         perform fc_debug('nSaldoItem: '||rSaldoItens.e62_quant, true);
         perform fc_debug('nSaldoItem: '||rAnulado.e37_qtd, true);

          ------------------- Criamos as linhas do recordset
          rtp_saldoitensempenho.riNumEmp             := rSaldoItens.e62_numemp;
          rtp_saldoitensempenho.riSeqItem            := rSaldoItens.e62_sequen;
          rtp_saldoitensempenho.rnQuantIni           := rSaldoItens.e62_quant;
          rtp_saldoitensempenho.riCodItem            := rSaldoItens.e62_sequencial;
          rtp_saldoitensempenho.rsDescr              := rSaldoItens.pc01_descrmater;
          rtp_saldoitensempenho.rsDescrEmp           := rSaldoItens.e62_descr;
          rtp_saldoitensempenho.rnSaldoItem          := coalesce((rSaldoItens.e62_quant::numeric  - nSaldoItem::numeric  - rAnulado.e37_qtd::numeric), 0);
          rtp_saldoitensempenho.rnValorIni           := rSaldoItens.e62_vltot;
          rtp_saldoitensempenho.rnSaldoValor         := coalesce((rSaldoItens.e62_vltot::numeric - nSaldoValor::numeric - rAnulado.e37_vlranu::numeric)::numeric, 0);
          rtp_saldoitensempenho.rnValorUni           := rSaldoItens.e62_vlrun;
          rtp_saldoitensempenho.rnSaldoEstoque       := coalesce(nSaldoEstoque,0);
          rtp_saldoitensempenho.rnValorEstoque       := coalesce(nValorEstoque,0);
          rtp_saldoitensempenho.rnSaldoOrdem         := coalesce(nSaldoOrdem,0);
          rtp_saldoitensempenho.rnValorOrdem         := coalesce(nValorOrdem,0);
          rtp_saldoitensempenho.riCodMater           := rSaldoItens.e62_item;
          rtp_saldoitensempenho.riCodEmp             := rSaldoItens.e60_codemp;
          rtp_saldoitensempenho.riAnoEmp             := rSaldoItens.e60_anousu;
          rtp_saldoitensempenho.rnValorLiq           := rNotas.e37_vlrliq;
          rtp_saldoitensempenho.rnValorAnul          := rAnulado.e37_vlranu;
          rtp_saldoitensempenho.rlControlaQuantidade := rSaldoItens.e62_servicoquantidade;
          -- se o item for servico, e o quantidade do empenho for 1 sempre teremos  1 item  de saldo do item. apenas alteramos valor.
          if rSaldoItens.pc01_servico is true then

            /**
             * Caso for serviço e este não for controlado por quantidade, setamos o saldo para 1 sempre
             */
            if rSaldoItens.e62_servicoquantidade is false then

             rtp_saldoitensempenho.rnSaldoItem    := 1;
             rtp_saldoitensempenho.rnSaldoOrdem   := 1;

            end if;

            rtp_saldoitensempenho.rnSaldoEstoque := 1;

          end if;

          if bRaise then
             raise notice 'Calculando item % quant: % saldo:%', rSaldoItens.pc01_descrmater,rSaldoItens.e62_quant, coalesce(nSaldoItem,0);
             raise notice '    Saldo Estoque: % Valor Estoque: % saldo Ordem:% valor ordem: %',nSaldoEstoque, nValorEstoque,nSaldoEstoque, nValorEstoque;
          end if;

          if rtp_saldoitensempenho.rnSaldoEstoque = (rtp_saldoitensempenho.rnQuantIni -rAnulado.e37_qtd) then

            rtp_saldoitensempenho.rnSaldoEntradaEmpenho := round(rtp_saldoitensempenho.rnValorIni,2) - round(rtp_saldoitensempenho.rnValorEstoque, 2)
                                                         - round(rAnulado.e37_vlranu, 2);
            /**
             * Calculamos o valor a abater do item. caso o saldo do valor for negativo, indica que tivemos uma anulacao
             * do item do sem solicitacao de anulaçao. esse caso so deverá ocorrer quando a o empenho estiver entrado
             * totalmente no estoque, e sobrou alguns centavos para anulacao do empenho e o usuario realizou a anulacao
             * diretamente.
             */
            rtp_saldoitensempenho.rnSaldoEntradaEmpenho :=  rtp_saldoitensempenho.rnSaldoEntradaEmpenho - round(abs(rtp_saldoitensempenho.rnSaldoValor),2);
            if rtp_saldoitensempenho.rnSaldoEntradaEmpenho < 0  or rtp_saldoitensempenho.rnValorOrdem = 0 then
              rtp_saldoitensempenho.rnSaldoEntradaEmpenho = 0;
            end if;
            if rtp_saldoitensempenho.rnSaldoValor < 0 then
              rtp_saldoitensempenho.rnSaldoValor = 0;
            end if;
            else
              rtp_saldoitensempenho.rnSaldoEntradaEmpenho := 0;
            end if;
          return next rtp_saldoitensempenho;
       end loop;
   return ;
end;
$$ language 'plpgsql';
SQL
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
