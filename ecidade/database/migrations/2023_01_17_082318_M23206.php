<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M23206 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
DB::connection()->getPdo()->exec(<<<SQL
        BEGIN;
            update
                db_syscampodef
            set
                defdescr = 'Imobiliária, zona de entrega, endereço de entrega, endereço do CGM, endereço da construção (predial)' where defcampo = '1' and codcam = 9856;

            update
                db_syscampodef
            set
                defdescr = 'Imobiliária, zona de entrega, endereço de entrega, endereço da construção (predial), endereço do CGM' where defcampo = '2' and codcam = 9856;

            update
                db_syscampodef
            set
                defdescr = 'Imobiliária, zona de entrega, endereço de entrega, endereço da construção (predial)' where defcampo = '3' and codcam = 9856;

            update
                db_syscampodef
            set
                defdescr = 'Imobiliária, zona de entrega, endereço do CGM, endereço da construção (predial)' where defcampo = '4' and codcam = 9856;

            update
                db_syscampodef
            set
                defdescr = 'Endereço de entrega, endereço da construção (predial), endereço do CGM' where defcampo = '5' and codcam = 9856;

            update
                db_syscampodef
            set
                defdescr = 'Endereço de entrega, endereço do CGM, endereço da construção (predial)' where defcampo = '6' and codcam = 9856;

            update
                db_syscampodef
            set
                defdescr = 'Baldio = Endereço do Terreno, Predial = Endereço da Construção' where defcampo = '7' and codcam = 9856;

            insert into db_syscampodef (codcam, defcampo, defdescr) values (9856, '8', 'Somente com endereço de entrega cadastrado');
        END;
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
        BEGIN;
            delete from db_syscampodef where codcam = 9856 and defcampo = '8' and defdescr = 'Somente com endereço de entrega cadastrado';
        END;
SQL
        );
    }
}
