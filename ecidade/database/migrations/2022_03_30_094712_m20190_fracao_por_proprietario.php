<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M20190FracaoPorProprietario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL
        alter table cadastro.iptubase add COLUMN "j01_fracaoproprietario" float8 default 100;
        alter table cadastro.propri add COLUMN "j42_fracaoproprietario" float8 default 0;
        alter table cadastro.propri add COLUMN "j42_arealoteproprietario" float8 default 0;

        insert into db_syscampo values(1013929,'j01_fracaoproprietario','float8','Fração por proprietário','100', 'Fração por proprietário',20,'t','f','f',4,'text','Fração por proprietário');

        insert into db_syscampo values(1013930,'j42_fracaoproprietario','float8','Fração por proprietário','0', 'Fração por proprietário',20,'t','f','f',4,'text','Fração por proprietário');

        insert into db_syscampo values(1013931,'j42_arealoteproprietario','float8','Área do lote por proprietário','0', 'Área do lote por proprietário',20,'t','f','f',4,'text','Área do lote por proprietário');

        insert into db_sysarqcamp values(27,1013929,17,0);

        insert into db_sysarqcamp values(34,1013930,4,0);

        insert into db_sysarqcamp values(34,1013931,5,0);
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
        DB::connection()->getPdo()->exec(
            <<<SQL
        alter table cadastro.iptubase drop COLUMN "j01_fracaoproprietario";
        alter table cadastro.propri drop COLUMN "j42_fracaoproprietario";
        alter table cadastro.propri drop COLUMN "j42_arealoteproprietario";

        delete from db_sysarqcamp where codcam in (1013929,1013930,1013931);
        delete from db_syscampo where codcam in (1013929,1013930,1013931);
SQL
        );
    }
}
