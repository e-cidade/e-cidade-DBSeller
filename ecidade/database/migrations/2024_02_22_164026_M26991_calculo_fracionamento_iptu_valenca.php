<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26991CalculoFracionamentoIptuValenca extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upFuncaoFraciona();
        $this->upFuncaoCalculo();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downFuncaoFraciona();
        $this->downFuncaoCalculo();
    }

    public function upFuncaoFraciona()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists fc_iptu_fracionalote(integer,integer,boolean,boolean);
drop function if exists fc_iptu_fracionalote(integer,integer,boolean,boolean,boolean);

drop   type if exists tp_iptu_fracionalote;
create type cadastro.tp_iptu_fracionalote as (rnFracao numeric, rtDemo text, rtMsgerro text, rbErro boolean);

create or replace function cadastro.fc_iptu_fracionalote(integer,integer,boolean,boolean)
    returns tp_iptu_fracionalote
as $$

    declare

        iMatricula            alias for $1;
        iAnousu               alias for $2;
        bMostrademo           alias for $3;
        lRaise                alias for $4;

        rtp_iptu_fracionalote tp_iptu_fracionalote%ROWTYPE;

        begin

            rtp_iptu_fracionalote.rnFracao  := 0;
            rtp_iptu_fracionalote.rtDemo    := '';
            rtp_iptu_fracionalote.rtMsgerro := '';
            rtp_iptu_fracionalote.rbErro    := 'f';

            select *
              into rtp_iptu_fracionalote
            from fc_iptu_fracionalote(iMatricula, iAnousu, bMostrademo, lRaise, true);

            return rtp_iptu_fracionalote;

        end;
$$ language 'plpgsql';

create or replace function cadastro.fc_iptu_fracionalote(integer,integer,boolean,boolean,boolean)
    returns tp_iptu_fracionalote
as $$
    
    declare

        iMatricula             alias for $1;
        iAnousu                alias for $2;
        bMostrademo            alias for $3;
        lRaise                 alias for $4;
        lAtualizaFracaoForcada alias for $5;

        cSetor                 char(4);
        cQuadra                char(4);
        cLote                  char(4);

        iIptufrac              integer;
        iTotalMatriculas       integer;
        iIdbql 	               integer  default 0;

        nTotalAreaConstruida   numeric;
        rnFracao               numeric  default 0;
        nAreacalc              numeric  default 0;
        nJ01_fracao            numeric  default 0;

        bFracionaIdbql         boolean;
        bConstrucaoIrregular   boolean default false;

        tManual                text     default '';
        tSql                   text     default '';

        rFracao	               record;

        rtp_iptu_fracionalote  tp_iptu_fracionalote%ROWTYPE;

    begin

        perform fc_debug('');
        perform fc_debug(' <fracionalote> INICIANDO FRACIONAMENTO DO LOTE...');

        select j18_fracionaidbql
          into bFracionaIdbql
        from cadastro.cfiptu
        where j18_anousu = iAnousu;

        /* se o cliente for Valenca, faz uma validacao para calcular a fracao */
        select true
        into bConstrucaoIrregular
        from db_config
        where db21_codigomunicipoestado = '3306107'
          and prefeitura is true;

        if not found or bConstrucaoIrregular is null then
            bConstrucaoIrregular = false;
        end if;

        perform fc_putsession('DB_construcaoIrregular', bConstrucaoIrregular::varchar);

        perform fc_debug(' <fracionalote> Tem construcao irregular: '||bConstrucaoIrregular);

        rtp_iptu_fracionalote.rnFracao  := 0;
        rtp_iptu_fracionalote.rtDemo    := '';
        rtp_iptu_fracionalote.rtMsgerro := '';
        rtp_iptu_fracionalote.rbErro    := 'f';

        select j01_idbql, j34_setor, j34_quadra, j34_lote, j01_fracao
          into iIdbql, cSetor, cQuadra, cLote, nJ01_fracao
        from iptubase
             join lote on j34_idbql = j01_idbql
        where j01_matric = iMatricula;

        /*
         * Conta quantas Matriculas tem para o lote da Matricula a ser calculada
         */
        if bFracionaIdbql then
            perform fc_debug(' <fracionalote> Fracionamento por Idbql: '||iIdbql);

            select count(j01_idbql)
              into iTotalMatriculas
            from iptubase
                 join lote on j34_idbql = j01_idbql
            where j01_baixa is null
              and j34_idbql = iIdbql;
        else
            perform fc_debug(' <fracionalote> fracionamento por Setor: '||cSetor||' - Quadra: '||cQuadra||' Lote: '||cLote);

            select count(j01_idbql)
              into iTotalMatriculas
            from iptubase
                 join lote on j34_idbql = j01_idbql
            where j01_baixa is null
              and j34_setor  = cSetor
              and j34_quadra = cQuadra
              and j34_lote   = cLote;
        end if;

        perform fc_debug(' <fracionalote> iMatricula          : ' || iMatricula);
        perform fc_debug(' <fracionalote> fracao              : ' || rnFracao);
        perform fc_debug(' <fracionalote> total de iMatriculas: ' || iTotalMatriculas);

        if nJ01_fracao is not null and nJ01_fracao > 0 then
            rnFracao = nJ01_fracao;
        else
            if iTotalMatriculas = 1 then
                if rnFracao is null or rnFracao = 0 then
                    rnFracao = 100::numeric;
                else
                    perform fc_debug(' <fracionalote> Calculando area construida da iMatricula... ' || iMatricula);

                    /*
                     * Retorna a area total construida da MATRICULA
                     */
                    select into nAreacalc fc_iptu_getareaconstrmat(iMatricula,iAnousu,bConstrucaoIrregular);

                    perform fc_debug(' <fracionalote> Fracao de novo: ' || rnFracao);
                    perform fc_debug(' <fracionalote> fracaocalc: ' || nAreacalc);

                    if nAreacalc is null or nAreacalc = 0 then
                        rnFracao = 100;
                    else
                        rnFracao = ( (nAreacalc / rnFracao ) * 100 );
                        perform fc_debug(' <fracionalote> nAreacalc: '||nAreacalc||' - rnFracao: ' || rnFracao);
                    end if;
                end if;
            else
                /*
                 * Retorna a area total construida do LOTE
                 */
                if bFracionaIdbql then
                    select into nTotalAreaConstruida fc_iptu_getareaconstrloteidbql(iIdbql,iAnousu,bConstrucaoIrregular);
                    if (nTotalAreaConstruida = 0 or nTotalAreaConstruida is null) and bConstrucaoIrregular then
                        bConstrucaoIrregular = false;
                        select into nTotalAreaConstruida fc_iptu_getareaconstrloteidbql(iIdbql,iAnousu,bConstrucaoIrregular);
                    end if;

                    perform fc_debug(' <fracionalote> Busca area total construida por idbql: '||nTotalAreaConstruida);
                else
                    select into nTotalAreaConstruida fc_iptu_getareaconstrlote(cSetor,cQuadra,cLote,iAnousu,bConstrucaoIrregular);
                    if (nTotalAreaConstruida = 0 or nTotalAreaConstruida is null) and bConstrucaoIrregular then
                        bConstrucaoIrregular = false;
                        select into nTotalAreaConstruida fc_iptu_getareaconstrlote(cSetor,cQuadra,cLote,iAnousu,bConstrucaoIrregular);
                    end if;

                    perform fc_debug(' <fracionalote> Busca area total construida por Setor/Quadra/Lote: '||nTotalAreaConstruida);
                end if;

                perform fc_debug(' <fracionalote> Total construido no lote: ' || nTotalAreaConstruida);

                tManual := tManual || 'total construido no lote: ' || nTotalAreaConstruida || ' - ';

                if nTotalAreaConstruida = 0 then

                    select j01_fracao
                    into nJ01_fracao
                    from iptubase
                    where j01_matric = iMatricula;

                    if nJ01_fracao = 0 or nJ01_fracao is null then

                        if lAtualizaFracaoForcada and (bMostrademo is false) then
                            update iptubase set j01_fracao = 0 where j01_idbql = iIdbql;
                        end if;

                        rnFracao = 100::numeric;
                    else
                        rnFracao = nJ01_fracao;
                    end if;

                else
                    perform fc_debug(' <fracionalote> Fraciona rFracao ');

                    tSql :=         'select j01_matric, sum(j39_area) as j39_area ';
                    tSql := tSql || 'from iptubase ';
                    tSql := tSql || '     join iptuconstr on j39_matric = j01_matric ';

                    if bConstrucaoIrregular then
                        perform fc_debug(' <fracionalote> Desconsidera construcoes irregulares na matricula');
                        tSql := tSql || ' join carconstr on j48_matric = j39_matric ';
                        tSql := tSql || '               and j48_idcons = j39_idcons ';
                        tSql := tSql || ' join caracter on j31_codigo = j48_caract ';
                        tSql := tSql || '              and j31_grupo = 7200 ';
                        tSql := tSql || ' left join carfator on j74_anousu = ' || iAnousu;
                        tSql := tSql || '                   and j74_caract = j48_caract ';
                    end if;

                    tSql := tSql || 'where j01_baixa  is null ';
                    tSql := tSql || '  and j01_matric = ' || iMatricula;
                    tSql := tSql || '  and j39_dtdemo is null ';

                    if bConstrucaoIrregular then
                        tSql := tSql || 'and j74_caract is null ';
                    end if;

                    tSql := tSql || 'group by j01_matric ';

                    for rFracao in execute tSql loop

                        perform fc_debug(' <fracionalote> processando fracao iMatricula: '||coalesce(rFracao.j01_matric,0)||' - construido desta: ' || coalesce(rFracao.j39_area,0), lRaise );

                        select j25_matric
                        into iIptufrac
                        from iptufrac
                        where j25_matric = rFracao.j01_matric
                          and j25_anousu = iAnousu;

                        perform fc_debug(' <fracionalote>    iptufrac: ' || coalesce( iIptufrac, 0 ));
                        perform fc_debug(' <fracionalote>    Area Total Construida: '||nTotalAreaConstruida);

                        if (bMostrademo is false) then
                            if iIptufrac is null or iIptufrac = 0 then
                                perform fc_debug(' <fracionalote>    insert no iptufrac');
                                insert into iptufrac values (iAnousu, rFracao.j01_matric, iIdbql, rFracao.j39_area / nTotalAreaConstruida * 100);
                            else
                                perform fc_debug(' <fracionalote>    update no iptufrac');
                                update iptufrac
                                set j25_fracao = rFracao.j39_area / nTotalAreaConstruida * 100,
                                    j25_idbql  = iIdbql
                                where j25_matric = rFracao.j01_matric
                                  and j25_anousu = iAnousu;
                            end if;
                        end if;
                    end loop;

                    select j25_fracao
                    into rnFracao
                    from iptufrac
                    where j25_matric = iMatricula
                      and j25_anousu = iAnousu;

                    if rnFracao is null or rnFracao = 0 then
                        rnFracao = 100::numeric;
                    end if;

                    if bConstrucaoIrregular and rFracao.j01_matric is null then
                        perform fc_debug(' <fracionalote> Construcao irregular, atualiza iptufrac');
                        rnFracao = 0;
                        update iptufrac
                        set j25_fracao = rnFracao
                        where j25_matric = iMatricula
                          and j25_anousu = iAnousu;
                    end if;
                end if;
            end if;
        end if;

        rtp_iptu_fracionalote.rnFracao := rnFracao;
        rtp_iptu_fracionalote.rtDemo   := tManual;

        perform fc_debug(' <fracionalote> texto demonstrativo :' || tManual);
        perform fc_debug(' <fracionalote> FIM FRACIONAMENTO DO LOTE');
        perform fc_debug(' ');

        return rtp_iptu_fracionalote;
    end;
