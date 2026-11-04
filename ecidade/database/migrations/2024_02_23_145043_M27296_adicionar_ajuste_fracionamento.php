<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M27296AdicionarAjusteFracionamento extends Migration
{
    public function up()
    {
        $sql = <<<SQL
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

                        rnFracao = rFracao.j39_area / nTotalAreaConstruida * 100;

                        if (bMostrademo is false) then
                            if iIptufrac is null or iIptufrac = 0 then
                                perform fc_debug(' <fracionalote>    insert no iptufrac');
                                insert into iptufrac values (iAnousu, rFracao.j01_matric, iIdbql, rnFracao);
                            else
                                perform fc_debug(' <fracionalote>    update no iptufrac');
                                update iptufrac
                                set j25_fracao = rnFracao,
                                    j25_idbql  = iIdbql
                                where j25_matric = rFracao.j01_matric
                                  and j25_anousu = iAnousu;
                            end if;
                        end if;
                    end loop;

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

SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $sql = <<<SQL
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

SQL;
        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
