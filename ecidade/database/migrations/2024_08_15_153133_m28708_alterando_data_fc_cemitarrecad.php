<?php

use Illuminate\Database\Migrations\Migration;

class M28708AlterandoDataFcCemitarrecad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
create or replace function fc_cemitarrecad(integer, integer, boolean) returns varchar as
$$
declare

  v_tipo        alias for $1;
  -- codigo referente ao campo k03_tipo do arquivo cadtipo
  -- 7 insert
  -- 5 update

  v_codnump     alias for $2;
  -- numpre para calculo do arrecad

  v_opcaocalc   alias for $3;
  -- true = calcula  false = recalcula

  v_chave       integer;
  v_privenc     date;
  v_provenc     date;
  v_dia         integer;
  v_totpar      integer;
  v_contador    integer;
  v_data        date;
  v_numpre      integer;
  v_dtoper      date;
  v_numparc     integer default 1;
  v_numcgm      integer;
  v_receita     integer;
  v_hist        integer;
  v_proced      integer;
  v_tipodebito  integer;
  v_taxaserv    integer;
  v_valor       float8;
  numpre        integer;
  v_valorparc   float8;
  v_valorrest   float8;
  v_codigo      integer;
  v_numero      varchar;
  v_obs         text;
  v_obsdiver    text;

  v_raise       boolean default false;