$$ language 'plpgsql';

drop function if exists fc_iptu_getareaconstrlote(char(4),char(4),char(4));
drop function if exists fc_iptu_getareaconstrlote(char(4),char(4),char(4),integer,boolean);

create or replace function cadastro.fc_iptu_getareaconstrlote(char(4),char(4),char(4),integer,boolean)
    returns numeric
as $$

declare

    sSetor                 alias for $1;
    sQuadra                alias for $2;
    sLote                  alias for $3;
    iAnousu                alias for $4;
    bConstrucaoIrregular   alias for $5;


    nTotalAreaConstruida   numeric default 0;

    sSql                   varchar := '';

begin

    sSql := 'with matriculas as (';
    sSql := sSql || 'select j39_matric, j39_idcons, j39_area ';
    sSql := sSql || 'from lote ';
    sSql := sSql || '     join iptubase on j01_idbql = j34_idbql ';
    sSql := sSql || '     join iptuconstr on j39_matric = j01_matric ';
    sSql := sSql || 'where j34_setor = \'' || sSetor || '\'';
    sSql := sSql || '  and j34_quadra = \'' || sQuadra || '\'';
    sSql := sSql || '  and j34_lote = \'' || sLote || '\'';
    sSql := sSql || '  and j01_baixa  is null ';
    sSql := sSql || '  and j39_dtdemo is null), ';

    if bConstrucaoIrregular then
        sSql := sSql || 'matriculas_irregulares as (';
        sSql := sSql || '    select j39_matric as matric, j39_idcons as idcons ';
        sSql := sSql || '    from lote ';
        sSql := sSql || '         join iptubase on j01_idbql = j34_idbql ';
        sSql := sSql || '         join iptuconstr on j39_matric = j01_matric ';
        sSql := sSql || '         join carconstr on j48_matric = j39_matric ';
        sSql := sSql || '                       and j48_idcons = j39_idcons ';
        sSql := sSql || '         join caracter on j31_codigo = j48_caract ';
        sSql := sSql || '                      and j31_grupo = 7200 ';
        sSql := sSql || '         join carfator on j74_anousu = ' || iAnousu;
        sSql := sSql || '                      and j74_caract = j48_caract ';
        sSql := sSql || '    where j34_setor = \'' || sSetor || '\'';
        sSql := sSql || '      and j34_quadra = \'' || sQuadra || '\'';
        sSql := sSql || '      and j34_lote = \'' || sLote || '\'';
        sSql := sSql || '      and j01_baixa  is null ';
        sSql := sSql || '      and j39_dtdemo is null), ';
        sSql := sSql || 'matriculas_ativas as (';
        sSql := sSql || '    select j39_matric, j39_idcons, j39_area ';
        sSql := sSql || '    from matriculas ';
        sSql := sSql || '         left join matriculas_irregulares on matric = j39_matric ';
        sSql := sSql || '                                         and idcons = j39_idcons ';
        sSql := sSql || '    where matric is null) ';
    else
        sSql := sSql || 'matriculas_ativas as (';
        sSql := sSql || '    select j39_matric, j39_idcons, j39_area ';
        sSql := sSql || '    from matriculas) ';
    end if;

    sSql := sSql || 'select round(coalesce(sum(j39_area), 0), 2)::numeric ';
    sSql := sSql || 'from matriculas_ativas; ';

    execute sSql into nTotalAreaConstruida;

    return nTotalAreaConstruida;

end;
$$ language 'plpgsql';

drop function if exists fc_iptu_getareaconstrloteidbql(integer);
drop function if exists fc_iptu_getareaconstrloteidbql(integer,integer,boolean);

create or replace function cadastro.fc_iptu_getareaconstrloteidbql(integer,integer,boolean)
    returns numeric
as $$

    declare

        iIdbql                 alias for $1;
        iAnousu                alias for $2;
        bConstrucaoIrregular   alias for $3;

        nTotalAreaConstruida   numeric default 0;

        sSql                   varchar := '';

    begin

        sSql := 'with matriculas as (';
        sSql := sSql || 'select j39_matric, j39_idcons, j39_area ';
        sSql := sSql || 'from iptubase ';
        sSql := sSql || '     join iptuconstr on j39_matric = j01_matric ';
        sSql := sSql || 'where j01_baixa  is null ';
        sSql := sSql || '  and j01_idbql  = ' || iIdbql;
        sSql := sSql || '  and j39_dtdemo is null), ';

        if bConstrucaoIrregular then
            sSql := sSql || 'matriculas_irregulares as (';
            sSql := sSql || '    select j39_matric as matric, j39_idcons as idcons ';
            sSql := sSql || '    from iptubase ';
            sSql := sSql || '         join iptuconstr on j39_matric = j01_matric ';
            sSql := sSql || '         join carconstr on j48_matric = j39_matric ';
            sSql := sSql || '                       and j48_idcons = j39_idcons ';
            sSql := sSql || '         join caracter on j31_codigo = j48_caract ';
            sSql := sSql || '                      and j31_grupo = 7200 ';
            sSql := sSql || '         join carfator on j74_anousu = ' || iAnousu;
            sSql := sSql || '                      and j74_caract = j48_caract ';
            sSql := sSql || '    where j01_baixa  is null ';
            sSql := sSql || '      and j01_idbql  = ' || iIdbql;
            sSql := sSql || '      and j39_dtdemo is null), ';
            sSql := sSql || 'matriculas_ativas as (';
            sSql := sSql || '    select j39_matric, j39_idcons, j39_area ';
            sSql := sSql || '    from matriculas ';
            sSql := sSql || '         left join matriculas_irregulares on matric = j39_matric ';
            sSql := sSql || '                                         and idcons = j39_idcons ';
            sSql := sSql || '    where matric is null) ';
        else
            sSql := sSql || 'matriculas_ativas as (';
            sSql := sSql || '    select j39_matric, j39_idcons, j39_area ';
            sSql := sSql || '    from matriculas) ';
        end if;

        sSql := sSql || 'select round(coalesce(sum(j39_area), 0), 2)::numeric ';
        sSql := sSql || 'from matriculas_ativas; ';

        execute sSql into nTotalAreaConstruida;

        return nTotalAreaConstruida;

    end;
$$ language 'plpgsql';

drop function if exists fc_iptu_getareaconstrmat(integer);
drop function if exists fc_iptu_getareaconstrmat(integer, integer,boolean);

create or replace function cadastro.fc_iptu_getareaconstrmat(integer,integer,boolean)
    returns numeric
as $$

declare

    iMatricula             alias for $1;
    iAnousu                alias for $2;
    bConstrucaoIrregular   alias for $3;

    nTotalAreaConstruida   numeric default 0;

    sSql                   varchar := '';

begin

    sSql := 'with matriculas as (';
    sSql := sSql || 'select j39_matric, j39_idcons, j39_area ';
    sSql := sSql || 'from iptubase ';
    sSql := sSql || '     join iptuconstr on j39_matric = j01_matric ';
    sSql := sSql || 'where j01_baixa  is null ';
    sSql := sSql || '  and j01_matric  = ' || iMatricula;
    sSql := sSql || '  and j39_dtdemo is null), ';

    if bConstrucaoIrregular then
        sSql := sSql || 'matriculas_irregulares as (';
        sSql := sSql || '    select j39_matric as matric, j39_idcons as idcons ';
        sSql := sSql || '    from iptubase ';
        sSql := sSql || '         join iptuconstr on j39_matric = j01_matric ';
        sSql := sSql || '         join carconstr on j48_matric = j39_matric ';
        sSql := sSql || '                       and j48_idcons = j39_idcons ';
        sSql := sSql || '         join caracter on j31_codigo = j48_caract ';
        sSql := sSql || '                      and j31_grupo = 7200 ';
        sSql := sSql || '         join carfator on j74_anousu = ' || iAnousu;
        sSql := sSql || '                      and j74_caract = j48_caract ';
        sSql := sSql || '    where j01_baixa  is null ';
        sSql := sSql || '      and j01_matric = ' || iMatricula;
        sSql := sSql || '      and j39_dtdemo is null), ';
        sSql := sSql || 'matriculas_ativas as (';
        sSql := sSql || '    select j39_matric, j39_idcons, j39_area ';
        sSql := sSql || '    from matriculas ';
        sSql := sSql || '         left join matriculas_irregulares on matric = j39_matric ';
        sSql := sSql || '                                         and idcons = j39_idcons ';
        sSql := sSql || '    where matric is null) ';
    else
        sSql := sSql || 'matriculas_ativas as (';
        sSql := sSql || '    select j39_matric, j39_idcons, j39_area ';
        sSql := sSql || '    from matriculas) ';
    end if;

    sSql := sSql || 'select round(coalesce(sum(j39_area), 0), 2)::numeric ';
    sSql := sSql || 'from matriculas_ativas; ';

    execute sSql into nTotalAreaConstruida;

    return nTotalAreaConstruida;

end;
$$ language 'plpgsql';

