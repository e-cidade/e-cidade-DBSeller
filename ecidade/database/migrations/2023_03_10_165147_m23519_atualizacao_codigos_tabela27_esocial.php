<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23519AtualizacaoCodigosTabela27Esocial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql  = <<<SQL
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1433', '1433 - Arsênico sanguíneo');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1434', '1434 - Berílio sanguíneo');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1435', '1435 - Cálcio sanguíneo');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1436', '1436 - Cálcio urinário');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1437', '1437 - Citopatologia');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1438', '1438 - Ferro urinário');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1439', '1439 - Formaldeído sanguíneo');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1440', '1440 - Formaldeído urinário');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1441', '1441 - Glicose urinária');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1442', '1442 - Magnésio sanguíneo');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1443', '1443 - Magnésio urinário');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1444', '1444 - Mamografia');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1445', '1445 - Mapeamento de retina (oftalmoscopia indireta)');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1446', '1446 - Níquel sanguíneo');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1447', '1447 - Procedimento diagnóstico em citopatologia cérvico-vaginal oncótica');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1448', '1448 - Radiografia de tórax em duas incidências');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('1449', '1449 - Videolaringoscopia');

            update recursoshumanos.monitoramentosaudeprocedimento set h28_descricao = '0095 - Ácido delta aminolevulínico (ALA-U)' where h28_codigo = '0095';
            update recursoshumanos.monitoramentosaudeprocedimento set h28_descricao = '0613 - Ferro sérico (sangue)' where h28_codigo = '0613';
            update recursoshumanos.monitoramentosaudeprocedimento set h28_descricao = '1393 - Zinco sanguíneo' where h28_codigo = '1393';

            delete from recursoshumanos.monitoramentosaudeprocedimento where h28_codigo in ('0353', '0628', '0829');
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
        $sql  = <<<SQL
            insert into recursoshumanos.monitoramentosaudeprocedimento values('0829', '0829 - Magnésio');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('0628', '0628 - Formaldeído');
            insert into recursoshumanos.monitoramentosaudeprocedimento values('0353', '0353 - Cálcio');

            update recursoshumanos.monitoramentosaudeprocedimento set h28_descricao = '0095 - Ácido delta aminolevulínico' where h28_codigo = '0095';
            update recursoshumanos.monitoramentosaudeprocedimento set h28_descricao = '0613 - Ferro sérico' where h28_codigo = '0613';
            update recursoshumanos.monitoramentosaudeprocedimento set h28_descricao = '1393 - Zinco' where h28_codigo = '1393';

            delete from recursoshumanos.monitoramentosaudeprocedimento where h28_codigo in ('1433', '1434', '1435', '1436', '1437', '1438', '1439', '1440', '1441', '1442', '1443', '1444', '1445', '1446', '1447', '1448', '1449');
SQL;
            DB::connection()->getPdo()->exec($sql);
    }
}