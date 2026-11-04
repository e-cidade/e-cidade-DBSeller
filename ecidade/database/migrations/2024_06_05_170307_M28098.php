<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M28098 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        
        CREATE OR REPLACE FUNCTION public.fc_matestoqueinimei_inc()
        RETURNS trigger
        LANGUAGE plpgsql
        AS \$function$

        declare

        nPrecoMedio                    numeric default 0;
        iMaterial                      integer;
        iInstituicao                   integer;
        nValorEstoque                  numeric;
        nQuantidadeEstoque             numeric default 0;
        iTipoMovimento                 integer;
        iCodigoEstoque                 integer;
        iCodigoMovimento               integer;
        nValorUnitario                 numeric default 0;
        dtMovimento                    date;
        tHora                          timestamp;
        tHoraMovimento                 time;
        lTemPrecoMedio                 boolean default false;
        nQuantidadeMovimento           numeric default 0;
        rValoresAnterior               record;
        rValoresPosteriores            record;
        lServico                       boolean default false;
        iDepto                         integer;
        nQuantidadeSaidasPosteriores   numeric default 0;
        nQuantidadeEntradasPosteriores numeric default 0;
        nSaidasNoPeriodo               numeric default 0;
        nSaldoNoPeriodo                numeric default 0;
        nSaldoAposPeriodo              numeric default 0;
        sMensagemEstoque               varchar;
        lEntradaAposPeriodo            boolean default false;
        begin

        iInstituicao = fc_getsession('DB_instit');
        if iInstituicao is null then
            raise exception 'Instituicao nao informada.';
        end if;


        /**
            * Consultamos o tipo da movimentacao
            */
        select m80_codtipo,
                m81_tipo,
                to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS'),
                m80_data,
                m80_hora,
                m80_coddepto,
                instit
            into iCodigoMovimento,
                iTipoMovimento,
                tHora,
                dtMovimento,
                tHoraMovimento,
                iDepto,
                iInstituicao
            from matestoqueini
                inner join matestoquetipo on m81_codtipo = m80_codtipo
                inner join DB_DEPART on m80_coddepto     = coddepto
                inner join matestoqueinimei on m80_codigo = m82_matestoqueini
                inner join matestoqueitem   on m82_matestoqueitem = m71_codlanc
                inner join matestoque       on m71_codmatestoque  = m70_codigo
            where m80_codigo = NEW.m82_matestoqueini;

            select m70_codmatmater,
                m71_servico
            into
                iMaterial,
                lServico
            from matestoqueitem
                join matestoque on m70_codigo = m71_codmatestoque
            where m71_codlanc = NEW.m82_matestoqueitem;

            /**
            * verifica se existe saldo no estoque para realizar a operacao (saldo cronologico.)
            * pois usuario pode retornar a data, termos uma saida do material antes de termos a entrada.....
            */
        raise notice 'codigo: % Hora: %', new.m82_codigo, tHora;
        if iTipoMovimento = 2 and lServico = false then

            raise notice 'tipo Movimento:% Operacao:%, Servico:%',iTipoMovimento, TG_OP,lServico;

            /**
            * percorremos todo a movimentação do estoque posterior a data do movimento
            */
            for rValoresAnterior in select m82_quant,
                                m81_tipo,
                                to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS') as dtmov
                            from matestoque
                                inner join db_depart        on m70_coddepto       = coddepto
                                inner join matestoqueitem   on m70_codigo         = m71_codmatestoque
                                inner join matestoqueinimei on m82_matestoqueitem = m71_codlanc
                                inner join matestoqueini    on m82_matestoqueini  = m80_codigo
                                inner join matestoquetipo   on m81_codtipo        = m80_codtipo
                            where instit           = iInstituicao
                            and m70_codmatmater  = iMaterial
                            and m70_coddepto     = iDepto
                            and m82_codigo       <> new.m82_codigo
                            and m81_tipo    in(1, 2)
                            and to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS') < tHora
                            order by to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS')

            loop

            raise notice 'Tipo: %, Quantidade:%',rValoresAnterior.m81_tipo,  rValoresAnterior.m82_quant;
            /**
                * Abatemos o valor do estoque do saldo no periodo, caso nao tenha nenhuma entrada, pois
                */
            if rValoresAnterior.m81_tipo = 1 then
                nQuantidadeEstoque = nQuantidadeEstoque + rValoresAnterior.m82_quant;
            elsif rValoresAnterior.m81_tipo = 2 then
                nQuantidadeEstoque = nQuantidadeEstoque - rValoresAnterior.m82_quant;
            end if;
            end loop;
            raise notice 'Antes No periodo: %, saidas no periodo:%', nQuantidadeEstoque, nSaidasNoPeriodo;
            nSaldoNoPeriodo    = nQuantidadeEstoque -  new.m82_quant;
            raise notice 'No periodo: %, apos Periodo:%', nSaldoNoPeriodo, nSaldoAposPeriodo;
            if (nSaldoNoPeriodo < 0 or nSaldoAposPeriodo  < 0) and lServico is false then
            raise notice 'Saldo no periodo %', nSaldoNoPeriodo;
            raise notice 'Saldo apos periodo %', nSaldoAposPeriodo;
            raise notice 'Quantidade em estoque %', nQuantidadeEstoque;

                raise exception 'Saldo total não disponível nesta data (%). Saldo disponivel: %. Servico: %',
                                to_char(dtMovimento, 'DD/MM/YYYY'),
                                nQuantidadeEstoque - nSaidasNoPeriodo, lServico;

            end if;
        end if;


            perform fc_calculaprecomedio(NEW.m82_codigo::integer,
                                        NEW.m82_matestoqueini::integer,
                                        NEW.m82_quant,
                                        NEW.m82_matestoqueinimeiorigem::integer);


            for rValoresPosteriores in select matestoqueinimei.*
                                        from matestoqueitem
                                            inner join matestoqueinimei on m82_matestoqueitem = m71_codlanc
                                            inner join matestoqueini    on m80_codigo         = m82_matestoqueini
                                            inner join matestoque       on m71_codmatestoque  = m70_codigo
                                            inner join db_depart        on m70_coddepto       = coddepto
                                        where m70_codmatmater  = iMaterial
                                        and instit            = iInstituicao
                                        and to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS') > tHora
                                    order by  to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS'), m82_codigo
            loop

                    perform fc_calculaprecomedio(rValoresPosteriores.m82_codigo::integer,
                                                rValoresPosteriores.m82_matestoqueini::integer,
                                                rValoresPosteriores.m82_quant,
                                                rValoresPosteriores.m82_matestoqueinimeiorigem::integer);
        end loop;

            /**
            * verifica se existe saldo no estoque para realizar a operacao (saldo cronologico.)
            * pois usuário pode retornar a data, termos uma saida do material antes de termos a entrada.....
            */

            if iTipoMovimento = 2 and TG_OP = 'INSERT' and lServico = false then

            /**
            * percorremos todo a movimentação do estoque posterior a data do movimento
            */
            for rValoresPosteriores in select m82_quant,
                                m81_tipo,
                                to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS') as dtmov
                            from matestoque
                                inner join db_depart        on m70_coddepto       = coddepto
                                inner join matestoqueitem   on m70_codigo         = m71_codmatestoque
                                inner join matestoqueinimei on m82_matestoqueitem = m71_codlanc
                                inner join matestoqueini    on m82_matestoqueini  = m80_codigo
                                inner join matestoquetipo   on m81_codtipo        = m80_codtipo
                            where instit           = iInstituicao
                            and m70_codmatmater  = iMaterial
                            and m70_coddepto     = iDepto
                            and m81_tipo    in(1, 2)
                            and to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS') > tHora
                            order by to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS'), m81_tipo
            loop

            if rValoresPosteriores.m81_tipo = 1 then
                lEntradaAposPeriodo := true;
            end if;
            /**
                * Abatemos o valor do estoque do saldo no periodo, caso nao tenha nenhuma entrada, pois
                */
            raise notice 'Valores tipo %', rValoresPosteriores.m81_tipo;

            if rValoresPosteriores.m81_tipo = 2 and lEntradaAposPeriodo = false then
                nSaidasNoPeriodo = nSaidasNoPeriodo + rValoresPosteriores.m82_quant;
            elsif rValoresPosteriores.m81_tipo = 2 and lEntradaAposPeriodo = true then
                nSaldoAposPeriodo = nSaldoAposPeriodo - rValoresPosteriores.m82_quant;
            else
                nSaldoAposPeriodo = nSaldoAposPeriodo + rValoresPosteriores.m82_quant;
            end if;
            end loop;

            nSaldoNoPeriodo    = nQuantidadeEstoque - nSaidasNoPeriodo - NEW.m82_quant;
            if (nSaldoNoPeriodo < 0 or nSaldoAposPeriodo  < 0) and lServico is false  then

            raise notice 'Estoque %', nQuantidadeEstoque;
            raise notice 'Saida no periodo % ', nSaidasNoPeriodo;

                raise exception 'Saldo total não disponível nesta data (%). Saldo disponível: %. Servico: %',
                                to_char(dtMovimento, 'DD/MM/YYYY'),
                                nQuantidadeEstoque - nSaidasNoPeriodo, lServico;

            end if;
        end if;
        return new;
        end;
        \$function$
        ;

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
        DB::connection()->getPdo()->exec(<<<SQL
        -- DROP FUNCTION public.fc_matestoqueinimei_inc();
        CREATE OR REPLACE FUNCTION public.fc_matestoqueinimei_inc()
        RETURNS trigger
        LANGUAGE plpgsql
        AS \$function$

        declare

        nPrecoMedio                    numeric default 0;
        iMaterial                      integer;
        iInstituicao                   integer;
        nValorEstoque                  numeric;
        nQuantidadeEstoque             numeric default 0;
        iTipoMovimento                 integer;
        iCodigoEstoque                 integer;
        iCodigoMovimento               integer;
        nValorUnitario                 numeric default 0;
        dtMovimento                    date;
        tHora                          timestamp;
        tHoraMovimento                 time;
        lTemPrecoMedio                 boolean default false;
        nQuantidadeMovimento           numeric default 0;
        rValoresAnterior               record;
        rValoresPosteriores            record;
        lServico                       boolean default false;
        iDepto                         integer;
        nQuantidadeSaidasPosteriores   numeric default 0;
        nQuantidadeEntradasPosteriores numeric default 0;
        nSaidasNoPeriodo               numeric default 0;
        nSaldoNoPeriodo                numeric default 0;
        nSaldoAposPeriodo              numeric default 0;
        sMensagemEstoque               varchar;
        lEntradaAposPeriodo            boolean default false;
        begin

        iInstituicao = fc_getsession('DB_instit');
        if iInstituicao is null then
            raise exception 'Instituicao nao informada.';
        end if;


        /**
            * Consultamos o tipo da movimentacao
            */
        select m80_codtipo,
                m81_tipo,
                to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS'),
                m80_data,
                m80_hora,
                m80_coddepto,
                instit
            into iCodigoMovimento,
                iTipoMovimento,
                tHora,
                dtMovimento,
                tHoraMovimento,
                iDepto,
                iInstituicao
            from matestoqueini
                inner join matestoquetipo on m81_codtipo = m80_codtipo
                inner join DB_DEPART on m80_coddepto     = coddepto
                inner join matestoqueinimei on m80_codigo = m82_matestoqueini
                inner join matestoqueitem   on m82_matestoqueitem = m71_codlanc
                inner join matestoque       on m71_codmatestoque  = m70_codigo
            where m80_codigo = NEW.m82_matestoqueini;

            select m70_codmatmater,
                m71_servico
            into
                iMaterial,
                lServico
            from matestoqueitem
                join matestoque on m70_codigo = m71_codmatestoque
            where m71_codlanc = NEW.m82_matestoqueitem;

            /**
            * verifica se existe saldo no estoque para realizar a operacao (saldo cronologico.)
            * pois usuario pode retornar a data, termos uma saida do material antes de termos a entrada.....
            */
        raise notice 'codigo: % Hora: %', new.m82_codigo, tHora;
        if iTipoMovimento = 2 and lServico = false then

            raise notice 'tipo Movimento:% Operacao:%, Servico:%',iTipoMovimento, TG_OP,lServico;

            /**
            * percorremos todo a movimentação do estoque posterior a data do movimento
            */
            for rValoresAnterior in select m82_quant,
                                m81_tipo,
                                to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS') as dtmov
                            from matestoque
                                inner join db_depart        on m70_coddepto       = coddepto
                                inner join matestoqueitem   on m70_codigo         = m71_codmatestoque
                                inner join matestoqueinimei on m82_matestoqueitem = m71_codlanc
                                inner join matestoqueini    on m82_matestoqueini  = m80_codigo
                                inner join matestoquetipo   on m81_codtipo        = m80_codtipo
                            where instit           = iInstituicao
                            and m70_codmatmater  = iMaterial
                            and m70_coddepto     = iDepto
                            and m82_codigo       <> new.m82_codigo
                            and m81_tipo    in(1, 2)
                            and to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS') < tHora
                            order by to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS')

            loop

            raise notice 'Tipo: %, Quantidade:%',rValoresAnterior.m81_tipo,  rValoresAnterior.m82_quant;
            /**
                * Abatemos o valor do estoque do saldo no periodo, caso nao tenha nenhuma entrada, pois
                */
            if rValoresAnterior.m81_tipo = 1 then
                nQuantidadeEstoque = nQuantidadeEstoque + rValoresAnterior.m82_quant;
            elsif rValoresAnterior.m81_tipo = 2 then
                nQuantidadeEstoque = nQuantidadeEstoque - rValoresAnterior.m82_quant;
            end if;
            end loop;
            raise notice 'Antes No periodo: %, saidas no periodo:%', nQuantidadeEstoque, nSaidasNoPeriodo;
            nSaldoNoPeriodo    = nQuantidadeEstoque -  new.m82_quant;
            raise notice 'No periodo: %, apos Periodo:%', nSaldoNoPeriodo, nSaldoAposPeriodo;
            if (nSaldoNoPeriodo < 0 or nSaldoAposPeriodo  < 0) and lServico is false then
            raise notice 'Saldo no periodo %', nSaldoNoPeriodo;
            raise notice 'Saldo apos periodo %', nSaldoAposPeriodo;
            raise notice 'Quantidade em estoque %', nQuantidadeEstoque;

                raise exception 'Saldo total não disponível nesta data (%). Saldo disponivel: %. Servico: %',
                                to_char(dtMovimento, 'DD/MM/YYYY'),
                                nQuantidadeEstoque - nSaidasNoPeriodo, lServico;

            end if;
        end if;


            perform fc_calculaprecomedio(NEW.m82_codigo::integer,
                                        NEW.m82_matestoqueini::integer,
                                        NEW.m82_quant,
                                        NEW.m82_matestoqueinimeiorigem::integer);


            for rValoresPosteriores in select matestoqueinimei.*
                                        from matestoqueitem
                                            inner join matestoqueinimei on m82_matestoqueitem = m71_codlanc
                                            inner join matestoqueini    on m80_codigo         = m82_matestoqueini
                                            inner join matestoque       on m71_codmatestoque  = m70_codigo
                                            inner join db_depart        on m70_coddepto       = coddepto
                                        where m70_codmatmater  = iMaterial
                                        and instit            = iInstituicao
                                        and to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS') > tHora
                                    order by  to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS'), m82_codigo
            loop

                    perform fc_calculaprecomedio(rValoresPosteriores.m82_codigo::integer,
                                                rValoresPosteriores.m82_matestoqueini::integer,
                                                rValoresPosteriores.m82_quant,
                                                rValoresPosteriores.m82_matestoqueinimeiorigem::integer);
        end loop;

            /**
            * verifica se existe saldo no estoque para realizar a operacao (saldo cronologico.)
            * pois usuário pode retornar a data, termos uma saida do material antes de termos a entrada.....
            */

            if iTipoMovimento = 2 and TG_OP = 'INSERT' and lServico = false then

            /**
            * percorremos todo a movimentação do estoque posterior a data do movimento
            */
            for rValoresPosteriores in select m82_quant,
                                m81_tipo,
                                to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS') as dtmov
                            from matestoque
                                inner join db_depart        on m70_coddepto       = coddepto
                                inner join matestoqueitem   on m70_codigo         = m71_codmatestoque
                                inner join matestoqueinimei on m82_matestoqueitem = m71_codlanc
                                inner join matestoqueini    on m82_matestoqueini  = m80_codigo
                                inner join matestoquetipo   on m81_codtipo        = m80_codtipo
                            where instit           = iInstituicao
                            and m70_codmatmater  = iMaterial
                            and m70_coddepto     = iDepto
                            and m81_tipo    in(1, 2)
                            and to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS') > tHora
                            order by to_timestamp(m80_data || ' ' || m80_hora, 'YYYY-MM-DD HH24:MI:SS')
                                        limit 1 
            loop

            if rValoresPosteriores.m81_tipo = 1 then
                lEntradaAposPeriodo := true;
            end if;
            /**
                * Abatemos o valor do estoque do saldo no periodo, caso nao tenha nenhuma entrada, pois
                */
            raise notice 'Valores tipo %', rValoresPosteriores.m81_tipo;

            if rValoresPosteriores.m81_tipo = 2 and lEntradaAposPeriodo = false then
                nSaidasNoPeriodo = nSaidasNoPeriodo + rValoresPosteriores.m82_quant;
            elsif rValoresPosteriores.m81_tipo = 2 and lEntradaAposPeriodo = true then
                nSaldoAposPeriodo = nSaldoAposPeriodo - rValoresPosteriores.m82_quant;
            else
                nSaldoAposPeriodo = nSaldoAposPeriodo + rValoresPosteriores.m82_quant;
            end if;
            end loop;

            nSaldoNoPeriodo    = nQuantidadeEstoque - nSaidasNoPeriodo - NEW.m82_quant;
            if (nSaldoNoPeriodo < 0 or nSaldoAposPeriodo  < 0) and lServico is false  then

            raise notice 'Estoque %', nQuantidadeEstoque;
            raise notice 'Saida no periodo % ', nSaidasNoPeriodo;

                raise exception 'Saldo total não disponível nesta data (%). Saldo disponível: %. Servico: %',
                                to_char(dtMovimento, 'DD/MM/YYYY'),
                                nQuantidadeEstoque - nSaidasNoPeriodo, lServico;

            end if;
        end if;
        return new;
        end;
        \$function$
        ;
        
SQL
       );
    }
}