SQL
        );
    }

    public function upFuncaoCalculo()
    {
        DB::connection()->getPdo()->exec(<<<SQL
CREATE OR REPLACE FUNCTION cadastro.fc_calculoiptu_valenca_2023(integer,integer,boolean,boolean,boolean,boolean,boolean,integer,integer)
    RETURNS varchar(100) AS
$$

declare

    iMatricula                          alias   for $1;
    iAnousu                             alias   for $2;
    lGerafinanc                         alias   for $3;
    lAtualizaParcela                    alias   for $4;
    lNovonumpre                         alias   for $5;
    lCalculogeral                       alias   for $6;
    lDemonstrativo                      alias   for $7;
    iParcelaini                         alias   for $8;
    iParcelafim                         alias   for $9;

    iIdbql                              integer default 0;
    iNumcgm                             integer default 0;
    iCodcli                             integer default 0;
    iCodisen                            integer default 0;
    iTipois                             integer default 0;
    iParcelas                           integer default 0;
    iNumconstr                          integer default 0;
    iCodErro                            integer default 0;

    dDatabaixa                          date;

    nAreal                              numeric default 0;
    nAreac                              numeric default 0;
    nTotarea                            numeric default 0;
    nFracao                             numeric default 0;
    nFracaolote                         numeric default 0;
    nAliquota                           numeric default 0;
    nIsenaliq                           numeric default 0;
    nArealo                             numeric default 0;
    nVvc                                numeric(15,2) default 0;
    nVvt                                numeric(15,2) default 0;
    nVv                                 numeric(15,2) default 0;
    nViptu                              numeric(15,2) default 0;

    tRetorno                            text default '';
    tDemo                               text default '';
    tErro                               text default '';

   lPredial                             boolean;
   lFinanceiro                          boolean;
   lDadosIptu                           boolean;
   lErro                                boolean;
   lIsentaxas                           boolean;
   lTempagamento                        boolean;
   lEmpagamento                         boolean;
   lTaxasCalculadas                     boolean;
   lRaise                               boolean default false; -- true para habilitar raise na funcao principal
   lSubRaise                            boolean default false; -- true para habilitar raise nas sub-funcoes

   rCfiptu                              record;

begin

    lRaise := ( case when fc_getsession('DB_debugon') is null then false else true end );
    lSubRaise := lRaise;

    perform fc_debug('INICIANDO CALCULO', lRaise);
    perform fc_debug('', lRaise);

    /**
     * Guarda os parametros do calculo
     */

    select * from into rCfiptu cfiptu where j18_anousu = iAnousu;

    select j34_area, coalesce(j34_totcon, 0)
    into nArealo, nTotarea
    from iptubase
             join lote on j34_idbql = j01_idbql
    where j01_matric = iMatricula;

    if not found then
        select fc_iptu_geterro( 3, 'ERRO - Sem dados para a matricula '||iMatricula ) into tRetorno;
        return tRetorno;
    end if;

    /**
     * Executa PRE CALCULO
     */
    select r_iIdbql, r_nAreal, r_nFracao, r_iNumcgm, r_dDatabaixa, r_nFracaolote,
           r_tDemo, r_lTempagamento, r_lEmpagamento, r_iCodisen, r_iTipois, r_nIsenaliq,
           r_lIsentaxas, r_nArealote, r_iCodCli, r_tRetorno
    into iIdbql, nAreal, nFracao, iNumcgm, dDatabaixa, nFracaolote, tDemo, lTempagamento,
        lEmpagamento, iCodisen, iTipois, nIsenaliq, lIsentaxas, nArealo, iCodCli, tRetorno
    from fc_iptu_precalculo( iMatricula, iAnousu, lCalculogeral, lAtualizaParcela, lDemonstrativo, lRaise );

    perform fc_debug(' RETORNO DA PRE CALCULO: ',             lRaise);
    perform fc_debug('  iIdbql        -> ' || iIdbql,         lRaise);
    perform fc_debug('  nAreal        -> ' || nAreal,         lRaise);
    perform fc_debug('  nFracao       -> ' || nFracao,        lRaise);
    perform fc_debug('  iNumcgm       -> ' || iNumcgm,        lRaise);
    perform fc_debug('  dDatabaixa    -> ' || dDatabaixa,     lRaise);
    perform fc_debug('  nFracaolote   -> ' || nFracaolote,    lRaise);
    perform fc_debug('  tDemo         -> ' || tDemo,          lRaise);
    perform fc_debug('  lTempagamento -> ' || lTempagamento,  lRaise);
    perform fc_debug('  lEmpagamento  -> ' || lEmpagamento,   lRaise);
    perform fc_debug('  iCodisen      -> ' || iCodisen,       lRaise);
    perform fc_debug('  iTipois       -> ' || iTipois,        lRaise);
    perform fc_debug('  nIsenaliq     -> ' || nIsenaliq,      lRaise);
    perform fc_debug('  lIsentaxas    -> ' || lIsentaxas,     lRaise);
    perform fc_debug('  nArealote     -> ' || nArealo,        lRaise);
    perform fc_debug('  iCodCli       -> ' || iCodCli,        lRaise);
    perform fc_debug('  tRetorno      -> ' || tRetorno,       lRaise);
    perform fc_debug('', lRaise);

    /**
     * Variavel de retorno contem a msg
     * de erro retornada do pre calculo
     */

    if trim(tRetorno) <> '' then
        return tRetorno;
    end if;

    update tmpdadosiptu set matric = iMatricula;

    update tmpdadostaxa
    set anousu = iAnousu,
        matric = iMatricula,
        idbql  = iIdbql,
        valref = rCfiptu.j18_vlrref;

    /**
     * Calcula valor do terreno
     */

    perform fc_debug('PARAMETROS fc_iptu_calculavvt_valenca_2023 IDBQL: '||iIdbql||' - iMatricula: '||iMatricula||' - Anousu: '||iAnousu||' - FRACAO DO LOTE: '||nFracaolote||' - DEMO: '||lDemonstrativo||' - DEBUG: '||lRaise, lRaise);

    select rnvvt, rnarea, rtdemo, rtmsgerro, rberro, riCodErro, rtErro
    into nVvt, nAreac, tDemo, tRetorno, lErro, iCodErro, tErro
    from fc_iptu_calculavvt_valenca_2023( iIdbql, iMatricula, iAnousu, nFracaolote, lDemonstrativo, lRaise);

    perform fc_debug('RETORNO fc_iptu_calculavvt_valenca_2023 -> VVT: '||nVvt||' - AREA CONSTRUIDA: '||nAreac||' - RETORNO: '||tRetorno||' - ERRO: '||lErro, lRaise);
    perform fc_debug('', lRaise);

    if lErro is true then
        select fc_iptu_geterro( iCodErro, tErro ) into tRetorno;
        return tRetorno;
    end if;

    /**
     * Calcula valor da construcao
     */

    perform fc_debug('PARAMETROS fc_iptu_calculavvc_valenca_2023 MATRICULA: '||iMatricula||' - ANOUSU: '||iAnousu||' - DEMO: '||lDemonstrativo||' - DEBUG: '||lRaise, lRaise);

    select rnvvc, rntotarea, rinumconstr, rtdemo, rtmsgerro, rberro, riCodErro, rtErro
      into nVvc, nTotarea, iNumconstr, tDemo, tRetorno, lErro, iCodErro, tErro
    from fc_iptu_calculavvc_valenca_2023( iMatricula, iAnousu, lDemonstrativo, lRaise );

    perform fc_debug('RETORNO fc_iptu_calculavvc_valenca_2023 -> VVC: '||nVvc||' - AREA TOTAL: '||nTotarea||' - NUMERO DE CONSTRUCOES: '||iNumconstr||' - RETORNO: '||tRetorno||' - ERRO: '||lErro, lRaise);
    perform fc_debug('', lRaise);

    if lErro is true then
        select fc_iptu_geterro(iCodErro, tErro) into tRetorno;
        return tRetorno;
    end if;

    select predial into lPredial from tmpdadosiptu;

    /* BUSCA A ALIQUOTA  */

    perform fc_debug('BUSCA A ALIQUOTA DO IPTU ', lRaise);

    select coderro, descrerro, aliquota
    into iCodErro, tErro, nAliquota
    from fc_iptu_getaliquota_valenca_2023(iMatricula, iIdbql, iAnousu, lPredial, lSubRaise);

    if nAliquota = 0 then
        select fc_iptu_geterro( iCodErro, tErro ) into tRetorno;
        return tRetorno;
    end if;

    perform fc_debug('RETORNO DA BUSCA A ALIQUOTA DO IPTU ', lRaise);
    perform fc_debug(' ', lRaise);

    /*--------- CALCULA O VALOR VENAL -----------*/

    perform fc_debug('valor venal construcao (nVvc) - '||nVvc||' valor venal terreno (nVvt) - '||nVvt, lRaise);

    nVv    := nVvc + nVvt;

    perform fc_debug('valor venal total - '||nVv, lRaise);

    nViptu := nVv * ( nAliquota / 100 );

    if nViptu < rCfiptu.j18_vlrref then
        perform fc_debug('Valor do IPTU menor que a UFIVA', lRaise);
        nViptu := rCfiptu.j18_vlrref;
    end if;

    perform fc_debug('valor iptu '||nViptu||' - aliquota '||nAliquota||'%', lRaise);
    perform fc_debug(' ', lRaise);
    perform fc_debug('Inserindo as receitas de IPTU na tabela tmprecval ', lRaise);
    perform fc_debug(' ', lRaise);

    if lPredial then
        insert into tmprecval values (rCfiptu.j18_rpredi, nViptu, 1, false);
    else
        insert into tmprecval values (rCfiptu.j18_rterri, nViptu, 1, false);
    end if;

    perform fc_debug('Inserindo valor e codigo de vencimento do IPTU na tabela tmpdadosiptu', lRaise);
    perform fc_debug(' ', lRaise);

    update tmpdadosiptu
    set viptu   = nViptu,
        codvenc = rCfiptu.j18_vencim;

    /*-------------------------------------------*/

    select count(*)
    into iParcelas
    from cadvencdesc
             inner join cadvenc on q92_codigo = q82_codigo
    where q92_codigo = rCfiptu.j18_vencim ;

    if not found or iParcelas = 0 then
        select fc_iptu_geterro(14,'') into tRetorno;
        return tRetorno;
    end if;

    update tmpdadostaxa set valiptu = nViptu, vvt = nVvt, nparc = iParcelas, totareaconst = nTotarea;

    /* CALCULA AS TAXAS */

    perform fc_debug('PARAMETROS fc_iptu_calculataxas  ANOUSU '||iAnousu||' -- CODCLI '||iCodcli||' -- Debug: '||lSubRaise, lRaise);
    perform fc_debug(' ', lRaise);

    select fc_iptu_calculataxas(iMatricula, iAnousu, iCodcli, lSubRaise)
    into lTaxasCalculadas;

    perform fc_debug('RETORNO fc_iptu_calculataxas --->>> TAXAS CALCULADAS - '||lTaxasCalculadas, lRaise);

    /* MONTA O DEMONSTRATIVO */
    select fc_iptu_demonstrativo(iMatricula,iAnousu,iIdbql,lSubRaise )
    into tDemo;

    /* GERA FINANCEIRO */
    if lDemonstrativo is false then -- Se nao for demonstrativo gera o financeiro, caso contrario retorna o demonstrativo
        select fc_iptu_geradadosiptu(iMatricula,iIdbql,iAnousu,nIsenaliq,lDemonstrativo,lSubRaise)
        into lDadosIptu;
        if lGerafinanc then
            select fc_iptu_gerafinanceiro(iMatricula,iAnousu,iParcelaini,iParcelafim,lCalculogeral,lTempagamento,lNovonumpre,lDemonstrativo,lSubRaise)
            into lFinanceiro;
        end if;
    else
        return tDemo;
    end if;

    if lDemonstrativo is false then
        update iptucalc
        set j23_manual = tDemo
        where j23_matric = iMatricula
          and j23_anousu = iAnousu;
    end if;

    select fc_iptu_geterro(1, '') into tRetorno;

    return tRetorno;

end;
$$ LANGUAGE 'plpgsql';

create or replace function cadastro.fc_iptu_calculavvc_valenca_2023(integer,integer,boolean,boolean)
    returns tp_iptu_calculavvc as
$$

    declare

        iMatricula          alias for $1;
        iAnousu             alias for $2;
        lMostrademo         alias for $3;
        lRaise              alias for $4;

        nAreatc             numeric default 0;
        nVm2c               numeric default 0;
        nVvcP               numeric default 0;
        nVvc                numeric default 0;
        nFatorIdade         numeric default 0;
        nFatorLocalizacao   numeric default 0;

        iNumerocontr        integer default 0;
        iPontos             integer default 0;
        iTotalPontos        integer default 0;
        iTipoConstrucao     integer default 0;

        lAtualiza           boolean default true;

        rConstr             record;
        rCargrup            record;

        jCargrup            json;

        tMsg                text;

        rtp_iptu_calculavvc tp_iptu_calculavvc%ROWTYPE;

    begin

        perform fc_debug('INICIANDO CALCULO VVC ...');

        rtp_iptu_calculavvc.rnVvc       := 0;
        rtp_iptu_calculavvc.rnTotarea   := 0;
        rtp_iptu_calculavvc.riNumconstr := 0;
        rtp_iptu_calculavvc.rtDemo      := '';
        rtp_iptu_calculavvc.rtMsgerro   := 'Retorno ok' ;
        rtp_iptu_calculavvc.rbErro      := 'f';
        rtp_iptu_calculavvc.riCodErro   := 0;
        rtp_iptu_calculavvc.rtErro      := '';

        /* busca os grupos de caracteristicas com pontuacao */
        select json_agg(cargrup.*)
          into jCargrup
        from cargrup
        where j32_grupo in (1700, 1800, 1900, 2000, 2100, 2200, 2300, 2400, 7800, 7900, 8000, 8100);

        iNumerocontr := 0;

        for rConstr in select j39_matric, j39_idcons, j39_idprinc, j39_area, j39_areap, coalesce(j39_ano, 0) as j39_ano
                      from iptuconstr
                     where j39_matric = iMatricula
                       and j39_dtdemo is null
        loop

                /* busca os pontos de cada construcao */
                iTotalPontos := 0;
                for rCargrup in select (value->>'j32_grupo')::int as codigo, value->>'j32_descr' as descricao
                                from json_array_elements(jCargrup)
                loop
                        select coalesce(j31_pontos, 0)
                        into iPontos
                        from carconstr
                             join caracter on j31_codigo = j48_caract
                                          and j31_grupo  = rCargrup.codigo
                        where j48_matric = rConstr.j39_matric
                          and j48_idcons = rConstr.j39_idcons;

                        if not found then
                            tMsg := ' PARA O GRUPO '||rCargrup.codigo||' - '||rCargrup.descricao;
                            perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                             rConstr.j39_idcons||' nao encontrado '||tMsg);

                            rtp_iptu_calculavvc.rtErro      := tMsg;
                            rtp_iptu_calculavvc.rtMsgErro   := 'sem caracteristica '||tMsg;
                            rtp_iptu_calculavvc.riCodErro   := 23;
                            rtp_iptu_calculavvc.rbErro      := 't';

                            return rtp_iptu_calculavvc;
                        end if;

                        iTotalPontos := iTotalPontos + iPontos;
                end loop;

                if iTotalPontos = 0 then
                    perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                     rConstr.j39_idcons||' sem pontuacao.');
                    rtp_iptu_calculavvc.rtErro      := ' PARA O GRUPO 500';
                    rtp_iptu_calculavvc.rtMsgErro   := 'SEM PONTUACAO PARA A EDIFICACAO '||rConstr.j39_idcons;
                    rtp_iptu_calculavvc.riCodErro   := 23;
                    rtp_iptu_calculavvc.rbErro      := 't';
                    return rtp_iptu_calculavvc;
                end if;

                /* busca a caracteristica do grupo 500 - tipo construcao */
                select j48_caract
                  into iTipoConstrucao
                from carconstr
                     join caracter on j31_codigo = j48_caract
                                  and j31_grupo  = 500
                where j48_matric = rConstr.j39_matric
                  and j48_idcons = rConstr.j39_idcons;

                if not found then
                    perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                     rConstr.j39_idcons||' nao encontrado a caracteristica do grupo 500.');
                    rtp_iptu_calculavvc.rtErro      := ' 500';
                    rtp_iptu_calculavvc.rtMsgErro   := 'SEM CARACTERISITICA PARA O GRUPO 500';
                    rtp_iptu_calculavvc.riCodErro   := 102;
                    rtp_iptu_calculavvc.rbErro      := 't';
                    return rtp_iptu_calculavvc;
                end if;

                /* busca o valor do m2 para a construcao */
                select j71_valor::numeric
                  into nVm2c
                from carvalor
                where j71_anousu = iAnousu
                  and j71_caract = iTipoConstrucao
                  and iTotalPontos between j71_quantini and j71_quantfim;

                if not found then
                    perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                     rConstr.j39_idcons||' nao encontrado para caracteristica '||iTipoConstrucao);
                    rtp_iptu_calculavvc.rtErro      := ' OU GRUPO 500 NAO ENCONTRADO OU SEM VALOR PARA O ANO DE '||iAnousu;
                    rtp_iptu_calculavvc.rtMsgErro   := 'SEM CARACTERISITICA PARA O GRUPO 500 OU SEM VALOR PARA O ANO DE '||iAnousu;
                    rtp_iptu_calculavvc.riCodErro   := 29;
                    rtp_iptu_calculavvc.rbErro      := 't';
                    return rtp_iptu_calculavvc;
                end if;

                select coalesce(j74_fator, 0)
                  into nFatorIdade
                from carconstr
                     join caracter on j31_codigo = j48_caract
                                  and j31_grupo = 7200
                     join carfator on j74_anousu = iAnousu
                                  and j74_caract = j48_caract
                where j48_matric = rConstr.j39_matric
                  and j48_idcons = rConstr.j39_idcons;

                if not found or nFatorIdade = 0 then
                    /* validar o ano da construcao */
                    if rConstr.j39_ano = 0 then
                        perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                         rConstr.j39_idcons||' com ano da construcao invalido.');
                        rtp_iptu_calculavvc.rtErro      := '';
                        rtp_iptu_calculavvc.rtMsgErro   := 'ANO DA CONSTRUCAO INVALIDO - CONSTRUCAO: '||rConstr.j39_idcons;
                        rtp_iptu_calculavvc.riCodErro   := 116;
                        rtp_iptu_calculavvc.rbErro      := 't';
                        return rtp_iptu_calculavvc;
                    end if;

                    /* busca fator idade da construcao */
                    select coalesce(j118_fatorreajuste, 0)::numeric
                      into nFatorIdade
                    from iptupadraoconstrpontos
                    where j118_anousu = iAnousu
                      and j118_caracter = iTipoConstrucao
                      and (iAnousu - rConstr.j39_ano) between j118_pontosini and j118_pontosfim;

                    if not found or nFatorIdade = 0 then
                        perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                         rConstr.j39_idcons||' fator idade da construcao nao encontrado ou zerado para o ano de '||iAnousu);
                        rtp_iptu_calculavvc.rtErro      := ' OU SEM VALOR PARA O ANO DE '||iAnousu||' - CONSTRUCAO: '||rConstr.j39_idcons;
                        rtp_iptu_calculavvc.rtMsgErro   := 'FATOR IDADE DA CONSTRUCAO NAO ENCONTRADO OU SEM VALOR PARA O ANO DE '||iAnousu||' - CONSTRUCAO: '||rConstr.j39_idcons;
                        rtp_iptu_calculavvc.riCodErro   := 36;
                        rtp_iptu_calculavvc.rbErro      := 't';
                        return rtp_iptu_calculavvc;
                    end if;
                end if;

                /* busca o fator localizacao da construcao */
                select coalesce(j74_fator, 0)::numeric
                  into nFatorLocalizacao
                from carconstr
                     join caracter on j31_codigo = j48_caract
                                  and j31_grupo = 6700
                     join carfator on j74_anousu = iAnousu
                                  and j74_caract = j48_caract
                where j48_matric = rConstr.j39_matric
                  and j48_idcons = rConstr.j39_idcons;

                if not found or nFatorLocalizacao = 0 then
                    perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                     rConstr.j39_idcons||' fator localizacao da construcao nao encontrado ou zerado para o ano de '||iAnousu);
                    rtp_iptu_calculavvc.rtErro    := ' OU SEM VALOR PARA O ANO DE '||iAnousu;
                    rtp_iptu_calculavvc.rtMsgErro := 'FATOR LOCALIZACAO DA CONSTRUCAO NAO ENCONTRADO OU SEM VALOR PARA O ANO DE '||iAnousu;
                    rtp_iptu_calculavvc.riCodErro := 37;
                    rtp_iptu_calculavvc.rbErro    := 't';
                    return rtp_iptu_calculavvc;
                end if;

                /* calcula o valor venal da construcao */
                nVvcp   := rConstr.j39_area * nVm2c * nFatorIdade * nFatorLocalizacao;
                nAreatc := nAreatc + rConstr.j39_area;
                nVvc    := nVvc + nVvcp;

                perform fc_debug('< calculo vvc > Valor venal total parcial - nVvc - '||nVvc||' nVm2c - '||nVm2c|| 'area - '||rConstr.j39_area);

                iNumerocontr := iNumerocontr + 1;

                insert into tmpiptucale (anousu, matric, idcons, areaed, vm2, pontos, valor)
                                 values (iAnousu, iMatricula, rConstr.j39_idcons, rConstr.j39_area, nVm2c, iTotalPontos, nVvcp);
                if lAtualiza then
                    update tmpdadosiptu set predial = true;
                    lAtualiza := false;
                end if;
        end loop;

        nVvc := round(nVvc, 2);
        perform fc_debug('< calculo vvc > Valor venal total final - nVvc - '||nVvc);

        rtp_iptu_calculavvc.rnVvc       := nVvc::numeric;
        rtp_iptu_calculavvc.rnTotarea   := nAreatc::numeric;
        rtp_iptu_calculavvc.riNumconstr := iNumerocontr;
        rtp_iptu_calculavvc.rtDemo      := '';
        rtp_iptu_calculavvc.rbErro      := 'f';

        update tmpdadosiptu set vvc = rtp_iptu_calculavvc.rnVvc;

        return rtp_iptu_calculavvc;
    end;