begin

  -- calculo do arquivo taxa de servicos - cemiterio

  if v_tipo = 1 then

    select cm10_i_codigo,
           cm10_d_privenc,
           cm10_d_privenc,
           extract (day from cm10_d_privenc),
           cm10_i_numpre,
           cm10_d_data,
           cm28_i_proprietario,
           cm10_i_taxaserv,
           cm10_f_valor,
           cm10_t_obs,
           cm25_i_codigo,
           cm25_c_numero
      into v_chave,
           v_privenc,
           v_provenc,
           v_dia,
           v_numpre,
           v_dtoper,
           v_numcgm,
           v_taxaserv,
           v_valor,
           v_obs,
           v_codigo,
           v_numero
      from itenserv
           inner join txossoariojazigo on cm30_i_itenserv       = cm10_i_codigo
           inner join ossoariojazigo   on cm25_i_codigo         = cm30_i_ossoariojazigo
           inner join propricemit      on cm28_i_ossoariojazigo = cm25_i_codigo
     where cm10_i_numpre = v_codnump;

    -- observacoes do diverso
    v_obsdiver = 'REF. OSSOARIO ' || ' Cod.: ' || v_codigo || ' N: ' || v_numero;

  else


    select cm10_i_codigo,
           cm10_d_privenc,
           cm10_d_privenc,
           extract (day from cm10_d_privenc),
           cm10_i_numpre,
           cm10_d_data,
           cm01_i_declarante,
           cm10_i_taxaserv,
           cm10_f_valor,
           cm10_t_obs,
           cm01_i_codigo,
           cm05_c_numero
      into v_chave,
           v_privenc,
           v_provenc,
           v_dia,
           v_numpre,
           v_dtoper,
           v_numcgm,
           v_taxaserv,
           v_valor,
           v_obs,
           v_codigo,
           v_numero
      from itenserv
           inner join txsepultamentos on cm31_i_itenserv     = cm10_i_codigo
           inner join sepultamentos   on cm01_i_codigo       = cm31_i_sepultamento
            left join sepulta         on cm01_i_codigo       = cm24_i_sepultamento
            left join sepulturas      on cm24_i_sepultura    = cm05_i_codigo
     where cm10_i_numpre = v_codnump;

     -- observacoes do diverso
     v_obsdiver = 'REF. SEPULTAMENTO ' || ' Cod.: ' || v_codigo || ' N: ' || v_numero;

  end if;

  if v_chave is null then
    return '0 - CODIGO NAO ENCONTRADO';
  end if;
  if v_privenc is null then
    return '1 - DATA DO PRIMEIRO VENCIMENTO ZERADA';
  end if;
  if v_numparc > 1 and v_provenc is null then
    return '2 - DATA DO SEGUNDO VENCIMENTO ZERADA';
  end if;
  if v_dia is null then
    v_dia = substr(v_privenc,9,2)::integer;
  end if;

  if v_numpre is null then
    return '5 CODIGO DE ARRECADACAO NAO GERADO';
  else
    select k00_numpre
      into numpre
      from arrecad
     where k00_numpre = v_numpre
     limit 1;
    if not numpre is null and v_opcaocalc = true then
      return '4 CODIGO JA CALCULADO';
    else
      if not numpre is null then
        insert into arreold (
          k00_numpar,
          k00_numcgm,
          k00_dtoper,
          k00_receit,
          k00_hist,
          k00_valor,
          k00_dtvenc,
          k00_numtot,
          k00_numdig,
          k00_tipo,
          k00_tipojm
        )
        select k00_numpre,
               k00_numpar,
               k00_numcgm,
               k00_dtoper,
               k00_receit,
               k00_hist,
               k00_valor,
               k00_dtvenc,
               k00_numtot,
               k00_numdig,
               k00_tipo,
               k00_tipojm
          from arrecad
         where k00_numpre = v_numpre;
        delete from arrecad where k00_numpre = v_numpre;
      end if;
    end if;
  end if;

  select cm11_i_receita,
         cm11_i_historico,
         cm11_i_proced,
         cm11_i_tipo
    into v_receita,
         v_hist,
         v_proced,
         v_tipodebito
    from taxaserv
   where cm11_i_codigo = v_taxaserv;

  if v_receita is null then
    return '6 CODIGO DA RECEITA NAO ENCONTRADO NO PROCDIVER';
  end if;
  if v_hist is null then
    return '7 CODIGO DO HISTORICO NAO ENCONTRADO NO PROCDIVER';
  end if;
  if v_proced is null then
    return '8 CODIGO DO TIPO DE DEBITO NAO ENCONTRADO NO PROCDIVER';
  end if;

  v_contador = 1;
  v_valorparc = round( v_valor / v_numparc, 2 );
  v_valorrest = v_valor - ( v_valorparc * v_numparc );

  if v_raise is true then
    raise notice 'valorparc-%',v_valorparc;
    raise notice 'valorrest-%',v_valorrest;
  end if;

  loop

    if v_contador = 1 then
      v_data = v_privenc;
    end if;
    if v_contador = 2 then
      v_data = v_provenc;
    end if;
    if v_contador > 2 then
      v_data = fc_vencimento(v_provenc,v_contador - 1,v_dia);
      --v_data = (v_provenc,v_contador - 1,v_dia);
    end if;

    insert into diversos
    (
     dv05_coddiver,
     dv05_numcgm,
     dv05_dtinsc,
     dv05_exerc,
     dv05_numpre,
     dv05_vlrhis,
     dv05_procdiver,
     dv05_numtot,
     dv05_privenc,
     dv05_provenc,
     dv05_diaprox,
     dv05_oper,
     dv05_valor,
     dv05_obs,
     dv05_instit
    )
    values
    (
    nextval('diversos_dv05_coddiver_seq'),
    v_numcgm,
    v_dtoper,
    extract (year from v_data),
    v_codnump,
    v_valorparc,
    v_proced,
    v_numparc,
    v_data,
    v_data,
    extract (day from v_data),
    v_dtoper,
    v_valorparc,
    v_obsdiver || ' - ' || trim(v_obs),
    fc_getsession('DB_instit')::integer
    );

    insert into arrecad ( k00_numcgm ,
                          k00_dtoper ,
                          k00_receit,
                          k00_hist ,
                          k00_valor,
                          k00_dtvenc,
                          k00_numpre,
                          k00_numpar,
                          k00_numtot,
                          k00_numdig,
                          k00_tipo ,
                          k00_tipojm )
                values (  v_numcgm,
                          v_dtoper,
                          v_receita,
                          v_hist,
                          v_valorparc,
                          v_data,
                          v_numpre,
                          v_contador,
                          v_numparc,
                          fc_digito(v_numpre,v_contador,v_numparc),
                          v_tipodebito,
                          0);

    --select fc_numbco(d01_codbco,d01_codage) into numbco;
    --insert into arrebanco values(numpre,numparc,r_contri.d01_codbco,r_contri.d01_codage,numbco);

    v_contador = v_contador + 1;
    if v_raise is true then
      raise notice 'contador-%, %',v_contador, v_numparc;
    end if;

    if v_contador > v_numparc then
      exit;
    else
      if v_contador = v_numparc then
        v_valorparc = v_valorparc + v_valorrest;
        if v_raise is true then
          raise notice 'valorparc2-%',v_valorparc;
        end if;
      end if;
    end if;

  end loop;
  return '9 CALCULO EFETUADO';

