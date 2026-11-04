<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M26266AjustarPlDescontoRecibo extends Migration
{
    public function up()
    {
        $sql = <<<SQL
drop function if exists fc_recibodesconto;
create or replace function arrecadacao.fc_recibodesconto(iNumpre integer,
    iNumpar integer,
    iNumtot integer,
    iReceit integer,
    iArreTipo integer,
    dEmissao date,
    dVencimento date,
        OUT nPercRetPrinc numeric,
        OUT nPercDescCorre numeric,
        OUT nPercDescJuros numeric,
        OUT nPercDescMulta numeric,
        OUT iRetCodRegra integer,
        OUT sRetDescrRegra varchar)
AS $$
declare

    iCadTipo      integer;
    iInstitSessao integer;
    iTipoValor    integer;
    iCodRegra     integer default 0;

    rArreDesconto record;

    -- Valores Totais para Calculo
    nVlrHis       numeric(15,2) default 0;
    nVlrCor       numeric(15,2) default 0;
    nVlrJur       numeric(15,2) default 0;
    nVlrMul       numeric(15,2) default 0;
    nVlrDes       numeric(15,2) default 0;
    nVlrTot       numeric(15,2) default 0;
    nVlrTotOri    numeric(15,2) default 0;

    -- Percentual de Desconto
    nPercRetorno  numeric(15,2) default 0;

    -- Percentuais de Descontos da Regra
    nPercDescCor  numeric(15,2) default 0;
    nPercDescJur  numeric(15,2) default 0;
    nPercDescMul  numeric(15,2) default 0;

    -- Percentuais de Desconto apos lancamento
    nDescontojurdepoislancamento    float8 default 0;
    nDescontomuldepoislancamento    float8 default 0;

    sSqlRegra     text   default '';

    sDescrRegra   varchar default '';

    lRaise        boolean default false;

begin

    -- inicializa variaveis de retorno
    nPercRetPrinc := nPercRetorno;
    nPercDescCorre := nPercDescCor;
    nPercDescJuros := nPercDescJur;
    nPercDescMulta := nPercDescMul;
    iRetCodRegra := iCodRegra;
    sRetDescrRegra := sDescrRegra;

    lRaise := ( case when fc_getsession('DB_debugon') is null then false else true end );
  
    if lRaise is true then
      if fc_getsession('db_debug') <> '' then
        perform fc_debug('<recibodesconto> Iniciando processamento...', lRaise, false, false);
      else
        perform fc_debug('<recibodesconto> Iniciando processamento...', lRaise, true, false);
      end if;
    end if;
  
    if dEmissao > dVencimento then
      if lRaise is true then
        perform fc_debug('<recibodesconto> debito vencido, nao calcula desconto',lRaise, false, false);
        perform fc_debug('<recibodesconto> retorno: '||nPercRetorno,lRaise, false, false);
      end if;
      return;
    end if;

    iInstitSessao := cast(fc_getsession('DB_instit') as integer);

    -- Busca Arredesconto
    select *
      into rArreDesconto
      from arredesconto
           inner join arreinstit on k00_numpre = k38_numpre
     where k38_numpre = iNumpre
       and k00_instit = iInstitSessao;

    if not found then
       return;
    end if;

    if lRaise is true then
       perform fc_debug('<recibodesconto> Arredesconto '||rArredesconto,lRaise, false, false);
       perform fc_debug('<recibodesconto> ---------------',lRaise, false, false);
    end if;

    -- Busca CadTipo do Debito
    select k03_tipo
      into iCadTipo
      from arretipo
     where k00_tipo   = iArreTipo
       and k00_instit = iInstitSessao;

    if not found then
      return;
    end if;

    if lRaise is true then
      perform fc_debug('<recibodesconto> CadTipo '||iCadTipo,lRaise, false, false);
      perform fc_debug('<recibodesconto> ---------------',lRaise, false, false);
    end if;

    if iCadTipo in (6,13,16,17) then
       if lRaise is true then
          perform fc_debug('<recibodesconto> PARCELAMENTO',lRaise, false, false);
          perform fc_debug('<recibodesconto> ---------------',lRaise, false, false);
       end if;

       select cast(v07_vlrhis as numeric),
              cast(v07_vlrcor - v07_vlrhis as numeric), -- (Valor da Correcao)
              cast(v07_vlrjur as numeric),
              cast(v07_vlrmul as numeric),
              cast(v07_vlrdes as numeric),
              cast(v07_valor  as numeric)
         into nVlrHis,
              nVlrCor,
              nVlrJur,
              nVlrMul,
              nVlrDes,
              nVlrTot
         from termo
        where v07_numpre = iNumpre
          and v07_instit = iInstitSessao;

       if lRaise is true then
         perform fc_debug('<recibodesconto> TERMO: VlrHis '||nVlrHis||' VlrCor '||nVlrCor||' VlrJur '||nVlrJur||' VlrMul '||nVlrMul||' VlrDes '||nVlrDes||' VlrTot '||nVlrTot,lRaise, false, false);
         perform fc_debug('<recibodesconto> ---------------',lRaise, false, false);
       end if;

       -- Salva Montante Original do Parcelamento
       nVlrTotOri := nVlrTot;

    --else
    --
    -- No futuro colocaremos a FC_CALCULA para podermos fazer o desconto para qualquer tipo de debito
    --
    end if;

    sSqlRegra := 'select tipovlr,
                         descjur,
                         descmul,
                         descvlr,
                         k40_codigo,
                         k40_descr
                    from cadtipoparc
                         join tipoparc on tipoparc.cadtipoparc = cadtipoparc.k40_codigo
                   where k40_codigo     = '|| rArreDesconto.k38_cadtipoparc ||'
                     and k40_aplicacao  = 2
                     and k40_instit     = '|| iInstitSessao ||'
                     and '||iNumtot||' <= maxparc
                  -- comentado para dar desconto independente da data fim da regra
                  -- and '||quote_literal(dEmissao)||' between k40_dtini and k40_dtfim
                  order by maxparc limit 1;';

    if lRaise is true then
       perform fc_debug('<recibodesconto> Busca regra: '|| sSqlRegra);
       perform fc_debug('<recibodesconto> ---------------');
    end if;

    -- Buscar Regra de Acordo com a CadTipoParc
    execute sSqlRegra into iTipoValor, nPercDescJur, nPercDescMul, nPercDescCor, iCodRegra, sDescrRegra;

    if not found then
       perform fc_debug('<recibodesconto> Nao encontrou regra na CadTipoParc: '|| sSqlRegra);
       return;
    end if;

    -- verifica se existe percentuais de desconto para juros e multa salvos na tabela termo
    select
        v07_perjur as percentualdescontojuros,
        v07_permul as percentualdescontomultas
      into nDescontojurdepoislancamento,
      nDescontomuldepoislancamento
    from termo
      where v07_numpre = iNumpre
        and v07_instit = iInstitSessao;

    if (nDescontojurdepoislancamento > 0) then
      nPercDescJur = nDescontojurdepoislancamento;
    end if;

    if (nDescontomuldepoislancamento > 0) then
      nPercDescMul = nDescontomuldepoislancamento;
    end if;

    if lRaise is true then
      perform fc_debug('<recibodesconto> Regra '||rArreDesconto.k38_cadtipoparc||'-'||sDescrRegra||' DescJur '||nPercDescJur||'  DescMul '||nPercDescMul||'  DescVlr '||nPercDescCor, lRaise, false, false);
      perform fc_debug('<recibodesconto> ---------------',lRaise, false, false);
      perform fc_debug('<recibodesconto> nVlrHis : '||nVlrHis||' nVlrCor : '||nVlrCor,lRaise, false, false);
      perform fc_debug('<recibodesconto> ---------------',lRaise, false, false);
    end if;
  
    --
    -- Se o Tipo de valor for = 2 entao o valor a ser utilizado deve ser o valor corrigido (historio + correcao)
    --   caso contrario deve ser o valor da correcao
    --
    if iTipoValor = 2 then
      nVlrCor := ( nVlrHis + nVlrCor );
      if lRaise then
        perform fc_debug('<recibodesconto> Calculando percentual com valor corrigido (historico + correcao) Valor Encontrado : '||nVlrCor,lRaise, false, false);
        perform fc_debug('<recibodesconto> ---------------',lRaise, false, false);
      end if;
    end if;

    -- Recalcula Valores Deduzindo os Descontos
    nVlrJur := nVlrJur - round( (nVlrJur * nPercDescJur)/100, 2 );
    nVlrMul := nVlrMul - round( (nVlrMul * nPercDescMul)/100, 2 );
    nVlrCor := nVlrCor - round( (nVlrCor * nPercDescCor)/100, 2 );

    -- Refaz valor do montante do parcelamento
    if  iTipoValor = 2 then
      nVlrTot := round( nVlrCor + nVlrJur + nVlrMul - nVlrDes, 2);
    else
      nVlrTot := round( nVlrHis + nVlrCor + nVlrJur + nVlrMul - nVlrDes, 2);
    end if;
  
    if lRaise then
      perform fc_debug('<recibodesconto> nVlrTot : '||nVlrTot,lRaise, false, false);
    end if;

    nPercRetorno := abs(100.00 - round( (nVlrTot * 100) / nVlrTotOri, 2 ));

    if lRaise is true then
      perform fc_debug('<recibodesconto> JUROS: Desconto '||nPercDescJur||' Valor Com Desconto '||nVlrJur,lRaise, false, false);
      perform fc_debug('<recibodesconto> MULTA: Desconto '||nPercDescMul||' Valor Com Desconto '||nVlrMul,lRaise, false, false);
      perform fc_debug('<recibodesconto> VALOR: Desconto '||nPercDescCor||' Valor Com Desconto '||nVlrCor,lRaise, false, false);
      perform fc_debug('<recibodesconto> ---------------'                                                ,lRaise, false, false);
      perform fc_debug('<recibodesconto> TOTAL ANTES DESCONTO: '||nVlrTotOri                             ,lRaise, false, false);
      perform fc_debug('<recibodesconto> TOTAL APOS  DESCONTO: '||nVlrTot                                ,lRaise, false, false);
      perform fc_debug('<recibodesconto> ---------------'                                                ,lRaise, false, false);
      perform fc_debug('<recibodesconto> Percentual de Desconto: '||nPercRetorno                         ,lRaise, false, false);
      perform fc_debug('<recibodesconto> '                                                               ,lRaise, false, false);
      perform fc_debug('<recibodesconto> Fim do processamento'                                           ,lRaise, false, true);
    end if;

    nPercRetPrinc := nPercRetorno;
    nPercDescCorre := nPercDescCor;
    nPercDescJuros := nPercDescJur;
    nPercDescMulta := nPercDescMul;
    iRetCodRegra := iCodRegra;
    sRetDescrRegra := sDescrRegra;

end;
$$ language 'plpgsql';
SQL;
        $this->executeQuery($sql);
    }

    public function down()
    {
        $sql = <<<SQL
drop function if exists fc_recibodesconto;
create or replace function arrecadacao.fc_recibodesconto(iNumpre integer,
    iNumpar integer,
    iNumtot integer,
    iReceit integer,
    iArreTipo integer,
    dEmissao date,
    dVencimento date,
        OUT nPercRetPrinc numeric,
        OUT nPercDescCorre numeric,
        OUT nPercDescJuros numeric,
        OUT nPercDescMulta numeric,
        OUT iRetCodRegra integer,
        OUT sRetDescrRegra varchar)
AS $$
declare

    iCadTipo      integer;
    iInstitSessao integer;
    iTipoValor    integer;
    iCodRegra     integer default 0;

    rArreDesconto record;

    -- Valores Totais para Calculo
    nVlrHis       numeric(15,2) default 0;
    nVlrCor       numeric(15,2) default 0;
    nVlrJur       numeric(15,2) default 0;
    nVlrMul       numeric(15,2) default 0;
    nVlrDes       numeric(15,2) default 0;
    nVlrTot       numeric(15,2) default 0;
    nVlrTotOri    numeric(15,2) default 0;

    -- Percentual de Desconto
    nPercRetorno  numeric(15,2) default 0;

    -- Percentuais de Descontos da Regra
    nPercDescCor  numeric(15,2) default 0;
    nPercDescJur  numeric(15,2) default 0;
    nPercDescMul  numeric(15,2) default 0;

    sSqlRegra     text   default '';

    sDescrRegra   varchar default '';

    lRaise        boolean default false;

begin

    -- inicializa variaveis de retorno
    nPercRetPrinc := nPercRetorno;
    nPercDescCorre := nPercDescCor;
    nPercDescJuros := nPercDescJur;
    nPercDescMulta := nPercDescMul;
    iRetCodRegra := iCodRegra;
    sRetDescrRegra := sDescrRegra;

    lRaise := ( case when fc_getsession('DB_debugon') is null then false else true end );
  
    if lRaise is true then
      if fc_getsession('db_debug') <> '' then
        perform fc_debug('<recibodesconto> Iniciando processamento...', lRaise, false, false);
      else
        perform fc_debug('<recibodesconto> Iniciando processamento...', lRaise, true, false);
      end if;
    end if;
  
    if dEmissao > dVencimento then
      if lRaise is true then
        perform fc_debug('<recibodesconto> debito vencido, nao calcula desconto',lRaise, false, false);
        perform fc_debug('<recibodesconto> retorno: '||nPercRetorno,lRaise, false, false);
      end if;
      return;
    end if;

    iInstitSessao := cast(fc_getsession('DB_instit') as integer);

    -- Busca Arredesconto
    select *
      into rArreDesconto
      from arredesconto
           inner join arreinstit on k00_numpre = k38_numpre
     where k38_numpre = iNumpre
       and k00_instit = iInstitSessao;

    if not found then
       return;
    end if;

    if lRaise is true then
       perform fc_debug('<recibodesconto> Arredesconto '||rArredesconto,lRaise, false, false);
       perform fc_debug('<recibodesconto> ---------------',lRaise, false, false);
    end if;

    -- Busca CadTipo do Debito
    select k03_tipo
      into iCadTipo
      from arretipo
     where k00_tipo   = iArreTipo
       and k00_instit = iInstitSessao;

    if not found then
      return;
    end if;

    if lRaise is true then
      perform fc_debug('<recibodesconto> CadTipo '||iCadTipo,lRaise, false, false);
      perform fc_debug('<recibodesconto> ---------------',lRaise, false, false);
    end if;

    if iCadTipo in (6,13,16,17) then
       if lRaise is true then
          perform fc_debug('<recibodesconto> PARCELAMENTO',lRaise, false, false);
          perform fc_debug('<recibodesconto> ---------------',lRaise, false, false);
       end if;

       select cast(v07_vlrhis as numeric),
              cast(v07_vlrcor - v07_vlrhis as numeric), -- (Valor da Correcao)
              cast(v07_vlrjur as numeric),
              cast(v07_vlrmul as numeric),
              cast(v07_vlrdes as numeric),
              cast(v07_valor  as numeric)
         into nVlrHis,
              nVlrCor,
              nVlrJur,
              nVlrMul,
              nVlrDes,
              nVlrTot
         from termo
        where v07_numpre = iNumpre
          and v07_instit = iInstitSessao;

       if lRaise is true then
         perform fc_debug('<recibodesconto> TERMO: VlrHis '||nVlrHis||' VlrCor '||nVlrCor||' VlrJur '||nVlrJur||' VlrMul '||nVlrMul||' VlrDes '||nVlrDes||' VlrTot '||nVlrTot,lRaise, false, false);
         perform fc_debug('<recibodesconto> ---------------',lRaise, false, false);
       end if;

       -- Salva Montante Original do Parcelamento
       nVlrTotOri := nVlrTot;

    --else
    --
    -- No futuro colocaremos a FC_CALCULA para podermos fazer o desconto para qualquer tipo de debito
    --
    end if;

    sSqlRegra := 'select tipovlr,
                         descjur,
                         descmul,
                         descvlr,
                         k40_codigo,
                         k40_descr
                    from cadtipoparc
                         join tipoparc on tipoparc.cadtipoparc = cadtipoparc.k40_codigo
                   where k40_codigo     = '|| rArreDesconto.k38_cadtipoparc ||'
                     and k40_aplicacao  = 2
                     and k40_instit     = '|| iInstitSessao ||'
                     and '||iNumtot||' <= maxparc
                  -- comentado para dar desconto independente da data fim da regra
                  -- and '||quote_literal(dEmissao)||' between k40_dtini and k40_dtfim
                  order by maxparc limit 1;';

    if lRaise is true then
       perform fc_debug('<recibodesconto> Busca regra: '|| sSqlRegra);
       perform fc_debug('<recibodesconto> ---------------');
    end if;

    -- Buscar Regra de Acordo com a CadTipoParc
    execute sSqlRegra into iTipoValor, nPercDescJur, nPercDescMul, nPercDescCor, iCodRegra, sDescrRegra;

    if not found then
       perform fc_debug('<recibodesconto> Nao encontrou regra na CadTipoParc: '|| sSqlRegra);
       return;
    end if;

    if lRaise is true then
      perform fc_debug('<recibodesconto> Regra '||rArreDesconto.k38_cadtipoparc||'-'||sDescrRegra||' DescJur '||nPercDescJur||'  DescMul '||nPercDescMul||'  DescVlr '||nPercDescCor, lRaise, false, false);
      perform fc_debug('<recibodesconto> ---------------',lRaise, false, false);
      perform fc_debug('<recibodesconto> nVlrHis : '||nVlrHis||' nVlrCor : '||nVlrCor,lRaise, false, false);
      perform fc_debug('<recibodesconto> ---------------',lRaise, false, false);
    end if;
  
    --
    -- Se o Tipo de valor for = 2 entao o valor a ser utilizado deve ser o valor corrigido (historio + correcao)
    --   caso contrario deve ser o valor da correcao
    --
    if iTipoValor = 2 then
      nVlrCor := ( nVlrHis + nVlrCor );
      if lRaise then
        perform fc_debug('<recibodesconto> Calculando percentual com valor corrigido (historico + correcao) Valor Encontrado : '||nVlrCor,lRaise, false, false);
        perform fc_debug('<recibodesconto> ---------------',lRaise, false, false);
      end if;
    end if;

    -- Recalcula Valores Deduzindo os Descontos
    nVlrJur := nVlrJur - round( (nVlrJur * nPercDescJur)/100, 2 );
    nVlrMul := nVlrMul - round( (nVlrMul * nPercDescMul)/100, 2 );
    nVlrCor := nVlrCor - round( (nVlrCor * nPercDescCor)/100, 2 );

    -- Refaz valor do montante do parcelamento
    if  iTipoValor = 2 then
      nVlrTot := round( nVlrCor + nVlrJur + nVlrMul - nVlrDes, 2);
    else
      nVlrTot := round( nVlrHis + nVlrCor + nVlrJur + nVlrMul - nVlrDes, 2);
    end if;
  
    if lRaise then
      perform fc_debug('<recibodesconto> nVlrTot : '||nVlrTot,lRaise, false, false);
    end if;

    nPercRetorno := abs(100.00 - round( (nVlrTot * 100) / nVlrTotOri, 2 ));

    if lRaise is true then
      perform fc_debug('<recibodesconto> JUROS: Desconto '||nPercDescJur||' Valor Com Desconto '||nVlrJur,lRaise, false, false);
      perform fc_debug('<recibodesconto> MULTA: Desconto '||nPercDescMul||' Valor Com Desconto '||nVlrMul,lRaise, false, false);
      perform fc_debug('<recibodesconto> VALOR: Desconto '||nPercDescCor||' Valor Com Desconto '||nVlrCor,lRaise, false, false);
      perform fc_debug('<recibodesconto> ---------------'                                                ,lRaise, false, false);
      perform fc_debug('<recibodesconto> TOTAL ANTES DESCONTO: '||nVlrTotOri                             ,lRaise, false, false);
      perform fc_debug('<recibodesconto> TOTAL APOS  DESCONTO: '||nVlrTot                                ,lRaise, false, false);
      perform fc_debug('<recibodesconto> ---------------'                                                ,lRaise, false, false);
      perform fc_debug('<recibodesconto> Percentual de Desconto: '||nPercRetorno                         ,lRaise, false, false);
      perform fc_debug('<recibodesconto> '                                                               ,lRaise, false, false);
      perform fc_debug('<recibodesconto> Fim do processamento'                                           ,lRaise, false, true);
    end if;

    nPercRetPrinc := nPercRetorno;
    nPercDescCorre := nPercDescCor;
    nPercDescJuros := nPercDescJur;
    nPercDescMulta := nPercDescMul;
    iRetCodRegra := iCodRegra;
    sRetDescrRegra := sDescrRegra;

end;
$$ language 'plpgsql';
SQL;
        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