$$ language 'plpgsql';

create or replace function cadastro.fc_iptu_calculavvt_valenca_2023(integer,integer,integer,numeric,boolean,boolean) returns tp_iptu_calculavvt as
$$

declare

    iIdbql                   alias for $1;
    iMatricula               alias for $2;
    iAnousu                  alias for $3;
    nFracao                  alias for $4;
    lMostrademo              alias for $5;
    lRaise                   alias for $6;
  
    lPredial                 boolean default false;
    lConstrucaoIrregular     boolean;

    nVm2t                    numeric default 0;
    nAreaLoteCorrigi         numeric default 0;
    nAreaTerreno             numeric default 0;
    nValor                   numeric default 0;
    nTestada                 numeric default 0;
    nFatorSituacao           numeric default 0;
    nFatorCondFisTerreno     numeric default 0;
    nAreaConstruida          numeric default 0;

    iZona                    bigint default 0;

    rtp_iptu_calculavvt      tp_iptu_calculavvt%ROWTYPE;
  
begin

    rtp_iptu_calculavvt.rnAreaTotalC := 0;
    rtp_iptu_calculavvt.rnArea       := 0;
    rtp_iptu_calculavvt.rnTestada    := 0;
    rtp_iptu_calculavvt.riCoderro    := 0;
    rtp_iptu_calculavvt.rtDemo       := '';
    rtp_iptu_calculavvt.rtMsgerro    := '';
    rtp_iptu_calculavvt.rtErro       := '';
    rtp_iptu_calculavvt.rbErro       := 'f';

    perform fc_debug('< calculo vvt > INICIANDO CALCULO DO VALOR VENAL TERRITORIAL...');

    lConstrucaoIrregular := ( case when fc_getsession('DB_construcaoIrregular') is not null and fc_getsession('DB_construcaoIrregular') = 'true' then true else false end );

    select case when j39_matric is not null
                then true
                else false
           end
    into lPredial
    from iptubase
         left join iptuconstr on j39_matric = j01_matric
                             and j39_dtdemo is null
    where j01_matric = iMatricula;

    if not found then
        rtp_iptu_calculavvt.rbErro    := 't';
        rtp_iptu_calculavvt.riCoderro := 9;
        rtp_iptu_calculavvt.rtErro    := ' DADOS DA MATRICULA NAO ENCONTRADO';

        return rtp_iptu_calculavvt;
    end if;

    /* verifica a area do lote */
    select j34_area::numeric, j34_zona
      into nAreaTerreno, iZona
    from lote
    where j34_idbql = iIdbql;

    if nAreaTerreno is null or nAreaTerreno = 0 then

        rtp_iptu_calculavvt.rbErro    := 't';
        rtp_iptu_calculavvt.riCoderro := 3;
        rtp_iptu_calculavvt.rtErro    := 'AREA DO LOTE NAO ENCONTRADA OU ZERADA';

        return rtp_iptu_calculavvt;
    end if;

    nAreaLoteCorrigi := ( nAreaTerreno * ( nFracao / 100::numeric ) );

    perform fc_debug('< calculo vvt > Area do terreno: '||nAreaTerreno);
    perform fc_debug('< calculo vvt > Fracao do terreno: '||nFracao);

    /* busca valor do m2 do terreno */

    select j51_valorm2t::numeric
      into nVm2t
    from zonasvalor
    where j51_anousu = iAnousu
      and j51_zona = iZona;

    if not found then

      rtp_iptu_calculavvt.rbErro    := 't';
      rtp_iptu_calculavvt.riCoderro := 7;
      rtp_iptu_calculavvt.rtMsgErro := 'VERIFIQUE O VALOR DO M2 DO TERRENO PARA A ZONA';

      return rtp_iptu_calculavvt;
    end if;

    perform fc_debug('< calculo vvt > Valor do m2 do terreno: '||nVm2t);

    /* busca fator Situacao */
    select j74_fator::numeric
      into nFatorSituacao
    from carlote
           inner join caracter
                   on j31_codigo = j35_caract
           inner join carfator
                   on j74_anousu = iAnousu
                  and j74_caract = j35_caract
    where j35_idbql = iIdbql
      and j31_grupo = 100;

    if nFatorSituacao = 0 or nFatorSituacao is null then

      rtp_iptu_calculavvt.rbErro    := 't';
      rtp_iptu_calculavvt.riCoderro := 24;
      rtp_iptu_calculavvt.rtErro    := ' PARA GRUPO 100 OU SEM VALOR PARA O ANO DE '||iAnousu;
      rtp_iptu_calculavvt.rtMsgErro := 'SEM CARACTERISTICA PARA GRUPO 100 OU SEM VALOR PARA O ANO DE '||iAnousu;

      return rtp_iptu_calculavvt;
    end if;

    /* busca fator Condicao Fisica do Terreno */

    select j74_fator::numeric
    into nFatorCondFisTerreno
    from carlote
             inner join caracter
                        on j31_codigo = j35_caract
             inner join carfator
                        on j74_anousu = iAnousu
                            and j74_caract = j35_caract
    where j35_idbql = iIdbql
      and j31_grupo = 200;

    if nFatorCondFisTerreno = 0 or nFatorCondFisTerreno is null then

        rtp_iptu_calculavvt.rbErro    := 't';
        rtp_iptu_calculavvt.riCoderro := 24;
        rtp_iptu_calculavvt.rtErro    := ' PARA GRUPO 200 OU SEM VALOR PARA O ANO DE '||iAnousu;
        rtp_iptu_calculavvt.rtMsgErro := 'SEM CARACTERISTICA PARA GRUPO 200 OU SEM VALOR PARA O ANO DE '||iAnousu;

        return rtp_iptu_calculavvt;
    end if;

    nValor := round( nAreaLoteCorrigi * nVm2t * nFatorSituacao * nFatorCondFisTerreno, 2);

    if (nValor <= 0 or nValor is null) and lConstrucaoIrregular = false then

        rtp_iptu_calculavvt.rbErro    := 't';
        rtp_iptu_calculavvt.riCoderro := 113;
        rtp_iptu_calculavvt.rtErro    := '';

        return rtp_iptu_calculavvt;
    end if;

    /* formula de calculo do terreno */
    perform fc_debug('< calculo vvt > Formula: VVT = (nAreaLoteCorrigi * nVm2t * nFatorSituacao * nFatorCondFisTerreno)');
    perform fc_debug('');
    perform fc_debug('< calculo vvt > Formula: '||nValor||' = ('||nAreaLoteCorrigi||' * '||nVm2t||' * '||nFatorSituacao||' * '||nFatorCondFisTerreno||')');

    nAreaConstruida = fc_iptu_getareaconstrmat(iMatricula,iAnousu,true);

    if nAreaConstruida = 0 and nFracao = 0 then
        nValor = 0;
        perform fc_debug('< calculo vvt > Matricula irregular, nao calcula valor venal de terreno.');
    end if;

    rtp_iptu_calculavvt.rnArea       := nAreaTerreno;
    rtp_iptu_calculavvt.rnVvt        := nValor;
    rtp_iptu_calculavvt.rnAreaTotalC := nAreaLoteCorrigi;
    rtp_iptu_calculavvt.rnTestada    := nTestada;
    rtp_iptu_calculavvt.rtDemo       := '';
    rtp_iptu_calculavvt.rtMsgerro    := '';
    rtp_iptu_calculavvt.rbErro       := 'f';

    update tmpdadosiptu
    set vvt = rtp_iptu_calculavvt.rnVvt,
        vm2t = nVm2t,
        areat = nAreaLoteCorrigi;

    return rtp_iptu_calculavvt;