END;
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
        DB::unprepared(<<<SQL
create or replace function fc_cemitarrecad(integer, integer, boolean) returns varchar as
$$
declare

  v_tipo        alias for $1;
  -- codigo referente ao campo k03_tipo do arquivo cadtipo
  -- 7 insert
  -- 5 update

  v_codnump     alias for $2;
  -- numpre para calculo do arrecad

  v_opcaocalc   alias for $3;
  -- true = calcula  false = recalcula

  v_chave       integer;
  v_privenc     date;
  v_provenc     date;
  v_dia         integer;
  v_totpar      integer;
  v_contador    integer;
  v_data        date;
  v_numpre      integer;
  v_dtoper      date;
  v_numparc     integer default 1;
  v_numcgm      integer;
  v_receita     integer;
  v_hist        integer;
  v_proced      integer;
  v_tipodebito  integer;
  v_taxaserv    integer;
  v_valor       float8;
  numpre        integer;
  v_valorparc   float8;
  v_valorrest   float8;
  v_codigo      integer;
  v_numero      varchar;
  v_obs         text;
  v_obsdiver    text;

  v_raise       boolean default false;

begin

  -- calculo do arquivo taxa de servicos - cemiterio

  if v_tipo = 1 then

    select cm10_i_codigo,
           cm10_d_privenc,
           cm10_d_privenc,
           extract (day from cm10_d_privenc),
           cm10_i_numpre,
           cm10_d_data,
           cm28_i_proprietario,
           cm10_i_taxaserv,
           cm10_f_valor,
           cm10_t_obs,
           cm25_i_codigo,
           cm25_c_numero
      into v_chave,
           v_privenc,
           v_provenc,
           v_dia,
           v_numpre,
           v_dtoper,
           v_numcgm,
           v_taxaserv,
           v_valor,
           v_obs,
           v_codigo,
           v_numero
      from itenserv
           inner join txossoariojazigo on cm30_i_itenserv       = cm10_i_codigo
           inner join ossoariojazigo   on cm25_i_codigo         = cm30_i_ossoariojazigo
           inner join propricemit      on cm28_i_ossoariojazigo = cm25_i_codigo
     where cm10_i_numpre = v_codnump;

    -- observacoes do diverso
    v_obsdiver = 'REF. OSSOARIO ' || ' Cod.: ' || v_codigo || ' N: ' || v_numero;

  else


    select cm10_i_codigo,
           cm10_d_privenc,
           cm10_d_privenc,
           extract (day from cm10_d_privenc),
           cm10_i_numpre,
           cm10_d_data,
           cm01_i_declarante,
           cm10_i_taxaserv,
           cm10_f_valor,
           cm10_t_obs,
           cm01_i_codigo,
           cm05_c_numero
      into v_chave,
           v_privenc,
           v_provenc,
           v_dia,
           v_numpre,
           v_dtoper,
           v_numcgm,
           v_taxaserv,
           v_valor,
           v_obs,
           v_codigo,
           v_numero
      from itenserv
           inner join txsepultamentos on cm31_i_itenserv     = cm10_i_codigo
           inner join sepultamentos   on cm01_i_codigo       = cm31_i_sepultamento
            left join sepulta         on cm01_i_codigo       = cm24_i_sepultamento
            left join sepulturas      on cm24_i_sepultura    = cm05_i_codigo
     where cm10_i_numpre = v_codnump;

     -- observacoes do diverso
     v_obsdiver = 'REF. SEPULTAMENTO ' || ' Cod.: ' || v_codigo || ' N: ' || v_numero;

  end if;

  if v_chave is null then
    return '0 - CODIGO NAO ENCONTRADO';
  end if;
  if v_privenc is null then
    return '1 - DATA DO PRIMEIRO VENCIMENTO ZERADA';
  end if;
  if v_numparc > 1 and v_provenc is null then
    return '2 - DATA DO SEGUNDO VENCIMENTO ZERADA';
  end if;
  if v_dia is null then
    v_dia = substr(v_privenc,9,2)::integer;
  end if;

  if v_numpre is null then
    return '5 CODIGO DE ARRECADACAO NAO GERADO';
  else
    select k00_numpre
      into numpre
      from arrecad
     where k00_numpre = v_numpre
     limit 1;
    if not numpre is null and v_opcaocalc = true then
      return '4 CODIGO JA CALCULADO';
    else
      if not numpre is null then
        insert into arreold (
          k00_numpar,
          k00_numcgm,
          k00_dtoper,
          k00_receit,
          k00_hist,
          k00_valor,
          k00_dtvenc,
          k00_numtot,
          k00_numdig,
          k00_tipo,
          k00_tipojm
        )
        select k00_numpre,
               k00_numpar,
               k00_numcgm,
               k00_dtoper,
               k00_receit,
               k00_hist,
               k00_valor,
               k00_dtvenc,
               k00_numtot,
               k00_numdig,
               k00_tipo,
               k00_tipojm
          from arrecad
         where k00_numpre = v_numpre;
        delete from arrecad where k00_numpre = v_numpre;
      end if;
    end if;
  end if;

  select cm11_i_receita,
         cm11_i_historico,
         cm11_i_proced,
         cm11_i_tipo
    into v_receita,
         v_hist,
         v_proced,
         v_tipodebito
    from taxaserv
   where cm11_i_codigo = v_taxaserv;

  if v_receita is null then
    return '6 CODIGO DA RECEITA NAO ENCONTRADO NO PROCDIVER';
  end if;
  if v_hist is null then
    return '7 CODIGO DO HISTORICO NAO ENCONTRADO NO PROCDIVER';
  end if;
  if v_proced is null then
    return '8 CODIGO DO TIPO DE DEBITO NAO ENCONTRADO NO PROCDIVER';
  end if;

  v_contador = 1;
  v_valorparc = round( v_valor / v_numparc, 2 );
  v_valorrest = v_valor - ( v_valorparc * v_numparc );

  if v_raise is true then
    raise notice 'valorparc-%',v_valorparc;
    raise notice 'valorrest-%',v_valorrest;
  end if;

  loop

    if v_contador = 1 then
      v_data = v_privenc;
    end if;
    if v_contador = 2 then
      v_data = v_provenc;
    end if;
    if v_contador > 2 then
      v_data = fc_vencimento(v_provenc,v_contador - 1,v_dia);
      --v_data = (v_provenc,v_contador - 1,v_dia);
    end if;

    insert into diversos
    (
     dv05_coddiver,
     dv05_numcgm,
     dv05_dtinsc,
     dv05_exerc,
     dv05_numpre,
     dv05_vlrhis,
     dv05_procdiver,
     dv05_numtot,
     dv05_privenc,
     dv05_provenc,
     dv05_diaprox,
     dv05_oper,
     dv05_valor,
     dv05_obs,
     dv05_instit
    )
    values
    (
    nextval('diversos_dv05_coddiver_seq'),
    v_numcgm,
    v_data,
    extract (year from v_data),
    v_codnump,
    v_valorparc,
    v_proced,
    v_numparc,
    v_data,
    v_data,
    extract (day from v_data),
    v_dtoper,
    v_valorparc,
    v_obsdiver || ' - ' || trim(v_obs),
    fc_getsession('DB_instit')::integer
    );

    insert into arrecad ( k00_numcgm ,
                          k00_dtoper ,
                          k00_receit,
                          k00_hist ,
                          k00_valor,
                          k00_dtvenc,
                          k00_numpre,
                          k00_numpar,
                          k00_numtot,
                          k00_numdig,
                          k00_tipo ,
                          k00_tipojm )
                values (  v_numcgm,
                          v_dtoper,
                          v_receita,
                          v_hist,
                          v_valorparc,
                          v_data,
                          v_numpre,
                          v_contador,
                          v_numparc,
                          fc_digito(v_numpre,v_contador,v_numparc),
                          v_tipodebito,
                          0);

    --select fc_numbco(d01_codbco,d01_codage) into numbco;
    --insert into arrebanco values(numpre,numparc,r_contri.d01_codbco,r_contri.d01_codage,numbco);

    v_contador = v_contador + 1;
    if v_raise is true then
      raise notice 'contador-%, %',v_contador, v_numparc;
    end if;

    if v_contador > v_numparc then
      exit;
    else
      if v_contador = v_numparc then
        v_valorparc = v_valorparc + v_valorrest;
        if v_raise is true then
          raise notice 'valorparc2-%',v_valorparc;
        end if;
      end if;
    end if;

  end loop;
  return '9 CALCULO EFETUADO';

END;
$$ language 'plpgsql';
SQL
        );
    }
}
