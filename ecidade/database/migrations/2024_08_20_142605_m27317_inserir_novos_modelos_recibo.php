<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27317InserirNovosModelosRecibo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
            insert into caixa.cadmodcarne (k47_sequencial, k47_descr, k47_altura, k47_largura, k47_tipoconvenio)
            values(112, 'RECIBO COBRANCA REGISTRADA C/ PIX', 0, 0, null),
                  (113, 'CARNE COBRANCA REGISTRADA C/ PIX', 0, 0, null),
                  (114, 'FICHA DE COBRANCA A4 PAISAGEM C/PIX', 0, 0, null);
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
            delete from caixa.cadmodcarne 
            where k47_sequencial in (112, 113, 114) 
            and k47_descr in ('RECIBO COBRANCA REGISTRADA C/ PIX', 'CARNE COBRANCA REGISTRADA C/ PIX', 'FICHA DE COBRANCA A4 PAISAGEM C/PIX');
SQL
    );
    }
}