end;
$$  language 'plpgsql';

SQL
        );
    }

    public function downFuncaoFraciona()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists fc_iptu_fracionalote(integer,integer,boolean,boolean);
drop function if exists fc_iptu_fracionalote(integer,integer,boolean,boolean,boolean);

drop   type if exists tp_iptu_fracionalote;
create type cadastro.tp_iptu_fracionalote as (rnFracao numeric, rtDemo text, rtMsgerro text, rbErro boolean);

create or replace function cadastro.fc_iptu_fracionalote(integer,integer,boolean,boolean)
    returns tp_iptu_fracionalote
as $$

    declare

        iMatricula            alias for $1;
        iAnousu               alias for $2;
        bMostrademo           alias for $3;
        lRaise                alias for $4;

        rtp_iptu_fracionalote tp_iptu_fracionalote%ROWTYPE;

        begin

            rtp_iptu_fracionalote.rnFracao  := 0;
            rtp_iptu_fracionalote.rtDemo    := '';
            rtp_iptu_fracionalote.rtMsgerro := '';
            rtp_iptu_fracionalote.rbErro    := 'f';

            select *
              into rtp_iptu_fracionalote
            from fc_iptu_fracionalote(iMatricula, iAnousu, bMostrademo, lRaise, true);

            return rtp_iptu_fracionalote;

        end;
$$ language 'plpgsql';


create or replace function cadastro.fc_iptu_fracionalote(integer,integer,boolean,boolean,boolean)
    returns tp_iptu_fracionalote
as $$
    
    declare

        iMatricula 	           alias for $1;
        iAnousu    	           alias for $2;
        bMostrademo            alias for $3; --Não utilizada no escopo
        lRaise                 alias for $4;
        lAtualizaFracaoForcada alias for $5;

        cSetor	               char(4);
        cQuadra	               char(4);
        cLote		           char(4);

        iIptufrac	           integer;
        iTotalMatriculas	   integer;
        iIdbql 	               integer  default 0;

        nTotalAreaConstruida   numeric;
        rnFracao 	           numeric  default 0;
        nAreacalc	           numeric  default 0;
        nJ01_fracao            numeric  default 0;

        lFracionaIdbql         boolean;

        tManual 	           text     default '';

        rFracao	               record;

        rtp_iptu_fracionalote  tp_iptu_fracionalote%ROWTYPE;

    begin

        perform fc_debug('', lRaise);
        perform fc_debug(' <fracionalote> INICIANDO FRACIONAMENTO DO LOTE...', lRaise);

        select j18_fracionaidbql
          into lFracionaIdbql
        from cadastro.cfiptu
        where j18_anousu = iAnousu;

        rtp_iptu_fracionalote.rnFracao  := 0;
        rtp_iptu_fracionalote.rtDemo    := '';
        rtp_iptu_fracionalote.rtMsgerro := '';
        rtp_iptu_fracionalote.rbErro    := 'f';

        select j01_idbql, j34_setor, j34_quadra, j34_lote
          into iIdbql, cSetor, cQuadra, cLote
        from iptubase
             join lote on j34_idbql = j01_idbql
        where j01_matric = iMatricula;

        /*
         * Conta quantas Matriculas tem para o lote da Matricula a ser calculada
         */
        if lFracionaIdbql then
            perform fc_debug(' <fracionalote> Fracionamento por Idbql: '||iIdbql, lRaise);

            select count(j01_idbql)
              into iTotalMatriculas
            from iptubase
                 join lote on j34_idbql = j01_idbql
            where j01_baixa is null
              and j34_idbql = iIdbql;
        else
            perform fc_debug(' <fracionalote> fracionamento por Setor: '||cSetor||' - Quadra: '||cQuadra||' Lote: '||cLote, lRaise);

            select count(j01_idbql)
              into iTotalMatriculas
            from iptubase
                 join lote on j34_idbql = j01_idbql
            where j01_baixa is null
              and j34_setor  = cSetor
              and j34_quadra = cQuadra
              and j34_lote   = cLote;
        end if;

        perform fc_debug(' <fracionalote> iMatricula          : ' || iMatricula, lRaise);
        perform fc_debug(' <fracionalote> fracao              : ' || rnFracao, lRaise);
        perform fc_debug(' <fracionalote> total de iMatriculas: ' || iTotalMatriculas, lRaise);

        if iTotalMatriculas = 1 then
            if rnFracao is null or rnFracao = 0 then
               rnFracao = 100::numeric;
            else
                perform fc_debug(' <fracionalote> Calculando area construida da iMatricula... ' || iMatricula, lRaise);

                /*
                 * Retorna a area total construida da MATRICULA
                 */
                select into nAreacalc fc_iptu_getareaconstrmat( iMatricula );

                perform fc_debug(' <fracionalote> Fracao de novo: ' || rnFracao, lRaise);
                perform fc_debug(' <fracionalote> fracaocalc: ' || nAreacalc, lRaise);

                if nAreacalc is null or nAreacalc = 0 then
                    rnFracao = 100;
                else
                    rnFracao = ( (nAreacalc / rnFracao ) * 100 );
                    perform fc_debug(' <fracionalote> nAreacalc: '||nAreacalc||' - rnFracao: ' || rnFracao, lRaise);
                end if;
            end if;
        else
            /*
             * Retorna a area total construida do LOTE
             */
            if lFracionaIdbql then
                select into nTotalAreaConstruida fc_iptu_getareaconstrloteidbql(iIdbql, iAnousu);
                perform fc_debug(' <fracionalote> Busca area total construida por idbql: '||nTotalAreaConstruida, lRaise);
            else
                select into nTotalAreaConstruida fc_iptu_getareaconstrlote(cSetor,cQuadra,cLote);
                perform fc_debug(' <fracionalote> Busca area total construida por Setor/Quadra/Lote: '||nTotalAreaConstruida, lRaise);
            end if;

            perform fc_debug(' <fracionalote> Total construido no lote: ' || nTotalAreaConstruida, lRaise);

            tManual := tManual || 'total construido no lote: ' || nTotalAreaConstruida || ' - ';

            if nTotalAreaConstruida = 0 then

                select j01_fracao
                into nJ01_fracao
                from iptubase
                where j01_matric = iMatricula;

                if nJ01_fracao = 0 or nJ01_fracao is null then

                    if lAtualizaFracaoForcada then
                        update iptubase set j01_fracao = 0 where j01_idbql = iIdbql;
                    end if;

                    rnFracao = 100::numeric;
                else
                    rnFracao = nJ01_fracao;
                end if;

            else
                perform fc_debug(' <fracionalote> Fraciona rFracao ', lRaise);

                for rFracao in
                    select j01_matric, sum(j39_area) as j39_area
                    from iptubase
                         join iptuconstr on j39_matric = j01_matric
                    where j01_baixa  is null
                      and j39_dtdemo is null
                      and j01_matric = iMatricula
                    group by j01_matric loop

                    perform fc_debug(' <fracionalote> processando fracao iMatricula: '||coalesce(rFracao.j01_matric,0)||' - construido desta: ' || coalesce(rFracao.j39_area,0), lRaise );

                    select j25_matric
                      into iIptufrac
                    from iptufrac
                    where j25_matric = rFracao.j01_matric
                      and j25_anousu = iAnousu;

                    perform fc_debug(' <fracionalote>    iptufrac: ' || coalesce( iIptufrac, 0 ), lRaise);

                    if iIptufrac is null or iIptufrac = 0 then
                        perform fc_debug(' <fracionalote>    insert no iptufrac', lRaise);
                        insert into iptufrac values (iAnousu, rFracao.j01_matric, iIdbql, rFracao.j39_area / nTotalAreaConstruida * 100);
                    else
                        perform fc_debug(' <fracionalote>    update no iptufrac', lRaise);
                        update iptufrac
                            set j25_fracao = rFracao.j39_area / nTotalAreaConstruida * 100,
                                j25_idbql  = iIdbql
                        where j25_matric = rFracao.j01_matric
                          and j25_anousu = iAnousu;
                    end if;
                end loop;

                select j25_fracao
                  into rnFracao
                from iptufrac
                where j25_matric = iMatricula
                  and j25_anousu = iAnousu;

                if rnFracao is null or rnFracao = 0 then
                   rnFracao = 100::numeric;
                end if;
            end if;
        end if;

        select j01_fracao
        into nJ01_fracao
        from iptubase
        where j01_matric = iMatricula;

        if nJ01_fracao is not null and nJ01_fracao > 0 then
           rnFracao = nJ01_fracao;
        end if;

        rtp_iptu_fracionalote.rnFracao := rnFracao;
        rtp_iptu_fracionalote.rtDemo   := tManual;

        perform fc_debug(' <fracionalote> texto demonstrativo :' || tManual, lRaise);
        perform fc_debug(' <fracionalote> FIM FRACIONAMENTO DO LOTE', lRaise);
        perform fc_debug(' ', lRaise);

        return rtp_iptu_fracionalote;
    end;
