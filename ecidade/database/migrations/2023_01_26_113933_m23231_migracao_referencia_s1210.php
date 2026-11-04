<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23231MigracaoReferenciaS1210 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            create temp table if not exists w23231_migracaoS1210 as 
                select 
                    rh213_sequencial as sequencial,
                    rh213_responsavelpreenchimento as dadoatual,
                    split_part(rh213_responsavelpreenchimento, '_', 1) || '_' || substring(split_part(rh213_responsavelpreenchimento, '_', 2) from 1 for 4) || lpad(substring(split_part(rh213_responsavelpreenchimento, '_', 2) from 5 for 6)::int + 1, 2, '0') as dadonovo
                from 
                    esocialenvio 
                where 
                    rh213_evento = '1210'
                    and rh213_dados not ilike '%"perApur":"' || substring(split_part(rh213_responsavelpreenchimento, '_', 2) from 1 for 4) || '-' || substring(split_part(rh213_responsavelpreenchimento, '_', 2) from 5 for 6) || '"%'
                ;

            -- select 'ajustes de competencia';
            update w23231_migracaoS1210 set dadonovo = split_part(dadonovo, '_', 1) || '_202101' where dadonovo = split_part(dadonovo, '_', 1) || '_202013';
            update w23231_migracaoS1210 set dadonovo = split_part(dadonovo, '_', 1) || '_202201' where dadonovo = split_part(dadonovo, '_', 1) || '_202113';
            update w23231_migracaoS1210 set dadonovo = split_part(dadonovo, '_', 1) || '_202301' where dadonovo = split_part(dadonovo, '_', 1) || '_202213';

            -- select count(*) from esocialenvio a inner join w23231_migracaoS1210 b on a.rh213_sequencial = b.sequencial inner join esocialenvio c on c.rh213_evento = '1210'  and b.dadonovo = c.rh213_responsavelpreenchimento;
            create temp table if not exists w23231_esocialenviostatus as select * from esocialenviostatus where rh214_esocialenvio in (select sequencial from esocialenvio a inner join w23231_migracaoS1210 b on a.rh213_sequencial = b.sequencial inner join esocialenvio c on c.rh213_evento = '1210'  and b.dadonovo = c.rh213_responsavelpreenchimento);
            create temp table if not exists w23231_esocialenvio as select * from esocialenvio where rh213_sequencial in (select sequencial from esocialenvio a inner join w23231_migracaoS1210 b on a.rh213_sequencial = b.sequencial inner join esocialenvio c on c.rh213_evento = '1210'  and b.dadonovo = c.rh213_responsavelpreenchimento);

            delete from esocial.esocialenviostatus where rh214_sequencial in (select rh214_sequencial from w23231_esocialenviostatus);
            delete from esocial.esocialenvio where rh213_sequencial in (select rh213_sequencial from w23231_esocialenvio);

            update esocial.esocialenvio set rh213_responsavelpreenchimento = dadonovo from w23231_migracaoS1210 where sequencial = rh213_sequencial;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
