<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23100FcVerificaLancamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement(<<<SQL

create or replace function fc_verifica_lancamento(integer,date,integer,float8) returns text as
$$
declare

  p_numemp         alias for $1;
  p_dtfim          alias for $2;
  p_doc            alias for $3;
  p_valor          alias for $4;

  c_lanc           record;

  v_dtini          date;
  v_erro           integer default 0;

  v_vlremp         float8 default 0;
  v_vlrliq         float8 default 0;
  v_vlrpag         float8 default 0;

  v_vlremp_g       float8 default 0;
  v_vlrliq_g       float8 default 0;
  v_vlrpag_g       float8 default 0;

  v_saldodisprp    numeric default 0;

  m_erro           text;

  v_anousu         integer default 0;
  v_instit         integer ;

  v_saldo_dotacao  float8 default 0;
  v_reserva_valor  float8 default 0;

  dataemp          boolean;  --  permite empenho com data maior que o ultimo empenho
  dataserv         boolean;  --  permite empenho com data maior que a data do servidor
  maxdataemp       date;

begin

  -- para documento numero 1 o valor da variavel p_numemp deve ser o numero da autorizacao
  -- significa que a autorizacao esta sendo empenhada e ira gerar o documento 1
  if p_doc = 1 then

    -- Busca Instituicao da Autorizacao
    select e54_instit
      into v_instit
      from empautoriza
     where e54_autori = p_numemp;

    -- verifica se pode empenhar com data anterior a do servidor ( retornaram a data )
    select e30_empdataemp,
           e30_empdataserv
      into dataemp, dataserv
      from empparametro
     where e39_anousu = substr(p_dtfim,1,4)::integer ;

    if (dataemp = false) then
      -- nao permite empenhar com data anterior ao ultimo empenho emitido
      select max(e60_emiss)
        into maxdataemp
        from empempenho
       where e60_instit = v_instit;

      if  p_dtfim < maxdataemp  then
        return '11 VOCÊ NÃO PODE EMPENHAR COM DATA INFERIOR AO ULTIMO EMPENHO EMITIDO. INSTITUICAO ('||v_instit||')';
      end if;

    end if;

    if (dataserv = false) then
      -- nao permite empenhar com data superior a data do servidor
      if  p_dtfim > current_date  then
        return '11 VOCÊ NÃO PODE EMPENHAR COM DATA SUPERIOR A DATA DO SISTEMA (SERVIDOR). INSTITUICAO ('||v_instit||')';
      end if;

    end if;

    -- testa se existe a reserva de saldo para est
    select o83_codres
      into v_erro
      from orcreservaaut
     where o83_autori = p_numemp;

    if not found then
      return '11 AUTORIZAÇÃO SEM RESERVA DE SALDO. (' || p_numemp || ').';
    else
      select substr(fc_dotacaosaldo(o80_anousu,o80_coddot,2,p_dtfim,p_dtfim),106,13)::float8, o80_valor
        into v_saldo_dotacao, v_reserva_valor
        from orcreserva
       where o80_codres = V_ERRO;

      if v_saldo_dotacao >= v_reserva_valor then
        return '0  PROCESSO AUTORIZADO (' || v_erro || ').';
      else
        return '12 SEM SALDO NA DOTACAO (' || v_erro || ' - ' || v_saldo_dotacao || ').';
      end if;

    end if;

  end if;

  -- testa os lancamentos do empenho
  select e60_emiss,
         e60_anousu,
         e60_instit
    into v_dtini, v_anousu
    from empempenho
   where e60_numemp = p_numemp;

  if v_dtini is null then
    return '14 Empenho não encontrado.';
  end if;

  if v_anousu < substr(p_dtfim,1,4)::integer then

    select round(round(e91_vlremp,2) - round(e91_vlranu,2),2)::float8,
           round(e91_vlrliq,2)::float8,
           round(e91_vlrpag,2)::float8
      into v_vlremp,v_vlrliq,v_vlrpag
      from empresto
     where e91_anousu = substr(p_dtfim,1,4)::integer
       and e91_numemp = p_numemp;

    if v_vlremp is null then
      return '16 EMPENHO NÃO CADASTRADO COMO RESTOS A PAGAR';
    end if;

    v_dtini := (substr(p_dtfim,1,4)||'-01-01')::date;

    v_vlremp_g := v_vlremp;
    v_vlrliq_g := v_vlrliq;
    v_vlrpag_g := v_vlrpag;

  end if;


  if v_dtini > p_dtfim then
    return '14 Data informada menor que data de emissão do Empenho.';
  end if;

  raise notice 'data ini % - EMP %', v_dtini, p_numemp;

  for c_lanc in
      select c53_coddoc,
             c53_descr,
             c75_numemp,
             c70_data,
             c70_valor
        from conlancamemp
             inner join conlancamdoc on c75_codlan = c71_codlan
             inner join conlancam    on c70_codlan = c75_codlan
             inner join conhistdoc   on c53_coddoc = c71_coddoc
       where c75_numemp = p_numemp
         and c75_data >= v_dtini
    order by c70_data
  loop

    RAISE NOTICE '\n%,%,%,%,%',c_lanc.c53_coddoc,c_lanc.c53_descr,c_lanc.c75_numemp,c_lanc.c70_valor,c_lanc.c70_data;

    /**
     * Empenho - 10
     */
    if c_lanc.c53_coddoc in(1, 82, 304, 308, 410, 500, 504) then

      if c_lanc.c70_data <= p_dtfim then
        v_vlremp := round(v_vlremp + c_lanc.c70_valor,2)::float8;
      end if;
      v_vlremp_g := round(v_vlremp_g + c_lanc.c70_valor,2)::float8;

    /**
     * Estorno de Empenho - 11
     */
    elsif c_lanc.c53_coddoc in(2, 83, 305, 309, 411, 501, 505) then

      if c_lanc.c70_data <= p_dtfim then
        v_vlremp := round(v_vlremp - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlremp_g := round(v_vlremp_g - c_lanc.c70_valor,2)::float8;

    /**
     * Estorno de RP nao processado - 11
     */
    elsif c_lanc.c53_coddoc = 32 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlremp := round(v_vlremp - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlremp_g := round(v_vlremp_g - c_lanc.c70_valor,2)::float8;

    /**
     * Liquidacao - 20
     */
    elsif c_lanc.c53_coddoc in(3, 23, 39, 84, 412, 202, 204, 206, 306, 310, 502, 506) then

      if c_lanc.c70_data <= p_dtfim then
        v_vlrliq := round(v_vlrliq + c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrliq_g := round(v_vlrliq_g + c_lanc.c70_valor,2)::float8;

    /**
     * liquidacao de RP - 20
     */
    elsif c_lanc.c53_coddoc in (33, 39) then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrliq := round(v_vlrliq + c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrliq_g := round(v_vlrliq_g + c_lanc.c70_valor,2)::float8;

     /**
      * Anulacao de liquidacao - 21
      */
    elsif c_lanc.c53_coddoc in(4, 24, 40, 85, 203, 205, 207, 413, 307, 311, 503, 507) then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrliq := round(v_vlrliq - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrliq_g := round(v_vlrliq_g - c_lanc.c70_valor,2)::float8;

    /**
     * Anulacao de Liquidacao de RP - 21
     */
    elsif c_lanc.c53_coddoc in (34, 40) then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrliq := round(v_vlrliq - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrliq_g := round(v_vlrliq_g - c_lanc.c70_valor,2)::float8;


    /**
     * pagamento - 30
     */
    elsif c_lanc.c53_coddoc = 5 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrpag := round(v_vlrpag + c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrpag_g := round(v_vlrpag_g + c_lanc.c70_valor,2)::float8;

     /**
      * estorno de pagamento - 31
      */
    elsif c_lanc.c53_coddoc = 6 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrpag := round(v_vlrpag - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrpag_g := round(v_vlrpag_g - c_lanc.c70_valor,2)::float8;

     /**
      * Pagamento de RP - 30
      */
    elsif c_lanc.c53_coddoc = 35 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrpag := round(v_vlrpag + c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrpag_g := round(v_vlrpag_g + c_lanc.c70_valor,2)::float8;

    /**
     * estorno de pagamento de RP - 31
     */
    elsif c_lanc.c53_coddoc = 36 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrpag := round(v_vlrpag - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrpag_g := round(v_vlrpag_g - c_lanc.c70_valor,2)::float8;

    /**
     * Pagamento de RP processado - 30
     */
    elsif c_lanc.c53_coddoc = 37 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrpag := round(v_vlrpag + c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrpag_g := round(v_vlrpag_g + c_lanc.c70_valor,2)::float8;

      /**
       * Estorno de RP não processado - 31
       */
    elsif c_lanc.c53_coddoc = 38 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrpag := round(v_vlrpag - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrpag_g := round(v_vlrpag_g - c_lanc.c70_valor,2)::float8;

    /**
     *  6002 | ESTORNO DE RETENO DE VALORES CONSIGNADOS
     */
    elsif c_lanc.c53_coddoc = 6002 then
    
      if c_lanc.c70_data <= p_dtfim then
        v_vlrpag := round(v_vlrpag + c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrpag_g := round(v_vlrpag_g + c_lanc.c70_valor,2)::float8;


    /**
     *  6003 | ESTORNO DE RETENÇÃO DE VALORES CONSIGNADOS
     */
    elsif c_lanc.c53_coddoc = 6003 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrpag := round(v_vlrpag - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrpag_g := round(v_vlrpag_g - c_lanc.c70_valor,2)::float8;

    end if;

  end loop;

  raise notice 'v_vlremp: % , v_vlrliq: % , v_vlrpag %', v_vlremp, v_vlrliq, v_vlrpag;

  if p_doc = 2 or p_doc = 32 then

    -- testar anulacao de empenho
    -- precisa ter saldo para anular ( emp - liq > 0 )
    -- erro na data passada
    if round(v_vlremp - v_vlrliq,2)::float8 >= p_valor then
      m_erro := '0 PROCESSO AUTORIZADO.';
      v_erro := 0;
    else
      m_erro :=  '01 Não existe saldo para anular nesta data.';
      v_erro := 1;
    end if;

    -- erro geral no empenho
    if v_erro = 0 then
      if round(v_vlremp_g - v_vlrliq_g,2)::float8 >= p_valor then
        m_erro := '0 PROCESSO AUTORIZADO.';
        v_erro := 0;
      else
        m_erro := '02 Não existe saldo geral no empenho para anular.';
        v_erro := 2;
      end if;
    end if;

  end if;

  if p_doc = 3 or p_doc in (33, 39) or p_doc = 23 then

    -- testar liquidacao de empenho
    -- precisa ter saldo para anular ( emp - liq > 0 )
    -- erro na data passada
    if round(v_vlremp - v_vlrliq,2)::float8 >= round(p_valor,2)::float8 then
      m_erro := '0 PROCESSO AUTORIZADO.';
      v_erro := 0;
    else
      m_erro := '03 Não existe saldo a liquidar neste data.';
      v_erro := 3;
    end if;

    -- erro geral no empenho
    if v_erro = 0 then
      if round(v_vlremp_g - v_vlrliq_g,2)::float8 >= p_valor then
        m_erro := '0 PROCESSO AUTORIZADO.';
        v_erro := 0;
      else
        m_erro := '04 Não existe saldo geral no empenho para liquidar.';
        v_erro := 4;
      end if;
    end if;
  end if;

  if p_doc = 4 or p_doc in (34,40) or p_doc = 24 then

    -- testar estorno de liquidacao de empenho
    -- precisa ter saldo para anular ( emp - liq > 0 )
    -- erro na data passada

    if p_doc in (34, 40) then

      select sum(case
               when c71_coddoc in(33, 39) then
                 c70_valor
               else
                 c70_valor * -1
             end)
        into v_saldodisprp
        from conlancam
             inner join conlancamemp on c75_codlan = c70_codlan
             inner join conlancamdoc on c71_codlan = c70_codlan
       where c75_numemp = p_numemp
         and c71_coddoc in (33, 34, 39, 40)
         and extract (year from c70_data) = extract (year from p_dtfim);

      --raise notice 'disp: %', v_saldodisprp;

      if v_saldodisprp is null then
        v_saldodisprp = 0;
      end if;

      --raise notice 'doc: % - valor: % - disp: % - numemp: % - p_dtfim: %', p_doc, p_valor, v_saldodisprp, p_numemp, p_dtfim;

      if p_valor > v_saldodisprp then
        m_erro := '05 Não existe saldo para anular a liquidação nesta data.) Saldo Disponível: '|| v_saldodisprp;
        v_erro := 5;
      end if;

    end if;

    if v_erro = 0 then
      if round(v_vlrliq - v_vlrpag,2)::float8 >= p_valor then
        m_erro := '0 PROCESSO AUTORIZADO.';
        v_erro := 0;
      else
        m_erro := '05 Não existe saldo para anular a liquidação nesta data.';
        v_erro := 5;
      end if;
    end if;

    -- erro geral no empenho
    if v_erro = 0 then
      if round(v_vlrliq_g - v_vlrpag_g,2)::float8 >= p_valor then
        m_erro := '0 PROCESSO AUTORIZADO.';
        v_erro := 0;
      else
        m_erro := '06 Não existem saldo geral no empenho para estornar a liquidação.';
        v_erro := 6;
      end if;
    end if;

  end if;


  if p_doc = 5 or p_doc = 35  or p_doc = 37 then

    -- testar pagamento de empenho
    -- precisa ter saldo para anular ( emp - liq > 0 )
    -- erro na data passada
    if( round(v_vlrliq - v_vlrpag,2)::float8 >= p_valor) then
      v_erro := 0;
      m_erro := '0 PROCESSO AUTORIZADO.';
    else
      m_erro := '07 Não existe saldo a pagar nesta data. Empenho: ' || to_char(p_numemp, '9999999999');
      v_erro := 7;
    end if;

    -- erro geral no empenho
    if v_erro = 0 then
      if round(v_vlrliq_g - v_vlrpag_g,2)::float8 >= p_valor then
        m_erro := '0 PROCESSO AUTORIZADO.';
        v_erro := 0;
      else
        m_erro := '08 Não existe saldo geral a pagar no empenho.';
        v_erro := 8;
      end if;
    end if;

  end if;

  if p_doc = 6 or p_doc = 36  or p_doc = 38 then

    -- testar anulacao de pagamento empenho
    -- precisa ter saldo para anular ( emp - liq > 0 )
    -- erro na data passada
    if v_vlrpag >= p_valor then
      m_erro := '0 PROCESSO AUTORIZADO.';
      v_erro := 0;
    else
      m_erro := '09 Não existe saldo para anular pagamento nesta data.';
      v_erro := 9;
    end if;

    -- erro geral no empenho
    if v_erro = 0 then
      if v_vlrpag_g >= p_valor then
        m_erro := '0 PROCESSO AUTORIZADO.';
        v_erro := 0;
      else
        m_erro := '10 Não existe saldo geral no empenho para anular o pagamento.';
        v_erro := 10;
      end if;
    end if;

  end if;


  --raise notice ' ate data %,%,%',v_vlremp,v_vlrliq,v_vlrpag;
  --raise notice ' geral    %,%,%',v_vlremp_g,v_vlrliq_g,v_vlrpag_g;

  return m_erro;

end;
$$
language 'plpgsql';

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
        DB::statement(<<<SQL

create or replace function fc_verifica_lancamento(integer,date,integer,float8) returns text as
$$
declare

  p_numemp         alias for $1;
  p_dtfim          alias for $2;
  p_doc            alias for $3;
  p_valor          alias for $4;

  c_lanc           record;

  v_dtini          date;
  v_erro           integer default 0;

  v_vlremp         float8 default 0;
  v_vlrliq         float8 default 0;
  v_vlrpag         float8 default 0;

  v_vlremp_g       float8 default 0;
  v_vlrliq_g       float8 default 0;
  v_vlrpag_g       float8 default 0;

  v_saldodisprp    numeric default 0;

  m_erro           text;

  v_anousu         integer default 0;
  v_instit         integer ;

  v_saldo_dotacao  float8 default 0;
  v_reserva_valor  float8 default 0;

  dataemp          boolean;  --  permite empenho com data maior que o ultimo empenho
  dataserv         boolean;  --  permite empenho com data maior que a data do servidor
  maxdataemp       date;

begin

  -- para documento numero 1 o valor da variavel p_numemp deve ser o numero da autorizacao
  -- significa que a autorizacao esta sendo empenhada e ira gerar o documento 1
  if p_doc = 1 then

    -- Busca Instituicao da Autorizacao
    select e54_instit
      into v_instit
      from empautoriza
     where e54_autori = p_numemp;

    -- verifica se pode empenhar com data anterior a do servidor ( retornaram a data )
    select e30_empdataemp,
           e30_empdataserv
      into dataemp, dataserv
      from empparametro
     where e39_anousu = substr(p_dtfim,1,4)::integer ;

    if (dataemp = false) then
      -- nao permite empenhar com data anterior ao ultimo empenho emitido
      select max(e60_emiss)
        into maxdataemp
        from empempenho
       where e60_instit = v_instit;

      if  p_dtfim < maxdataemp  then
        return '11 VOCÊ NÃO PODE EMPENHAR COM DATA INFERIOR AO ULTIMO EMPENHO EMITIDO. INSTITUICAO ('||v_instit||')';
      end if;

    end if;

    if (dataserv = false) then
      -- nao permite empenhar com data superior a data do servidor
      if  p_dtfim > current_date  then
        return '11 VOCÊ NÃO PODE EMPENHAR COM DATA SUPERIOR A DATA DO SISTEMA (SERVIDOR). INSTITUICAO ('||v_instit||')';
      end if;

    end if;

    -- testa se existe a reserva de saldo para est
    select o83_codres
      into v_erro
      from orcreservaaut
     where o83_autori = p_numemp;

    if not found then
      return '11 AUTORIZAÇÃO SEM RESERVA DE SALDO. (' || p_numemp || ').';
    else
      select substr(fc_dotacaosaldo(o80_anousu,o80_coddot,2,p_dtfim,p_dtfim),106,13)::float8, o80_valor
        into v_saldo_dotacao, v_reserva_valor
        from orcreserva
       where o80_codres = V_ERRO;

      if v_saldo_dotacao >= v_reserva_valor then
        return '0  PROCESSO AUTORIZADO (' || v_erro || ').';
      else
        return '12 SEM SALDO NA DOTACAO (' || v_erro || ' - ' || v_saldo_dotacao || ').';
      end if;

    end if;

  end if;

  -- testa os lancamentos do empenho
  select e60_emiss,
         e60_anousu,
         e60_instit
    into v_dtini, v_anousu
    from empempenho
   where e60_numemp = p_numemp;

  if v_dtini is null then
    return '14 Empenho não encontrado.';
  end if;

  if v_anousu < substr(p_dtfim,1,4)::integer then

    select round(round(e91_vlremp,2) - round(e91_vlranu,2),2)::float8,
           round(e91_vlrliq,2)::float8,
           round(e91_vlrpag,2)::float8
      into v_vlremp,v_vlrliq,v_vlrpag
      from empresto
     where e91_anousu = substr(p_dtfim,1,4)::integer
       and e91_numemp = p_numemp;

    if v_vlremp is null then
      return '16 EMPENHO NÃO CADASTRADO COMO RESTOS A PAGAR';
    end if;

    v_dtini := (substr(p_dtfim,1,4)||'-01-01')::date;

    v_vlremp_g := v_vlremp;
    v_vlrliq_g := v_vlrliq;
    v_vlrpag_g := v_vlrpag;

  end if;


  if v_dtini > p_dtfim then
    return '14 Data informada menor que data de emissão do Empenho.';
  end if;

  raise notice 'data ini % - EMP %', v_dtini, p_numemp;

  for c_lanc in
      select c53_coddoc,
             c53_descr,
             c75_numemp,
             c70_data,
             c70_valor
        from conlancamemp
             inner join conlancamdoc on c75_codlan = c71_codlan
             inner join conlancam    on c70_codlan = c75_codlan
             inner join conhistdoc   on c53_coddoc = c71_coddoc
       where c75_numemp = p_numemp
         and c75_data >= v_dtini
    order by c70_data
  loop

    RAISE NOTICE '\n%,%,%,%,%',c_lanc.c53_coddoc,c_lanc.c53_descr,c_lanc.c75_numemp,c_lanc.c70_valor,c_lanc.c70_data;

    /**
     * Empenho - 10
     */
    if c_lanc.c53_coddoc in(1, 82, 304, 308, 410, 500, 504) then

      if c_lanc.c70_data <= p_dtfim then
        v_vlremp := round(v_vlremp + c_lanc.c70_valor,2)::float8;
      end if;
      v_vlremp_g := round(v_vlremp_g + c_lanc.c70_valor,2)::float8;

    /**
     * Estorno de Empenho - 11
     */
    elsif c_lanc.c53_coddoc in(2, 83, 305, 309, 411, 501, 505) then

      if c_lanc.c70_data <= p_dtfim then
        v_vlremp := round(v_vlremp - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlremp_g := round(v_vlremp_g - c_lanc.c70_valor,2)::float8;

    /**
     * Estorno de RP nao processado - 11
     */
    elsif c_lanc.c53_coddoc = 32 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlremp := round(v_vlremp - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlremp_g := round(v_vlremp_g - c_lanc.c70_valor,2)::float8;

    /**
     * Liquidacao - 20
     */
    elsif c_lanc.c53_coddoc in(3, 23, 39, 84, 412, 202, 204, 206, 306, 310, 502, 506) then

      if c_lanc.c70_data <= p_dtfim then
        v_vlrliq := round(v_vlrliq + c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrliq_g := round(v_vlrliq_g + c_lanc.c70_valor,2)::float8;

    /**
     * liquidacao de RP - 20
     */
    elsif c_lanc.c53_coddoc in (33, 39) then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrliq := round(v_vlrliq + c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrliq_g := round(v_vlrliq_g + c_lanc.c70_valor,2)::float8;

     /**
      * Anulacao de liquidacao - 21
      */
    elsif c_lanc.c53_coddoc in(4, 24, 40, 85, 203, 205, 207, 413, 307, 311, 503, 507) then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrliq := round(v_vlrliq - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrliq_g := round(v_vlrliq_g - c_lanc.c70_valor,2)::float8;

    /**
     * Anulacao de Liquidacao de RP - 21
     */
    elsif c_lanc.c53_coddoc in (34, 40) then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrliq := round(v_vlrliq - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrliq_g := round(v_vlrliq_g - c_lanc.c70_valor,2)::float8;


    /**
     * pagamento - 30
     */
    elsif c_lanc.c53_coddoc = 5 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrpag := round(v_vlrpag + c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrpag_g := round(v_vlrpag_g + c_lanc.c70_valor,2)::float8;

     /**
      * estorno de pagamento - 31
      */
    elsif c_lanc.c53_coddoc = 6 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrpag := round(v_vlrpag - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrpag_g := round(v_vlrpag_g - c_lanc.c70_valor,2)::float8;

     /**
      * Pagamento de RP - 30
      */
    elsif c_lanc.c53_coddoc = 35 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrpag := round(v_vlrpag + c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrpag_g := round(v_vlrpag_g + c_lanc.c70_valor,2)::float8;

    /**
     * estorno de pagamento de RP - 31
     */
    elsif c_lanc.c53_coddoc = 36 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrpag := round(v_vlrpag - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrpag_g := round(v_vlrpag_g - c_lanc.c70_valor,2)::float8;

    /**
     * Pagamento de RP processado - 30
     */
    elsif c_lanc.c53_coddoc = 37 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrpag := round(v_vlrpag + c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrpag_g := round(v_vlrpag_g + c_lanc.c70_valor,2)::float8;

      /**
       * Estorno de RP não processado - 31
       */
    elsif c_lanc.c53_coddoc = 38 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrpag := round(v_vlrpag - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrpag_g := round(v_vlrpag_g - c_lanc.c70_valor,2)::float8;

    /**
     *  6003 | ESTORNO DE RETENÇÃO DE VALORES CONSIGNADOS
     */
    elsif c_lanc.c53_coddoc = 6003 then
      if c_lanc.c70_data <= p_dtfim then
        v_vlrpag := round(v_vlrpag - c_lanc.c70_valor,2)::float8;
      end if;
      v_vlrpag_g := round(v_vlrpag_g - c_lanc.c70_valor,2)::float8;

    end if;

  end loop;

  raise notice 'v_vlremp: % , v_vlrliq: % , v_vlrpag %', v_vlremp, v_vlrliq, v_vlrpag;

  if p_doc = 2 or p_doc = 32 then

    -- testar anulacao de empenho
    -- precisa ter saldo para anular ( emp - liq > 0 )
    -- erro na data passada
    if round(v_vlremp - v_vlrliq,2)::float8 >= p_valor then
      m_erro := '0 PROCESSO AUTORIZADO.';
      v_erro := 0;
    else
      m_erro :=  '01 Não existe saldo para anular nesta data.';
      v_erro := 1;
    end if;

    -- erro geral no empenho
    if v_erro = 0 then
      if round(v_vlremp_g - v_vlrliq_g,2)::float8 >= p_valor then
        m_erro := '0 PROCESSO AUTORIZADO.';
        v_erro := 0;
      else
        m_erro := '02 Não existe saldo geral no empenho para anular.';
        v_erro := 2;
      end if;
    end if;

  end if;

  if p_doc = 3 or p_doc in (33, 39) or p_doc = 23 then

    -- testar liquidacao de empenho
    -- precisa ter saldo para anular ( emp - liq > 0 )
    -- erro na data passada
    if round(v_vlremp - v_vlrliq,2)::float8 >= round(p_valor,2)::float8 then
      m_erro := '0 PROCESSO AUTORIZADO.';
      v_erro := 0;
    else
      m_erro := '03 Não existe saldo a liquidar neste data.';
      v_erro := 3;
    end if;

    -- erro geral no empenho
    if v_erro = 0 then
      if round(v_vlremp_g - v_vlrliq_g,2)::float8 >= p_valor then
        m_erro := '0 PROCESSO AUTORIZADO.';
        v_erro := 0;
      else
        m_erro := '04 Não existe saldo geral no empenho para liquidar.';
        v_erro := 4;
      end if;
    end if;
  end if;

  if p_doc = 4 or p_doc in (34,40) or p_doc = 24 then

    -- testar estorno de liquidacao de empenho
    -- precisa ter saldo para anular ( emp - liq > 0 )
    -- erro na data passada

    if p_doc in (34, 40) then

      select sum(case
               when c71_coddoc in(33, 39) then
                 c70_valor
               else
                 c70_valor * -1
             end)
        into v_saldodisprp
        from conlancam
             inner join conlancamemp on c75_codlan = c70_codlan
             inner join conlancamdoc on c71_codlan = c70_codlan
       where c75_numemp = p_numemp
         and c71_coddoc in (33, 34, 39, 40)
         and extract (year from c70_data) = extract (year from p_dtfim);

      --raise notice 'disp: %', v_saldodisprp;

      if v_saldodisprp is null then
        v_saldodisprp = 0;
      end if;

      --raise notice 'doc: % - valor: % - disp: % - numemp: % - p_dtfim: %', p_doc, p_valor, v_saldodisprp, p_numemp, p_dtfim;

      if p_valor > v_saldodisprp then
        m_erro := '05 Não existe saldo para anular a liquidação nesta data.) Saldo Disponível: '|| v_saldodisprp;
        v_erro := 5;
      end if;

    end if;

    if v_erro = 0 then
      if round(v_vlrliq - v_vlrpag,2)::float8 >= p_valor then
        m_erro := '0 PROCESSO AUTORIZADO.';
        v_erro := 0;
      else
        m_erro := '05 Não existe saldo para anular a liquidação nesta data.';
        v_erro := 5;
      end if;
    end if;

    -- erro geral no empenho
    if v_erro = 0 then
      if round(v_vlrliq_g - v_vlrpag_g,2)::float8 >= p_valor then
        m_erro := '0 PROCESSO AUTORIZADO.';
        v_erro := 0;
      else
        m_erro := '06 Não existem saldo geral no empenho para estornar a liquidação.';
        v_erro := 6;
      end if;
    end if;

  end if;


  if p_doc = 5 or p_doc = 35  or p_doc = 37 then

    -- testar pagamento de empenho
    -- precisa ter saldo para anular ( emp - liq > 0 )
    -- erro na data passada
    if( round(v_vlrliq - v_vlrpag,2)::float8 >= p_valor) then
      v_erro := 0;
      m_erro := '0 PROCESSO AUTORIZADO.';
    else
      m_erro := '07 Não existe saldo a pagar nesta data. Empenho: ' || to_char(p_numemp, '9999999999');
      v_erro := 7;
    end if;

    -- erro geral no empenho
    if v_erro = 0 then
      if round(v_vlrliq_g - v_vlrpag_g,2)::float8 >= p_valor then
        m_erro := '0 PROCESSO AUTORIZADO.';
        v_erro := 0;
      else
        m_erro := '08 Não existe saldo geral a pagar no empenho.';
        v_erro := 8;
      end if;
    end if;

  end if;

  if p_doc = 6 or p_doc = 36  or p_doc = 38 then

    -- testar anulacao de pagamento empenho
    -- precisa ter saldo para anular ( emp - liq > 0 )
    -- erro na data passada
    if v_vlrpag >= p_valor then
      m_erro := '0 PROCESSO AUTORIZADO.';
      v_erro := 0;
    else
      m_erro := '09 Não existe saldo para anular pagamento nesta data.';
      v_erro := 9;
    end if;

    -- erro geral no empenho
    if v_erro = 0 then
      if v_vlrpag_g >= p_valor then
        m_erro := '0 PROCESSO AUTORIZADO.';
        v_erro := 0;
      else
        m_erro := '10 Não existe saldo geral no empenho para anular o pagamento.';
        v_erro := 10;
      end if;
    end if;

  end if;


  --raise notice ' ate data %,%,%',v_vlremp,v_vlrliq,v_vlrpag;
  --raise notice ' geral    %,%,%',v_vlremp_g,v_vlrliq_g,v_vlrpag_g;

  return m_erro;

end;
$$
language 'plpgsql';

SQL
);

    }
}