$$ language 'plpgsql';

drop function if exists fc_iptu_getareaconstrlote(char(4),char(4),char(4));
create or replace function cadastro.fc_iptu_getareaconstrlote(char(4),char(4),char(4)) returns float8 as 
$$

select coalesce(sum(j39_area),0)
  from iptubase
       inner join iptuconstr on j39_matric = j01_matric
			 inner join lote       on j34_idbql  = j01_idbql
where j01_baixa is null
  and j34_setor  = $1
  and j34_quadra = $2
	and j34_lote   = $3
  and j39_dtdemo is null;

$$ 
language 'sql';

drop function if exists fc_iptu_getareaconstrloteidbql(integer, integer);
create or replace function cadastro.fc_iptu_getareaconstrloteidbql(integer, integer)
    returns numeric
as $$

    declare

        iIdbql                 alias for $1;
        iAnousu                alias for $2;

        nTotalAreaConstruida   numeric default 0;

        sSql                   varchar := '';

        bConstrucaoIrregular   boolean default false;

    begin

        select true
          into bConstrucaoIrregular
        from db_config
        where db21_codigomunicipoestado = '3306107'
          and prefeitura is true;

        sSql := 'with matriculas as (';
        sSql := sSql || 'select j39_matric, j39_idcons, j39_area ';
        sSql := sSql || 'from iptubase ';
        sSql := sSql || '     join iptuconstr on j39_matric = j01_matric ';
        sSql := sSql || 'where j01_baixa  is null ';
        sSql := sSql || '  and j01_idbql  = ' || iIdbql;
        sSql := sSql || '  and j39_dtdemo is null), ';

        if bConstrucaoIrregular then
            sSql := sSql || 'matriculas_irregulares as (';
            sSql := sSql || '    select j39_matric as matric, j39_idcons as idcons ';
            sSql := sSql || '    from iptubase ';
            sSql := sSql || '         join iptuconstr on j39_matric = j01_matric ';
            sSql := sSql || '         join carconstr on j48_matric = j39_matric ';
            sSql := sSql || '                       and j48_idcons = j39_idcons ';
            sSql := sSql || '         join caracter on j31_codigo = j48_caract ';
            sSql := sSql || '                      and j31_grupo = 7200 ';
            sSql := sSql || '         join carfator on j74_anousu = ' || iAnousu;
            sSql := sSql || '                      and j74_caract = j48_caract ';
            sSql := sSql || '    where j01_baixa  is null ';
            sSql := sSql || '      and j01_idbql  = ' || iIdbql;
            sSql := sSql || '      and j39_dtdemo is null), ';
            sSql := sSql || 'matriculas_ativas as (';
            sSql := sSql || '    select j39_matric, j39_idcons, j39_area ';
            sSql := sSql || '    from matriculas ';
            sSql := sSql || '         left join matriculas_irregulares on matric = j39_matric ';
            sSql := sSql || '                                         and idcons = j39_idcons ';
            sSql := sSql || '    where matric is null) ';
        else
            sSql := sSql || 'matriculas_ativas as (';
            sSql := sSql || '    select j39_matric, j39_idcons, j39_area ';
            sSql := sSql || '    from matriculas) ';
        end if;

        sSql := sSql || 'select round(coalesce(sum(j39_area), 0), 2)::numeric ';
        sSql := sSql || 'from matriculas_ativas; ';

        execute sSql into nTotalAreaConstruida;

        return nTotalAreaConstruida;

    end;
$$ language 'plpgsql';

drop function if exists fc_iptu_getareaconstrmat(integer);
create or replace function cadastro.fc_iptu_getareaconstrmat(integer) returns float8 as 
$$
   select coalesce(sum(j39_area), 0)
     from iptuconstr
   where j39_matric = $1
     and j39_dtdemo is null
	 group by j39_matric;
$$ 
language 'sql';

