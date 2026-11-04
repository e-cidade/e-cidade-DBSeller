<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M26487AjustarDescontoRecibo extends Migration
{
    public function up()
    {
        $sql = <<<SQL
drop function IF EXISTS fc_recibo(integer,date,date,integer);
drop   type tp_recibo;

create type tp_recibo as ( rvMensagem varchar(100),
                           rlErro     boolean );

create or replace function fc_recibo(integer,date,date,integer) returns tp_recibo  as
$$

DECLARE

  NUMPRE                ALIAS FOR $1;
  DTEMITE               ALIAS FOR $2;
  DTVENC                ALIAS FOR $3;
  ANOUSU                ALIAS FOR $4;

  iFormaCorrecao        integer default 2;
  TEM_DESCONTO          INTEGER DEFAULT 0;
  iInstit               integer;
  iExerc                integer;
  iRegraIptu            integer;
  iRegraIss             integer;
  NUMCGM                INTEGER;
  RECEITA               INTEGER;
  K03_RECMUL            INTEGER;
  K03_RECJUR            INTEGER;
  V_K00_HIST            INTEGER;
  QUAL_OPER             INTEGER;
  RECEITA_JUR           INTEGER;
  RECEITA_MUL           INTEGER;
  iTipoVlr              INTEGER;
  V_CADTIPOPARC         INTEGER;
  V_CADTIPOPARC_FORMA   INTEGER;
  NUMPAR                INTEGER;
  NUMTOT                INTEGER;
  NUMDIG                INTEGER;
  ARRETIPO              INTEGER;
  CODBCO                INTEGER;

  RECORD_NUMPRE         RECORD;
  RECORD_ALIAS          RECORD;
  RECORD_GRAVA          RECORD;
  RECORD_NUMPREF        RECORD;
  RECORD_UNICA          RECORD;
  v_composicao          record;
  aPercArreDesconto     record;

  VALOR_RECEITA         FLOAT8;
  VALOR_RECEITA_ORI     FLOAT8;
  VALOR_RECEITAORI      FLOAT8;
  CORRECAOORI           FLOAT8;
  DESCONTO              FLOAT8;
  TOTPERC               FLOAT8;
  DESC_VALOR_RECEITA    FLOAT8 DEFAULT 0;
  CORRECAO              FLOAT8 DEFAULT 0;
  DESC_CORRECAO         FLOAT8 DEFAULT 0;
  JURO                  FLOAT8 DEFAULT 0;
  MULTA                 FLOAT8 DEFAULT 0;
  vlrjuroparc           FLOAT8 DEFAULT 0;
  vlrmultapar           FLOAT8 DEFAULT 0;
  nDescontoCorrigido    FLOAT8 default 0;
  PERCDESCJUR           FLOAT8 DEFAULT 0;
  PERCDESCMUL           FLOAT8 DEFAULT 0;
  PERCDESCVLR           FLOAT8 DEFAULT 0;
  nPercArreDesconto     FLOAT8 DEFAULT 0;
  VLRJUROS              FLOAT8 default 0;
  VLRMULTA              FLOAT8 default 0;
  VLRDESCONTO           FLOAT8 default 0;

  DTOPER                DATE;
  DATAVENC              DATE;

  CODAGE                CHAR(5);
  NUMERO_ERRO           char(200);

  NUMBCO                VARCHAR(15);
  SQLRECIBO             VARCHAR(400);

  numpreValorTotal      numeric       default 0;
  nComposCorrecao       numeric(15,2) default 0;
  nComposJuros          numeric(15,2) default 0;
  nComposMulta          numeric(15,2) default 0;
  nCorreComposJuros     numeric(15,2) default 0;
  nCorreComposMulta     numeric(15,2) default 0;

  rtp_recibo            tp_recibo%ROWTYPE;

  USASISAGUA            BOOLEAN;
  ISSQNVARIAVEL         BOOLEAN;
  UNICA                 BOOLEAN DEFAULT FALSE;
  PROCESSA              BOOLEAN DEFAULT FALSE;
  lRaise                boolean default false;
  lParcelamento         boolean default false;
  lReceitaPossuiJuro    boolean default false;
  lReceitaPossuiMulta   boolean default false;

BEGIN

  lRaise := ( case when fc_getsession('DB_debugon') is null then false else true end );
  if lRaise is true then
    if fc_getsession('db_debug') <> '' then
      perform fc_debug('<recibo> Inicio do processamento do recibo...', lRaise, false, false);
    else
      perform fc_debug('<recibo> Inicio do processamento do recibo...', lRaise, true, false);
    end if;
  end if;

  select cast( fc_getsession('DB_instit') as integer )
  into iInstit;

  select cast( fc_getsession('DB_anousu') as integer )
  into iExerc;

  select db21_regracgmiptu::integer from db_config where codigo = iInstit
  into iRegraIptu;

  select db21_regracgmiss::integer from db_config where codigo = iInstit
  into iRegraIss;

  select db21_usasisagua
  into USASISAGUA
  from db_config
  where codigo = iInstit;

  if lRaise is true then
    perform fc_debug('<recibo> Numpre ...............:'||NUMPRE,  lRaise, false, false);
    perform fc_debug('<recibo> Data de Emissao ......:'||DTEMITE, lRaise, false, false);
    perform fc_debug('<recibo> Data de Vencimento ...:'||DTVENC,  lRaise, false, false);
    perform fc_debug('<recibo> AnoUsu ...............:'||ANOUSU,  lRaise, false, false);
  end if;

  select k03_separajurmulparc
  into iFormaCorrecao
  from numpref
  where k03_instit = iInstit
        and k03_anousu = iExerc;

  FOR RECORD_NUMPREF IN SELECT *
                        FROM NUMPREF
                        WHERE K03_ANOUSU = ANOUSU
  LOOP
    RECEITA_JUR := RECORD_NUMPREF.K03_RECJUR;
    RECEITA_MUL := RECORD_NUMPREF.K03_RECMUL;
  END LOOP;

  if lRaise is true then
    perform fc_debug('<recibo>'                                 ,lRaise, false, false);
    perform fc_debug('<recibo> Receita para Juro:'||RECEITA_JUR ,lRaise, false, false);
    perform fc_debug('<recibo> Receita para Multa:'||RECEITA_MUL,lRaise, false, false);
    perform fc_debug('<recibo>'                                 ,lRaise, false, false);
  end if;

  perform k00_numpre
  from recibo
  where k00_numnov = numpre LIMIT 1;
  if found then

    rtp_recibo.rvMensagem    := '4 - Erro ao gerar recibo. Contate suporte!';
    rtp_recibo.rlErro        := true;

    if lRaise is true then
      perform fc_debug('<recibo> Encontrados registros do numpre na tabela recibo'           , lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, false);
      perform fc_debug('<recibo> 5 - Fim do processamento - Retorno: '||rtp_recibo.rvMensagem, lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, true);
    end if;

    return  rtp_recibo;

  end if;

  perform 1
  from db_reciboweb
  where k99_numpre_n = numpre limit 1;
  if not found then

    rtp_recibo.rvMensagem    := '2 - Erro ao gerar recibo. Contate suporte!';
    rtp_recibo.rlErro        := true;

    if lRaise is true then
      perform fc_debug('<recibo> Nao encontrados registros do numpre na tabela db_reciboweb' , lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, false);
      perform fc_debug('<recibo> 2 - Fim do processamento - Retorno: '||rtp_recibo.rvMensagem, lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, true);
    end if;

    return  rtp_recibo;

  end if;

  if lRaise is true then
    perform fc_debug('<recibo> Encontrados registros do numpre '||NUMPRE||' na tabela db_reciboweb, processando...',lRaise, false, false);
  end if;
  FOR RECORD_NUMPRE IN SELECT *
                       FROM DB_RECIBOWEB
                       WHERE K99_NUMPRE_N = NUMPRE
  LOOP

    CODBCO = RECORD_NUMPRE.K99_CODBCO;
    CODAGE = RECORD_NUMPRE.K99_CODAGE;
    --    NUMBCO = RECORD_NUMPRE.K99_NUMBCO;

    if lRaise is true then
      perform fc_debug('<recibo> '                                                           , lRaise, false, false);
      perform fc_debug('<recibo> -- Processando funcao fc_numbcoconvenio...'                 , lRaise, false, false);
    end if;
    select fc_numbcoconvenio(NUMBCO::integer) into NUMBCO;
    if lRaise is true then
      perform fc_debug('<recibo> Numbco : '||NUMBCO,lRaise, false, false);
      perform fc_debug('<recibo> -- Fim do processamento da funcao fc_numbcoconvenio...'     , lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, false);
    end if;

    TEM_DESCONTO = RECORD_NUMPRE.K99_DESCONTO;
    if lRaise is true then
      perform fc_debug('<recibo> TEM_DESCONTO: '||TEM_DESCONTO, lRaise, false, false);
    end if;

    if lRaise is true then
      perform fc_debug('<recibo> '                                                                  , lRaise, false, false);
      perform fc_debug('<recibo> '||lpad('',100,'-')                                                , lRaise, false, false);
      perform fc_debug('<recibo> 1 Buscando dados na tabela arrecad pelo Numpre '||RECORD_NUMPRE.K99_NUMPRE||' Parcela '||RECORD_NUMPRE.K99_NUMPAR||'...', lRaise, false, false);
    end if;

    drop table if exists w_receitas_com_juros_e_multa;
    create temp table w_receitas_com_juros_e_multa as 
      select
          distinct k00_receit,
          termodiv.juros > 0 as possuijuros,
          termodiv.multa > 0 as possuimulta
      from
          termo
          inner join termodiv ON termodiv.parcel = termo.v07_parcel
          inner join divida on v01_coddiv = coddiv
          and v01_instit = 1
          inner join arreold on v01_numpre = arreold.k00_numpre
          and v01_numpar = arreold.k00_numpar
          and arreold.k00_valor > 0
          inner join arretipo on arreold.k00_tipo = arretipo.k00_tipo
          and arretipo.k00_instit = 1
          inner join proced on v01_proced = v03_codigo
      where
          v07_numpre = RECORD_NUMPRE.K99_NUMPRE;

    FOR RECORD_UNICA IN SELECT DISTINCT
                          K00_NUMPRE,
                          K00_NUMPAR
                        FROM ARRECAD
                        WHERE K00_NUMPRE = RECORD_NUMPRE.K99_NUMPRE
                              AND CASE
                                  WHEN RECORD_NUMPRE.K99_NUMPAR = 0 THEN
                                    TRUE
                                  ELSE
                                    K00_NUMPAR = RECORD_NUMPRE.K99_NUMPAR
                                  END
    LOOP

      if lRaise is true then
        perform fc_debug('<recibo> Encontrou dados, Processa = true'                                  , lRaise, false, false);
        perform fc_debug('<recibo> Nnumpre: '||RECORD_NUMPRE.K99_NUMPRE||' - Numpar: '||RECORD_NUMPRE.K99_NUMPAR||' - processa: '||PROCESSA,lRaise, false, false);
      end if;
      PROCESSA := TRUE;

      IF RECORD_NUMPRE.K99_NUMPAR = 0 THEN
        UNICA := TRUE;

      ELSE
        IF RECORD_NUMPRE.K99_NUMPAR != RECORD_UNICA.K00_NUMPAR THEN
          if lRaise is true then
            perform fc_debug('<recibo> Parcela ('||RECORD_NUMPRE.K99_NUMPAR||') da tabela db_reciboweb diferente da parcela ('||RECORD_UNICA.K00_NUMPAR||') do arrecad', lRaise, false, false);
          end if;
          PROCESSA := FALSE;
        END IF;

      END IF;

      NUMPAR := RECORD_UNICA.K00_NUMPAR;

      IF PROCESSA = TRUE THEN

        if lRaise is true then
          perform fc_debug('<recibo> 2 Buscando dados na tabela arrecad pelo Numpre '||RECORD_NUMPRE.K99_NUMPRE||' Parcela '||NUMPAR||'...', lRaise, false, false);
        end if;

        FOR RECORD_ALIAS IN
        SELECT K00_RECEIT,
          K00_DTOPER,
          ( select rinumcgm from fc_socio_promitente(K00_NUMPRE, true, iRegraIptu, iRegraIss) limit 1 )as K00_NUMCGM,
          fc_calculavenci(k00_numpre,k00_numpar,K00_DTVENC,DTEMITE) AS K00_DTVENC,
          K00_NUMPRE,
          K00_NUMPAR,
          min(K00_hist) as K00_hist,
          (select sum(k00_valor)
           from arrecad as a
           where a.k00_numpre = arrecad.k00_numpre
                 and a.k00_numpar = arrecad.k00_numpar
                 and a.k00_receit = arrecad.k00_receit
                 and a.k00_tipo   = arrecad.k00_tipo ) as k00_valor,
          K00_TIPO
        FROM ARRECAD
        WHERE K00_NUMPRE = RECORD_NUMPRE.K99_NUMPRE
              AND K00_NUMPAR = NUMPAR
        group by K00_RECEIT,
          K00_DTOPER,
          K00_NUMCGM,
          fc_calculavenci(k00_numpre,k00_numpar,K00_DTVENC,DTEMITE),
          K00_NUMPRE,
          K00_NUMPAR,
          K00_TIPO
        ORDER BY K00_NUMPRE,K00_NUMPAR,K00_RECEIT
        LOOP

          if lRaise is true then

            perform fc_debug('<recibo> '                                                                  , lRaise, false, false);
            perform fc_debug('<recibo> Processando registros do Numpre '||RECORD_ALIAS.K00_NUMPRE||'...'  , lRaise, false, false);
            perform fc_debug('<recibo> Parcela .............:'||RECORD_ALIAS.K00_NUMPAR                   , lRaise, false, false);
            perform fc_debug('<recibo> Receita .............:'||RECORD_ALIAS.K00_RECEIT                   , lRaise, false, false);
            perform fc_debug('<recibo> Tipo ................:'||RECORD_ALIAS.K00_TIPO                     , lRaise, false, false);
            perform fc_debug('<recibo> Data de Operacao ....:'||RECORD_ALIAS.K00_DTOPER                   , lRaise, false, false);
            perform fc_debug('<recibo> Data de Vencimento ..:'||RECORD_ALIAS.K00_DTVENC                   , lRaise, false, false);
            perform fc_debug('<recibo> Valor da Receita ....:'||RECORD_ALIAS.K00_RECEIT                   , lRaise, false, false);
            perform fc_debug('<recibo> '                                                                  , lRaise, false, false);
            perform fc_debug('<recibo> Processa = true...'                                                , lRaise, false, false);

          end if;
          PROCESSA := TRUE;
          RECEITA  := RECORD_ALIAS.K00_RECEIT;
          ARRETIPO := RECORD_ALIAS.K00_TIPO;
          DTOPER   := RECORD_ALIAS.K00_DTOPER;
          NUMCGM   := RECORD_ALIAS.K00_NUMCGM;
          DATAVENC := RECORD_ALIAS.K00_DTVENC;
          VALOR_RECEITA := RECORD_ALIAS.K00_VALOR;

          if ((select count(k00_receit) 
              from w_receitas_com_juros_e_multa
              where k00_receit = RECEITA) > 0) then
            select 
              possuijuros,
              possuimulta
              into lReceitaPossuiJuro,lReceitaPossuiMulta
              from w_receitas_com_juros_e_multa where k00_receit = RECEITA
              limit 1;
          else
            lReceitaPossuiJuro = true;
            lReceitaPossuiMulta = true;
          end if;

          IF VALOR_RECEITA = 0 THEN
            SELECT Q05_VLRINF
            INTO VALOR_RECEITA
            FROM ISSVAR
            WHERE Q05_NUMPRE = RECORD_ALIAS.K00_NUMPRE
                  AND Q05_NUMPAR = RECORD_ALIAS.K00_NUMPAR;
            IF VALOR_RECEITA IS NULL THEN
              VALOR_RECEITA := 0;
            ELSE
              ISSQNVARIAVEL := TRUE;
            END IF;
          END IF;

          QUAL_OPER := 0;
          -- T24879: Se valor da receita nao for 0 (zero) ou
          -- recibo for proveniente de uma emissao geral de iss variavel
          -- continua geracao da recibopaga
          IF ( VALOR_RECEITA <> 0 OR RECORD_NUMPRE.K99_TIPO = 6 ) THEN

            FOR RECORD_GRAVA IN SELECT *
                                FROM ARRECAD
                                WHERE K00_NUMPRE = RECORD_NUMPRE.K99_NUMPRE
                                      AND K00_NUMPAR = NUMPAR
                                      AND K00_RECEIT = RECEITA
            LOOP

              IF QUAL_OPER = 0 THEN
                V_K00_HIST := RECORD_GRAVA.K00_HIST;
                NUMTOT := RECORD_GRAVA.K00_NUMTOT;
                NUMDIG  := RECORD_GRAVA.K00_NUMDIG;
                QUAL_OPER := 1;
              END IF;

            END LOOP;

            -- CALCULA CORRECAO
            if VALOR_RECEITA <> 0 then

              if iFormaCorrecao = 1 then

                VALOR_RECEITA_ORI = VALOR_RECEITA;

                if lRaise is true then
                  perform fc_debug('<recibo> Forma de correcao .......: '||iFormaCorrecao, lRaise, false, false);
                  perform fc_debug('<recibo> VALOR_RECEITA_ORI .......: '||VALOR_RECEITA_ORI, lRaise, false, false);
                  perform fc_debug('<recibo> VALOR_RECEITA ...: '||VALOR_RECEITA, lRaise, false, false);
                  perform fc_debug('<recibo> fc_retornacomposicao('||record_alias.k00_numpre||','||record_alias.k00_numpar||','||record_alias.k00_receit||','||record_alias.k00_hist||','||dtoper||','||dtvenc||','||anousu||','||datavenc||')', lRaise, false, false);
                end if;

                select coalesce(rnCorreComposJuros,0),
                  coalesce(rnCorreComposMulta,0),
                  coalesce(rnComposCorrecao,0),
                  coalesce(rnComposJuros,0),
                  coalesce(rnComposMulta,0)
                into nCorreComposJuros,
                  nCorreComposMulta,
                  nComposCorrecao,
                  nComposJuros,
                  nComposMulta
                from fc_retornacomposicao(record_alias.k00_numpre, record_alias.k00_numpar, record_alias.k00_receit, record_alias.k00_hist, dtoper, dtvenc, anousu, datavenc);

                if lRaise is true then
                  perform fc_debug('<recibo> 1=nComposCorrecao: '||nComposCorrecao||' - VALOR_RECEITA: '||VALOR_RECEITA,lRaise, false,false);
                end if;

                VALOR_RECEITA = VALOR_RECEITA + nComposCorrecao;
                if lRaise is true then
                  perform fc_debug('<recibo> 2=nComposCorrecao: '||nComposCorrecao||' - VALOR_RECEITA: '||VALOR_RECEITA||' - VALOR_RECEITA: '||VALOR_RECEITA,lRaise, false,false);
                  perform fc_debug('<recibo> 1 Chamando a funcao fc_corre...',lRaise, false,false);
                end if;

                CORRECAO := ROUND( FC_CORRE(RECEITA,DTOPER,VALOR_RECEITA,DTVENC,ANOUSU,DATAVENC) , 2 );

                if lRaise is true then
                  perform fc_debug('<recibo> CORRECAO 1: '||CORRECAO,lRaise, false,false);
                end if;

                CORRECAO := ROUND( CORRECAO - VALOR_RECEITA + nComposCorrecao, 2 );

                if lRaise is true then
                  perform fc_debug('<recibo> CORRECAO 2: '||CORRECAO||' - nCorreComposJuros: '||nCorreComposJuros||' - nCorreComposMulta: '||nCorreComposMulta,lRaise, false,false);
                end if;

                CORRECAO := CORRECAO + nCorreComposJuros + nCorreComposMulta;

                if lRaise is true then
                  perform fc_debug('<recibo> VALOR_RECEITA: '||VALOR_RECEITA||' VALOR_RECEITA: '||VALOR_RECEITA||' - CORRECAO 3: '||CORRECAO,lRaise, false,false);
                end if;

                VALOR_RECEITA = VALOR_RECEITA_ORI;

              else

                if lRaise is true then
                  perform fc_debug('<recibo> 2 Chamando a funcao fc_corre...',lRaise, false,false);
                end if;

                CORRECAO := ROUND(FC_CORRE(RECEITA, DTOPER, VALOR_RECEITA, DTVENC, ANOUSU, DATAVENC) - round(VALOR_RECEITA, 2), 2);

                if lRaise is true then
                  perform fc_debug('<recibo> Forma de correcao ..............: '||coalesce(iFormaCorrecao,0), lRaise, false, false);
                  perform fc_debug('<recibo> Receita ........................: '||RECEITA, lRaise, false, false);
                  perform fc_debug('<recibo> DtOper .........................: '||DTOPER, lRaise, false, false);
                  perform fc_debug('<recibo> Valor da receita para calculo ..: '||VALOR_RECEITA, lRaise, false, false);
                  perform fc_debug('<recibo> DtVencto .......................: '||DTVENC, lRaise, false, false);
                  perform fc_debug('<recibo> Ano ............................: '||ANOUSU, lRaise, false, false);
                  perform fc_debug('<recibo> Data para Vencimento ...........: '||DATAVENC, lRaise, false, false);
                  perform fc_debug('<recibo> Correcao .......................: '||CORRECAO, lRaise, false, false);
                end if;

              end if;

            else
              CORRECAO := 0;
            end if;

            if lRaise is true then
                perform fc_debug('<recibo> 2 - TEM_DESCONTO: '||TEM_DESCONTO,lRaise, false,false);
            end if;

            if TEM_DESCONTO > 0 then

              select sum(fc_calcula_total_arrecad(x.k99_numpre, DTVENC))
                into numpreValorTotal
                from (select distinct db_reciboweb.k99_numpre 
                        from db_reciboweb 
                       where db_reciboweb.k99_numpre_n = NUMPRE) as x;

              select tipoparc.descjur,
                     tipoparc.descmul,
                     tipoparc.descvlr,
                     cadtipoparc.k40_codigo,
                     cadtipoparc.k40_forma,
                     tipoparc.tipovlr
                into percdescjur,
                     percdescmul,
                     percdescvlr,
                     v_cadtipoparc,
                     v_cadtipoparc_forma,
                     iTipoVlr
                from cadtipoparc
                     inner join tipoparc on tipoparc.cadtipoparc = cadtipoparc.k40_codigo
               where DTEMITE between tipoparc.dtini and tipoparc.dtfim
                 and tipoparc.maxparc = 1
                 and fc_verifica_desconto_faixa_valor(numpreValorTotal::numeric, tipoparc.vlrmin::numeric, tipoparc.vlrmax::numeric)
                 and cadtipoparc.k40_codigo = TEM_DESCONTO;

              if lRaise is true then

                perform fc_debug('<recibo> DTEMITE '||DTEMITE                             ,lRaise, false, false);
                perform fc_debug('<recibo> numpreValorTotal '||numpreValorTotal           ,lRaise, false, false);
                perform fc_debug('<recibo> '                                              ,lRaise, false, false);
                perform fc_debug('<recibo> Desconto em Regra...'                          ,lRaise, false, false);
                perform fc_debug('<recibo> DTVENC ................:'||DTVENC              ,lRaise, false, false);
                perform fc_debug('<recibo> percdescjur ...........:'||percdescjur         ,lRaise, false, false);
                perform fc_debug('<recibo> percdescmul ...........:'||percdescmul         ,lRaise, false, false);
                perform fc_debug('<recibo> percdescvlr ...........:'||percdescvlr         ,lRaise, false, false);
                perform fc_debug('<recibo> v_cadtipoparc .........:'||v_cadtipoparc       ,lRaise, false, false);
                perform fc_debug('<recibo> v_cadtipoparc_forma ...:'||v_cadtipoparc_forma ,lRaise, false, false);
                perform fc_debug('<recibo> iTipoVlr ..............:'||iTipoVlr            ,lRaise, false, false);
                perform fc_debug('<recibo> numpreValorTotal ......:'||numpreValorTotal    ,lRaise, false, false);
                perform fc_debug('<recibo> TEM_DESCONTO ..........:'||TEM_DESCONTO        ,lRaise, false, false);
              end if;

            end if;

            if lRaise is true then
              perform fc_debug('<recibo> CORRECAO '||receita||'-'||dtoper||'-'||VALOR_RECEITA||'-'||VALOR_RECEITA||'-'||datavenc||'-'||dtvenc, lRaise, false, false);
            end if;

            CORRECAOORI := CORRECAO;
            VALOR_RECEITAORI := VALOR_RECEITA;

            --  Trabalhar neste if para utilizar a mesma logica da recibodesconto
            --   alterar o programa de emissao de recibo para selecionar
            --   a regra se o contribuinte for ou nao loteador

            -- Verificar se a receita possui 'valores adicionais' (Jurídico > Procedimentos > Processo do Foro > Valores Adicionais) lançados a um processo.
            -- Caso possua, deve-se desconsiderar descontos em cima dessa receita.
            -- Obs.: Esse caso se aplica para débitos de inicial.
            perform j150_receita
               from processoforomulta
                    inner join processoforoinicial on j150_processoforo = v71_processoforo
                    inner join inicialnumpre on v71_inicial = v59_inicial
              where j150_receita = RECEITA
                and v59_numpre   = RECORD_ALIAS.K00_NUMPRE;

            if found then
              percdescvlr := 0;
              percdescmul := 0;
              percdescjur := 0;
            end if;

            if percdescvlr is not null and percdescvlr > 0 then

              if iTipoVlr = 1 then

                DESC_CORRECAO := ROUND(CORRECAO * percdescvlr / 100,2);
                if lRaise is true then
                  perform fc_debug('<recibo> desconto na correcao 2: '||CORRECAO||' (-'||DESC_CORRECAO||') - VALOR_RECEITA: '||VALOR_RECEITA||' - VALOR_RECEITA: '||VALOR_RECEITA||' - PERCENTUAL: '||percdescvlr,lRaise, false,false);
                end if;
                if DESC_CORRECAO > 0 then
                  --

                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 01 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 01 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 01 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 01 - Receita ....: '||RECEITA, lRaise, false, false);
                    perform fc_debug('<recibo> 01 - Historico ..:  918', lRaise, false, false);
                    perform fc_debug('<recibo> 01 - Valor ......: '||(DESC_CORRECAO*-1), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  INSERT INTO RECIBOPAGA (k00_numcgm,
                                          k00_dtoper,
                                          k00_receit,
                                          k00_hist,
                                          k00_valor,
                                          k00_dtvenc,
                                          k00_numpre,
                                          k00_numpar,
                                          k00_numtot,
                                          k00_numdig,
                                          k00_conta,
                                          k00_dtpaga,
                                          k00_numnov)
                  VALUES (NUMCGM,
                    DTEMITE,
                    RECEITA,
                    918,
                    (DESC_CORRECAO*-1),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,NUMTOT,
                    NUMDIG,
                    0,
                          DTVENC,
                          NUMPRE);
                end if;
              elsif iTipoVlr = 2 then
                nDescontoCorrigido := ROUND((VALOR_RECEITA + CORRECAO) * percdescvlr / 100,2);
                if lRaise is true then
                  perform fc_debug('<recibo> desconto na correcao 2: '||CORRECAO||' (-'||DESC_CORRECAO||') - VALOR_RECEITA: '||VALOR_RECEITA||' - VALOR_RECEITA: '||VALOR_RECEITA||' - PERCENTUAL: '||percdescvlr,lRaise, false,false);
                end if;
                if nDescontoCorrigido > 0 then
                  --
                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 02 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 02 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 02 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 02 - Receita ....: '||RECEITA, lRaise, false, false);
                    perform fc_debug('<recibo> 02 - Historico ..:  918', lRaise, false, false);
                    perform fc_debug('<recibo> 02 - Valor ......: '||(nDescontoCorrigido*-1), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  INSERT INTO RECIBOPAGA (k00_numcgm,
                                          k00_dtoper,
                                          k00_receit,
                                          k00_hist,
                                          k00_valor,
                                          k00_dtvenc,
                                          k00_numpre,
                                          k00_numpar,
                                          k00_numtot,
                                          k00_numdig,
                                          k00_conta,
                                          k00_dtpaga,
                                          k00_numnov )
                  VALUES (NUMCGM,
                    DTEMITE,
                    RECEITA,
                    918,
                    (nDescontoCorrigido*-1),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,
                    NUMTOT,
                    NUMDIG,
                    0,
                          DTVENC,
                          NUMPRE);
                end if;
              end if;

              -- Se a forma de aplicacao da regra for pra loteamentos (= 3)
              -- entao aplica desconto no valor da receita (historico)
              if v_cadtipoparc_forma = 3 then
                DESC_VALOR_RECEITA := ROUND(VALOR_RECEITA * percdescvlr / 100,2);
                if DESC_VALOR_RECEITA > 0 then
                  if lRaise is true then
                    perform fc_debug('<recibo> desconto (3) - DESC_VALOR_RECEITA: '||DESC_VALOR_RECEITA,lRaise, false,false);
                  end if;
                  --
                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 03 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 03 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 03 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 03 - Receita ....: '||RECEITA, lRaise, false, false);
                    perform fc_debug('<recibo> 03 - Historico ..:  918', lRaise, false, false);
                    perform fc_debug('<recibo> 03 - Valor ......: '||(DESC_VALOR_RECEITA*-1), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  INSERT INTO RECIBOPAGA (k00_numcgm,
                                          k00_dtoper,
                                          k00_receit,
                                          k00_hist,
                                          k00_valor,
                                          k00_dtvenc,
                                          k00_numpre,
                                          k00_numpar,
                                          k00_numtot,
                                          k00_numdig,
                                          k00_conta,
                                          k00_dtpaga,
                                          k00_numnov)
                  VALUES (NUMCGM,
                    DTEMITE,
                    RECEITA,
                    918,
                    (DESC_VALOR_RECEITA*-1),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,
                    NUMTOT,
                    NUMDIG,
                    0,
                          DTVENC,
                          NUMPRE);
                end if;
              end if;

              if lRaise is true then
                perform fc_debug('<recibo> desconto na correcao 2: '||CORRECAO||' - VALOR_RECEITA: '||VALOR_RECEITA||' - VALOR_RECEITA: '||VALOR_RECEITA,lRaise, false,false);
              end if;

            end if;

            /**
             * final na manutencao
             *
             */
            if lRaise is true then
              perform fc_debug('<recibo> '                                                                     , lRaise, false, false);
              perform fc_debug('<recibo> - juro ....................: '||JURO||' - descjur: '||percdescjur     , lRaise, false, false);
              perform fc_debug('<recibo> - multa ...................: '||MULTA||' - descmul: '||percdescmul    , lRaise, false, false);
              perform fc_debug('<recibo> - correcao ................: '||CORRECAO||' - descvlr: '||percdescvlr , lRaise, false, false);
              perform fc_debug('<recibo> - VALOR_RECEITA ...........: '||VALOR_RECEITA                         , lRaise, false, false);
              perform fc_debug('<recibo> - VALOR_RECEITA ...: '||VALOR_RECEITA                 , lRaise, false, false);
              perform fc_debug('<recibo> - cadtipoparc: '||coalesce(v_cadtipoparc::varchar, 'NULL')            , lRaise, false, false);
              perform fc_debug('<recibo> '                                                                     , lRaise, false, false);
            end if;

            -- T24879: Se valor diferente de zero ou tipo recibo for da emissao geral do iss
            -- gera recibopaga normalmente
            IF (VALOR_RECEITA + CORRECAO) <> 0 OR RECORD_NUMPRE.K99_TIPO = 6 THEN

              if lRaise is true then

                perform fc_debug('<recibo> ', lRaise, false, false);
                perform fc_debug('<recibo> 04 - inserindo na recibopaga... ', lRaise, false, false);
                perform fc_debug('<recibo> 04 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                perform fc_debug('<recibo> 04 - Numpar .....: '||NUMPAR, lRaise, false, false);
                perform fc_debug('<recibo> 04 - Receita ....: '||RECEITA, lRaise, false, false);
                perform fc_debug('<recibo> 04 - Historico ..: '||V_K00_HIST + 100, lRaise, false, false);
                perform fc_debug('<recibo> 04 - Valor ......: '||ROUND(VALOR_RECEITA+CORRECAO,2), lRaise, false, false);
                perform fc_debug('<recibo> ', lRaise, false, false);

              end if;

              INSERT INTO RECIBOPAGA ( k00_numcgm,
                                       k00_dtoper,
                                       k00_receit,
                                       k00_hist  ,
                                       k00_valor ,
                                       k00_dtvenc,
                                       k00_numpre,
                                       k00_numpar,
                                       k00_numtot,
                                       k00_numdig,
                                       k00_conta ,
                                       k00_dtpaga,
                                       k00_numnov )
              VALUES ( NUMCGM,
                DTEMITE,
                RECEITA,
                V_K00_HIST + 100,
                ROUND(VALOR_RECEITA+CORRECAO,2),
                DATAVENC,
                RECORD_NUMPRE.K99_NUMPRE,
                NUMPAR,
                NUMTOT,
                NUMDIG,
                0,
                       DTVENC,
                       NUMPRE );

              -- CALCULA DESCONTO DA ARREDESCONTO
              perform v07_numpre
              from termo
              where v07_numpre = RECORD_NUMPRE.K99_NUMPRE;

              if found then
                lParcelamento := true;
              end if;

              if lParcelamento then

                -- Verifica desconto
                select nPercRetPrinc,
                       nPercDescCorre,
                       nPercDescJuros,
                       nPercDescMulta
                  into aPercArreDesconto
                from fc_recibodesconto(RECORD_NUMPRE.K99_NUMPRE,
                                       NUMPAR,
                                       NUMTOT,
                                       RECEITA,
                                       ARRETIPO,
                                       DTEMITE,
                                       fc_proximo_dia_util(DATAVENC));

                nPercArreDesconto = aPercArreDesconto.nPercRetPrinc;
                if (nComposCorrecao + nComposJuros + nComposMulta) > 0 then
                    nPercArreDesconto := aPercArreDesconto.nPercDescCorre;
                    percdescjur := aPercArreDesconto.nPercDescJuros;
                    percdescmul := aPercArreDesconto.nPercDescMulta;
                end if;

                if nPercArreDesconto > 0 then

                  if lRaise is true then
                     perform fc_debug('<recibo> desconto (4) - nPercArreDesconto: '||nPercArreDesconto,lRaise, false,false);
                  end if;

                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 05 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 05 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 05 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 05 - Receita ....: '||RECEITA, lRaise, false, false);
                    perform fc_debug('<recibo> 05 - Historico ..: 918', lRaise, false, false);
                    perform fc_debug('<recibo> 05 - Valor ......: '||ROUND(((ROUND(VALOR_RECEITA+CORRECAO,2) * nPercArreDesconto)/100),2) * -1, lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  -- se regra de desconto da apenas desconto em juros e/ou multa e o debito possui juros e/ou multa
                  if (percdescjur > 0 or percdescmul > 0) and (lReceitaPossuiJuro or lReceitaPossuiMulta) then
                    INSERT INTO RECIBOPAGA ( k00_numcgm,
                                             k00_dtoper,
                                             k00_receit,
                                             k00_hist  ,
                                             k00_valor ,
                                             k00_dtvenc,
                                             k00_numpre,
                                             k00_numpar,
                                             k00_numtot,
                                             k00_numdig,
                                             k00_conta ,
                                             k00_dtpaga,
                                             k00_numnov )
                    VALUES ( NUMCGM,
                      DTEMITE,
                      RECEITA,
                      918,
                      ROUND(((ROUND(VALOR_RECEITA+CORRECAO,2) * nPercArreDesconto)/100),2) * -1,
                      DATAVENC,
                      RECORD_NUMPRE.K99_NUMPRE,
                      NUMPAR,
                      NUMTOT,
                      NUMDIG,
                      0,
                             DTVENC,
                             NUMPRE );
                  end if;
                end if;
              end if;

            END IF;

            IF (VALOR_RECEITAORI + CORRECAOORI) <> 0 THEN

              -- CALCULA JUROS
              if lRaise is true then
                perform fc_debug('<recibo> VALOR_RECEITAORI: '||VALOR_RECEITAORI,lRaise, false,false);
              end if;

              if iFormaCorrecao = 1 then
                JURO := ROUND((VALOR_RECEITAORI + CORRECAO) * FC_JUROS(RECEITA, DATAVENC, DTEMITE, DTOPER, FALSE, ANOUSU), 2);
              else
                JURO := ROUND((CORRECAOORI + VALOR_RECEITAORI) * FC_JUROS(RECEITA, DATAVENC, DTEMITE, DTOPER, FALSE, ANOUSU), 2);
              end if;

              if lRaise is true then
                perform fc_debug('<recibo> JURO: '||JURO||' - nComposJuros: '||nComposJuros||' - valor para calcular juros: 1: '||CORRECAOORI||' - 2: '||VALOR_RECEITAORI,lRaise, false,false);
              end if;

              JURO = JURO + nComposJuros;

              -- CALCULA MULTA
              if iFormaCorrecao = 1 then
                MULTA := round((VALOR_RECEITAORI + CORRECAO)::numeric(15, 2) * FC_MULTA(RECEITA, DATAVENC, DTEMITE, DTOPER, ANOUSU)::numeric(15, 5), 2);
              else
                MULTA := ROUND((CORRECAOORI + VALOR_RECEITAORI)::numeric(15, 2) * FC_MULTA(RECEITA, DATAVENC, DTEMITE, DTOPER, ANOUSU)::numeric(15, 5), 2);
              end if;

              if lRaise is true then
                perform fc_debug('<recibo> MULTA: '||MULTA||' - nComposMulta: '||nComposMulta||' - valor para calcular juros: 1: '||CORRECAOORI||' - 2: '||VALOR_RECEITAORI, lRaise, false,false);
                perform fc_debug('<recibo> CORRECAO: '||CORRECAO, lRaise, false, false);
              end if;

              MULTA = MULTA + nComposMulta;

              SELECT K02_RECMUL,
                K02_RECJUR
              INTO K03_RECMUL,
                K03_RECJUR
              FROM TABREC
              WHERE K02_CODIGO = RECEITA;

              IF K03_RECMUL IS NULL THEN
                K03_RECMUL := RECEITA_MUL;
              END IF;

              IF K03_RECJUR IS NULL THEN
                K03_RECJUR := RECEITA_JUR;
              END IF;
              -- INCLUIDO VARIAVEL DESCONTO NO DB_RECIBOWEB

              if lRaise is true then
                 perform fc_debug('<recibo> percdescmul: '||percdescmul, lRaise, false, false);
                 perform fc_debug('<recibo> percdescjur: '||percdescjur, lRaise, false, false);
              end if;

              if percdescjur is not null and percdescmul is not null and (percdescjur+percdescmul) > 0 then
                vlrjuroparc := (ROUND(cast(JURO as FLOAT8) * percdescjur / 100,2));

                if lRaise is true then
                  perform fc_debug('<recibo> desconto (5) - vlrjuroparc: '||vlrjuroparc, lRaise, false, false);
                end if;

                if vlrjuroparc > 0 then

                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 06 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 06 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 06 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 06 - Receita ....: '||K03_RECJUR, lRaise, false, false);
                    perform fc_debug('<recibo> 06 - Historico ..: 918', lRaise, false, false);
                    perform fc_debug('<recibo> 06 - Valor ......: '||(vlrjuroparc * -1), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  if (lReceitaPossuiJuro or lReceitaPossuiMulta) then
                    INSERT INTO RECIBOPAGA ( k00_numcgm,
                                             k00_dtoper,
                                             k00_receit,
                                             k00_hist  ,
                                             k00_valor ,
                                             k00_dtvenc,
                                             k00_numpre,
                                             k00_numpar,
                                             k00_numtot,
                                             k00_numdig,
                                             k00_conta ,
                                             k00_dtpaga,
                                             k00_numnov)
                    VALUES ( NUMCGM,
                      DTEMITE,
                      K03_RECJUR,
                      918,
                      (vlrjuroparc * -1),
                      DATAVENC,
                      RECORD_NUMPRE.K99_NUMPRE,
                      NUMPAR,
                      NUMTOT,
                      NUMDIG,
                      0,
                             DTVENC,
                             NUMPRE);
                  end if;
                end if;
                vlrmultapar := (ROUND(cast(MULTA as FLOAT8) * percdescmul / 100,2));
                if vlrmultapar > 0  then
                  if lRaise is true then
                    perform fc_debug('<recibo> desconto (6) - vlrmultapar: '||vlrmultapar, lRaise, false, false);
                  end if;

                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 07 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 07 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 07 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 07 - Receita ....: '||K03_RECMUL, lRaise, false, false);
                    perform fc_debug('<recibo> 07 - Historico ..: 918', lRaise, false, false);
                    perform fc_debug('<recibo> 07 - Valor ......: '||(vlrmultapar * -1), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  INSERT INTO RECIBOPAGA ( k00_numcgm,
                                           k00_dtoper,
                                           k00_receit,
                                           k00_hist  ,
                                           k00_valor ,
                                           k00_dtvenc,
                                           k00_numpre,
                                           k00_numpar,
                                           k00_numtot,
                                           k00_numdig,
                                           k00_conta ,
                                           k00_dtpaga,
                                           k00_numnov )
                  VALUES ( NUMCGM,
                    DTEMITE,
                    K03_RECMUL,
                    918,
                    (vlrmultapar * -1),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,
                    NUMTOT,
                    NUMDIG,
                    0,
                           DTVENC,
                           NUMPRE );
                end if;
              end if;

              if lRaise is true then
                perform fc_debug('<recibo>    2 - juro: '||JURO||' - descjur: '||percdescjur||' - multa: '||MULTA||' - descmul: '||percdescmul||' - correcao: '||CORRECAO||' - VALOR_RECEITA: '||VALOR_RECEITA, lRaise, false, false);
              end if;

              IF K03_RECJUR = 0 OR K03_RECMUL = 0 OR K03_RECJUR = K03_RECMUL THEN

                IF JURO+MULTA <> 0 THEN

                  VLRJUROS := VLRJUROS + JURO;
                  VLRMULTA := VLRMULTA + MULTA;
                  if lRaise is true then
                    perform fc_debug('<recibo>  valor total juros + multa (7) - '||(JURO+MULTA), lRaise, false, false);
                  end if;

                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 08 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 08 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 08 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 08 - Receita ....: '||K03_RECJUR, lRaise, false, false);
                    perform fc_debug('<recibo> 08 - Historico ..: 400', lRaise, false, false);
                    perform fc_debug('<recibo> 08 - Valor ......: '||ROUND(JURO+MULTA,2), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  if (lReceitaPossuiJuro or lReceitaPossuiMulta) then
                    INSERT INTO RECIBOPAGA ( k00_numcgm,
                                             k00_dtoper,
                                             k00_receit,
                                             k00_hist  ,
                                             k00_valor ,
                                             k00_dtvenc,
                                             k00_numpre,
                                             k00_numpar,
                                             k00_numtot,
                                             k00_numdig,
                                             k00_conta ,
                                             k00_dtpaga,
                                             k00_numnov )
                    VALUES ( NUMCGM,
                      DTEMITE,
                      K03_RECJUR,
                      400,
                      ROUND(JURO+MULTA,2),
                      DATAVENC,
                      RECORD_NUMPRE.K99_NUMPRE,
                      NUMPAR,
                      NUMTOT,
                      NUMDIG,
                      0,
                             DTVENC,
                             NUMPRE );
                  end if;
                END IF;

              ELSE

                IF JURO <> 0 THEN

                  VLRJUROS := VLRJUROS + JURO;

                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 09 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 09 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 09 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 09 - Receita ....: '||K03_RECJUR, lRaise, false, false);
                    perform fc_debug('<recibo> 09 - Historico ..: 400', lRaise, false, false);
                    perform fc_debug('<recibo> 09 - Valor ......: '||ROUND(JURO,2), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  INSERT INTO RECIBOPAGA ( k00_numcgm,
                                           k00_dtoper,
                                           k00_receit,
                                           k00_hist  ,
                                           k00_valor ,
                                           k00_dtvenc,
                                           k00_numpre,
                                           k00_numpar,
                                           k00_numtot,
                                           k00_numdig,
                                           k00_conta ,
                                           k00_dtpaga,
                                           k00_numnov )
                  VALUES ( NUMCGM,
                    DTEMITE,
                    K03_RECJUR,
                    400,
                    ROUND(JURO,2),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,
                    NUMTOT,
                    NUMDIG,
                    0,
                           DTVENC,
                           NUMPRE );

                END IF;

                IF MULTA <> 0 THEN

                  VLRMULTA := VLRMULTA + MULTA;

                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 10 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 10 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 10 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 10 - Receita ....: '||K03_RECMUL, lRaise, false, false);
                    perform fc_debug('<recibo> 10 - Historico ..: 401', lRaise, false, false);
                    perform fc_debug('<recibo> 10 - Valor ......: '||ROUND(MULTA,2), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  INSERT INTO RECIBOPAGA ( k00_numcgm,
                                           k00_dtoper,
                                           k00_receit,
                                           k00_hist  ,
                                           k00_valor ,
                                           k00_dtvenc,
                                           k00_numpre,
                                           k00_numpar,
                                           k00_numtot,
                                           k00_numdig,
                                           k00_conta ,
                                           k00_dtpaga,
                                           k00_numnov )
                  VALUES ( NUMCGM,
                    DTEMITE,
                    K03_RECMUL,
                    401,
                    ROUND(MULTA,2),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,
                    NUMTOT,
                    NUMDIG,
                    0,
                           DTVENC,
                           NUMPRE );

                END IF;

              END IF;

              --CALCULAR DESCONTO
              IF CORRECAOORI+VALOR_RECEITAORI <> 0 THEN

                DESCONTO := FC_DESCONTO(RECEITA,
                                        DTEMITE,
                                        CORRECAOORI+VALOR_RECEITAORI,
                                        JURO+MULTA,
                                        UNICA,
                                        DATAVENC,
                                        ANOUSU,
                                        RECORD_NUMPRE.K99_NUMPRE);
                IF DESCONTO <> 0 THEN
                  VLRDESCONTO := VLRDESCONTO + DESCONTO;

                  if lRaise is true then

                    perform fc_debug('<recibo> desconto (8) - '||DESCONTO, lRaise, false, false);

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 11 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 11 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 11 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 11 - Receita ....: '||RECEITA, lRaise, false, false);
                    perform fc_debug('<recibo> 11 - Historico ..: 918', lRaise, false, false);
                    perform fc_debug('<recibo> 11 - Valor ......: '||ROUND(DESCONTO*-1,2), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  INSERT INTO RECIBOPAGA ( k00_numcgm,
                                           k00_dtoper,
                                           k00_receit,
                                           k00_hist  ,
                                           k00_valor ,
                                           k00_dtvenc,
                                           k00_numpre,
                                           k00_numpar,
                                           k00_numtot,
                                           k00_numdig,
                                           k00_conta ,
                                           k00_dtpaga,
                                           k00_numnov )
                  VALUES ( NUMCGM,
                    DTEMITE,
                    RECEITA,
                    918,
                    ROUND(DESCONTO*-1,2),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,
                    NUMTOT,
                    NUMDIG,
                    0,
                           DTVENC,
                           NUMPRE );
                END IF;

              END IF;

            END IF;

          ELSE

            IF USASISAGUA = FALSE AND RECEITA <> 401002 THEN

              rtp_recibo.rvMensagem    := '1 - Erro ao gerar recibo. Contate suporte!';
              rtp_recibo.rlErro        := true;

              if lRaise is true then
                perform fc_debug('<recibo> '                                                           , lRaise, false, false);
                perform fc_debug('<recibo> 1 - Fim do processamento - Retorno: '||rtp_recibo.rvMensagem, lRaise, false, false);
                perform fc_debug('<recibo> '                                                           , lRaise, false, true);
              end if;
              RETURN rtp_recibo;

            END IF;

          END IF;

          -- Aplica desconto no IPTU referente a NFS-e (Plugin)
          PERFORM fc_desconto_iptu_nfse(
            NUMCGM,
            DTEMITE,
            RECEITA,
            DATAVENC,
            RECORD_NUMPRE.K99_NUMPRE,
            NUMPAR,
            NUMTOT,
            NUMDIG,
            DTVENC,
            NUMPRE,
            UNICA
          );

        END LOOP;

      END IF;

    END LOOP;

  END LOOP;

  IF PROCESSA = TRUE THEN

    if cast(NUMBCO as integer) <> 0 then

      INSERT INTO ARREBANCO (k00_numpre,
                             k00_numpar,
                             k00_codbco,
                             k00_codage,
                             k00_numbco)
      VALUES (NUMPRE    ,
              0,
              CODBCO    ,
              CODAGE    ,
              NUMBCO    );
    end if;

    -- @todo - verificar esta validacao
    perform k00_receit,
      round(sum(k00_valor),2)
    from recibopaga
    where k00_numnov = NUMPRE
    group by k00_receit
    having round(sum(k00_valor),2) < 0;

    if found then
      rtp_recibo.rlErro     := true;
      rtp_recibo.rvMensagem := 'Recibo com registros negativos por receita. Contate suporte!';
    else
      rtp_recibo.rlErro     := false;
      rtp_recibo.rvMensagem := '';
    end if;

    if lRaise is true then
      perform fc_debug('<recibo> '                                                           , lRaise, false, false);
      perform fc_debug('<recibo> 3 - Fim do processamento - Retorno: '||rtp_recibo.rvMensagem, lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, true);
    end if;

    RETURN rtp_recibo;

  ELSE

    rtp_recibo.rvMensagem    := '3 - Erro ao gerar recibo. Contate suporte!';
    rtp_recibo.rlErro        := true;

    if lRaise is true then
      perform fc_debug('<recibo> Nao encontrados registros na tabela arrecad'                , lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, false);
      perform fc_debug('<recibo> 4 - Fim do processamento - Retorno: '||rtp_recibo.rvMensagem, lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, true);
    end if;

    RETURN  rtp_recibo;

  END IF;

END;
$$ language 'plpgsql';
SQL;
        $this->executeQuery($sql);
    }

    public function down()
    {
        $sql = <<<SQL
drop function fc_recibo(integer,date,date,integer);
drop   type tp_recibo;

create type tp_recibo as ( rvMensagem varchar(100),
                           rlErro     boolean );

create or replace function fc_recibo(integer,date,date,integer) returns tp_recibo  as
$$

DECLARE

  NUMPRE                ALIAS FOR $1;
  DTEMITE               ALIAS FOR $2;
  DTVENC                ALIAS FOR $3;
  ANOUSU                ALIAS FOR $4;

  iFormaCorrecao        integer default 2;
  TEM_DESCONTO          INTEGER DEFAULT 0;
  iInstit               integer;
  iExerc                integer;
  iRegraIptu            integer;
  iRegraIss             integer;
  NUMCGM                INTEGER;
  RECEITA               INTEGER;
  K03_RECMUL            INTEGER;
  K03_RECJUR            INTEGER;
  V_K00_HIST            INTEGER;
  QUAL_OPER             INTEGER;
  RECEITA_JUR           INTEGER;
  RECEITA_MUL           INTEGER;
  iTipoVlr              INTEGER;
  V_CADTIPOPARC         INTEGER;
  V_CADTIPOPARC_FORMA   INTEGER;
  NUMPAR                INTEGER;
  NUMTOT                INTEGER;
  NUMDIG                INTEGER;
  ARRETIPO              INTEGER;
  CODBCO                INTEGER;

  RECORD_NUMPRE         RECORD;
  RECORD_ALIAS          RECORD;
  RECORD_GRAVA          RECORD;
  RECORD_NUMPREF        RECORD;
  RECORD_UNICA          RECORD;
  v_composicao          record;
  aPercArreDesconto     record;

  VALOR_RECEITA         FLOAT8;
  VALOR_RECEITA_ORI     FLOAT8;
  VALOR_RECEITAORI      FLOAT8;
  CORRECAOORI           FLOAT8;
  DESCONTO              FLOAT8;
  TOTPERC               FLOAT8;
  DESC_VALOR_RECEITA    FLOAT8 DEFAULT 0;
  CORRECAO              FLOAT8 DEFAULT 0;
  DESC_CORRECAO         FLOAT8 DEFAULT 0;
  JURO                  FLOAT8 DEFAULT 0;
  MULTA                 FLOAT8 DEFAULT 0;
  vlrjuroparc           FLOAT8 DEFAULT 0;
  vlrmultapar           FLOAT8 DEFAULT 0;
  nDescontoCorrigido    FLOAT8 default 0;
  PERCDESCJUR           FLOAT8 DEFAULT 0;
  PERCDESCMUL           FLOAT8 DEFAULT 0;
  PERCDESCVLR           FLOAT8 DEFAULT 0;
  nPercArreDesconto     FLOAT8 DEFAULT 0;
  VLRJUROS              FLOAT8 default 0;
  VLRMULTA              FLOAT8 default 0;
  VLRDESCONTO           FLOAT8 default 0;

  DTOPER                DATE;
  DATAVENC              DATE;

  CODAGE                CHAR(5);
  NUMERO_ERRO           char(200);

  NUMBCO                VARCHAR(15);
  SQLRECIBO             VARCHAR(400);

  numpreValorTotal      numeric       default 0;
  nComposCorrecao       numeric(15,2) default 0;
  nComposJuros          numeric(15,2) default 0;
  nComposMulta          numeric(15,2) default 0;
  nCorreComposJuros     numeric(15,2) default 0;
  nCorreComposMulta     numeric(15,2) default 0;

  rtp_recibo            tp_recibo%ROWTYPE;

  USASISAGUA            BOOLEAN;
  ISSQNVARIAVEL         BOOLEAN;
  UNICA                 BOOLEAN DEFAULT FALSE;
  PROCESSA              BOOLEAN DEFAULT FALSE;
  lRaise                boolean default false;
  lParcelamento         boolean default false;

BEGIN

  lRaise := ( case when fc_getsession('DB_debugon') is null then false else true end );
  if lRaise is true then
    if fc_getsession('db_debug') <> '' then
      perform fc_debug('<recibo> Inicio do processamento do recibo...', lRaise, false, false);
    else
      perform fc_debug('<recibo> Inicio do processamento do recibo...', lRaise, true, false);
    end if;
  end if;

  select cast( fc_getsession('DB_instit') as integer )
  into iInstit;

  select cast( fc_getsession('DB_anousu') as integer )
  into iExerc;

  select db21_regracgmiptu::integer from db_config where codigo = iInstit
  into iRegraIptu;

  select db21_regracgmiss::integer from db_config where codigo = iInstit
  into iRegraIss;

  select db21_usasisagua
  into USASISAGUA
  from db_config
  where codigo = iInstit;

  if lRaise is true then
    perform fc_debug('<recibo> Numpre ...............:'||NUMPRE,  lRaise, false, false);
    perform fc_debug('<recibo> Data de Emissao ......:'||DTEMITE, lRaise, false, false);
    perform fc_debug('<recibo> Data de Vencimento ...:'||DTVENC,  lRaise, false, false);
    perform fc_debug('<recibo> AnoUsu ...............:'||ANOUSU,  lRaise, false, false);
  end if;

  select k03_separajurmulparc
  into iFormaCorrecao
  from numpref
  where k03_instit = iInstit
        and k03_anousu = iExerc;

  FOR RECORD_NUMPREF IN SELECT *
                        FROM NUMPREF
                        WHERE K03_ANOUSU = ANOUSU
  LOOP
    RECEITA_JUR := RECORD_NUMPREF.K03_RECJUR;
    RECEITA_MUL := RECORD_NUMPREF.K03_RECMUL;
  END LOOP;

  if lRaise is true then
    perform fc_debug('<recibo>'                                 ,lRaise, false, false);
    perform fc_debug('<recibo> Receita para Juro:'||RECEITA_JUR ,lRaise, false, false);
    perform fc_debug('<recibo> Receita para Multa:'||RECEITA_MUL,lRaise, false, false);
    perform fc_debug('<recibo>'                                 ,lRaise, false, false);
  end if;

  perform k00_numpre
  from recibo
  where k00_numnov = numpre LIMIT 1;
  if found then

    rtp_recibo.rvMensagem    := '4 - Erro ao gerar recibo. Contate suporte!';
    rtp_recibo.rlErro        := true;

    if lRaise is true then
      perform fc_debug('<recibo> Encontrados registros do numpre na tabela recibo'           , lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, false);
      perform fc_debug('<recibo> 5 - Fim do processamento - Retorno: '||rtp_recibo.rvMensagem, lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, true);
    end if;

    return  rtp_recibo;

  end if;

  perform 1
  from db_reciboweb
  where k99_numpre_n = numpre limit 1;
  if not found then

    rtp_recibo.rvMensagem    := '2 - Erro ao gerar recibo. Contate suporte!';
    rtp_recibo.rlErro        := true;

    if lRaise is true then
      perform fc_debug('<recibo> Não encontrados registros do numpre na tabela db_reciboweb' , lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, false);
      perform fc_debug('<recibo> 2 - Fim do processamento - Retorno: '||rtp_recibo.rvMensagem, lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, true);
    end if;

    return  rtp_recibo;

  end if;

  if lRaise is true then
    perform fc_debug('<recibo> Encontrados registros do numpre '||NUMPRE||' na tabela db_reciboweb, processando...',lRaise, false, false);
  end if;
  FOR RECORD_NUMPRE IN SELECT *
                       FROM DB_RECIBOWEB
                       WHERE K99_NUMPRE_N = NUMPRE
  LOOP

    CODBCO = RECORD_NUMPRE.K99_CODBCO;
    CODAGE = RECORD_NUMPRE.K99_CODAGE;
    --    NUMBCO = RECORD_NUMPRE.K99_NUMBCO;

    if lRaise is true then
      perform fc_debug('<recibo> '                                                           , lRaise, false, false);
      perform fc_debug('<recibo> -- Processando funcao fc_numbcoconvenio...'                 , lRaise, false, false);
    end if;
    select fc_numbcoconvenio(NUMBCO::integer) into NUMBCO;
    if lRaise is true then
      perform fc_debug('<recibo> Numbco : '||NUMBCO,lRaise, false, false);
      perform fc_debug('<recibo> -- Fim do processamento da funcao fc_numbcoconvenio...'     , lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, false);
    end if;

    TEM_DESCONTO = RECORD_NUMPRE.K99_DESCONTO;
    if lRaise is true then
      perform fc_debug('<recibo> TEM_DESCONTO: '||TEM_DESCONTO, lRaise, false, false);
    end if;

    if lRaise is true then
      perform fc_debug('<recibo> '                                                                  , lRaise, false, false);
      perform fc_debug('<recibo> '||lpad('',100,'-')                                                , lRaise, false, false);
      perform fc_debug('<recibo> 1 Buscando dados na tabela arrecad pelo Numpre '||RECORD_NUMPRE.K99_NUMPRE||' Parcela '||RECORD_NUMPRE.K99_NUMPAR||'...', lRaise, false, false);
    end if;

    FOR RECORD_UNICA IN SELECT DISTINCT
                          K00_NUMPRE,
                          K00_NUMPAR
                        FROM ARRECAD
                        WHERE K00_NUMPRE = RECORD_NUMPRE.K99_NUMPRE
                              AND CASE
                                  WHEN RECORD_NUMPRE.K99_NUMPAR = 0 THEN
                                    TRUE
                                  ELSE
                                    K00_NUMPAR = RECORD_NUMPRE.K99_NUMPAR
                                  END
    LOOP

      if lRaise is true then
        perform fc_debug('<recibo> Encontrou dados, Processa = true'                                  , lRaise, false, false);
        perform fc_debug('<recibo> Nnumpre: '||RECORD_NUMPRE.K99_NUMPRE||' - Numpar: '||RECORD_NUMPRE.K99_NUMPAR||' - processa: '||PROCESSA,lRaise, false, false);
      end if;
      PROCESSA := TRUE;

      IF RECORD_NUMPRE.K99_NUMPAR = 0 THEN
        UNICA := TRUE;

      ELSE
        IF RECORD_NUMPRE.K99_NUMPAR != RECORD_UNICA.K00_NUMPAR THEN
          if lRaise is true then
            perform fc_debug('<recibo> Parcela ('||RECORD_NUMPRE.K99_NUMPAR||') da tabela db_reciboweb diferente da parcela ('||RECORD_UNICA.K00_NUMPAR||') do arrecad', lRaise, false, false);
          end if;
          PROCESSA := FALSE;
        END IF;

      END IF;

      NUMPAR := RECORD_UNICA.K00_NUMPAR;

      IF PROCESSA = TRUE THEN

        if lRaise is true then
          perform fc_debug('<recibo> 2 Buscando dados na tabela arrecad pelo Numpre '||RECORD_NUMPRE.K99_NUMPRE||' Parcela '||NUMPAR||'...', lRaise, false, false);
        end if;

        FOR RECORD_ALIAS IN
        SELECT K00_RECEIT,
          K00_DTOPER,
          ( select rinumcgm from fc_socio_promitente(K00_NUMPRE, true, iRegraIptu, iRegraIss) limit 1 )as K00_NUMCGM,
          fc_calculavenci(k00_numpre,k00_numpar,K00_DTVENC,DTEMITE) AS K00_DTVENC,
          K00_NUMPRE,
          K00_NUMPAR,
          min(K00_hist) as K00_hist,
          (select sum(k00_valor)
           from arrecad as a
           where a.k00_numpre = arrecad.k00_numpre
                 and a.k00_numpar = arrecad.k00_numpar
                 and a.k00_receit = arrecad.k00_receit
                 and a.k00_tipo   = arrecad.k00_tipo ) as k00_valor,
          K00_TIPO
        FROM ARRECAD
        WHERE K00_NUMPRE = RECORD_NUMPRE.K99_NUMPRE
              AND K00_NUMPAR = NUMPAR
        group by K00_RECEIT,
          K00_DTOPER,
          K00_NUMCGM,
          fc_calculavenci(k00_numpre,k00_numpar,K00_DTVENC,DTEMITE),
          K00_NUMPRE,
          K00_NUMPAR,
          K00_TIPO
        ORDER BY K00_NUMPRE,K00_NUMPAR,K00_RECEIT
        LOOP

          if lRaise is true then

            perform fc_debug('<recibo> '                                                                  , lRaise, false, false);
            perform fc_debug('<recibo> Processando registros do Numpre '||RECORD_ALIAS.K00_NUMPRE||'...'  , lRaise, false, false);
            perform fc_debug('<recibo> Parcela .............:'||RECORD_ALIAS.K00_NUMPAR                   , lRaise, false, false);
            perform fc_debug('<recibo> Receita .............:'||RECORD_ALIAS.K00_RECEIT                   , lRaise, false, false);
            perform fc_debug('<recibo> Tipo ................:'||RECORD_ALIAS.K00_TIPO                     , lRaise, false, false);
            perform fc_debug('<recibo> Data de Operacao ....:'||RECORD_ALIAS.K00_DTOPER                   , lRaise, false, false);
            perform fc_debug('<recibo> Data de Vencimento ..:'||RECORD_ALIAS.K00_DTVENC                   , lRaise, false, false);
            perform fc_debug('<recibo> Valor da Receita ....:'||RECORD_ALIAS.K00_RECEIT                   , lRaise, false, false);
            perform fc_debug('<recibo> '                                                                  , lRaise, false, false);
            perform fc_debug('<recibo> Processa = true...'                                                , lRaise, false, false);

          end if;
          PROCESSA := TRUE;
          RECEITA  := RECORD_ALIAS.K00_RECEIT;
          ARRETIPO := RECORD_ALIAS.K00_TIPO;
          DTOPER   := RECORD_ALIAS.K00_DTOPER;
          NUMCGM   := RECORD_ALIAS.K00_NUMCGM;
          DATAVENC := RECORD_ALIAS.K00_DTVENC;
          VALOR_RECEITA := RECORD_ALIAS.K00_VALOR;

          IF VALOR_RECEITA = 0 THEN
            SELECT Q05_VLRINF
            INTO VALOR_RECEITA
            FROM ISSVAR
            WHERE Q05_NUMPRE = RECORD_ALIAS.K00_NUMPRE
                  AND Q05_NUMPAR = RECORD_ALIAS.K00_NUMPAR;
            IF VALOR_RECEITA IS NULL THEN
              VALOR_RECEITA := 0;
            ELSE
              ISSQNVARIAVEL := TRUE;
            END IF;
          END IF;

          QUAL_OPER := 0;
          -- T24879: Se valor da receita nao for 0 (zero) ou
          -- recibo for proveniente de uma emissao geral de iss variavel
          -- continua geracao da recibopaga
          IF ( VALOR_RECEITA <> 0 OR RECORD_NUMPRE.K99_TIPO = 6 ) THEN

            FOR RECORD_GRAVA IN SELECT *
                                FROM ARRECAD
                                WHERE K00_NUMPRE = RECORD_NUMPRE.K99_NUMPRE
                                      AND K00_NUMPAR = NUMPAR
                                      AND K00_RECEIT = RECEITA
            LOOP

              IF QUAL_OPER = 0 THEN
                V_K00_HIST := RECORD_GRAVA.K00_HIST;
                NUMTOT := RECORD_GRAVA.K00_NUMTOT;
                NUMDIG  := RECORD_GRAVA.K00_NUMDIG;
                QUAL_OPER := 1;
              END IF;

            END LOOP;

            -- CALCULA CORRECAO
            if VALOR_RECEITA <> 0 then

              if iFormaCorrecao = 1 then

                VALOR_RECEITA_ORI = VALOR_RECEITA;

                if lRaise is true then
                  perform fc_debug('<recibo> Forma de correcao .......: '||iFormaCorrecao, lRaise, false, false);
                  perform fc_debug('<recibo> VALOR_RECEITA_ORI .......: '||VALOR_RECEITA_ORI, lRaise, false, false);
                  perform fc_debug('<recibo> VALOR_RECEITA ...: '||VALOR_RECEITA, lRaise, false, false);
                  perform fc_debug('<recibo> fc_retornacomposicao('||record_alias.k00_numpre||','||record_alias.k00_numpar||','||record_alias.k00_receit||','||record_alias.k00_hist||','||dtoper||','||dtvenc||','||anousu||','||datavenc||')', lRaise, false, false);
                end if;

                select coalesce(rnCorreComposJuros,0),
                  coalesce(rnCorreComposMulta,0),
                  coalesce(rnComposCorrecao,0),
                  coalesce(rnComposJuros,0),
                  coalesce(rnComposMulta,0)
                into nCorreComposJuros,
                  nCorreComposMulta,
                  nComposCorrecao,
                  nComposJuros,
                  nComposMulta
                from fc_retornacomposicao(record_alias.k00_numpre, record_alias.k00_numpar, record_alias.k00_receit, record_alias.k00_hist, dtoper, dtvenc, anousu, datavenc);

                if lRaise is true then
                  perform fc_debug('<recibo> 1=nComposCorrecao: '||nComposCorrecao||' - VALOR_RECEITA: '||VALOR_RECEITA,lRaise, false,false);
                end if;

                VALOR_RECEITA = VALOR_RECEITA + nComposCorrecao;
                if lRaise is true then
                  perform fc_debug('<recibo> 2=nComposCorrecao: '||nComposCorrecao||' - VALOR_RECEITA: '||VALOR_RECEITA||' - VALOR_RECEITA: '||VALOR_RECEITA,lRaise, false,false);
                  perform fc_debug('<recibo> 1 Chamando a funcao fc_corre...',lRaise, false,false);
                end if;

                CORRECAO := ROUND( FC_CORRE(RECEITA,DTOPER,VALOR_RECEITA,DTVENC,ANOUSU,DATAVENC) , 2 );

                if lRaise is true then
                  perform fc_debug('<recibo> CORRECAO 1: '||CORRECAO,lRaise, false,false);
                end if;

                CORRECAO := ROUND( CORRECAO - VALOR_RECEITA + nComposCorrecao, 2 );

                if lRaise is true then
                  perform fc_debug('<recibo> CORRECAO 2: '||CORRECAO||' - nCorreComposJuros: '||nCorreComposJuros||' - nCorreComposMulta: '||nCorreComposMulta,lRaise, false,false);
                end if;

                CORRECAO := CORRECAO + nCorreComposJuros + nCorreComposMulta;

                if lRaise is true then
                  perform fc_debug('<recibo> VALOR_RECEITA: '||VALOR_RECEITA||' VALOR_RECEITA: '||VALOR_RECEITA||' - CORRECAO 3: '||CORRECAO,lRaise, false,false);
                end if;

                VALOR_RECEITA = VALOR_RECEITA_ORI;

              else

                if lRaise is true then
                  perform fc_debug('<recibo> 2 Chamando a funcao fc_corre...',lRaise, false,false);
                end if;

                CORRECAO := ROUND(FC_CORRE(RECEITA, DTOPER, VALOR_RECEITA, DTVENC, ANOUSU, DATAVENC) - round(VALOR_RECEITA, 2), 2);

                if lRaise is true then
                  perform fc_debug('<recibo> Forma de correcao ..............: '||coalesce(iFormaCorrecao,0), lRaise, false, false);
                  perform fc_debug('<recibo> Receita ........................: '||RECEITA, lRaise, false, false);
                  perform fc_debug('<recibo> DtOper .........................: '||DTOPER, lRaise, false, false);
                  perform fc_debug('<recibo> Valor da receita para calculo ..: '||VALOR_RECEITA, lRaise, false, false);
                  perform fc_debug('<recibo> DtVencto .......................: '||DTVENC, lRaise, false, false);
                  perform fc_debug('<recibo> Ano ............................: '||ANOUSU, lRaise, false, false);
                  perform fc_debug('<recibo> Data para Vencimento ...........: '||DATAVENC, lRaise, false, false);
                  perform fc_debug('<recibo> Correcao .......................: '||CORRECAO, lRaise, false, false);
                end if;

              end if;

            else
              CORRECAO := 0;
            end if;

            if lRaise is true then
                perform fc_debug('<recibo> 2 - TEM_DESCONTO: '||TEM_DESCONTO,lRaise, false,false);
            end if;

            if TEM_DESCONTO > 0 then

              select sum(fc_calcula_total_arrecad(x.k99_numpre, DTVENC))
                into numpreValorTotal
                from (select distinct db_reciboweb.k99_numpre 
                        from db_reciboweb 
                       where db_reciboweb.k99_numpre_n = NUMPRE) as x;

              select tipoparc.descjur,
                     tipoparc.descmul,
                     tipoparc.descvlr,
                     cadtipoparc.k40_codigo,
                     cadtipoparc.k40_forma,
                     tipoparc.tipovlr
                into percdescjur,
                     percdescmul,
                     percdescvlr,
                     v_cadtipoparc,
                     v_cadtipoparc_forma,
                     iTipoVlr
                from cadtipoparc
                     inner join tipoparc on tipoparc.cadtipoparc = cadtipoparc.k40_codigo
               where DTEMITE between tipoparc.dtini and tipoparc.dtfim
                 and tipoparc.maxparc = 1
                 and fc_verifica_desconto_faixa_valor(numpreValorTotal::numeric, tipoparc.vlrmin::numeric, tipoparc.vlrmax::numeric)
                 and cadtipoparc.k40_codigo = TEM_DESCONTO;

              if lRaise is true then

                perform fc_debug('<recibo> DTEMITE '||DTEMITE                             ,lRaise, false, false);
                perform fc_debug('<recibo> numpreValorTotal '||numpreValorTotal           ,lRaise, false, false);
                perform fc_debug('<recibo> '                                              ,lRaise, false, false);
                perform fc_debug('<recibo> Desconto em Regra...'                          ,lRaise, false, false);
                perform fc_debug('<recibo> DTVENC ................:'||DTVENC              ,lRaise, false, false);
                perform fc_debug('<recibo> percdescjur ...........:'||percdescjur         ,lRaise, false, false);
                perform fc_debug('<recibo> percdescmul ...........:'||percdescmul         ,lRaise, false, false);
                perform fc_debug('<recibo> percdescvlr ...........:'||percdescvlr         ,lRaise, false, false);
                perform fc_debug('<recibo> v_cadtipoparc .........:'||v_cadtipoparc       ,lRaise, false, false);
                perform fc_debug('<recibo> v_cadtipoparc_forma ...:'||v_cadtipoparc_forma ,lRaise, false, false);
                perform fc_debug('<recibo> iTipoVlr ..............:'||iTipoVlr            ,lRaise, false, false);
                perform fc_debug('<recibo> numpreValorTotal ......:'||numpreValorTotal    ,lRaise, false, false);
                perform fc_debug('<recibo> TEM_DESCONTO ..........:'||TEM_DESCONTO        ,lRaise, false, false);
              end if;

            end if;

            if lRaise is true then
              perform fc_debug('<recibo> CORRECAO '||receita||'-'||dtoper||'-'||VALOR_RECEITA||'-'||VALOR_RECEITA||'-'||datavenc||'-'||dtvenc, lRaise, false, false);
            end if;

            CORRECAOORI := CORRECAO;
            VALOR_RECEITAORI := VALOR_RECEITA;

            --  Trabalhar neste if para utilizar a mesma logica da recibodesconto
            --   alterar o programa de emissao de recibo para selecionar
            --   a regra se o contribuinte for ou nao loteador

            -- Verificar se a receita possui 'valores adicionais' (Jurídico > Procedimentos > Processo do Foro > Valores Adicionais) lançados a um processo.
            -- Caso possua, deve-se desconsiderar descontos em cima dessa receita.
            -- Obs.: Esse caso se aplica para débitos de inicial.
            perform j150_receita
               from processoforomulta
                    inner join processoforoinicial on j150_processoforo = v71_processoforo
                    inner join inicialnumpre on v71_inicial = v59_inicial
              where j150_receita = RECEITA
                and v59_numpre   = RECORD_ALIAS.K00_NUMPRE;

            if found then
              percdescvlr := 0;
              percdescmul := 0;
              percdescjur := 0;
            end if;

            if percdescvlr is not null and percdescvlr > 0 then

              if iTipoVlr = 1 then

                DESC_CORRECAO := ROUND(CORRECAO * percdescvlr / 100,2);
                if lRaise is true then
                  perform fc_debug('<recibo> desconto na correcao 2: '||CORRECAO||' (-'||DESC_CORRECAO||') - VALOR_RECEITA: '||VALOR_RECEITA||' - VALOR_RECEITA: '||VALOR_RECEITA||' - PERCENTUAL: '||percdescvlr,lRaise, false,false);
                end if;
                if DESC_CORRECAO > 0 then
                  --

                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 01 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 01 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 01 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 01 - Receita ....: '||RECEITA, lRaise, false, false);
                    perform fc_debug('<recibo> 01 - Historico ..:  918', lRaise, false, false);
                    perform fc_debug('<recibo> 01 - Valor ......: '||(DESC_CORRECAO*-1), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  INSERT INTO RECIBOPAGA (k00_numcgm,
                                          k00_dtoper,
                                          k00_receit,
                                          k00_hist,
                                          k00_valor,
                                          k00_dtvenc,
                                          k00_numpre,
                                          k00_numpar,
                                          k00_numtot,
                                          k00_numdig,
                                          k00_conta,
                                          k00_dtpaga,
                                          k00_numnov)
                  VALUES (NUMCGM,
                    DTEMITE,
                    RECEITA,
                    918,
                    (DESC_CORRECAO*-1),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,NUMTOT,
                    NUMDIG,
                    0,
                          DTVENC,
                          NUMPRE);
                end if;
              elsif iTipoVlr = 2 then
                nDescontoCorrigido := ROUND((VALOR_RECEITA + CORRECAO) * percdescvlr / 100,2);
                if lRaise is true then
                  perform fc_debug('<recibo> desconto na correcao 2: '||CORRECAO||' (-'||DESC_CORRECAO||') - VALOR_RECEITA: '||VALOR_RECEITA||' - VALOR_RECEITA: '||VALOR_RECEITA||' - PERCENTUAL: '||percdescvlr,lRaise, false,false);
                end if;
                if nDescontoCorrigido > 0 then
                  --
                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 02 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 02 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 02 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 02 - Receita ....: '||RECEITA, lRaise, false, false);
                    perform fc_debug('<recibo> 02 - Historico ..:  918', lRaise, false, false);
                    perform fc_debug('<recibo> 02 - Valor ......: '||(nDescontoCorrigido*-1), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  INSERT INTO RECIBOPAGA (k00_numcgm,
                                          k00_dtoper,
                                          k00_receit,
                                          k00_hist,
                                          k00_valor,
                                          k00_dtvenc,
                                          k00_numpre,
                                          k00_numpar,
                                          k00_numtot,
                                          k00_numdig,
                                          k00_conta,
                                          k00_dtpaga,
                                          k00_numnov )
                  VALUES (NUMCGM,
                    DTEMITE,
                    RECEITA,
                    918,
                    (nDescontoCorrigido*-1),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,
                    NUMTOT,
                    NUMDIG,
                    0,
                          DTVENC,
                          NUMPRE);
                end if;
              end if;

              -- Se a forma de aplicacao da regra for pra loteamentos (= 3)
              -- entao aplica desconto no valor da receita (historico)
              if v_cadtipoparc_forma = 3 then
                DESC_VALOR_RECEITA := ROUND(VALOR_RECEITA * percdescvlr / 100,2);
                if DESC_VALOR_RECEITA > 0 then
                  if lRaise is true then
                    perform fc_debug('<recibo> desconto (3) - DESC_VALOR_RECEITA: '||DESC_VALOR_RECEITA,lRaise, false,false);
                  end if;
                  --
                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 03 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 03 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 03 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 03 - Receita ....: '||RECEITA, lRaise, false, false);
                    perform fc_debug('<recibo> 03 - Historico ..:  918', lRaise, false, false);
                    perform fc_debug('<recibo> 03 - Valor ......: '||(DESC_VALOR_RECEITA*-1), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  INSERT INTO RECIBOPAGA (k00_numcgm,
                                          k00_dtoper,
                                          k00_receit,
                                          k00_hist,
                                          k00_valor,
                                          k00_dtvenc,
                                          k00_numpre,
                                          k00_numpar,
                                          k00_numtot,
                                          k00_numdig,
                                          k00_conta,
                                          k00_dtpaga,
                                          k00_numnov)
                  VALUES (NUMCGM,
                    DTEMITE,
                    RECEITA,
                    918,
                    (DESC_VALOR_RECEITA*-1),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,
                    NUMTOT,
                    NUMDIG,
                    0,
                          DTVENC,
                          NUMPRE);
                end if;
              end if;

              if lRaise is true then
                perform fc_debug('<recibo> desconto na correcao 2: '||CORRECAO||' - VALOR_RECEITA: '||VALOR_RECEITA||' - VALOR_RECEITA: '||VALOR_RECEITA,lRaise, false,false);
              end if;

            end if;

            /**
             * final na manutencao
             *
             */
            if lRaise is true then
              perform fc_debug('<recibo> '                                                                     , lRaise, false, false);
              perform fc_debug('<recibo> - juro ....................: '||JURO||' - descjur: '||percdescjur     , lRaise, false, false);
              perform fc_debug('<recibo> - multa ...................: '||MULTA||' - descmul: '||percdescmul    , lRaise, false, false);
              perform fc_debug('<recibo> - correcao ................: '||CORRECAO||' - descvlr: '||percdescvlr , lRaise, false, false);
              perform fc_debug('<recibo> - VALOR_RECEITA ...........: '||VALOR_RECEITA                         , lRaise, false, false);
              perform fc_debug('<recibo> - VALOR_RECEITA ...: '||VALOR_RECEITA                 , lRaise, false, false);
              perform fc_debug('<recibo> - cadtipoparc: '||coalesce(v_cadtipoparc::varchar, 'NULL')            , lRaise, false, false);
              perform fc_debug('<recibo> '                                                                     , lRaise, false, false);
            end if;

            -- T24879: Se valor diferente de zero ou tipo recibo for da emissao geral do iss
            -- gera recibopaga normalmente
            IF (VALOR_RECEITA + CORRECAO) <> 0 OR RECORD_NUMPRE.K99_TIPO = 6 THEN

              if lRaise is true then

                perform fc_debug('<recibo> ', lRaise, false, false);
                perform fc_debug('<recibo> 04 - inserindo na recibopaga... ', lRaise, false, false);
                perform fc_debug('<recibo> 04 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                perform fc_debug('<recibo> 04 - Numpar .....: '||NUMPAR, lRaise, false, false);
                perform fc_debug('<recibo> 04 - Receita ....: '||RECEITA, lRaise, false, false);
                perform fc_debug('<recibo> 04 - Historico ..: '||V_K00_HIST + 100, lRaise, false, false);
                perform fc_debug('<recibo> 04 - Valor ......: '||ROUND(VALOR_RECEITA+CORRECAO,2), lRaise, false, false);
                perform fc_debug('<recibo> ', lRaise, false, false);

              end if;

              INSERT INTO RECIBOPAGA ( k00_numcgm,
                                       k00_dtoper,
                                       k00_receit,
                                       k00_hist  ,
                                       k00_valor ,
                                       k00_dtvenc,
                                       k00_numpre,
                                       k00_numpar,
                                       k00_numtot,
                                       k00_numdig,
                                       k00_conta ,
                                       k00_dtpaga,
                                       k00_numnov )
              VALUES ( NUMCGM,
                DTEMITE,
                RECEITA,
                V_K00_HIST + 100,
                ROUND(VALOR_RECEITA+CORRECAO,2),
                DATAVENC,
                RECORD_NUMPRE.K99_NUMPRE,
                NUMPAR,
                NUMTOT,
                NUMDIG,
                0,
                       DTVENC,
                       NUMPRE );

              -- CALCULA DESCONTO DA ARREDESCONTO
              perform v07_numpre
              from termo
              where v07_numpre = RECORD_NUMPRE.K99_NUMPRE;

              if found then
                lParcelamento := true;
              end if;

              if lParcelamento then

                -- Verifica desconto
                select nPercRetPrinc,
                       nPercDescCorre,
                       nPercDescJuros,
                       nPercDescMulta
                  into aPercArreDesconto
                from fc_recibodesconto(RECORD_NUMPRE.K99_NUMPRE,
                                       NUMPAR,
                                       NUMTOT,
                                       RECEITA,
                                       ARRETIPO,
                                       DTEMITE,
                                       fc_proximo_dia_util(DATAVENC));

                nPercArreDesconto = aPercArreDesconto.nPercRetPrinc;
                if (nComposCorrecao + nComposJuros + nComposMulta) > 0 then
                    nPercArreDesconto := aPercArreDesconto.nPercDescCorre;
                    percdescjur := aPercArreDesconto.nPercDescJuros;
                    percdescmul := aPercArreDesconto.nPercDescMulta;
                end if;

                if nPercArreDesconto > 0 then

                  if lRaise is true then
                     perform fc_debug('<recibo> desconto (4) - nPercArreDesconto: '||nPercArreDesconto,lRaise, false,false);
                  end if;

                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 05 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 05 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 05 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 05 - Receita ....: '||RECEITA, lRaise, false, false);
                    perform fc_debug('<recibo> 05 - Historico ..: 918', lRaise, false, false);
                    perform fc_debug('<recibo> 05 - Valor ......: '||ROUND(((ROUND(VALOR_RECEITA+CORRECAO,2) * nPercArreDesconto)/100),2) * -1, lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  INSERT INTO RECIBOPAGA ( k00_numcgm,
                                           k00_dtoper,
                                           k00_receit,
                                           k00_hist  ,
                                           k00_valor ,
                                           k00_dtvenc,
                                           k00_numpre,
                                           k00_numpar,
                                           k00_numtot,
                                           k00_numdig,
                                           k00_conta ,
                                           k00_dtpaga,
                                           k00_numnov )
                  VALUES ( NUMCGM,
                    DTEMITE,
                    RECEITA,
                    918,
                    ROUND(((ROUND(VALOR_RECEITA+CORRECAO,2) * nPercArreDesconto)/100),2) * -1,
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,
                    NUMTOT,
                    NUMDIG,
                    0,
                           DTVENC,
                           NUMPRE );

                end if;
              end if;

            END IF;

            IF (VALOR_RECEITAORI + CORRECAOORI) <> 0 THEN

              -- CALCULA JUROS
              if lRaise is true then
                perform fc_debug('<recibo> VALOR_RECEITAORI: '||VALOR_RECEITAORI,lRaise, false,false);
              end if;

              if iFormaCorrecao = 1 then
                JURO := ROUND((VALOR_RECEITAORI + CORRECAO) * FC_JUROS(RECEITA, DATAVENC, DTEMITE, DTOPER, FALSE, ANOUSU), 2);
              else
                JURO := ROUND((CORRECAOORI + VALOR_RECEITAORI) * FC_JUROS(RECEITA, DATAVENC, DTEMITE, DTOPER, FALSE, ANOUSU), 2);
              end if;

              if lRaise is true then
                perform fc_debug('<recibo> JURO: '||JURO||' - nComposJuros: '||nComposJuros||' - valor para calcular juros: 1: '||CORRECAOORI||' - 2: '||VALOR_RECEITAORI,lRaise, false,false);
              end if;

              JURO = JURO + nComposJuros;

              -- CALCULA MULTA
              if iFormaCorrecao = 1 then
                MULTA := round((VALOR_RECEITAORI + CORRECAO)::numeric(15, 2) * FC_MULTA(RECEITA, DATAVENC, DTEMITE, DTOPER, ANOUSU)::numeric(15, 5), 2);
              else
                MULTA := ROUND((CORRECAOORI + VALOR_RECEITAORI)::numeric(15, 2) * FC_MULTA(RECEITA, DATAVENC, DTEMITE, DTOPER, ANOUSU)::numeric(15, 5), 2);
              end if;

              if lRaise is true then
                perform fc_debug('<recibo> MULTA: '||MULTA||' - nComposMulta: '||nComposMulta||' - valor para calcular juros: 1: '||CORRECAOORI||' - 2: '||VALOR_RECEITAORI, lRaise, false,false);
                perform fc_debug('<recibo> CORRECAO: '||CORRECAO, lRaise, false, false);
              end if;

              MULTA = MULTA + nComposMulta;

              SELECT K02_RECMUL,
                K02_RECJUR
              INTO K03_RECMUL,
                K03_RECJUR
              FROM TABREC
              WHERE K02_CODIGO = RECEITA;

              IF K03_RECMUL IS NULL THEN
                K03_RECMUL := RECEITA_MUL;
              END IF;

              IF K03_RECJUR IS NULL THEN
                K03_RECJUR := RECEITA_JUR;
              END IF;
              -- INCLUIDO VARIAVEL DESCONTO NO DB_RECIBOWEB

              if lRaise is true then
                 perform fc_debug('<recibo> percdescmul: '||percdescmul, lRaise, false, false);
                 perform fc_debug('<recibo> percdescjur: '||percdescjur, lRaise, false, false);
              end if;

              if percdescjur is not null and percdescmul is not null and (percdescjur+percdescmul) > 0 then
                vlrjuroparc := (ROUND(cast(JURO as FLOAT8) * percdescjur / 100,2));

                if lRaise is true then
                  perform fc_debug('<recibo> desconto (5) - vlrjuroparc: '||vlrjuroparc, lRaise, false, false);
                end if;

                if vlrjuroparc > 0 then

                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 06 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 06 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 06 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 06 - Receita ....: '||K03_RECJUR, lRaise, false, false);
                    perform fc_debug('<recibo> 06 - Historico ..: 918', lRaise, false, false);
                    perform fc_debug('<recibo> 06 - Valor ......: '||(vlrjuroparc * -1), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;


                  INSERT INTO RECIBOPAGA ( k00_numcgm,
                                           k00_dtoper,
                                           k00_receit,
                                           k00_hist  ,
                                           k00_valor ,
                                           k00_dtvenc,
                                           k00_numpre,
                                           k00_numpar,
                                           k00_numtot,
                                           k00_numdig,
                                           k00_conta ,
                                           k00_dtpaga,
                                           k00_numnov)
                  VALUES ( NUMCGM,
                    DTEMITE,
                    K03_RECJUR,
                    918,
                    (vlrjuroparc * -1),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,
                    NUMTOT,
                    NUMDIG,
                    0,
                           DTVENC,
                           NUMPRE);
                end if;
                vlrmultapar := (ROUND(cast(MULTA as FLOAT8) * percdescmul / 100,2));
                if vlrmultapar > 0  then
                  if lRaise is true then
                    perform fc_debug('<recibo> desconto (6) - vlrmultapar: '||vlrmultapar, lRaise, false, false);
                  end if;

                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 07 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 07 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 07 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 07 - Receita ....: '||K03_RECMUL, lRaise, false, false);
                    perform fc_debug('<recibo> 07 - Historico ..: 918', lRaise, false, false);
                    perform fc_debug('<recibo> 07 - Valor ......: '||(vlrmultapar * -1), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  INSERT INTO RECIBOPAGA ( k00_numcgm,
                                           k00_dtoper,
                                           k00_receit,
                                           k00_hist  ,
                                           k00_valor ,
                                           k00_dtvenc,
                                           k00_numpre,
                                           k00_numpar,
                                           k00_numtot,
                                           k00_numdig,
                                           k00_conta ,
                                           k00_dtpaga,
                                           k00_numnov )
                  VALUES ( NUMCGM,
                    DTEMITE,
                    K03_RECMUL,
                    918,
                    (vlrmultapar * -1),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,
                    NUMTOT,
                    NUMDIG,
                    0,
                           DTVENC,
                           NUMPRE );
                end if;
              end if;

              if lRaise is true then
                perform fc_debug('<recibo>    2 - juro: '||JURO||' - descjur: '||percdescjur||' - multa: '||MULTA||' - descmul: '||percdescmul||' - correcao: '||CORRECAO||' - VALOR_RECEITA: '||VALOR_RECEITA, lRaise, false, false);
              end if;

              IF K03_RECJUR = 0 OR K03_RECMUL = 0 OR K03_RECJUR = K03_RECMUL THEN

                IF JURO+MULTA <> 0 THEN

                  VLRJUROS := VLRJUROS + JURO;
                  VLRMULTA := VLRMULTA + MULTA;
                  if lRaise is true then
                    perform fc_debug('<recibo>  valor total juros + multa (7) - '||(JURO+MULTA), lRaise, false, false);
                  end if;

                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 08 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 08 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 08 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 08 - Receita ....: '||K03_RECJUR, lRaise, false, false);
                    perform fc_debug('<recibo> 08 - Historico ..: 400', lRaise, false, false);
                    perform fc_debug('<recibo> 08 - Valor ......: '||ROUND(JURO+MULTA,2), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  INSERT INTO RECIBOPAGA ( k00_numcgm,
                                           k00_dtoper,
                                           k00_receit,
                                           k00_hist  ,
                                           k00_valor ,
                                           k00_dtvenc,
                                           k00_numpre,
                                           k00_numpar,
                                           k00_numtot,
                                           k00_numdig,
                                           k00_conta ,
                                           k00_dtpaga,
                                           k00_numnov )
                  VALUES ( NUMCGM,
                    DTEMITE,
                    K03_RECJUR,
                    400,
                    ROUND(JURO+MULTA,2),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,
                    NUMTOT,
                    NUMDIG,
                    0,
                           DTVENC,
                           NUMPRE );
                END IF;

              ELSE

                IF JURO <> 0 THEN

                  VLRJUROS := VLRJUROS + JURO;

                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 09 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 09 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 09 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 09 - Receita ....: '||K03_RECJUR, lRaise, false, false);
                    perform fc_debug('<recibo> 09 - Historico ..: 400', lRaise, false, false);
                    perform fc_debug('<recibo> 09 - Valor ......: '||ROUND(JURO,2), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  INSERT INTO RECIBOPAGA ( k00_numcgm,
                                           k00_dtoper,
                                           k00_receit,
                                           k00_hist  ,
                                           k00_valor ,
                                           k00_dtvenc,
                                           k00_numpre,
                                           k00_numpar,
                                           k00_numtot,
                                           k00_numdig,
                                           k00_conta ,
                                           k00_dtpaga,
                                           k00_numnov )
                  VALUES ( NUMCGM,
                    DTEMITE,
                    K03_RECJUR,
                    400,
                    ROUND(JURO,2),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,
                    NUMTOT,
                    NUMDIG,
                    0,
                           DTVENC,
                           NUMPRE );

                END IF;

                IF MULTA <> 0 THEN

                  VLRMULTA := VLRMULTA + MULTA;

                  if lRaise is true then

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 10 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 10 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 10 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 10 - Receita ....: '||K03_RECMUL, lRaise, false, false);
                    perform fc_debug('<recibo> 10 - Historico ..: 401', lRaise, false, false);
                    perform fc_debug('<recibo> 10 - Valor ......: '||ROUND(MULTA,2), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;


                  INSERT INTO RECIBOPAGA ( k00_numcgm,
                                           k00_dtoper,
                                           k00_receit,
                                           k00_hist  ,
                                           k00_valor ,
                                           k00_dtvenc,
                                           k00_numpre,
                                           k00_numpar,
                                           k00_numtot,
                                           k00_numdig,
                                           k00_conta ,
                                           k00_dtpaga,
                                           k00_numnov )
                  VALUES ( NUMCGM,
                    DTEMITE,
                    K03_RECMUL,
                    401,
                    ROUND(MULTA,2),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,
                    NUMTOT,
                    NUMDIG,
                    0,
                           DTVENC,
                           NUMPRE );

                END IF;

              END IF;

              --CALCULAR DESCONTO
              IF CORRECAOORI+VALOR_RECEITAORI <> 0 THEN

                DESCONTO := FC_DESCONTO(RECEITA,
                                        DTEMITE,
                                        CORRECAOORI+VALOR_RECEITAORI,
                                        JURO+MULTA,
                                        UNICA,
                                        DATAVENC,
                                        ANOUSU,
                                        RECORD_NUMPRE.K99_NUMPRE);
                IF DESCONTO <> 0 THEN
                  VLRDESCONTO := VLRDESCONTO + DESCONTO;

                  if lRaise is true then

                    perform fc_debug('<recibo> desconto (8) - '||DESCONTO, lRaise, false, false);

                    perform fc_debug('<recibo> ', lRaise, false, false);
                    perform fc_debug('<recibo> 11 - inserindo na recibopaga... ', lRaise, false, false);
                    perform fc_debug('<recibo> 11 - Numpre .....: '||RECORD_NUMPRE.K99_NUMPRE, lRaise, false, false);
                    perform fc_debug('<recibo> 11 - Numpar .....: '||NUMPAR, lRaise, false, false);
                    perform fc_debug('<recibo> 11 - Receita ....: '||RECEITA, lRaise, false, false);
                    perform fc_debug('<recibo> 11 - Historico ..: 918', lRaise, false, false);
                    perform fc_debug('<recibo> 11 - Valor ......: '||ROUND(DESCONTO*-1,2), lRaise, false, false);
                    perform fc_debug('<recibo> ', lRaise, false, false);

                  end if;

                  INSERT INTO RECIBOPAGA ( k00_numcgm,
                                           k00_dtoper,
                                           k00_receit,
                                           k00_hist  ,
                                           k00_valor ,
                                           k00_dtvenc,
                                           k00_numpre,
                                           k00_numpar,
                                           k00_numtot,
                                           k00_numdig,
                                           k00_conta ,
                                           k00_dtpaga,
                                           k00_numnov )
                  VALUES ( NUMCGM,
                    DTEMITE,
                    RECEITA,
                    918,
                    ROUND(DESCONTO*-1,2),
                    DATAVENC,
                    RECORD_NUMPRE.K99_NUMPRE,
                    NUMPAR,
                    NUMTOT,
                    NUMDIG,
                    0,
                           DTVENC,
                           NUMPRE );
                END IF;

              END IF;

            END IF;

          ELSE

            IF USASISAGUA = FALSE AND RECEITA <> 401002 THEN

              rtp_recibo.rvMensagem    := '1 - Erro ao gerar recibo. Contate suporte!';
              rtp_recibo.rlErro        := true;

              if lRaise is true then
                perform fc_debug('<recibo> '                                                           , lRaise, false, false);
                perform fc_debug('<recibo> 1 - Fim do processamento - Retorno: '||rtp_recibo.rvMensagem, lRaise, false, false);
                perform fc_debug('<recibo> '                                                           , lRaise, false, true);
              end if;
              RETURN rtp_recibo;

            END IF;

          END IF;

          -- Aplica desconto no IPTU referente a NFS-e (Plugin)
          PERFORM fc_desconto_iptu_nfse(
            NUMCGM,
            DTEMITE,
            RECEITA,
            DATAVENC,
            RECORD_NUMPRE.K99_NUMPRE,
            NUMPAR,
            NUMTOT,
            NUMDIG,
            DTVENC,
            NUMPRE,
            UNICA
          );

        END LOOP;

      END IF;

    END LOOP;

  END LOOP;

  IF PROCESSA = TRUE THEN

    if cast(NUMBCO as integer) <> 0 then

      INSERT INTO ARREBANCO (k00_numpre,
                             k00_numpar,
                             k00_codbco,
                             k00_codage,
                             k00_numbco)
      VALUES (NUMPRE    ,
              0,
              CODBCO    ,
              CODAGE    ,
              NUMBCO    );
    end if;

    -- @todo - verificar esta validacao
    perform k00_receit,
      round(sum(k00_valor),2)
    from recibopaga
    where k00_numnov = NUMPRE
    group by k00_receit
    having round(sum(k00_valor),2) < 0;

    if found then
      rtp_recibo.rlErro     := true;
      rtp_recibo.rvMensagem := 'Recibo com registros negativos por receita. Contate suporte!';
    else
      rtp_recibo.rlErro     := false;
      rtp_recibo.rvMensagem := '';
    end if;

    if lRaise is true then
      perform fc_debug('<recibo> '                                                           , lRaise, false, false);
      perform fc_debug('<recibo> 3 - Fim do processamento - Retorno: '||rtp_recibo.rvMensagem, lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, true);
    end if;

    RETURN rtp_recibo;

  ELSE

    rtp_recibo.rvMensagem    := '3 - Erro ao gerar recibo. Contate suporte!';
    rtp_recibo.rlErro        := true;

    if lRaise is true then
      perform fc_debug('<recibo> Não encontrados registros na tabela arrecad'                , lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, false);
      perform fc_debug('<recibo> 4 - Fim do processamento - Retorno: '||rtp_recibo.rvMensagem, lRaise, false, false);
      perform fc_debug('<recibo> '                                                           , lRaise, false, true);
    end if;

    RETURN  rtp_recibo;

  END IF;

END;
$$ language 'plpgsql';
SQL;
        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