SQL
        );
    }

    public function downFuncaoCalculo()
    {
        DB::connection()->getPdo()->exec(<<<SQL
CREATE OR REPLACE FUNCTION cadastro.fc_calculoiptu_valenca_2023(integer,integer,boolean,boolean,boolean,boolean,boolean,integer,integer)
    RETURNS varchar(100) AS
$$

declare

    iMatricula                          alias   for $1;
    iAnousu                             alias   for $2;
    lGerafinanc                         alias   for $3;
    lAtualizaParcela                    alias   for $4;
    lNovonumpre                         alias   for $5;
    lCalculogeral                       alias   for $6;
    lDemonstrativo                      alias   for $7;
    iParcelaini                         alias   for $8;
    iParcelafim                         alias   for $9;

    iIdbql                              integer default 0;
    iNumcgm                             integer default 0;
    iCodcli                             integer default 0;
    iCodisen                            integer default 0;
    iTipois                             integer default 0;
    iParcelas                           integer default 0;
    iNumconstr                          integer default 0;
    iCodErro                            integer default 0;

    dDatabaixa                          date;

    nAreal                              numeric default 0;
    nAreac                              numeric default 0;
    nTotarea                            numeric default 0;
    nFracao                             numeric default 0;
    nFracaolote                         numeric default 0;
    nAliquota                           numeric default 0;
    nIsenaliq                           numeric default 0;
    nArealo                             numeric default 0;
    nVvc                                numeric(15,2) default 0;
    nVvt                                numeric(15,2) default 0;
    nVv                                 numeric(15,2) default 0;
    nViptu                              numeric(15,2) default 0;

    tRetorno                            text default '';
    tDemo                               text default '';
    tErro                               text default '';

   lPredial                             boolean;
   lFinanceiro                          boolean;
   lDadosIptu                           boolean;
   lErro                                boolean;
   lIsentaxas                           boolean;
   lTempagamento                        boolean;
   lEmpagamento                         boolean;
   lTaxasCalculadas                     boolean;
   lRaise                               boolean default false; -- true para habilitar raise na funcao principal
   lSubRaise                            boolean default false; -- true para habilitar raise nas sub-funcoes

   rCfiptu                              record;

begin

    lRaise := ( case when fc_getsession('DB_debugon') is null then false else true end );
    lSubRaise := lRaise;

    perform fc_debug('INICIANDO CALCULO',lRaise,true,false);
    perform fc_debug('',lRaise,true,false);

    /**
     * Guarda os parametros do calculo
     */

    select * from into rCfiptu cfiptu where j18_anousu = iAnousu;

    select j34_area, coalesce(j34_totcon, 0)
    into nArealo, nTotarea
    from iptubase
             join lote on j34_idbql = j01_idbql
    where j01_matric = iMatricula;

    if not found then
        select fc_iptu_geterro( 3, 'ERRO - Sem dados para a matricula '||iMatricula ) into tRetorno;
        return tRetorno;
    end if;

    /**
     * Executa PRE CALCULO
     */
    select r_iIdbql, r_nAreal, r_nFracao, r_iNumcgm, r_dDatabaixa, r_nFracaolote,
           r_tDemo, r_lTempagamento, r_lEmpagamento, r_iCodisen, r_iTipois, r_nIsenaliq,
           r_lIsentaxas, r_nArealote, r_iCodCli, r_tRetorno
    into iIdbql, nAreal, nFracao, iNumcgm, dDatabaixa, nFracaolote, tDemo, lTempagamento,
        lEmpagamento, iCodisen, iTipois, nIsenaliq, lIsentaxas, nArealo, iCodCli, tRetorno
    from fc_iptu_precalculo( iMatricula, iAnousu, lCalculogeral, lAtualizaParcela, lDemonstrativo, lRaise );

    perform fc_debug(' RETORNO DA PRE CALCULO: ',            lRaise);
    perform fc_debug('  iIdbql        -> ' || iIdbql,        lRaise);
    perform fc_debug('  nAreal        -> ' || nAreal,        lRaise);
    perform fc_debug('  nFracao       -> ' || nFracao,       lRaise);
    perform fc_debug('  iNumcgm       -> ' || iNumcgm,       lRaise);
    perform fc_debug('  dDatabaixa    -> ' || dDatabaixa,    lRaise);
    perform fc_debug('  nFracaolote   -> ' || nFracaolote,   lRaise);
    perform fc_debug('  tDemo         -> ' || tDemo,         lRaise);
    perform fc_debug('  lTempagamento -> ' || lTempagamento, lRaise);
    perform fc_debug('  lEmpagamento  -> ' || lEmpagamento,  lRaise);
    perform fc_debug('  iCodisen      -> ' || iCodisen,      lRaise);
    perform fc_debug('  iTipois       -> ' || iTipois,       lRaise);
    perform fc_debug('  nIsenaliq     -> ' || nIsenaliq,     lRaise);
    perform fc_debug('  lIsentaxas    -> ' || lIsentaxas,    lRaise);
    perform fc_debug('  nArealote     -> ' || nArealo,       lRaise);
    perform fc_debug('  iCodCli       -> ' || iCodCli,       lRaise);
    perform fc_debug('  tRetorno      -> ' || tRetorno,      lRaise);
    perform fc_debug('',lRaise,true,false);

    /**
     * Variavel de retorno contem a msg
     * de erro retornada do pre calculo
     */

    if trim(tRetorno) <> '' then
        return tRetorno;
    end if;

    update tmpdadosiptu set matric = iMatricula;

    update tmpdadostaxa
    set anousu = iAnousu,
        matric = iMatricula,
        idbql  = iIdbql,
        valref = rCfiptu.j18_vlrref;

    /**
     * Calcula valor do terreno
     */

    perform fc_debug('PARAMETROS fc_iptu_calculavvt_valenca_2023 IDBQL: '||iIdbql||' - iMatricula: '||iMatricula||' - Anousu: '||iAnousu||' - FRACAO DO LOTE: '||nFracaolote||' - DEMO: '||lDemonstrativo||' - DEBUG: '||lRaise, lRaise);

    select rnvvt, rnarea, rtdemo, rtmsgerro, rberro, riCodErro, rtErro
    into nVvt, nAreac, tDemo, tRetorno, lErro, iCodErro, tErro
    from fc_iptu_calculavvt_valenca_2023( iIdbql, iMatricula, iAnousu, nFracaolote, lDemonstrativo, lRaise);

    perform fc_debug('RETORNO fc_iptu_calculavvt_valenca_2023 -> VVT: '||nVvt||' - AREA CONSTRUIDA: '||nAreac||' - RETORNO: '||tRetorno||' - ERRO: '||lErro, lRaise);
    perform fc_debug('', lRaise);

    if lErro is true then
        select fc_iptu_geterro( iCodErro, tErro ) into tRetorno;
        return tRetorno;
    end if;

    /**
     * Calcula valor da construcao
     */

    perform fc_debug('PARAMETROS fc_iptu_calculavvc_valenca_2023 MATRICULA: '||iMatricula||' - ANOUSU: '||iAnousu||' - DEMO: '||lDemonstrativo||' - DEBUG: '||lRaise, lRaise);

    select rnvvc, rntotarea, rinumconstr, rtdemo, rtmsgerro, rberro, riCodErro, rtErro
      into nVvc, nTotarea, iNumconstr, tDemo, tRetorno, lErro, iCodErro, tErro
    from fc_iptu_calculavvc_valenca_2023( iMatricula, iAnousu, lDemonstrativo, lRaise );

    perform fc_debug('RETORNO fc_iptu_calculavvc_valenca_2023 -> VVC: '||nVvc||' - AREA TOTAL: '||nTotarea||' - NUMERO DE CONSTRUCOES: '||iNumconstr||' - RETORNO: '||tRetorno||' - ERRO: '||lErro, lRaise);
    perform fc_debug('', lRaise);

    if lErro is true then
        select fc_iptu_geterro(iCodErro, tErro) into tRetorno;
        return tRetorno;
    end if;

    select predial into lPredial from tmpdadosiptu;

    /* BUSCA A ALIQUOTA  */

    perform fc_debug('BUSCA A ALIQUOTA DO IPTU ', lRaise);

    select coderro, descrerro, aliquota
    into iCodErro, tErro, nAliquota
    from fc_iptu_getaliquota_valenca_2023(iMatricula, iIdbql, iAnousu, lPredial, lSubRaise);

    if nAliquota = 0 then
        select fc_iptu_geterro( iCodErro, tErro ) into tRetorno;
        return tRetorno;
    end if;

    perform fc_debug('RETORNO DA BUSCA A ALIQUOTA DO IPTU ', lRaise);
    perform fc_debug(' ', lRaise);

    /*--------- CALCULA O VALOR VENAL -----------*/

    perform fc_debug('valor venal construcao (nVvc) - '||nVvc||' valor venal terreno (nVvt) - '||nVvt, lRaise);

    nVv    := nVvc + nVvt;

    perform fc_debug('valor venal total - '||nVv, lRaise);

    nViptu := nVv * ( nAliquota / 100 );

    if nViptu < rCfiptu.j18_vlrref then
        perform fc_debug('Valor do IPTU menor que a UFIVA', lRaise);
        nViptu := rCfiptu.j18_vlrref;
    end if;

    perform fc_debug('valor iptu '||nViptu||' - aliquota '||nAliquota||'%', lRaise);
    perform fc_debug(' ', lRaise);
    perform fc_debug('Inserindo as receitas de IPTU na tabela tmprecval ', lRaise);
    perform fc_debug(' ', lRaise);

    if lPredial then
        insert into tmprecval values (rCfiptu.j18_rpredi, nViptu, 1, false);
    else
        insert into tmprecval values (rCfiptu.j18_rterri, nViptu, 1, false);
    end if;

    perform fc_debug('Inserindo valor e codigo de vencimento do IPTU na tabela tmpdadosiptu', lRaise);
    perform fc_debug(' ', lRaise);

    update tmpdadosiptu
    set viptu   = nViptu,
        codvenc = rCfiptu.j18_vencim;

    /*-------------------------------------------*/

    select count(*)
    into iParcelas
    from cadvencdesc
             inner join cadvenc on q92_codigo = q82_codigo
    where q92_codigo = rCfiptu.j18_vencim ;

    if not found or iParcelas = 0 then
        select fc_iptu_geterro(14,'') into tRetorno;
        return tRetorno;
    end if;

    update tmpdadostaxa set valiptu = nViptu, vvt = nVvt, nparc = iParcelas, totareaconst = nTotarea;

    /* CALCULA AS TAXAS */

    perform fc_debug('PARAMETROS fc_iptu_calculataxas  ANOUSU '||iAnousu||' -- CODCLI '||iCodcli||' -- Debug: '||lSubRaise, lRaise);
    perform fc_debug(' ', lRaise);

    select fc_iptu_calculataxas(iMatricula, iAnousu, iCodcli, lSubRaise)
    into lTaxasCalculadas;

    perform fc_debug('RETORNO fc_iptu_calculataxas --->>> TAXAS CALCULADAS - '||lTaxasCalculadas, lRaise);

    /* MONTA O DEMONSTRATIVO */
    select fc_iptu_demonstrativo(iMatricula,iAnousu,iIdbql,lSubRaise )
    into tDemo;

    /* GERA FINANCEIRO */
    if lDemonstrativo is false then -- Se nao for demonstrativo gera o financeiro, caso contrario retorna o demonstrativo
        select fc_iptu_geradadosiptu(iMatricula,iIdbql,iAnousu,nIsenaliq,lDemonstrativo,lSubRaise)
        into lDadosIptu;
        if lGerafinanc then
            select fc_iptu_gerafinanceiro(iMatricula,iAnousu,iParcelaini,iParcelafim,lCalculogeral,lTempagamento,lNovonumpre,lDemonstrativo,lSubRaise)
            into lFinanceiro;
        end if;
    else
        return tDemo;
    end if;

    if lDemonstrativo is false then
        update iptucalc
        set j23_manual = tDemo
        where j23_matric = iMatricula
          and j23_anousu = iAnousu;
    end if;

    select fc_iptu_geterro(1, '') into tRetorno;

    return tRetorno;

end;
$$ LANGUAGE 'plpgsql';

create or replace function cadastro.fc_iptu_calculavvc_valenca_2023(integer,integer,boolean,boolean)
    returns tp_iptu_calculavvc as
$$

    declare

        iMatricula          alias for $1;
        iAnousu             alias for $2;
        lMostrademo         alias for $3;
        lRaise              alias for $4;

        nAreatc             numeric default 0;
        nVm2c               numeric default 0;
        nVvcP               numeric default 0;
        nVvc                numeric default 0;
        nFatorIdade         numeric default 0;
        nFatorLocalizacao   numeric default 0;

        iNumerocontr        integer default 0;
        iPontos             integer default 0;
        iTotalPontos        integer default 0;
        iTipoConstrucao     integer default 0;

        lAtualiza           boolean default true;

        rConstr             record;
        rCargrup            record;

        jCargrup            json;

        tMsg                text;

        rtp_iptu_calculavvc tp_iptu_calculavvc%ROWTYPE;

    begin

        perform fc_debug('INICIANDO CALCULO VVC ...', lRaise);

        rtp_iptu_calculavvc.rnVvc       := 0;
        rtp_iptu_calculavvc.rnTotarea   := 0;
        rtp_iptu_calculavvc.riNumconstr := 0;
        rtp_iptu_calculavvc.rtDemo      := '';
        rtp_iptu_calculavvc.rtMsgerro   := 'Retorno ok' ;
        rtp_iptu_calculavvc.rbErro      := 'f';
        rtp_iptu_calculavvc.riCodErro   := 0;
        rtp_iptu_calculavvc.rtErro      := '';

        /* busca os grupos de caracteristicas com pontuacao */
        select json_agg(cargrup.*)
          into jCargrup
        from cargrup
        where j32_grupo in (1700, 1800, 1900, 2000, 2100, 2200, 2300, 2400, 7800, 7900, 8000, 8100);

        iNumerocontr := 0;

        for rConstr in select j39_matric, j39_idcons, j39_idprinc, j39_area, j39_areap, coalesce(j39_ano, 0) as j39_ano
                      from iptuconstr
                     where j39_matric = iMatricula
                       and j39_dtdemo is null
        loop

                /* busca os pontos de cada construcao */
                iTotalPontos := 0;
                for rCargrup in select (value->>'j32_grupo')::int as codigo, value->>'j32_descr' as descricao
                                from json_array_elements(jCargrup)
                loop
                        select coalesce(j31_pontos, 0)
                        into iPontos
                        from carconstr
                             join caracter on j31_codigo = j48_caract
                                          and j31_grupo  = rCargrup.codigo
                        where j48_matric = rConstr.j39_matric
                          and j48_idcons = rConstr.j39_idcons;

                        if not found then
                            tMsg := ' PARA O GRUPO '||rCargrup.codigo||' - '||rCargrup.descricao;
                            perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                             rConstr.j39_idcons||' nao encontrado '||tMsg, lRaise);

                            rtp_iptu_calculavvc.rtErro      := tMsg;
                            rtp_iptu_calculavvc.rtMsgErro   := 'sem caracteristica '||tMsg;
                            rtp_iptu_calculavvc.riCodErro   := 23;
                            rtp_iptu_calculavvc.rbErro      := 't';

                            return rtp_iptu_calculavvc;
                        end if;

                        iTotalPontos := iTotalPontos + iPontos;
                end loop;

                if iTotalPontos = 0 then
                    perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                     rConstr.j39_idcons||' sem pontuacao.', lRaise);
                    rtp_iptu_calculavvc.rtErro      := ' PARA O GRUPO 500';
                    rtp_iptu_calculavvc.rtMsgErro   := 'SEM PONTUACAO PARA A EDIFICACAO '||rConstr.j39_idcons;
                    rtp_iptu_calculavvc.riCodErro   := 23;
                    rtp_iptu_calculavvc.rbErro      := 't';
                    return rtp_iptu_calculavvc;
                end if;

                /* busca a caracteristica do grupo 500 - tipo construcao */
                select j48_caract
                  into iTipoConstrucao
                from carconstr
                     join caracter on j31_codigo = j48_caract
                                  and j31_grupo  = 500
                where j48_matric = rConstr.j39_matric
                  and j48_idcons = rConstr.j39_idcons;

                if not found then
                    perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                     rConstr.j39_idcons||' nao encontrado a caracteristica do grupo 500.', lRaise);
                    rtp_iptu_calculavvc.rtErro      := ' 500';
                    rtp_iptu_calculavvc.rtMsgErro   := 'SEM CARACTERISITICA PARA O GRUPO 500';
                    rtp_iptu_calculavvc.riCodErro   := 102;
                    rtp_iptu_calculavvc.rbErro      := 't';
                    return rtp_iptu_calculavvc;
                end if;

                /* busca o valor do m2 para a construcao */
                select j71_valor::numeric
                  into nVm2c
                from carvalor
                where j71_anousu = iAnousu
                  and j71_caract = iTipoConstrucao
                  and iTotalPontos between j71_quantini and j71_quantfim;

                if not found then
                    perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                     rConstr.j39_idcons||' nao encontrado para caracteristica '||iTipoConstrucao, lRaise);
                    rtp_iptu_calculavvc.rtErro      := ' OU GRUPO 500 NAO ENCONTRADO OU SEM VALOR PARA O ANO DE '||iAnousu;
                    rtp_iptu_calculavvc.rtMsgErro   := 'SEM CARACTERISITICA PARA O GRUPO 500 OU SEM VALOR PARA O ANO DE '||iAnousu;
                    rtp_iptu_calculavvc.riCodErro   := 29;
                    rtp_iptu_calculavvc.rbErro      := 't';
                    return rtp_iptu_calculavvc;
                end if;

                select coalesce(j74_fator, 0)
                  into nFatorIdade
                from carconstr
                     join caracter on j31_codigo = j48_caract
                                  and j31_grupo = 7200
                     join carfator on j74_anousu = iAnousu
                                  and j74_caract = j48_caract
                where j48_matric = rConstr.j39_matric
                  and j48_idcons = rConstr.j39_idcons;

                if not found or nFatorIdade = 0 then
                    /* validar o ano da construcao */
                    if rConstr.j39_ano = 0 then
                        perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                         rConstr.j39_idcons||' com ano da construcao invalido.', lRaise);
                        rtp_iptu_calculavvc.rtErro      := '';
                        rtp_iptu_calculavvc.rtMsgErro   := 'ANO DA CONSTRUCAO INVALIDO - CONSTRUCAO: '||rConstr.j39_idcons;
                        rtp_iptu_calculavvc.riCodErro   := 116;
                        rtp_iptu_calculavvc.rbErro      := 't';
                        return rtp_iptu_calculavvc;
                    end if;

                    /* busca fator idade da construcao */
                    select coalesce(j118_fatorreajuste, 0)::numeric
                      into nFatorIdade
                    from iptupadraoconstrpontos
                    where j118_anousu = iAnousu
                      and j118_caracter = iTipoConstrucao
                      and (iAnousu - rConstr.j39_ano) between j118_pontosini and j118_pontosfim;

                    if not found or nFatorIdade = 0 then
                        perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                         rConstr.j39_idcons||' fator idade da construcao nao encontrado ou zerado para o ano de '||iAnousu, lRaise);
                        rtp_iptu_calculavvc.rtErro      := ' OU SEM VALOR PARA O ANO DE '||iAnousu||' - CONSTRUCAO: '||rConstr.j39_idcons;
                        rtp_iptu_calculavvc.rtMsgErro   := 'FATOR IDADE DA CONSTRUCAO NAO ENCONTRADO OU SEM VALOR PARA O ANO DE '||iAnousu||' - CONSTRUCAO: '||rConstr.j39_idcons;
                        rtp_iptu_calculavvc.riCodErro   := 36;
                        rtp_iptu_calculavvc.rbErro      := 't';
                        return rtp_iptu_calculavvc;
                    end if;
                end if;

                /* busca o fator localizacao da construcao */
                select coalesce(j74_fator, 0)::numeric
                  into nFatorLocalizacao
                from carconstr
                     join caracter on j31_codigo = j48_caract
                                  and j31_grupo = 6700
                     join carfator on j74_anousu = iAnousu
                                  and j74_caract = j48_caract
                where j48_matric = rConstr.j39_matric
                  and j48_idcons = rConstr.j39_idcons;

                if not found or nFatorLocalizacao = 0 then
                    perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                     rConstr.j39_idcons||' fator localizacao da construcao nao encontrado ou zerado para o ano de '||iAnousu, lRaise);
                    rtp_iptu_calculavvc.rtErro    := ' OU SEM VALOR PARA O ANO DE '||iAnousu;
                    rtp_iptu_calculavvc.rtMsgErro := 'FATOR LOCALIZACAO DA CONSTRUCAO NAO ENCONTRADO OU SEM VALOR PARA O ANO DE '||iAnousu;
                    rtp_iptu_calculavvc.riCodErro := 37;
                    rtp_iptu_calculavvc.rbErro    := 't';
                    return rtp_iptu_calculavvc;
                end if;

                /* calcula o valor venal da construcao */
                nVvcp   := rConstr.j39_area * nVm2c * nFatorIdade * nFatorLocalizacao;
                nAreatc := nAreatc + rConstr.j39_area;
                nVvc    := nVvc + nVvcp;

                perform fc_debug('< calculo vvc > Valor venal total parcial - nVvc - '||nVvc||' nVm2c - '||nVm2c|| 'area - '||rConstr.j39_area, lRaise);

                iNumerocontr := iNumerocontr + 1;

                insert into tmpiptucale (anousu, matric, idcons, areaed, vm2, pontos, valor)
                                 values (iAnousu, iMatricula, rConstr.j39_idcons, rConstr.j39_area, nVm2c, 0, nVvcp);
                if lAtualiza then
                    update tmpdadosiptu set predial = true;
                    lAtualiza := false;
                end if;
        end loop;

        nVvc := round(nVvc, 2);
        perform fc_debug('< calculo vvc > Valor venal total final - nVvc - '||nVvc, lRaise);

        rtp_iptu_calculavvc.rnVvc       := nVvc::numeric;
        rtp_iptu_calculavvc.rnTotarea   := nAreatc::numeric;
        rtp_iptu_calculavvc.riNumconstr := iNumerocontr;
        rtp_iptu_calculavvc.rtDemo      := '';
        rtp_iptu_calculavvc.rbErro      := 'f';

        update tmpdadosiptu set vvc = rtp_iptu_calculavvc.rnVvc;

        return rtp_iptu_calculavvc;
    end;
$$ language 'plpgsql';

create or replace function cadastro.fc_iptu_calculavvt_valenca_2023(integer,integer,integer,numeric,boolean,boolean) returns tp_iptu_calculavvt as
$$

declare

    iIdbql                   alias for $1;
    iMatricula               alias for $2;
    iAnousu                  alias for $3;
    nFracao                  alias for $4;
    lMostrademo              alias for $5;
    lRaise                   alias for $6;
  
    lPredial                 boolean default false;

    nVm2t                    numeric default 0;
    nAreaLoteCorrigi         numeric default 0;
    nAreaTerreno             numeric default 0;
    nValor                   numeric default 0;
    nTestada                 numeric default 0;
    nFatorSituacao           numeric default 0;
    nFatorCondFisTerreno     numeric default 0;

    iZona                    bigint default 0;

    rtp_iptu_calculavvt      tp_iptu_calculavvt%ROWTYPE;
  
begin

    rtp_iptu_calculavvt.rnAreaTotalC := 0;
    rtp_iptu_calculavvt.rnArea       := 0;
    rtp_iptu_calculavvt.rnTestada    := 0;
    rtp_iptu_calculavvt.riCoderro    := 0;
    rtp_iptu_calculavvt.rtDemo       := '';
    rtp_iptu_calculavvt.rtMsgerro    := '';
    rtp_iptu_calculavvt.rtErro       := '';
    rtp_iptu_calculavvt.rbErro       := 'f';

    perform fc_debug('< calculo vvt > INICIANDO CALCULO DO VALOR VENAL TERRITORIAL...', lRaise);

    select case when j39_matric is not null
                then true
                else false
           end
    into lPredial
    from iptubase
         left join iptuconstr on j39_matric = j01_matric
                             and j39_dtdemo is null
    where j01_matric = iMatricula;

    if not found then
        rtp_iptu_calculavvt.rbErro    := 't';
        rtp_iptu_calculavvt.riCoderro := 9;
        rtp_iptu_calculavvt.rtErro    := ' DADOS DA MATRICULA NAO ENCONTRADO';

        return rtp_iptu_calculavvt;
    end if;

    /* verifica a area do lote */
    select j34_area::numeric, j34_zona
      into nAreaTerreno, iZona
    from lote
    where j34_idbql = iIdbql;

    if nAreaTerreno is null or nAreaTerreno = 0 then

        rtp_iptu_calculavvt.rbErro    := 't';
        rtp_iptu_calculavvt.riCoderro := 3;
        rtp_iptu_calculavvt.rtErro    := 'AREA DO LOTE NAO ENCONTRADA OU ZERADA';

        return rtp_iptu_calculavvt;
    end if;

    nAreaLoteCorrigi := ( nAreaTerreno * ( nFracao / 100::numeric ) );

    perform fc_debug('< calculo vvt > Area do terreno: '||nAreaTerreno, lRaise);

    /* busca valor do m2 do terreno */

    select j51_valorm2t::numeric
      into nVm2t
    from zonasvalor
    where j51_anousu = iAnousu
      and j51_zona = iZona;

    if not found then

      rtp_iptu_calculavvt.rbErro    := 't';
      rtp_iptu_calculavvt.riCoderro := 7;
      rtp_iptu_calculavvt.rtMsgErro := 'VERIFIQUE O VALOR DO M2 DO TERRENO PARA A ZONA';

      return rtp_iptu_calculavvt;
    end if;

    perform fc_debug('< calculo vvt > Valor do m2 do terreno: '||nVm2t, lRaise);

    /* busca fator Situacao */
    select j74_fator::numeric
      into nFatorSituacao
    from carlote
           inner join caracter
                   on j31_codigo = j35_caract
           inner join carfator
                   on j74_anousu = iAnousu
                  and j74_caract = j35_caract
    where j35_idbql = iIdbql
      and j31_grupo = 100;

    if nFatorSituacao = 0 or nFatorSituacao is null then

      rtp_iptu_calculavvt.rbErro    := 't';
      rtp_iptu_calculavvt.riCoderro := 24;
      rtp_iptu_calculavvt.rtErro    := ' PARA GRUPO 100 OU SEM VALOR PARA O ANO DE '||iAnousu;
      rtp_iptu_calculavvt.rtMsgErro := 'SEM CARACTERISTICA PARA GRUPO 100 OU SEM VALOR PARA O ANO DE '||iAnousu;

      return rtp_iptu_calculavvt;
    end if;

  /* busca fator Condicao Fisica do Terreno */

    select j74_fator::numeric
    into nFatorCondFisTerreno
    from carlote
             inner join caracter
                        on j31_codigo = j35_caract
             inner join carfator
                        on j74_anousu = iAnousu
                            and j74_caract = j35_caract
    where j35_idbql = iIdbql
      and j31_grupo = 200;

   if nFatorCondFisTerreno = 0 or nFatorCondFisTerreno is null then
     
      rtp_iptu_calculavvt.rbErro    := 't';
      rtp_iptu_calculavvt.riCoderro := 24;
      rtp_iptu_calculavvt.rtErro    := ' PARA GRUPO 200 OU SEM VALOR PARA O ANO DE '||iAnousu;
      rtp_iptu_calculavvt.rtMsgErro := 'SEM CARACTERISTICA PARA GRUPO 200 OU SEM VALOR PARA O ANO DE '||iAnousu;
   
      return rtp_iptu_calculavvt;
   end if;

   
	 nValor := round( nAreaLoteCorrigi * nVm2t * nFatorSituacao * nFatorCondFisTerreno, 2);
      
   if nValor <= 0 or nValor is null then
   
      rtp_iptu_calculavvt.rbErro    := 't';
      rtp_iptu_calculavvt.riCoderro := 113;
      rtp_iptu_calculavvt.rtErro    := '';
   
      return rtp_iptu_calculavvt;
   end if;

    /* formula de calculo do terreno */
   perform fc_debug('< calculo vvt > Formula: VVT = (nAreaLoteCorrigi * nVm2t * nFatorSituacao * nFatorCondFisTerreno)', lRaise);
   perform fc_debug('', lRaise);
   perform fc_debug('< calculo vvt > Formula: '||nValor||' = ('||nAreaLoteCorrigi||' * '||nVm2t||' * '||nFatorSituacao||' * '||nFatorCondFisTerreno||')', lRaise, lRaise, lRaise);
    
   rtp_iptu_calculavvt.rnArea       := nAreaTerreno;
   rtp_iptu_calculavvt.rnVvt        := nValor;
   rtp_iptu_calculavvt.rnAreaTotalC := nAreaLoteCorrigi;
   rtp_iptu_calculavvt.rnTestada    := nTestada;
   rtp_iptu_calculavvt.rtDemo       := '';
   rtp_iptu_calculavvt.rtMsgerro    := '';
   rtp_iptu_calculavvt.rbErro       := 'f';

   update tmpdadosiptu 
      set vvt = rtp_iptu_calculavvt.rnVvt, 
          vm2t = nVm2t, 
          areat = nAreaLoteCorrigi;

   return rtp_iptu_calculavvt;

end;
$$  language 'plpgsql';

SQL
        );
    }
}
